<?php
/*
Plugin Name: Grimp-TimeTracker
Plugin URI: http://git.grimp.eu/
Description: This plugin will allow you to keep tracking of the time you invest over different projects.
Version: 0.1
Author: Fabio Alessandro Locati
Author URI: http://grimp.eu
License: GPL2
*/

include_once 'grimp-timetracker_setup.php';
grimp_timetracker_setup();

add_action('admin_menu', 'grimp_timetracker_menu');

function grimp_timetracker_menu() {
  add_menu_page(__('Timetracker','grimp-timetracker'), __('Timetracker','grimp-timetracker'), 'read', 'grimp-timetracker-options', 'grimp_timetracker_options');
  add_submenu_page( 'grimp-timetracker-options', __('Add Project','grimp-timetracker'), __('Add Project','grimp-timetracker'), 'manage_options', 'grimp-timetracker-add-project', 'grimp_timetracker_add_project');
  add_submenu_page( 'grimp-timetracker-options', __('Add Hours','grimp-timetracker'), __('Add Hours','grimp-timetracker'), 'read', 'grimp-timetracker-add-hour', 'grimp_timetracker_add_hour');
}

function grimp_timetracker_options() {
  if (!current_user_can('read'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  
  global $wpdb;
  echo '<p>Here is where the form would go if I actually had options.</p>';
}

function grimp_timetracker_add_project() {
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  global $wpdb;
  
  if(isset($_POST['submitted']) and $_POST['submitted'] == 'yes') {
    $table_name = $wpdb->prefix . "timetracker_projects";
		$wpdb->insert( $table_name, array( 'id' => '', 'name' => $_POST['name'] ), array( '%i', '%s' ) );
	}

  $o = '<div class="wrap">';
  $o.= '  <h2>Add a new project:</h2>';
  $o.= '  <form method="post" name="update_form" target="_self">';
  $o.= '    Name <input type="text" name="name" value="" size="30" />';
  $o.= '    <p class="submit" id="jump_submit">';
  $o.= '      <input name="submitted" type="hidden" value="yes" />';
  $o.= '      <input type="submit" name="Submit" value="Save Changes" />';
  $o.= '    </p>';
  $o.= '  </form>';
  $o.= '</div>';
  
  echo $o;
}

function grimp_timetracker_add_hour() {
  if (!current_user_can('read'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  global $wpdb;
  global $user_ID;
  get_currentuserinfo();

  $table_projects = $wpdb->prefix . "timetracker_projects";
  $table_hours = $wpdb->prefix . "timetracker_hours";

  if(isset($_POST['submitted']) and $_POST['submitted'] == 'yes')
		$wpdb->insert( $table_projects, array( 'id' => '', 'person' => $user_ID, 'project' => $_POST['project'], 'hours' => $_POST['hours'], 'description' => $_POST['description'], 'day' => $_POST['day'] ), array( '%i', '%s', '%s', '%s', '%s', '%s' ) );
  
  $ids = $wpdb->get_col("SELECT id FROM $table_projects");
  foreach($ids as $i => $id)
    $projects[] = $wpdb->get_row("SELECT * FROM $table_projects WHERE id = $id");

  $o = '<div class="wrap">';
  $o.= '  <h2>Add a new hours:</h2>';
  $o.= '  <form method="post" name="update_form" target="_self">';
  $o.= '    <table>';
  $o.= '      <tr>';
  $o.= '        <th>Project</th>';
  $o.= '        <td><select name="project">';
  foreach($projects as $c => $project)
    $o.= '          <option value="' . $project->name . '">' . $project->name . '</option>';
  $o.= '        </select></td>';
  $o.= '      </tr>';
  $o.= '      <tr>';
  $o.= '        <th>Hours</th>';
  $o.= '        <td><input type="text" name="hours" value="1.00" size="30" /></td>';
  $o.= '      </tr>';
  $o.= '      <tr>';
  $o.= '        <th>Description</th>';
  $o.= '        <td><input type="text" name="description" value="" size="30" /></td>';
  $o.= '      </tr>';
  $o.= '      <tr>';
  $o.= '        <th>Day</th>';
  $o.= '        <td><input type="text" name="day" value="' . date('Y-m-d') . '" size="30" /></td>';
  $o.= '      </tr>';
  $o.= '    </table>';
  $o.= '    <p class="submit" id="jump_submit">';
  $o.= '      <input name="submitted" type="hidden" value="yes" />';
  $o.= '      <input type="submit" name="Submit" value="Save Changes" />';
  $o.= '    </p>';
  $o.= '  </form>';
  $o.= '</div>';
  
  echo $o;
}
?>
