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
	 * Constructor.
	 *
	 * @since 4.9.0
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

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
		<# _.defaults( data, <?php echo wp_json_encode( $this->json() ) ?> ); #>

		<span class="customize-control-title">
			<label>{{ data.label }}</label>
		</span>
		<div class="customize-control-notifications-container"></div>
		<span class="description customize-control-description">{{ data.description }}</span>
		<div class="preview-link-wrapper">
			<label>
				<span class="screen-reader-text"><?php esc_html_e( 'Preview Link' ); ?></span>
				<input readonly value="" >
			</label>
			<button class="customize-copy-preview-link button button-secondary" data-copy-text="<?php esc_attr_e( 'Copy' ); ?>" data-copied-text="<?php esc_attr_e( 'Copied' ); ?>" ><?php esc_html_e( 'Copy' ); ?></button>
		</div>
		<?php
	}
}
