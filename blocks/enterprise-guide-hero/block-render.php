<?php
$anchor = ! empty( $block['anchor'] ) ? sanitize_title( $block['anchor'] ) : 'enterprise-guide-hero';
$attributes = get_block_wrapper_attributes( array( 'class' => 'enterprise-guide enterprise-guide-hero alignfull' ) );
$eyebrow = (string) get_field( 'eyebrow' );
$title = (string) get_field( 'title' );
$description = (string) get_field( 'description' );
$benefits = get_field( 'benefits' );
$form_title = (string) get_field( 'form_title' );
$form_description = (string) get_field( 'form_description' );
$form_embed = (string) get_field( 'form_embed' );
$portal_id = '';
$form_id = '';
if ( preg_match( '/\bportalId\s*:\s*["\'](\d+)["\']/', $form_embed, $portal_match ) ) {
  $portal_id = $portal_match[1];
}
if ( preg_match( '/\bformId\s*:\s*["\']([a-zA-Z0-9-]+)["\']/', $form_embed, $form_match ) ) {
  $form_id = $form_match[1];
}
$form_target = 'enterprise-guide-form-' . sanitize_html_class( $block['id'] );
?>
<section id="<?php echo esc_attr( $anchor ); ?>" <?php echo $attributes; ?>>
  <div class="enterprise-guide__container enterprise-guide-hero__layout">
    <div class="enterprise-guide-hero__copy">
      <?php if ( $eyebrow ) : ?><p class="enterprise-guide__eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
      <?php if ( $title ) : ?><h1><?php echo esc_html( $title ); ?></h1><?php endif; ?>
      <?php if ( $description ) : ?><p class="enterprise-guide-hero__description"><?php echo esc_html( $description ); ?></p><?php endif; ?>
      <?php if ( is_array( $benefits ) && $benefits ) : ?>
        <ul class="enterprise-guide-hero__benefits">
          <?php foreach ( $benefits as $benefit ) : ?>
            <?php if ( ! empty( $benefit['text'] ) ) : ?><li><?php echo esc_html( $benefit['text'] ); ?></li><?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
    <div id="download" class="enterprise-guide-hero__form-card">
      <?php if ( $form_title ) : ?><h2><?php echo esc_html( $form_title ); ?></h2><?php endif; ?>
      <?php if ( $form_description ) : ?><p class="enterprise-guide-hero__form-description"><?php echo esc_html( $form_description ); ?></p><?php endif; ?>
      <?php if ( $is_preview ) : ?>
        <p><?php echo $portal_id && $form_id ? esc_html__( 'The HubSpot form appears on the published page.', 'oboto' ) : esc_html__( 'Paste a HubSpot form embed in the block settings.', 'oboto' ); ?></p>
      <?php elseif ( $portal_id && $form_id ) : ?>
        <div id="<?php echo esc_attr( $form_target ); ?>" class="enterprise-guide-hero__form" data-portal-id="<?php echo esc_attr( $portal_id ); ?>" data-form-id="<?php echo esc_attr( $form_id ); ?>"></div>
      <?php endif; ?>
    </div>
  </div>
</section>
