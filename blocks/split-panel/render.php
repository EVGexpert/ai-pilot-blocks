<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$visual = in_array( $attributes['visual'] ?? 'registry', array( 'registry','cards' ), true ) ? $attributes['visual'] : 'registry';
$tone = in_array( $attributes['tone'] ?? 'white', array( 'white','paper','ink' ), true ) ? $attributes['tone'] : 'white';
$class = 'ap-split-panel is-tone-' . $tone . ( ! empty( $attributes['reverse'] ) ? ' is-reverse' : '' );
$wrapper = get_block_wrapper_attributes( array( 'class' => $class ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-split-panel__inner"><div class="ap-split-panel__content">
 <?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?><h2 class="ap-section-title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2><?php if ( ! empty( $attributes['text'] ) ) : ?><p class="ap-split-panel__text"><?php echo esc_html( $attributes['text'] ); ?></p><?php endif; ?><?php if ( ! empty( $attributes['buttonLabel'] ) ) : ?><div class="ap-split-panel__actions"><a class="ap-btn ap-btn--primary" href="<?php echo esc_url( $attributes['buttonUrl'] ?? '#' ); ?>"><?php echo esc_html( $attributes['buttonLabel'] ); ?></a></div><?php endif; ?>
 </div><div class="ap-split-panel__visual" aria-hidden="true">
 <?php if ( 'registry' === $visual ) : ?><div class="ap-registry"><div class="ap-registry__bar"><span></span><span></span><span></span></div><div class="ap-registry__row"><code>hero</code><span>title · description · CTA</span><em>valid</em></div><div class="ap-registry__row"><code>features</code><span>cards · columns · tone</span><em>valid</em></div><div class="ap-registry__row"><code>steps</code><span>items · layout</span><em>valid</em></div><div class="ap-registry__row"><code>cta</code><span>title · actions · theme</span><em>valid</em></div></div><?php else : ?><div class="ap-cards-visual"><span>Strategy</span><span>Content</span><span>Design</span><span>Build</span></div><?php endif; ?>
 </div></div></section>
