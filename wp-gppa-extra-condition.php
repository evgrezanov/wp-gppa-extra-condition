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
gform.addFilter('gform_conditional_logic_values_input', 'set_rule_info');

function set_rule_info(str, objectType, ruleIndex, selectedFieldId, selectedValue) {
    str = str.replace('Enter a value', 'Enter huinter the product name');
    return str;
}
</script>
<?php
        return $form;
    } 

    public static function condition_humans_label(){
        return apply_filters( 'gppa_strings', array(
			'populateChoices' => esc_html__( 'Populate choices dynamically', 'gp-populate-anything' ),
			'populateValues'  => esc_html__( 'Populate value dynamically', 'gp-populate-anything' ),
			'addFilter'       => esc_html__( 'Add Filter', 'gp-populate-anything' ),
			'label'           => esc_html__( 'Label', 'gp-populate-anything' ),
			'value'           => esc_html__( 'Value', 'gp-populate-anything' ),
			'price'           => esc_html__( 'Price', 'gp-populate-anything' ),
			'loadingEllipsis' => esc_html__( 'Loading...', 'gp-populate-anything' ),
			'addCustomValue'  => esc_html__( 'Add Custom Value', 'gp-populate-anything' ),
			'standardValues'  => esc_html__( 'Standard Values', 'gp-populate-anything' ),
			'formFieldValues' => esc_html__( 'Form Field Values', 'gp-populate-anything' ),
			'specialValues'   => esc_html__( 'Special Values', 'gp-populate-anything' ),
			'valueBoolTrue'   => esc_html__( '(boolean) true', 'gp-populate-anything' ),
			'valueBoolFalse'  => esc_html__( '(boolean) false', 'gp-populate-anything' ),
			'valueNull'       => esc_html__( '(null) NULL', 'gp-populate-anything' ),
			'selectAnItem'    => esc_html__( 'Select a %s', 'gp-populate-anything' ),
			'unique'          => esc_html__( 'Only Show Unique Results', 'gp-populate-anything' ),
			'reset'           => esc_html__( 'Reset', 'gp-populate-anything' ),
			'type'            => esc_html__( 'Type', 'gp-populate-anything' ),
			'objectType'      => esc_html__( 'Object Type', 'gp-populate-anything' ),
			'filters'         => esc_html__( 'Filters', 'gp-populate-anything' ),
			'ordering'        => esc_html__( 'Ordering', 'gp-populate-anything' ),
			'ascending'       => esc_html__( 'Ascending', 'gp-populate-anything' ),
			'descending'      => esc_html__( 'Descending', 'gp-populate-anything' ),
			'choiceTemplate'  => esc_html__( 'Choice Template', 'gp-populate-anything' ),
			'valueTemplates'  => esc_html__( 'Value Templates', 'gp-populate-anything' ),
			'operators'       => array(
				'is'          => __( 'is', 'gp-populate-anything' ),
				'isnot'       => __( 'is not', 'gp-populate-anything' ),
				'>'           => __( '>', 'gp-populate-anything' ),
				'>='          => __( '>=', 'gp-populate-anything' ),
				'<'           => __( '<', 'gp-populate-anything' ),
				'<='          => __( '<=', 'gp-populate-anything' ),
				'contains'    => __( 'contains', 'gp-populate-anything' ),
				'starts_with' => __( 'starts with', 'gp-populate-anything' ),
				'ends_with'   => __( 'ends with', 'gp-populate-anything' ),
                'like'        => __( 'is LIKE', 'gp-populate-anything' ),
                'is_contained_in' => __('is contained in', 'wp-gppa-extra-condition'),
			),
			'chosen_no_results' => esc_attr( gf_apply_filters( array( 'gform_dropdown_no_results_text', 0 ), __( 'No results matched', 'gp-populate-anything' ), 0 ) ),
            'restrictedObjectTypeNonPrivileged' => esc_html__( 'This field is configured to an object type for which you do not have permission to edit.', 'gp-populate-anything' ),
            'restrictedObjectTypePrivileged' => esc_html__( 'The selected Object Type is restricted. Non-super admins will not be able to edit this field\'s GPPA settings.', 'gp-populate-anything' ),
		) );
    }

}

WPGPPAextraCondition::init();