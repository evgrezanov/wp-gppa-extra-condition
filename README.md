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

#1
The problem is that I have 100 workers, divided in lets say 10 projects.
I operate many forms by sending in the Project PID and then populating
the values based on that project PID. Now I have a task/extra
work/report in the specific project where only 10 out of 100 workers are
working. I need to have a way to get a radio/checkbox with the name of
only 10 workers that are "activated" in the project and then to be able
to select the specific workers. Now we have a way where we populate all
the workers (even though they are not active in the project) and select
those we need. We do this where we bring all the workers "owned" by my,
in stead I want to bring all the workers which are active in an array.
In order to do so I have an ARRAY in the form I'm working on and I want
to populate EACH worker, whos PID is matched in the PID's array in the
current form Im working in. SO I need an operator "Is contained in" as
in Populate worker field IF workers PID IS CONTAINED IN the current
ARRAY of PIDs. So I match not an array to an array, but single PID's to
the current array in my form.

Hope this helps.

There are many functions where we can use this. Take all active workers
and give tasks to specific ones, add workers who are active in the
project to specific tasks they did alone or together with other active
workers, send notifications to active workers.... The uses are limitless
and we need to have an options to populate these workers.”

sql =>
SELECT SQL_CALC_FOUND_ROWS DISTINCT
`t1`.`id`
FROM
`wpstg0_gf_entry` AS `t1`
LEFT JOIN `wpstg0_gf_entry_meta` AS `m2`
ON (`m2`.`entry_id` = `t1`.`id` AND `m2`.`meta_key` = 4)
WHERE (`t1`.`form_id` IN (2)
AND (
(
(`m2`.`meta_key` = 4
AND `m2`.`meta_value` IN ('35789b', 'fa32f0', '7e88b9', 'b8627b', '81ce8b')
)
AND (`m2`.`meta_key` = 4
AND `m2`.`meta_value` = '35789b, fa32f0, 7e88b9, b8627b, 81ce8b'
)
)
AND `t1`.`status` != 'trash'))
ORDER BY `t1`.`id` ASC LIMIT 1250
