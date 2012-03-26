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
 * Internal library of functions for module alternative
 *
 * All the alternative specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


require dirname(__FILE__) . '/lib.php';
require dirname(__FILE__) . '/registration_form.php';

////////////////////////////////////////////////////////////////////////////////
// Custom functions                                                           //
////////////////////////////////////////////////////////////////////////////////

/**
 * Gets a full alternative record, with included options.
 *
 * @param int $id
 * @return object The object or null.
 */
function alternative_get_alternative($id, $withoptions=true) {
    global $DB;
    $alternative = $DB->get_record("alternative", array("id" => $id));
    if ($alternative && $withoptions) {
        $options = $DB->get_records("alternative_option", array("alternativeid" => $id), "id");
        if ($options) {
            foreach ($options as $option) {
                $alternative->option[$option->id] = $option;
            }
            /** @todo add a field 'placesoccupied' and simplify code elsewhere */
        }
    } else {
        $alternative = null;
    }
    return $alternative;
}

/**
 * Returns the form from which one can choose options.
 *
 * @global \moodle_db $DB
 * @param object $alternative
 * @param int $userid
 * @return \mod_alternative_registration_form
 */
function alternative_options_form($alternative, $userid) {
    global $DB;
    $sql = 'SELECT ao.*, ar.id AS registrationid '
        . 'FROM {alternative_option} AS ao '
        . 'LEFT OUTER JOIN {alternative_registration} AS ar '
        . 'ON (ao.id = ar.optionid AND ar.userid = ?) '
        . 'WHERE ao.alternativeid = ?';
    $options = $DB->get_records_sql($sql, array($userid, $alternative->id));
    return new mod_alternative_registration_form(
        null,
        array('alternative' => $alternative, 'options' => $options)
    );
}

/**
 * Returns an assoc array: optionid => occupied_places.
 *
 * If team reg is enable, each team count for one place.
 *
 * @global \moodle_db $DB
 * @param object $alternative
 * @param boolean $ignore_teams (opt) Force to ignore teams and count users.
 * @return array assoc array: optionid => occupied_places.
 */
function alternative_options_occupied_places($alternative, $ignore_teams=false) {
    global $DB;
    if ($alternative->teammin and !$ignore_teams) {
        $countable = "DISTINCT teamleaderid";
    } else {
        $countable = "*";
    }
    $sql = "SELECT optionid, count($countable) FROM {alternative_registration} "
        . 'WHERE alternativeid = ? GROUP BY optionid';
    return $DB->get_records_sql_menu($sql, array($alternative->id));
}

/**
 * Prints the HTML for the instructions to display above options.
 *
 * @param type $alternative
 */
function alternative_print_instructions($alternative) {
    global $OUTPUT;
    $instructions = get_string('instructionsgeneral', 'alternative');
    if ($instructions) {
        $instructions .= "<li>" . $instructions . "</li>";
    }
    if (!$alternative->changeallowed) {
        $instructions .= "<li>" . get_string('instructionsnochange', 'alternative', $alternative) . "</li>";
    }
    if ($alternative->teammin) {
        $instructions .= "<li>" . get_string('instructionsteam', 'alternative', $alternative) . "</li>";
    }
    if ($alternative->multiplemin) {
        if (!$alternative->multiplemax) {
            $instructions .= "<li>" . get_string('instructionsmultiplenomax', 'alternative', $alternative) . "</li>";
        } else {
            $instructions .= "<li>" . get_string('instructionsmultiple', 'alternative', $alternative) . "</li>";
        }
    }
    if ($instructions) {
        echo $OUTPUT->box("<ul>" . $instructions . "</ul>", 'generalbox', 'alternativeinstructions');
    }
}

function alternative_table_registrations($alternative) {
    global $DB;
    $table = array();

    return $table;
}

function alternative_table_users_reg($alternative) {
    global $DB;
    $table = array();

    return $table;
}

function alternative_table_users_not_reg($alternative, $context) {
    global $DB;
    $table = array();

    return $table;
}
