<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$heading_size = in_array( $attributes['headingSize'] ?? 'auto', array( 'auto','xs','sm','md','lg','xl' ), true ) ? $attributes['headingSize'] : 'auto';
$question_size = in_array( $attributes['questionSize'] ?? 'auto', array( 'auto','sm','md','lg' ), true ) ? $attributes['questionSize'] : 'auto';
$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? array_slice( $attributes['items'], 0, 12 ) : array();
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-faq is-heading-' . $heading_size . ' is-question-' . $question_size ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-faq__inner"><div><?php if ( ! empty( $attributes['eyebrow'] ) ) : ?><p class="ap-kicker"><?php echo esc_html( $attributes['eyebrow'] ); ?></p><?php endif; ?><h2 class="ap-section-title"><?php echo esc_html( $attributes['title'] ?? '' ); ?></h2></div><div class="ap-faq__items"><?php foreach ( $items as $item ) : ?><details><summary><?php echo esc_html( $item['question'] ?? '' ); ?></summary><div class="ap-faq__answer"><?php echo wp_kses_post( wpautop( $item['answer'] ?? '' ) ); ?></div></details><?php endforeach; ?></div></div></section>
