<?php

class WPChangeDefaultAuthor {

	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	private static function init_hooks() {
		self::$initiated = true;

		add_action( 'admin_init', array( 'WPChangeDefaultAuthor', 'admin_init' ) );
	}

	public static function admin_init() {
		add_action( 'add_meta_boxes', array( 'WPChangeDefaultAuthor', 'add_meta_boxes' ), 10, 2 );
		self::register_setting();
	}

	public static function add_meta_boxes( $post_type , $post ) {
		$post_type_object = get_post_type_object( $post_type );
		if ( post_type_supports( $post_type, 'author' ) && current_user_can( $post_type_object->cap->edit_others_posts ) ) {
			remove_meta_box( 'authordiv', $post_type, 'normal' );
			add_meta_box( 'wp-change-default-author-authordiv', __( 'Author' ), array( 'WPChangeDefaultAuthor', 'post_author_meta_box' ), null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
		}
	}

	/**
	 * Display form field with list of authors.
	 *
	 * @global int $user_ID
	 *
	 * @param object $post
	 */
	public static function post_author_meta_box( $post ) {
		global $user_ID;
		$default_author = get_option( 'wp_change_default_author__author' );

		if ( 'auto-draft' === $post->post_status ) {
			$is_new_post = true;
		}

		if ( empty( $post->ID ) ) {
			$selected = empty( $default_author ) ? $user_ID : $default_author;
		} else {
			$selected = $is_new_post ? $default_author : $post->post_author;
		}
		?>
		<label class="screen-reader-text" for="post_author_override"><?php echo $default_author; ?> / <?php echo $post->post_author; ?> / <?php _e( 'Author' ); ?></label>
		<?php
		wp_dropdown_users(
			array(
				'who'              => 'authors',
				'name'             => 'post_author_override',
				'selected'         => $selected,
				'include_selected' => true,
				'show'             => 'display_name_with_login'
			)
		);
	}

	public static function register_setting() {
		add_settings_field(
			'wp-change-default-author',
			__( 'Default Author', 'wp-change-default-author' ),
			array( 'WPChangeDefaultAuthor', 'author_field' ),
			'general',
			'default'
		);
		register_setting(
			'general',
			'wp_change_default_author__author',
			array(
				'type' => 'integer',
				'description' => __( 'change post default author.', 'wp-change-default-user' ),
				'sanitize_callback' => array( 'WPChangeDefaultAuthor', 'author_sanitize_callback' )
			)
		);
	}

	public static function author_sanitize_callback( $value ) {
		if ( ! self::validate_author_id( $value ) ) {
			add_settings_error( 'general', 'wp_change_default_author__author__is_not_author', __( "Not valid vaule $value", 'wp-change-default-user' ) );
			return get_option( 'wp_change_default_author__author' );
		}
		$sanitized_value = intval( $value );
		if ( $sanitized_value === -1 ) {
			return null;
		}
		return $sanitized_value;
	}

	public static function get_author_id( $author ) {
		return $author->ID;
	}

	public static function validate_author_id( $value ) {
		try {
			$author_id = intval( $value );
		} catch( Exception $e ) {
			return FALSE;
		}

		if ( $author_id === -1 ) {
			return TRUE;
		}

		$authors = get_users(
			array(
				'who' => 'authors'
			)
		);
		$author_ids = array_map( array( 'WPChangeDefaultAuthor', 'get_author_id' ), $authors );
		return in_array( $author_id, $author_ids, TRUE );
	}

	public static function author_field() {
		$selected = get_option( 'wp_change_default_author__author' );
		wp_dropdown_users(
			array(
				'who'              => 'authors',
				'name'             => 'wp_change_default_author__author',
				'show_option_none' => __( 'Not Set', 'wp-change-default-author' ),
				'selected'         =>  $selected ? $selected : -1,
				'include_selected' => true,
				'show'             => 'display_name_with_login'
			)
		);
	}
}
