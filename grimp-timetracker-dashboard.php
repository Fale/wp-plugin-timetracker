<?php

function grimp_timetracker_options() {
  if (!current_user_can('read'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  
  global $wpdb;

  $table_hours = $wpdb->prefix . "timetracker_hours";
  $table_projects = $wpdb->prefix . "timetracker_projects";
  $table_types = $wpdb->prefix . "timetracker_types";
  $table_users = $wpdb->users;

  $ids = $wpdb->get_col("SELECT id FROM $table_projects");
  foreach($ids as $i => $id)
    $projects[] = $wpdb->get_row("SELECT * FROM $table_projects WHERE id = $id");

  $ids = $wpdb->get_col("SELECT id FROM $table_types");
  foreach($ids as $i => $id)
    $types[] = $wpdb->get_row("SELECT * FROM $table_types WHERE id = $id");

  $o = '<div class="wrap">';
  if (isset($_GET['p'])) {
    $p = $wpdb->get_var("SELECT name FROM $table_projects WHERE id = $_GET[p]");
    $o.= '<h2>Progetto ' . $p . ' <a href="' . strstr($_SERVER['REQUEST_URI'], "&", true) . '" class="button">Back</a></h2>';
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
    $ids = $wpdb->get_col("SELECT id FROM $table_hours WHERE project = $_GET[p] ORDER BY day ASC");
    foreach($ids as $i => $id)
      $hours[] = $wpdb->get_row("SELECT * FROM $table_hours WHERE id = $id");
      foreach($hours as $h => $hour) {
        $o.= '    <tr>';
        $o.= '      <td>';
        $o.= get_userdata($hour->person)->display_name;
        $o.= '<div class="row-actions no-wrap">';
        $o.= '<a href="' . strstr($_SERVER['REQUEST_URI'], "?", true) . '?page=grimp-timetracker-hour&h=' . $hour->id . '">Edit</a>';
      $o.= '</div>';
        $o.= '</td>';
        $o.= '      <td>' . $hour->hours . '</td>';
        $o.= '      <td>' . $types[$hour->type-1]->name . '</td>';
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
  }
  
  if (isset($_GET['u'])) {
    $u = $wpdb->get_var("SELECT id FROM $table_users WHERE id = $_GET[u]");
    $o.= '<h2>Ore fatte da ' . get_userdata($u)->display_name . ' <a href="' . strstr($_SERVER['REQUEST_URI'], "&", true) . '" class="button">Back</a></h2>';
    $o.= '<table class="widefat">';
    $o.= '  <thead>';
    $o.= '   <tr>';
    $o.= '      <th>Progetto</th>';
    $o.= '      <th>Ore</th>';
    $o.= '      <th>Tipo</th>';
    $o.= '      <th>Descrizione</th>';
    $o.= '      <th>Giorno</th>';
    $o.= '    </tr>';
    $o.= '  </thead>';
    $o.= '  <tbody>';
    $ids = $wpdb->get_col("SELECT id FROM $table_hours WHERE person = $_GET[u] ORDER BY day ASC");
    foreach($ids as $i => $id)
      $hours[] = $wpdb->get_row("SELECT * FROM $table_hours WHERE id = $id");
      foreach($hours as $h => $hour) {
        $o.= '    <tr>';
        $o.= '      <td>';
        $o.= $projects[$hour->project-1]->name;
        $o.= '<div class="row-actions no-wrap">';
        $o.= '<a href="' . strstr($_SERVER['REQUEST_URI'], "?", true) . '?page=grimp-timetracker-hour&h=' . $hour->id . '">Edit</a>';
      $o.= '</div>';
        $o.= '</td>';
        $o.= '      <td>' . $hour->hours . '</td>';
        $o.= '      <td>' . $types[$hour->type-1]->name . '</td>';
        $o.= '      <td>' . $hour->description . '</td>';
        $o.= '      <td>' . $hour->day . '</td>';
        $o.= '    </tr>';
      }
    unset ($hours);
    $o.= '  </tbody>';
    $o.= '  <tfoot>';
    $o.= '   <tr>';
    $o.= '      <th>Progetto</th>';
    $o.= '      <th>Ore</th>';
    $o.= '      <th>Tipo</th>';
    $o.= '      <th>Descrizione</th>';
    $o.= '      <th>Giorno</th>';
    $o.= '    </tr>';
    $o.= '  </tfoot>';
    $o.= '</table>';
  }

  if (!isset($_GET['p']) && !isset($_GET['u'])) {
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
        $o.= '      <td>';
        $o.= '<a href="' . $_SERVER['REQUEST_URI'] . '&p=' . $pr->id . '">' . $pr->name . '</a>';
        $o.= '<div class="row-actions no-wrap">';
        $o.= '<a href="' . strstr($_SERVER['REQUEST_URI'], "?", true) . '?page=grimp-timetracker-project&p=' . $pr->id . '">Edit</a>';
      $o.= '</div>';
        $o.= '</td>';
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
    $o.= '<br />';
    $o.= '<h2>Persone</h2>';
    $o.= '<table class="widefat">';
    $o.= '  <thead>';
    $o.= '   <tr>';
    $o.= '      <th>Persona</th>';
    $o.= '      <th>Ore</th>';
    $o.= '    </tr>';
    $o.= '  </thead>';
    $o.= '  <tbody>';
    $ids = $wpdb->get_col("SELECT id FROM $table_users");
    foreach($ids as $i => $id) {
      $h = '';
      $h = $wpdb->get_var("SELECT SUM(hours) FROM $table_hours WHERE person = $id");
      if ($h) {
        $o.= '    <tr>';
        $o.= '      <td><a href="' . $_SERVER['REQUEST_URI'] . '&u=' . $id . '">' . get_userdata($id)->display_name . '</a></td>';
        $o.= '      <td>' . $h . '</td>';
        $o.= '    </tr>';
      }
    }
    $o.= '  </tbody>';
    $o.= '  <tfoot>';
    $o.= '   <tr>';
    $o.= '      <th>Persona</th>';
    $o.= '      <th>Ore</th>';
    $o.= '    </tr>';
    $o.= '  </tfoot>';
    $o.= '</table>';
    $o.= '<br />';
    $o.= '<h2>Tipi</h2>';
    $o.= '<table class="widefat">';
    $o.= '  <thead>';
    $o.= '   <tr>';
    $o.= '      <th>Tipi</th>';
    $o.= '      <th>Ore</th>';
    $o.= '    </tr>';
    $o.= '  </thead>';
    $o.= '  <tbody>';
    foreach($types as $t => $ty) {
      $h = $wpdb->get_var("SELECT SUM(hours) FROM $table_hours WHERE type = $ty->id");
      $o.= '    <tr>';
        $o.= '      <td>';
//        $o.= '<a href="' . $_SERVER['REQUEST_URI'] . '&p=' . $ty->id . '">' . $ty->name . '</a>';
        $o.= $ty->name;
        $o.= '<div class="row-actions no-wrap">';
        $o.= '<a href="' . strstr($_SERVER['REQUEST_URI'], "?", true) . '?page=grimp-timetracker-type&t=' . $ty->id . '">Edit</a>';
      $o.= '</div>';
        $o.= '</td>';
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

  echo $o;  
}

?>
