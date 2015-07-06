<?php
/**
 * Additional helper functions that the framework or themes may use. The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

# Add extra support for post types.
add_action( 'init', 'carelib_add_post_type_support', 15 );

# Filters the title for untitled posts.
add_filter( 'the_title', 'carelib_untitled_post' );

# Filters the archive title and description.
add_filter( 'get_the_archive_title',       'carelib_archive_title_filter',       5 );
add_filter( 'get_the_archive_description', 'carelib_archive_description_filter', 5 );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type. Comments and trackbacks are added for the
 * 'attachment' post type. Technically, these are already used for attachments in core, but
 * they're not registered.
 *
 * @since 0.8.0
 * @access public
 * @return void
 */
function carelib_add_post_type_support() {
	// Add support for excerpts to the 'page' post type.
	add_post_type_support( 'page', array( 'excerpt' ) );

	// Add thumbnail support for audio and video attachments.
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );

	// Add theme layouts support to core and custom post types.
	add_post_type_support( 'post',              'theme-layouts' );
	add_post_type_support( 'page',              'theme-layouts' );
	add_post_type_support( 'attachment',        'theme-layouts' );

	add_post_type_support( 'forum',             'theme-layouts' );
	add_post_type_support( 'literature',        'theme-layouts' );
	add_post_type_support( 'portfolio_item',    'theme-layouts' );
	add_post_type_support( 'portfolio_project', 'theme-layouts' );
	add_post_type_support( 'product',           'theme-layouts' );
	add_post_type_support( 'restaurant_item',   'theme-layouts' );
}

/**
 * Function for setting the content width of a theme. This does not check if a content width has been set; it
 * simply overwrites whatever the content width is.
 *
 * @since  0.2.0
 * @access public
 * @param  int    $width
 * @return void
 */
function carelib_set_content_width( $width = '' ) {
	$GLOBALS['content_width'] = absint( $width );
}

/**
 * Function for getting the theme's content width.
 *
 * @since  0.2.0
 * @access public
 * @return int
 */
function carelib_get_content_width() {
	return absint( $GLOBALS['content_width'] );
}

/**
 * The WordPress.org theme review requires that a link be provided to the single post page for untitled
 * posts. This is a filter on 'the_title' so that an '(Untitled)' title appears in that scenario, allowing
 * for the normal method to work.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $title
 * @return string
 */
function carelib_untitled_post( $title ) {
	// Translators: Used as a placeholder for untitled posts on non-singular views.
	if ( ! $title && ! is_singular() && in_the_loop() && ! is_admin() ) {
		$title = esc_html__( '(Untitled)', 'carelib' );
	}

	return $title;
}


/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $location
 * @return string
 */
function carelib_get_menu_location_name( $location ) {

	$locations = get_registered_nav_menus();

	return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
}

/**
 * Function for grabbing a WP nav menu name based on theme location.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $location
 * @return string
 */
function carelib_get_menu_name( $location ) {

	$locations = get_nav_menu_locations();

	return isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] )->name : '';
}

/**
 * Filters `get_the_archve_title` to add better archive titles than core.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $title
 * @return string
 */
function carelib_archive_title_filter( $title ) {
	if ( is_home() && !is_front_page() )
		$title = get_post_field( 'post_title', get_queried_object_id() );

	elseif ( is_category() )
		$title = single_cat_title( '', false );

	elseif ( is_tag() )
		$title = single_tag_title( '', false );

	elseif ( is_tax() )
		$title = single_term_title( '', false );

	elseif ( is_author() )
		$title = carelib_get_single_author_title();

	elseif ( is_search() )
		$title = carelib_get_search_title();

	elseif ( is_post_type_archive() )
		$title = post_type_archive_title( '', false );

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$title = carelib_get_single_minute_hour_title();

	elseif ( get_query_var( 'minute' ) )
		$title = carelib_get_single_minute_title();

	elseif ( get_query_var( 'hour' ) )
		$title = carelib_get_single_hour_title();

	elseif ( is_day() )
		$title = carelib_get_single_day_title();

	elseif ( get_query_var( 'w' ) )
		$title = carelib_get_single_week_title();

	elseif ( is_month() )
		$title = single_month_title( ' ', false );

	elseif ( is_year() )
		$title = carelib_get_single_year_title();

	elseif ( is_archive() )
		$title = carelib_get_single_archive_title();

	return apply_filters( 'carelib_archive_title', $title );
}

/**
 * Filters `get_the_archve_description` to add better archive descriptions than core.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function carelib_archive_description_filter( $desc ) {
	if ( is_home() && !is_front_page() )
		$desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

	elseif ( is_category() )
		$desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

	elseif ( is_tag() )
		$desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

	elseif ( is_tax() )
		$desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

	elseif ( is_author() )
		$desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

	elseif ( is_post_type_archive() )
		$desc = get_post_type_object( get_query_var( 'post_type' ) )->description;

	return apply_filters( 'carelib_archive_description', $desc );
}
