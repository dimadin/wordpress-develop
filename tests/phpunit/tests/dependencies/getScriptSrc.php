<?php
/**
 * @group dependencies
 * @group scripts
 */
class Tests_Dependencies_GetScriptSrc extends WP_UnitTestCase {
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
	function test_get_script_src_for_script_with_relative_url() {
		global $wp_scripts;
		wp_register_script( 'cool-script', '/cool-script.js', array(), '1' );

		$actual = $wp_scripts->get_script_src( 'cool-script' );
		$this->assertEquals( '/cool-script.js?ver=1', $actual );
	}

	/**
	 * @ticket 22249
	 */
	function test_get_script_src_for_script_with_full_url() {
		global $wp_scripts;
		wp_register_script( 'd3', 'https://d3js.org/d3.v3.min.js', array(), '1' );

		$actual = $wp_scripts->get_script_src( 'd3' );
		$this->assertEquals( 'https://d3js.org/d3.v3.min.js?ver=1', $actual );
	}

}
