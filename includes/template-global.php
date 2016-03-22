<?php
/**
 * Template helper functions used for global site elements.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      1.0.0
 */

/**
 * Load the base theme framework template.
 *
 * This works similarly to WP core's `get_template_part` except it imposes
 * some restrictions on the template's name and location.
 *
 * It also uses `require_once` as there should only be a single framework
 * template loaded on any given page.
 *
 * For the $name parameter, if the file is called `framework-special.php`
 * then specify "special".
 *
 * @since  1.0.0
 * @param  string $name The name of the specialized template.
 * @return void
 */
function carelib_framework( $name = '' ) {
	$templates = array();
	/**
	 * Fires before the default framework template file is loaded.
	 *
	 * @since 1.0.0
	 * @param string $name The name of the specialized framework template.
	 */
	do_action( "{$GLOBALS['carelib_prefix']}_framework", $name, $templates );

	$name      = (string) $name;
	$templates = (array) $templates;

	if ( ! empty( $name ) ) {
		$templates[] = "templates/framework-{$name}.php";
	}
	$templates[] = 'templates/framework.php';

	locate_template( $templates, true );
}

/**
 * Returns the formatted site title.
 *
 * @since  1.0.0
 * @access public
 * @param  array $args A list of arguments to control the output of the site title.
 * @return string
 */
function carelib_get_site_title( $args = array() ) {
	$defaults = apply_filters( "{$GLOBALS['carelib_prefix']}_site_title_defaults",
		array(
			'attr'   => 'site-title',
			'title'  => '<a href="' . esc_url( home_url() ) . '" rel="home">' . get_bloginfo( 'name' ) . '</a>',
			'tag'    => is_front_page() || is_home() ? 'h1' : 'p',
			'wrap'   => '<%1$s %2$s>%3$s</%1$s>',
			'before' => '',
			'after'  => '',
		)
	);

	$args = wp_parse_args( $args, $defaults );

	// Bail if required args have been removed via a filter.
	if ( ! isset( $args['attr'], $args['title'], $args['tag'], $args['wrap'] ) ) {
		return false;
	}

	$html = '';

	$html .= isset( $args['before'] ) ? $args['before'] : '';

	$html .= sprintf( $args['wrap'],
		$args['tag'],
		carelib_get_attr( $args['attr'] ),
		$args['title']
	);

	$html .= isset( $args['after'] ) ? $args['after'] : '';

	return apply_filters( "{$GLOBALS['carelib_prefix']}_site_title", $html, $args );
}

/**
 * Return the site description wrapped in a `<p>` tag.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_site_description() {
	if ( $desc = get_bloginfo( 'description' ) ) {
		$desc = sprintf( '<p %s>%s</p>',
			carelib_get_attr( 'site-description' ),
			$desc
		);
	}

	return apply_filters( "{$GLOBALS['carelib_prefix']}_site_description", $desc );
}

/**
 * Adds microdata to avatars.
 *
 * @since  1.0.0
 * @access public
 * @param  string $avatar
 * @return string
 */
function carelib_get_avatar( $avatar ) {
	return preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $avatar );
}

/**
 * Return an arbitrary widget as a template tag.
 *
 * This is literally just an output buffer around WordPress core's the_widget
 * function. I have no idea why core doesn't have a return function here.
 *
 * @since 1.0.0
 * @param string $widget   The widget's PHP class name (see class-wp-widget.php).
 * @param array  $instance Optional. The widget's instance settings. Default empty array.
 * @param array  $args {
 *     Optional. Array of arguments to configure the display of the widget.
 *
 *     @type string $before_widget HTML content that will be prepended to the widget's HTML output.
 *                                 Default `<div class="widget %s">`, where `%s` is the widget's class name.
 *     @type string $after_widget  HTML content that will be appended to the widget's HTML output.
 *                                 Default `</div>`.
 *     @type string $before_title  HTML content that will be prepended to the widget's title when displayed.
 *                                 Default `<h2 class="widgettitle">`.
 *     @type string $after_title   HTML content that will be appended to the widget's title when displayed.
 *                                 Default `</h2>`.
 * }
 */
function carelib_get_the_widget( $widget, $instance = array(), $args = array() ) {
	ob_start();
	the_widget( $widget, $instance, $args );
	return ob_get_clean();
}

/**
 * Check whether or not the user is viewing the static front page.
 *
 * @since  1.0.0
 * @access public
 * @return bool True if the user is viewing the static front page, false otherwise.
 */
function carelib_is_static_front_page() {
	if ( is_front_page() && ! is_home() ) {
		return true;
	}

	return false;
}

/**
 * Add all the WordPress default "the_content" filters to a specific hook.
 *
 * @since  1.0.0
 * @access public
 * @global $wp_embed
 * @param  string $hook The action hook to add content filters to.
 * @return void
 */
