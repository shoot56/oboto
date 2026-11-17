<?php
$anchor = ! empty( $block['anchor'] ) ? sanitize_title( $block['anchor'] ) : 'enterprise-guide-maturity';
$attributes = get_block_wrapper_attributes( array( 'class' => 'enterprise-guide enterprise-guide-maturity alignfull' ) );
$eyebrow = (string) get_field( 'eyebrow' );
$title = (string) get_field( 'title' );
$description = (string) get_field( 'description' );
$stages = get_field( 'stages' );
?>
<section id="<?php echo esc_attr( $anchor ); ?>" <?php echo $attributes; ?>>
  <div class="enterprise-guide__container">
    <?php if ( $eyebrow ) : ?><p class="enterprise-guide__section-label"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
    <?php if ( $title ) : ?><h2><?php echo esc_html( $title ); ?></h2><?php endif; ?>
    <?php if ( $description ) : ?><p class="enterprise-guide-maturity__intro"><?php echo esc_html( $description ); ?></p><?php endif; ?>
    <?php if ( is_array( $stages ) && $stages ) : ?>
      <div class="enterprise-guide-maturity__interactive">
        <div class="enterprise-guide-maturity__track" aria-hidden="true">
          <div class="enterprise-guide-maturity__track-line"></div>
          <div class="enterprise-guide-maturity__track-fill"></div>
          <div class="enterprise-guide-maturity__nodes">
            <?php foreach ( $stages as $index => $stage ) : ?>
              <span class="enterprise-guide-maturity__node"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg></span>
            <?php endforeach; ?>
          </div>
        </div>
      <ol class="enterprise-guide-maturity__stages">
        <?php foreach ( $stages as $index => $stage ) : ?>
          <li class="enterprise-guide-maturity__stage" tabindex="0">
            <div class="enterprise-guide-maturity__stage-heading"><span class="enterprise-guide-maturity__number"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span><span class="enterprise-guide-maturity__stage-label"><?php echo esc_html( sprintf( __( 'Stage %s', 'oboto' ), array( 'one', 'two', 'three', 'four' )[ $index ] ?? (string) ( $index + 1 ) ) ); ?></span></div>
            <h3><?php echo esc_html( $stage['title'] ?? '' ); ?></h3>
            <?php if ( ! empty( $stage['description'] ) ) : ?>
              <ul>
                <?php foreach ( preg_split( '/\r\n|\r|\n/', (string) $stage['description'] ) as $point ) : ?>
                  <?php if ( trim( $point ) !== '' ) : ?><li><?php echo esc_html( trim( $point ) ); ?></li><?php endif; ?>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ol>
      </div>
    <?php endif; ?>
  </div>
</section>
