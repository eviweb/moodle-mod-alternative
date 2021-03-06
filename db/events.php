<?php

// This file is part of the module "alternative" for Moodle.
//
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
 * Events handlers
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = array (
    array (
        'eventname' => '\core\event\group_created',
        'callback'  => '\mod_alternative\observer\group::created',
    ),
    
    array (
        'eventname' => '\core\event\group_updated',
        'callback'  => '\mod_alternative\observer\group::updated',
    ),
    
    array (
        'eventname' => '\core\event\group_deleted',
        'callback'  => '\mod_alternative\observer\group::deleted',
    ),
);
