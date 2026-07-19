<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$heading_size = in_array( $attributes['headingSize'] ?? 'auto', array( 'auto','xs','sm','md','lg','xl' ), true ) ? $attributes['headingSize'] : 'auto';
$tone = in_array( $attributes['tone'] ?? 'plain', array( 'plain','paper' ), true ) ? $attributes['tone'] : 'plain';
$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_slice( $attributes['items'], 0, 20 ) : array();
$title_id = wp_unique_id( 'ap-article-links-' );
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-article-links is-tone-' . $tone . ' is-heading-' . $heading_size, 'aria-labelledby' => $title_id ) );
?>
<section <?php echo $wrapper; ?>><header><h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2><?php if ( ! empty( $attributes['description'] ) ) : ?><p><?php echo esc_html( $attributes['description'] ); ?></p><?php endif; ?></header><ol class="ap-article-links__list"><?php foreach ( $items as $item ) : if ( empty( $item['title'] ) ) { continue; } ?><li><a href="<?php echo esc_url( $item['url'] ?? '#' ); ?>"><strong><?php echo esc_html( $item['title'] ); ?></strong><?php if ( ! empty( $item['description'] ) ) : ?><span><?php echo esc_html( $item['description'] ); ?></span><?php endif; ?></a></li><?php endforeach; ?></ol></section>
