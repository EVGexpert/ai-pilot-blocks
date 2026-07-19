<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$heading_size = in_array( $attributes['headingSize'] ?? 'auto', array( 'auto','xs','sm','md','lg','xl' ), true ) ? $attributes['headingSize'] : 'auto';
$tone = in_array( $attributes['tone'] ?? 'paper', array( 'paper','white','ink' ), true ) ? $attributes['tone'] : 'paper';
$count = max( 2, min( 6, (int) ( $attributes['count'] ?? 3 ) ) );
$columns = in_array( (int) ( $attributes['columns'] ?? 3 ), array( 2,3 ), true ) ? (int) $attributes['columns'] : 3;
$post_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();
$category_ids = $post_id ? wp_get_post_categories( $post_id ) : array();
$args = array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $count, 'post__not_in' => $post_id ? array( $post_id ) : array(), 'ignore_sticky_posts' => true, 'no_found_rows' => true );
if ( $category_ids ) { $args['category__in'] = $category_ids; }
$query = new WP_Query( $args );
if ( ! $query->have_posts() ) { return; }
$title_id = wp_unique_id( 'ap-related-posts-' );
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-related-posts is-tone-' . $tone . ' is-heading-' . $heading_size, 'aria-labelledby' => $title_id, 'style' => '--ap-related-columns:' . $columns ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-related-posts__inner"><h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $attributes['title'] ?? 'Читайте также' ); ?></h2><div class="ap-related-posts__grid"><?php while ( $query->have_posts() ) : $query->the_post(); ?><article class="ap-related-post"><a href="<?php the_permalink(); ?>"><?php if ( ! empty( $attributes['showImage'] ) && has_post_thumbnail() ) : ?><figure><?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?></figure><?php endif; ?><div><span class="ap-related-post__date"><?php echo esc_html( get_the_date() ); ?></span><h3><?php the_title(); ?></h3><?php if ( ! empty( $attributes['showExcerpt'] ) ) : ?><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p><?php endif; ?><span class="ap-related-post__more">Читать <span aria-hidden="true">→</span></span></div></a></article><?php endwhile; wp_reset_postdata(); ?></div></div></section>
