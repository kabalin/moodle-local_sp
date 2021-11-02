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

namespace local_sp\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for local_sp.
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2021 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class observer {

    /**
     * User enrolment creation event callback.
     *
     * @param  \core\event\user_enrolment_created $event
     * @return void
     */
    public static function user_enrolment_created(\core\event\user_enrolment_created $event) {
        $coursesids = explode(',', get_config('local_sp', 'autocompletioncourses'));
        if (in_array($event->courseid, $coursesids) && $event->other['enrol'] === 'coursecompleted') {
            // One of the courses we enabled in settings and enrolment method is 'completion'.
            $params = [
                'userid'    => $event->relateduserid,
                'course'    => $event->courseid,
            ];
            $ccompletion = new \completion_completion($params);
            // Only mark completed if not already completed (i.e. via other enrolment).
            if (!$ccompletion->is_complete()) {
                // Use enrolment time as completion time.
                $ccompletion->mark_complete($event->timecreated);
            }
        }
    }
}
