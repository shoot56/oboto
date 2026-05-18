<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'landing-hero-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-landing-hero';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$heading             = trim( (string) get_field( 'heading' ) );
$subheading_lead     = trim( (string) get_field( 'subheading_lead' ) );
$description         = trim( (string) get_field( 'description' ) );
$primary_button      = get_field( 'primary_button' );
$github_intro        = trim( (string) get_field( 'github_intro' ) );
$github_link         = get_field( 'github_link' );
$subheading_emphasis = get_field( 'subheading_emphasis' );

$normalize_emphasis_items = static function ( $value ) {
	$items = array();

	if ( is_array( $value ) ) {
		foreach ( $value as $row ) {
			if ( is_array( $row ) && ! empty( $row['text'] ) ) {
				$items[] = $row['text'];
				continue;
			}

			if ( is_string( $row ) && $row !== '' ) {
				$items[] = $row;
			}
		}
	} elseif ( is_string( $value ) && $value !== '' ) {
		$items[] = $value;
	}

	$items = array_values( array_filter( array_map( 'trim', $items ) ) );

	return $items;
};

$subheading_emphasis_items = $normalize_emphasis_items( $subheading_emphasis );
$has_subheading_emphasis   = ! empty( $subheading_emphasis_items );
$has_rotating_emphasis     = count( $subheading_emphasis_items ) > 1;
$subheading_rotator_attrs  = '';

if ( $has_rotating_emphasis ) {
	$subheading_rotator_attrs = sprintf(
		' data-obot-landing-hero-rotator data-obot-landing-hero-texts="%s" aria-live="polite"',
		esc_attr( wp_json_encode( $subheading_emphasis_items ) )
	);
}

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-landing-hero__glow obot-landing-hero__glow--top" aria-hidden="true"></div>
	<div class="obot-landing-hero__glow obot-landing-hero__glow--bottom" aria-hidden="true"></div>

	<div class="obot-landing-hero__visuals" aria-hidden="true" role="presentation">
		<svg class="obot-landing-hero__network" viewBox="0 0 1600 900" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
			<path d="M 230 120 Q 500 150 760 450" />
			<path d="M 230 250 Q 500 280 760 450" />
			<path d="M 230 400 Q 500 430 760 450" />
			<path d="M 230 560 Q 500 590 760 450" />
			<path d="M 230 720 Q 500 750 760 450" />
			<path d="M 1370 100 Q 1100 130 840 450" class="obot-landing-hero__network-path--reverse" />
			<path d="M 1370 240 Q 1100 270 840 450" class="obot-landing-hero__network-path--reverse" />
			<path d="M 1370 390 Q 1100 420 840 450" class="obot-landing-hero__network-path--reverse" />
			<path d="M 1370 540 Q 1100 570 840 450" class="obot-landing-hero__network-path--reverse" />
			<path d="M 1370 700 Q 1100 730 840 450" class="obot-landing-hero__network-path--reverse" />
		</svg>

		<div class="obot-landing-hero__dots">
			<?php for ( $dot_index = 1; $dot_index <= 14; $dot_index++ ) : ?>
				<span class="obot-landing-hero__dot obot-landing-hero__dot--<?php echo esc_attr( (string) $dot_index ); ?>"></span>
			<?php endfor; ?>
		</div>
	</div>

	<div class="obot-landing-hero__inner">
		<div class="obot-landing-hero__copy">
			<?php if ( $heading ) : ?>
				<h1 class="obot-landing-hero__title"<?php oboto_the_aos_attributes( 100 ); ?>><?php echo esc_html( $heading ); ?></h1>
			<?php endif; ?>

			<?php if ( $subheading_lead || $has_subheading_emphasis ) : ?>
				<div class="obot-landing-hero__subtitle"<?php oboto_the_aos_attributes( 200 ); ?>>
					<?php if ( $subheading_lead ) : ?>
						<?php echo esc_html( $subheading_lead ); ?>
					<?php endif; ?>

					<?php if ( $has_subheading_emphasis ) : ?>
						<span class="obot-landing-hero__subtitle-emphasis<?php echo $has_rotating_emphasis ? ' is-rotating' : ''; ?>">
							<span class="obot-landing-hero__typing"<?php echo $subheading_rotator_attrs; ?>>
								<span class="obot-landing-hero__typing-current" data-obot-landing-hero-current><?php echo esc_html( $subheading_emphasis_items[0] ); ?></span>
							</span><span class="obot-landing-hero__cursor" aria-hidden="true"></span>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="obot-landing-hero__description"<?php oboto_the_aos_attributes( 300 ); ?>><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>

			<?php if ( is_array( $primary_button ) && ! empty( $primary_button['url'] ) ) : ?>
				<?php
				$primary_button_target = ! empty( $primary_button['target'] ) ? $primary_button['target'] : '';
				$primary_button_title  = ! empty( $primary_button['title'] ) ? $primary_button['title'] : $primary_button['url'];
				?>
				<div class="obot-landing-hero__actions"<?php oboto_the_aos_attributes( 360 ); ?>>
					<a
						class="obot-landing-how__button obot-landing-how__button--primary"
						href="<?php echo esc_url( $primary_button['url'] ); ?>"
						<?php echo $primary_button_target ? 'target="' . esc_attr( $primary_button_target ) . '"' : ''; ?>
						<?php echo $primary_button_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
					>
						<span><?php echo esc_html( $primary_button_title ); ?></span>
						<span class="obot-landing-how__button-arrow obot-landing-how__button-arrow--right" aria-hidden="true"></span>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( is_array( $github_link ) && ! empty( $github_link['url'] ) ) : ?>
			<div class="obot-landing-hero__conversion"<?php oboto_the_aos_attributes( 400 ); ?>>
				<p class="obot-landing-hero__meta">
					<?php if ( $github_intro ) : ?>
						<span><?php echo esc_html( $github_intro ); ?></span>
					<?php endif; ?>
					<a class="obot-landing-hero__meta-link" href="<?php echo esc_url( $github_link['url'] ); ?>"<?php echo ! empty( $github_link['target'] ) ? ' target="' . esc_attr( $github_link['target'] ) . '" rel="noopener"' : ''; ?>>
						<?php echo esc_html( ! empty( $github_link['title'] ) ? $github_link['title'] : $github_link['url'] ); ?>
					</a>
				</p>
			</div>
		<?php endif; ?>
	</div>
</section>
