<?php
/**
 * Customize API: WP_Customize_Custom_CSS_Setting class
 *
 * This handles validation, sanitization and saving of the value.
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.7.0
 */

/**
 * Custom Setting to handle WP Custom CSS.
 *
 * @since 4.7.0
 *
 * @see WP_Customize_Setting
 */
final class WP_Customize_Custom_CSS_Setting extends WP_Customize_Setting {

	/**
	 * The setting type.
	 *
	 * @var string
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public $type = 'custom_css';

	/**
	 * Setting Transport
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * Capability required to edit this setting.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @var string
	 */
	public $capability = 'unfiltered_css';

	/**
	 * Stylesheet
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @var string
	 */
	public $stylesheet = '';

	/**
	 * WP_Customize_Custom_CSS_Setting constructor.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @throws Exception If the setting ID does not match the pattern `custom_css[$stylesheet]`.
	 *
	 * @param WP_Customize_Manager $manager The Customize Manager class.
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Setting arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		if ( 'custom_css' !== $this->id_data['base'] ) {
			throw new Exception( 'Expected custom_css id_base.' );
		}
		if ( 1 !== count( $this->id_data['keys'] ) || empty( $this->id_data['keys'][0] ) ) {
			throw new Exception( 'Expected single stylesheet key.' );
		}
		$this->stylesheet = $this->id_data['keys'][0];
	}

	/**
	 * Add filter to preview post value.
	 *
	 * @since 4.7.9
	 * @access public
	 *
	 * @return bool False when preview short-circuits due no change needing to be previewed.
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return false;
		}
		$this->is_previewed = true;
		add_filter( 'wp_get_custom_css', array( $this, 'filter_previewed_wp_get_custom_css' ), 9, 2 );
		return true;
	}

	/**
	 * Filter wp_get_custom_css for applying customized value to return value.
	 *
	 * @since 4.7.9
	 * @access public
	 *
	 * @param string $css        Original CSS.
	 * @param string $stylesheet Current stylesheet.
	 * @return string CSS.
	 */
	public function filter_previewed_wp_get_custom_css( $css, $stylesheet ) {
		if ( $stylesheet === $this->stylesheet ) {
			$customized_value = $this->post_value( null );
			if ( ! is_null( $customized_value ) ) {
				$css = $customized_value;
			}
		}
		return $css;
	}

	/**
	 * Fetch the value of the setting.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @return string
	 */
	public function value() {
		return wp_get_custom_css( $this->stylesheet );
	}

	/**
	 * Validate CSS.
	 *
	 * Checks for imbalanced braces, brackets and comments.
	 *
	 * Notifications are rendered when the Preview
	 * is saved.
	 *
	 * @todo remove string literals before validation.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param string $css The input string.
	 * @return true|WP_Error True if the input was validated, otherwise WP_Error.
	 */
	public function validate( $css ) {
		$validity = new WP_Error();

		if ( preg_match( '#</?\w+#', $css ) ) {
			$validity->add( 'illegal_markup', __( 'Markup is not allowed in CSS.' ) );
		}

		$css_validation_error = false;
		// Make sure that there is a closing brace for each opening brace.
		if ( ! self::validate_balanced_characters( '{', '}', $css ) ) {
			$validity->add( 'imbalanced_curly_brackets', __( 'Your curly brackets <code>{}</code> are imbalanced. Make sure there is a closing <code>}</code> for every opening <code>{</code>.' ) );
			$css_validation_error = true;
		}

		// Ensure brackets are balanced.
		if ( ! self::validate_balanced_characters( '[', ']', $css ) ) {
			$validity->add( 'imbalanced_braces', __( 'Your brackets <code>[]</code> are imbalanced. Make sure there is a closing <code>]</code> for every opening <code>[</code>.' ) );
			$css_validation_error = true;
		}

		// Ensure parentheses are balanced.
		if ( ! self::validate_balanced_characters( '(', ')', $css ) ) {
			$validity->add( 'imbalanced_parentheses', __( 'Your parentheses <code>()</code> are imbalanced. Make sure there is a closing <code>)</code> for every opening <code>(</code>.' ) );
			$css_validation_error = true;
		}

		// Ensure single quotes are equal.
		if ( ! self::validate_equal_characters( '\'', $css ) ) {
			$validity->add( 'unequal_single_quotes', __( 'Your single quotes <code>\'</code> are uneven. Make sure there is a closing <code>\'</code> for every opening <code>\'</code>.' ) );
			$css_validation_error = true;
		}

		// Ensure single quotes are equal.
		if ( ! self::validate_equal_characters( '"', $css ) ) {
			$validity->add( 'unequal_double_quotes', __( 'Your double quotes <code>"</code> are uneven. Make sure there is a closing <code>"</code> for every opening <code>"</code>.' ) );
			$css_validation_error = true;
		}

		/*
		 * Make sure any code comments are closed properly.
		 *
		 * The first check could miss stray an unpaired comment closing figure, so if
		 * The number appears to be balanced, then check for equal numbers
		 * of opening/closing comment figures.
		 *
		 * Although it may initially appear redundant, we use the first method
		 * to give more specific feedback to the user.
		 */
		$unclosed_comment_count = self::validate_count_unclosed_comments( $css );
		if ( 0 < $unclosed_comment_count ) {
			$validity->add( 'unclosed_comment', sprintf( _n( 'There is %s unclosed code comment. Close each comment with <code>*/</code>.', 'There are %s unclosed code comments. Close each comment with <code>*/</code>.', $unclosed_comment_count ), $unclosed_comment_count ) );
			$css_validation_error = true;
		} elseif ( ! self::validate_balanced_characters( '/*', '*/', $css ) ) {
			$validity->add( 'imbalanced_comments', __( 'There is an extra <code>*/</code>, indicating an end to a comment.  Be sure that there is an opening <code>/*</code> for every closing <code>*/</code>.' ) );
			$css_validation_error = true;
		}
		if ( true === $css_validation_error && self::is_possible_content_error( $css ) ) {
			$validity->add( 'css_validation_notice', __( 'Imbalanced/Unclosed character errors can be caused <code>content: "";</code> declarations. You may need to remove this or add it a custom CSS file.' ) );
		}

		if ( empty( $validity->errors ) ) {
			$validity = parent::validate( $css );
		}
		return $validity;
	}

