<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'ink', array( 'ink','paper' ), true ) ? $attributes['tone'] : 'ink';
$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_slice( $attributes['items'], 0, 6 ) : array();
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-stats is-tone-' . $tone ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-stats__inner"><?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?><h2 class="ap-section-title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2><div class="ap-stats__grid" style="--ap-count:<?php echo esc_attr( (string) max( 1, count( $items ) ) ); ?>"><?php foreach ( $items as $item ) : ?><div class="ap-stat"><span class="ap-stat__value"><?php echo esc_html( $item['value'] ?? '' ); ?></span><span class="ap-stat__label"><?php echo esc_html( $item['label'] ?? '' ); ?></span></div><?php endforeach; ?></div></div></section>
