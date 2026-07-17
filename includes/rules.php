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

    public static function validate_content( string $content ): array {
        return self::validate_blocks( parse_blocks( $content ) );
    }

    public static function validate_blocks( array $blocks ): array {
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

        $walk = static function ( array $nodes, ?string $parent_name = null, string $path = 'root' ) use ( &$walk, &$errors, &$warnings, &$counts, &$h1_count, $known ): void {
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
                    foreach ( $attrs as $key => $value ) {
                        if ( ! isset( $meta['attributes'][ $key ] ) ) {
                            $warnings[] = array( 'code' => 'undeclared_attribute', 'path' => $node_path, 'message' => $name . ' contains undeclared attribute: ' . $key );
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

                if ( 'core/heading' === $name && 1 === (int) ( $node['attrs']['level'] ?? 2 ) ) {
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

        if ( 1 !== $h1_count ) {
            $warnings[] = array( 'code' => 'h1_count', 'path' => 'root', 'message' => 'Expected exactly one H1 provider; found ' . $h1_count . '.' );
        }

        return array(
            'valid' => empty( $errors ),
            'errors' => $errors,
            'warnings' => $warnings,
            'summary' => array( 'blockCount' => array_sum( $counts ), 'h1Count' => $h1_count, 'counts' => $counts ),
        );
    }
}
