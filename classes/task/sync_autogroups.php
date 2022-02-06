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

namespace local_sp\task;
use \local_autogroup\domain;

/**
 * Task function to sync autogroups.
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2021 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_autogroups extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('autogrouptask', 'local_sp');
    }

    /**
     * Informs whether this task can be run.
     * @return bool true when this task can be run. false otherwise.
     */
    public function can_run(): bool {
        return parent::can_run() && (bool) \core_component::get_plugin_directory('local', 'autogroup');
    }

    /**
     * Execute the task
     */
    public function execute() {
        global $DB;
        if (get_config('local_autogroup', 'enabled')) {
            // Raise PHP time limit and memory limit to avoid getting memory exhausted error.
            \core_php_time_limit::raise();
            raise_memory_limit(MEMORY_HUGE);

            $courses = get_courses();
            foreach (array_keys($courses) as $courseid) {
                $course = new domain\course($courseid, $DB);
                $enrolledusers = \get_enrolled_users(\context_course::instance($courseid));
                foreach ($enrolledusers as $user) {
                    try {
                        $course->verify_user_group_membership($user, $DB);
                    } catch (\Exception $e) {
                        mtrace("Error while processing user {$user->id} in course {$courseid}: " . $e->getMessage());
                        mtrace($e->getTraceAsString());
                    }
                }
            }
        }
    }
}
