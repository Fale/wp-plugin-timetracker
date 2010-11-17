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
  add_submenu_page( 'grimp-timetracker-options', __('Add Type','grimp-timetracker'), __('Add Type','grimp-timetracker'), 'manage_options', 'grimp-timetracker-add-type', 'grimp_timetracker_add_type');
  add_submenu_page( 'grimp-timetracker-options', __('Add Hours','grimp-timetracker'), __('Add Hours','grimp-timetracker'), 'read', 'grimp-timetracker-add-hour', 'grimp_timetracker_add_hour');
}

function grimp_timetracker_options() {
  if (!current_user_can('read'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  
  global $wpdb;

  $table_hours = $wpdb->prefix . "timetracker_hours";
  $table_projects = $wpdb->prefix . "timetracker_projects";
  $table_types = $wpdb->prefix . "timetracker_types";

  $ids = $wpdb->get_col("SELECT id FROM $table_projects");
  foreach($ids as $i => $id)
    $projects[] = $wpdb->get_row("SELECT * FROM $table_projects WHERE id = $id");

  $ids = $wpdb->get_col("SELECT id FROM $table_types");
  foreach($ids as $i => $id)
    $types[] = $wpdb->get_row("SELECT * FROM $table_types WHERE id = $id");

  $o = '<div class="wrap">';
  if ($_GET['p']) {
    $p = $wpdb->get_var("SELECT name FROM $table_projects WHERE id = $_GET[p]");
    $o.= '<h2>Progetto ' . $p . ' <a href="admin.php?page=grimp-timetracker-options" class="button">Back</a> </h2>';
    $o.= '<table class="widefat">';
    $o.= '  <thead>';
    $o.= '   <tr>';
    $o.= '      <th>Persona</th>';
    $o.= '      <th>Ore</th>';
    $o.= '      <th>Tipo</th>';
    $o.= '      <th>Descrizione</th>';
    $o.= '      <th>Giorno</th>';
    $o.= '    </tr>';
    $o.= '  </thead>';
    $o.= '  <tbody>';
    $ids = $wpdb->get_col("SELECT id FROM $table_hours WHERE project = $_GET[p]");
    foreach($ids as $i => $id)
      $hours[] = $wpdb->get_row("SELECT * FROM $table_hours WHERE id = $id");
      foreach($hours as $h => $hour) {
        $o.= '    <tr>';
        $o.= '      <td>' . get_userdata($hour->person)->display_name . '</td>';
        $o.= '      <td>' . $hour->hours . '</td>';
        $o.= '      <td>' . $types[$hour->type]->name . '</td>';
        $o.= '      <td>' . $hour->description . '</td>';
        $o.= '      <td>' . $hour->day . '</td>';
        $o.= '    </tr>';
      }
    unset ($hours);
    $o.= '  </tbody>';
    $o.= '  <tfoot>';
    $o.= '   <tr>';
    $o.= '      <th>Persona</th>';
    $o.= '      <th>Ore</th>';
    $o.= '      <th>Tipo</th>';
    $o.= '      <th>Descrizione</th>';
    $o.= '      <th>Giorno</th>';
    $o.= '    </tr>';
    $o.= '  </tfoot>';
    $o.= '</table>';
  } else {
    $o.= '<h2>Progetti</h2>';
    $o.= '<table class="widefat">';
    $o.= '  <thead>';
    $o.= '   <tr>';
    $o.= '      <th>Progetto</th>';
    $o.= '      <th>Ore</th>';
    $o.= '    </tr>';
    $o.= '  </thead>';
    $o.= '  <tbody>';
    foreach($projects as $p => $pr) {
      $h = $wpdb->get_var("SELECT SUM(hours) FROM $table_hours WHERE project = $pr->id");
      $o.= '    <tr>';
      $o.= '      <td><a href="admin.php?page=grimp-timetracker-options&p=' . $pr->id . '">' . $pr->name . '</a></td>';
      $o.= '      <td>' . $h . '</td>';
      $o.= '    </tr>';
    }
    $o.= '  </tbody>';
    $o.= '  <tfoot>';
    $o.= '   <tr>';
    $o.= '      <th>Persona</th>';
    $o.= '      <th>Ore</th>';
    $o.= '    </tr>';
    $o.= '  </tfoot>';
    $o.= '</table>';
  }
  $o.= '</div>';

  $o.= '<div class="wrap">';
  $o.= '</div>';

  echo $o;  
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

function grimp_timetracker_add_type() {
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  global $wpdb;
  
  if(isset($_POST['submitted']) and $_POST['submitted'] == 'yes') {
    $table_name = $wpdb->prefix . "timetracker_types";
		$wpdb->insert( $table_name, array( 'id' => '', 'name' => $_POST['name'] ), array( '%i', '%s' ) );
	}

  $o = '<div class="wrap">';
  $o.= '  <h2>Add a new type of hours:</h2>';
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

  $table_hours = $wpdb->prefix . "timetracker_hours";
  $table_projects = $wpdb->prefix . "timetracker_projects";
  $table_types = $wpdb->prefix . "timetracker_types";

  if(isset($_POST['submitted']) and $_POST['submitted'] == 'yes')
		$wpdb->insert( $table_hours, array( 'person' => $user_ID, 'project' => $_POST['project'], 'hours' => $_POST['hours'], 'type' => $_POST['type'], 'description' => $_POST['description'], 'day' => $_POST['day'] ), array( '%s', '%s', '%s', '%s', '%s', '%s' ) );

  $ids = $wpdb->get_col("SELECT id FROM $table_projects");
  foreach($ids as $i => $id)
    $projects[] = $wpdb->get_row("SELECT * FROM $table_projects WHERE id = $id");

  $ids = $wpdb->get_col("SELECT id FROM $table_types");
  foreach($ids as $i => $id)
    $types[] = $wpdb->get_row("SELECT * FROM $table_types WHERE id = $id");

  $o = '<div class="wrap">';
  $o.= '  <h2>Add a new hours:</h2>';
  $o.= '  <form method="post" name="update_form" target="_self">';
  $o.= '    <table>';
  $o.= '      <tr>';
  $o.= '        <th>Project</th>';
  $o.= '        <td><select name="project">';
  foreach($projects as $c => $project)
    $o.= '          <option value="' . $project->id . '">' . $project->name . '</option>';
  $o.= '        </select></td>';
  $o.= '      </tr>';
  $o.= '      <tr>';
  $o.= '        <th>Hours</th>';
  $o.= '        <td><input type="text" name="hours" value="1.00" size="30" /></td>';
  $o.= '      </tr>';
  $o.= '      <tr>';
  $o.= '      <tr>';
  $o.= '        <th>Type</th>';
  $o.= '        <td><select name="type">';
  foreach($types as $c => $type)
    $o.= '          <option value="' . $type->id . '">' . $type->name . '</option>';
  $o.= '        </select></td>';
  $o.= '      </tr>';
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