	/**
	 * Bypass the process of saving the value of the "Additional CSS"
	 * customizer setting.
	 *
	 * This setting does not use "option" or "theme_mod" but
	 * rather the "custom_css" custom post type.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param string $value The input value.
	 *
	 * @return int|false The post ID or false if the value could not be saved.
	 */
	public function update( $value ) {

		$args = array(
			'post_content' => ( null === $value ) ? '' : $value,
			'post_title'   => $this->stylesheet,
			'post_name'   => $this->stylesheet,
			'post_type'    => 'custom_css',
			'post_status'  => 'publish',
		);

		// Update post if it already exists, otherwise create a new one.
		$post_id = null;
		$post = get_page_by_title( $this->stylesheet, OBJECT, 'custom_css' ); // @todo This needs to be looking up by post_name instead, since it is indexed.
		if ( ! empty( $post ) ) {
			$args['ID'] = $post->ID;
			$post_id = wp_update_post( wp_slash( $args ) );
		} else {
			$post_id = wp_insert_post( wp_slash( $args ) );
		}
		if ( ! $post_id ) {
			return false;
		}

		// Cache post ID in theme mod for performance to avoid additional DB query.
		if ( $this->manager->get_stylesheet() === $this->stylesheet ) {
			set_theme_mod( 'custom_css_post_id', $post_id );
		}

		return $post_id;
	}

	/**
	 * Ensure there are a balanced number of paired characters.
	 *
	 * This is used to check that the number of opening and closing
	 * characters is equal.
	 *
	 * For instance, there should be an equal number of braces ("{", "}")
	 * in the CSS.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param string $opening_char The opening character.
	 * @param string $closing_char The closing character.
	 * @param string $css The CSS input string.
	 *
	 * @return bool
	 */
	public static function validate_balanced_characters( $opening_char, $closing_char, $css ) {
		return substr_count( $css, $opening_char ) === substr_count( $css, $closing_char );
	}

	/**
	 * Ensure there are an even number of paired characters.
	 *
	 * This is used to check that the number of a specific
	 * character is even.
	 *
	 * For instance, there should be an even number of double quotes
	 * in the CSS.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param string $char A character.
	 * @param string $css The CSS input string.
	 *
	 * @return bool
	 */
	public static function validate_equal_characters( $char, $css ) {
		$char_count = substr_count( $css, $char );
		return ( 0 === $char_count % 2 );
	}

	/**
	 * Count unclosed CSS Comments.
	 *
	 * Used during validation.
	 *
	 * @see self::validate()
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param string $css The CSS input string.
	 *
	 * @return int
	 */
	public static function validate_count_unclosed_comments( $css ) {
		$count = 0;
		$comments = explode( '/*', $css );

		if ( ! is_array( $comments ) || ( 1 >= count( $comments ) ) ) {
			return $count;
		}

		unset( $comments[0] ); // The first item is before the first comment.
		foreach ( $comments as $comment ) {
			if ( false === strpos( $comment, '*/' ) ) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Find "content:" within a string.
	 *
	 * Imbalanced/Unclosed validation errors may be caused
	 * when a character is used in a "content:" declaration.
	 *
	 * This function is used to detect if this is a possible
	 * cause of the validation error, so that if it is,
	 * a notification may be added to the Validation Errors.
	 *
	 * Example:
	 * .element::before {
	 *   content: "(\"";
	 * }
	 * .element::after {
	 *   content: "\")";
	 * }
	 *
	 * Using ! empty() because strpos() may return non-boolean values
	 * that evaluate to false. This would be problematic when
	 * using a strict "false === strpos()" comparison.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param string $css The CSS input string.
	 *
	 * @return bool
	 */
	public static function is_possible_content_error( $css ) {
		$found = preg_match( '/\bcontent\s*:/', $css );
		if ( ! empty( $found ) ) {
			return true;
		}
		return false;
	}
}