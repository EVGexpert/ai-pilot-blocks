<?php
/**
 * Server render for SEO and AI search benefits.
 *
 * @package AIPilotBlocks
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'ink', array( 'ink', 'paper' ), true ) ? $attributes['tone'] : 'ink';
$search_items = isset( $attributes['searchItems'] ) && is_array( $attributes['searchItems'] ) ? array_slice( $attributes['searchItems'], 0, 6 ) : array();
$ai_items = isset( $attributes['aiItems'] ) && is_array( $attributes['aiItems'] ) ? array_slice( $attributes['aiItems'], 0, 6 ) : array();
$section_id = wp_unique_id( 'ap-seo-ai-' );
$title_id = $section_id . '-title';
$search_title_id = $section_id . '-search-title';
$ai_title_id = $section_id . '-ai-title';
$wrapper = get_block_wrapper_attributes(
    array(
        'class' => 'ap-seo-ai is-tone-' . $tone,
        'aria-labelledby' => $title_id,
    )
);
?>
<section <?php echo $wrapper; ?>>
    <div class="ap-seo-ai__inner">
        <header class="ap-seo-ai__header">
            <div>
                <?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?>
                <h2 id="<?php echo esc_attr( $title_id ); ?>" class="ap-section-title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2>
            </div>
            <?php if ( ! empty( $attributes['description'] ) ) : ?><p class="ap-section-copy"><?php echo esc_html( $attributes['description'] ); ?></p><?php endif; ?>
        </header>
        <div class="ap-seo-ai__columns">
            <section class="ap-seo-ai__panel is-search" aria-labelledby="<?php echo esc_attr( $search_title_id ); ?>">
                <span class="ap-seo-ai__symbol" aria-hidden="true">↗</span>
                <h3 id="<?php echo esc_attr( $search_title_id ); ?>"><?php echo esc_html( $attributes['searchTitle'] ?? '' ); ?></h3>
                <ul class="ap-seo-ai__list">
                    <?php foreach ( $search_items as $item ) : ?>
                        <li><h4><?php echo esc_html( $item['title'] ?? '' ); ?></h4><?php if ( ! empty( $item['text'] ) ) : ?><p><?php echo esc_html( $item['text'] ); ?></p><?php endif; ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <section class="ap-seo-ai__panel is-ai" aria-labelledby="<?php echo esc_attr( $ai_title_id ); ?>">
                <span class="ap-seo-ai__symbol" aria-hidden="true">✦</span>
                <h3 id="<?php echo esc_attr( $ai_title_id ); ?>"><?php echo esc_html( $attributes['aiTitle'] ?? '' ); ?></h3>
                <ul class="ap-seo-ai__list">
                    <?php foreach ( $ai_items as $item ) : ?>
                        <li><h4><?php echo esc_html( $item['title'] ?? '' ); ?></h4><?php if ( ! empty( $item['text'] ) ) : ?><p><?php echo esc_html( $item['text'] ); ?></p><?php endif; ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        </div>
    </div>
</section>
