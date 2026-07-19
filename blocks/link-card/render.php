<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$heading_size = in_array( $attributes['headingSize'] ?? 'auto', array( 'auto','xs','sm','md','lg','xl' ), true ) ? $attributes['headingSize'] : 'auto';
$tone = in_array( $attributes['tone'] ?? 'white', array( 'white','blue','acid' ), true ) ? $attributes['tone'] : 'white';
$url = esc_url( $attributes['url'] ?? '#' );
$host = ! empty( $attributes['domain'] ) ? $attributes['domain'] : (string) wp_parse_url( $url, PHP_URL_HOST );
$target = ! empty( $attributes['newTab'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-link-card is-tone-' . $tone . ' is-heading-' . $heading_size ) );
?>
<aside <?php echo $wrapper; ?>><a class="ap-link-card__link" href="<?php echo $url; ?>"<?php echo $target; ?>><span class="ap-link-card__label"><?php echo esc_html( $attributes['label'] ?? '' ); ?></span><strong class="ap-link-card__title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></strong><?php if ( ! empty( $attributes['description'] ) ) : ?><span class="ap-link-card__description"><?php echo esc_html( $attributes['description'] ); ?></span><?php endif; ?><span class="ap-link-card__footer"><span><?php echo esc_html( $host ); ?></span><span aria-hidden="true">↗</span></span></a></aside>
