<?php
/**
 * @group dependencies
 * @group scripts
 */
class Tests_Dependencies_GetScriptAttributes extends WP_UnitTestCase {
	var $old_wp_scripts;

	function setUp() {
		parent::setUp();
		$this->old_wp_scripts = isset( $GLOBALS['wp_scripts'] ) ? $GLOBALS['wp_scripts'] : null;
		remove_action( 'wp_default_scripts', 'wp_default_scripts' );
		$GLOBALS['wp_scripts'] = new WP_Scripts();
		$GLOBALS['wp_scripts']->default_version = get_bloginfo( 'version' );
	}

	function tearDown() {
		$GLOBALS['wp_scripts'] = $this->old_wp_scripts;
		add_action( 'wp_default_scripts', 'wp_default_scripts' );
		parent::tearDown();
	}

	/**
	 * @ticket 22249
	 */
	function test_get_script_attributes_with_defaults() {
		global $wp_scripts;
		wp_register_script( 'cool-script', '/cool-script.js', array(), '1' );

		$expected_attributes = array(
			'type' => 'text/javascript',
			'src' => '/cool-script.js?ver=1',
		);
		$actual_attributes = $wp_scripts->get_script_attributes( 'cool-script' );
		$this->assertEquals( $expected_attributes, $actual_attributes );
	}

	/**
	 * @ticket 22249
	 */
	function test_get_script_attributes_with_arbitrary_attributes_added() {
		global $wp_scripts;
		wp_register_script( 'cool-script', '/cool-script.js', array(), '1', false, array( 'async' => 'async' ) );

		$expected_attributes = array(
			'type' => 'text/javascript',
			'src' => '/cool-script.js?ver=1',
			'async' => 'async',
		);
		$actual_attributes = $wp_scripts->get_script_attributes( 'cool-script' );
		$this->assertEquals( $expected_attributes, $actual_attributes );
	}

}
