<?php
/**
 * Load all required library files.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

require_once dirname( __FILE__ ) . '/inc/autoload.php';
new CareLib_Autoload( __FILE__ );

/**
 * Grab an instance of the main library class.
 *
 * If you need to reference a method in the class, do it using this function.
 *
 * Example:
 *
 * <?php carelib()->is_customizer_preview(); ?>
 *
 * @since   0.1.0
 * @return  object CareLib
 */
function carelib() {
	return CareLib_Library::get_instance( __FILE__ );
}

/**
 * Grab an instance of one of the library class objects.
 *
 * If you need to reference a method in one of the library classes, you should
 * typically do it using this function.
 *
 * Example:
 *
 * <?php carelib_get( 'attributes' )->get_attr( $args ); ?>
 *
 * @since   0.1.0
 * @return  object
 */
function carelib_get( $object, $name = 'canonical', $args = array() ) {
	return CareLib_Factory::get( $object, $name, $args );
}
