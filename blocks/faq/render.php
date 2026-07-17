<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_slice( $attributes['items'], 0, 12 ) : array();
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-faq' ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-faq__inner"><div><?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?><h2 class="ap-section-title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2></div><div class="ap-faq__items"><?php foreach ( $items as $item ) : ?><details><summary><?php echo esc_html( $item['question'] ?? '' ); ?></summary><div class="ap-faq__answer"><?php echo wp_kses_post( wpautop( $item['answer'] ?? '' ) ); ?></div></details><?php endforeach; ?></div></div></section>
