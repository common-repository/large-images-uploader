<?php

namespace GPLSCore\GPLS_PLUGIN_LIDFW;

/**
 * Uploader CLass.
 */
class Uploader {

	/**
	 * Singular Instance.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Plugin Info Array.
	 *
	 * @var array
	 */
	private static $plugin_info;

	/**
	 * Core Object.
	 *
	 * @var object
	 */
	private static $core;

	/**
	 * Initialize the Uploader.
	 *
	 * @param array $plugin_info Plugin Info Array.
	 * @return object
	 */
	public static function init( $plugin_info, $core ) {
		self::$plugin_info = $plugin_info;
		self::$core        = $core;
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * Actions and Filters Hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'uploader_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-images-uploader-action', array( get_called_class(), 'ajax_upload_images' ) );
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-images-uploader-attach-action', array( get_called_class(), 'ajax_attach_detach_images' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'thumbnail_link_after_large_images_upload' ), PHP_INT_MAX, 3 );
		add_filter( 'wp_image_editors', array( $this, 'choose_our_editor' ), PHP_INT_MAX, 1 );
	}

	/**
	 * Select Our Editor for Our Large Images Uploader.
	 *
	 * @param array $editors
	 * @return string
	 */
	public function choose_our_editor( $editors ) {
		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-custom-images-uploader' ] ) ) {
			array_unshift( $editors, __NAMESPACE__ . '\WP_Large_Images_Uploader' );
		}
		return $editors;
	}

	/**
	 * Get Max Upload Size on Server.
	 *
	 * @return string
	 */
	public static function get_max_upload_size() {
		$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
		$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
		return size_format( min( $u_bytes, $p_bytes ) );
	}

	/**
	 * Add Thumbnail Link in the attachment Response Object for our custom image Upload process.
	 *
	 * @param array  $response
	 * @param object $attachment
	 * @param array  $meta
	 * @return array
	 */
	public function thumbnail_link_after_large_images_upload( $response, $attachment, $meta ) {
		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-custom-images-uploader' ] ) ) {
			$thumbnail = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
			if ( $thumbnail ) {
				$response[ self::$plugin_info['classes_prefix'] . '-thumbnail-link' ] = $thumbnail[0];
			}
		}
		return $response;
	}

	/**
	 * Admin Assets.
	 *
	 * @return void
	 */
	public function admin_assets() {

		// Large Images uploader.
		if ( ! empty( $_GET['page'] ) && ( self::$plugin_info['options_page'] . '-uploader' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) ) {
			$max_upload_size = wp_max_upload_size();
			if ( ! $max_upload_size ) {
				$max_upload_size = 0;
			}
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			if ( ! wp_script_is( 'media' ) ) {
				wp_enqueue_script( 'media' );
			}
			wp_enqueue_script( 'plupload-handlers' );
			wp_enqueue_style( self::$plugin_info['name'] . '-bootstrap', self::$core->core_assets_lib( 'bootstrap', 'css' ), array(), self::$plugin_info['version'], 'all' );
			wp_enqueue_style( self::$plugin_info['name'] . '-image-uploader-lib-css', self::$core->core_assets_lib( 'jquery.dm-uploader', 'css' ), array(), self::$plugin_info['version'], 'all' );
			wp_enqueue_style( self::$plugin_info['name'] . '-images-uploader-css', self::$plugin_info['url'] . 'assets/dist/css/admin/images-upload.min.css', array(), self::$plugin_info['version'], 'all' );
			wp_enqueue_script( self::$plugin_info['name'] . '-bootstrap-js', self::$core->core_assets_lib( 'bootstrap.bundle', 'js' ), array( 'jquery' ), self::$plugin_info['version'], true );
			wp_enqueue_script( self::$plugin_info['name'] . '-image-uploader-lib-js', self::$core->core_assets_lib( 'jquery.dm-uploader', 'js' ), array( 'jquery' ), self::$plugin_info['version'], true );
			wp_enqueue_script( self::$plugin_info['name'] . '-images-uploader-script', self::$plugin_info['url'] . 'assets/dist/js/admin/images-uploader.min.js', array( 'jquery', self::$plugin_info['name'] . '-image-uploader-lib-js' ), self::$plugin_info['version'], true );
			wp_localize_script(
				self::$plugin_info['name'] . '-images-uploader-script',
				str_replace( '-', '_', self::$plugin_info['localize'] . '_localize_vars' ),
				array(
					'classes_prefix'     => self::$plugin_info['classes_prefix'],
					'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
					'nonce'              => wp_create_nonce( self::$plugin_info['name'] . '-images-upload-nonce' ),
					'_wpnonce'           => wp_create_nonce( 'media-form' ),
					'imagesUploadAction' => self::$plugin_info['name'] . '-images-uploader-action',
					'attachDetachAction' => self::$plugin_info['name'] . '-images-uploader-attach-action',
					'labels'             => array(
						'attach'      => esc_html__( 'Attach' ),
						'detach'      => esc_html__( 'Detach' ),
						'unattached'  => esc_html__( 'Unattached' ),
						'uploading'   => esc_html__( 'Uploading', 'large-images-uploader' ),
						'images_only' => esc_html__( 'Only images are allowed!', 'large-images-uploader' ),
						'max_size'    => sprintf( esc_html__( 'Max size allowed is: %s', 'large-images-uploader' ), size_format( $max_upload_size ) ),
					),
				)
			);
		}
	}

	/**
	 * Large Images uploader Menu.
	 *
	 * @return void
	 */
	public function uploader_menu() {
		add_media_page(
			esc_html__( 'Upload Large Images', 'large-images-uploader' ),
			esc_html__( 'Large Images Uploader', 'large-images-uploader' ),
			'upload_files',
			self::$plugin_info['options_page'] . '-uploader',
			array( $this, 'images_uploader_page' )
		);
	}

	/**
	 * Images Uploader Page.
	 *
	 * @return void
	 */
	public function images_uploader_page() {
		?>
		<div class="wrap">
			<div class="container-fluid images-uploader-wrapper w-100 p-5">
				<div class="row">
					<?php
					load_template(
						self::$plugin_info['path'] . 'templates/images-uploader.php',
						true,
						array(
							'core'        => self::$core,
							'plugin_info' => self::$plugin_info,
						)
					);
					?>
				</div>
			</div>
		</div>
		<?php
		self::$core->plugins_sidebar();
	}

	/**
	 * Ajax Upload Images.
	 *
	 * @return void
	 */
	public static function ajax_upload_images() {
		if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], self::$plugin_info['name'] . '-images-upload-nonce' ) ) {
			// 1) Check if the file is image.
			if ( empty( $_FILES['async-upload'] ) || empty( $_FILES['async-upload']['tmp_name'] ) || ! preg_match( '!^image/!', sanitize_text_field( wp_unslash( $_FILES['async-upload']['type'] ) ) ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Images only are allowed!', 'large-images-uploader' ),
						'status'  => 'error',
					)
				);
			}

			// 2) Upload the Image.
			$attachment_id = media_handle_upload(
				'async-upload',
				0,
				array()
			);

			if ( is_wp_error( $attachment_id ) ) {
				wp_send_json_error(
					array(
						'message' => $attachment_id->get_error_message(),
						'status'  => 'error',
					)
				);
			}
			wp_send_json_success( wp_prepare_attachment_for_js( $attachment_id ) );
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html_e( 'Nonce check failed, please refresh the page!', 'large-images-uploader' ),
					'status'  => 'error',
				)
			);
		}
	}

	/**
	 * Ajax Attach - Detach Images.
	 *
	 * @return void
	 */
	public static function ajax_attach_detach_images() {
		if ( ! empty( $_POST['nonce'] ) && ! empty( $_POST['attachmentID'] ) && ! empty( $_POST['subAction'] ) && wp_verify_nonce( $_POST['nonce'], self::$plugin_info['name'] . '-images-upload-nonce' ) ) {
			$parent_id     = empty( $_POST['parentID'] ) ? 0 : absint( sanitize_text_field( wp_unslash( $_POST['parentID'] ) ) );
			$attachment_id = absint( sanitize_text_field( wp_unslash( $_POST['attachmentID'] ) ) );
			$action        = sanitize_text_field( wp_unslash( $_POST['subAction'] ) );

			$result = self::attach_detach_image( $parent_id, $attachment_id, $action );
			wp_send_json_success(
				array(
					'result' => $result,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html_e( 'Nonce check failed, please refresh the page!', 'large-images-uploader' ),
					'status'  => 'error',
				)
			);
		}
	}

	/**
	 * Attach Detach Image.
	 *
	 * @param int $parent_id Post ID to attach the image to.
	 * @param int $attachment_id Post ID.
	 * @return array
	 */
	private static function attach_detach_image( $parent_id, $attachment_id, $action = 'attach' ) {
		global $wpdb;
		if ( 'attach' === $action ) {
			$result = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID = %d", $parent_id, $attachment_id ) );
		} else {
			$result = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = 0 WHERE post_type = 'attachment' AND ID =%d", $attachment_id ) );
		}

		return array(
			'parent_id'        => $parent_id,
			'attachment_id'    => $attachment_id,
			'action'           => $action,
			'parent_edit_link' => get_edit_post_link( $parent_id, 'edit' ),
			'parent_title'     => trim( get_the_title( $parent_id ) ) ? get_the_title( $parent_id ) : esc_html__( '(no title)' ),
		);
	}
}
