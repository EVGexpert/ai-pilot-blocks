<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$layout = in_array( $attributes['layout'] ?? 'horizontal', array( 'horizontal','vertical' ), true ) ? $attributes['layout'] : 'horizontal';
$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_slice( $attributes['items'], 0, 8 ) : array();
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-steps is-layout-' . $layout ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-steps__inner"><div class="ap-steps__header"><div><?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?><h2 class="ap-section-title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2></div><?php if ( ! empty( $attributes['description'] ) ) : ?><p class="ap-section-copy"><?php echo esc_html( $attributes['description'] ); ?></p><?php endif; ?></div><div class="ap-steps__list" style="--ap-count:<?php echo esc_attr( (string) max( 1, count( $items ) ) ); ?>">
<?php foreach ( $items as $index => $item ) : ?><article class="ap-step"><span class="ap-step__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3><?php if ( ! empty( $item['text'] ) ) : ?><p><?php echo esc_html( $item['text'] ); ?></p><?php endif; ?></article><?php endforeach; ?>
</div></div></section>
