<?php

class GPPA_Object_Type_GF_Entry_Rewrite extends GPPA_Object_Type_GF_Entry {

	public function __construct($id) {
		parent::__construct($id);
		add_action( 'gppa_pre_object_type_query_gf_entry', array( $this, 'add_filter_hooks_rewrite' ) );
	}

	public function add_filter_hooks_rewrite() {
		add_filter('gppa_object_type_gf_entry_filter', array( $this, 'process_filter_rewrite'), 10, 4 );
	}

    public function process_filter_rewrite( $gf_query_where, $args ) {

		/**
		 * @var $filter_value
		 * @var $filter
		 * @var $filter_group
		 * @var $filter_group_index
		 * @var $primary_property_value
		 * @var $property
		 * @var $property_id
		 */
		extract($args);

		if ( ! isset( $gf_query_where[ $filter_group_index ] ) ) {
			$gf_query_where[ $filter_group_index ] = array();
		}

		switch ( strtoupper( $filter['operator'] ) ) {
			case 'CONTAINS' :
				$operator     = GF_Query_Condition::LIKE;
				$filter_value = $this->get_sql_value( $filter['operator'], $filter_value );
                break;
            case 'IS_CONTAINED_IN' :
                $operator     = GF_Query_Condition::IN;
                $filter_value = $this->get_sql_value( $filter['operator'], $filter_value );
                break;
			case 'STARTS_WITH' :
				$operator     = GF_Query_Condition::LIKE;
				$filter_value = $this->get_sql_value( $filter['operator'], $filter_value );
				break;
			case 'ENDS_WITH' :
				$operator     = GF_Query_Condition::LIKE;
				$filter_value = $this->get_sql_value( $filter['operator'], $filter_value );
				break;
			case 'IS NOT' :
			case 'ISNOT' :
			case '<>' :
				$operator = GF_Query_Condition::NEQ;
				break;
			case 'LIKE' :
				$operator = GF_Query_Condition::LIKE;
				break;
			case 'NOT IN' :
				$operator = GF_Query_Condition::NIN;
				break;
			case 'IN' :
				$operator = GF_Query_Condition::IN;
				break;
			case '>=':
				$operator = GF_Query_Condition::GTE;
				break;
			case '<=':
				$operator = GF_Query_Condition::LTE;
				break;
			case '<':
				$operator = GF_Query_Condition::LT;
				break;
			case '>':
				$operator = GF_Query_Condition::GT;
				break;
			case 'IS' :
			case '=' :
			default:
				$operator = GF_Query_Condition::EQ;
				// Implemented to support Checkbox fields as a Form Field Value filters.
				if( is_array( $filter_value ) ) {
					$operator = GF_Query_Condition::IN;
				}
				break;
		}

		if ( is_numeric( $filter_value ) ) {
			$filter_value = floatval( $filter_value );
		}

		if ( is_array( $filter_value ) ) {
			foreach( $filter_value as &$_filter_value ) {
				$_filter_value = new GF_Query_Literal( $_filter_value );
			}
			unset( $_filter_value );
			$filter_value = new GF_Query_Series( $filter_value );
		} else {
			/**
			 * Convert date string to ISO 8601 for MySQL date comparisons
			 *
			 * strtotime doesn't play nicely with formats like d/m/y out of the box so we need to parse the date
			 * ourselves into a time based on the format from the actual date field saved in the form that we're
			 * pulling entries from.
			 */
			$form_id  = $primary_property_value;
			$field_id = str_replace( 'gf_field_', '', rgar( $args, 'property_id' ) );
			$date_field  = GFAPI::get_field( $form_id, absint( $field_id ) );
			$time = null;

			// Ensure we're querying a date field before attempting to parse the filter as such
			if ( $date_field->type == 'date' || gf_apply_filters( array( 'gppa_process_value_as_date', $form_id, $date_field->id ), false, $date_field ) ) {
				if ( $date_format = rgar( $date_field, 'dateFormat' ) ) {
					$time = $this->date_to_time( $filter_value, $date_format );
				}

				if ( ! is_numeric( $filter_value ) &&
												 ( $time || (strlen( $filter_value ) > 1 && strtotime( $filter_value )) )
						) {
					if ( !$time) {
						$time = strtotime( $filter_value );
					}

					$filter_value = date( 'Y-m-d', $time );
				}
			}

			$filter_value = new GF_Query_Literal( $filter_value );
		}

		$gf_query_where[ $filter_group_index ][] = new GF_Query_Condition(
			new GF_Query_Column( rgar( $property, 'value' ), (int) $primary_property_value ),
			$operator,
			$filter_value
		);

		return $gf_query_where;

	}


}