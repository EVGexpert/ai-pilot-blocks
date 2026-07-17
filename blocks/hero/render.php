<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'ink', array( 'ink','paper' ), true ) ? $attributes['tone'] : 'ink';
$alignment = in_array( $attributes['alignment'] ?? 'left', array( 'left','center' ), true ) ? $attributes['alignment'] : 'left';
$visual = in_array( $attributes['visual'] ?? 'personas', array( 'personas','grid','none' ), true ) ? $attributes['visual'] : 'personas';
$personas = isset( $attributes['personas'] ) && is_array( $attributes['personas'] ) ? array_slice( $attributes['personas'], 0, 8 ) : array();
$class = 'ap-hero is-tone-' . $tone . ' is-align-' . $alignment . ' has-visual-' . $visual;
$wrapper = get_block_wrapper_attributes( array( 'class' => $class ) );
?>
<section <?php echo $wrapper; ?>>
 <div class="ap-hero__inner"><div class="ap-hero__content">
  <?php if ( ! empty( $attributes['badge'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['badge'] ); ?></p><?php endif; ?>
  <h1 class="ap-hero__title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h1>
  <?php if ( ! empty( $attributes['description'] ) ) : ?><p class="ap-hero__description"><?php echo esc_html( $attributes['description'] ); ?></p><?php endif; ?>
  <div class="ap-hero__actions">
   <?php if ( ! empty( $attributes['primaryLabel'] ) ) : ?><a class="ap-btn <?php echo 'ink' === $tone ? 'ap-btn--light' : 'ap-btn--primary'; ?>" href="<?php echo esc_url( $attributes['primaryUrl'] ?? '#' ); ?>"><?php echo esc_html( $attributes['primaryLabel'] ); ?></a><?php endif; ?>
   <?php if ( ! empty( $attributes['secondaryLabel'] ) ) : ?><a class="ap-btn ap-btn--ghost" href="<?php echo esc_url( $attributes['secondaryUrl'] ?? '#' ); ?>"><?php echo esc_html( $attributes['secondaryLabel'] ); ?></a><?php endif; ?>
  </div>
 </div>
 <?php if ( 'none' !== $visual ) : ?><div class="ap-hero__visual" aria-hidden="true">
  <?php if ( 'personas' === $visual ) : foreach ( $personas as $persona ) : ?><span class="ap-persona"><?php echo esc_html( $persona ); ?></span><?php endforeach; else : ?><div class="ap-visual-grid"><?php for ( $i=0; $i<9; $i++ ) : ?><span></span><?php endfor; ?></div><?php endif; ?>
 </div><?php endif; ?>
 </div>
</section>
