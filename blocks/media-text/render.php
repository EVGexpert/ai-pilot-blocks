<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$position = in_array( $attributes['mediaPosition'] ?? 'left', array( 'left','right' ), true ) ? $attributes['mediaPosition'] : 'left';
$tone = in_array( $attributes['tone'] ?? 'white', array( 'white','paper','blue' ), true ) ? $attributes['tone'] : 'white';
$heading_size = in_array( $attributes['headingSize'] ?? 'auto', array( 'auto','xs','sm','md','lg','xl' ), true ) ? $attributes['headingSize'] : 'auto';
$ratio = in_array( $attributes['ratio'] ?? 'landscape', array( 'landscape','portrait','square' ), true ) ? $attributes['ratio'] : 'landscape';
$requested_level = (int) ( $attributes['headingLevel'] ?? 2 );
$level = in_array( $requested_level, array( 2,3 ), true ) ? $requested_level : 2;
$title_id = wp_unique_id( 'ap-media-text-' );
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-media-text is-media-' . $position . ' is-tone-' . $tone . ' is-ratio-' . $ratio . ' is-heading-' . $heading_size, 'aria-labelledby' => $title_id ) );
$image = '';
if ( ! empty( $attributes['imageId'] ) ) {
    $image = wp_get_attachment_image( (int) $attributes['imageId'], 'large', false, array( 'alt' => (string) ( $attributes['alt'] ?? '' ), 'loading' => 'lazy' ) );
} elseif ( ! empty( $attributes['imageUrl'] ) ) {
    $image = '<img src="' . esc_url( $attributes['imageUrl'] ) . '" alt="' . esc_attr( $attributes['alt'] ?? '' ) . '" loading="lazy">';
}
?>
<section <?php echo $wrapper; ?>><figure class="ap-media-text__media"><?php echo $image; ?><?php if ( ! empty( $attributes['caption'] ) ) : ?><figcaption><?php echo esc_html( $attributes['caption'] ); ?></figcaption><?php endif; ?></figure><div class="ap-media-text__content"><?php printf( '<h%1$d id="%2$s">%3$s</h%1$d>', $level, esc_attr( $title_id ), esc_html( $attributes['title'] ?? '' ) ); ?><?php if ( ! empty( $attributes['text'] ) ) : ?><div class="ap-media-text__copy"><?php echo wp_kses_post( wpautop( $attributes['text'] ) ); ?></div><?php endif; ?></div></section>
