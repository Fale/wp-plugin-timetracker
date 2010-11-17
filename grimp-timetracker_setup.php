<?php

global $grimp_timetracker_db_version;
$grimp_timetracker_db_version = "0.1";

function grimp_timetracker_setup() {
  global $wpdb;
  global $grimp_timetracker_db_version;

  $table_name = $wpdb->prefix . "timetracker_projects";
  if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $projects = "CREATE TABLE " . $table_name . " (
  	  id smallint unsigned NOT NULL AUTO_INCREMENT,
	    name tinytext NOT NULL,
  	  UNIQUE KEY (id)
    	);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($projects);
    add_option("grimp_timetracker_db_version", $grimp_timetracker_db_version);
  }


  $table_name = $wpdb->prefix . "timetracker_types";
  if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $projects = "CREATE TABLE " . $table_name . " (
  	  id smallint unsigned NOT NULL AUTO_INCREMENT,
	    name tinytext NOT NULL,
  	  UNIQUE KEY (id)
    	);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($projects);
    add_option("grimp_timetracker_db_version", $grimp_timetracker_db_version);
  }

  $table_name = $wpdb->prefix . "timetracker_hours";
  if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $hours = "CREATE TABLE " . $table_name . " (
  	  id smallint unsigned NOT NULL AUTO_INCREMENT,
 	    person smallint unsigned NOT NULL,
 	    project smallint unsigned NOT NULL,
	    hours decimal(5,2) unsigned NOT NULL,
	    description tinytext NOT NULL,
	    day date NOT NULL,
  	  UNIQUE KEY (id)
    	);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($hours);
    add_option("grimp_timetracker_db_version", $grimp_timetracker_db_version);
  }
}

?>
