<?php
/**
 * The main CareLib library class.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class for common theme functionality.
 *
 * @version 0.1.0
 */
class CareLib {

	/**
	 * Our library version number.
	 *
	 * @since 0.1.0
	 * @type  string
	 */
	private $version = '0.1.0';

	/**
	 * Prefix to prevent conflicts.
	 *
	 * Used to prefix filters to make them unique.
	 *
	 * @since 0.1.0
	 * @type  string
	 */
	private $prefix;

	/**
	 * The main library file.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $file = __FILE__;

	/**
	 * The library's directory path with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $dir;

	/**
	 * The library directory URL with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $url;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 * @param array $args arguments to be passed in via the helper function.
	 */
	public function __construct() {
		$this->dir = trailingslashit( dirname( __FILE__ ) );
		$this->uri = trailingslashit( $this->normalize_uri( dirname( __FILE__ ) ) );
	}

	/**
	 * Method to initialize the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function run( $args = array() ) {
		$this->prefix = empty( $args['prefix'] ) ? 'carelib' : sanitize_key( $args['prefix'] );
		add_action( 'after_setup_theme', array( $this, 'core' ), -95 );
	}

	/**
	 * Loads and instantiates all library functionality.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function core() {
		spl_autoload_register( array( $this, 'autoloader' ) );
		self::includes();
		self::build( 'CareLib_Factory' );
	}

	/**
	 * Whether the current request is a Customizer preview.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  bool
	 */
	public function is_customizer_preview() {
		global $wp_customize;
		return $wp_customize instanceof WP_Customize_Manager && $wp_customize->is_preview();
	}

	/**
	 * Whether the current environment is WordPress.com.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  bool
	 */
	public function is_wpcom() {
		return apply_filters( 'carelib_is_wpcom', false );
	}

	/**
	 * Getter method for reading the protected version variable.
	 *
	 * @since   0.2.0
	 * @access  public
	 * @return  bool
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Getter method for reading the protected prefix variable.
	 *
	 * @since   0.2.0
	 * @access  public
	 * @return  bool
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Return the path to the CareLib directory with a trailing slash.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  string
	 */
	public function get_dir( $path = '' ) {
		return $this->dir . $path;
	}

	/**
	 * Return the URI to the CareLib directory with a trailing slash.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_uri( $path = '' ) {
		return $this->uri . $path;
	}

	/**
	 * Fix asset directory path on Windows installations.
	 *
	 * Because we don't know where the library is located, we need to
	 * generate a URI based on the library directory path. In order to do
	 * this, we are replacing the theme root directory portion of the
	 * library directory with the theme root URI.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @uses   get_theme_root()
	 * @uses   get_theme_root_uri()
	 * @return void
	 */
	protected function normalize_uri( $path ) {
		return str_replace(
			wp_normalize_path( get_theme_root() ),
			get_theme_root_uri(),
			wp_normalize_path( $path )
		);
	}

	/**
	 * Load all plugin classes when they're instantiated.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function autoloader( $class ) {
		$class = strtolower( str_replace( '_', '-', str_replace( __CLASS__ . '_', '', $class ) ) );
		$file  = "{$this->dir}inc/class-{$class}.php";

		if ( false !== strpos( $class, 'admin' ) ) {
			$class = str_replace( 'admin-', '', $class );
			$file  = "{$this->dir}admin/class-{$class}.php";
		}

		if ( file_exists( $file ) ) {
			require_once $file;
			return true;
		}
		return false;
	}

	/**
	 * Load all library functions.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function includes() {
		if ( ! is_admin() ) {
			require_once "{$this->dir}inc/tha-hooks.php";
		}
	}

	/**
	 * Store a reference to our classes and get them running.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	protected function build( $factory ) {
		$classes = array(
			'customize',
			'i18n',
			'image-grabber',
			'layouts',
			'sidebar',
		);
		if ( is_admin() ) {
			$classes[] = 'admin-dashboard';
			$classes[] = 'admin-metabox-post-layouts';
			$classes[] = 'admin-metabox-post-styles';
			$classes[] = 'admin-metabox-post-templates';
			$classes[] = 'admin-scripts';
			$classes[] = 'admin-tinymce';
		} else {
			$classes[] = 'attributes';
			$classes[] = 'context';
			$classes[] = 'filters';
			$classes[] = 'head';
			$classes[] = 'meta';
			$classes[] = 'public-scripts';
			$classes[] = 'search-form';
			$classes[] = 'site-logo';
			$classes[] = 'support';
			$classes[] = 'template-hierarchy';
		}

		$classes = apply_filters( "{$this->prefix}_build_classes", $classes );

		foreach ( (array) $classes as $class ) {
			$object = $factory::get( $class );
			$object->run();
		}
	}

	/**
	 * Main CareLib Instance
	 *
	 * Insures that only one instance of CareLib exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 0.1.0
	 * @static
	 * @uses   CareLib::includes() Include the required files
	 * @return CareLib
	 */
	public static function instance() {
		static $instance;
		if ( null === $instance ) {
			$instance = new self;
		}
		return $instance;
	}

}
