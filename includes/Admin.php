<?php

namespace ACPL\AIAltGenerator;

class Admin {
	const SETTINGS_SECTION_ID = 'acpl_ai_alt_generator_section';

	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_menu', [ $this, 'add_plugin_settings' ] );
	}

	public function register_settings(): void {
		register_setting(
			'media',
			AltGeneratorPlugin::OPTION_NAME,
			[
				'type'              => 'array',
				'sanitize_callback' => function ( $input ) {
					$input['api_key']       = isset( $input['api_key'] ) ? sanitize_text_field( $input['api_key'] ) : null;
					$input['auto_generate'] = isset( $input['auto_generate'] ) && $input['auto_generate'];
					$input['detail']        = isset( $input['detail'] ) ? sanitize_text_field( $input['detail'] ) : 'low';

					return $input;
				},
				'default'           => [
					'api_key'       => null,
					'auto_generate' => false,
					'detail'        => 'low',
				],
				'show_in_rest'      => false,
			]
		);
	}

	public function add_plugin_settings(): void {
		$options = AltGeneratorPlugin::get_options();

		add_settings_section(
			self::SETTINGS_SECTION_ID,
			__( 'GPT Vision Alt Generator', 'acpl-ai-alt-generator' ),
			function () {
				echo '<p>' .
					esc_html__( 'This plugin uses the OpenAI API to generate alt text for images.', 'acpl-ai-alt-generator' )
					. '</p>';
			},
			'media',
			[
				'before_section' => sprintf( '<div id="%s">', self::SETTINGS_SECTION_ID ),
				'after_section'  => '</div>',
			]
		);

		add_settings_field(
			'acpl_ai_alt_generator_api_key',
			__( 'OpenAI API Key', 'acpl-ai-alt-generator' ),
			function () use ( $options ) {
				printf(
					'<input type="password" id="openai_api_key" name="%1$s[api_key]" value="%2$s" class="regular-text" placeholder="sk-..." autocomplete="off"/>',
					esc_attr( AltGeneratorPlugin::OPTION_NAME ),
					esc_attr( $options['api_key'] ?? '' )
				);

				echo '<p class="description">' .
					wp_kses(
						sprintf(
							// translators: %s is for link attributes.
							__(
								'Enter your OpenAI API key here. You can find it in your <a href="https://platform.openai.com/account/api-keys" %s>OpenAI account settings</a>.',
								'acpl-ai-alt-generator'
							),
							'target="_blank" rel="noopener noreferrer"'
						),
						[
							'a' => [
								'href'   => [],
								'target' => [],
								'rel'    => [],
							],
						]
					)
				. '</p>';
			},
			'media',
			self::SETTINGS_SECTION_ID,
			[
				'label_for' => 'openai_api_key',
			]
		);

		add_settings_field(
			'acpl_ai_alt_generator_auto_generate',
			__( 'Auto generate alt text on image upload', 'acpl-ai-alt-generator' ),
			function () use ( $options ) {
				printf(
					'<input type="checkbox" id="auto_generate_alt" name="%1$s[auto_generate]" %2$s/>',
					esc_attr( AltGeneratorPlugin::OPTION_NAME ),
					checked( $options['auto_generate'] ?? false, true, false )
				);

				echo '<p class="description">' .
					esc_html__(
						'Enable this option to automatically generate alt text when images are uploaded. Please review generated alt texts as GPT can sometimes produce inaccurate descriptions.',
						'acpl-ai-alt-generator'
					)
				. '</p>';
			},
			'media',
			self::SETTINGS_SECTION_ID,
			[
				'label_for' => 'auto_generate_alt',
			]
		);

		add_settings_field(
			'acpl_ai_alt_generator_img_size',
			__( 'Detail level', 'acpl-ai-alt-generator' ),
			function () use ( $options ) {
				$detail_levels = [
					'high' => _x( 'High', 'Detail level', 'acpl-ai-alt-generator' ),
					'low'  => _x( 'Low', 'Detail level', 'acpl-ai-alt-generator' ),
				];

				printf( '<select id="detail_level" name="%s[detail]">', esc_attr( AltGeneratorPlugin::OPTION_NAME ) );
				foreach ( $detail_levels as $detail => $label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $detail ),
						selected( $options['detail'] ?? 'low', $detail, false ),
						esc_html( $label )
					);
				}
				echo '</select>';

				echo '<p class="description">' .
					wp_kses(
						sprintf(
							// translators: %s is for link attributes.
							__(
								'Choose "Low" detail to minimize token usage and costs for image processing, which should be sufficient for most use cases and is significantly cheaper. "High" detail will use more tokens but provides finer detail. For precise token calculations and cost implications, refer to the <a href="https://platform.openai.com/docs/guides/vision/calculating-costs" %s>OpenAI documentation on calculating costs</a>.',
								'acpl-ai-alt-generator'
							),
							'target="_blank" rel="noopener noreferrer"'
						),
						[
							'a' => [
								'href'   => [],
								'target' => [],
								'rel'    => [],
							],
						]
					)
				. '</p>';
			},
			'media',
			self::SETTINGS_SECTION_ID,
			[
				'label_for' => 'detail_level',
			]
		);
	}
}
