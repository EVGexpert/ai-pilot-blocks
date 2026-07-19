<?php
/**
 * Machine-readable authoring rules and block-tree validation.
 *
 * @package AIPilotBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class AIPilot_Blocks_Rules {
    private static ?array $cache = null;

    public static function get(): array {
        if ( null !== self::$cache ) {
            return self::$cache;
        }
        $file = AIPILOT_BLOCKS_PATH . 'rules/block-authoring-v3.json';
        $data = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array();
        self::$cache = is_array( $data ) ? $data : array();
        return self::$cache;
    }

    /**
     * Audit block source files before a library release.
     *
     * This is intentionally read-only and does not write options, posts or files.
     */
    public static function audit_library(): array {
        $rules = self::get();
        $errors = array();
        $warnings = array();
        $checked = array();
        $required_files = $rules['folderContract']['required'] ?? array();
        $style_rules = $rules['styleSystem'] ?? array();
        $shared_selector = (string) ( $style_rules['sharedTokenSelector'] ?? ':where([class*="wp-block-aipilot-"])' );
        $style_file = AIPILOT_BLOCKS_PATH . 'assets/blocks.css';
        $shared_css = file_exists( $style_file ) ? (string) file_get_contents( $style_file ) : '';

        if ( '' === $shared_css ) {
            $errors[] = array( 'code' => 'shared_style_missing', 'path' => 'assets/blocks.css', 'message' => 'Shared block stylesheet is missing or empty.' );
        } elseif ( ! str_contains( $shared_css, $shared_selector ) ) {
            $errors[] = array( 'code' => 'token_scope_not_future_safe', 'path' => 'assets/blocks.css', 'message' => 'Shared design tokens must use the namespace-wide selector: ' . $shared_selector );
        }

        foreach ( (array) ( $style_rules['requiredSharedVariables'] ?? array() ) as $variable ) {
            if ( ! str_contains( $shared_css, (string) $variable . ':' ) ) {
                $errors[] = array( 'code' => 'shared_token_missing', 'path' => 'assets/blocks.css', 'message' => 'Missing shared design token declaration: ' . (string) $variable );
            }
        }

        $directories = glob( AIPILOT_BLOCKS_PATH . 'blocks/*', GLOB_ONLYDIR );
        foreach ( is_array( $directories ) ? $directories : array() as $directory ) {
            $slug = basename( $directory );
            $block_path = 'blocks/' . $slug;
            $block_file = $directory . '/block.json';
            $agent_file = $directory . '/ai.json';
            $render_file = $directory . '/render.php';
            $editor_file = $directory . '/index.js';

            foreach ( (array) $required_files as $required_file ) {
                if ( ! file_exists( $directory . '/' . $required_file ) ) {
                    $errors[] = array( 'code' => 'required_file_missing', 'path' => $block_path, 'message' => 'Missing required file: ' . $required_file );
                }
            }

            $block = file_exists( $block_file ) ? json_decode( (string) file_get_contents( $block_file ), true ) : null;
            $agent = file_exists( $agent_file ) ? json_decode( (string) file_get_contents( $agent_file ), true ) : null;
            if ( ! is_array( $block ) ) {
                $errors[] = array( 'code' => 'invalid_block_json', 'path' => $block_path . '/block.json', 'message' => 'block.json is invalid JSON.' );
                continue;
            }
            if ( ! is_array( $agent ) ) {
                $errors[] = array( 'code' => 'invalid_ai_json', 'path' => $block_path . '/ai.json', 'message' => 'ai.json is invalid JSON.' );
                $agent = array();
            }

            $name = (string) ( $block['name'] ?? '' );
            $checked[] = $name ?: $slug;
            if ( 3 !== (int) ( $block['apiVersion'] ?? 0 ) ) {
                $errors[] = array( 'code' => 'invalid_api_version', 'path' => $block_path . '/block.json', 'message' => 'Block must use apiVersion 3.' );
            }
            if ( ! str_starts_with( $name, 'aipilot/' ) ) {
                $errors[] = array( 'code' => 'invalid_namespace', 'path' => $block_path . '/block.json', 'message' => 'Block name must use the aipilot/* namespace.' );
            }
            if ( false !== ( $block['supports']['html'] ?? null ) ) {
                $errors[] = array( 'code' => 'html_support_must_be_false', 'path' => $block_path . '/block.json', 'message' => 'supports.html must be false for agent-safe blocks.' );
            }
            foreach ( array( 'editorScript', 'style', 'editorStyle', 'render' ) as $asset_key ) {
                if ( empty( $block[ $asset_key ] ) ) {
                    $errors[] = array( 'code' => 'asset_metadata_missing', 'path' => $block_path . '/block.json', 'message' => 'Missing block.json asset field: ' . $asset_key );
                }
            }
            if ( 'aipilot-blocks-style' !== ( $block['style'] ?? null ) ) {
                $errors[] = array( 'code' => 'shared_front_style_missing', 'path' => $block_path . '/block.json', 'message' => 'Block must load aipilot-blocks-style.' );
            }
            if ( 'aipilot-blocks-editor-style' !== ( $block['editorStyle'] ?? null ) ) {
                $errors[] = array( 'code' => 'shared_editor_style_missing', 'path' => $block_path . '/block.json', 'message' => 'Block must load aipilot-blocks-editor-style.' );
            }

            $render_source = file_exists( $render_file ) ? (string) file_get_contents( $render_file ) : '';
            $editor_source = file_exists( $editor_file ) ? (string) file_get_contents( $editor_file ) : '';
            if ( ! str_contains( $render_source, 'get_block_wrapper_attributes' ) ) {
                $errors[] = array( 'code' => 'wrapper_attributes_missing', 'path' => $block_path . '/render.php', 'message' => 'render.php must call get_block_wrapper_attributes().' );
            }
            if ( ! str_contains( $editor_source, 'dynamicEdit' ) && ! str_contains( $editor_source, 'useBlockProps' ) ) {
                $errors[] = array( 'code' => 'editor_wrapper_missing', 'path' => $block_path . '/index.js', 'message' => 'Editor root must use useBlockProps() directly or through dynamicEdit().' );
            }

            $contract = isset( $agent['styleContract'] ) && is_array( $agent['styleContract'] ) ? $agent['styleContract'] : array();
            $attributes = isset( $block['attributes'] ) && is_array( $block['attributes'] ) ? $block['attributes'] : array();
            $contract_attributes = array();
            foreach ( array( 'classAttributes', 'cssVariableAttributes' ) as $contract_group ) {
                foreach ( (array) ( $contract[ $contract_group ] ?? array() ) as $attribute_name => $definition ) {
                    $contract_attributes[ $attribute_name ] = true;
                }
            }
            foreach ( (array) ( $contract['booleanClasses'] ?? array() ) as $attribute_name => $definition ) {
                $contract_attributes[ $attribute_name ] = true;
            }

            foreach ( $attributes as $attribute_name => $attribute_schema ) {
                $enum = isset( $attribute_schema['enum'] ) && is_array( $attribute_schema['enum'] ) ? $attribute_schema['enum'] : array();
                $requires_contract = in_array( $attribute_name, array( 'tone', 'variant', 'layout', 'spacing', 'width', 'alignment', 'headingSize' ), true );
                if ( 'align' === $attribute_name && array_diff( $enum, array( 'wide', 'full' ) ) ) {
                    $requires_contract = true;
                }
                if ( $requires_contract && ! isset( $contract_attributes[ $attribute_name ] ) ) {
                    $errors[] = array( 'code' => 'style_contract_attribute_missing', 'path' => $block_path . '/ai.json', 'message' => 'CSS-driven attribute must be documented in styleContract: ' . $attribute_name );
                }
            }

            if ( empty( $contract ) ) {
                continue;
            }

            $root_class = (string) ( $contract['rootClass'] ?? '' );
            if ( '' === $root_class ) {
                $errors[] = array( 'code' => 'style_root_class_missing', 'path' => $block_path . '/ai.json', 'message' => 'styleContract.rootClass is required.' );
                continue;
            }
            if ( ! str_contains( $render_source, $root_class ) ) {
                $errors[] = array( 'code' => 'style_root_not_rendered', 'path' => $block_path . '/render.php', 'message' => 'Rendered root does not contain styleContract.rootClass: ' . $root_class );
            }
            if ( ! str_contains( $shared_css, '.' . $root_class ) ) {
                $errors[] = array( 'code' => 'style_root_not_styled', 'path' => 'assets/blocks.css', 'message' => 'Shared stylesheet has no selector for .' . $root_class );
            }
            if ( 'shared' === ( $contract['tokenScope'] ?? '' ) && ! str_contains( $shared_css, $shared_selector ) ) {
                $errors[] = array( 'code' => 'shared_token_scope_missing', 'path' => 'assets/blocks.css', 'message' => $name . ' requires the shared token scope.' );
            }

            foreach ( (array) ( $contract['classAttributes'] ?? array() ) as $attribute_name => $definition ) {
                $attribute_schema = $attributes[ $attribute_name ] ?? array();
                $enum = isset( $attribute_schema['enum'] ) && is_array( $attribute_schema['enum'] ) ? $attribute_schema['enum'] : array();
                $prefix = (string) ( $definition['classPrefix'] ?? '' );
                $base_values = isset( $definition['baseValues'] ) && is_array( $definition['baseValues'] ) ? $definition['baseValues'] : array();
                if ( empty( $enum ) || '' === $prefix ) {
                    $errors[] = array( 'code' => 'invalid_class_attribute_contract', 'path' => $block_path . '/ai.json', 'message' => 'classAttributes.' . $attribute_name . ' must reference an enum and classPrefix.' );
                    continue;
                }
                if ( ! str_contains( $render_source, $prefix ) ) {
                    $errors[] = array( 'code' => 'variant_class_not_rendered', 'path' => $block_path . '/render.php', 'message' => 'render.php does not emit class prefix ' . $prefix . ' for ' . $attribute_name );
                }
                foreach ( $enum as $value ) {
                    if ( in_array( $value, $base_values, true ) ) {
                        continue;
                    }
                    $variant_class = '.' . $prefix . $value;
                    if ( ! str_contains( $shared_css, $variant_class ) ) {
                        $errors[] = array( 'code' => 'variant_css_missing', 'path' => 'assets/blocks.css', 'message' => $name . '.' . $attribute_name . '=' . $value . ' has no CSS selector ' . $variant_class );
                    }
                }
            }

            foreach ( (array) ( $contract['booleanClasses'] ?? array() ) as $attribute_name => $class_name ) {
                if ( 'boolean' !== ( $attributes[ $attribute_name ]['type'] ?? '' ) ) {
                    $errors[] = array( 'code' => 'invalid_boolean_class_contract', 'path' => $block_path . '/ai.json', 'message' => 'booleanClasses.' . $attribute_name . ' must reference a boolean attribute.' );
                    continue;
                }
                if ( ! str_contains( $render_source, (string) $class_name ) || ! str_contains( $shared_css, '.' . (string) $class_name ) ) {
                    $errors[] = array( 'code' => 'boolean_class_incomplete', 'path' => $block_path, 'message' => 'Boolean class must exist in render.php and CSS: ' . (string) $class_name );
                }
            }

            foreach ( (array) ( $contract['cssVariableAttributes'] ?? array() ) as $attribute_name => $definition ) {
                $variable = (string) ( $definition['variable'] ?? '' );
                if ( ! isset( $attributes[ $attribute_name ] ) || '' === $variable ) {
                    $errors[] = array( 'code' => 'invalid_css_variable_contract', 'path' => $block_path . '/ai.json', 'message' => 'cssVariableAttributes.' . $attribute_name . ' is invalid.' );
                    continue;
                }
                if ( ! str_contains( $render_source, $variable ) || ! str_contains( $shared_css, $variable ) ) {
                    $errors[] = array( 'code' => 'css_variable_incomplete', 'path' => $block_path, 'message' => 'CSS variable must exist in render.php and CSS: ' . $variable );
                }
            }
        }

        return array(
            'valid' => empty( $errors ),
            'errors' => $errors,
            'warnings' => $warnings,
            'summary' => array(
                'checkedBlocks' => count( $checked ),
                'blocks' => $checked,
                'errorCount' => count( $errors ),
                'warningCount' => count( $warnings ),
                'sharedTokenSelector' => $shared_selector,
            ),
        );
    }

    public static function validate_content( string $content, string $document_type = 'page' ): array {
        return self::validate_blocks( parse_blocks( $content ), $document_type );
    }

    public static function validate_blocks( array $blocks, string $document_type = 'page' ): array {
        $document_type = in_array( $document_type, array( 'page', 'post', 'template', 'single-post-template', 'archive-template' ), true ) ? $document_type : 'page';
        $manifest = AIPilot_Blocks_Manifest::get();
        $known = array();
        foreach ( $manifest['blocks'] ?? array() as $item ) {
            if ( ! empty( $item['name'] ) ) {
                $known[ $item['name'] ] = $item;
            }
        }

        $errors = array();
        $warnings = array();
        $counts = array();
        $h1_count = 0;

        $walk = static function ( array $nodes, ?string $parent_name = null, string $path = 'root' ) use ( &$walk, &$errors, &$warnings, &$counts, &$h1_count, $known, $document_type ): void {
            foreach ( $nodes as $index => $node ) {
                $name = isset( $node['blockName'] ) && is_string( $node['blockName'] ) ? $node['blockName'] : '';
                if ( '' === $name ) {
                    continue;
                }
                $node_path = $path . '.' . $index;
                $counts[ $name ] = ( $counts[ $name ] ?? 0 ) + 1;

                if ( 'core/html' === $name ) {
                    $errors[] = array( 'code' => 'forbidden_html_block', 'path' => $node_path, 'message' => 'core/html is not allowed in agent-generated trees.' );
                }

                if ( 'archive-template' === $document_type && 'core/query' === $name && true !== ( $node['attrs']['query']['inherit'] ?? false ) ) {
                    $errors[] = array( 'code' => 'archive_query_must_inherit', 'path' => $node_path, 'message' => 'Archive templates must use core/query with query.inherit=true.' );
                }

                if ( str_starts_with( $name, 'aipilot/' ) && ! isset( $known[ $name ] ) ) {
                    $errors[] = array( 'code' => 'unknown_aipilot_block', 'path' => $node_path, 'message' => 'Unknown AI Pilot block: ' . $name );
                    continue;
                }

                if ( isset( $known[ $name ] ) ) {
                    $meta = $known[ $name ];
                    $parents = $meta['parent'] ?? array();
                    if ( is_array( $parents ) && ! empty( $parents ) && ! in_array( $parent_name, $parents, true ) ) {
                        $errors[] = array( 'code' => 'invalid_parent', 'path' => $node_path, 'message' => $name . ' must be inside: ' . implode( ', ', $parents ) );
                    }

                    $attrs = isset( $node['attrs'] ) && is_array( $node['attrs'] ) ? $node['attrs'] : array();
                    $support_attributes = array( 'align', 'anchor', 'className', 'style', 'metadata', 'backgroundColor', 'textColor', 'gradient', 'fontSize', 'fontFamily', 'lock' );
                    foreach ( $attrs as $key => $value ) {
                        if ( ! isset( $meta['attributes'][ $key ] ) ) {
                            if ( ! in_array( $key, $support_attributes, true ) ) {
                                $warnings[] = array( 'code' => 'undeclared_attribute', 'path' => $node_path, 'message' => $name . ' contains undeclared attribute: ' . $key );
                            }
                            continue;
                        }
                        $schema = $meta['attributes'][ $key ];
                        if ( isset( $schema['enum'] ) && is_array( $schema['enum'] ) && ! in_array( $value, $schema['enum'], true ) ) {
                            $errors[] = array( 'code' => 'invalid_enum', 'path' => $node_path, 'message' => $name . '.' . $key . ' is outside the allowed enum.' );
                        }
                    }

                    $heading = (int) ( $meta['agent']['semantics']['headingLevel'] ?? 0 );
                    if ( 1 === $heading ) {
                        ++$h1_count;
                    }
                }

                if ( in_array( $name, array( 'core/heading', 'core/post-title', 'core/query-title' ), true ) && 1 === (int) ( $node['attrs']['level'] ?? ( 'core/query-title' === $name ? 1 : 2 ) ) ) {
                    ++$h1_count;
                }

                $children = isset( $node['innerBlocks'] ) && is_array( $node['innerBlocks'] ) ? $node['innerBlocks'] : array();
                if ( isset( $known[ $name ]['allowedBlocks'] ) && is_array( $known[ $name ]['allowedBlocks'] ) && ! empty( $known[ $name ]['allowedBlocks'] ) ) {
                    foreach ( $children as $child_index => $child ) {
                        $child_name = $child['blockName'] ?? '';
                        if ( $child_name && ! in_array( $child_name, $known[ $name ]['allowedBlocks'], true ) ) {
                            $errors[] = array( 'code' => 'child_not_allowed', 'path' => $node_path . '.' . $child_index, 'message' => $child_name . ' is not allowed inside ' . $name );
                        }
                    }
                }
                $walk( $children, $name, $node_path . '.innerBlocks' );
            }
        };
        $walk( $blocks );

        $maximums = AIPilot_Blocks_Rules::get()['placement']['maximums'] ?? array();
        foreach ( is_array( $maximums ) ? $maximums : array() as $name => $maximum ) {
            if ( ( $counts[ $name ] ?? 0 ) > (int) $maximum ) {
                $errors[] = array( 'code' => 'max_per_page', 'path' => 'root', 'message' => $name . ' exceeds maximum per page: ' . (int) $maximum );
            }
        }

        if ( 'post' === $document_type && 0 !== $h1_count ) {
            $errors[] = array( 'code' => 'post_content_h1', 'path' => 'root', 'message' => 'Post content must not contain H1; the single-post template provides it. Found ' . $h1_count . '.' );
        } elseif ( in_array( $document_type, array( 'page', 'template', 'single-post-template', 'archive-template' ), true ) && 1 !== $h1_count ) {
            $warnings[] = array( 'code' => 'h1_count', 'path' => 'root', 'message' => 'Expected exactly one H1 provider; found ' . $h1_count . '.' );
        }

        if ( 'single-post-template' === $document_type ) {
            if ( 1 !== ( $counts['core/post-title'] ?? 0 ) ) {
                $errors[] = array( 'code' => 'single_template_post_title', 'path' => 'root', 'message' => 'single-post template must contain exactly one core/post-title.' );
            }
            if ( 1 !== ( $counts['core/post-content'] ?? 0 ) ) {
                $errors[] = array( 'code' => 'single_template_post_content', 'path' => 'root', 'message' => 'single-post template must contain exactly one core/post-content.' );
            }
        }

        if ( 'archive-template' === $document_type && 1 > ( $counts['core/query'] ?? 0 ) ) {
            $errors[] = array( 'code' => 'archive_template_query', 'path' => 'root', 'message' => 'Archive template must contain an inherited core/query block.' );
        }

        return array(
            'valid' => empty( $errors ),
            'errors' => $errors,
            'warnings' => $warnings,
            'summary' => array( 'documentType' => $document_type, 'blockCount' => array_sum( $counts ), 'h1Count' => $h1_count, 'counts' => $counts ),
        );
    }
}
