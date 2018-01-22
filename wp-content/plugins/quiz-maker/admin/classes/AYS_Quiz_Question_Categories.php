<?php
class AYS_Quiz_Question_Categories{
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
        if (!defined('AYS_QVERSION')) {
            define('AYS_QVERSION', plugins_url($this->version));
        }
        if(!defined('AYS_QFILE')){
                define( 'AYS_QFILE', AYS_QDIR . 'ays_slider.php' );
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
                $where[] = isset( $args['title'] ) ? ' title = '.(int)$args['title'] : '';

        $where = ( count( $where ) ? '  ' . implode( ' AND ', $where ) : '' );	
        if($where)
                $where = 'WHERE'.$where;
        $oderby = ' ORDER BY '.$args['orderby'].' '.$args['order'];

        $rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."aysquiz_categories ".$where.$oderby , OBJECT);

        return $rows;
    }    
    public function ays_quiz_question_cat_display_list(){
        include_once AYS_QDIR.'/classes/AYS_Quiz_Question_Cat_List_Tables.php';
        $list_table = new AYS_Quiz_Question_Cat_List_Tables();
        $list_table->prepare_items();
        ?>
        <div class="wrap">
            <h2>
                <?php
                    echo esc_html( __( 'AYS Quiz Question Categories', 'ays' ) );
                    echo ' <a href="admin.php?page=ays_quiz_questions_categories&task=add_or_edit" class="add-new-h2">' . esc_html( __( 'Add Category', 'ays' ) ) . '</a>';
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
                <?php $list_table->search_box( __( 'Search Categories', 'ays' ), 'ays' ); ?>
                <?php $list_table->display(); ?>
            </form>
        </div>
        <?php         
    }
    function ays_qu_cat_updated_message() {
            if ( empty( $_REQUEST['message'] ) )
                    return;

            if ( 'created' == $_REQUEST['message'] )
                    $updated_message = esc_html( __( 'Question category created.', 'ays_quiz_quiz_categories' ) );
            elseif ( 'saved' == $_REQUEST['message'] )
                    $updated_message = esc_html( __( 'Question category saved.', 'ays_quiz_quiz_categories' ) );
            elseif ( 'deleted' == $_REQUEST['message'] )
                    $updated_message = esc_html( __( 'Question category deleted.', 'ays_quiz_quiz_categories' ) );

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
    $ays_quiz_cat_id = 0;
    if(isset($_GET["q_id"])){
        $ays_quiz_cat_id = $_GET["q_id"];
    }
    $ays_quiz_cat_params = array();
    if($ays_quiz_cat_id == 0){
        $ays_quiz_cat_params = array(
            'title'=>'',
            'description'=>'',
            'status'=>''
        );
    }
    else{
        $ays_quiz_cat_result = $wpdb->get_row("SELECT * FROM ".$ayq_quiz_category_table." WHERE id=".$ays_quiz_cat_id."");
        $ays_quiz_cat_params = array(
            'title'=>$ays_quiz_cat_result->title,
            'description'=>$ays_quiz_cat_result->description,
            'status'=>$ays_quiz_cat_result->published
        );
    }
    ?>
    <div class="wrap">
        <?php
            if($ays_quiz_cat_id==0){
                echo '<h1>Add question new category</h1>';
            }
            else{
                echo '<h1>Edit question category</h1>';
            }
        ?>
        <form id="adminForm" action="" method="post">
            <table class="wp-list-table widefat fixed pages product_table">
                <tbody>
                    <!-- title -->
                    <tr>
                        <td class="col_key">
                            <label for="ays_quiz_cat_title">Title:</label>
                        </td>
                        <td class="col_value">
                            <input type="text" name="ays_quiz_cat_title" id="ays_quiz_cat_title"  value="<?php echo $ays_quiz_cat_params["title"]; ?>"/> 
                        </td>
                    </tr>
                    <!-- description -->
                    <tr>
                        <td class="col_key">
                            <label for="ays_quiz_cat_description">Description:</label>
                        </td>
                        <td class="col_value">
                            <textarea name="ays_quiz_cat_description" id="ays_quiz_cat_description" rows="4" columns="2"><?php echo $ays_quiz_cat_params["description"]; ?></textarea>
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
                                   echo '<input type="radio" name="ays_quiz_cat_status" id="ays_quiz_cat_status_pub" checked value="1"><label for="ays_quiz_cat_status_pub">Publish</label>';
                                   echo '<input type="radio" name="ays_quiz_cat_status" id="ays_quiz_cat_status_unpub" valu="0"><label for="ays_quiz_cat_status_unpub">Unpublish</label>';
                                }
                                else if($ays_quiz_cat_params["status"]=='0'){
                                   echo '<input type="radio" name="ays_quiz_cat_status" id="ays_quiz_cat_status_pub" value="1"><label for="ays_quiz_cat_status_pub">Publish</label>';
                                   echo '<input type="radio" name="ays_quiz_cat_status" id="ays_quiz_cat_status_unpub" checked value="0"><label for="ays_quiz_cat_status_unpub">Unpublish</label>'; 
                                }
                                else{
                                    echo '<input type="radio" name="ays_quiz_cat_status" id="ays_quiz_cat_status_pub" value="1"><label for="ays_quiz_cat_status_pub">Publish</label>';
                                    echo '<input type="radio" name="ays_quiz_cat_status" id="ays_quiz_cat_status_unpub" value="0"><label for="ays_quiz_cat_status_unpub">Unpublish</label>';
                                }
                            ?>
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
    <?php
        if(isset($_POST["ays_quiz_cat_edit_options"])){
            $ays_quiz_cat_title = sanitize_text_field($_POST["ays_quiz_cat_title"]);
            $ays_quiz_cat_description = sanitize_text_field($_POST["ays_quiz_cat_description"]);
            $ays_quiz_cat_status_type = sanitize_text_field($_POST["ays_quiz_cat_status"]);
            $ays_quiz_cat_status = null;
            if($ays_quiz_cat_status_type == '1'){
                $ays_quiz_cat_status = true;
            }
            else if($ays_quiz_cat_status_type == '0'){
                $ays_quiz_cat_status = false;
            }
            if($ays_quiz_cat_id==0){
                $wpdb->insert(
                    $ayq_quiz_category_table,
                    array(
                        "title"=>$ays_quiz_cat_title,
                        "description"=>$ays_quiz_cat_description,
                        "published"=>$ays_quiz_cat_status
                    )
                );
            }
            else{
                $wpdb->update(
                    $ayq_quiz_category_table,
                    array(
                        "title"=>$ays_quiz_cat_title,
                        "description"=>$ays_quiz_cat_description,
                        "published"=>$ays_quiz_cat_status
                    ),
                    array( 'id' => $ays_quiz_cat_id ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                    ),
                    array( '%d' )
                );
            }
            AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_questions_categories");

        }
        if(isset($_POST["ays_quiz_cat_apply_options"])){
            $ays_quiz_cat_title = sanitize_text_field($_POST["ays_quiz_cat_title"]);
            $ays_quiz_cat_description = sanitize_text_field($_POST["ays_quiz_cat_description"]);
            $ays_quiz_cat_status_type = sanitize_text_field($_POST["ays_quiz_cat_status"]);
            $ays_quiz_cat_status = null;
            if($ays_quiz_cat_status_type == '1'){
                $ays_quiz_cat_status = true;
            }
            else if($ays_quiz_cat_status_type == '0'){
                $ays_quiz_cat_status = false;
            }
            if($ays_quiz_cat_id==0){
                $wpdb->insert(
                    $ayq_quiz_category_table,
                    array(
                        "title"=>$ays_quiz_cat_title,
                        "description"=>$ays_quiz_cat_description,
                        "published"=>$ays_quiz_cat_status
                    )
                );
                $ays_last_id = $wpdb->insert_id;
                AYS_Quiz_Helper::ays_redirect("admin.php?page=ays_quiz_questions_categories&task=add_or_edit&q_id=".$ays_last_id."");
            }
            else{
                $wpdb->update(
                    $ayq_quiz_category_table,
                    array(
                        "title"=>$ays_quiz_cat_title,
                        "description"=>$ays_quiz_cat_description,
                        "published"=>$ays_quiz_cat_status
                    ),
                    array( 'id' => $ays_quiz_cat_id ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                    ),
                    array( '%d' )
                );
                AYS_Quiz_Helper::ays_redirect("admin.php?page=ays_quiz_questions_categories&task=add_or_edit&q_id=".$ays_quiz_cat_id);
            }
        }
        if(isset($_POST["ays_quiz_cat_cancel_options"])){
            AYS_Quiz_Helper::ays_redirect("?page=ays_quiz_questions_categories");
        }
    }
    public function delete() {
        if ( $this->initial() )
                return;

        global $wpdb;

        $query = "DELETE FROM ".$wpdb->prefix."aysquiz_categories WHERE id = ".$this->id;
        $wpdb->query($query);
        $this->id = 0;
    }
    public static function get_instance( $qu_cat ) {
            global $wpdb;
            $row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."aysquiz_categories WHERE id=".(int)$qu_cat, OBJECT);

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

