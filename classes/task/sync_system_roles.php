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

/**
 * Task function to sync system roles.
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2021 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_system_roles extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('systemrolesync', 'local_sp');
    }

    /**
     * Execute the task
     */
    public function execute() {
        global $DB;
        $courseid = get_config('local_sp', 'systemrolesynccourse');
        $courseroleid = get_config('local_sp', 'systemrolesynccourserole');
        $systemroleid = get_config('local_sp', 'systemrolesynctargetrole');

        $courseexists = $DB->record_exists('course', ['id' => $courseid]);
        $systemroleexists = in_array(CONTEXT_SYSTEM, get_role_contextlevels($systemroleid));
        if ($courseexists && $systemroleexists) {
            $context = \context_course::instance($courseid);
            $syscontext = \context_system::instance()->id;

            $users = $DB->get_fieldset_sql("SELECT userid FROM {role_assignments}
                WHERE contextid = ? AND roleid = ?", [$context->id, $courseroleid]);
            foreach ($users as $userid) {
                $user = \core_user::get_user($userid);
                if (empty($user->confirmed)) {
                    // Confirm user.
                    $auth = get_auth_plugin($user->auth);
                    $auth->user_confirm($user->username, $user->secret);
                }
                if (!user_has_role_assignment($userid, $systemroleid, $syscontext)) {
                    role_assign($systemroleid, $userid, $syscontext);
                }
            }
        }
    }
}
