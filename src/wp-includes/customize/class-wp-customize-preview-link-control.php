<?php
/**
 * Customize API: WP_Customize_Preview_Link_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.9.0
 */

/**
 * Customize Preview Link Control class.
 *
 * @since 4.9.0
 *
 * @see WP_Customize_Control
 */
class WP_Customize_Preview_Link_Control extends WP_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $type = 'preview_link';

	/**
	 * Don't render the control's content - it's rendered with a JS template.
	 *
	 * @since 4.9.0
	 */
	public function render_content() {}

	/**
	 * Renders a JS template for the content of preview link control.
	 *
	 * @since 4.9.0
	 */
	public function content_template() {
		?>
		<# _.defaults( data, <?php echo wp_json_encode( $this->json() ); ?> ); #>

		<span class="customize-control-title">
			<label>{{ data.label }}</label>
		</span>
		<span class="description customize-control-description">{{ data.description }}</span>
		<div class="customize-control-notifications-container"></div>
		<div class="preview-link-wrapper">
			<label>
				<span class="screen-reader-text"><?php esc_html_e( 'Preview Link' ); ?></span>
				<a class="preview-control-element" data-component="link" href="" target=""></a>
				<input readonly class="preview-control-element" data-component="input" value="test" >
				<button class="customize-copy-preview-link preview-control-element button button-secondary" data-component="button" data-copy-text="<?php esc_attr_e( 'Copy' ); ?>" data-copied-text="<?php esc_attr_e( 'Copied' ); ?>" ><?php esc_html_e( 'Copy' ); ?></button>
			</label>
		</div>
		<?php
	}
}
