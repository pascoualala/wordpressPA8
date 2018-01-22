<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
 
$option_name = 'ays_db_version';
 
delete_option( $option_name );
 
// For site options in Multisite
delete_site_option( $option_name );  
 
// Drop a custom db table
global $wpdb;
$table = $wpdb->prefix . 'aysquiz_quizcategories';
$wpdb->query( "DROP TABLE IF EXISTS ".$table );
$table1 = $wpdb->prefix . 'aysquiz_quizes';
$wpdb->query( "DROP TABLE IF EXISTS ".$table1 );
$table2 = $wpdb->prefix . 'aysquiz_questions';
$wpdb->query( "DROP TABLE IF EXISTS ".$table2 );
$table3 = $wpdb->prefix . 'aysquiz_categories';
$wpdb->query( "DROP TABLE IF EXISTS ".$table3 );
$table4 = $wpdb->prefix . 'aysquiz_answers';
$wpdb->query( "DROP TABLE IF EXISTS ".$table4 );
$table5 = $wpdb->prefix . 'aysquiz_reports';
$wpdb->query( "DROP TABLE IF EXISTS ".$table5 );
$table6 = $wpdb->prefix . 'aysquiz_themes';
$wpdb->query( "DROP TABLE IF EXISTS ".$table6 );