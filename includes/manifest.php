<?php
/**
 * Agent-readable block manifest, rules, validation and optional Abilities/MCP exposure.
 *
 * @package AIPilotBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class AIPilot_Blocks_Manifest {
    private static ?array $cache = null;

    public static function get(): array {
        if ( null !== self::$cache ) {
            return self::$cache;
        }

        $items = array();
        $directories = glob( AIPILOT_BLOCKS_PATH . 'blocks/*', GLOB_ONLYDIR );
        foreach ( is_array( $directories ) ? $directories : array() as $directory ) {
            $block_file = $directory . '/block.json';
            if ( ! file_exists( $block_file ) ) {
                continue;
            }

            $block = json_decode( (string) file_get_contents( $block_file ), true );
            $agent_file = $directory . '/ai.json';
            $agent = file_exists( $agent_file ) ? json_decode( (string) file_get_contents( $agent_file ), true ) : array();
            if ( ! is_array( $block ) ) {
                continue;
            }

            $items[] = array(
                'name'          => $block['name'] ?? basename( $directory ),
                'apiVersion'    => $block['apiVersion'] ?? null,
                'version'       => $block['version'] ?? '',
                'title'         => $block['title'] ?? '',
                'category'      => $block['category'] ?? '',
                'icon'          => $block['icon'] ?? '',
                'description'   => $block['description'] ?? '',
                'keywords'      => $block['keywords'] ?? array(),
                'attributes'    => $block['attributes'] ?? array(),
                'supports'      => $block['supports'] ?? array(),
                'parent'        => $block['parent'] ?? array(),
                'ancestor'      => $block['ancestor'] ?? array(),
                'allowedBlocks' => $block['allowedBlocks'] ?? array(),
                'usesContext'   => $block['usesContext'] ?? array(),
                'providesContext' => $block['providesContext'] ?? array(),
                'selectors'     => $block['selectors'] ?? array(),
                'styles'        => $block['styles'] ?? array(),
                'variations'    => $block['variations'] ?? array(),
                'example'       => $block['example'] ?? array(),
                'assets'        => array(
                    'editorScript' => $block['editorScript'] ?? null,
                    'style'        => $block['style'] ?? null,
                    'editorStyle'  => $block['editorStyle'] ?? null,
                    'viewScript'   => $block['viewScript'] ?? null,
                    'render'       => $block['render'] ?? null,
                ),
                'agent'         => is_array( $agent ) ? $agent : array(),
            );
        }

        self::$cache = array(
            'schemaVersion' => 2,
            'pluginVersion' => AIPILOT_BLOCKS_VERSION,
            'namespace'     => 'aipilot',
            'blockApiVersion' => 3,
            'contentModel'  => 'dynamic-attributes-and-inner-blocks',
            'rulesEndpoint' => rest_url( 'aipilot-blocks/v1/rules' ),
            'validateEndpoint' => rest_url( 'aipilot-blocks/v1/validate' ),
            'blocks'        => $items,
        );
        return self::$cache;
    }
}

function aipilot_blocks_public_discovery_enabled(): bool {
    return (bool) apply_filters( 'aipilot_blocks_public_discovery', true );
}

add_action(
    'rest_api_init',
    static function (): void {
        $read_permission = static function (): bool {
            return aipilot_blocks_public_discovery_enabled() || current_user_can( 'edit_posts' );
        };

        register_rest_route(
            'aipilot-blocks/v1',
            '/manifest',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => $read_permission,
                'callback'            => static fn(): WP_REST_Response => rest_ensure_response( AIPilot_Blocks_Manifest::get() ),
            )
        );
        register_rest_route(
            'aipilot-blocks/v1',
            '/rules',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => $read_permission,
                'callback'            => static fn(): WP_REST_Response => rest_ensure_response( AIPilot_Blocks_Rules::get() ),
            )
        );
        register_rest_route(
            'aipilot-blocks/v1',
            '/validate',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'permission_callback' => static fn(): bool => current_user_can( 'edit_posts' ),
                'args'                => array(
                    'content' => array( 'type' => 'string', 'required' => true ),
                ),
                'callback'            => static function ( WP_REST_Request $request ): WP_REST_Response {
                    return rest_ensure_response( AIPilot_Blocks_Rules::validate_content( (string) $request->get_param( 'content' ) ) );
                },
            )
        );
    }
);

add_action(
    'wp_abilities_api_categories_init',
    static function (): void {
        if ( function_exists( 'wp_register_ability_category' ) ) {
            wp_register_ability_category(
                'aipilot-blocks',
                array(
                    'label'       => __( 'AI Pilot Blocks', 'ai-pilot-blocks' ),
                    'description' => __( 'Block metadata, authoring rules and non-destructive validation.', 'ai-pilot-blocks' ),
                )
            );
        }
    }
);

add_action(
    'wp_abilities_api_init',
    static function (): void {
        if ( ! function_exists( 'wp_register_ability' ) ) {
            return;
        }
        wp_register_ability(
            'aipilot-blocks/get-manifest',
            array(
                'label'               => __( 'Get AI Pilot block manifest', 'ai-pilot-blocks' ),
                'description'         => __( 'Returns Block API v3 metadata, schemas, placement constraints and usage hints.', 'ai-pilot-blocks' ),
                'category'            => 'aipilot-blocks',
                'input_schema'        => array( 'type' => 'object', 'properties' => array() ),
                'output_schema'       => array( 'type' => 'object' ),
                'permission_callback' => static fn( array $input = array() ): bool => aipilot_blocks_public_discovery_enabled() || current_user_can( 'edit_posts' ),
                'execute_callback'    => static fn( array $input = array() ): array => AIPilot_Blocks_Manifest::get(),
                'meta'                => array( 'mcp' => array( 'public' => true, 'type' => 'tool' ), 'annotations' => array( 'readonly' => true, 'destructive' => false, 'idempotent' => true ) ),
            )
        );
        wp_register_ability(
            'aipilot-blocks/get-rules',
            array(
                'label'               => __( 'Get AI Pilot block authoring rules', 'ai-pilot-blocks' ),
                'description'         => __( 'Returns API v3, placement, metadata, accessibility, SEO and agent-safety rules.', 'ai-pilot-blocks' ),
                'category'            => 'aipilot-blocks',
                'input_schema'        => array( 'type' => 'object', 'properties' => array() ),
                'output_schema'       => array( 'type' => 'object' ),
                'permission_callback' => static fn( array $input = array() ): bool => aipilot_blocks_public_discovery_enabled() || current_user_can( 'edit_posts' ),
                'execute_callback'    => static fn( array $input = array() ): array => AIPilot_Blocks_Rules::get(),
                'meta'                => array( 'mcp' => array( 'public' => true, 'type' => 'tool' ), 'annotations' => array( 'readonly' => true, 'destructive' => false, 'idempotent' => true ) ),
            )
        );
        wp_register_ability(
            'aipilot-blocks/validate-content',
            array(
                'label'               => __( 'Validate an AI Pilot block tree', 'ai-pilot-blocks' ),
                'description'         => __( 'Validates serialized Gutenberg content without changing the site.', 'ai-pilot-blocks' ),
                'category'            => 'aipilot-blocks',
                'input_schema'        => array( 'type' => 'object', 'properties' => array( 'content' => array( 'type' => 'string' ) ), 'required' => array( 'content' ) ),
                'output_schema'       => array( 'type' => 'object' ),
                'permission_callback' => static fn( array $input = array() ): bool => current_user_can( 'edit_posts' ),
                'execute_callback'    => static fn( array $input = array() ): array => AIPilot_Blocks_Rules::validate_content( (string) ( $input['content'] ?? '' ) ),
                'meta'                => array( 'mcp' => array( 'public' => true, 'type' => 'tool' ), 'annotations' => array( 'readonly' => true, 'destructive' => false, 'idempotent' => true ) ),
            )
        );
    }
);

add_filter(
    'mcp_adapter_default_server_config',
    static function ( array $config ): array {
        $config['tools'] = isset( $config['tools'] ) && is_array( $config['tools'] ) ? $config['tools'] : array();
        foreach ( array( 'aipilot-blocks/get-manifest', 'aipilot-blocks/get-rules', 'aipilot-blocks/validate-content' ) as $tool ) {
            if ( ! in_array( $tool, $config['tools'], true ) ) {
                $config['tools'][] = $tool;
            }
        }
        return $config;
    }
);
