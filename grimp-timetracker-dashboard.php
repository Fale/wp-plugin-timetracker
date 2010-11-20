<?php

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
                <a href=\"$_SERVER[REQUEST_URI]?page=grimp-timetracker-$t[2]&$t[3]=$var->ID\">Edit</a>
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
