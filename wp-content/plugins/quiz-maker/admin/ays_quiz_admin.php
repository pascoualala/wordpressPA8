<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class AYS_Quiz_Admin{
    protected static $instance = null;
    
    private function __construct() {
        $this->setup_constants();
        add_action('admin_menu', array($this, 'ays_quiz_menu_generate'));
        add_action('admin_enqueue_scripts',array($this,'ays_quiz_admin_scripts_styles'));
	    add_action('wp_ajax_yntrel', array($this, 'yntrel'));
        add_thickbox();
    }

    public function setup_constants() {
        if (!defined('AYS_QDIR')) {
            define('AYS_QDIR', dirname(__FILE__));
        }
        if (!defined('AYS_QURL')) {
            define('AYS_QURL', plugins_url(plugin_basename(dirname(__FILE__))));
        }
        if(!defined('AYS_QFILE')){
                define( 'AYS_QFILE', AYS_QDIR . 'ays_quiz_admin.php' );
        }
    }
    function yntrel(){
        global $wpdb;
        ?>
        <div id="my-content-id">
            <table class = "wp-list-table product_table" style="margin-top:15px;width: 100%;" cellpadding="15">
                <tr align="center">
                    <th>
                        <input type="checkbox" name="ays_select_all" id="ays_select_all">
                    </th>
                    <th>
                        Question
                    </th>
                    <th>
                        Category
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        ID
                    </th>
                </tr>
                <?php
                    $ays_questions_table = $wpdb->prefix . 'aysquiz_questions';
                    $ays_questions_display_query = "SELECT * FROM ".$ays_questions_table;
                    $ays_questions_results = $wpdb->get_results($ays_questions_display_query);
                    $ays_questions_category_table = $wpdb->prefix . "aysquiz_categories";
                    foreach ($ays_questions_results as $ays_results){
                        $ays_question_category_row = $wpdb->get_row("SELECT * FROM ".$ays_questions_category_table." WHERE id=".$ays_results->category_id);
                        echo "<tr align='center'>"
                        . "<td><input type='checkbox' ays_hamar='".$ays_results->id."' id='result_".$ays_results->id."' name='result_".$ays_results->id."' class='result_quest' value='".$ays_results->id."'></td>"
                        . "<td><a class='ays_select_question' ays_id='".$ays_results->id."' style='cursor:pointer;'>".$ays_results->question."</a></td>"
                        . "<td>".$ays_question_category_row->title."</td>"
                        . "<td>".$ays_results->type."</td>"
                        . "<td>".$ays_results->id."</td>"
                        ."</tr>";
                    }
                ?>
            </table>
            <input type="button" name="ays_select_questions" id="ays_select_questions" value="Select" style="margin-top:15px;"/>
            <script type="text/javascript">
              jQuery(document).ready(function($) {
                    var ays_click_counter = 0;
                    jQuery("#ays_select_all").click(function(){
                        ays_click_counter++;
                        jQuery(".result_quest").each(function(){
                            if(ays_click_counter == 1){
                                jQuery(this).prop("checked",true);
                            }
                            else{
                                jQuery(this).prop("checked",false);
                                ays_click_counter=0;
                            }
                        });
                    });
                    jQuery("#ays_select_questions").click(function(){
                        var questions_array = new Array();
                        jQuery(".result_quest").each(function(){
                            if(jQuery(this).prop("checked") == true){
                                questions_array.push(jQuery(this).attr("ays_hamar"));
                            }
                        });
                        tb_remove();
                        for(var i=0; i<questions_array.length; i++){
                            jQuery('#ays_all_questions_'+questions_array[i]).fadeIn();
                        }
                        jQuery("#ays_hidden_question").val(questions_array.join("***"));
                    });
                    jQuery('.ays_select_question').click(
                      function () {
                            var choice = $(this).attr('ays_id');
                            tb_remove();
                            var hidden_parunak = jQuery("#ays_hidden_question").val();
                            if(hidden_parunak == "")
                            {
                                jQuery("#ays_hidden_question").val(choice);
                            }
                            else{
                                var Q_array = hidden_parunak.split("***");
                                var flag = false;
                                for(var i = 0; i<Q_array.length;i++){
                                    if(choice == Q_array[i]){
                                        flag = true;
                                    }
                                }
                                if(!flag){
                                    jQuery("#ays_hidden_question").val(hidden_parunak + "***" + choice);
                                }
                            }
                            jQuery('#ays_all_questions_'+choice).fadeIn();
                      }
                    );
              });
            </script>
        </div> 
        <?php
    }
    public function ays_quiz_menu_generate(){
        add_options_page('Quiz Maker','Quiz Maker','manage_options','ays_quiz_main',array($this,'ays_quiz_main'));
        $qz_icon_url = AYS_QURL."/includes/images/icon.png";
        add_menu_page('Quiz Maker', 'Quiz Maker', 'manage_options', 'ays_quiz_main',array($this,'ays_quiz_main'),$qz_icon_url);
        add_submenu_page('ays_quiz_main','AYS Quiz Categories', 'Quiz Categories', 'manage_options', 'ays_quiz_quiz_categories',array($this, "ays_quiz_quiz_categories"));
        add_submenu_page('ays_quiz_main','AYS Quizes', 'Quizes', 'manage_options', 'ays_quiz_quizes',array($this, "ays_quiz_quizes"));        
        add_submenu_page('ays_quiz_main','AYS Quiz Questions Categories', 'Question Categories', 'manage_options', 'ays_quiz_questions_categories',array($this, "ays_quiz_question_categories"));
        add_submenu_page('ays_quiz_main','AYS Quiz Questions', 'Questions', 'manage_options', 'ays_quiz_questions',array($this, "ays_quiz_questions"));
        //add_submenu_page('ays_quiz_main','AYS Quiz Results', 'Results', 'manage_options', 'ays_quiz_results',array($this, "ays_quiz_results"));
        //add_submenu_page('ays_quiz_main','AYS Quiz Themes', 'Themes', 'manage_options', 'ays_quiz_themes',array($this, "ays_quiz_themes"));
    }
    public  function ays_quiz_main(){
        include_once( AYS_QDIR . '/classes/AYS_Quiz_Main.php' );
        AYS_Quiz_Main::AYS_Main();
    }
    public  function ays_quiz_quiz_categories(){
        include_once( AYS_QDIR . '/classes/AYS_Quiz_Quiz_Categories.php' );
        if (isset($_GET["task"]) && $_GET["task"] == "add_or_edit"){
                AYS_Quiz_Quiz_Categories::add_or_edit();
        }
        else{
                AYS_Quiz_Quiz_Categories::ays_quiz_cat_display_list();
        }
    }
    public  function ays_quiz_quizes(){
        include_once( AYS_QDIR . '/classes/AYS_Quiz_Quizes.php' );
        if (isset($_GET["task"]) && $_GET["task"] == "add_or_edit"){
                AYS_Quiz_Quizes::add_or_edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "edit" ){
                AYS_Quiz_Quizes::edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "delete" ){
                AYS_Quiz_Quizes::delete();
        }
        else{
                AYS_Quiz_Quizes::ays_quiz_display_list();
        }
    }
    public  function ays_quiz_question_categories(){
        include_once( AYS_QDIR . '/classes/AYS_Quiz_Question_Categories.php' );
        if (isset($_GET["task"]) && $_GET["task"] == "add_or_edit"){
                AYS_Quiz_Question_Categories::add_or_edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "edit" ){
                AYS_Quiz_Question_Categories::edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "delete" ){
                AYS_Quiz_Question_Categories::delete();
        }
        else{
                AYS_Quiz_Question_Categories::ays_quiz_question_cat_display_list();
        }
    }
    public  function ays_quiz_questions(){
        include_once( AYS_QDIR . '/classes/AYS_Quiz_Questions.php' );
        if (isset($_GET["task"]) && $_GET["task"] == "add_or_edit"){
                AYS_Quiz_Questions::add_or_edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "edit" ){
                AYS_Quiz_Questions::edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "delete" ){
                AYS_Quiz_Questions::delete();
        }
        else{
                AYS_Quiz_Questions::ays_quiz_questions_display_list();
        }
    }
    public  function ays_quiz_results(){
        include_once( AYS_QDIR . '/classes/AYS_Quiz_Results.php' );
        if (isset($_GET["task"]) && $_GET["task"] == "add"){
                AYS_Quiz_Results::add();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "edit" ){
                AYS_Quiz_Results::edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "delete" ){
                AYS_Quiz_Results::delete();
        }
        else{
                AYS_Quiz_Results::ays_quiz_results_display_list();
        }
    }
    public  function ays_quiz_themes(){
        include_once( AYS_QDIR . '/classes/AYS_Quiz_Themes.php' );
        if (isset($_GET["task"]) && $_GET["task"] == "add"){
                AYS_Quiz_Themes::add();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "edit" ){
                AYS_Quiz_Themes::edit();
        }
        else if(isset($_GET["task"]) && $_GET["task"] == "delete" ){
                AYS_Quiz_Themes::delete();
        }
        else{
                AYS_Quiz_Themes::ays_quiz_themes_display_list();
        }
    }
    public function ays_quiz_admin_scripts_styles(){
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
        wp_register_style('ays_admin_style',AYS_QURL.'/includes/css/jquery-ui.css');
        wp_enqueue_style( 'ays_admin_style');
        wp_register_style('ays_admin_style1',AYS_QURL.'/includes/css/jquery.minicolors.css');
        wp_enqueue_style( 'ays_admin_style1');
        wp_register_style('ays_admin_style2',AYS_QURL.'/includes/css/style.css');
        wp_enqueue_style( 'ays_admin_style2');
        wp_register_style('ays_admin_style3',AYS_QURL.'/includes/css/toolbar.css');
        wp_enqueue_style( 'ays_admin_style3');
        wp_enqueue_script( 'jquery-ui-sortable');
        wp_register_script('ays_admin_script',AYS_QURL.'/includes/js/aysquiz_jquery.js');
        wp_enqueue_script( 'ays_admin_script' );
        wp_register_script('ays_admin_script1',AYS_QURL.'/includes/js/deafult_layout.js');
        wp_enqueue_script( 'ays_admin_script1' );
        wp_register_script('ays_admin_script2',AYS_QURL.'/includes/js/jquery.minicolors.js');
        wp_enqueue_script( 'ays_admin_script2' );
        wp_register_script('ays_admin_script3',AYS_QURL.'/includes/js/jquery-ui.js');
        wp_enqueue_script( 'ays_admin_script3' );
        wp_enqueue_media();
    }
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}