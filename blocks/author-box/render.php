<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$heading_size = in_array( $attributes['headingSize'] ?? 'sm', array( 'auto','xs','sm','md','lg','xl' ), true ) ? $attributes['headingSize'] : 'sm';
$tone = in_array( $attributes['tone'] ?? 'white', array( 'white','paper','ink' ), true ) ? $attributes['tone'] : 'white';
$post_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();
$source = in_array( $attributes['source'] ?? 'post-author', array( 'post-author','manual' ), true ) ? $attributes['source'] : 'post-author';
$name = $role = $bio = $url = $avatar = '';
if ( 'manual' === $source ) {
    $name = trim( (string) ( $attributes['manualName'] ?? '' ) );
    $role = trim( (string) ( $attributes['manualRole'] ?? '' ) );
    $bio = trim( (string) ( $attributes['manualBio'] ?? '' ) );
    $url = trim( (string) ( $attributes['manualUrl'] ?? '' ) );
    if ( ! empty( $attributes['manualImageId'] ) ) {
        $avatar = wp_get_attachment_image( (int) $attributes['manualImageId'], 'thumbnail', false, array( 'alt' => $name, 'class' => 'ap-author-box__avatar-image' ) );
    }
} else {
    $author_id = (int) get_post_field( 'post_author', $post_id );
    $name = trim( (string) get_the_author_meta( 'display_name', $author_id ) );
    $bio = trim( (string) get_the_author_meta( 'description', $author_id ) );
    $url = get_author_posts_url( $author_id );
    $avatar = get_avatar( $author_id, 160, '', $name, array( 'class' => 'ap-author-box__avatar-image' ) );
}
if ( '' === $name ) { return; }
$title_id = wp_unique_id( 'ap-author-box-' );
$wrapper = get_block_wrapper_attributes( array( 'class' => 'ap-author-box is-tone-' . $tone . ' is-heading-' . $heading_size, 'aria-labelledby' => $title_id ) );
?>
<section <?php echo $wrapper; ?>>
    <header class="ap-author-box__header"><h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $attributes['title'] ?? 'Об авторе' ); ?></h2></header>
    <div class="ap-author-box__card">
        <?php if ( ! empty( $attributes['showAvatar'] ) && $avatar ) : ?><div class="ap-author-box__avatar"><?php echo $avatar; ?></div><?php endif; ?>
        <div class="ap-author-box__content">
            <strong class="ap-author-box__name"><?php echo esc_html( $name ); ?></strong>
            <?php if ( $role && 'manual' === $source ) : ?><span class="ap-author-box__role"><?php echo esc_html( $role ); ?></span><?php endif; ?>
            <?php if ( ! empty( $attributes['showBio'] ) && $bio ) : ?><p><?php echo esc_html( $bio ); ?></p><?php endif; ?>
            <?php if ( ! empty( $attributes['showPostsLink'] ) && $url ) : ?><a href="<?php echo esc_url( $url ); ?>"><?php echo 'manual' === $source ? esc_html__( 'Подробнее об авторе', 'ai-pilot-blocks' ) : esc_html__( 'Все материалы автора', 'ai-pilot-blocks' ); ?> <span aria-hidden="true">→</span></a><?php endif; ?>
        </div>
    </div>
</section>
