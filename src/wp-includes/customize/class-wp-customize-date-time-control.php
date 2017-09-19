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
		$timezone_info = $this->get_timezone_info();
		?>

		<# _.defaults( data, <?php echo wp_json_encode( $data ); ?> ); #>

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
							<select class="date-input month" data-component="month">
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
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input day" data-component="day" min="1" max="31" value="{{ data.day }}" />
					</label>
					<span class="time-special-char date-time-separator">,</span>
					<label class="year-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Year' ); ?></span>
						<input type="number" size="4" maxlength="4" autocomplete="off" class="date-input year" data-component="year" min="<?php echo esc_attr( date( 'Y' ) ); ?>" value="{{ data.year }}" max="9999" />
					</label>
				</div>
			</div>
			<div class="time-row clear">
				<span class="title-time"><?php esc_html_e( 'Time' ); ?></span>
				<div class="time-fields clear">
					<label class="hour-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Hour' ); ?></span>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input hour" data-component="hour" min="0" max="11" value="{{ data.hour }}" />
					</label>
					<span class="time-special-char date-time-separator">:</span>
					<label class="minute-field">
						<span class="screen-reader-text"><?php esc_html_e( 'Minute' ); ?></span>
						<input type="number" size="2" maxlength="2" autocomplete="off" class="date-input minute" data-component="minute" min="0" max="59" value="{{ data.minute }}" />
					</label>
					<label class="am-pm-field">
						<span class="screen-reader-text"><?php esc_html_e( 'AM / PM' ); ?></span>
						<select class="date-input" data-component="am_pm">
							<option value="am"><?php esc_html_e( 'AM' ); ?></option>
							<option value="pm"><?php esc_html_e( 'PM' ); ?></option>
						</select>
					</label>
					<span class="date-timezone" aria-label="<?php esc_attr_e( 'Timezone' ); ?>" title="<?php esc_attr_e( $timezone_info['description'] ) ?>"><?php esc_html_e( $timezone_info['abbr'] ); ?></span>
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

	/**
	 * Get timezone info.
	 *
	 * @return array abbr and description.
	 */
	public function get_timezone_info() {
		$tz_string = get_option( 'timezone_string' );
		$timezone_info = array();

		if ( $tz_string ) {
			$tz = new DateTimezone( $tz_string );
			$now = new DateTime( 'now', $tz );
			$formatted_gmt_offset = sprintf( 'UTC%s', $this->format_gmt_offset( $tz->getOffset( $now ) / 3600 ) );
			$tz_name = str_replace( '_', ' ', $tz->getName() );
			$timezone_info['abbr'] = $now->format( 'T' );

			/* translators: 1: timezone name, 2: timezone abbreviation, 3: gmt offset  */
			$timezone_info['description'] = sprintf( __( 'Timezone is %1$s (%2$s), currently %3$s.' ), $tz_name, $timezone_info['abbr'], $formatted_gmt_offset );
		} else {
			$formatted_gmt_offset = $this->format_gmt_offset( intval( get_option( 'gmt_offset', 0 ) ) );
			$timezone_info['abbr'] = sprintf( 'UTC%s', $formatted_gmt_offset );

			/* translators: %s: UTC offset  */
			$timezone_info['description'] = sprintf( __( 'Timezone is %s.' ), $timezone_info['abbr'] );
		}

		return $timezone_info;
	}

	/**
	 * Format GMT Offset.
	 *
	 * @see wp_timezone_choice()
	 * @param float $offset Offset in hours.
	 * @return string Formatted offset.
	 */
	public function format_gmt_offset( $offset ) {
		if ( 0 <= $offset ) {
			$formatted_offset = '+' . (string) $offset;
		} else {
			$formatted_offset = (string) $offset;
		}
		$formatted_offset = str_replace(
			array( '.25', '.5', '.75' ),
			array( ':15', ':30', ':45' ),
			$formatted_offset
		);
		return $formatted_offset;
	}
}
