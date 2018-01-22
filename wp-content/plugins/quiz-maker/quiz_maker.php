<?php
/*
Plugin Name: Quiz Maker Demo
Version: 1.0.1
Author: AYS Pro
Author URI: http://ays-pro.com
Description: Create quizes with your questions
License: GPLv2 or later
*/
defined('AYS_DS') or define('AYS_DS', DIRECTORY_SEPARATOR);

define( 'AYS_QZ_BASENAME', plugin_basename( __FILE__ ) );
define( 'AYS_QZ_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'AYS_QZ_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
include('admin/ays_quiz_admin.php');
include('site/ays_quiz_site.php');
add_action( 'plugins_loaded', array( 'AYS_Quiz_Admin', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'AYS_Quiz_Site', 'get_instance' ) );
function aysquiz_activation()
{
    global $wpdb;
    /*quiz categories*/
    $table = $wpdb->prefix . 'aysquiz_quizcategories';
    $sql="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id`        	INT(16)     UNSIGNED NOT NULL AUTO_INCREMENT,
      `title`     	VARCHAR(256)         NOT NULL,
      `description` text               NOT NULL,
      `published` 	TINYINT     UNSIGNED NOT NULL,


      PRIMARY KEY (`id`)
    )
      ENGINE = MyISAM
      DEFAULT CHARSET = utf8
      AUTO_INCREMENT = 1";  
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
 
    
    /* quizes  */
    $table = $wpdb->prefix . 'aysquiz_quizes';
    $sql="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id`        	         INT(16)     UNSIGNED NOT NULL AUTO_INCREMENT,
      `title`                VARCHAR(256)       NOT NULL,
      `description`          TEXT               NOT NULL,
      `quiz_category_id`     INT(16)       NOT NULL,
      `question_ids`         VARCHAR(256)         NOT NULL,
      `ordering`         	 INT(16)       NOT NULL,
      `published` 	         TINYINT     UNSIGNED NOT NULL,


      PRIMARY KEY (`id`)
    )
      ENGINE = MyISAM
      DEFAULT CHARSET = utf8
      AUTO_INCREMENT = 1";   
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    /* questions */
    $table = $wpdb->prefix . 'aysquiz_questions';
    $sql="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id`          INT(16)     UNSIGNED NOT NULL AUTO_INCREMENT,
      `category_id` INT(16)     UNSIGNED NOT NULL ,
      `question`    VARCHAR(256)         NOT NULL,
      `type` 		varchar(256) 		 NOT NULL,
      `published`   TINYINT     UNSIGNED NOT NULL,


      PRIMARY KEY (`id`)
    )
      ENGINE = MyISAM
      DEFAULT CHARSET = utf8
      AUTO_INCREMENT = 1";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    /* categories */
    $table = $wpdb->prefix . 'aysquiz_categories';
    $sql="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id`        	INT(16)     UNSIGNED NOT NULL AUTO_INCREMENT,
      `title`     	VARCHAR(256)         NOT NULL,
      `description` text               NOT NULL,
      `published` 	TINYINT     UNSIGNED NOT NULL,


      PRIMARY KEY (`id`)
    )
      ENGINE = MyISAM
      DEFAULT CHARSET = utf8
      AUTO_INCREMENT = 1";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    /* answers */
    $table = $wpdb->prefix . 'aysquiz_answers';
    $sql="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id` 		     int(11)		NOT NULL AUTO_INCREMENT,
      `question_id`	 int(11) 		NOT NULL,
      `answer`	     text 			NOT NULL,
      `correct`	     tinyint(1) 	NOT NULL,
      `ordering` int(11) 		NOT NULL,

      PRIMARY KEY (`id`)
    )
      ENGINE = MyISAM
      DEFAULT CHARSET = utf8
      AUTO_INCREMENT = 1";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    
    /* reports */
    $table = $wpdb->prefix . 'aysquiz_reports';
    $sql="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id` 		     int(11)		NOT NULL AUTO_INCREMENT,
      `quiz_id`      int(11)		NOT NULL ,
      `user_id`	     int(11) 		NOT NULL,
      `user_ip`	     varchar(256)   NOT NULL,
      `date`	     datetime 	    NOT NULL,
      `score`	     varchar(256)	NOT NULL,
      `options` 	 text           NOT NULL,

      PRIMARY KEY (`id`)
    )
      ENGINE = MyISAM
      DEFAULT CHARSET = utf8
      AUTO_INCREMENT = 1";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    /* themes */
    $table = $wpdb->prefix . 'aysquiz_themes';
    $sql="CREATE TABLE IF NOT EXISTS `".$table."` (
      `id` 		 int(11)		NOT NULL AUTO_INCREMENT,
      `title`	 varchar(255) 	NOT NULL,
      `border_radius`	 varchar(255) 	NOT NULL,
      `show_result_presentage`	 int(11) 	NOT NULL,
      `show_result_answers`	 int(11) 	NOT NULL,
      `buttons_color`	 varchar(255) 	NOT NULL,
      `buttons_bg_color`	 varchar(255) 	NOT NULL,
      `buttons_hover_color`	 varchar(255) 	NOT NULL,
      `buttons_hover_bg_color`	 varchar(255) 	NOT NULL,
      `quiz_title_color`	 varchar(255)	NOT NULL,
      `quiz_description_color`	 varchar(255)	NOT NULL,
      `question_color`	 varchar(255)	NOT NULL,
      `question_bg_color`	 varchar(255)	NOT NULL,
      `question_answer_color`	 varchar(255)	NOT NULL,
      `question_answer_bg_color`	 varchar(255)	NOT NULL,
      `question_answer_hover_color`	 varchar(255)	NOT NULL,
      `question_answer_hover_bg_color`	 varchar(255)	NOT NULL,
      `question_correct_answer_bg_color`	 varchar(255)	NOT NULL,
      `question_incorrect_answer_bg_color`	 varchar(255)	NOT NULL,
      `pagination_bg_color`	 varchar(255)	NOT NULL,
      `pagination_color`	 varchar(255)	NOT NULL,

      PRIMARY KEY (`id`)
    )
      ENGINE = MyISAM
      DEFAULT CHARSET = utf8
      AUTO_INCREMENT = 1";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    $table_name1 = $wpdb->prefix .'aysquiz_categories';
    $table_name2 = $wpdb->prefix .'aysquiz_quizcategories';
    $table_name3 = $wpdb->prefix .'aysquiz_themes';
    
    $wpdb->insert(
        $table_name1,
        array(
            'title'=>'Uncategorized',
            'description'=> '',
            'published'=> 1
        )
    );
    $wpdb->insert(
        $table_name2,
        array(
            'title'=>'Uncategorized',
            'description'=> '',
            'published'=> 1
        )
    );
    $wpdb->insert(
        $table_name3,
        array(
            'id'=>'',
            'title'=>'Default',
            'border_radius'=>'4',
            'show_result_presentage'=>1,
            'show_result_answers'=>1,
            'buttons_color'=>'#ffffff',
            'buttons_bg_color'=>'#70b1f2',
            'buttons_hover_color'=>'#ffffff',
            'buttons_hover_bg_color'=>'#4797e7',
            'quiz_title_color'=>'#000000',
            'quiz_description_color'=>'#000000',
            'question_color'=>'#ffffff',
            'question_bg_color'=>'#70b1f2',
            'question_answer_color'=>'#7a7575',
            'question_answer_bg_color'=>'#efefef',
            'question_answer_hover_color'=>'#7a7575',
            'question_answer_hover_bg_color'=>'#d6d2c9',
            'question_correct_answer_bg_color'=>'#4fed24',
            'question_incorrect_answer_bg_color'=>'#ed3324',
            'pagination_bg_color'=>'#efefef',
            'pagination_color'=>'#70b1f2'
        )
    );

    add_option( 'ays_db_version', $ays_db_version);
}

register_activation_hook( __FILE__, 'aysquiz_activation');