function carelib_add_the_content_filters( $hook ) {
	global $wp_embed;

	add_filter( $hook, array( $wp_embed, 'run_shortcode' ),  5 );
	add_filter( $hook, array( $wp_embed, 'autoembed' ),      5 );
	add_filter( $hook, 'wptexturize',                       10 );
	add_filter( $hook, 'convert_smilies',                   12 );
	add_filter( $hook, 'convert_chars',                     14 );
	add_filter( $hook, 'wpautop',                           16 );
	add_filter( $hook, 'shortcode_unautop',                 18 );
	add_filter( $hook, 'prepend_attachment',                20 );
	add_filter( $hook, 'wp_make_content_images_responsive', 22 );
	add_filter( $hook, 'capital_P_dangit',                  24 );
	add_filter( $hook, 'do_shortcode',                      26 );
}

/**
 * Remove all the WordPress default "the_content" filters from a specific hook.
 *
 * @since  1.0.0
 * @access public
 * @global $wp_embed
 * @param  string $hook The action hook to add content filters to.
 * @return void
 */
function carelib_remove_the_content_filters( $hook ) {
	global $wp_embed;

	remove_filter( $hook, array( $wp_embed, 'run_shortcode' ),  5 );
	remove_filter( $hook, array( $wp_embed, 'autoembed' ),      5 );
	remove_filter( $hook, 'wptexturize',                       10 );
	remove_filter( $hook, 'convert_smilies',                   12 );
	remove_filter( $hook, 'convert_chars',                     14 );
	remove_filter( $hook, 'wpautop',                           16 );
	remove_filter( $hook, 'shortcode_unautop',                 18 );
	remove_filter( $hook, 'prepend_attachment',                20 );
	remove_filter( $hook, 'wp_make_content_images_responsive', 22 );
	remove_filter( $hook, 'capital_P_dangit',                  24 );
	remove_filter( $hook, 'do_shortcode',                      26 );
}

/**
 * Format a link to the customizer panel.
 *
 * Since WordPress 4.1, the customizer panel allows for deeplinking, but setting
 * up a link can be rather tedious. This function wraps the query args required
 * to deep link to a customzer panel or control, plus return to the correct page
 * when the customizer is exited by the user.
 *
 * @since  1.0.0
 * @access public
 * @param  array $args options for how the link will be formatted.
 * @return string an escaped link to the WordPress customizer panel.
 */
function carelib_get_customizer_link( $args = array() ) {
	$defaults = array(
		'focus_type'   => '',
		'focus_target' => '',
		'return'       => get_permalink(),
	);

	$args = wp_parse_args( $args, $defaults );

	$query_args = array();
	$type       = $args['focus_type'];
	$target     = $args['focus_target'];
	$return     = $args['return'];

	if ( ! empty( $type ) && ! empty( $target ) ) {
		$query_args[] = array( 'autofocus' => array( $type => $target ) );
	}
	if ( ! empty( $return ) ) {
		$query_args['return'] = urlencode( wp_unslash( $return ) );
	}

	return esc_url( add_query_arg( $query_args, admin_url( 'customize.php' ) ) );
}

/**
 * Returns a formatted theme credit link.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_credit_link() {
	$link = sprintf( '<a class="author-link" href="%s" title="%s">%s</a>',
		'http://www.wpsitecare.com',
		__( 'Free WordPress Theme by', 'carelib' ) . ' WP Site Care',
		'WP Site Care'
	);
	return apply_filters( "{$GLOBALS['carelib_prefix']}_credit_link", $link );
}

/**
 * Returns formatted theme information.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_theme_info() {
	$info = '<div class="credit">';
	$info .= sprintf(
		// Translators: 1 is current year, 2 is site name/link, 3 is the theme author name/link.
		__( 'Copyright &#169; %1$s %2$s. Free WordPress Theme by %3$s', 'alpha' ),
		date_i18n( 'Y' ),
		carelib_get_site_link(),
		carelib_get_credit_link()
	);
	$info .= '</div>';
	return apply_filters( "{$GLOBALS['carelib_prefix']}_theme_info", $info );
}

/**
 * Returns a link back to the site.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_site_link() {
	return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>',
		esc_url( home_url() ),
		get_bloginfo( 'name' )
	);
}

/**
 * Returns a link to WordPress.org.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_wp_link() {
	return sprintf( '<a class="wp-link" href="%s">%s</a>',
		esc_url( __( 'http://wordpress.org', 'carelib' ) ),
		esc_html__( 'WordPress', 'carelib' )
	);
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_theme_link() {
	$theme = carelib_get_parent();
	$allowed = array(
		'abbr'    => array( 'title' => true ),
		'acronym' => array( 'title' => true ),
		'code'    => true,
		'em'      => true,
		'strong'  => true,
	);

	// Note: URI is escaped via `WP_Theme::markup_header()`.
	return sprintf( '<a class="theme-link" href="%s">%s</a>',
		$theme->display( 'ThemeURI' ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);
}

/**
 * Returns a link to the child theme URI.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_child_theme_link() {
	if ( ! is_child_theme() ) {
		return '';
	}

	$theme   = carelib_get_theme();
	$allowed = array(
		'abbr'    => array( 'title' => true ),
		'acronym' => array( 'title' => true ),
		'code'    => true,
		'em'      => true,
		'strong'  => true,
	);

	// Note: URI is escaped via `WP_Theme::markup_header()`.
	return sprintf( '<a class="child-link" href="%s">%s</a>',
		$theme->display( 'ThemeURI' ),
		wp_kses( $theme->display( 'Name' ),	$allowed )
	);
}
