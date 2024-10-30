<?php
namespace GPLSCore\GPLS_PLUGIN_LIDFW;

class WP_Large_Images_Uploader extends \WP_Image_Editor {

	/**
	 * Plugin Info Array.
	 *
	 * @var array
	 */
	private static $plugin_info;

	/**
	 * Selected Image Editor Classname.
	 *
	 * @var string
	 */
	private static $editor_classname = null;

	/**
	 * Selected Image Editor Class Object.
	 *
	 * @var object
	 */
	private static $editor = null;

	/**
	 * Current Uploaded Attachment ID.
	 *
	 * @var int
	 */
	protected static $current_attachment_id = 0;

	/**
	 * Current Uploaded Attachment Meta.
	 *
	 * @var array
	 */
	protected static $current_attachemnt_meta = array();

	/**
	 * Timeout limit to break the subsizes process at.
	 */
	private static $timeout_limit = 15;

	/**
	 * Passed Out Limit before timeout limit.
	 *
	 * @var float
	 */
	private static $passed_out_limit = 0.7;

	/**
	 * Fallback to The basic editors
	 *
	 * @var array
	 */
	protected static $editors_fallback = array(
		'\WP_Image_Editor_Imagick',
		'\WP_Image_Editor_GD',
	);

	/**
	 * Constructor.
	 *
	 * @param string $file Image file Path.
	 */
	public function __construct( $file ) {
		$this->file = $file;
		parent::__construct( $file );
	}

	/**
	 * Initialize Function.
	 *
	 * @param array $plugin_info
	 * @return void
	 */
	public static function init( $plugin_info ) {
		self::$plugin_info = $plugin_info;
		self::hooks();
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_filter( 'wp_get_missing_image_subsizes', array( get_called_class(), 'get_attachment_id_before_making_subsizes' ), 1000, 3 );
		add_filter( 'intermediate_image_sizes_advanced', array( get_called_class(), 'get_attachment_id_before_making_subsizes' ), 1000, 3 );
		add_filter( 'big_image_size_threshold', array( get_called_class(), 'disable_size_threshold' ), 1000, 4 );
	}

