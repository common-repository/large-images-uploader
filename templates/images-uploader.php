<?php
namespace GPLSCore\GPLS_PLUGIN_LIDFW;

use GPLSCore\GPLS_PLUGIN_LIDFW\Uploader;

$core        = $args['core'];
$plugin_info = $args['plugin_info'];
?>

<div class="wrap w-100">
	<?php $core->review_notice( 'https://wordpress.org/support/plugin/large-images-uploader/reviews/#new-post' ); ?>
	<div class="images-uploader-notice">
	</div>
	<div class="selected-sizes row my-5">
		<div class=" mx-auto">
			<button class="float-right select-sizes-to-create-btn accordion-button collapsed mb-4 border bg-light" type="button" >
				<span class="float-left" >
					<?php esc_html_e( 'Select subsizes to be created', 'large-images-uploader' ); ?>
					<?php $core->pro_btn( 'https://grandplugins.com/product/wp-large-images-uploader/', 'Get Premium' ); ?>
				</span>
			</button>
			<div class="subsizes-list collapse">
				<?php $sizes = wp_get_registered_image_subsizes(); ?>
				<ul class="p-0 list-group disabled-sizes-list ">
				<?php foreach ( $sizes as $size_name => $size_arr ) : ?>
					<li class="list-group-item d-flex align-items-center flex-row justify-content-start">
						<input type="checkbox" class="size-to-create-input" value="<?php echo esc_attr( $size_name ); ?>" checked="checked" >
						<span class="mb-1"><?php echo esc_html( $size_name . ' [ ' . $size_arr['width'] . ' x ' . $size_arr['height'] . ' ] ' ); ?></span>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<div id="images-uploader-area" class="dm-uploader p-5 text-center mx-auto col-6">
		<h5 class='dnd-label'><?php esc_html_e( 'Drag and Drop image Here', 'large-images-uploader' ); ?></h5>
		<div class="select-files-btn btn btn-primary my-4">
			<span><?php esc_html_e( 'Select image', 'large-images-uploader' ); ?></span>
			<input type="file" name="async-upload" title="<?php esc_html_e( 'Select Image', 'large-images-uploader' ); ?>">
		</div>
	</div>
	<p class="text-center my-3"><?php echo esc_html__( 'Max upload size to server: ', 'large-images-uploader' ) . esc_html( Uploader::get_max_upload_size() ); ?></p>

	<div class="upload-progress-wrapper row p3 my-5 pt-4 bg-light">
		<div class="w-100">
			<ul class="image-upload-items list-unstyled p-2 d-flex flex-column col">
				<li class="image-upload-item-placeholder border d-none mb-0">
					<div class="media-body mb-1 d-flex flex-row justify-content-between align-items-center py-3 px-3 rounded">
						<div class="image-details d-flex flex-row justify-content-start align-items-center flex-grow-1">
							<img class="image-attachment img-thumbnail d-none" width="70" height="70" src="#" >
							<div class="name-details ms-2">
								<h5 class="item-title d-none"><strong></strong></h5>
								<span class="item-filename mb-0"></span>
							</div>
						</div>
						<p class="uploading-label"><?php esc_html_e( 'uploading', 'large-images-uploader' ); ?></p>
						<div class="upload-progress progress mb-0">
							<div class="progress-bar bg-primary bg" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="1">1%</div>
						</div>
						<div class="actions d-flex flex-row align-items-center">
							<a target="_blank" href="#" class="image-uploaded-link mx-5 d-none"><?php esc_html_e( 'Edit' ); ?></a>
						</div>
					</div>
					<div class="media-footer">
						<div class="subsizes-progress notice notice-warning d-none m-2"><p></p></div>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<?php find_posts_div(); ?>
</div>

<div class="plugins-sidebar mt-5">
</div>
