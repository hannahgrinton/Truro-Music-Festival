<?php

function nf_views_get_ninja_form_fields( $form_id ) {
	if ( empty( $form_id ) ) {
		return '{}';
	}
	$form_fields_obj = new stdClass();
	$fields = Ninja_Forms()->form( $form_id )->get_fields();
	foreach ( $fields as $field ) {
		$form_fields_obj->{$field->get_id()} = (object)array( 'id' => $field->get_id(), 'label' => $field->get_setting( 'label' ) );
	}
	return json_encode( $form_fields_obj );

}


/**
 * Get submissions based on specific critera.
 *
 * @since 2.7
 * @param array   $args
 * @return array $sub_ids
 */
function nf_views_get_submissions( $args ) {

	$query_args = array(
		'post_type'   => 'nf_sub',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'date_query'  => array(
			'inclusive'  => true,
		),
	);
	if ( isset( $args['posts_per_page'] ) && ! empty( $args['posts_per_page'] ) ) {
		$query_args['posts_per_page'] = $args['posts_per_page'];
	}
	if ( isset( $args['offset'] ) && ! empty( $args['offset'] ) ) {
		$query_args['offset'] = $args['offset'];
	}

	if ( isset( $args['form_id'] ) ) {
		$query_args['meta_query'][] = array(
			'key' => '_form_id',
			'value' => $args['form_id'],
		);
	}

	if ( isset( $args['seq_num'] ) ) {
		$query_args['meta_query'][] = array(
			'key' => '_seq_num',
			'value' => $args['seq_num'],
		);
	}

	if ( isset( $args['user_id'] ) ) {
		$query_args['author'] = $args['user_id'];
	}

	if ( isset( $args['action'] ) ) {
		$query_args['meta_query'][] = array(
			'key' => '_action',
			'value' => $args['action'],
		);
	}

	if ( isset ( $args['meta'] ) ) {
		foreach ( $args['meta'] as $key => $value ) {
			$query_args['meta_query'][] = array(
				'key' => $key,
				'value' => $value,
			);
		}
	}

	if ( isset ( $args['fields'] ) ) {
		foreach ( $args['fields'] as $field_id => $value ) {
			$query_args['meta_query'][] = array(
				'key' => '_field_' . $field_id,
				'value' => $value,
			);
		}
	}

	if ( isset( $args['begin_date'] ) and $args['begin_date'] != '' ) {
		$query_args['date_query']['after'] = nf_get_begin_date( $args['begin_date'] )->format( "Y-m-d G:i:s" );
	}

	if ( isset( $args['end_date'] ) and $args['end_date'] != '' ) {
		$query_args['date_query']['before'] = nf_get_end_date( $args['end_date'] )->format( "Y-m-d G:i:s" );
	}

	$subs = new WP_Query( $query_args );;

	$sub_objects = array();
	// var_dump( $subs->posts); die;
	if ( is_array( $subs->posts ) && ! empty( $subs->posts ) ) {
		foreach ( $subs->posts as $sub ) {
			$sub_objects[] = Ninja_Forms()->form()->get_sub( $sub->ID );
		}
	}

	wp_reset_postdata();
	return $sub_objects;
}
