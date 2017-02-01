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
 * instance viewed event
 *
 * @package    mod
 * @subpackage alternative
 * @author     Eric VILLARD <dev@eviweb.fr>
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_alternative\observer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/alternative/lib.php');

class group {
    /**
     * A group was created.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function created($event) {
        alternative_group_created(
            self::createEventData($event)
        );
    }
    
    /**
     * A group was updated.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function updated($event) {
        alternative_group_updated(
            self::createEventData($event)
        );
    }
    
    /**
     * A group was deleted.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function deleted($event) {
        alternative_group_deleted(
            self::createEventData($event)
        );
    }
    
    /**
     * Create an event data object.
     *
     * @param \core\event\base $event The event.
     * @return object
     */
    private static function createEventData($event)
    {
        $eventdata = new \stdClass();
        $eventdata->id = $event->objectid;
        $eventdata->courseid = $event->courseid;
        $eventdata->name = $event->get_record_snapshot(
            'groups',
            $event->objectid
        )->name;
        
        return $eventdata;
    }
}