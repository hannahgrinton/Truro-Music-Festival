<?php
/*
 * Plugin Name: Views for Ninja Forms
 * Plugin URI: https://webolics.org
 * Description: Show Ninja Forms Submissions in frontend.
 * Version: 1.0
 * Author: Webholics
 * Author URI: https://webolics.org
 * Text Domain: nf-views
 *
 * Copyright 2018.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( "NF_VIEWS_URL", plugins_url() . "/" . basename( dirname( __FILE__ ) ) );
define( "NF_VIEWS_DIR_URL", WP_PLUGIN_DIR . "/" . basename( dirname( __FILE__ ) ) );

require_once NF_VIEWS_DIR_URL . '/inc/helpers.php';

//Backend
require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-posttype.php';
require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-metabox.php';
require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-ajax.php';

//Frontend
require_once NF_VIEWS_DIR_URL . '/inc/class-nf-views-shortcode.php';

add_action( 'admin_enqueue_scripts', 'nf_views_admin_scripts' );

add_action( 'wp_enqueue_scripts', 'nf_views_frontend_scripts' );

function nf_views_admin_scripts( $hook ) {
	global $post;
	if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
		if ( 'nf-views' === $post->post_type ) {

			wp_enqueue_style( 'font-awesome', NF_VIEWS_URL .'/assets/css/font-awesome.css' );
			wp_enqueue_style( 'pure-css', NF_VIEWS_URL .'/assets/css/pure-min.css' );
			wp_enqueue_style( 'pure-grid-css', NF_VIEWS_URL .'/assets/css/grids-responsive-min.css' );
			wp_enqueue_style( 'nf-views-admin', NF_VIEWS_URL . '/assets/css/nf-views-admin.css' );


			$js_dir    = NF_VIEWS_DIR_URL . '/ninja-views/build/static/js';
			$js_files = array_diff( scandir( $js_dir ), array( '..', '.' ) );
			$count = 0;
			foreach ( $js_files as $js_file ) {
				if ( strpos( $js_file , '.js.map'  )  === false  ) {
					$js_file_name = $js_file;
					wp_enqueue_script( 'nf_views_script' . $count, NF_VIEWS_URL . '/ninja-views/build/static/js/' . $js_file_name, array( 'jquery' ), '', true );
					$count++;
					// wp_localize_script( 'react_grid_script'.$count, 'formData' , $form_data );
				}
			}

			$css_dir    = NF_VIEWS_DIR_URL . '/ninja-views/build/static/css';
			$css_files = array_diff( scandir( $css_dir ), array( '..', '.' ) );

			foreach ( $css_files as $css_file ) {
				if ( strpos( $css_file , '.css.map'  ) === false ) {
					$css_file_name = $css_file;
				}
			}
			// $grid_options = get_option( 'gf_stla_form_id_grid_layout_4');
			wp_enqueue_style( 'nf_views_style', NF_VIEWS_URL . '/ninja-views/build/static/css/' . $css_file_name );
		}
	}
}

function nf_views_frontend_scripts() {
	wp_enqueue_style( 'pure-css', NF_VIEWS_URL .'/assets/css/pure-min.css' );
	wp_enqueue_style( 'pure-grid-css', NF_VIEWS_URL .'/assets/css/grids-responsive-min.css' );
	wp_enqueue_style( 'nf-views-front', NF_VIEWS_URL . '/assets/css/nf-views-display.css' );
}
