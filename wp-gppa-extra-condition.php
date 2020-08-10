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
        "is_contained_in": "Starred"
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

}

WPGPPAextraCondition::init();