<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'acid', array( 'acid','blue','ink' ), true ) ? $attributes['tone'] : 'acid';
$align = in_array( $attributes['align'] ?? 'center', array( 'left','center' ), true ) ? $attributes['align'] : 'center';
$wrapper = get_block_wrapper_attributes( array( 'class' => 'alignfull ap-cta is-tone-' . $tone . ' is-align-' . $align ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-cta__inner"><?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?><h2 class="ap-cta__title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2><?php if ( ! empty( $attributes['text'] ) ) : ?><p class="ap-cta__text"><?php echo esc_html( $attributes['text'] ); ?></p><?php endif; ?><div class="ap-cta__actions"><?php if ( ! empty( $attributes['primaryLabel'] ) ) : ?><a class="ap-btn ap-btn--primary" href="<?php echo esc_url( $attributes['primaryUrl'] ?? '#' ); ?>"><?php echo esc_html( $attributes['primaryLabel'] ); ?></a><?php endif; ?><?php if ( ! empty( $attributes['secondaryLabel'] ) ) : ?><a class="ap-btn ap-btn--ghost" href="<?php echo esc_url( $attributes['secondaryUrl'] ?? '#' ); ?>"><?php echo esc_html( $attributes['secondaryLabel'] ); ?></a><?php endif; ?></div></div></section>
