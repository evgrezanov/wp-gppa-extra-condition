<?php
//use GPPA_Object_Type;
/**
 * Plugin Name: WP-GPPA-extra-condition
 * Description: Add extra condition "IS CONTAIN IN"
 * Plugin URI: https://github.com/evgrezanov/wp-gppa-extra-condition
 * Author: Evgeniy Rezanov
 * Author URI: https://www.upwork.com/fl/evgeniirezanov
 * Version: 1.5.0
 */

defined('ABSPATH') || exit;
//gppa_default_operators
class WPGPPAextraCondition {

    public static function init(){
		//require plugin_dir_path( __FILE__ ) . 'class-gppa-object.php';
        add_filter( 'gppa_strings', array(__CLASS__, 'condition_humans_label') );
		add_filter( 'gppa_default_operators', array(__CLASS__, 'rewrite_default_operators') );
		add_filter( 'gppa_object_type_gf_entry_filter', array(__CLASS__, 'custom_contain_in_process_filter_default'), 10, 4 );
    }

    public static function condition_humans_label( $strings ) {
        $strings['operators']['is_contained_in'] = 'is contained in';
        return $strings;
	}
	
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
		
		$operator     = 'IN';
		$filter_value = $wpdb->esc_sql($filter_value);
		
		//$fvalue = exploid(', ', $filter_value);
		//-----------------------------------------
		$fvalue = array_map('trim',explode(",",$filter_value));
		$filter_value = $fvalue;
		//-----------------------------------------
		$fvalue = $wpdb->esc_sql($fvalue);
		
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