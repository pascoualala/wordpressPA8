<?php
class AYS_Quiz_Questions{
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
            if( isset( $args['question'] ) && $args['question'] != '' )
                    $where[] = isset( $args['question'] ) ? ' question = '.(int)$args['question'] : '';
            if( isset( $args['category_id'] ) && $args['category_id'] != '' )
                    $where[] = isset( $args['category_id'] ) ? ' category_id = '.(int)$args['category_id'] : '';
            $where = ( count( $where ) ? '  ' . implode( ' AND ', $where ) : '' );	
            if($where)
                    $where = 'WHERE'.$where;
            $oderby = ' ORDER BY '.$args['orderby'].' '.$args['order'];

            $rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."aysquiz_questions".$where.$oderby , OBJECT);

            return $rows;
        }         
        public function ays_quiz_questions_display_list(){
            include_once AYS_QDIR.'/classes/AYS_Quiz_Questions_List_Tables.php';
            $list_table = new AYS_Quiz_Questions_List_Tables();
            $list_table->prepare_items();
            ?>
            <div class="wrap">
                <h2>
                    <?php
                        echo esc_html( __( 'AYS Quiz Questions', 'ays' ) );
                        echo ' <a href="admin.php?page=ays_quiz_questions&task=add_or_edit&q_type=radio" class="add-new-h2">' . esc_html( __( 'Add Question', 'ays' ) ) . '</a>';
                        if ( ! empty( $_REQUEST['s'] ) ) {
                                echo sprintf( '<span class="subtitle">'
                                        . __( 'Search results for &#8220;%s&#8221;', 'ays_quiz_questions_categories' )
                                        . '</span>', esc_html( $_REQUEST['s'] ) );
                        }
                    ?>
                </h2>
                <?php do_action('ays_qu_cat_admin_notices' ); ?>
                <form method="get" action="">
                    <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
                    <?php $list_table->search_box( __( 'Search Questions', 'ays' ), 'ays' ); ?>
                    <?php $list_table->display(); ?>
                </form>
            </div>
            <?php              
        }
        function ays_qu_cat_updated_message() {
                if ( empty( $_REQUEST['message'] ) )
                        return;

                if ( 'created' == $_REQUEST['message'] )
                        $updated_message = esc_html( __( 'Question  created.', 'ays_quiz_quiz_categories' ) );
                elseif ( 'saved' == $_REQUEST['message'] )
                        $updated_message = esc_html( __( 'Question  saved.', 'ays_quiz_quiz_categories' ) );
                elseif ( 'deleted' == $_REQUEST['message'] )
                        $updated_message = esc_html( __( 'Question deleted.', 'ays_quiz_quiz_categories' ) );

                if ( empty( $updated_message ) )
                        return;
                ?>
                <div id="message" class="updated">
                        <p><?php echo $updated_message; ?></p>
                </div>
                <?php
        }
    public function add_or_edit(){
    include_once AYS_QDIR.'/helpers/helper.php';
    global $wpdb;
    $ayq_quiz_category_table = $wpdb->prefix . "aysquiz_categories";
    $ays_quiz_questions_table = $wpdb->prefix . "aysquiz_questions";
    $ays_quiz_questions_answers_table = $wpdb->prefix . "aysquiz_answers";
    $answers_count = null;
    $ays_quiz_cat_id = 0;
    $ays_quiz_nk_type = null;
    if(isset($_GET["q_type"])){
        $ays_quiz_nk_type = $_GET["q_type"];
    }
    if(isset($_GET["q_id"])){
        $ays_quiz_cat_id = $_GET["q_id"];
    }
    $ays_quiz_cat_params = array();
    $ays_quiz_mets_answers = array();
    $ays_quiz_answers_params = array();
    if($ays_quiz_cat_id == 0){
        $ays_quiz_cat_params = array(
            'question'=>'',
            'category'=>'',
            'type'=>'',
            'status'=>''
        );
        $answers_count = 3;
        for($i=0;$i<$answers_count;$i++){
            $ays_quiz_answers_params = array(
                "id"=>'',
                "question_id"=>'',
                "answer"=>'',
                "correct"=>''
            );
            $ays_quiz_mets_answers[] = $ays_quiz_answers_params;
        }
    }
    else{
        $ays_quiz_cat_result = $wpdb->get_row("SELECT * FROM ".$ays_quiz_questions_table." WHERE id=".$ays_quiz_cat_id."");
        $ays_query = "SELECT COUNT(*) FROM ".$ays_quiz_questions_answers_table." WHERE question_id=".$ays_quiz_cat_id."";
        $answers_count = $wpdb->get_var($ays_query);
        $ays_quiz_answers_result = $wpdb->get_results("SELECT * FROM ".$ays_quiz_questions_answers_table." WHERE question_id=".$ays_quiz_cat_id."");
        $ays_quiz_cat_params = array(
            'question'=>$ays_quiz_cat_result->question,
            'category'=>$ays_quiz_cat_result->category_id,
            'type'=>$ays_quiz_cat_result->type,
            'status'=>$ays_quiz_cat_result->published
        );
        foreach($ays_quiz_answers_result as $ays_res){
            $ays_quiz_answers_params = array(
                "id"=>$ays_res->id,
                "question_id"=>$ays_res->question_id,
                "answer"=>$ays_res->answer,
                "correct"=>$ays_res->correct
            );
            $ays_quiz_mets_answers[] = $ays_quiz_answers_params;
        }
    }
    
    ?>
    <div class="wrap">
        <?php
            if($ays_quiz_cat_id==0){
                echo '<h1>Add new question</h1>';
            }
            else{
                echo '<h1>Edit question</h1>';
            }
        ?>
        <form id="adminForm" action="" method="post">
            <table class="wp-list-table widefat fixed pages product_table">
                <tbody>
                    <!-- title -->
                    <tr>
                        <td class="col_key">
                            <label for="ays_quiz_question">Question:</label>
                        </td>
                        <td class="col_value">
                            <textarea rows="10" cols="45" name="ays_quiz_question" id="ays_quiz_question"><?php echo $ays_quiz_cat_params['question'];?></textarea>
                        </td>
                    </tr>
                    <!-- category -->
                    <tr>
                        <td class="col_key">
                            <label for="ays_quiz_question_cat_id">Category:</label>
                        </td>
                        <td class="col_value">
                            <select name="ays_quiz_question_cat_id" id="ays_quiz_question_cat_id">
                                <?php
                                    $ays_quiz_category_results = $wpdb->get_results("SELECT * FROM ".$ayq_quiz_category_table);
                                    foreach ($ays_quiz_category_results as $ays_quiz_category_result){
                                        if($ays_quiz_category_result->id == $ays_quiz_cat_params['category']){
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
                    <!-- published -->
                    <tr>
                        <td class="col_key">
                            <label>Status:</label>
                        </td>
                        <td class="col_value">
                            <?php
                                if($ays_quiz_cat_params["status"]=='1'){
                                   echo '<input type="radio" name="ays_quiz_question_status" id="ays_quiz_question_status_pub" checked value="1"><label for="ays_quiz_question_status_pub">Publish</label>';
                                   echo '<input type="radio" name="ays_quiz_question_status" id="ays_quiz_question_status_unpub" valu="0"><label for="ays_quiz_question_status_unpub">Unpublish</label>';
                                }
                                else if($ays_quiz_cat_params["status"]=='0'){
                                   echo '<input type="radio" name="ays_quiz_question_status" id="ays_quiz_question_status_pub" value="1"><label for="ays_quiz_question_status_pub">Publish</label>';
                                   echo '<input type="radio" name="ays_quiz_question_status" id="ays_quiz_question_status_unpub" checked value="0"><label for="ays_quiz_question_status_unpub">Unpublish</label>'; 
                                }
                                else{
                                    echo '<input type="radio" name="ays_quiz_question_status" id="ays_quiz_question_status_pub" value="1"><label for="ays_quiz_question_status_pub">Publish</label>';
                                    echo '<input type="radio" name="ays_quiz_question_status" id="ays_quiz_question_status_unpub" value="0"><label for="ays_quiz_question_status_unpub">Unpublish</label>';
                                }
                            ?>
                        </td>
                    </tr>
                    <!-- type -->
                    <tr>
                        <td class="col_key">
                            <label for="ays_quiz_question_type">Type:</label>
                        </td>
                        <td class="col_value">
                            <select name="ays_quiz_question_type" id="ays_quiz_question_type">
                                <?php
                                    $ays_quiz_question_type = array('radio','checkbox','text','select');
                                    $ays_quiz_question_type_name = array('Radio','Checkbox','Text','Dropdown');
                                    foreach($ays_quiz_question_type as $index=>$type){
                                        if($ays_quiz_cat_params['type'] == $type){
                                            echo '<option value='.$type.' selected>'.$ays_quiz_question_type_name[$index].'</option>';
                                        }
                                        else{
                                            echo '<option value='.$type.'>'.$ays_quiz_question_type_name[$index].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="answers_table" id="answers_table" width="100%">
                                <tbody class="answers_tbody"  id="answers_tbody" >
                                    <tr>
                                            <th class="ays_center" width="6%">Move</th>
                                            <th class="ays_center" width="10%">Correct</th>
                                            <th class="ays_center" > Answer</th>
                                            <th class="ays_center" width="10%">Delete</th>
                                            <th class="ays_center" width="4%"><img src="<?php echo AYS_QURL.'/includes/images/add_option.png'; ?>" onclick="add_option('radio')"/></th>		
                                    </tr>
                                    <?php
									
                                    $ays_correctss = array();
                                    foreach($ays_quiz_mets_answers as $k=>$poqr_answers)
                                    {
                                        $ishmarForJisht = null;
                                        if($poqr_answers['correct']==1)
                                        {
                                            $ishmarForJisht = "checked";
                                        }
                                        else{
                                            $ishmarForJisht = "";
                                        }
                                        echo    '<tr id="single_ques_'.$k.'" class="answers">

                                                        <td class="move_answer">
                                                            <img src="'.AYS_QURL.'/includes/images/cursor.png" />
                                                        </td>
                                                        <td>
                                                            <input type="'.$ays_quiz_cat_params['type'].'" name="correct" '.$ishmarForJisht.' class="correct" value="'.($k+1).'" onclick="refresh_correct()"/>		
                                                        </td>

                                                        <td>
                                                            <input type="text" name="answer[]" class="answer" value="'.$poqr_answers['answer'].'"/>
                                                            <input type="hidden" name="answer_id[]" value="'.$poqr_answers['id'].'" />
                                                        </td>
                                                        <td>
                                                            <img src="'.AYS_QURL.'/includes/images/delete_option.png" onclick="delete_option(\'single_ques_'.$k.'\')"/>
                                                        </td>
                                                        <td>
                                                        </td>
                                                </tr>
                                                ';
                                        $ays_correctss[] = $poqr_answers['correct'];
                                    }
                                    $ays_corrects_string = implode(',',$ays_correctss);
                                    ?>
                                </tbody>	
                            </table>
                            <input type="hidden" id="correct_values" name="correct_values" value="<?php echo $ays_corrects_string; ?>"/>                             
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" class="button-primary" name="ays_quiz_cat_edit_options" value="<?php echo __("Save","ays"); ?>" />
                            <input type="submit" class="button-primary" name="ays_quiz_cat_apply_options" value="<?php echo __("Apply","ays"); ?>" />
                            <input type="submit" class="button-primary" name="ays_quiz_cat_cancel_options" value="<?php echo __("Cancel","ays"); ?>" />                        
                        </td>
                    </tr>                
                </tbody>
            </table>
        </form>
    </div>
    <script>
    jQuery(document).ready(function(){
        jQuery("#ays_quiz_question_type").change(function (){
            var answer_type = jQuery(this).val();
            if(answer_type == 'select' || answer_type == 'text'){
                answer_type = 'radio';
            }
            jQuery(".correct").each(function(){
               jQuery(this).attr('type',answer_type);
            });
        });
    });
    </script>
    <script>
        function delete_option(id)
        {
            if (confirm("Are you sure to delete?")) 
            jQuery('#'+id).remove();	

            refresh_correct();
        }
        jQuery( ".answers_tbody" ).sortable({
            axis: "y",
            items: ".answers",
            connectWith: ".answers_tbody",
            handle: ".move_answer" , 
            cursor: 'move',
            helper: function(e, tr)
            {
                    var $originals = tr.children();
                    var $helper = tr;
                    $helper.children().each(function(index)
                    {
                      // Set helper cell sizes to match the original sizes
                      jQuery(this).width($originals.eq(index).width());
                    });
                    return $helper;
            },
            update: function( event, ui ) 
            {
                    refresh_correct();
            }

         });
        function add_option(type)
        {
            var max_value = 0;
            jQuery('.answers').each(function() {
                var value = parseInt(jQuery(this)[0].id.replace('single_ques_',''));
                max_value = (value > max_value) ? value : max_value;
            });

            max_value = max_value + 1;	

            var tbody = document.getElementById('answers_tbody');
            var tr = document.createElement('tr');
            tr.setAttribute("id", "single_ques_"+max_value);
            tr.setAttribute("class", "answers");

            var td1 = document.createElement('td');
            td1.setAttribute("id", "single_ques_"+max_value);
            td1.setAttribute("class", "move_answer");

            var td2 = document.createElement('td');
            var td3 = document.createElement('td');
            var td4 = document.createElement('td');
            var td5 = document.createElement('td');

            var img1 = document.createElement('img');
            img1.setAttribute("src", "<?php echo AYS_QURL.'/includes/images/cursor.png';?>");

            var input2 = document.createElement('input');
            input2.setAttribute("type", type=="text" ? "hidden" : type);
            input2.setAttribute("name", "correct");
            input2.setAttribute("value", max_value);

            var input3 = document.createElement('input');
            input3.setAttribute("type", "text");
            input3.setAttribute("name", "answer[]");
            input3.setAttribute("class", "answer");
            input3.setAttribute("value", "");
            input3.setAttribute("ays_a_c",max_value);
            input3.setAttribute("onkeyup", "refresh_values()");

            var img4 = document.createElement('img');
            img4.setAttribute("src", "<?php echo AYS_QURL.'/includes/images/delete_option.png';?>");	
            img4.setAttribute("onclick", "delete_option('single_ques_"+max_value+"')");	

            td1.appendChild(img1);
            td2.appendChild(input2);
            td3.appendChild(input3);
            td4.appendChild(img4);
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tr.appendChild(td4);
            tr.appendChild(td5);

            tbody.appendChild(tr);

        }

        function refresh_correct()
        {
            correct = '';
            answer_count = jQuery('.correct').length;
            jQuery('.correct').each(function(index) {
                    correct += (jQuery(this).prop('checked') ? 1 : 0);
                    if(index != answer_count-1)
                            correct +=',';
            });

            jQuery('#correct_values').val(correct);
        }    
    </script>
    <?php
        if(isset($_POST["ays_quiz_cat_edit_options"])){ 
            $ays_quiz_quest_question=sanitize_text_field($_POST['ays_quiz_question']);
            $ays_quiz_quest_category=sanitize_text_field($_POST['ays_quiz_question_cat_id']);
            $ays_quiz_quest_type=sanitize_text_field($_POST['ays_quiz_question_type']);
            $ays_quiz_quest_published=sanitize_text_field($_POST['ays_quiz_question_status']);
            $ays_quiz_question_status = null;
            if($ays_quiz_quest_published == '1'){
                $ays_quiz_question_status = true;
            }
            else if($ays_quiz_quest_published == '0'){
                $ays_quiz_question_status = false;
            } 
            if($ays_quiz_cat_id == 0){
                $wpdb->insert(
                    $ays_quiz_questions_table,
                    array(
                        "question"=>$ays_quiz_quest_question,
                        "category_id"=>$ays_quiz_quest_category,
                        "type"=>$ays_quiz_quest_type,
                        "published"=>$ays_quiz_quest_published
                    )
                );  
                $ays_last_id = $wpdb->insert_id;    
                /*Answers part*/
                $ays_quiz_correct_answers = explode(',',$_POST["correct_values"]);
				$ays_quiz_answers = $_POST['answer'];
                $ays_quiz_answers_id = $_POST['answers_id'];
                foreach($ays_quiz_answers as $index=>$ans){
                    $correct_values = null;
                    if($ays_quiz_correct_answers[$index] == '1'){
                        $correct_values = 1;
                    }
                    else{
                        $correct_values = 0;
                    }
                    $wpdb->insert(
                        $ays_quiz_questions_answers_table,
                        array(
                            "question_id"=>$ays_last_id,
                            "answer"=>$ans,
                            "correct"=>$correct_values
                        )
                    );                    
                }
            }
            else{
                $wpdb->update(
                    $ays_quiz_questions_table,
                    array(
                        "question"=>$ays_quiz_quest_question,
                        "category_id"=>$ays_quiz_quest_category,
                        "type"=>$ays_quiz_quest_type,
                        "published"=>$ays_quiz_quest_published
                    ),
                    array( 'id' => $ays_quiz_cat_id ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ),
                    array( '%d' )
                );
                /*Answers part*/
                $ays_quiz_correct_answers = explode(',',$_POST["correct_values"]);
						
                $ays_quiz_answers = $_POST['answer'];
                $ays_quiz_answers_id = $_POST['answer_id'];
                foreach($ays_quiz_answers as $index=>$ans){
                    $correct_values = null;
                    if($ays_quiz_correct_answers[$index] == '1'){
                        $correct_values = 1;
                    }
                    else{
                        $correct_values = 0;
                    }
                    $wpdb->update(
                        $ays_quiz_questions_answers_table,
                        array(
                            "question_id"=>$ays_quiz_cat_id,
                            "answer"=>$ans,
                            "correct"=>$correct_values
                        ),
                        array('id'=>$ays_quiz_answers_id[$index]),
                        array(
                            '%d',
                            '%s',
                            '%d',
                        ),
                        array('%d')
                    );                
                }
            }
            AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_questions");
        }
        if(isset($_POST["ays_quiz_cat_apply_options"])){
            $ays_quiz_quest_question=sanitize_text_field($_POST['ays_quiz_question']);
            $ays_quiz_quest_category=sanitize_text_field($_POST['ays_quiz_question_cat_id']);
            $ays_quiz_quest_type=sanitize_text_field($_POST['ays_quiz_question_type']);
            $ays_quiz_quest_published=sanitize_text_field($_POST['ays_quiz_question_status']);
            $ays_quiz_question_status = null;
            if($ays_quiz_quest_published == '1'){
                $ays_quiz_question_status = true;
            }
            else if($ays_quiz_quest_published == '0'){
                $ays_quiz_question_status = false;
            } 
            if($ays_quiz_cat_id == 0){
                $wpdb->insert(
                    $ays_quiz_questions_table,
                    array(
                        "question"=>$ays_quiz_quest_question,
                        "category_id"=>$ays_quiz_quest_category,
                        "type"=>$ays_quiz_quest_type,
                        "published"=>$ays_quiz_quest_published
                    )
                ); 
                                $ays_last_id = $wpdb->insert_id;    
                /*Answers part*/
                $ays_quiz_correct_answers = explode(',',$_POST["correct_values"]);
                $ays_quiz_answers = $_POST['answer'];
                $ays_quiz_answers_id = $_POST['answers_id'];
                foreach($ays_quiz_answers as $index=>$ans){
                    $correct_values = null;
                    if($ays_quiz_correct_answers[$index] == '1'){
                        $correct_values = 1;
                    }
                    else{
                        $correct_values = 0;
                    }
                    $wpdb->insert(
                        $ays_quiz_questions_answers_table,
                        array(
                            "question_id"=>$ays_last_id,
                            "answer"=>$ans,
                            "correct"=>$correct_values
                        )
                    );                    
                }
                AYS_Quiz_Helper::ays_redirect("admin.php?page=ays_quiz_questions&task=add_or_edit&q_id=".$ays_last_id."");                
            }
            else{
                $wpdb->update(
                    $ays_quiz_questions_table,
                    array(
                        "question"=>$ays_quiz_quest_question,
                        "category_id"=>$ays_quiz_quest_category,
                        "type"=>$ays_quiz_quest_type,
                        "published"=>$ays_quiz_quest_published
                    ),
                    array( 'id' => $ays_quiz_cat_id ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ),
                    array( '%d' )
                );
                /*Answers part*/
                $ays_quiz_correct_answers = explode(',',$_POST["correct_values"]);
                $ays_quiz_answers = $_POST['answer'];
                $ays_quiz_answers_id = $_POST['answer_id'];
                foreach($ays_quiz_answers as $index=>$ans){
                    $correct_values = null;
                    if($ays_quiz_correct_answers[$index] == '1'){
                        $correct_values = 1;
                    }
                    else{
                        $correct_values = 0;
                    }
                    $wpdb->update(
                        $ays_quiz_questions_answers_table,
                        array(
                            "question_id"=>$ays_quiz_cat_id,
                            "answer"=>$ans,
                            "correct"=>$correct_values
                        ),
                        array('id'=>$ays_quiz_answers_id[$index]),
                        array(
                            '%d',
                            '%s',
                            '%d',
                        ),
                        array('%d')
                    );                
                }
            }
            
            AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_questions&task=add_or_edit&q_id=".$ays_quiz_cat_id);
        }
        if(isset($_POST["ays_quiz_cat_cancel_options"])){
            AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_questions");
        }
    }
    
    
        public function delete() {
        if ( $this->initial() )
                return;

        global $wpdb;

        $query = "DELETE FROM ".$wpdb->prefix."aysquiz_questions WHERE id = ".$this->id;
        $wpdb->query($query);
        $this->id = 0;
    }
        public static function get_instance( $qu_cat ) {
            global $wpdb;
            $row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."aysquiz_questions WHERE id=".(int)$qu_cat, OBJECT);

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
