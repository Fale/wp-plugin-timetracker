<?php

function grimp_timetracker_widget_five($t, $d) {
  if (!current_user_can('read'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  
  global $wpdb;

  $table_hours = $wpdb->prefix . "timetracker_hours";
  $table_projects = $wpdb->prefix . "timetracker_projects";
  $table_types = $wpdb->prefix . "timetracker_types";
  $table_users = $wpdb->users;

  $ids = $wpdb->get_col("SELECT ID FROM $table_projects");
  foreach($ids as $i => $id)
    $projects[] = $wpdb->get_row("SELECT * FROM $table_projects WHERE ID = $id");

  $ids = $wpdb->get_col("SELECT ID FROM $table_types");
  foreach($ids as $i => $id)
    $types[] = $wpdb->get_row("SELECT * FROM $table_types WHERE ID = $id");

  if( $t[1] == "projects" ) {
    $var = $wpdb->get_var("SELECT name FROM $table_projects WHERE ID = $d");
    $c1 = "Persona";
    $c3 = "Tipo";
  }
  if( $t[1] == "users" ) {
    $var = $wpdb->get_var("SELECT ID FROM $table_users WHERE ID = $d");
    $var = get_userdata($var)->display_name;
    $c1 = "Progetto";
    $c3 = "Tipo";
  }
  if( $t[1] == "types" ) {
    $var = $wpdb->get_var("SELECT name FROM $table_types WHERE ID = $d");
    $c1 = "Persona";
    $c3 = "Progetto";
  }
  echo "
  <h2>$t[0] $var<a href=" . strstr($_SERVER['REQUEST_URI'], "&", true) . " class=\"button\">Back</a></h2>
  <table class=\"widefat\">
    <thead>
     <tr>
        <th>$c1</th>
        <th>Ore</th>
        <th>$c3</th>
        <th>Descrizione</th>
        <th>Giorno</th>
      </tr>
    </thead>
    <tbody>";
  if ( $t[1] == "projects" ) {
    $ids = $wpdb->get_col("SELECT ID FROM $table_hours WHERE project = $d ORDER BY day ASC");
    foreach($ids as $i => $id)
      $hours[] = $wpdb->get_row("SELECT * FROM $table_hours WHERE ID = $id");
    foreach($hours as $h => $hour) {
      echo"
      <tr>
        <td>
          " . get_userdata($hour->person)->display_name . "
          <div class=\"row-actions no-wrap\">
            <a href=\"" . strstr($_SERVER['REQUEST_URI'], "?", true) . "?page=grimp-timetracker-hour&h=$hour->ID\">Edit</a>
          </div>
        </td>
        <td>$hour->hours</td>
        <td>" . $types[$hour->type-1]->name . "</td>
        <td>$hour->description</td>
        <td>$hour->day</td>
      </tr>";
    }
    unset ($hours);
  }
  if ( $t[1] == "users" ) {
    $ids = $wpdb->get_col("SELECT ID FROM $table_hours WHERE person = $d ORDER BY day ASC");
    foreach($ids as $i => $id)
      $hours[] = $wpdb->get_row("SELECT * FROM $table_hours WHERE ID = $id");
      foreach($hours as $h => $hour) {
        echo"
        <tr>
          <td>
            " . $projects[$hour->project-1]->name . "
            <div class=\"row-actions no-wrap\">
              <a href=\"" . strstr($_SERVER['REQUEST_URI'], "?", true) . "?page=grimp-timetracker-hour&h=$hour->ID\">Edit</a>
            </div>
          </td>
          <td>$hour->hours</td>
          <td>" . $types[$hour->type-1]->name . "</td>
          <td>$hour->description</td>
          <td>$hour->day</td>
        </tr>";
      }
    unset ($hours);
  }
  if ( $t[1] == "types" ) {
    $ids = $wpdb->get_col("SELECT ID FROM $table_hours WHERE type = $d ORDER BY day ASC");
    foreach($ids as $i => $id)
      $hours[] = $wpdb->get_row("SELECT * FROM $table_hours WHERE ID = $id");
      foreach($hours as $h => $hour) {
        echo"
        <tr>
          <td>
            " . get_userdata($hour->person)->display_name . "
            <div class=\"row-actions no-wrap\">
              <a href=\"" . strstr($_SERVER['REQUEST_URI'], "?", true) . "?page=grimp-timetracker-hour&h=$hour->ID\">Edit</a>
            </div>
          </td>
          <td>$hour->hours</td>
          <td>" . $projects[$hour->project-1]->name . "</td>
          <td>$hour->description</td>
          <td>$hour->day</td>
        </tr>";
    }
    unset ($hours);
  }
  echo"
    </tbody>
    <tfoot>
     <tr>
        <th>$c1</th>
        <th>Ore</th>
        <th>$c3</th>
        <th>Descrizione</th>
        <th>Giorno</th>
      </tr>
    </tfoot>
  </table>";
}

function grimp_timetracker_widget_two($t) {

  global $wpdb;

  $table_hours = $wpdb->prefix . "timetracker_hours";

  if ($t[1] != "users")
    $table_var = $wpdb->prefix . "timetracker_" . $t[1];
  else
    $table_var = $wpdb->users;

  $ids = $wpdb->get_col("SELECT ID FROM $table_var");
  foreach($ids as $i => $id)
    $vars[] = $wpdb->get_row("SELECT * FROM $table_var WHERE ID = $id");
  
  echo "
  <h2>$t[0]</h2>
  <table class=\"widefat\">
    <thead>
      <tr>
        <th>$t[0]</th>
        <th>Ore</th>
      </tr>
    </thead>
    <tbody>";
  foreach($vars as $v => $var) {
    $h = $wpdb->get_var("SELECT SUM(hours) FROM $table_hours WHERE $t[2] = $var->ID");
    if ($t[1] != "users") {
      echo"
          <tr>
            <td>
              <a href=\"$_SERVER[REQUEST_URI]&$t[3]=$var->ID\">$var->name</a>
              <div class=\"row-actions no-wrap\">
                <a href=\"" . strstr($_SERVER['REQUEST_URI'], "?", true) . "?page=grimp-timetracker-$t[2]&$t[3]=$var->ID\">Edit</a>
              </div>
            </td>
            <td>$h</td>
          </tr>";
    } else {
      if ($h) {
        echo"
            <tr>
              <td><a href=\"$_SERVER[REQUEST_URI]&u=$var->ID\">" . get_userdata($var->ID)->display_name . "</a></td>
              <td>$h</td>
            </tr>";
      }
    }
  }
  echo"
      </tbody>
      <tfoot>
       <tr>
        <th>$t[0]</th>
        <th>Ore</th>
      </tr>
    </tfoot>
  </table>
  <br />";
}
?>
