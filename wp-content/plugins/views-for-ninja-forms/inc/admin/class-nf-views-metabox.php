<?php

class NF_Views_Metabox {


	function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );

	}
	/**
	 * Register meta box(es).
	 */
	function register_meta_boxes() {
		add_meta_box( 'nf-views-metabox', __( 'View Settings', 'nf-views' ),  array( $this, 'views_metabox' ), 'nf-views', 'normal', 'high' );
	}



	function views_metabox( $post ) {
		// $form_id = $this->get_setting( 'ninja_form_id' );
		if ( class_exists( 'Ninja_Forms' ) ) {
			$forms = Ninja_Forms()->form()->get_forms();
			// echo '<pre>';

			$view_forms = array( array( 'id' => '', 'label' => 'Select' ) );
			foreach ( $forms as $form ) {
				// print_r($models = Ninja_Forms()->form( $form->get_id() )->get_fields()); die;
				$view_forms[] = array( 'id' => $form->get_id(), 'label' => $form->get_setting( 'title' ) );
			}
			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'nf_views_metabox', 'nf_views_nonce' );
			// delete_post_meta($post->ID, 'view_settings');
			$nf_view_saved_settings = get_post_meta( $post->ID, 'view_settings', true );
			if ( empty( $nf_view_saved_settings ) ) {
				$nf_view_saved_settings = '{}';
				$form_id = '';
				if ( ! empty( $view_forms[1]['id'] ) ) {
					$form_id = $view_forms[1]['id'];
				}
			}else {
				$view_settings = json_decode( html_entity_decode( $nf_view_saved_settings ) );
				$form_id = $view_settings->formId;
			}
			$form_fields = nf_views_get_ninja_form_fields( $form_id );
?>
	<script>
		var view_forms = '<?php echo json_encode( $view_forms ); ?>';
		var _nf_view_saved_settings = '<?php echo $nf_view_saved_settings ?>';
		var _nf_view_form_fields =  '<?php echo $form_fields; ?>';
	</script>
		   <div id="views-container"></div>

		<?php
		} else {
			echo 'Please install Ninja Forms 3.0 or later to use this plugin';

		}
	}

	/**
	 * Save meta box content.
	 *
	 * @param int     $post_id Post ID
	 */
	function save_meta_box( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['nf_views_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['nf_views_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'nf_views_metabox' ) ) {
			return $post_id;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$finale_view_settings = $_POST['final_view_settings'];

		update_post_meta( $post_id, 'view_settings', $finale_view_settings );

		/* OK, it's safe for us to save the data now. */

		// // Sanitize the user input.
		// $nf_popups = $_POST['nf_popups_settings'];

		// // Update the meta field.
		// update_post_meta( $post_id, 'nf_popups_settings', $nf_popups );
	}



	function get_setting( $setting_name ) {
		$settings = $this->settings;
		return isset( $settings[$setting_name] )?$settings[$setting_name]: '';
	}

}

new NF_Views_Metabox();
