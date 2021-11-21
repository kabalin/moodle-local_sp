<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tasks definition.
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2021 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$tasks = [
    [
        'classname' => 'local_sp\task\sync_system_roles',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '3',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false
    ],
    [
        'classname' => 'local_sp\task\sync_autogroups',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '1',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
        'disabled' => false
    ],
];
