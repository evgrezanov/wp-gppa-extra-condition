<?php

/**
 * Plugin Name: WP-GPPA-extra-condition
 * Description: Add extra condition "IS CONTAIN IN". Tested with GP Populate Anything Version 1.0-beta-4.91 and Gravity Forms Version 2.4.20
 * Plugin URI: https://github.com/evgrezanov/wp-gppa-extra-condition
 * Author: Evgeniy Rezanov
 * Author URI: https://www.upwork.com/fl/evgeniirezanov
 * Version: 1.8
 */

defined('ABSPATH') || exit;

class WPGPPAextraCondition {
    public static function init(){
		// Add string label for new operator
		add_filter( 'gppa_strings', array(__CLASS__, 'condition_humans_label') );
		// Re-write default operators
		add_filter( 'gppa_default_operators', array(__CLASS__, 'rewrite_default_operators') );
		// Re-write query operators before sql query
		add_filter( 'gppa_object_type_gf_entry_filter', array(__CLASS__, 'custom_contain_in_process_filter_default'), 10, 4 );
    }

	// Add string label for new operator
    public static function condition_humans_label( $strings ) {
        $strings['operators']['is_contained_in'] = 'is contained in';
        return $strings;
	}
	
	// Re-write default operators
    public static function rewrite_default_operators() {
		return array(
			'is',
			'isnot',
			'>',
			'>=',
			'<',
			'<=',
			'contains',
			'starts_with',
			'ends_with',
            'like',
            'is_contained_in',
		);
	}

	// Re-write query operators
	public static function custom_contain_in_process_filter_default( $gf_query_where, $args ) {
		global $wpdb;
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


		if ( strtoupper($filter['operator']) != 'IS_CONTAINED_IN' ):
			return $gf_query_where;
		endif;

		if ( ! isset( $gf_query_where[ $filter_group_index ] ) ) {
			$gf_query_where[ $filter_group_index ] = array();
		}
		
		// Correct operator for IS_CONTAINED_IN array
		$operator     = 'IN';
		$filter_value = esc_sql($filter_value);
		
		// Trim string array and exploid values
		$fvalue = array_map('trim',explode(",",$filter_value));
		$filter_value = $fvalue;
		
		if ( is_array( $filter_value ) ) {
			foreach( $filter_value as &$_filter_value ) {
				$_filter_value = new GF_Query_Literal( $_filter_value );
			}
			unset( $_filter_value );
			$filter_value = new GF_Query_Series( $filter_value );
		}
		
		$gf_query_where[ $filter_group_index ][] = new GF_Query_Condition(
			new GF_Query_Column( rgar( $property, 'value' ), (int) $primary_property_value ),
			$operator,
			$filter_value
		);
		return $gf_query_where;
    }

}

WPGPPAextraCondition::init();