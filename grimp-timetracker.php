<?php
/*
Plugin Name: Grimp-TimeTracker
Plugin URI: http://git.grimp.eu/
Description: This plugin will allow you to keep tracking of the time you invest over different projects.
Version: 0.3
Author: Fabio Alessandro Locati
Author URI: http://grimp.eu
License: GPL2
*/

include_once 'grimp-timetracker_setup.php';
grimp_timetracker_setup();

add_action('admin_menu', 'grimp_timetracker_menu');

function grimp_timetracker_menu() {
  add_menu_page(__('Time Tracker','grimp-timetracker'), __('Time Tracker','grimp-timetracker'), 'read', 'grimp-timetracker-options', 'grimp_timetracker_options');
  add_submenu_page( 'grimp-timetracker-options', __('Add Project','grimp-timetracker'), __('Add Project','grimp-timetracker'), 'manage_options', 'grimp-timetracker-project', 'grimp_timetracker_project');
  add_submenu_page( 'grimp-timetracker-options', __('Add Type','grimp-timetracker'), __('Add Type','grimp-timetracker'), 'manage_options', 'grimp-timetracker-type', 'grimp_timetracker_type');
  add_submenu_page( 'grimp-timetracker-options', __('Add Hours','grimp-timetracker'), __('Add Hours','grimp-timetracker'), 'read', 'grimp-timetracker-hour', 'grimp_timetracker_hour');
}

