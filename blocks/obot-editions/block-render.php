<?php

/**
 * Block template file: block-render.php
 *
 * @param array      $block The block settings and attributes.
 * @param string     $content The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

$id = 'obot-editions-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$wrapper_classes = 'obot-editions';
if ( empty( $block['align'] ) ) {
	$wrapper_classes .= ' alignfull';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => $wrapper_classes,
	)
);

$eyebrow        = trim( (string) get_field( 'eyebrow' ) );
$title          = trim( (string) get_field( 'title' ) );
$rows           = get_field( 'cards' );
$cards          = array();
$default_colors = array( '#4cc2a8', '#5b9bff', '#8f8ff7' );

$hex_to_rgb = static function ( $color ) {
	$hex = ltrim( $color, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	return implode(
		', ',
		array_map(
			'hexdec',
			str_split( $hex, 2 )
		)
	);
};

if ( is_array( $rows ) ) {
	foreach ( $rows as $row_index => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$accent_color = sanitize_hex_color( (string) ( $row['accent_color'] ?? '' ) );
		if ( ! $accent_color ) {
			$accent_color = $default_colors[ $row_index % count( $default_colors ) ];
		}

		$card_eyebrow = trim( (string) ( $row['eyebrow'] ?? '' ) );
		$card_title   = trim( (string) ( $row['title'] ?? '' ) );
		$card_text    = trim( (string) ( $row['text'] ?? '' ) );
		$option_rows  = $row['options'] ?? array();
		$options      = array();
		$button       = $row['button'] ?? null;

		if ( is_array( $option_rows ) ) {
			foreach ( $option_rows as $option_row ) {
				if ( ! is_array( $option_row ) ) {
					continue;
				}

				$option_text = trim( (string) ( $option_row['text'] ?? '' ) );
				$status      = sanitize_key( (string) ( $option_row['status'] ?? 'check' ) );
				if ( ! in_array( $status, array( 'check', 'cross' ), true ) ) {
					$status = 'check';
				}

				if ( '' === $option_text ) {
					continue;
				}

				$options[] = array(
					'text'   => $option_text,
					'status' => $status,
				);
			}
		}

		if ( is_string( $button ) && '' !== trim( $button ) ) {
			$button = array(
				'url'   => trim( $button ),
				'title' => '',
				'target' => '',
			);
		}

		$has_button = is_array( $button ) && ! empty( $button['url'] );
		if ( '' === $card_eyebrow && '' === $card_title && '' === $card_text && ! $options && ! $has_button ) {
			continue;
		}

		$cards[] = array(
			'accent_color' => $accent_color,
			'accent_rgb'   => $hex_to_rgb( $accent_color ),
			'eyebrow'      => $card_eyebrow,
			'title'        => $card_title,
			'text'         => $card_text,
			'options'      => $options,
			'button'       => $has_button ? $button : null,
		);
	}
}

?>
<section id="<?php echo esc_attr( $id ); ?>" <?php echo $wrapper_attributes; ?>>
	<div class="obot-editions__inner">
		<?php if ( $eyebrow || $title ) : ?>
			<header class="obot-editions__header">
				<?php if ( $eyebrow ) : ?>
					<div class="obot-editions__eyebrow">
						<span class="obot-editions__eyebrow-dot" aria-hidden="true"></span>
						<span><?php echo esc_html( $eyebrow ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="obot-editions__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $cards ) : ?>
			<div class="obot-editions__grid">
				<?php foreach ( $cards as $index => $card ) : ?>
					<?php
					$is_recommended = 1 === $index;
					$card_classes   = 'obot-editions__card';
//					if ( $is_recommended ) {
//						$card_classes .= ' is-recommended';
//					}

					$card_style = sprintf(
						'--obot-editions-accent: %s; --obot-editions-accent-rgb: %s;',
						$card['accent_color'],
						$card['accent_rgb']
					);
					?>
					<article class="<?php echo esc_attr( $card_classes ); ?>" style="<?php echo esc_attr( $card_style ); ?>">
<!--						--><?php //if ( $is_recommended ) : ?>
<!--							<span class="obot-editions__recommended">--><?php //esc_html_e( 'Recommended', 'oboto' ); ?><!--</span>-->
<!--						--><?php //endif; ?>

						<?php if ( $card['eyebrow'] ) : ?>
							<div class="obot-editions__card-eyebrow"><?php echo esc_html( $card['eyebrow'] ); ?></div>
						<?php endif; ?>

						<?php if ( $card['title'] ) : ?>
							<h3 class="obot-editions__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( $card['text'] ) : ?>
							<p class="obot-editions__card-text"><?php echo nl2br( esc_html( $card['text'] ) ); ?></p>
						<?php endif; ?>

						<?php if ( $card['options'] ) : ?>
							<ul class="obot-editions__options">
								<?php foreach ( $card['options'] as $option ) : ?>
									<li class="obot-editions__option is-<?php echo esc_attr( $option['status'] ); ?>">
										<span class="obot-editions__option-icon" aria-hidden="true"></span>
										<span><?php echo esc_html( $option['text'] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( $card['button'] ) : ?>
							<?php
							$button       = $card['button'];
							$button_title = ! empty( $button['title'] ) ? $button['title'] : __( 'Learn more', 'oboto' );
							$button_target = ! empty( $button['target'] ) ? $button['target'] : '';
							?>
							<a
								class="obot-editions__button"
								href="<?php echo esc_url( $button['url'] ); ?>"
								<?php echo $button_target ? 'target="' . esc_attr( $button_target ) . '"' : ''; ?>
								<?php echo '_blank' === $button_target ? 'rel="noopener noreferrer"' : ''; ?>
							><?php echo esc_html( $button_title ); ?></a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $is_preview ) : ?>
			<div class="obot-editions__placeholder">
				<?php esc_html_e( 'Complete the three edition cards in the block fields.', 'oboto' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
