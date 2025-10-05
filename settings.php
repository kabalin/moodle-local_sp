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
 * Plugin settings.
 *
 * @package   local_sp
 * @author    Ruslan Kabalin <ruslan.kabalin@gmail.com>
 * @copyright 2021 Swiss Post Ltd {@link https://www.post.ch}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_sp', new lang_string('defaultsettings', 'local_sp'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading('local_sp/autocompletion',
                new lang_string('autocompletion', 'local_sp'), ''));

    $courses = core_course_category::search_courses(['onlywithcompletion' => true]);
    $options = array_combine(array_column($courses, 'id'), array_column($courses, 'fullname'));
    $settings->add(new admin_setting_configmultiselect('local_sp/autocompletioncourses',
                new lang_string('autocompletioncourses', 'local_sp'),
                new lang_string('autocompletioncourses_desc', 'local_sp'), [], $options));

    if (has_capability('moodle/role:assign', context_system::instance())) {
        $settings->add(new admin_setting_heading('local_sp/systemrolesync',
                    new lang_string('systemrolesync', 'local_sp'), ''));

        $courses = core_course_category::search_courses(['onlywithcompletion' => true]);
        $options = array_combine(array_column($courses, 'id'), array_column($courses, 'fullname'));
        $options = [0 => get_string('none')] + $options;
        $settings->add(new admin_setting_configselect('local_sp/systemrolesynccourse',
                    new lang_string('course'),
                    new lang_string('systemrolesynccourse_desc', 'local_sp'), 0, $options));

        $roles = get_roles_for_contextlevels(CONTEXT_COURSE);
        $options = role_fix_names(array_flip($roles), null, ROLENAME_ALIAS, true);
        $settings->add(new admin_setting_configselect('local_sp/systemrolesynccourserole',
                    new lang_string('systemrolesynccourserole', 'local_sp'),
                    new lang_string('systemrolesynccourserole_desc', 'local_sp'), '', $options));

        $roles = get_roles_for_contextlevels(CONTEXT_SYSTEM);
        $options = role_fix_names(array_flip($roles), null, ROLENAME_ALIAS, true);
        $settings->add(new admin_setting_configselect('local_sp/systemrolesynctargetrole',
                    new lang_string('systemrolesynctargetrole', 'local_sp'),
                    new lang_string('systemrolesynctargetrole_desc', 'local_sp'), '', $options));
    }

    if (\core_component::get_plugin_directory('local', 'autogroup')) {
        $settings->add(new admin_setting_heading('local_sp/autogrouptask',
            new lang_string('autogrouptask', 'local_sp'), ''));
        $url = new moodle_url('/admin/tool/task/scheduledtasks.php', ['action' => 'edit', 'task' => 'local_sp\\task\\sync_autogroups']);
        $settings->add(new admin_setting_description('local_sp/autogrouptaskdesc',
            '', new lang_string('autogrouptaskdesc', 'local_sp', $url->out())));
    }
}
