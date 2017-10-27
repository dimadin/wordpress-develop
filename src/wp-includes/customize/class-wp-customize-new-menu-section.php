<?php
/**
 * Customize API: WP_Customize_New_Menu_Section class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Menu Section Class
 *
 * Implements a section for creating new menus. This class now exists for
 * backwards compatibility, following earlier versions that overrode
 * base class methods.
 *
 * @since 4.3.0
 *
 * @see WP_Customize_Section
 */
class WP_Customize_New_Menu_Section extends WP_Customize_Section {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'new_menu';
}
