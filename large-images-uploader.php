<?php

namespace GPLSCore\GPLS_PLUGIN_LIDFW;

/**
 * Plugin Name:  Large Images Uploader [[GrandPlugins]]
 * Description:  Upload large images to your WordPress website without the risk of the timeout limits or maximum pixels threshold.
 * Author:       GrandPlugins
 * Author URI:   https://profiles.wordpress.org/grandplugins/
 * Plugin URI:   https://grandplugins.com/product/wp-large-images-uploader/
 * Domain Path:  /languages
 * Requires PHP: 7.0
 * Text Domain:  large-images-uploader
 * Std Name:     large-images-uploader
 * Version:      1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GPLSCore\GPLS_PLUGIN_LIDFW\Uploader;
use GPLSCore\GPLS_PLUGIN_LIDFW\WP_Large_Images_Uploader;
use GPLSCore\GPLS_PLUGIN_LIDFW\Core;


if ( ! class_exists( __NAMESPACE__ . '\GPLS_LIDFW_Large_Images_Uploader' ) ) :


	/**
	 * Exporter Main Class.
	 */
	class GPLS_LIDFW_Large_Images_Uploader {

		/**
		 * Single Instance
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Plugin Info
		 *
		 * @var array
		 */
		private static $plugin_info;

		/**
		 * Core Object
		 *
		 * @var object
		 */
		private static $core;

		/**
		 * Debug Mode Status
		 *
		 * @var bool
		 */
		protected $debug = false;

		/**
		 * Singular init Function.
		 *
		 * @return Object
		 */
		public static function init() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Plugin Activated Hook.
		 *
		 * @return void
		 */
		public static function plugin_activated() {
			self::setup_plugin_info();
			self::disable_duplicate();
		}

		/**
		 * Plugin Deactivated Hook.
		 *
		 * @return void
		 */
		public static function plugin_deactivated() {
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			self::setup_plugin_info();
			$this->load_languages();
			$this->includes();

			self::$core = new Core( self::$plugin_info );

			Uploader::init( self::$plugin_info, self::$core );
			WP_Large_Images_Uploader::init( self::$plugin_info );
		}

		/**
		 * Includes Files
		 *
		 * @return void
		 */
		public function includes() {
			require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
			require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'core/bootstrap.php';
		}

		/**
		 * Load languages Folder.
		 *
		 * @return void
		 */
		public function load_languages() {
			load_plugin_textdomain( self::$plugin_info['text_domain'], false, self::$plugin_info['path'] . 'languages/' );
		}

		/**
		 * Set Plugin Info
		 *
		 * @return array
		 */
		public static function setup_plugin_info() {
			$plugin_data = get_file_data(
				__FILE__,
				array(
					'Version'     => 'Version',
					'Name'        => 'Plugin Name',
					'URI'         => 'Plugin URI',
					'SName'       => 'Std Name',
					'text_domain' => 'Text Domain',
				),
				false
			);

			self::$plugin_info = array(
				'basename'       => plugin_basename( __FILE__ ),
				'version'        => $plugin_data['Version'],
				'name'           => $plugin_data['SName'],
				'text_domain'    => $plugin_data['text_domain'],
				'file'           => __FILE__,
				'plugin_url'     => $plugin_data['URI'],
				'public_name'    => $plugin_data['Name'],
				'path'           => trailingslashit( plugin_dir_path( __FILE__ ) ),
				'url'            => trailingslashit( plugin_dir_url( __FILE__ ) ),
				'options_page'   => $plugin_data['SName'] . '-settings-tab',
				'localize_var'   => str_replace( '-', '_', $plugin_data['SName'] ) . '_localize_data',
				'type'           => 'pro',
				'general_prefix' => 'gpls-plugins-general-prefix',
				'classes_prefix' => 'gpls-lidfw',
				'localize'       => 'gpls-lidfw-large-images-uploader',
				'duplicate_base' => 'gpls-lidfw-large-images-uploader-for-wordpress-pro/gpls-lidfw-large-images-uploader-for-wordpress-pro.php',
			);
		}

				/**
		 * Disable duplicate.
		 *
		 * @return void
		*/
		private static function disable_duplicate() {
			require_once \ABSPATH . 'wp-admin/includes/plugin.php';
		   if ( ! empty( self::$plugin_info['duplicate_base'] ) && is_plugin_active( self::$plugin_info['duplicate_base'] ) ) {
				require_once \ABSPATH . 'wp-admin/includes/plugin.php';
			   deactivate_plugins( self::$plugin_info['duplicate_base'] );
		   }
	    }

	}

	add_action( 'plugins_loaded', array( __NAMESPACE__ . '\GPLS_LIDFW_Large_Images_Uploader', 'init' ), 10 );
	register_activation_hook( __FILE__, array( __NAMESPACE__ . '\GPLS_LIDFW_Large_Images_Uploader', 'plugin_activated' ) );
	register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\GPLS_LIDFW_Large_Images_Uploader', 'plugin_deactivated' ) );
endif;
