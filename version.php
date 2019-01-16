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
 * Defines the version of alternative
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012-2013 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2017020100;      // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2014051200;      // Requires this Moodle version
$plugin->cron      = 0;               // Period for cron to check this module (secs)
$plugin->component = 'mod_alternative'; // To check on upgrade, that module sits in correct place
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.3.2 (Build 2019011600)';

$plugin->dependencies = array();
