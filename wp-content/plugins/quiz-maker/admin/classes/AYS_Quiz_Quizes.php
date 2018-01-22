<?php
class AYS_Quiz_Quizes{
    private static $current = null;
    private function __construct() {
        $this->setup_constants();
        add_action( 'ays_qu_cat_admin_notices', array($this,'ays_qu_cat_updated_message') );
    }    
    public function setup_constants() {
        if (!defined('AYS_QDIR')) {
            define('AYS_QDIR', dirname(__FILE__));
        }
        if (!defined('AYS_QURL')) {
            define('AYS_QURL', plugins_url(plugin_basename(dirname(__FILE__))));
        }
    }
    public static function find( $args = '' ) {
        global $wpdb;
        $defaults = array(
                'orderby' => 'id',
                'order' => 'ASC' );

        $args = wp_parse_args( $args, $defaults );

        $where = array();
        if( isset( $args['s'] ) )
                $where[] = isset( $args['s'] ) ? ' title LIKE "%'.$args['s'].'%"' : '';
        if( isset( $args['title'] ) && $args['title'] != '' )
                $where[] = isset( $args['title'] ) ? ' title = '.(int)$args['question'] : '';
        if( isset( $args['quiz_category_id'] ) && $args['quiz_category_id'] != '' )
                $where[] = isset( $args['quiz_category_id'] ) ? ' quiz_category_id = '.(int)$args['quiz_category_id'] : '';
        $where = ( count( $where ) ? '  ' . implode( ' AND ', $where ) : '' );	
        if($where)
                $where = 'WHERE'.$where;
        $oderby = ' ORDER BY '.$args['orderby'].' '.$args['order'];

        $rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."aysquiz_quizes".$where.$oderby , OBJECT);

