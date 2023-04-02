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

namespace local_sp\external;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;

/**
 * local_sp external class local_sp_get_course_completion_status
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2023 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_sp_get_course_completion_status extends external_api {

    /**
     * Course completion status parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course id', VALUE_REQUIRED),
                'completedonly' => new external_value(PARAM_BOOL, 'List only users who completed the course', VALUE_DEFAULT, true),
            ]
        );
    }

    /**
     * Course completion status
     *
     * @param int $courseid
     * @param bool $completedonly
     * @return array
     */
    public static function execute(int $courseid, bool $completedonly): array {
        global $DB, $USER;
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'completedonly' => $completedonly,
        ]);

        // Check that the course exists.
        if ($params['courseid']) {
            $course = $DB->get_record('course', ['id' => $params['courseid']], '*');
        }

        if (!$course) {
            throw new \invalid_parameter_exception(print_error('invalidcourse'));
        }

        // Validate context.
        $context = \context_course::instance($course->id);
        self::validate_context($context);

        // Permission validation.
        if (!completion_can_view_data($USER->id, $course)) {
            throw new \moodle_exception('cannotviewreport');
        }

        // Query database directly, we can't use completion API, too expensive for 10k+ completion records.
        $where = $params['completedonly'] ? ' AND crc.timecompleted IS NOT NULL ' : '';
        $sql = "SELECT u.id AS userid, crc.timecompleted AS timecompleted
                  FROM {user} u
            INNER JOIN {user_enrolments} ue ON ue.userid = u.id
            INNER JOIN {enrol} e ON e.id = ue.enrolid
            INNER JOIN {course} c ON c.id = e.courseid
             LEFT JOIN {course_completions} crc ON crc.course = c.id AND crc.userid = u.id
                 WHERE c.enablecompletion = 1
                   AND c.id = ?
                   AND ue.status = 0
                   AND e.status = 0
                   AND u.deleted = 0
                   AND ue.timestart < ?
                   AND (ue.timeend > ? OR ue.timeend = 0)
                   {$where}
              ORDER BY userid";
        $now = time();
        $completions = $DB->get_recordset_sql($sql, [$course->id, $now, $now]);

        // Record result.
        $result = [];
        foreach ($completions as $completion) {
            $result[] = ['userid' => $completion->userid, 'completed' => (bool) $completion->timecompleted];
        }
        return $result;
    }

    /**
     * Return for getting completion status.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'userid'        => new external_value(PARAM_INT, 'User ID'),
                    'completed'     => new external_value(PARAM_BOOL, 'true if the course is completed, false otherwise'),
                ], 'Course completion status for active enrolments', VALUE_DEFAULT, []
            )
        );
    }
}
