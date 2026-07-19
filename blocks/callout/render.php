<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$heading_size = in_array( $attributes['headingSize'] ?? 'auto', array( 'auto','xs','sm','md','lg','xl' ), true ) ? $attributes['headingSize'] : 'auto';
$type = in_array( $attributes['type'] ?? 'key', array( 'key','note','tip','warning' ), true ) ? $attributes['type'] : 'key';
$labels = array( 'key' => 'Ключевой вывод', 'note' => 'Заметка', 'tip' => 'Совет', 'warning' => 'Важно' );
$title = ! empty( $attributes['title'] ) ? $attributes['title'] : $labels[ $type ];
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-callout is-type-' . $type . ' is-heading-' . $heading_size, 'aria-label' => $title ) );
?>
<aside <?php echo $wrapper; ?>><span class="ap-callout__symbol" aria-hidden="true"><?php echo 'warning' === $type ? '!' : ( 'tip' === $type ? '↗' : '•' ); ?></span><div><strong class="ap-callout__title"><?php echo esc_html( $title ); ?></strong><p><?php echo esc_html( $attributes['text'] ?? '' ); ?></p><?php if ( ! empty( $attributes['linkLabel'] ) && ! empty( $attributes['linkUrl'] ) ) : ?><a href="<?php echo esc_url( $attributes['linkUrl'] ); ?>"><?php echo esc_html( $attributes['linkLabel'] ); ?></a><?php endif; ?></div></aside>
