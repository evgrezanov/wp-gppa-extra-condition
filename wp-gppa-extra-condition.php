<?php

/**
 * Plugin Name: WP-GPPA-extra-condition
 * Description: Add extra condition "IS CONTAIN IN"
 * Plugin URI: https://github.com/evgrezanov/wp-gppa-extra-condition
 * Author: Evgeniy Rezanov
 * Author URI: https://www.upwork.com/fl/evgeniirezanov
 * Version: 1.0
 */

defined('ABSPATH') || exit;

class WPGPPAextraCondition {

    public static function init(){
        add_action( 'gform_admin_pre_render', array(__CLASS__, 'set_conditional') );
        add_filter( 'gppa_strings', array(__CLASS__, 'condition_humans_label') );
        add_filter( 'gppa_object_type_gf_entry_filter', array(__CLASS__, 'custom_process_filter_default'), 10, 4 );
    }

    public static function set_conditional( $form ) {
        ?>
<script type="text/javascript">
gform.addFilter('gform_conditional_logic_operators', function(operators, objectType, fieldId) {
    operators = {
        "is": "is",
        "isnot": "isNot",
        ">": "greaterThan",
        "<": "lessThan",
        "contains": "contains",
        "starts_with": "startsWith",
        "ends_with": "endsWith",
        "is_contained_in": "isContainedIn"
    };

    return operators;
});
/*
gform.addFilter('gform_conditional_logic_values_input', 'set_rule_info');

function set_rule_info(str, objectType, ruleIndex, selectedFieldId, selectedValue) {
    str = str.replace('Enter a value', 'Enter huinter the product name');
    return str;
}
*/
</script>
<?php
        return $form;
    } 

    public static function condition_humans_label( $strings ) {
        $strings['operators']['is_contained_in'] = 'is contained in';
        return $strings;
    }

    public function custom_process_filter_default( $gf_query_where, $args ) {
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

WPGPPAextraCondition::init();