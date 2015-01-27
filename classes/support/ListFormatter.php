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

namespace mod_alternative\support;

defined('MOODLE_INTERNAL') || die();

/**
 * List formatter
 *
 * @package    mod
 * @subpackage alternative
 * @author     Eric VILLARD <dev@eviweb.fr>
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class ListFormatter
{
    /**
     * list items
     *
     * @var array
     */
    private $items;

    /**
     * construtor
     *
     * @param array $items list items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * get a comma separated list of given items
     *
     * @return string
     */
    public function getCommaSeparatedList()
    {
        return join(', ', $this->items);
    }

    /**
     * get a plain text list of given items
     *
     * @return string
     */
    public function getPlainTextList()
    {
        $list = '';
        foreach ($this->items as $item) {
            $list .= "\t- ".$item."\n";
        }

        return $list;
    }

    /**
     * get an unordered html list of given items
     *
     * @return string
     */
    public function getUnorderedHTMLList()
    {
        return '<ul>'.$this->getHTMLListItems().'</ul>';
    }

    /**
     * get an ordered html list of given items
     *
     * @return string
     */
    public function getOrderedHTMLList()
    {
        return '<ol>'.$this->getHTMLListItems().'</ol>';
    }

    /**
     * get the list of items in HTML format
     *
     * @return string
     */
    private function getHTMLListItems()
    {
        $items = '';
        foreach ($this->items as $item) {
            $items .= '<li>'.$item.'</li>';
        }

        return $items;
    }
}