	/**
	 * Disable Size Threshold.
	 *
	 * @param int    $default_threshold The Default Threshold number : 2560
	 * @param array  $imagesize ImageSize Array Details.
	 * @param string $file File PATH.
	 * @param int    $attachment_id Attachment POST ID.
	 * @return int|false
	 */
	public static function disable_size_threshold( $default_threshold, $imagesize, $file, $attachment_id ) {
		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-custom-images-uploader' ] ) ) {
			return false;
		}
		return $default_threshold;
	}

	/**
	 * Store the Attachment ID and meta just before apply sub-sizes.
	 *
	 * @param array $new_sizes
	 * @param array $image_meta
	 * @param int   $attachment_id
	 * @return array
	 */
	public static function get_attachment_id_before_making_subsizes( $new_sizes, $image_meta, $attachment_id ) {
		self::$current_attachment_id   = $attachment_id;
		self::$current_attachemnt_meta = $image_meta;
		return $new_sizes;
	}

	/**
	 * Load the Editor.
	 *
	 * @return true|\WP_Error
	 */
	public function load() {
		if ( is_null( self::$editor_classname ) ) {
			return new \WP_Error( 'image_no_editor', esc_html__( 'No editor could be selected.' ) );
		}
		self::$editor = new self::$editor_classname( $this->file );

		return self::$editor->load();
	}

	/**
	 * Checks to see if current environment supports GD.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args
	 * @return bool
	 */
	public static function test( $args = array() ) {
		unset( $_POST[ self::$plugin_info['name'] . '-custom-images-uploader' ] );
		$implementations = apply_filters( 'wp_image_editors', array( 'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD' ) );
		foreach ( $implementations as $implementation ) {
			// Prepend "\" to search outside the namespace.
			if ( 0 !== strpos( $implementation, '\\' ) ) {
				$implementation = '\\' . $implementation;
			}
			if ( ! call_user_func( array( $implementation, 'test' ), $args ) ) {
				continue;
			}
			if ( ! method_exists( $implementation, 'make_subsize' ) ) {
				continue;
			}
			if ( isset( $args['methods'] ) &&
				array_diff( $args['methods'], get_class_methods( $implementation ) ) ) {
				continue;
			}

			self::$editor_classname = $implementation;
			$_POST[ self::$plugin_info['name'] . '-custom-images-uploader' ] = true;
			return true;
		}

		self::$editor_classname = null;
		self::$editor           = null;
		return false;
	}

	/**
	 * Checks to see if editor supports the mime-type specified.
	 *
	 * @param string $mime_type
	 * @return bool
	 */
	public static function supports_mime_type( $mime_type ) {
		return ( ! is_null( self::$editor_classname ) && self::$editor_classname::supports_mime_type( $mime_type ) );
	}

	/**
	 * Save Image.
	 *
	 * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
	 */
	public function save( $filename = null, $mime_type = null ) {
		return self::$editor->save( $filename, $mime_type );
	}

	/**
	 * Resize Image.
	 *
	 * @return true|\WP_Error
	 */
	public function resize( $max_w, $max_h, $crop = false ) {
		return self::$editor->resize( $max_w, $max_h, $crop );
	}

	/**
	 * Check if a JPEG image has EXIF Orientation tag and rotate it if needed.
	 *
	 * @return bool|WP_Error True if the image was rotated. False if not rotated (no EXIF data or the image doesn't need to be rotated).
	 *                       WP_Error if error while rotating.
	 */

	public function maybe_exif_rotate() {
		return self::$editor->maybe_exif_rotate();
	}

	/**
	 * Create multiple smaller images from a single source.
	 *
	 * @param array $sizes An array of image size data arrays.
	 *
	 * @return array An array of resized images' metadata by size.
	 */
	public function multi_resize( $sizes ) {
		return $this->extended_multi_resize( $sizes );
	}

	/**
	 * Extended Multi Resize Func.
	 *
	 * @param array $sizes Images Sizes Array.
	 * @return array
	 */
	public function extended_multi_resize( $sizes ) {
		$metadata          = array();
		$start_resize_time = time();
		// If its the first time after upload and the lib is not GD, stop the request and start a new one.
		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-custom-images-uploader' ] ) && empty( $_POST[ self::$plugin_info['name'] . '-custom-images-uploader-subsizes' ] ) && ( '\WP_Image_Editor_GD' !== self::$editor_classname ) ) {
			self::send_max_timeout_limit_response( $metadata, $sizes );
		}

		// Set timeout limit to 0, not reliable in shared hostings.
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		foreach ( $sizes as $size => $size_data ) {
			$meta = self::$editor->make_subsize( $size_data );
			if ( ! is_wp_error( $meta ) ) {
				$metadata[ $size ] = $meta;
			} else {
				continue;
			}
			$resize_time    = time() - $start_resize_time;
			$passed_percent = round( $resize_time / self::$timeout_limit, 1 );
			// Stop the resize process if it passed or near timeout limit seconds, Close the request to avoid timeout.
			if ( ( $resize_time < self::$timeout_limit ) && ( $passed_percent < self::$passed_out_limit ) ) {
				continue;
			}
			// If it was the last size, just break.
			if ( $size === array_key_last( $sizes ) ) {
				break;
			}
			// Store the created sub-sizes into meta, and send json response to send a new request to continue the rest.
			self::send_max_timeout_limit_response( $metadata, $sizes );
		}

		// Reset the attachment meta and ID.
		self::$current_attachment_id   = 0;
		self::$current_attachemnt_meta = array();

		return $metadata;
	}

	/**
	 * Send Response to stop the subsize process to continue in a new request.
	 *
	 * @param array $metadata Array of created Subsizes.
	 * @param array $sizes Array of all subsizes to create.
	 * @return void
	 */
	private static function send_max_timeout_limit_response( $metadata, $sizes ) {
		if ( ! empty( self::$current_attachemnt_meta ) && self::$current_attachment_id ) {

			// Update the attachment metadata with the created sub-sizes.
			$image_meta          = self::$current_attachemnt_meta;
			$image_meta['sizes'] = array_merge( $image_meta['sizes'], $metadata );
			$image_meta          = apply_filters( 'wp_generate_attachment_metadata', $image_meta, self::$current_attachment_id, 'update' );

			wp_update_attachment_metadata( self::$current_attachment_id, $image_meta );

			$still_subsizes           = array_diff( array_keys( $sizes ), array_keys( $image_meta['sizes'] ) );
			$extended_subsize_message = self::still_subsizes_notice_html( $still_subsizes, 'row' );
			$resp_array               = array(
				'id'      => self::$current_attachment_id,
				'message' => $extended_subsize_message,
				'percent' => (int) round( ( count( $image_meta['sizes'] ) * 100 / ( count( $image_meta['sizes'] ) + count( $sizes ) ) ) ),
				self::$plugin_info['name'] . '-subsizes-timeout-limit-reached' => true,
			);

			// Response to send another Request to continue the rest of subsizes.
			wp_send_json_success( $resp_array );
		}
	}

	/**
	 * Extended Sub-size creation notice.
	 *
	 * @param array $subsizes
	 * @return string
	 */
	private static function still_subsizes_notice_html( $subsizes, $type = 'column' ) {
		$subsizes = (array) $subsizes;
		ob_start();
		if ( 'column' === $type ) :
			?>
		<p><?php esc_html_e( 'These sub-sizes still in progress...', 'wp-gif-editor' ); ?></p>
		<ul>
			<?php foreach ( $subsizes as $subsize ) : ?>
				<li><?php echo esc_html( $subsize ); ?></li>
			<?php endforeach; ?>
		</ul>
			<?php
		elseif ( 'row' === $type ) :
			?>
		<p><?php esc_html_e( 'These sub-sizes still in progress...', 'wp-gif-editor' ); ?></p>
		<span>&#91;</span>
			<?php foreach ( $subsizes as $index => $subsize ) : ?>
		<strong><?php echo esc_html( $subsize ); ?></strong>
				<?php if ( $index !== array_key_last( $subsizes ) ) : ?>
			<span> &#124; </span>
			<?php endif; ?>
		<?php endforeach; ?>
		<span>&#93;</span>
			<?php
		endif;
		return ob_get_clean();
	}

	/**
	 * Crops Image.
	 *
	 * @param int  $src_x   The start x position to crop from.
	 * @param int  $src_y   The start y position to crop from.
	 * @param int  $src_w   The width to crop.
	 * @param int  $src_h   The height to crop.
	 * @param int  $dst_w   Optional. The destination width.
	 * @param int  $dst_h   Optional. The destination height.
	 * @param bool $src_abs Optional. If the source crop points are absolute.
	 * @return true|WP_Error
	 */
	public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
		return self::$editor->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs );
	}

	/**
	 * Rotates current image counter-clockwise by $angle.
	 *
	 * @param float $angle
	 * @return true|WP_Error
	 */
	public function rotate( $angle ) {
		return self::$editor->rotate( $angle );
	}

	/**
	 * Flips current image.
	 *
	 * @param bool $horz Flip along Horizontal Axis
	 * @param bool $vert Flip along Vertical Axis
	 * @return true|WP_Error
	 */
	public function flip( $horz, $vert ) {
		return self::$editor->flip( $horz, $vert );
	}

	/**
	 * Streams current image to browser.
	 *
	 * @param string $mime_type The mime type of the image.
	 * @return true|WP_Error True on success, WP_Error object on failure.
	 */
	public function stream( $mime_type = null ) {
		return self::$editor->stream( $mime_type );
	}
}
