<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$align = in_array( $attributes['align'] ?? 'left', array( 'left','center' ), true ) ? $attributes['align'] : 'left';
$tone = in_array( $attributes['tone'] ?? 'paper', array( 'paper','white','ink' ), true ) ? $attributes['tone'] : 'paper';
$wrapper = get_block_wrapper_attributes( array( 'class' => 'alignfull ap-statement is-align-' . $align . ' is-tone-' . $tone ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-statement__inner">
 <?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?>
 <p class="ap-statement__text"><?php echo esc_html( $attributes['text'] ?? '' ); ?></p>
</div></section>
