<?php
class NF_Views_Shortcode {
	public $view_id;
	public $submissions_count;
	public $table_heading_added = false;
	function __construct() {

		add_shortcode( 'nf-views', array( $this, 'shortcode' ), 10 );

	}

	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => '',
			), $atts );

		if ( empty( $atts['id'] ) ) {
			return;
		}
		$view_id = $atts['id'];
		$this->view_id = $view_id;
		$view_settings_json = get_post_meta( $view_id, 'view_settings', true );
		if ( empty( $view_settings_json ) ) {
			return;
		}

		$view_settings =  json_decode( $view_settings_json );
		$view_type = $view_settings->viewType;
		$method_name = 'get_view';
		$view  = $this->$method_name( $view_settings );
		return $view;

	}

	function get_view( $view_settings ) {
		global $wpdb;
		$before_loop_rows = $view_settings->sections->beforeloop->rows;
		$loop_rows = $view_settings->sections->loop->rows;
		$after_loop_rows = $view_settings->sections->afterloop->rows;

		$submissions_count = 0;
		$subs = $wpdb->get_results( "SELECT post_id FROM " . $wpdb->postmeta . " WHERE `meta_key` = '_form_id' AND `meta_value` = $view_settings->formId" );
		foreach ( $subs as $sub ) {
			if ( 'publish' == get_post_status( $sub->post_id ) ) $submissions_count++;
		}
		$this->submissions_count = $submissions_count;

		$per_page = $view_settings->viewSettings->multipleentries->perPage;
		$args = array(
			'form_id' => $view_settings->formId,
			'posts_per_page' =>$per_page,
		);

		if ( ! empty( $_GET['pagenum'] ) ) {
			$page_no = sanitize_text_field( $_GET['pagenum'] );
			$offset = $per_page * ( $page_no-1 );
			$args['offset'] = $offset;
		}

		$submissions = nf_views_get_submissions( $args );

		$view_content = '';
		if ( ! empty ( $before_loop_rows ) ) {
			$view_content .= $this->get_sections_content( 'beforeloop', $view_settings, $submissions );
		}

		if ( ! empty ( $loop_rows ) ) {
			$view_content .= $this->get_table_content( 'loop', $view_settings, $submissions );
		}

		if ( ! empty ( $after_loop_rows ) ) {
			$view_content .= $this->get_sections_content( 'afterloop', $view_settings, $submissions );
		}
		return $view_content;

	}


	function get_sections_content( $section_type, $view_settings, $submissions ) {
		$content = '';
		$section_rows = $view_settings->sections->{$section_type}->rows;
		if ( $section_type == 'loop' ) {
			foreach ( $submissions as $sub ) {
				foreach ( $section_rows as $row_id ) {
					$content .= $this->get_table_content( $row_id, $view_settings, $sub );
				}
			}
		} else {
			foreach ( $section_rows as $row_id ) {
				$content .= $this->get_grid_row_html( $row_id, $view_settings );
			}
		}
		return $content;
	}



	function get_table_content( $section_type, $view_settings, $submissions ) {
		$content = '';
		$section_rows = $view_settings->sections->{$section_type}->rows;
		$content = '<table class="nf-views-table nf-view-'.$this->view_id.'-table pure-table pure-table-bordered">';
		$content .= '<thead>';
		foreach ( $submissions as $sub ) {
			$content .= '<tr>';
			foreach ( $section_rows as $row_id ) {

				$content .= $this->get_table_row_html( $row_id, $view_settings, $sub);
			}
			$content .='</tr>';

		}
		$content .= '</tbody></table>';

		return $content;
	}

	function get_table_row_html( $row_id, $view_settings, $sub = false ) {
		$row_content = '';
		$row_columns = $view_settings->rows->{$row_id}->cols;
		foreach ( $row_columns as $column_id ) {
			$row_content .= $this->get_table_column_html( $column_id, $view_settings, $sub );
		}
		//$row_content .= '</table>'; // row ends
		return $row_content;
	}

	function get_table_column_html( $column_id, $view_settings, $sub ) {
		$column_size = $view_settings->columns->{$column_id}->size;
		$column_fields = $view_settings->columns->{$column_id}->fields;

		$column_content = '';

		if ( ! ( $this->table_heading_added ) ) {

			foreach ( $column_fields as $field_id ) {
				$column_content .= $this->get_table_headers( $field_id, $view_settings, $sub );
			}
			$this->table_heading_added = true;
			$column_content .= '</tr></thead><tbody><tr>';
		}
		foreach ( $column_fields as $field_id ) {

			$column_content .= $this->get_field_html( $field_id, $view_settings, $sub );
		}

		return $column_content;
	}



	function get_grid_row_html( $row_id, $view_settings, $sub = false ) {
		$row_columns = $view_settings->rows->{$row_id}->cols;

		$row_content = '<div class="pure-g">';
		foreach ( $row_columns as $column_id ) {
			$row_content .= $this->get_grid_column_html( $column_id, $view_settings, $sub );
		}
		$row_content .= '</div>'; // row ends
		return $row_content;
	}

	function get_grid_column_html( $column_id, $view_settings, $sub ) {
		$column_size = $view_settings->columns->{$column_id}->size;
		$column_fields = $view_settings->columns->{$column_id}->fields;

		$column_content = '<div class="pure-u-1 pure-u-md-' . $column_size . '">';

		foreach ( $column_fields as $field_id ) {

			$column_content .= $this->get_field_html( $field_id, $view_settings, $sub );

		}
		$column_content .= '</div>'; // column ends
		return $column_content;
	}

	function get_field_html( $field_id, $view_settings, $sub ) {
		$field = $view_settings->fields->{$field_id};
		$form_field_id = $field->formFieldId;
		$fieldSettings = $field->fieldSettings;
		$label = $fieldSettings->useCustomLabel ? $fieldSettings->label : $field->label;
		$class = $fieldSettings->customClass;
		$view_type = $view_settings->viewType;
		$field_html = '';
		if ( $view_type == 'table' ) {
			$field_html .= '<td>';
		}

		$field_html .= '<div class="field-' . $form_field_id . ' ' . $class . '">';

		// check if it's a form field
		if ( ! empty( $sub ) && is_object( $sub ) ) {
			//  if view type is table then don't send label
			if (  $view_type == 'table' ) {
				$field_html .= $sub->get_field_value( $form_field_id ) ;
			}else {
				if ( ! empty( $label ) ) {
					$field_html .= '<div class="field-label">' . $label . '</div>';
				}
				$field_html .= $sub->get_field_value( $form_field_id ) ;
			}
		} else {

			switch ( $field->formFieldId ) {
			case 'pagination':
				$field_html .= $this->get_pagination_links( $view_settings, $sub );
				break;
			case 'paginationInfo':
				$field_html .= $this->get_pagination_info( $view_settings, $sub );
				break;
			}
		}

		$field_html .= '</div>';
		if ( $view_type == 'table' ) {
			$field_html .= '</td>';
		}


		return $field_html;
	}

	function get_table_headers( $field_id, $view_settings, $sub ) {
		$field = $view_settings->fields->{$field_id};
		$fieldSettings = $field->fieldSettings;
		$label = $fieldSettings->useCustomLabel ? $fieldSettings->label : $field->label;
		return '<th>'.$label.'</th>';
	}


	function get_pagination_links( $view_settings, $sub ) {
		$links = '';
		$page_no = empty(  $_GET['pagenum'] ) ? 0 : sanitize_text_field( $_GET['pagenum'] );

		$submissions_count = $this->submissions_count;
		$per_page = $view_settings->viewSettings->multipleentries->perPage;
		$total_pages = round( $submissions_count / $per_page );
		if ( $submissions_count > $per_page ) {
			$links .= '<div class="pagination">';
			if ( $page_no > 1 ) {
				$links .= '	<a href="?pagenum=' . ( $page_no - 1 ) . '">&laquo;</a>';
			}

			for ( $i = 1; $i <= $total_pages; $i++ ) {
				$active = ( $page_no == $i || ( $page_no == 0 && $i == 1 ) ) ? 'active': '';
				$links .= '<a class="' . $active . '"  href="?pagenum=' . $i . '">' . $i . '</a>';
			}


			if ( ! empty( $_GET['pagenum'] ) ) {
				if ( $page_no < $total_pages ) {
					$links .= '<a href="?pagenum=' . ( $page_no + 1 ) . '">&raquo;</a>';
				}
			} else if ( $submissions_count > $per_page ) {
				$links .= '<a href="?pagenum=2">&raquo;</a>';
			}


			$links .= '</div>';
		}
		return $links;



	}

	function get_pagination_info( $view_settings, $sub ) {
		$page_no = empty(  $_GET['pagenum'] ) ? 1 : sanitize_text_field( $_GET['pagenum'] );
		$submissions_count = $this->submissions_count;
		$per_page = $view_settings->viewSettings->multipleentries->perPage;
		$from = ( $page_no-1 ) * $per_page;
		$of = $per_page * $page_no;


		return 'Displaying ' . $from . ' - ' . $of . ' of ' . $submissions_count ;
	}

}
new NF_Views_Shortcode();
