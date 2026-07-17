<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$columns = in_array( (int) ( $attributes['columns'] ?? 3 ), array( 2,3,4 ), true ) ? (int) $attributes['columns'] : 3;
$tone = in_array( $attributes['tone'] ?? 'paper', array( 'paper','white' ), true ) ? $attributes['tone'] : 'paper';
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-feature-grid is-tone-' . $tone ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-feature-grid__inner">
 <div class="ap-feature-grid__header"><div><?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?><h2 class="ap-section-title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2></div><?php if ( ! empty( $attributes['description'] ) ) : ?><p class="ap-section-copy"><?php echo esc_html( $attributes['description'] ); ?></p><?php endif; ?></div>
 <div class="ap-feature-grid__cards" style="--ap-columns:<?php echo esc_attr( (string) $columns ); ?>"><?php echo $content; ?></div>
</div></section>
