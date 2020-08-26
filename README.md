# wp-gppa-extra-condition

The plugin we are talking about is Gravity Perks Populate Anything. The operator should check if Variable A is contained in Array B ("is contained in").
Currently the operators that is available for us is ("contains"), where it checks if Array A contains Variable B.
/_ # Correct query should look like:
SELECT
DISTINCT `t1`.`id`
FROM
`wpstg0_gf_entry` AS `t1`
LEFT JOIN `wpstg0_gf_entry_meta` AS `m2` ON (`m2`.`entry_id` = `t1`.`id` AND `m2`.`meta_key` = 4)
WHERE (`t1`.`form_id` IN (2)
AND ((
`m2`.`meta_key` = 4
AND
`m2`.`meta_value` IN ('35789b', 'fa32f0', '7e88b9', 'b8627b', '81ce8b') ) ## <-- correct operator and \$fvalue
AND
`t1`.`status` != 'trash'))
ORDER BY `t1`.`id` ASC
LIMIT 1250
_/
