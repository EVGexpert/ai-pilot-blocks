<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'paper', array( 'paper','ink','rose' ), true ) ? $attributes['tone'] : 'paper';
$size = in_array( $attributes['size'] ?? 'medium', array( 'medium','large' ), true ) ? $attributes['size'] : 'medium';
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-pullquote is-tone-' . $tone . ' is-size-' . $size ) );
?>
<figure <?php echo $wrapper; ?>><blockquote><p><?php echo esc_html( $attributes['quote'] ?? '' ); ?></p></blockquote><?php if ( ! empty( $attributes['cite'] ) || ! empty( $attributes['role'] ) ) : ?><figcaption><span><?php echo esc_html( $attributes['cite'] ?? '' ); ?></span><?php if ( ! empty( $attributes['role'] ) ) : ?><small><?php echo esc_html( $attributes['role'] ); ?></small><?php endif; ?></figcaption><?php endif; ?></figure>
