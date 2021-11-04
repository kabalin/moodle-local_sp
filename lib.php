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
 * Hooks and callbacks.
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2021 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extend the navigation with the report item
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param context $context The context of the course
 * @throws coding_exception
 * @throws moodle_exception
 */
function local_sp_extend_navigation_course(\navigation_node $navigation, \stdClass $course, \context $context) {
    if (!has_capability('report/completion:view', $context)) {
        // The user does not have the capability to view the course report.
        return;
    }

    $url = new moodle_url('/report/completion/index.php', ['course'=>$course->id]);
    $navigation->add(
        get_string('coursecompletion'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        null,
        new pix_icon('i/report', '')
    );
}

