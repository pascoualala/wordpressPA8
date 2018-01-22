<?php
class AYS_Quiz_Questions_List_Tables extends WP_List_Table {

	public static function define_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'question' => __( 'Title', 'ays_quiz_questions' ),
			'category_id' => __('Category','ays_quiz_questions'),
                        'type' => __('Type','ays_quiz_questions'),
                        'status' => __('Status','ays_quiz_questions'),
			'id' => __( 'ID', 'ays_quiz_questions' ) );

		return $columns;
	}
	function __construct() {
		parent::__construct();
	}
	function get_columns(){
            $columns = array(
			'cb' => '<input type="checkbox" />',
			'question' => __( 'Title', 'ays_quiz_questions' ),
			'category_id' => __('Category','ays_quiz_questions'),
                        'type' => __('Type','ays_quiz_questions'),
                        'status' => __('Status','ays_quiz_questions'),
			'id' => __( 'ID', 'ays_quiz_questions' ) );

		return $columns;		
        }
	function prepare_items() {
		$current_screen = get_current_screen();
		$columns = $this->define_columns();
		$hidden = array();
		$sortable =  $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		if ( ! empty( $_REQUEST['s'] ) )
			$args['s'] = $_REQUEST['s'];

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			if ( 'question' == $_REQUEST['orderby'] )
				$args['orderby'] = 'question';
			elseif ( 'id' == $_REQUEST['orderby'] )
				$args['orderby'] = 'id';
		}

		if ( ! empty( $_REQUEST['order'] ) ) {
			if ( 'asc' == strtolower( $_REQUEST['order'] ) )
				$args['order'] = 'ASC';
			elseif ( 'desc' == strtolower( $_REQUEST['order'] ) )
				$args['order'] = 'DESC';
		}
                
                /** Process bulk action */
                $this->process_bulk_action();
                
		$this->items = AYS_Quiz_Questions::find( $args );	
		$per_page = $this->get_items_per_page( 'ays_qu_cat_per_page', 5 );
	
		$current_page = $this->get_pagenum();
		$total_items = count($this->items);
		$total_pages = ceil( $total_items / $per_page );

		$ays_nk_data = array_slice($this->items,(($current_page-1)*$per_page),$per_page);

		$this->set_pagination_args( array(
		'total_items' => $total_items,            
		'per_page'    => $per_page                    
		) );

		$this->items = $ays_nk_data;		
	}
	function get_sortable_columns() {
		$columns = array(
			'question' => array( 'title', true ),
			'category' => array( 'description', false ),
			'status' => array( 'status', true ),
			'id' => array( 'id', true ) );

		return $columns;
	}
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'ays_quiz_questions' ) );
		return $actions;
	}
	function column_default( $item, $column_name ) {
		return '';
        }
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="qu_cat[]" value="%s" />',
			$item->id );
	}
	function column_question( $item ) {
		$url = admin_url( 'admin.php?page=ays_quiz_questions&task=add_or_edit&q_id=' . absint( $item->id) . '&q_type=' . $item->type );
		$edit_link = add_query_arg( array( 'action' => 'edit' ), $url );

		$actions = array(
			'edit' => '<a href="' . $edit_link . '">' . __( 'Edit', 'ays_quiz_questions' ) . '</a>' );
		$a = sprintf( '<a class="row-title" href="%1$s" title="%2$s">%3$s</a>',
			$edit_link,
			esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'ays_quiz_questions' ),
				$item->question ) ),
			esc_html( $item->question ) );

		return '<strong>' . $a . '</strong> ' . $this->row_actions( $actions );
        }
        function column_category_id($item){
                global $wpdb;
                $ayq_quiz_category_table = $wpdb->prefix . "aysquiz_categories";
                $ays_quiz_cat_result = $wpdb->get_row("SELECT title FROM ".$ayq_quiz_category_table." WHERE id=".$item->category_id."");
                return $ays_quiz_cat_result->title;
        }
        function column_type($item){
                return $item->type;
        }
	function column_status( $item ) {
                $ays_publish_item='';
                if($item->published == 1){
                    $ays_publish_item='<a> <img src="'.AYS_QURL.'../includes/images/published.png" width="12"> Published</a>';
                }
                else{
                    $ays_publish_item='<a> <img src="'.AYS_QURL.'../includes/images/unpublished.png" width="12"> Unpublished</a>';
                }
		return $ays_publish_item;
        }
	function column_id( $item ) {
		$ids = array((int)$item->id);
		return (int)$item->id;
	}
        public function process_bulk_action() {

            // security check!
            if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['ays_quiz_questions'];

                if ( ! wp_verify_nonce( $nonce, $action ) )
                    wp_die( 'Nope! Security check failed!' );

            }

            $action = $this->current_action();

            switch ( $action ) {

                case 'delete':
                    $qu_cats = empty( $_POST['post_id'] )
                            ? (array) $_REQUEST['qu_cat']
                            : (array) $_POST['post_id'];

                    $deleted = 0;

                    foreach ( $qu_cats as $qu_cat ) {
                            $qu_cat = AYS_Quiz_Questions::get_instance( $qu_cat );
                            if ( empty( $qu_cat ) )
                                    continue;

                            $qu_cat->delete();
                            $deleted += 1;
                    }

                    $query = array();
                    if ( ! empty( $deleted ) )
                            $query['message'] = 'deleted';
                    $_REQUEST["message"] = 'deleted';
                    $redirect_to = add_query_arg( $query, '?page=ays_quiz_questions' );
                    break;
                default:
                    return;
                    break;
            }

            return;
        }
}
?>


