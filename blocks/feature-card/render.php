<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$variant = in_array( $attributes['variant'] ?? 'plain', array( 'plain','outlined','accent','dark' ), true ) ? $attributes['variant'] : 'plain';
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-feature-card is-variant-' . $variant ) );
?>
<article <?php echo $wrapper; ?>>
 <?php if ( ! empty( $attributes['number'] ) ) : ?><span class="ap-feature-card__number"><?php echo esc_html( $attributes['number'] ); ?></span><?php endif; ?>
 <div class="ap-feature-card__body"><?php if ( ! empty( $attributes['kicker'] ) ) : ?><p class="ap-feature-card__kicker"><?php echo esc_html( $attributes['kicker'] ); ?></p><?php endif; ?><h3 class="ap-feature-card__title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h3><?php if ( ! empty( $attributes['text'] ) ) : ?><p class="ap-feature-card__text"><?php echo esc_html( $attributes['text'] ); ?></p><?php endif; ?><?php if ( ! empty( $attributes['linkLabel'] ) ) : ?><a class="ap-feature-card__link" href="<?php echo esc_url( $attributes['linkUrl'] ?? '#' ); ?>"><?php echo esc_html( $attributes['linkLabel'] ); ?></a><?php endif; ?></div>
</article>
