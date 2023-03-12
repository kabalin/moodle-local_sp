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
 * local_sp external functions and service definitions.
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2023 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$functions = [
    'local_sp_get_course_completion_status' => [
        'classname' => \local_sp\external\local_sp_get_course_completion_status::class,
        'methodname' => 'execute',
        'description' => 'Get course completion status for all enrolled users',
        'type' => 'read',
        'capabilities' => 'report/completion:view',
    ],
];
