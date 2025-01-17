<?php
namespace GPLSCore\GPLS_PLUGIN_LIDFW;

defined( 'ABSPATH' ) || exit();

/**
 * Core Class
 */
class Core {

	/**
	 * Plugin Info
	 *
	 * @var array
	 */
	protected $plugin_info;

	/**
	 * Core Path
	 *
	 * @var string
	 */
	public $core_path;

	/**
	 * Core URL
	 *
	 * @var string
	 */
	public $core_url;

	/**
	 * Core Assets PATH
	 *
	 * @var string
	 */
	public $core_assets_path;

	/**
	 * Core Assets URL
	 *
	 * @var string
	 */
	public $core_assets_url;

	/**
	 * Constructor.
	 *
	 * @param array $plugin_info
	 */
	public function __construct( $plugin_info ) {
		$this->init( $plugin_info );
	}

	/**
	 * Init constants and other variables.
	 *
	 * = Set the Plugin Update URL
	 *
	 * @return void
	 */
	public function init( $plugin_info ) {
		$this->plugin_info      = $plugin_info;
		$this->core_path        = plugin_dir_path( __FILE__ );
		$this->core_url         = plugin_dir_url( __FILE__ );
		$this->core_assets_path = $this->core_path . 'assets';
		$this->core_assets_url  = $this->core_url . 'assets';
		$this->plugins_list     = array(
			'woo-cart-tracker' => array(
				'title'     => 'Woo Cart Tracker',
				'desc'      => 'Track all carts in your website in real time. apply coupon, send custom notices to customers and get push notifications in real time...',
				'thumbnail' => 'woo-cart-tracker.webp',
				'pro_link'  => 'https://grandplugins.com/product/woo-cart-tracker/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'advanced-captcha'         => array(
				'title'     => 'Advanced Captcha',
				'desc'      => 'protect your website from spammers and bots using multiple and random captchas...',
				'thumbnail' => 'advanced-captcha.webp',
				'pro_link'  => 'https://grandplugins.com/product/woo-advanced-captcha/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'wp-watermark-images'    => array(
				'title'     => 'WP Watermark Images',
				'desc'      => 'protect your images by watermarking them with text and image watermarks using the most advanced watermarking plugin',
				'thumbnail' => 'wp-watermark-images.png',
				'free_link' => 'https://wordpress.org/plugins/watermark-images-for-wp-and-woo-grandpluginswp/',
				'pro_link'  => 'https://grandplugins.com/product/wp-images-watermark/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'woo-coming-soon'        => array(
				'title'     => 'WooCommerce Coming Soon Products',
				'desc'      => 'Set your products to coming soon mode with countdown timer.',
				'thumbnail' => 'woo-coming-soon.png',
				'free_link' => 'https://wordpress.org/plugins/coming-soon-products-for-woocommerce/',
				'pro_link'  => 'https://grandplugins.com/product/woo-coming-soon-products/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'woo-advanced-add-to-cart' => array(
				'title'     => 'Woo Advanced Add To Cart',
				'desc'      => 'Add To cart in bulk, custom prices and more...',
				'thumbnail' => 'woo-advanced-add-to-cart.webp',
				'pro_link'  => 'https://grandplugins.com/product/woo-advanced-add-to-cart/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'woo-advanced-pricing' => array(
				'title'     => 'Woo Advanced Pricing',
				'desc'      => 'Apply all types of Quantity based pricing models',
				'thumbnail' => 'woo-advanced-pricing.webp',
				'pro_link'  => 'https://grandplugins.com/product/woo-advanced-pricing/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'woo-maintenance-mode' => array(
				'title'     => 'Woo Maintenance Mode',
				'desc'      => '',
				'thumbnail' => 'woo-maintenance-mode.png',
				'free_link' => 'https://wordpress.org/plugins/ultimate-maintenance-mode-for-woocommerce/',
				'pro_link'  => 'https://grandplugins.com/product/woocommerce-maintenance-mode-pro/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'woo-quick-view'         => array(
				'title'     => 'Woo Quick View and Buy Now',
				'desc'      => 'Increase your website conversion rate, encourage your visitors to buy from your website using quick view and buy now buttons with direct checkout.',
				'thumbnail' => 'woo-quick-view-and-buy-now.png',
				'free_link' => 'https://wordpress.org/plugins/quick-view-and-buy-now-for-woocommerce/',
				'pro_link'  => 'https://grandplugins.com/product/quick-view-and-buy-now-for-woocommerce/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'woo-cart-limiter'       => array(
				'title'     => 'WooCommerce Order Limiter',
				'desc'      => 'Control your website cart, limit cart totals, products count and quantity, limit products based on other products in cart, set minimum and maxmium quantity limits and more...',
				'thumbnail' => 'woo-cart-limiter.jpg',
				'free_link' => 'https://wordpress.org/plugins/cart-limiter/',
				'pro_link'  => 'https://grandplugins.com/product/woo-cart-limiter/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'simple-countdown-timer' => array(
				'title'     => 'Simple Countdown Timer',
				'desc'      => 'Add countdown timers easily to your WordPress website',
				'thumbnail' => 'simple-countdown-timer.gif',
				'free_link' => 'https://wordpress.org/plugins/simple-countdown/',
				'pro_link'  => 'https://grandplugins.com/product/simple-countdown-timer/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'wp-watermark-pdf'       => array(
				'title'     => 'WP Watermark PDF',
				'desc'      => 'Add text and image watermarks to your PDF files easily with our watermark PDFs plugin.',
				'thumbnail' => 'wp-watermark-pdf.png',
				'free_link' => 'https://wordpress.org/plugins/watermark-pdf/',
				'pro_link'  => 'https://grandplugins.com/product/wp-watermark-pdf/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),

			'image-sizes-controller' => array(
				'title'     => 'Image Sizes Controller',
				'desc'      => 'Control your website image sizes, create custom image sizes and disable generating unneeded sizes.',
				'thumbnail' => 'image-sizes-controller.png',
				'free_link' => 'https://wordpress.org/plugins/image-sizes-controller/',
				'pro_link'  => 'https://grandplugins.com/product/image-sizes-controller/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
			'wp-gif-editor'   => array(
				'title'     => 'WP GIF Uploader',
				'desc'      => 'Upload GIF images without losing the GIF animation in the uploaded gif and all generated subsizes.',
				'thumbnail' => 'wp-gif-editor.gif',
				'free_link' => 'https://wordpress.org/plugins/gif-uploader-wp-grandplugins',
				'pro_link'  => 'https://grandplugins.com/product/wp-gif-editor/?utm_source=free&utm_medium=sidebar&utm_content=' . $this->plugin_info['name'],
			),
		);
	}

	/**
	 * Plugins Sidebar.
	 *
	 * @return void
	 */
	public function plugins_sidebar( $exclude = '' ) {
		?>
		<div class="gpls-core-recommended-section mb-5">
			<h6 class="shadow-sm border p-3 shadow-sm border rounded"><?php esc_html_e( 'Empower Your Website with Our Additional Plugins' ); ?></h6>
			<div class="section-body bg-light p-3 shadow-sm border rounded">
				<ul class="plugins-list list-group">
					<?php
					foreach ( $this->plugins_list as $plugin_key => $plugin_arr ) :
						if ( $plugin_key === $exclude ) {
							continue;
						}
						?>
					<li class="plugin-list-item list-group-item border rounded">
						<h6 class="border rounded p-1 mb-2 text-center py-3 shadow-sm fw-bolder"><?php echo esc_html( $plugin_arr['title'] ); ?></h6>
						<?php if ( ! empty( $plugin_arr['thumbnail'] ) ) : ?>
							<img width="200px" src="<?php echo esc_url_raw( $this->plugin_info['url'] . 'core/assets/images/' . $plugin_arr['thumbnail'] ); ?>" class="thumbnail img-thumbanil my-3 mx-auto">
						<?php endif; ?>
						<p><?php echo esc_html( $plugin_arr['desc'] ); ?></p>
						<div class="row border p-1 rounded gx-0">
							<?php if ( ! empty( $plugin_arr['pro_link'] ) ) : ?>
							<div class="col d-flex justify-content-center border-end">
								<a class="btn btn-primary text-decoration-underline" target="_blank" href="<?php echo esc_url_raw( $plugin_arr['pro_link'] ); ?>"><strong><?php esc_html_e( 'Pro' ); ?></strong></a>
							</div>
							<?php endif; ?>
							<?php if ( ! empty( $plugin_arr['free_link'] ) ) : ?>
							<div class="col d-flex justify-content-center">
								<a class="btn btn-success text-decoration-underline" target="_blank" href="<?php echo esc_url_raw( $plugin_arr['free_link'] ); ?>"><?php esc_html_e( 'Free' ); ?></a>
							</div>
							<?php endif; ?>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
				<a class="btn btn-primary d-block mt-3" target="_blank" href="https://grandplugins.com/product-category/plugin/?utm_source=free&utm_medium=sidebar&utm_content=<?php echo esc_attr( $this->plugin_info['name'] ); ?>"><?php esc_html_e( 'Browse All Plugins' ); ?></a>
			</div>
		</div>
		<style>
			.gpls-core-recommended-section .plugins-list {
				display: flex;flex-direction: row;padding-left: 0;margin-bottom: 0;border-radius: 0.25rem;flex-wrap: wrap;
			}
			.gpls-core-recommended-section .plugin-list-item {
				width: 375px;display: flex;flex-direction: column;justify-content: space-between;margin: 10px;
			}
		</style>
		<?php
	}

	/**
	 * Get Core assets file
	 *
	 * @param string $asset_file    Assets File Name
	 * @param string $type          Assets File Folder Type [ js / css /images / etc.. ]
	 * @param string $suffix        Assets File Type [ js / css / png /jpg / etc ... ]
	 * @param string $prefix        [ .min ]
	 * @return string
	 */
	public function core_assets_file( $asset_file, $type, $suffix, $prefix = 'min' ) {
		return $this->core_assets_url . '/dist/' . $type . '/' . $asset_file . ( ! empty( $prefix ) ? ( '.' . $prefix ) : '' ) . '.' . $suffix;
	}

	/**
	 * Get Core assets lib file
	 *
	 * @param string $asset_file    Assets File Name
	 * @param string $suffix        Assets File Type [ js / css / png /jpg / etc ... ]
	 * @param string $prefix        [ .min ]
	 * @return string
	 */
	public function core_assets_lib( $asset_file, $suffix, $prefix = 'min' ) {
		return $this->core_assets_url . '/libs/' . $asset_file . ( ! empty( $prefix ) ? ( '.' . $prefix ) : '' ) . '.' . $suffix;
	}

	/**
	 * Plugin Activation Hub function
	 *
	 * @return void
	 */
	public function plugin_activated() {

		do_action( $this->plugin_info['name'] . '-core-activated', $this );
	}

	/**
	 * Plugin Deactivation Hub function
	 *
	 * @return void
	 */
	public function plugin_deactivated() {

		do_action( $this->plugin_info['name'] . '-core-deactivated', $this );
	}

	/**
	 * Uninstall the plugin hook.
	 *
	 * @return void
	 */
	public function plugin_uninstalled() {

		do_action( $this->plugin_info['name'] . '-core-uninstalled', $this );
	}

	/**
	 * Pro Button.
	 *
	 * @param string $pro_link
	 * @param string $btn_title
	 * @param string $additional_classes
	 * @param string $additional_css
	 * @return void
	 */
	public function pro_btn( $pro_link, $btn_title = 'Pro', $additional_classes = '', $additional_css = '' ) {

		?>
		<a target="_blank" class="ms-2 btn gpls-permium-btn-wave btn-primary <?php echo esc_attr( $additional_classes ); ?>" href="<?php echo esc_url_raw( $pro_link ); ?>">
			<span><?php printf( esc_html__( '%s' ), $btn_title ); ?></span>
			<div class="wave"></div>
		</a>
		<style>.gpls-permium-btn-wave{position:relative;text-decoration:none;overflow:hidden}.gpls-permium-btn-wave:hover .wave{top:-120px}.gpls-permium-btn-wave span{position:relative;z-index:1;color:#fff;font-size:15px}.gpls-permium-btn-wave .wave{width:200px;height:200px;background-color:#3c65ff;box-shadow:inset 0 0 50px rgba(0,0,0,.5);position:absolute;left:0;top:-80px;transition:.4s}.gpls-permium-btn-wave .wave::before,a .wave::after{width:200%;height:225%;content:"";position:absolute;top:0;left:50%;transform:translate(-50%,-75%)}.gpls-permium-btn-wave .wave::before{border-radius:45%;background-color:#8681ff;animation:5s linear infinite wave}.gpls-permium-btn-wave .wave::after{border-radius:40%;background-color:rgb(74 105 235 / 50%);animation:10s linear infinite wave}@keyframes wave{0%{transform:translate(-50%,-75%) rotate(0)}100%{transform:translate(-50%,-75%) rotate(360deg)}}
		<?php echo esc_attr( $additional_css ); ?>
		</style>
		<?php
	}

	/**
	 * Review Notice.
	 *
	 * @param string $review_link
	 * @return void
	 */
	public function review_notice( $review_link ) {
		?>
		<p class="notice notice-success p-4">
			<?php esc_html_e( 'We need your support to keep updating and improving the plugin. Please, ' ); ?>
			<a class="text-decoration-none" href="<?php echo esc_url_raw( $review_link ); ?>" target="_blank">
				<u><?php esc_html_e( 'help us by leaving a good review' ); ?></u>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</a>
			<?php esc_html_e( ':) Thanks!' ); ?>
		</p>
	<?php
	}
}
