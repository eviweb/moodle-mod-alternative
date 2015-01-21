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
 * The form that gets user registration for the module "alternative".
 *
 * $form = new mod_alternative_registration_form(
 *     null,
 *     array('alternative' => , 'options' =>)
 * );
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_alternative_registration_form extends moodleform {
    private $user_has_registered = false;
    private $registrationinfo;
    /**
     * @var int null unless the registration applies to a selected user.
     */
    private $userid;

    public function __construct($action=null, $customdata=null) {
        if (!isset($customdata['alternative']->id)) {
            print_error('invalidform');
        }
        $customdata['occupied'] = alternative_options_occupied_places($customdata['alternative'], false);
        $customdata['teamoccupied'] = alternative_options_occupied_places($customdata['alternative'], true);
        parent::moodleform($action, $customdata);
        $this->registrationinfo = array();
    }

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'a', $this->_customdata['alternative']->id);
        $mform->setType('a', PARAM_INT);

        // register another user
        $context = context_course::instance($this->_customdata['alternative']->course);
        if (has_capability('mod/alternative:forceregistrations', $context)) {
            $mform->addElement('hidden', 'forcereg', 1);
            $mform->setType('forcereg', PARAM_BOOL);
            $mform->addElement('header', 'fieldset_user', get_string("chooseuser", 'alternative'));
            $mform->targetselector = new select_team_members(
                'targetuser',
                array('alternative' => $this->_customdata['alternative'], 'multiselect' => false)
            );
            $mform->targetselector->message = get_string('usersnotreg', 'alternative');
            $mform->addElement('html', $mform->targetselector->display(true));
        }

        // team members
        $is_team = false;
        if ($this->_customdata['alternative']->teammax > 1) {
            $is_team = true;
            $mform->addElement('header', 'fieldset_team', get_string("chooseteammembers", 'alternative'));
            $mform->membersselector = new select_team_members(
                'teammembers',
                array('alternative' => $this->_customdata['alternative'])
            );
            $mform->membersselector->message = get_string('potentialteammembers', 'alternative');
            $mform->addElement('html', $mform->membersselector->display(true));
        }

        $input = $this->_customdata['alternative']->multiplemin ? 'checkbox' : 'radio';
        $occupied = $this->_customdata['occupied'];
        $teamoccupied = $this->_customdata['teamoccupied'];

		if ( $this->_customdata['alternative']->compact ) { //compact display
			$mform->addElement('header', "fieldset[0]", 'Options');
		}

        foreach ($this->_customdata['options'] as $id => $option) {
            $attributes = null; // s'il n'y a plus de places dans cette option => disabled
            if ($option->placesavail) {
                $avail = $option->placesavail - (empty($occupied[$id]) ? 0 : $occupied[$id]);
				if ($avail <= 0 && ($option->registrationid == null) ) { // si option cochée, elle reste disponible même si 0 places
                    $attributes = array('disabled' => 'disabled');
                }
			}
            if ($is_team && $option->teamplacesavail) {
                $teamavail = $option->teamplacesavail - (empty($teamoccupied[$id]) ? 0 : $teamoccupied[$id]);
				if ($teamavail <= 0 && ($option->registrationid == null) ) { // si option cochée, elle reste disponible même si 0 places
                    $attributes = array('disabled' => 'disabled');
                }
			}

			if ( ! $this->_customdata['alternative']->compact ) { // long display
				$mform->addElement('header', "fieldset[$id]", $option->name);
				if ($input === 'checkbox') {
                    $attributes['value'] = $id; // booléen suffirait
					$mform->addElement($input, "option[{$id}]", '', ' ' . $option->name, $attributes);
					$mform->setDefault("option[{$id}]", $option->registrationid);
				} else { // radio
	                $mform->addElement($input, "option", '', ' ' . $option->name, $id, $attributes);
		            if ($option->registrationid) {
			            $mform->setDefault("option", $id);
				    }
				}
				$mform->addElement('static', "optionintro[{$id}]", 'Description', format_text($option->intro, $option->introformat));
				if ($option->datecomment) {
	                $mform->addElement('static', 'datecomment', 'Date', $option->datecomment);
		        }
			    if ($option->placesavail) {
					$mform->addElement('static', 'places', 'Indiv. places', $avail);
				}
                if ($is_team && $option->teamplacesavail) {
					$mform->addElement('static', 'teamplaces', 'Team places', $teamavail);
				}
			} else { // compact display
				$line = '';
				$line .= ($option->datecomment ? '('.$option->datecomment.') ' : '');
				$line .= $option->name;
				$line .= ($option->placesavail ? ' ('. $option->placesavail .' ind. pl.)' : '');
                $line .= ($is_team && $option->teamplacesavail ? ' ('. $option->teamplacesavail .' team pl.)' : '');
				if ($input === 'checkbox') {
                    $attributes['value'] = $id; // booléen suffirait
					$mform->addElement($input, "option[{$id}]", '', ' ' . $line, $attributes);
					$mform->setDefault("option[{$id}]", $option->registrationid);
				} else {
	                $mform->addElement($input, "option", '', ' ' . $line, $id, $attributes);
		            if ($option->registrationid) {
			            $mform->setDefault("option", $id);
				    }
				}
				//** @todo ajouter la description repliée ou popup
			}
            if ($option->registrationid) {
                $this->user_has_registered = true;
            }
        }
        //-------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    public function freeze() {
        $this->_form->freeze();
    }

    public function is_registered() {
        return $this->user_has_registered;
    }

    public function getRegistrationInfo()
    {
        return $this->registrationinfo;
    }

    public function save_to_db($userid) {
        global $DB;
        $this->registrationinfo = array();
        $data = $this->get_data();
        if (empty($data) or empty($data->option)) {
            return false;
        }
        if (!is_array($data->option)) {
            $data->option = array((int) $data->option => 1);
        }

        // register another user
        if (!empty($this->userid)) {
            $userid = $this->userid;
        }

        // clean old registration
        $aid = $this->_customdata['alternative']->id;
        if ($this->_customdata['alternative']->teammax > 1) {
            $DB->delete_records('alternative_registration', array('alternativeid' => $aid, 'teamleaderid' => $userid));
        } else {
            $DB->delete_records('alternative_registration', array('alternativeid' => $aid, 'userid' => $userid));
        }

        $ok = true;
        $regEmpty = array(
            'alternativeid' => $aid,
            'userid' => $userid, 'teamleaderid' => null, 'timemodified' => time()
        );
        if ($this->_customdata['alternative']->teammax > 1) {
            $regEmpty['teamleaderid'] = $userid;
        }
        foreach ($data->option as $id => $val) {
            $id = (int) $id;
            $reg = $regEmpty;
            if ($id) {
                $reg['optionid'] = $id;
                if ($this->_customdata['alternative']->teammax > 1) {
                    foreach ($this->_form->membersselector->get_selected_users() as $member) {
                        $regMember = $reg;
                        $regMember['userid'] = $member->id;
                        $recordid = $DB->insert_record('alternative_registration', $regMember);
                        if ($recordid) {
                            $this->registrationinfo[] = array(
                                'registrationid' => $recordid,
                                'userid' => $regMember['userid']
                            );
                        }
                        $ok = $ok && $recordid;
                    }
                }
                $recordid = $DB->insert_record('alternative_registration', $reg);
                if ($recordid) {
                    $this->registrationinfo[] = array(
                        'registrationid' => $recordid,
                        'userid' => $reg['userid']
                    );
                }
                $ok = $ok && $recordid;
            }
        }
        if ($ok) {
            $this->user_has_registered = true;
        }
        return $ok;
    }

    /**
     * Validates the user data.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *               or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    function validation($data, $files) {
        /**
         * @todo validate placesavail && occupied
         * @todo validate multiple
         * @todo validate changeallowed
         * @todo how to display error messages on "users selections"?
         */
        $errors = parent::validation($data, $files);

        // register another user
        $context = context_course::instance($this->_customdata['alternative']->course);
        if (has_capability('mod/alternative:forceregistrations', $context)) {
            $usertmp = $this->_form->targetselector->get_selected_user();
            if ($usertmp === null) {
                $errors['fieldset_user'] = get_string('noselectedusers', 'alternative');
            } else {
                $this->userid = $usertmp->id;
                unset($usertmp);
            }
        }

        // validate team size
        if ($this->_customdata['alternative']->teammax > 1) {
            $users = $this->_form->membersselector->get_selected_users();
            $size = count($users) + 1;
            if (
                $size < $this->_customdata['alternative']->teammin
                || $size > $this->_customdata['alternative']->teammax
            ) {
                $errors['fieldset_team'] = get_string('wrongteamsize', 'alternative');
            }
            if (!empty($this->userid)) {
                foreach ($users as $user) {
                    if ($this->userid == $user->id) {
                        $errors['fieldset_team'] = get_string('teamleadernotamember', 'alternative');
                    }
                }
            }
        }
        if (isset($errors['fieldset_team'])) {
            // QuickForm won't display errors on header/html fields, so an ugly hack seems necessary
            $position = $this->_form->_elementIndex['fieldset_team'] + 1;
            $this->_form->_elements[$position]->_text .= '<div class="error">' . $errors['fieldset_team'] . "</div>";
            // unset($errors['fieldset_team']);
        }

        // validate options
        if (empty($data['option'])) {
            $options = $this->_customdata['options'];
            $optionid = reset($options)->id;
            $fieldname = $this->_customdata['alternative']->multiplemin ? "option[{$optionid}]" : 'option';
            $errors[$fieldname] = get_string('noselectedoption', 'alternative');
        } else {
            $ids = array_keys($this->_customdata['options']);
            if (is_string($data['option'])) {
                if (!in_array($data['option'], $ids)) {
                    $errors['option'] = "Wrong ID";
                }
            } else {
                foreach ($data['option'] as $optionid => $val) {
                    $fieldname = $this->_customdata['alternative']->multiplemin ? "option[{$optionid}]" : 'option';
                    if (!in_array($optionid, $ids)) {
                        $errors[$fieldname] = "Wrong ID";
                    }
                }
            }
        }
        return $errors;
    }
}
