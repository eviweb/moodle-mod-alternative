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
 * notification sent event
 *
 * @package    mod
 * @subpackage alternative
 * @author     Eric VILLARD <dev@eviweb.fr>
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_alternative\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_alternative notification sent event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int alternativeid: the id of the alternative.
 *      - string alternativename: the name of the alternative.
 *      - int errors: the sending errors.
 * }
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_sent extends \core\event\base
{
    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventnotificationsent', 'mod_alternative');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "A notification was sent to users registered to the alternative named '" . s($this->other['alternativename']) ."'";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url(
            '/mod/alternative/notifyregistered.php',
            array(
                'a' => $this->other['alternativeid']
            )
        );
    }

    /**
     * Return the legacy event log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array(
            $this->courseid,
            'alternative',
            'notification '.($this->other['errors'] !== false ? 'sent' : 'NOT cleanly sent'),
            'view.php?id=' . $this->contextinstanceid,
            $this->other['alternativeid'],
            $this->contextinstanceid
        );
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['alternativeid'])) {
            throw new \coding_exception('The \'alternativeid\' value must be set in other.');
        }

        if (!isset($this->other['alternativename'])) {
            throw new \coding_exception('The \'alternativename\' value must be set in other.');
        }

        if (!isset($this->other['errors'])) {
            throw new \coding_exception('The \'errors\' value must be set in other.');
        }
    }
}
