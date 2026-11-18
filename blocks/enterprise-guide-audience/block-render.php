<?php
$anchor = ! empty( $block['anchor'] ) ? sanitize_title( $block['anchor'] ) : 'enterprise-guide-audience';
$attributes = get_block_wrapper_attributes( array( 'class' => 'enterprise-guide enterprise-guide-audience alignfull' ) );
$title = (string) get_field( 'title' );
$roles = get_field( 'roles' );
$cta_title = (string) get_field( 'cta_title' );
$cta_description = (string) get_field( 'cta_description' );
$primary_button = get_field( 'primary_button' );
$secondary_button = get_field( 'secondary_button' );
$buttons = array();
foreach ( array( 'primary' => $primary_button, 'secondary' => $secondary_button ) as $variant => $button ) {
  if ( is_array( $button ) && ! empty( $button['url'] ) && ! empty( $button['title'] ) ) {
    $buttons[] = array( 'variant' => $variant, 'link' => $button );
  }
}
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
      <?php if ( $buttons ) : ?>
        <div class="enterprise-guide-audience__actions">
          <?php foreach ( $buttons as $button_item ) : $button = $button_item['link']; ?>
            <a class="enterprise-guide__button<?php echo $button_item['variant'] === 'secondary' ? ' enterprise-guide__button--outline' : ''; ?>" href="<?php echo esc_url( $button['url'] ); ?>"<?php if ( ! empty( $button['target'] ) ) : ?> target="<?php echo esc_attr( $button['target'] ); ?>"<?php endif; ?><?php if ( ( $button['target'] ?? '' ) === '_blank' ) : ?> rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html( $button['title'] ); ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
