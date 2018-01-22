<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class AYS_Quiz_Site{
    protected static $instance = null;
    private function __construct() {
        $this->setup_constants();
        add_shortcode( 'ays_quiz', array($this,'ays_quiz_shortcode_generate'));
        add_action( 'wp_enqueue_scripts', array($this,'ays_site_styles') );
    }
    public function setup_constants() {
        if (!defined('AYS_QDIR')) {
            define('AYS_QDIR', dirname(__FILE__));
        }
        if (!defined('AYS_QURL')) {
            define('AYS_QURL', plugins_url(plugin_basename(dirname(__FILE__))));
        }
        if(!defined('AYS_QFILE')){
                define( 'AYS_QFILE', AYS_QDIR . 'ays_quiz_site.php' );
        }
    }

    public function ays_quiz_shortcode_generate($attributes){
        wp_enqueue_scripts('jquery');
        global $wpdb;
        $ays_question_table = $wpdb->prefix.'aysquiz_questions';
        $ays_answeres_table = $wpdb->prefix.'aysquiz_answers';
        $ays_quiz_id = null;
        if( !array_key_exists( 'id', $attributes ) ) {
            $ays_quiz_id = "No id provided in shortcode.";
        }
        else{
            $ays_quiz_id = $attributes["id"];
        }
        $ays_quiz_table = $wpdb->prefix . "aysquiz_quizes";
        $ays_quiz_questions_table = $wpdb->prefix . "aysquiz_questions";
        $ays_quiz_query = "SELECT * FROM ".$ays_quiz_table." WHERE id=".$ays_quiz_id;
        $row = $wpdb->get_row($ays_quiz_query);
        $q_array=explode("***",$row->question_ids);
        if(isset($_POST["ays_finish"]))
        {
            $ays_questions_table = $wpdb->prefix . "aysquiz_questions";
            $ays_answers_table = $wpdb ->prefix . "aysquiz_answers";

            $questions_in_quiz = sanitize_text_field($_POST['check_qstns']);
            $question_in_quiz_array = explode("***",$questions_in_quiz);
            $questions = array();
            $answers = array();
            $corrects = array();
            foreach ($question_in_quiz_array as $key => $quest) {
                $questions[] = $wpdb->get_row("SELECT * FROM ".$ays_questions_table." WHERE id=".$quest);
            }
            foreach($questions as $question){
                    $answers[$question->id] = $wpdb->get_results("SELECT * FROM ".$ays_answers_table." WHERE question_id=".$question->id);
            }
            foreach($questions as $q){
                switch($q->type)
                {
                    case 'checkbox':
                        $correct=1;
                        foreach($answers[$q->id] as $answer)
                        {
                            if($answer->correct=='1')
                            {
                               if(!isset($_POST['ans_'.$answer->id]))
                               {
                                 $correct=0;
                               }
                            }
                            else
                            {
                               if(isset($_POST['ans_'.$answer->id]))
                               {
                                     $correct=0;
                               }

                            }
                        }
                        $corrects[$q->id]=($correct==1)?1:0;
                        $correct=1;  
                    break;
                    case 'radio':
                        $correct_radio=0;
                        foreach($answers[$q->id] as $index=>$answer)
                        {
                            if($_POST['radio_ans_'.$q->id]==$answer->id && $answer->correct=='1')
                            {
                                $correct_radio=1;
                            }
                        }
                        $corrects[$q->id]=($correct_radio==1)?1:0;
                        $correct_radio=0;
                    break;
                    case 'select':
                        $correct_select=0;
                           foreach($answers[$q->id] as $answer)
                               {
                                       if($_POST['select_ans_'.$q->id]==$answer->id && $answer->correct=='1')
                                       $correct_select=1;
                               }
                        $corrects[$q->id]=($correct_select==1)?1:0;
                        $correct_select=0;
                     break;
                     case 'text':
                         $corrects[$q->id]=0;
                     break;
                }

            }
                $count_of_questions = count($question_in_quiz_array);
                $count_of_right_answers = array_sum($corrects);
                $score = round((($count_of_right_answers / $count_of_questions)*100),2);
                echo "Your score is ".$score."%";

        }
        else{
        echo "<div id='ays_quiz_main' style='padding:10px;'><form method='post'>";
        foreach($q_array as $question_id)
        {
            $ays_quiz_display_part3 = null;
            $ays_question_query = "SELECT * FROM ".$ays_question_table." WHERE id=".$question_id;
            $ays_question_result = $wpdb->get_row($ays_question_query);
            $ays_answer_query = "SELECT * FROM ".$ays_answeres_table." WHERE question_id=".$question_id;
            $ays_answer = $wpdb->get_results($ays_answer_query);
            switch($ays_question_result->type)
            {
                case 'checkbox':
                        $ays_quiz_display_part3 .= "<ul class='ays_answers_list' data-type='checkbox'>";
                                        foreach($ays_answer as $answer)
                                        {
                                                 $ays_quiz_display_part3 .="
                                                 <li data-name='ans_".$answer->id."' q='".$question_id."'>
                                                 <input class='ays_checkbox hide' type='checkbox' id='ans_".$answer->id."'  name='ans_".$answer->id."' value='1' >
                                                 <span>".$answer->answer."</span>
                                                 </li>
                                                 ";

                                        }
                                   $ays_quiz_display_part3 .= "</ul>";				
                        break;

                case 'radio':
                                $ays_quiz_display_part3 .= "<ul class='ays_answers_list' data-type='radio'>";
                                foreach($ays_answer as $answer)
                                {

                                         $ays_quiz_display_part3 .= "
                                         <li data-name='radio_ans_".$ays_question_result->id."' q='".$question_id."'>
                                         <input class='ays_radio hide' type='radio'  id='ans_".$answer->id."' name='radio_ans_".$ays_question_result->id."' value='".$answer->id."' >
                                         <span >".$answer->answer."</span>
                                         </li>
                                         ";

                                }

                $ays_quiz_display_part3 .= "</ul>";
                                break;

                case 'select':
                        $ays_quiz_display_part3 .= "<select class='ays_answers_list' name='select_ans_".$ays_question_result->id."'>";
                        foreach($ays_answer as $answer)
                        {
                                 $ays_quiz_display_part3 .= "
                                 <option value='".$answer->id."'>
                                 ".$answer->answer."
                                 </option>
                                 ";

                        }

            $ays_quiz_display_part3 .= "</select>";
                  break;

                case 'text':				
                        $ays_quiz_display_part3 .= "<ul  class='ays_answers_list ays_answers_list_text'>";
                        foreach($ays_answer as $answer)
                        {
                                 $ays_quiz_display_part3 .= "
                                 <li q='".$question_id."'>
                                 <input type='text' id='ans_".$answer->id."' name='ans_".$answer->id."' value='' >		
                                 </li>
                                 ";
                        }

                        $ays_quiz_display_part3 .= "</ul>"; 
                        break;

            }          
            ?>
            <div id="ays_question_<?php echo $question_id;?>" class='ays_quest' next_id='<?php echo $question_id;?>' style="border:2px solid black;padding:10px;margin-top:10px;">
                <div class='ays_question'>
                    <div class='ays-question'><?php echo $ays_question_result->question;?></div>
                    <?php echo $ays_quiz_display_part3; ?>
                </div>
            </div>
            <?php
            
        }
            ?>
            <script>
                /*jQuery('.ays_answers_list:not(.ays_answers_list_text) li').click(function(){
                    var type = jQuery(this).closest('.ays_answers_list').attr('data-type');
                    var name = jQuery(this).attr('data-name');

                    if(type == 'radio'){
                        jQuery('.ays_answers_list li').removeClass('active_answer');
                        jQuery('.ays_answers_list li [name='+ name + ']').removeAttr('checked');
                    }
                    jQuery(this).find('[name='+ name + ']').attr('checked', true);
                    jQuery(this).addClass('active_answer');
                });  */
            </script>
            <style>
                ul{
                    list-style-type: none;
                }
            </style>
            <input type="hidden" id="check_qstns" name="check_qstns" value="<?php echo $row->question_ids; ?>">
            <input type="submit" name="ays_finish" value="FINISH" id="ays_finish" style="margin-top:10px;"> 
            </form>
            </div>
            <?php
            }

    }
    
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    public function ays_site_styles(){

    }
}

