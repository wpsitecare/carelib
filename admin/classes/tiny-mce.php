<?php
/**
 * Modifications to TinyMCE, the default WordPress editor.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * A class to customize the default styleselect options in the WP TinyMCE.
 *
 * @package SiteCareLibrary
 */
class SiteCare_TinyMCE_Admin {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   SiteCare_Author_Box::$wp_hooks
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	private function wp_hooks() {
		add_filter( 'mce_buttons',          array( $this, 'add_styleselect' ),     99 );
		add_filter( 'mce_buttons_2',        array( $this, 'disable_styleselect' ), 99 );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_formats' ),     99 );
	}

	/**
	 * Add styleselect button to the end of the first row of TinyMCE buttons.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $buttons array existing TinyMCE buttons
	 * @return $buttons array modified TinyMCE buttons
	 */
	public function add_styleselect( $buttons ) {
		// Get rid of styleselect if it's been added somewhere else.
		if ( in_array( 'styleselect', $buttons ) ) {
			unset( $buttons['styleselect'] );
		}
		array_push( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Remove styleselect button if it's been added to the second row of TinyMCE
	 * buttons.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $buttons array existing TinyMCE buttons
	 * @return $buttons array modified TinyMCE buttons
	 */
	public function disable_styleselect( $buttons ) {
		if ( in_array( 'styleselect', $buttons ) ) {
			unset( $buttons['styleselect'] );
		}
		return $buttons;
	}

	/**
	 * Add our custom SiteCare styles to the styleselect dropdown button.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array existing TinyMCE arguments
	 * @return $args array modified TinyMCE arguments
	 * @see    http://wordpress.stackexchange.com/a/128950/9844
	 */
	public function tinymce_formats( $args ) {
		$sitecare_formats = apply_filters( 'sitecare_tiny_mce_formats',
			array(
				array(
					'title'    => __( 'Drop Cap', 'sitecare-library' ),
					'inline'   => 'span',
					'classes'  => 'dropcap',
				),
				array(
					'title'    => __( 'Pull Quote Left', 'sitecare-library' ),
					'block'    => 'blockquote',
					'classes'  => 'pullquote alignleft',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Pull Quote Right', 'sitecare-library' ),
					'block'    => 'blockquote',
					'classes'  => 'pullquote alignright',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Intro Paragraph', 'sitecare-library' ),
					'selector' => 'p',
					'classes'  => 'intro-pagragraph',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Call to Action', 'sitecare-library' ),
					'block'    => 'div',
					'classes'  => 'call-to-action',
					'wrapper'  => true,
					'exact'    => true,
				),
				array(
					'title'    => __( 'Feature Box', 'sitecare-library' ),
					'block'    => 'div',
					'classes'  => 'feature-box',
					'wrapper'  => true,
					'exact'    => true,
				),
				array(
					'title'    => __( 'Code Block', 'sitecare-library' ),
					'format'   => 'pre',
				),
				array(
					'title'    => __( 'Buttons', 'sitecare-library' ),
					'items'    => array(
						array(
							'title'    => __( 'Standard', 'sitecare-library' ),
							'selector' => 'a',
							'classes'  => 'button',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Standard Block', 'sitecare-library' ),
							'selector' => 'a',
							'classes'  => 'button block',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Call to Action', 'sitecare-library' ),
							'selector' => 'a',
							'classes'  => 'button secondary cta',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Call to Action Block', 'sitecare-library' ),
							'selector' => 'a',
							'classes'  => 'button secondary cta block',
							'exact'    => true,
						),
					),
				),
			)
		);
		// Merge with any existing formats which have been added by plugins.
		if ( ! empty( $args['style_formats'] ) ) {
			$existing_formats = json_decode( $args['style_formats'] );
			$sitecare_formats = array_merge( $sitecare_formats, $existing_formats );
		}

		$args['style_formats'] = wp_json_encode( $sitecare_formats );

		return $args;
	}
}