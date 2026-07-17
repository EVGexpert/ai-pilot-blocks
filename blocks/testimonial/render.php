<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'blue', array( 'blue','rose','acid','ink' ), true ) ? $attributes['tone'] : 'blue';
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-testimonial is-tone-' . $tone ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-testimonial__inner"><blockquote class="ap-testimonial__quote">“<?php echo esc_html( $attributes['quote'] ?? '' ); ?>”</blockquote><div class="ap-testimonial__meta"><span class="ap-testimonial__name"><?php echo esc_html( $attributes['name'] ?? '' ); ?></span><span class="ap-testimonial__role"><?php echo esc_html( trim( ( $attributes['role'] ?? '' ) . ( ! empty( $attributes['company'] ) ? ' · ' . $attributes['company'] : '' ) ) ); ?></span></div></div></section>