function grimp_timetracker_options() {
  if (!current_user_can('read'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  include_once("grimp-timetracker-dashboard.php");  

  echo "<div class=\"wrap\">";

  if (isset($_GET['p'])) {
    grimp_timetracker_widget_five(array("Progetto","projects","project","p"),$_GET['p']);
  }
  
  if (isset($_GET['u'])) {
    grimp_timetracker_widget_five(array("Ore fatte da","users","user","u"),$_GET['u']);
  }

  if (!isset($_GET['p']) && !isset($_GET['u'])) {
    grimp_timetracker_widget_two(array("Progetti","projects","project","p"));
    grimp_timetracker_widget_two(array("Persone","users","person","u"));
    grimp_timetracker_widget_two(array("Tipi di Ora","types","type","t"));
  }

  echo "</div>";
}

function grimp_timetracker_project() {
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  global $wpdb;
  $table_projects = $wpdb->prefix . "timetracker_projects";
  
  if (isset($_GET['p']))
    $i = $_GET['p'];

  if (isset($_POST['submitted']) and $_POST['submitted'] == 'yes') {
    if (isset($i))
  		$wpdb->update($table_projects, array('name'=>$_POST['name']), array('id'=>$_POST['id']), array('%s'), array('%s'));
  	else
  		$wpdb->insert($table_projects, array('name'=>$_POST['name']), array('%s'));
		echo '<div id="message" class="updated">';
    if (isset($i))
      echo '  <p>Project has been changed.</p>';
    else
  		echo '  <p>Project has been added.</p>';
		echo '</div>';
	}

  if (isset($i))
    $p = $wpdb->get_row("SELECT * FROM $table_projects WHERE ID = $i");

  $t1 = (isset($i)) ? "<h2>Edit project $p->name:</h2>" : "<h2>Add project:</h2>";
  $t2 = (isset($i)) ? $p->name : "" ;
  $t3 = (isset($i)) ? "<td><input name='id' id='id' value='$p->ID' class='hidden' type='text'/></td>" : "";
  echo "
<div class='wrap'>
  $t1
  <form method='post' name='update_form' target='_self'>
    <table class='form-table'>
      <tbody>
        <tr>
          <th><label for='name'>Name: </label></th>
          <td><input name='name' id='name' value='$t2' class='regular-text' type='text'/></td>
          $t3
        </tr>
      </tbody>
    </table>
    <p class='submit' id='jump_submit'>
      <input name='submitted' type='hidden' value='yes' />
      <input type='submit' value='Submit' class='button-primary' />
    </p>
  </form>
</div>";
}

function grimp_timetracker_type() {
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  global $wpdb;
  $table_types = $wpdb->prefix . "timetracker_types";
  
  if (isset($_GET['t']))
    $i = $_GET['t'];

  if (isset($_POST['submitted']) and $_POST['submitted'] == 'yes') {
    if (isset($i))
  		$wpdb->update($table_types, array('name'=>$_POST['name']), array('id'=>$_POST['id']), array('%s'), array('%s'));
  	else
  		$wpdb->insert($table_types, array('name'=>$_POST['name']), array('%s'));
		echo '<div id="message" class="updated">';
    if (isset($i))
      echo '  <p>Type has been changed.</p>';
    else
  		echo '  <p>Type has been added.</p>';
		echo '</div>';
	}

  if (isset($i))
    $t = $wpdb->get_row("SELECT * FROM $table_types WHERE ID = $i");

  $t1 = (isset($i)) ? "<h2>Edit type $t->name:</h2>" : "<h2>Add type:</h2>";
  $t2 = (isset($i)) ? $t->name : "" ;
  $t3 = (isset($i)) ? "<td><input name='id' id='id' value='$t->ID' class='hidden' type='text'/></td>" : "";
  echo "
<div class='wrap'>
  $t1
  <form method='post' name='update_form' target='_self'>
    <table class='form-table'>
      <tbody>
        <tr>
          <th><label for='name'>Name: </label></th>
          <td><input name='name' id='name' value='$t2' class='regular-text' type='text'/></td>
          $t3
        </tr>
      </tbody>
    </table>
    <p class='submit' id='jump_submit'>
      <input name='submitted' type='hidden' value='yes' />
      <input type='submit' value='Submit' class='button-primary' />
    </p>
  </form>
</div>";
}

function grimp_timetracker_select($arrays,$val="") {
  $o = "";
  foreach($arrays as $a => $array) {
    $o.= "<option id='type' value='$array->ID'";
    if($val == $array->ID)
      $o.= "selected";
    $o.=">$array->name</option>";
  }
  return $o;
}

function grimp_timetracker_hour() {
  if (!current_user_can('read'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  global $wpdb;
  global $user_ID;
  get_currentuserinfo();

  $table_hours = $wpdb->prefix . "timetracker_hours";
  $table_projects = $wpdb->prefix . "timetracker_projects";
  $table_types = $wpdb->prefix . "timetracker_types";

  if (isset($_GET['h']))
    $i = $_GET['h'];

  if(isset($_POST['submitted']) and $_POST['submitted'] == 'yes') {
    if (isset($i))
  		$wpdb->update( $table_hours, array( 'project' => $_POST['project'], 'hours' => $_POST['hours'], 'type' => $_POST['type'], 'description' => $_POST['description'], 'day' => $_POST['day'] ), array( 'id' => $_POST['id']), array( '%s', '%s', '%s', '%s', '%s' ), array( '%s' ) );
    else
      $wpdb->insert( $table_hours, array( 'person' => $user_ID, 'project' => $_POST['project'], 'hours' => $_POST['hours'], 'type' => $_POST['type'], 'description' => $_POST['description'], 'day' => $_POST['day'] ), array( '%s', '%s', '%s', '%s', '%s', '%s' ) );
		echo '<div id="message" class="updated">';
    if (isset($i))
      echo '  <p>Hours have been changed.</p>';
    else
  		echo '  <p>Hours have been added.</p>';
		echo '</div>';
  }

  if (isset($i))
    $h = $wpdb->get_row("SELECT * FROM $table_hours WHERE ID = $i");

  $ids = $wpdb->get_col("SELECT ID FROM $table_projects");
  foreach($ids as $c => $id)
    $projects[] = $wpdb->get_row("SELECT * FROM $table_projects WHERE ID = $id");

  $ids = $wpdb->get_col("SELECT ID FROM $table_types");
  foreach($ids as $c => $id)
    $types[] = $wpdb->get_row("SELECT * FROM $table_types WHERE ID = $id");

  $t1 = (isset($i)) ? "<h2>Edit hours of $h->person:</h2>" : "<h2>Add hours:</h2>";
  $t2 = (isset($i)) ? grimp_timetracker_select($projects,$h->project) : grimp_timetracker_select($projects) ;
  $t3 = (isset($i)) ? $h->hours : "1.00" ;
  $t4 = (isset($i)) ? grimp_timetracker_select($types,$h->type) : grimp_timetracker_select($types) ;
  $t5 = (isset($i)) ? $h->description : "" ;
  $t6 = (isset($i)) ? $h->day : date('Y-m-d') ;
  $t7 = (isset($i)) ? "<td><input name='id' id='id' value='$h->ID' class='hidden' type='text'/></td>" : "";

echo "
<div class='wrap'>
  <h2>$t1</h2>
  <form method='post' name='update_form' target='_self'>
    <table class='form-table'>
      <tbody>
      <tr>
        <th><label for='project'>Project</label></th>
        <td><select name='project' id='project'>$t2</select></td>
      </tr>
      <tr>
        <th><label for='hours'>Hours</label></th>
        <td><input name='hours' id='hours' value='$t3' class='regular-text' type='text'/></td>
      </tr>
      <tr>
        <th><label for='type'>Type</label></th>
        <td><select name='type' id='type'>$t4</select></td>
      </tr>
      <tr>
        <th><label for='description'>Description</label></th>
        <td><input name='description' id='description' value='$t5' class='regular-text' type='text'/></td>
      </tr>
      <tr>
        <th><label for='day'>Day</label></th>
        <td><input name='day' id='day' value='$t6' class='regular-text' type='text'/></td>
        $t7
      </tr>
      <tbody>
    </table>
    <p class='submit' id='jump_submit'>
      <input name='submitted' type='hidden' value='yes' />
      <input type='submit' value='Submit' class='button-primary' />
    </p>
  </form>
</div>";
}
?>
