<?php

class NF_Views_Posttype {
	function __construct() {
		add_action( 'init', array( $this, 'create_posttype' ) );
		add_filter( 'manage_posts_columns' , array( $this,'add_shortcode_column') );
		add_action( 'manage_nf-views_posts_custom_column' , array( $this, 'shortcode_column_detail' ), 10, 2 );
	}

	function create_posttype() {

		$labels = array(
			'name'               => _x( 'Views', 'post type general name', 'nf-views' ),
			'singular_name'      => _x( 'View', 'post type singular name', 'nf-views' ),
			'menu_name'          => _x( 'Views', 'admin menu', 'nf-views' ),
			'name_admin_bar'     => _x( 'Views', 'add new on admin bar', 'nf-views' ),
			'add_new'            => _x( 'Add View', 'book', 'nf-views' ),
			'add_new_item'       => __( 'Add New View', 'nf-views' ),
			'new_item'           => __( 'New View', 'nf-views' ),
			'edit_item'          => __( 'Edit View', 'nf-views' ),
			'view_item'          => __( 'View NF Popup', 'nf-views' ),
			'all_items'          => __( 'All Views', 'nf-views' ),
			'search_items'       => __( 'Search Views', 'nf-views' ),
			'parent_item_colon'  => __( 'Parent Views:', 'nf-views' ),
			'not_found'          => __( 'No view found.', 'nf-views' ),
			'not_found_in_trash' => __( 'No view found in Trash.', 'nf-views' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'nf-views' ),
			'public'             => false,
			'exclude_from_search'=> true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'nf-views' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'menu_icon'		 => 'dashicons-format-gallery',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'false' )
		);

		register_post_type( 'nf-views', $args );
	}

	function add_shortcode_column( $columns ) {
		$columns = array_slice($columns, 0, 2, true) + array("shortcode" =>__( 'Shortcode', 'nf-views' )) + array_slice($columns, 2, count($columns)-2, true);
		//$columns['shortcode'] = __( 'Shortcode', 'nf-views' );
		return $columns;
	}





	function shortcode_column_detail( $column, $post_id ) {
		switch ( $column ) {

			case 'shortcode' :
				 echo '<code>[nf-views id='.$post_id.']</code>';
				break;

		}
	}

}

new NF_Views_Posttype();
