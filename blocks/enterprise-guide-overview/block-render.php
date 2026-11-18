<?php
$anchor = ! empty( $block['anchor'] ) ? sanitize_title( $block['anchor'] ) : 'enterprise-guide-overview';
$attributes = get_block_wrapper_attributes( array( 'class' => 'enterprise-guide enterprise-guide-overview alignfull' ) );
$metrics = get_field( 'metrics' );
$eyebrow = (string) get_field( 'eyebrow' );
$title = (string) get_field( 'title' );
$features = get_field( 'features' );
$cover = get_field( 'cover' );
$cover_url = is_array( $cover ) && ! empty( $cover['url'] ) ? $cover['url'] : get_template_directory_uri() . '/images/enterprise-mcp-guide-cover.webp';
$cover_alt = is_array( $cover ) && ! empty( $cover['alt'] ) ? $cover['alt'] : __( 'Enterprise MCP Quick Start Guide cover', 'oboto' );
$feature_icons = array(
  'chart' => '<path d="M4.5 20V16"/><path d="M9.5 20V12"/><path d="M14.5 20V8"/><path d="M19.5 20V4"/><path d="M22 20H2"/>',
  'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M8 3v4"/><path d="M16 3v4"/>',
  'shield' => '<path d="M12 3l8 3v6c0 4.5-3.4 8.2-8 9-4.6-.8-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
  'catalog' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><path d="M17.5 14v7"/><path d="M14 17.5h7"/>',
  'network' => '<circle cx="5" cy="12" r="2.5"/><circle cx="19" cy="5" r="2.5"/><circle cx="19" cy="19" r="2.5"/><path d="M7.3 11l9.4-5"/><path d="M7.3 13l9.4 5"/>',
);
$allowed_icon_tags = array(
  'path' => array( 'd' => true ),
  'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
  'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
);
?>
<section id="<?php echo esc_attr( $anchor ); ?>" <?php echo $attributes; ?>>
  <div class="enterprise-guide__container">
    <?php if ( is_array( $metrics ) && $metrics ) : ?>
      <div class="enterprise-guide-overview__metrics">
        <?php foreach ( $metrics as $metric ) : ?>
          <div class="enterprise-guide-overview__metric"><strong><?php echo esc_html( $metric['value'] ?? '' ); ?></strong><span><?php echo esc_html( $metric['label'] ?? '' ); ?></span></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="enterprise-guide-overview__layout">
      <div class="enterprise-guide-overview__cover"><img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php echo esc_attr( $cover_alt ); ?>" loading="lazy"></div>
      <div class="enterprise-guide-overview__copy">
        <?php if ( $eyebrow ) : ?><p class="enterprise-guide__section-label"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
        <?php if ( $title ) : ?><h2><?php echo esc_html( $title ); ?></h2><?php endif; ?>
        <?php if ( is_array( $features ) && $features ) : ?>
          <div class="enterprise-guide-overview__features">
            <?php foreach ( $features as $index => $feature ) : ?>
              <?php
              $icon = sanitize_key( $feature['icon'] ?? '' );
              if ( ! isset( $feature_icons[ $icon ] ) ) {
                $icon = array_keys( $feature_icons )[ $index % count( $feature_icons ) ];
              }
              ?>
              <div class="enterprise-guide-overview__feature">
                <span class="enterprise-guide-overview__feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( $feature_icons[ $icon ], $allowed_icon_tags ); ?></svg></span>
                <div><h3><?php echo esc_html( $feature['title'] ?? '' ); ?></h3><p><?php echo esc_html( $feature['description'] ?? '' ); ?></p></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