        return $rows;
    }         
    function ays_quiz_display_list(){
        include_once AYS_QDIR.'/classes/AYS_Quiz_Quizes_List_Tables.php';
        $list_table = new AYS_Quiz_Quizes_List_Tables();
        $list_table->prepare_items();
        ?>
        <div class="wrap">
            <h2>
                <?php
                    echo esc_html( __( 'AYS Quiz Quizes', 'ays' ) );
                    echo ' <a href="admin.php?page=ays_quiz_quizes&task=add_or_edit" class="add-new-h2">' . esc_html( __( 'Add Quiz', 'ays' ) ) . '</a>';
                    if ( ! empty( $_REQUEST['s'] ) ) {
                            echo sprintf( '<span class="subtitle">'
                                    . __( 'Search results for &#8220;%s&#8221;', 'ays_quiz_quizes' )
                                    . '</span>', esc_html( $_REQUEST['s'] ) );
                    }
                ?>
            </h2>
            <?php do_action('ays_qu_cat_admin_notices' ); ?>
            <form method="get" action="">
                <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
                <?php $list_table->search_box( __( 'Search Quizes', 'ays' ), 'ays' ); ?>
                <?php $list_table->display(); ?>
            </form>
        </div>
        <?php 
    }
    function ays_qu_cat_updated_message() {
        if ( empty( $_REQUEST['message'] ) )
                return;

        if ( 'created' == $_REQUEST['message'] )
                $updated_message = esc_html( __( 'Quiz created.', 'ays_quiz_quizes' ) );
        elseif ( 'saved' == $_REQUEST['message'] )
                $updated_message = esc_html( __( 'Quiz  saved.', 'ays_quiz_quizes' ) );
        elseif ( 'deleted' == $_REQUEST['message'] )
                $updated_message = esc_html( __( 'Quiz deleted.', 'ays_quiz_quizes' ) );

        if ( empty( $updated_message ) )
                return;
        ?>
        <div id="message" class="updated">
                <p><?php echo $updated_message; ?></p>
        </div>
        <?php
    }
    
    function add_or_edit(){
        include_once AYS_QDIR.'/helpers/helper.php';
        global $wpdb;
        $ays_quiz_id = null;
        
        if(isset($_GET["q_id"])){
            $ays_quiz_id = $_GET["q_id"];
        }
        else{
            $ays_quiz_id = 0;
        }
        $ayq_quiz_category_table = $wpdb->prefix . "aysquiz_quizcategories";
        $ays_query = "SELECT * FROM ".$ayq_quiz_category_table;
        $ays_quiz_category_results = $wpdb->get_results($ays_query);
        $ays_quiz_table = $wpdb->prefix . "aysquiz_quizes";
        
        $ays_quiz_query = "SELECT * FROM ".$ays_quiz_table." WHERE id=".$ays_quiz_id;
        $ays_quiz_results = $wpdb->get_row($ays_quiz_query);

        $ays_quiz_array_params = array();
        if($ays_quiz_id == 0){
            $ays_quiz_array_params = array(
                "title" => "",
                "description" => "",
                "quiz_category_id" => "",
                "questions_ids" => "",
                "published" => ""
            );
        }
        else{
            $ays_quiz_array_params = array(
                "title" => $ays_quiz_results->title,
                "description" => $ays_quiz_results->description,
                "quiz_category_id" => $ays_quiz_results->quiz_category_id,
                "questions_ids" => $ays_quiz_results->question_ids,
                "published" => $ays_quiz_results->published
            );
        }
        
    ?>
        <div class="wrap">
            <?php
                if($ays_quiz_id == 0){
                    echo "<h1>Add new quiz</h1>";
                }
                else{
                    echo "<h1>Edit quiz</h1>";
                }
            ?>
            <form id="adminForm" action="" method="post">
                <table class='wp-list-table widefat fixed pages product_table' id="ays_main_ays">
                    <tr>
                        <td  style="width: 30%;">
                            <label for='ays_quiz_title'>Title</label>
                        </td>
                        <td>
                            <input type="text" id="ays_quiz_title" name="ays_quiz_title" value="<?php echo $ays_quiz_array_params["title"]; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for='ays_quiz_description'>Description</label>
                        </td>
                        <td>
                            <textarea rows="10" cols="45" name="ays_quiz_description" id="ays_quiz_description"><?php echo $ays_quiz_array_params["description"]; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="ays_quiz_category">Category</label>
                        </td>
                        <td>
                            <select id="ays_quiz_category" name="ays_quiz_category">
                                <?php
                                    foreach ($ays_quiz_category_results as $ays_quiz_category_result){
                                        if($ays_quiz_array_params["quiz_category_id"]==$ays_quiz_category_result->id){
                                            echo '<option value='.$ays_quiz_category_result->id.' selected>'.$ays_quiz_category_result->title.'</option>'; 
                                        }
                                        else{
                                            echo '<option value='.$ays_quiz_category_result->id.'>'.$ays_quiz_category_result->title.'</option>';
                                        }
                                    }
                                ?>                            
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Select questions:</label>
                        </td>
                        <td>
                            <a href="admin-ajax.php?action=yntrel&amp;width=600&amp;height=500" class="thickbox button" title="Select questions">
                                Select questions
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="wp-list-table widefat fixed pages product_table answers_table" id="answers_table" width="100%">
                                <tbody class="sel_question"  id="sel_question" >
                                    <tr align="center">
                                            <th class="ays_center" width="6%">Move</th>
                                            <th class="ays_center">Question</th>
                                            <th class="ays_center">Category</th>
                                            <th class="ays_center">Type</th>
                                            <th class="ays_center" width="6%">ID</th>
                                            <th class="ays_center" width="6%"></th>		
                                    </tr>
                                    <?php
                                        $ays_questions_table = $wpdb->prefix . 'aysquiz_questions';
                                        $ays_questions_display_query = "SELECT * FROM ".$ays_questions_table;
                                        $ays_questions_results = $wpdb->get_results($ays_questions_display_query);
                                        $ays_questions_category_table = $wpdb->prefix . "aysquiz_categories";
                                        foreach ($ays_questions_results as $ays_results){
                                            $ays_question_category_row = $wpdb->get_row("SELECT * FROM ".$ays_questions_category_table." WHERE id=".$ays_results->category_id);
                                            echo "<tr class='ays_all_questions' id='ays_all_questions_".$ays_results->id."' align='center'>"
                                            . "<td class='move_question'><img src='".AYS_QURL."/includes/images/cursor.png' /></td>"
                                            . "<td>".$ays_results->question."</td>"
                                            . "<td>".$ays_question_category_row->title."</td>"
                                            . "<td>".$ays_results->type."</td>"
                                            . "<td>".$ays_results->id."</td>"
                                            . "<td><img src='".AYS_QURL."/includes/images/delete_option.png' style='cursor:pointer;'  onclick='delete_option(\"ays_all_questions_".$ays_results->id."\",".$ays_results->id.")'/></td>"
                                            ."</tr>";
                                        }
                                    ?>
                                    <script>
                                        jQuery(document).ready(function(){
                                            var ays_selected_questions_string = "<?php echo $ays_quiz_array_params["questions_ids"]; ?>";
                                            console.log(ays_selected_questions_string);
                                            var ays_selected_questions_array = ays_selected_questions_string.split("***");
                                            for(var i = 0; i<ays_selected_questions_array.length; i++){
                                                jQuery('#ays_all_questions_'+ays_selected_questions_array[i]).fadeIn();
                                            }
                                        });
                                    </script>
                                </tbody>
                            </table>
                            <input type="hidden" id="ays_hidden_question" name="ays_hidden_question" value="<?php echo $ays_quiz_array_params["questions_ids"] ?>">
                            <script>
                                function delete_option(id,hamar)
                                {
                                    if (confirm("Are you sure to delete?"))
                                    jQuery('#'+id).fadeOut();
                                    var ays_ids = jQuery('#ays_hidden_question').val().split("***");
                                    for(var i =0;i<ays_ids.length;i++){
                                        if(ays_ids[i] == hamar){
                                            ays_ids.splice(i,1);
                                        }
                                    }
                                    jQuery('#ays_hidden_question').val(ays_ids.join("***"));
                                }
                                jQuery( ".sel_question" ).sortable({
                                    axis: "y",
                                    items: ".ays_all_questions",
                                    connectWith: ".sel_question",
                                    handle: ".move_question" , 
                                    cursor: 'move',
                                    helper: function(e, tr)
                                    {
                                            var $originals = tr.children();
                                            var $helper = tr;
                                            $helper.children().each(function(index)
                                            {
                                              // Set helper cell sizes to match the original sizes
                                              $(this).width($originals.eq(index).width());
                                            });
                                            return $helper;
                                    },
                                    update: function( event, ui ) 
                                    {
                                            refresh_correct();
                                    }

                                 });                            
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>
                            <?php
                                if($ays_quiz_array_params["published"]=='1'){
                                   echo '<input type="radio" name="ays_quiz_status" id="ays_quiz_status_pub" checked value="1"><label for="ays_quiz_status_pub">Publish</label>';
                                   echo '<input type="radio" name="ays_quiz_status" id="ays_quiz_status_unpub" valu="0"><label for="ays_quiz_status_unpub">Unpublish</label>';
                                }
                                else if($ays_quiz_array_params["published"]=='0'){
                                   echo '<input type="radio" name="ays_quiz_status" id="ays_quiz_status_pub" value="1"><label for="ays_quiz_status_pub">Publish</label>';
                                   echo '<input type="radio" name="ays_quiz_status" id="ays_quiz_status_unpub" checked value="0"><label for="ays_quiz_status_unpub">Unpublish</label>'; 
                                }
                                else{
                                    echo '<input type="radio" name="ays_quiz_status" id="ays_quiz_status_pub" value="1"><label for="ays_quiz_status_pub">Publish</label>';
                                    echo '<input type="radio" name="ays_quiz_status" id="ays_quiz_status_unpub" value="0"><label for="ays_quiz_status_unpub">Unpublish</label>';
                                }
                            ?>                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" class="button-primary" name="ays_quiz_edit_options" value="<?php echo __("Save","ays"); ?>" />
                            <input type="submit" class="button-primary" name="ays_quiz_apply_options" value="<?php echo __("Apply","ays"); ?>" />
                            <input type="submit" class="button-primary" name="ays_quiz_cancel_options" value="<?php echo __("Cancel","ays"); ?>" />                        
                        </td>
                    </tr>   
                </table>
            </form>
        </div>
    <?php
    if(isset($_POST["ays_quiz_edit_options"])){
        $ays_title = sanitize_text_field($_POST["ays_quiz_title"]);
        $ays_description = sanitize_text_field($_POST["ays_quiz_description"]);
        $ays_quiz_category = sanitize_text_field($_POST["ays_quiz_category"]);
        $ays_questions_id = sanitize_text_field($_POST["ays_hidden_question"]);
        $ays_published = sanitize_text_field($_POST["ays_quiz_status"]);
        if($ays_published == '1'){
            $ays_status = true;
        }
        else if($ays_published == '0'){
            $ays_status = false;
        }
        if($ays_quiz_id == 0){
            $wpdb->insert(
                $ays_quiz_table,
                array(
                    "title"=>$ays_title,
                    "description"=>$ays_description,
                    "quiz_category_id"=>$ays_quiz_category,
                    "question_ids"=>$ays_questions_id,
                    "published"=>$ays_status
                )
            );            
        }
        else{
            $wpdb->update(
                $ays_quiz_table,
                array(
                    "title"=>$ays_title,
                    "description"=>$ays_description,
                    "quiz_category_id"=>$ays_quiz_category,
                    "question_ids"=>$ays_questions_id,
                    "published"=>$ays_status
                ),
                array( 'id' => $ays_quiz_id ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%d'
                ),
                array( '%d' )
            );            
        }
        AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_quizes");
    }
    if(isset($_POST["ays_quiz_apply_options"])){
        $ays_title = sanitize_text_field($_POST["ays_quiz_title"]);
        $ays_description = sanitize_text_field($_POST["ays_quiz_description"]);
        $ays_quiz_category = sanitize_text_field($_POST["ays_quiz_category"]);
        $ays_questions_id = sanitize_text_field($_POST["ays_hidden_question"]);
        $ays_published = sanitize_text_field($_POST["ays_quiz_status"]);
        if($ays_published == '1'){
            $ays_status = true;
        }
        else if($ays_published == '0'){
            $ays_status = false;
        }
        if($ays_quiz_id == 0){
            $wpdb->insert(
                $ays_quiz_table,
                array(
                    "title"=>$ays_title,
                    "description"=>$ays_description,
                    "quiz_category_id"=>$ays_quiz_category,
                    "question_ids"=>$ays_questions_id,
                    "published"=>$ays_status
                )
            );  
            $ays_last_id = $wpdb->insert_id; 
            AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_quizes&task=add_or_edit&q_id=".$ays_last_id);   
        }
        else{
            $wpdb->update(
                $ays_quiz_table,
                array(
                    "title"=>$ays_title,
                    "description"=>$ays_description,
                    "quiz_category_id"=>$ays_quiz_category,
                    "question_ids"=>$ays_questions_id,
                    "published"=>$ays_status
                ),
                array( 'id' => $ays_quiz_id ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%d'
                ),
                array( '%d' )
            );
            AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_quizes&task=add_or_edit&q_id=".$ays_quiz_id);              
        }      
    }
    if(isset($_POST["ays_quiz_cancel_options"])){
        AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_quizes");
    }
    }

    public function delete() {
        if ( $this->initial() )
                return;

        global $wpdb;

        $query = "DELETE FROM ".$wpdb->prefix."aysquiz_quizes WHERE id = ".$this->id;
        $wpdb->query($query);
        $this->id = 0;
    }
    public static function get_instance( $qu_cat ) {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."aysquiz_quizes WHERE id=".(int)$qu_cat, OBJECT);

        self::$current = $quiz_category = new self( $qu_cat );
        $quiz_category->id = $row->id;
        $quiz_category->title = $row->title;
        $quiz_category->description = $row->description;
        $quiz_category->published = $row->published;

        return $quiz_category;
    }
    public function initial() {
        return empty( $this->id );
    }
    public function message( $status, $filter = true ) {
        $messages = $this->prop( 'messages' );
        $message = isset( $messages[$status] ) ? $messages[$status] : '';

        return $message;
    }  
}
