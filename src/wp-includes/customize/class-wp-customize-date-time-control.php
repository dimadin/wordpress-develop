<?php
/**
 * Customize API: WP_Customize_Date_Time_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.9.0
 */

/**
 * Customize Date Time Control class.
 *
 * @since 4.9.0
 *
 * @see WP_Customize_Control
 */
class WP_Customize_Date_Time_Control extends WP_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $type = 'date_time';

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
	 * Renders a JS template for the content of date time control.
	 *
	 * @since 4.9.0
	 */
	public function content_template() {
		$data = array_merge( $this->json(), $this->get_month_choices() );
		?>
		<# _.defaults( data, <?php echo wp_json_encode( $data ) ?> ); #>

		<span class="customize-control-title">
			<label>{{ data.label }}</label>
		</span>
		<div class="customize-control-notifications-container"></div>
		<span class="description customize-control-description">{{ data.description }}</span>
		<div class="date-time-fields">
			<div class="day-row">
				<span class="title-day"><?php esc_html_e( 'Day' ); ?></span>
				<div class="day-fields clear">
					<label class="month-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Month' ); ?></span>
							<select id="date-month" class="date-input month" data-date-input="month">
								<# _.each( data.month_choices, function( choice ) {
										if ( _.isObject( choice ) && ! _.isUndefined( choice.text ) && ! _.isUndefined( choice.value ) ) {
										text = choice.text;
										value = choice.value;
										}

										selected = choice.value == data.month ? 'selected="selected"' : '';
										#>
								<option value="{{ value }}" {{selected}} >
									{{ text }}
								</option>
								<# } ); #>
							</select>
					</label>
					<label class="day-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Day' ); ?></span>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input day" data-date-input="day" min="1" max="31" value="{{ data.day }}" />
					</label>
					<span class="time-special-char date-time-separator">,</span>
					<label class="year-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Year' ); ?></span>
						<input type="number" size="4" maxlength="4" autocomplete="off" class="date-input year" data-date-input="year" min="<?php esc_attr_e( date( 'Y' ) ); ?>" value="{{ data.year }}" max="9999" />
					</label>
				</div>
			</div>
			<div class="time-row clear">
				<span class="title-time"><?php esc_html_e( 'Time' ); ?></span>
				<div class="time-fields clear">
					<label class="hour-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Hour' ); ?></span>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input hour" data-date-input="hour" min="0" max="11" value="{{ data.hour }}" />
					</label>
					<span class="time-special-char date-time-separator">:</span>
					<label class="minute-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Minute' ); ?></span>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input minute" data-date-input="minute" min="0" max="59" value="{{ data.minute }}" />
					</label>
					<label class="am-pm-field">
						<span class="screen-reader-text"><?php esc_html_e( 'AM / PM' ); ?></span>
						<select id="">
							<option value="am"><?php esc_attr_e( 'AM' ) ?></option>
							<option value="pm"><?php esc_attr_e( 'PM' ) ?></option>
						</select>
					</label>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Generate options for the month Select.
	 *
	 * Based on touch_time().
	 *
	 * @see touch_time()
	 *
	 * @return array
	 */
	public function get_month_choices() {
		global $wp_locale;
		$months = array();
		for ( $i = 1; $i < 13; $i = $i + 1 ) {
			$month_number = zeroise( $i, 2 );
			$month_text = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );

			/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
			$months[ $i ]['text'] = sprintf( __( '%1$s-%2$s' ), $month_number, $month_text );
			$months[ $i ]['value'] = $month_number;
		}
		return array(
			'month_choices' => $months,
		);
	}
}
