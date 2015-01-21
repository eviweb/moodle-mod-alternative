<?php

// This module is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This module is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The module controller.
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"]))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$coursecontext = context_course::instance($course->id);

require_course_login($course);
$PAGE->set_pagelayout('incourse');

$event = \mod_alternative\event\course_module_instance_list_viewed::create(
    array(
        'context' => $coursecontext
    )
);
$event->trigger();

$PAGE->set_url('/mod/alternative/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

if (! $alternatives = get_all_instances_in_course('alternative', $course)) {
    notice(get_string('noalternatives', 'alternative'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

$table = new html_table();
if ($course->format == 'weeks') {
    $table->head  = array(get_string('week'), get_string('name'), '', '');
    $table->align = array('center', 'left', 'left', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array(get_string('topic'), get_string('name'), '', '');
    $table->align = array('center', 'left', 'left', 'left');
} else {
    $table->head  = array(get_string('name'), '', '');
    $table->align = array('left', 'left', 'left');
}

$cellmultiple = array(false => get_string('unique', 'alternative'), true => get_string('multiple', 'alternative'));
$cellteam = array(false => get_string('individual', 'alternative'), true => get_string('team', 'alternative'));

foreach ($alternatives as $alternative) {
    if (!$alternative->visible) {
        $link = html_writer::link(
            new moodle_url('/mod/alternative/view.php', array('id' => $alternative->coursemodule)),
            format_string($alternative->name, true),
            array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/alternative/view.php', array('id' => $alternative->coursemodule)),
            format_string($alternative->name, true));
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($alternative->section,
                                $link,
                                $cellteam[$alternative->teammin >=1],
                                $cellmultiple[$alternative->multiplemin >=1],
                );
    } else {
        $table->data[] = array($link,
                                $cellteam[$alternative->teammin >=1],
                                $cellmultiple[$alternative->multiplemin >=1],
            );
    }
}

echo $OUTPUT->heading(get_string('modulenameplural', 'alternative'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();
