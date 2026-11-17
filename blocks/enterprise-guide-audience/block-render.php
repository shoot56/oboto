<?php
$anchor = ! empty( $block['anchor'] ) ? sanitize_title( $block['anchor'] ) : 'enterprise-guide-audience';
$attributes = get_block_wrapper_attributes( array( 'class' => 'enterprise-guide enterprise-guide-audience alignfull' ) );
$title = (string) get_field( 'title' );
$roles = get_field( 'roles' );
$cta_title = (string) get_field( 'cta_title' );
$cta_description = (string) get_field( 'cta_description' );
?>
<section id="<?php echo esc_attr( $anchor ); ?>" <?php echo $attributes; ?>>
  <div class="enterprise-guide__container">
    <?php if ( $title ) : ?><h2><?php echo esc_html( $title ); ?></h2><?php endif; ?>
    <?php if ( is_array( $roles ) && $roles ) : ?>
      <div class="enterprise-guide-audience__roles">
        <?php foreach ( $roles as $role ) : ?><div class="enterprise-guide-audience__role"><h3><?php echo esc_html( $role['title'] ?? '' ); ?></h3><p><?php echo esc_html( $role['description'] ?? '' ); ?></p></div><?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="enterprise-guide-audience__cta">
      <div><?php if ( $cta_title ) : ?><h2><?php echo esc_html( $cta_title ); ?></h2><?php endif; ?><?php if ( $cta_description ) : ?><p><?php echo esc_html( $cta_description ); ?></p><?php endif; ?></div>
      <div class="enterprise-guide-audience__actions"><a class="enterprise-guide__button" href="#download"><?php esc_html_e( 'Download the guide', 'oboto' ); ?></a><a class="enterprise-guide__button enterprise-guide__button--outline" href="<?php echo esc_url( home_url( '/try/' ) ); ?>"><?php esc_html_e( 'Try Obot Now', 'oboto' ); ?></a></div>
    </div>
  </div>
</section>
