<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_slice( $attributes['items'], 0, 12 ) : array();
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-logo-cloud' ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-logo-cloud__inner">
 <?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?>
 <div class="ap-logo-cloud__items"><?php foreach ( $items as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?></div>
</div></section>
