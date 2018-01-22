<?php
/*
	Plugin Name: Quiz Cat Free
	Plugin URI: https://fatcatapps.com/quiz-cat
	Description: Provides an easy way to create and administer quizes
	Text Domain: quiz-cat
	Domain Path: /languages
	Author: Fatcat Apps
	Author URI: https://fatcatapps.com/
	License: GPLv2
	Version: 1.4.1
*/


// BASIC SECURITY
defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );



if ( !defined ('FCA_QC_PLUGIN_DIR') ) {
	
	// DEFINE SOME USEFUL CONSTANTS
	define( 'FCA_QC_DEBUG', FALSE );
	define( 'FCA_QC_PLUGIN_VER', '1.4.1' );
	define( 'FCA_QC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'FCA_QC_PLUGINS_URL', plugins_url( '', __FILE__ ) );
	define( 'FCA_QC_PLUGIN_FILE', __FILE__ );
	define( 'FCA_QC_PLUGIN_PACKAGE', 'Free' ); //DONT CHANGE THIS, IT WONT ADD FEATURES, ONLY BREAKS UPDATER AND LICENSE
	
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/activate.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/activate.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/sidebar.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/sidebar.php' );
	}	
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/premium/premium.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/premium/premium.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/premium/business.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/premium/business.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/premium/licensing.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/premium/licensing.php' );
	}	
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/premium/stats.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/premium/stats.php' );
	}
	if ( file_exists ( FCA_QC_PLUGIN_DIR . '/includes/upgrade.php' ) ) {
		include_once( FCA_QC_PLUGIN_DIR . '/includes/upgrade.php' );
	}	
	//FILTERABLE FRONT-END STRINGS
	$global_quiz_text_strings = array (
		'no_quiz_found' => __('No Quiz found', 'quiz-cat'),
		'correct' => __('Correct!', 'quiz-cat'),
		'wrong' => __('Wrong!', 'quiz-cat'),
		'your_answer' => __('Your answer:', 'quiz-cat'),
		'correct_answer' => __('Correct answer:', 'quiz-cat'),
		'question' => __('Question', 'quiz-cat'),
		'next' =>  __('Next', 'quiz-cat'),
		'you_got' =>  __('You got', 'quiz-cat'),
		'out_of' => __('out of', 'quiz-cat'),
		'your_answers' =>  __('Your Answers', 'quiz-cat'),
		'start_quiz' => __('Start Quiz', 'quiz-cat'),
		'retake_quiz' => __('Retake Quiz', 'quiz-cat'),
		'share_results' => __('SHARE YOUR RESULTS', 'quiz-cat'),
		'i_got' => __('I got', 'quiz-cat'),
		'skip_this_step' => __('Skip this step', 'quiz-cat'),
		'your_name' => __('Your Name', 'quiz-cat'),
		'your_email' => __('Your Email', 'quiz-cat'),
	);
	
	//ACTIVATION HOOK
	function fca_qc_activation() {
		
		$args = array(
			'post_type' => 'fca_qc_quiz',
			'posts_per_page'=> -1,
		);
			
		$posts = get_posts( $args );
		
		//check if we should display backward compatibility notice for 1.4 merge tags
		fca_qc_maybe_show_merge_tag_notice( $posts );
		
		//convert answer metadata from old format to new
		fca_qc_convert_question_meta( $posts );
		
		//convert CSV from old format to new
		fca_qc_convert_csv();

		//CREATE TABLE IF IT DOESNT EXIST
		if ( function_exists ('fca_qc_table') ) {
			
			global $wpdb;
			$new_table_name = fca_qc_table();
		
			if( $wpdb->get_var("SHOW TABLES LIKE '$new_table_name'") === null && !defined ( 'fca_qc_disable_activity' ) ) {
				
				//NEW TABLE DOESN'T EXIST, UPGRADE
				if ( function_exists( 'fca_qc_create_table' ) ) {
					fca_qc_create_table();
				}
								
				//convert table format from old format to new
				fca_qc_upgrade_quiz_tables( $posts );
				
				return true;
			}
		}
	}
	register_activation_hook( FCA_QC_PLUGIN_FILE, 'fca_qc_activation' );

	////////////////////////////
	// SET UP POST TYPE
	////////////////////////////

	//REGISTER CPT
	function fca_qc_register_post_type() {
		
		$labels = array(
			'name' => _x('Quizzes','quiz-cat'),
			'singular_name' => _x('Quiz','quiz-cat'),
			'add_new' => _x('Add New','quiz-cat'),
			'all_items' => _x('All Quizzes','quiz-cat'),
			'add_new_item' => _x('Add New Quiz','quiz-cat'),
			'edit_item' => _x('Edit Quiz','quiz-cat'),
			'new_item' => _x('New Quiz','quiz-cat'),
			'view_item' => _x('View Quiz','quiz-cat'),
			'search_items' => _x('Search Quizzes','quiz-cat'),
			'not_found' => _x('Quiz not found','quiz-cat'),
			'not_found_in_trash' => _x('No Quizzes found in trash','quiz-cat'),
			'parent_item_colon' => _x('Parent Quiz:','quiz-cat'),
			'menu_name' => _x('Quiz Cat','quiz-cat')
		);
			
		$args = array(
			'labels' => $labels,
			'description' => "",
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'menu_position' => 117,
			'menu_icon' => FCA_QC_PLUGINS_URL . '/assets/icon.png',
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title'),
			'has_archive' => false,
			'rewrite' => false,
			'query_var' => true,
			'can_export' => true
		);
		
		register_post_type( 'fca_qc_quiz', $args );
	}
	add_action ( 'init', 'fca_qc_register_post_type' );
	
	//CHANGE CUSTOM 'UPDATED' MESSAGES FOR OUR CPT
	function fca_qc_post_updated_messages( $messages ){
		
		$post = get_post();
		
		$messages['fca_qc_quiz'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Quiz updated.','quiz-cat'),
			2  => __( 'Quiz updated.','quiz-cat'),
			3  => __( 'Quiz deleted.','quiz-cat'),
			4  => __( 'Quiz updated.','quiz-cat'),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Quiz restored to revision from %s','quiz-cat'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Quiz published.' ,'quiz-cat'),
			7  => __( 'Quiz saved.' ,'quiz-cat'),
			8  => __( 'Quiz submitted.' ,'quiz-cat'),
			9  => sprintf(
				__( 'Quiz scheduled for: <strong>%1$s</strong>.','quiz-cat'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Quiz draft updated.' ,'quiz-cat'),
		);

		return $messages;
	}
	add_filter('post_updated_messages', 'fca_qc_post_updated_messages' );

	//Customize CPT table columns
	function fca_qc_add_new_post_table_columns($columns) {
		$new_columns = array();
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x('Quiz Name', 'column name', 'quiz-cat');
		$new_columns['shortcode'] = __('Shortcode', 'quiz-cat');
		$new_columns['date'] = _x('Date', 'column name', 'quiz-cat');
	 
		return $new_columns;
	}
	add_filter('manage_edit-fca_qc_quiz_columns', 'fca_qc_add_new_post_table_columns', 10, 1 );

	function fca_qc_manage_post_table_columns($column_name, $id) {
		switch ($column_name) {
			case 'shortcode':
				echo '<input type="text" readonly="readonly" onclick="this.select()" value="[quiz-cat id=&quot;'. $id . '&quot;]"/>';
					break;
		 
			default:
			break;
		} // end switch
	}
	// Add to admin_init function
	add_action('manage_fca_qc_quiz_posts_custom_column', 'fca_qc_manage_post_table_columns', 10, 2);

	//PREVIEW
	function fca_qc_live_preview( $content ){
		global $post;
		if ( is_user_logged_in() && $post->post_type === 'fca_qc_quiz' && is_main_query() && !doing_action( 'wp_head' ) )  {
			return $content . do_shortcode("[quiz-cat id='" . $post->ID . "']");
		} else {
			return $content;
		}
	}
	add_filter( 'the_content', 'fca_qc_live_preview');

	////////////////////////////
	// EDITOR PAGE 
	////////////////////////////

	//ENQUEUE ANY SCRIPTS OR CSS FOR OUR ADMIN PAGE EDITOR
	function fca_qc_admin_cpt_script( $hook ) {
		global $post;  
		if ( ($hook == 'post-new.php' || $hook == 'post.php')  &&  $post->post_type === 'fca_qc_quiz' ) {  
			wp_enqueue_media();	
			wp_enqueue_style('dashicons');
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-tooltip');
			
			wp_enqueue_script('fca_qc_wysi_tidy', FCA_QC_PLUGINS_URL . '/assets/admin/js/wysi/tidy.min.js', array(), FCA_QC_PLUGIN_VER, true );		
			wp_enqueue_script('fca_qc_wysi_js_main', FCA_QC_PLUGINS_URL . '/assets/admin/js/wysi/wysihtml.min.js', array(), FCA_QC_PLUGIN_VER, true );		
			wp_enqueue_script('fca_qc_wysi_js_toolbar', FCA_QC_PLUGINS_URL . '/assets/admin/js/wysi/wysihtml-toolbar.min.js', array(), FCA_QC_PLUGIN_VER, true );		
			wp_enqueue_style('fca_qc_wysi_css', FCA_QC_PLUGINS_URL . '/assets/admin/js/wysi/wysi.min.css', array(), FCA_QC_PLUGIN_VER );
			wp_enqueue_script('fca_qc_wysi_js', FCA_QC_PLUGINS_URL . '/assets/admin/js/wysi/wysi.min.js', array( 'jquery', 'fca_qc_wysi_tidy', 'fca_qc_wysi_js_main', 'fca_qc_wysi_js_toolbar' ), FCA_QC_PLUGIN_VER, true );		
			
			$editor_dependencies = array( 'fca_qc_wysi_tidy', 'fca_qc_wysi_js_main', 'fca_qc_wysi_js_toolbar', 'fca_qc_wysi_js', 'jquery','jquery-ui-core', 'jquery-ui-tooltip');
			
			if ( FCA_QC_DEBUG ) {
				wp_enqueue_script('fca_qc_admin_js', FCA_QC_PLUGINS_URL . '/assets/admin/js/admin.js', $editor_dependencies, FCA_QC_PLUGIN_VER, true );		
				wp_enqueue_style( 'fca_qc_admin_stylesheet', FCA_QC_PLUGINS_URL . '/assets/admin/css/admin.css', array(), FCA_QC_PLUGIN_VER );			
				
			} else {
				wp_enqueue_script('fca_qc_admin_js', FCA_QC_PLUGINS_URL . '/assets/admin/js/admin.min.js', $editor_dependencies, FCA_QC_PLUGIN_VER, true );		
				wp_enqueue_style( 'fca_qc_admin_stylesheet', FCA_QC_PLUGINS_URL . '/assets/admin/css/admin.min.css', array(), FCA_QC_PLUGIN_VER );
			}

			
			$admin_data = array (
				//A TEMPLATE DIV OF THE QUESTION AND RESULT DIVS, SO WE CAN ADD MORE OF THEM VIA JAVASCRIPT
				'questionDiv' => 	fca_qc_render_question( array(), '{{QUESTION_NUMBER}}' ),
				'resultDiv' 	=> 	fca_qc_render_result( array(), '{{RESULT_NUMBER}}' ),
				'answerDiv' 	=> 	fca_qc_render_answer( array(), '{{QUESTION_NUMBER}}', '{{ANSWER_NUMBER}}' ),

				//SOME LOCALIZATION STRINGS FOR JAVASCRIPT STUFF
				'navigationWarning_string' => __( "You have entered new data on this page.  If you navigate away from this page without first saving your data, the changes will be lost.", 'quiz-cat'),
				'sureWarning_string' => 	 __( 'Are you sure?', 'quiz-cat'),
				'selectImage_string' => __('Select Image', 'quiz-cat' ),			
				'remove_string' =>  __('remove', 'quiz-cat'),
				'show_string' =>  __('show', 'quiz-cat'),
				'unused_string' =>  __('Unused', 'quiz-cat'),
				'points_string' =>  __('Points', 'quiz-cat'),
				'question_string' =>  __('Question', 'quiz-cat'),
				'save_string' =>  __('Save', 'quiz-cat'),
				'preview_string' =>  __('Save & Preview', 'quiz-cat'),
				'on_string' =>  __('YES', 'quiz-cat'),
				'off_string' =>  __('NO', 'quiz-cat'),
				'debug' => FCA_QC_DEBUG,
				'stylesheet' => FCA_QC_PLUGINS_URL . '/assets/admin/js/wysi/wysi.min.css'
			);
			 
			wp_localize_script( 'fca_qc_admin_js', 'fcaQcAdminData', $admin_data );
			wp_localize_script( 'fca_qc_wysi_js', 'fcaQcAdminData', $admin_data );
		}

	}
	add_action( 'admin_enqueue_scripts', 'fca_qc_admin_cpt_script', 10, 1 );  
	
	function fca_qc_admin_nav() {
		global $post;
		if ( $post->post_type === 'fca_qc_quiz'	 ) {
			$html = '<div id="qc-nav">';
				
				$html .= '<h1 class="nav-tab-wrapper">';
					$html .= '<a href="#" id="editor-nav" class="nav-tab nav-tab-active">' . __('Editor', 'quiz-cat') . '</a>';
					$html .= '<a href="#" id="settings-nav" class="nav-tab">' . __('Settings', 'quiz-cat') . '</a>';
				$html .= '</h1>';
				
			$html .= '</div>';
			echo $html;
		}
	}
	add_action( 'edit_form_after_title', 'fca_qc_admin_nav' );	

	//ADD META BOXES TO EDIT CPT PAGE
	function fca_qc_add_custom_meta_boxes( $post ) {

		add_meta_box( 
			'fca_qc_description_meta_box',
			__( 'This Quiz', 'quiz-cat' ),
			'fca_qc_render_description_meta_box',
			null,
			'normal',
			'high'
		);	
		
		add_meta_box( 
			'fca_qc_questions_meta_box',
			__( 'Questions', 'quiz-cat' ),
			'fca_qc_render_questions_meta_box',
			null,
			'normal',
			'default'
		);
		

		add_meta_box( 
			'fca_qc_add_result_meta_box',
			__( 'Results', 'quiz-cat' ),
			'fca_qc_render_add_result_meta_box',
			null,
			'normal',
			'default'
		);
		
		add_meta_box( 
			'fca_qc_quiz_settings_meta_box',
			__( 'General', 'quiz-cat' ),
			'fca_qc_render_quiz_settings_meta_box',
			null,
			'normal',
			'default'
		);	
	}
	add_action( 'add_meta_boxes_fca_qc_quiz', 'fca_qc_add_custom_meta_boxes' );

	//RENDER THE DESCRIPTION META BOX
	function fca_qc_render_description_meta_box( $post ) {
		
		$quiz_meta = get_post_meta ( $post->ID, 'quiz_cat_meta', true );
		$quiz_meta = empty( $quiz_meta ) ? array() : $quiz_meta;
		$quiz_meta['desc'] = empty ( $quiz_meta['desc'] ) ? '' : $quiz_meta['desc'];
		$quiz_meta['desc_img_src'] = empty ( $quiz_meta['desc_img_src'] ) ? '' : $quiz_meta['desc_img_src'];

		//ADD A HIDDEN PREVIEW URL INPUT
		$html = "<input type='hidden' name='fca_qc_quiz_preview_url' id='fca_qc_quiz_preview_url' value='" . get_permalink( $post ) . "'>";
		
		$html .= "<table class='fca_qc_inner_setting_table'>";
			$html .= "<tr>";
				$html .= "<th>" . __('Description', 'quiz-cat') . "</th>";
				$html .= "<td>" . fca_qc_add_wysiwyg( $quiz_meta['desc'], 'fca_qc_quiz_description' ) . "</td>";
			$html .= "</tr>";
			$html .= "<tr>";
				$html .= "<th>" . __('Image', 'quiz-cat') . "</th>";
				$html .= "<td>" . fca_qc_add_image_input( $quiz_meta['desc_img_src'], 'fca_qc_quiz_description_image_src', 'fca_qc_quiz_description_image_src' ) . "</td>";
			$html .= "</tr>";
		$html .= "</table>";
		
		echo $html;
	}

	//RENDER THE ADD QUESTION META BOX
	function fca_qc_render_questions_meta_box( $post ) {
			
		$questions = get_post_meta ( $post->ID, 'quiz_cat_questions', true );
		
		echo "<p class='fca_qc_quiz_instructions'>" . __('Add your questions to ask and the possible responses. Drag to re-order.', 'quiz-cat') . "</p>";
		
		echo "<div class='fca_qc_sortable_questions'>";
		if ( empty ( $questions ) ) {
			
			echo fca_qc_render_question( array(), 1 );
			
		} else {
			
			$counter = 1;
			
			forEach ( $questions as $question ) {
				
				echo fca_qc_render_question( $question, $counter );
				$counter = $counter + 1;
				
			}		
		}	
		echo "</div>";
		echo "<button type='button' title='" . __( 'Add a new Question', 'quiz-cat') . "' id='fca_qc_add_question_btn' class='button-secondary fca_qc_add_btn' ><span class='dashicons dashicons-plus' style='vertical-align: text-top;'></span>" .__('Add', 'quiz-cat') . "</button>";
		
	}

	// RENDER A QUESTION META BOX
	// INPUT: ARRAY->$question
	// OUTPUT: HTML 
	function fca_qc_render_question( $question, $question_number ) {
		
		if ( empty ( $question ) ) {
			$question = array(
				'question' => '',
				'img' => '',
				'hint' => '',
				'answers' => '',
				'id' => '{{ID}}',
			);
		}
		$question['id'] = empty( $question['id'] ) ? '{{ID}}' : $question['id'];
				
		$html = "<div class='fca_qc_question_item fca_qc_deletable_item' id='fca_qc_question_$question_number'>";
			$html .= "<input class='fca_qc_id' name='fca_qc_quiz_questions[$question_number][id]' value='" . $question['id'] . "' hidden >";
			$html .= fca_qc_add_delete_button();
			$html .= "<h3 class='fca_qc_question_label'><span class='fca_qc_quiz_heading_question_number'>" . __('Question', 'quiz-cat') . ' ' . $question_number . ": </span><span class='fca_qc_quiz_heading_text'>". fca_qc_convert_entities($question['question']) . "</span></h3>";
				
				$html .= "<div class='fca_qc_question_input_div'>";
					$html .= "<div class='fca_qc_question_header_div'>";
						$html .= "<table class='fca_qc_inner_setting_table'>";
							$html .= "<tr>";
								$html .= "<th>" . __('Image', 'quiz-cat') . "</th>";
								$html .= '<td>' . fca_qc_add_image_input( $question['img'], "fca_qc_quiz_questions[$question_number][img]" ) . '</td>';
							$html .= "</tr>";
							$html .= "<tr>";
								$html .= "<th>" . __('Description', 'quiz-cat') . "</th>";
								$html .= "<td><textarea placeholder='" . __('e.g. Can cats fly?', 'quiz-cat') . "' class='fca_qc_question_texta fca_qc_question_text' name='fca_qc_quiz_questions[$question_number][question]'>" . $question['question']  ."</textarea></td>";				
							$html .= "</tr>";
						$html .= "</table>";
					$html .= "</div>";
					
					$answers = empty ($question['answers']) ? array(array(),array()) : $question['answers'];
					$answer_number = 1;
					forEach ( $answers as $answer ) {
						$html .= fca_qc_render_answer( $answer, $question_number, $answer_number );
						$answer_number++;
					}
					$html .= "<a class='fca_qc_add_answer_btn'>" . __('Add Answer', 'quiz-cat') ."</a>";
					
				$html .= "</div >";
		$html .= "</div >";

		return $html;
	
	}
	
	function fca_qc_render_answer( $answer, $question_number, $answer_number ) {
		
		$html = '';
	
		$answer['answer'] = empty ( $answer['answer'] ) ? '' : $answer['answer'];
		$answer['img'] = empty ( $answer['img'] ) ? '' : $answer['img'];
		$answer['id'] = empty ( $answer['id'] ) ? '{{ID}}' : $answer['id'];
		$answer['hint'] = empty( $answer['hint'] ) ? '' : $answer['hint'];
		
		$placeholder = $answer_number == 1 ? __('e.g. No', 'quiz-cat') :  __('e.g. Yes', 'quiz-cat');
		$html .= "<div class='fca_qc_answer_input_div fca_qc_deletable_item'>";
		
			$html .= "<input class='fca_qc_id' name='fca_qc_quiz_questions[$question_number][answers][$answer_number][id]' value='" . $answer['id'] . "' hidden >";
			
			if ( $answer_number == 1 ) {
				$html .= "<h4>" . __('Correct Answer', 'quiz-cat') . "</h4>";
			} else {
				$html .= "<h4>" . __('Wrong Answer', 'quiz-cat') . fca_qc_add_delete_button();
			}
			$html .= "<table class='fca_qc_inner_setting_table'>";
				
				if ( function_exists( 'fca_qc_save_premium_post' ) ) {
					$html .= "<tr>";
						$html .= "<th>" . __('Image', 'quiz-cat') . "</th>";
						$html .= "<td>" . fca_qc_add_image_input( $answer['img'], "fca_qc_quiz_questions[$question_number][answers][$answer_number][img]" ) . "</td>";
					$html .= "</tr>";
				}
				$html .= "<tr>";
					$html .= "<th>" . __('Text', 'quiz-cat') . "</th>";
					$html .= "<td><textarea placeholder='$placeholder' class='fca_qc_question_texta' name='fca_qc_quiz_questions[$question_number][answers][$answer_number][answer]'>" . $answer['answer']  ."</textarea></td>";
				$html .= "</tr>";
				
				if ( function_exists ('fca_qc_save_quiz_settings_premium' ) && $answer_number === 1 ) {
					$html .= "<tr class='fca_qc_hint_tr'>";
						$html .= "<th>" . __('Explanation', 'quiz-cat') . "</th>";
						$html .= "<td><textarea placeholder='" . __('Explanation', 'quiz-cat') . "' class='fca_qc_question_texta' name='fca_qc_quiz_questions[$question_number][answers][$answer_number][hint]'>" . $answer['hint']  ."</textarea></td>";
					$html .= "</tr>";
				}
			$html .= "</table>";
		$html .= "</div>";	
		
		return $html;	
	}

	//RENDER THE ADD RESULT META BOX
	function fca_qc_render_add_result_meta_box( $post ) {
				
		$results = get_post_meta ( $post->ID, 'quiz_cat_results', true );
		
		echo "<p class='fca_qc_quiz_instructions'>" . __('Add your results based on the number of correct answers. Drag to re-order. This is optional.', 'quiz-cat') . "</p>";
		
		echo "<div class='fca_qc_sortable_results'>";
		if ( empty ( $results ) ) {
			
			echo fca_qc_render_result( array(), 1 );
			
		} else {
			
			$counter = 1;
			
			forEach ( $results as $result ) {
				
				echo fca_qc_render_result ($result, $counter );
				
				$counter = $counter + 1;
				
			}		
		}
		echo "</div>";	
		echo "<button type='button' title='" . __( 'Add a new Result', 'quiz-cat') . "' id='fca_qc_add_result_btn' class='button-secondary fca_qc_add_btn' ><span class='dashicons dashicons-plus' style='vertical-align: text-top;'></span>" . __('Add', 'quiz-cat') . "</button>";

	}
	function fca_qc_add_delete_button ( $target_class = 'fca_qc_deletable_item' ) {
		
		$html = "<span class='dashicons dashicons-trash fca_qc_delete_icon fca_qc_delete_button'></span>";
		$html .= "<span style='display:none;' class='dashicons dashicons-no fca_qc_delete_icon fca_qc_delete_icon_cancel'></span>";
		$html .= "<span data-target='$target_class' style='display:none;' class='dashicons dashicons-yes fca_qc_delete_icon fca_qc_delete_icon_confirm'></span>";

		return $html;
	}
	// RENDER A RESULT META BOX
	// INPUT: ARRAY->$result (TITLE, DESC, IMG), INT|STRING->$result_number
	// OUTPUT: HTML			
	function fca_qc_render_result( $result, $result_number ) {
		
		if ( empty ( $result ) ) {
			$result = array(
				'title' => '',
				'desc' => '',
				'img' => '',
				'url' => '',
				'tags' => array(),
			);
		}
		
		$result['url'] = empty ( $result['url'] ) ? '' : $result['url'];
		$result['tags'] = empty ( $result['tags'] ) ? '' : $result['tags'];
		
		$html = "<div class='fca_qc_result_item fca_qc_deletable_item' id='fca_qc_result_$result_number'>";
			$html .= fca_qc_add_delete_button(); //nearest class?
			$html .= "<h3 class='fca_qc_result_label'><span class='fca_qc_result_score_value'></span><span class='fca_qc_result_score_title'>" . $result['title'] . "</span></h3>";
			
			$html .= "<div class='fca_qc_result_input_div'>";
				$html .= "<table class='fca_qc_inner_setting_table'>";
					$html .= "<tr>";
						$html .= "<th>" . __('Result Title', 'quiz-cat') . "</th>";
						$html .= "<td><input type='text' placeholder='" . __('Title', 'quiz-cat') . "' class='fca_qc_text_input fca_qc_quiz_result' name='fca_qc_quiz_result_title[]' value='" . $result['title'] . "'></input></td>";
					$html .= "</tr>";
					$html .= "<tr class='fca_qc_result_row_default'>";
						$html .= "<th>" . __('Image', 'quiz-cat') . "</th>";
						$html .= "<td>" . fca_qc_add_image_input( $result['img'], 'fca_qc_quiz_result_image_src[]' ) . "</td>";
					$html .= "</tr>";
					$html .= "<tr class='fca_qc_result_row_default'>";
						$html .= "<th>" . __('Description', 'quiz-cat') . "</th>";
						$html .= "<td>" . fca_qc_add_wysiwyg( $result['desc'], 'fca_qc_quiz_result_description[]' ) . "</td>";
					$html .= "</tr>";
					if ( function_exists ('fca_qc_save_quiz_settings_premium' ) ) {
						$html .= "<tr class='fca_qc_result_row_url'>";
							$html .= "<th>" . __('Redirect URL', 'quiz-cat') . "</th>";
							$html .= "<td><input type='url' placeholder='" . __('http://mycoolsite.com/grumpy-cat', 'quiz-cat') . "' class='fca_qc_url_input' name='fca_qc_quiz_result_url[]' value='" . $result['url'] . "'></input></td>";
						$html .= "</tr>";
					
						if ( function_exists( 'fca_qc_add_tag_div' ) ) {
							$html .= fca_qc_add_tag_div( 'results', $result['tags'] );
							
							$html .= "<tr class='fca_qc_mailchimp_api_settings'>";	
							
								$html .= "<th>";
									$html .= "<label class='fca_qc_admin_label fca_qc_admin_settings_label' for='fca_qc_quiz_result_mailchimp_groups'>" . __('Interest Groups', 'quiz-cat') . fca_qc_tooltip( __("If you use MailChimp Groups opt-in feature, select one or more interest groups quiz takers should be added to.  Optional.", 'quiz-cat') ) ."</label>";
								$html .= "</th>";
									
								$html .= "<td style='line-height: normal;'>";
									$html .= "<span style='display: none;' class='fca_qc_icon dashicons dashicons-image-rotate fca_qc_spin'></span>";
									$html .= '<select style="width: 300px; border: 1px solid #ddd; border-radius: 0;" data-placeholder="&#8681; ' . __('Select Interest Groups (Optional)', 'quiz-cat') . ' &#8681;" class="fca_qc_multiselect fca_qc_mailchimp_groups"" id="fca_qc_quiz_result_mailchimp_groups" multiple="multiple" name="fca_qc_quiz_result_mailchimp_groups[][]">';
										if ( !empty ( $result['groups'] ) ) {
											
											forEach ( $result['groups'] as $group ) {
												$html .= "<option value='$group' selected='selected' >" . __('Loading...', 'quiz-cat') . "</option>";
											}
											unset ( $group );
										}
									$html .= '</select>';
								$html .= "</td>";
							$html .= "</tr>";
							
						}
					}
					
				$html .= "</table>";
			$html .= '</div>';
			
			//SOME HIDDEN INPUTS FOR THE RANGE OF SCORES FOR THIS RESULT
			$html .= "<input type='number' class='fca_qc_result_min' name='fca_qc_result_min[]' value='-1' hidden >";
			$html .= "<input type='number' class='fca_qc_result_max' name='fca_qc_result_max[]' value='-1' hidden >";
			
		$html .= "</div>";
		
		return $html;
		
	}

	//RENDER THE QUIZ SETTINGS META BOX 
	function fca_qc_render_quiz_settings_meta_box( $post ) {
		
		$settings = get_post_meta ( $post->ID, 'quiz_cat_settings', true );
		$settings = empty( $settings ) ? array() : $settings;
		$quiz_type = empty( $settings['quiz_type'] ) ? '' : $settings['quiz_type'];
		$settings['hide_answers'] = empty( $settings['hide_answers'] ) ? '' : $settings['hide_answers'];
		$hide_answers = empty ( $settings['hide_answers'] ) ? '' : "checked='checked'";
		$shuffle_questions = empty ( $settings['shuffle_questions'] ) ? '' : "checked='checked'";
		$restart_button = empty ( $settings['restart_button'] ) ? '' : "checked='checked'";
		$show_explanations = empty ( $settings['explanations'] ) ? '' : "checked='checked'";
		$result_mode = empty ( $settings['result_mode'] ) ? 'basic' : $settings['result_mode'];

		$shortcode = '[quiz-cat id="' . $post->ID . '"]';
		echo "<table class='fca_qc_setting_table'>";

			echo "<tr>";
				echo "<th>";
					echo "<label class='fca_qc_admin_label fca_qc_admin_settings_label' id='fca_qc_shortcode_label' for='fca_qc_shortcode_input'>" . __('Shortcode', 'quiz-cat') . fca_qc_tooltip(__('Paste the shortcode in to a post or page to embed this quiz.', 'quiz-cat')) . "</label>";
				echo "</th>";
				echo "<td>";
					echo "<input type='text' class='fca_qc_input_wide fca_qc_shortcode_input' name='fca_qc_shortcode_input' value='$shortcode' readonly>";		
				echo "</td>";
			echo "<tr>";
			
			echo "<tr id='fca_qc_answer_mode_tr'>";
					if ( function_exists( 'fca_qc_answer_mode_toggle' ) ) {
						fca_qc_answer_mode_toggle( $settings['hide_answers'] );
					} else {
						echo "<th>";
							echo "<label class='fca_qc_admin_label fca_qc_admin_settings_label' for='fca_qc_hide_answers_until_end'>" . __('Hide answers until end of quiz', 'quiz-cat') . "</label>";
						echo "</th>";
						echo "<td>";
						echo "<div class='onoffswitch'>";
							echo "<input type='checkbox' class='onoffswitch-checkbox' id='fca_qc_hide_answers_until_end' style='display:none;' name='fca_qc_hide_answers_until_end' $hide_answers></input>";		
							echo "<label class='onoffswitch-label' for='fca_qc_hide_answers_until_end'><span class='onoffswitch-inner'><span class='onoffswitch-switch'></span></span></label>";
						echo "</div>";
					}
				echo "</td>";
			echo "</tr>";
			
			if ( function_exists ('fca_qc_save_quiz_settings_premium' ) ) {
				echo "<tr id='fca_qc_hints_toggle_tr'>";
					echo "<th>";
						echo "<label class='fca_qc_admin_label fca_qc_admin_settings_label' for='fca_qc_explanations'>" . __('Enable Explanations', 'quiz-cat') . fca_qc_tooltip(__('Show an explanation or reasoning why an answer is correct.', 'quiz-cat')) .  "</label>";
					echo "</th>";
					echo "<td>";
						echo "<div class='onoffswitch'>";
							echo "<input type='checkbox' class='onoffswitch-checkbox' id='fca_qc_explanations' style='display:none;' name='fca_qc_explanations' $show_explanations></input>";		
							echo "<label class='onoffswitch-label' for='fca_qc_explanations'><span class='onoffswitch-inner'><span class='onoffswitch-switch'></span></span></label>";
						echo "</div>";
					echo "</td>";
				echo "</tr>";
			
				echo "<tr>";
					echo "<th>";
						echo "<label class='fca_qc_admin_label fca_qc_admin_settings_label' for='fca_qc_result_mode'>" . __('Results', 'quiz-cat') . fca_qc_tooltip(__('Choose to show a result panel at the end of the quiz, or redirect to a new page when a user completes the quiz.', 'quiz-cat')) .  "</label>";
					echo "</th>";
					echo "<td>";
						echo "<div class='radio-toggle'>";
							if ( $result_mode === 'basic' ) {
								echo "<label class='selected'>";
								_e('Show Result', 'quiz-cat');
								echo '<input class="qc_radio_input fca_qc_result_mode_input" name="fca_qc_result_mode" type="radio" value="basic" checked /></label>';
								echo "<label>";
								_e('Redirect to URL', 'quiz-cat');
								echo '<input class="qc_radio_input fca_qc_result_mode_input" name="fca_qc_result_mode" type="radio" value="redirect" /></label>';
							} else {
								echo "<label>";
								_e('Show Result', 'quiz-cat');
								echo '<input class="qc_radio_input fca_qc_result_mode_input" name="fca_qc_result_mode" type="radio" value="basic" /></label>';
								echo "<label class='selected'>";
								_e('Redirect to URL', 'quiz-cat');
								echo '<input class="qc_radio_input fca_qc_result_mode_input" name="fca_qc_result_mode" type="radio" value="redirect" checked /></label>';								
							}
						echo "</div>";
					echo "</td>";
				echo "</tr>";				
			
		
				echo "<tr>";
					echo "<th>";
						echo "<label class='fca_qc_admin_label fca_qc_admin_settings_label' for='fca_qc_shuffle_question_order'>" . __('Shuffle Question Order', 'quiz-cat') . fca_qc_tooltip(__( 'Shuffle or randomize the order of questions each time someone takes your quiz.','quiz-cat')) . "</label>";
					echo "</th>";
					echo "<td>";
						echo "<div class='onoffswitch'>";
							echo "<input type='checkbox' class='onoffswitch-checkbox' id='fca_qc_shuffle_question_order' style='display:none;' name='fca_qc_shuffle_question_order' $shuffle_questions></input>";		
						echo "<label class='onoffswitch-label' for='fca_qc_shuffle_question_order'><span class='onoffswitch-inner'><span class='onoffswitch-switch'></span></span></label>";
						echo "</div>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<th>";
						echo "<label class='fca_qc_admin_label fca_qc_admin_settings_label' for='fca_qc_show_restart_button'>" . __('Show Restart Quiz Button', 'quiz-cat') . "</label>";
					echo "</th>";
					echo "<td>";
						echo "<div class='onoffswitch'>";
							echo "<input type='checkbox' class='onoffswitch-checkbox' id='fca_qc_show_restart_button' style='display:none;' name='fca_qc_show_restart_button' $restart_button></input>";		
						echo "<label class='onoffswitch-label' for='fca_qc_show_restart_button'><span class='onoffswitch-inner'><span class='onoffswitch-switch'></span></span></label>";
						echo "</div>";
					echo "</td>";
				echo "</tr>";
			}
			
			
		echo "</table>";
	}

	//CUSTOM SAVE HOOK
	function fca_qc_save_post( $post_id ) {
		
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		//ONLY DO OUR STUFF IF ITS A REAL SAVE, NOT A NEW IMPORTED ONE
		if ( array_key_exists ( 'fca_qc_quiz_preview_url', $_POST ) ) {
			
			if ( function_exists( 'fca_qc_insert_quiz_to_db' ) && !defined ( 'fca_qc_disable_activity' ) ) {
				fca_qc_insert_quiz_to_db( $post_id );
			}
			
			//SAVING META DATA ( DESCRIPTION, IMAGE )
			$meta_fields = array (
				'fca_qc_quiz_description'	=> 'desc',
				'fca_qc_quiz_description_image_src'	=> 'desc_img_src',
			);
			
			forEach ( $meta_fields as $key => $value ) {
				$meta[$value] = empty ( $_POST[$key] ) ? '' : fca_qc_escape_input ( $_POST[$key] );
			}
			
			update_post_meta ( $post_id, 'quiz_cat_meta', $meta );
			
			if ( function_exists ('fca_qc_save_appearance_settings') ) {
				fca_qc_save_appearance_settings( $post_id, $_POST );
			}
			
			
			if ( array_key_exists ( 'fca_qc_quiz_type', $_POST ) ) {
				if ( $_POST['fca_qc_quiz_type'] == 'pt' && function_exists ('fca_qc_save_premium_post') ) {
					return fca_qc_save_premium_post( $post_id, $_POST );
				}
			}			

			//SAVING QUESTIONS
			$questions = array();
			
			$i = 0;
			forEach ($_POST['fca_qc_quiz_questions'] as $question) {
				$questions[$i]['question'] = empty( $question['question'] ) ? '' : fca_qc_escape_input( $question['question'] );
				$questions[$i]['img'] = empty( $question['img'] ) ? '' : fca_qc_escape_input( $question['img'] );
				$questions[$i]['id'] = empty( $question['id'] ) ? '' : $question['id'];
				
				$j = 0;
				forEach ($question['answers'] as $answer) {
					$questions[$i]['answers'][$j]['answer'] = empty( $answer['answer'] ) ? '' : fca_qc_escape_input( $answer['answer'] );
					$questions[$i]['answers'][$j]['img'] = empty( $answer['img'] ) ? '' : fca_qc_escape_input( $answer['img'] );
					$questions[$i]['answers'][$j]['id'] =  empty( $answer['id'] ) ? '' : $answer['id'];
					if ( $j === 0 ) {
						$questions[$i]['answers'][$j]['hint'] = empty( $answer['hint'] ) ? '' : fca_qc_escape_input( $answer['hint'] );
					}
					$j++;
				}
				$i++;
			}
			
			update_post_meta ( $post_id, 'quiz_cat_questions', $questions );
			
			$results = array();
			
			//SAVING RESULTS
			$n = empty ( $_POST['fca_qc_quiz_result_title'] ) ? 0 : count ( $_POST['fca_qc_quiz_result_title'] );
			
			for ($i = 0; $i < $n ; $i++) {
				$results[$i]['title'] = fca_qc_escape_input( $_POST['fca_qc_quiz_result_title'][$i] );
				$results[$i]['desc'] = fca_qc_escape_input( $_POST['fca_qc_quiz_result_description'][$i] );
				$results[$i]['img'] = fca_qc_escape_input( $_POST['fca_qc_quiz_result_image_src'][$i] );
				$results[$i]['min'] = intval ( fca_qc_escape_input( $_POST['fca_qc_result_min'][$i] ) );
				$results[$i]['max'] = intval ( fca_qc_escape_input( $_POST['fca_qc_result_max'][$i] ) );
				$results[$i]['url'] = fca_qc_escape_input( $_POST['fca_qc_quiz_result_url'][$i] );
				$results[$i]['tags'] = empty ( $_POST['fca_qc_results_tags'][$i] ) ? '' : fca_qc_escape_input(  $_POST['fca_qc_results_tags'][$i] );
				$results[$i]['groups'] = empty ( $_POST['fca_qc_quiz_result_mailchimp_groups'][$i] ) ? array() : fca_qc_escape_input(  $_POST['fca_qc_quiz_result_mailchimp_groups'][$i] );
			}
						
			update_post_meta ( $post_id, 'quiz_cat_results', $results );

			$settings = array(
				'quiz_type' => 'mc',
			);
			
			if ( function_exists ('fca_qc_save_quiz_settings_premium') ) {
				fca_qc_save_quiz_settings_premium( $settings, $post_id );
			} else {
				fca_qc_save_quiz_settings( $settings, $post_id );
			}
	
			if ( function_exists ('fca_qc_save_optin_settings') ) {
				fca_qc_save_optin_settings( $post_id, $_POST );
			}
						
			wp_publish_post( $post_id );
		
		}	
	}
	add_action( 'save_post_fca_qc_quiz', 'fca_qc_save_post' );
	
	function fca_qc_save_quiz_settings( $settings, $post_id ) {
		
		//SAVING SETTINGS
		$fields = array (
			'fca_qc_hide_answers_until_end'	=> 'hide_answers',
			'fca_qc_result_mode'			=> 'result_mode',
		);

		
		forEach ( $fields as $key => $value ) {
			$settings[$value] = empty ( $_POST[$key] ) ? '' : fca_qc_escape_input( $_POST[$key] );
		}
			
		update_post_meta ( $post_id, 'quiz_cat_settings', $settings );
		
	}

	function fca_qc_escape_input($data) {
		
		if ( is_array ( $data ) ) {
			forEach ( $data as $k => $v ) {
				$data[$k] = fca_qc_escape_input($v);
			}
			return $data;
		}
		
		$data = wp_kses_post( $data );
			
		return $data;

	}

	/* Redirect when Save & Preview button is clicked */
	function fca_qc_save_preview_redirect ( $location ) {
		global $post;
		if ( !empty($_POST['fca_qc_quiz_preview_url'] ) ) {
			// Flush rewrite rules
			global $wp_rewrite;
			$wp_rewrite->flush_rules(true);

			return $_POST['fca_qc_quiz_preview_url'];
		}
	 
		return $location;
	}
	add_filter('redirect_post_location', 'fca_qc_save_preview_redirect');

	////////////////////////////
	// DISPLAY QUIZ
	////////////////////////////

	//SUPPRESS POST TITLES ON OUR CUSTOM POST TYPE
	function fca_qc_suppress_post_title() {
		global $post;
		if ( empty ( $post ) ) {
			return false;
		}
		if ( $post->post_type == 'fca_qc_quiz' &&  is_main_query() ) {
			wp_enqueue_style( 'fca_qc_quiz_post_stylesheet', FCA_QC_PLUGINS_URL . '/assets/admin/css/hide-title.css', array(), FCA_QC_PLUGIN_VER );
		}
	}	
	add_action( 'wp_enqueue_scripts', 'fca_qc_suppress_post_title' );

	function fca_qc_do_quiz( $atts ) {

		if ( !empty ( $atts[ 'id' ] ) ) {
						
			$post_id = intVal ( $atts[ 'id' ] );
			$all_meta =  get_post_meta ( $post_id, '', true );
			$quiz_meta = empty ( $all_meta['quiz_cat_meta'] ) ? array() : unserialize( $all_meta['quiz_cat_meta'][0] );
			$quiz_meta['title'] = get_the_title ( $post_id );
			$questions = empty ( $all_meta['quiz_cat_questions'] ) ? array() : unserialize( $all_meta['quiz_cat_questions'][0] );
			$quiz_results = empty ( $all_meta['quiz_cat_results'] ) ? array() : unserialize( $all_meta['quiz_cat_results'][0] );
			$quiz_settings = empty ( $all_meta['quiz_cat_settings'] ) ? array() : unserialize( $all_meta['quiz_cat_settings'][0] );
			$restart_button = empty ( $quiz_settings['restart_button'] ) ? false : true;
			$optin_settings = empty ( $all_meta['quiz_cat_optins'] ) ? array() : unserialize( $all_meta['quiz_cat_optins'][0] );
			$draw_optins = empty( $optin_settings['capture_emails'] ) ? false : true;

			if ( !$quiz_meta || !$questions ) {
				return '<p>Quiz Cat: ' . __('No Quiz found', 'quiz-cat') . '</p>';
			}
			
			wp_enqueue_script( 'jquery' );
			wp_enqueue_style( 'fca_qc_quiz_stylesheet', FCA_QC_PLUGINS_URL . '/assets/quiz/css/quiz.min.css', array(), FCA_QC_PLUGIN_VER );
			wp_enqueue_script( 'fca_qc_img_loaded', FCA_QC_PLUGINS_URL . '/assets/quiz/js/jquery.waitforimages.min.js', array(), FCA_QC_PLUGIN_VER, true );

			
			if ( $draw_optins ) {
				wp_enqueue_style( 'fca_qc_tooltipster_stylesheet', FCA_QC_PLUGINS_URL . '/assets/quiz/css/tooltipster.bundle.min.css', array(), FCA_QC_PLUGIN_VER );
				wp_enqueue_style( 'fca_qc_tooltipster_borderless_css', FCA_QC_PLUGINS_URL . '/assets/quiz/css/tooltipster-borderless.min.css', array(), FCA_QC_PLUGIN_VER );
				wp_enqueue_script( 'fca_qc_tooltipster_js', FCA_QC_PLUGINS_URL . '/assets/quiz/js/tooltipster.bundle.min.js', array('jquery'), FCA_QC_PLUGIN_VER, true );
				wp_enqueue_script( 'fca_qc_jstz_js', FCA_QC_PLUGINS_URL . '/assets/quiz/js/jstz.min.js', array(), FCA_QC_PLUGIN_VER, true );
			}
			
			if ( FCA_QC_DEBUG ) {
				wp_enqueue_script( 'fca_qc_quiz_js', FCA_QC_PLUGINS_URL . '/assets/quiz/js/quiz.js', array( 'jquery', 'fca_qc_img_loaded' ), FCA_QC_PLUGIN_VER, true );
			} else {
				wp_enqueue_script( 'fca_qc_quiz_js', FCA_QC_PLUGINS_URL . '/assets/quiz/js/quiz.min.js', array( 'jquery', 'fca_qc_img_loaded' ), FCA_QC_PLUGIN_VER, true );
			}
			
			//DONT SEND API KEYS TO CLIENT SIDE JS
			
			if ( !empty( $optin_settings['api_key'] ) ) { 
				unset( $optin_settings['api_key'] );
			}
			
			if ( !empty( $optin_settings['getresponse_key'] ) ) { 
				unset( $optin_settings['getresponse_key'] );
			}
			
			if ( !empty( $optin_settings['aweber_key'] ) ) { 
				unset( $optin_settings['aweber_key'] );
			}
			
			if ( !empty( $optin_settings['activecampaign_key'] ) ) { 
				unset( $optin_settings['activecampaign_key'] );
			}
			if ( !empty( $optin_settings['drip_key'] ) ) { 
				unset( $optin_settings['drip_key'] );
			}
			
			$quiz_text_strings = fca_qc_set_quiz_text_strings( $atts );
						
			//SEND JS THE DATA BUT CONVERT ANY ESCAPED THINGS BACK TO NORMAL CHARACTERS
			$quiz_data = array(
				'quiz_id' => $post_id,
				'quiz_meta' => fca_qc_convert_entities($quiz_meta),
				'questions' => fca_qc_convert_entities($questions),
				'quiz_results' => fca_qc_convert_entities($quiz_results),
				'quiz_settings' => $quiz_settings,
				'wrong_string' => $quiz_text_strings[ 'wrong' ],
				'correct_string' => $quiz_text_strings[ 'correct' ],
				'your_answer_string' => $quiz_text_strings[ 'your_answer' ],
				'correct_answer_string' => $quiz_text_strings[ 'correct_answer' ],
				'optin_settings' => $optin_settings,
				'nonce' => wp_create_nonce('fca_qc_quiz_ajax_nonce'),
				'ajaxurl' => admin_url('admin-ajax.php'),
				'default_img' => FCA_QC_PLUGINS_URL . '/assets/quizcat-240x240.png',
			);
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				if ( $user->ID !== 0 ) {
					$quiz_data['user'] = array (
						'name' => $user->user_firstname,
						'email' => $user->user_email,
						
					);
				}
			}
			wp_localize_script( 'fca_qc_quiz_js', "quizData_$post_id", $quiz_data );
			wp_localize_script( 'fca_qc_quiz_js', "fcaQcData", array( 'debug' => FCA_QC_DEBUG ) );
			
			//ADD IMPRESSION
			if ( function_exists('fca_qc_add_activity') ) {
				$return = fca_qc_add_activity( $post_id, 'impressions' );
			}
			
			ob_start(); ?>
			
			<?php echo fca_qc_maybe_add_custom_styles( $post_id ) ?>
			
			<div class='fca_qc_quiz' id='<?php echo "fca_qc_quiz_$post_id" ?>'>
				<span class='fca_qc_mobile_check'></span>
				<h2 class='fca_qc_quiz_title'><?php echo $quiz_meta['title'] ?></h2>
				<div class='fca_qc_quiz_description'><?php echo $quiz_meta['desc'] ?></div>
				<img class='fca_qc_quiz_description_img' src='<?php echo $quiz_meta['desc_img_src'] ?>'>
				
				<button type='button' class='fca_qc_button fca_qc_start_button'><?php echo $quiz_text_strings[ 'start_quiz' ] ?></button>
				
				<div class='flip-container fca_qc_quiz_div' style='display: none;'>
					<div class='fca-qc-flipper'>
						<?php echo fca_qc_do_question_panel( $post_id, $quiz_text_strings ) ?> 
						<?php echo fca_qc_do_answer_panel( $quiz_text_strings ) ?> 
						
					</div>
				</div>
				<?php echo fca_qc_do_score_panel( $post_id, $quiz_text_strings ) ?> 
				
				<div class='fca_qc_quiz_footer' style='display: none;'>
					<span class='fca_qc_question_count'></span>		
				</div>
				<?php if ( $draw_optins && function_exists('fca_qc_do_optin_panel') ) {
						echo fca_qc_do_optin_panel( $optin_settings, $quiz_text_strings );
					}?>
				
				<?php echo fca_qc_do_your_answers_panel( $quiz_text_strings ) ?> 
				
				<?php if ( $restart_button ) {
					$button_text = $quiz_text_strings[ 'retake_quiz' ];
					echo "<button type='button' class='fca_qc_button' id='fca_qc_restart_button' style='display: none;'>$button_text</button>";
					
				}?>
				
			</div>
			<?php
			
			return ob_get_clean();
		} else {
			return '<p>Quiz Cat: ' . __('No Quiz found', 'quiz-cat') . '</p>';
		}
	}
	add_shortcode( 'quiz-cat', 'fca_qc_do_quiz' );
	
	function fca_qc_maybe_add_custom_styles( $post_id ) {
		
		$quiz_appearance = get_post_meta ( $post_id, 'quiz_cat_appearance', true );
		
		if ( !empty( $quiz_appearance ) ) {
			$button_color = empty( $quiz_appearance['button_color'] ) ? '#58afa2' : $quiz_appearance['button_color'];
			$button_hover_color = empty( $quiz_appearance['button_hover_color'] ) ? '#3c7d73' : $quiz_appearance['button_hover_color'];
			$button_border_color = empty( $quiz_appearance['button_border_color'] ) ? '#3c7d73' : $quiz_appearance['button_border_color'];
			$answer_hover_color = empty( $quiz_appearance['answer_hover_color'] ) ? '#8dc8bf' : $quiz_appearance['answer_hover_color'];
			
			ob_start(); ?>
				<style>
					<?php echo "#fca_qc_quiz_$post_id" ?> button.fca_qc_button {
						background-color: <?php echo $button_color ?>;
						box-shadow: 0 2px 0 0 <?php echo $button_border_color ?>;
					}
					
					<?php echo "#fca_qc_quiz_$post_id" ?> button.fca_qc_button:hover {
						background-color: <?php echo $button_hover_color ?>;
					}
					
					<?php echo "#fca_qc_quiz_$post_id" ?> div.fca_qc_answer_div.fakehover,
					<?php echo "#fca_qc_quiz_$post_id" ?> div.fca_qc_answer_div:active {
						background-color: <?php echo $answer_hover_color ?>;
					}
					
				</style>
			<?php
			return ob_get_clean();
		}
		
		return false;
	}
	
	//SET UP THE MAIN QUIZ TEXTS FOR A QUIZ - CHECK FOR LOCALIZED STRINGS, THEN ANY PHP FILTERS, THEN SHORTCODES
	function fca_qc_set_quiz_text_strings( $atts ) {
		
		global $global_quiz_text_strings;

		$quiz_text_strings = apply_filters( 'fca_qc_quiz_text', $global_quiz_text_strings );
				
		$shortcode_text_strings = array (

			'no_quiz_found' => empty( $atts['no_quiz_found'] ) ? false : $atts['no_quiz_found'],
			'correct' => empty( $atts['correct'] ) ? false : $atts['correct'],
			'wrong' => empty( $atts['wrong'] ) ? false : $atts['wrong'],
			'your_answer' => empty( $atts['your_answer'] ) ? false : $atts['your_answer'],
			'correct_answer' => empty( $atts['correct_answer'] ) ? false : $atts['correct_answer'],
			'question' => empty( $atts['question'] ) ? false : $atts['question'],
			'next' =>  empty( $atts['next'] ) ? false : $atts['next'],
			'you_got' =>  empty( $atts['you_got'] ) ? false : $atts['you_got'],
			'out_of' => empty( $atts['out_of'] ) ? false : $atts['out_of'],
			'your_answers' => empty( $atts['your_answers'] ) ? false : $atts['your_answers'],
			'start_quiz' => empty( $atts['start_quiz'] ) ? false : $atts['start_quiz'],
			'retake_quiz' => empty( $atts['retake_quiz'] ) ? false : $atts['retake_quiz'],
			'share_results' => empty( $atts['share_results'] ) ? false : $atts['share_results'],
			'i_got' => empty( $atts['i_got'] ) ? false : $atts['i_got'],
			'skip_this_step' => empty( $atts['skip_this_step'] ) ? false : $atts['skip_this_step'],
			'your_name' => empty( $atts['your_name'] ) ? false : $atts['your_name'],
			'your_email' => empty( $atts['your_email'] ) ? false : $atts['your_email'],
		
		);
		
		//CHECK SHORTCODES FOR TRANSLATIONS & OVERWRITE
		forEach ( $quiz_text_strings as $key => $value ) {
			if ( !empty ( $shortcode_text_strings[$key] ) && $shortcode_text_strings[$key] !== false ) {
				$quiz_text_strings[$key] = $shortcode_text_strings[$key];
			}			
		}
		
		return $quiz_text_strings;
		
	}

	function fca_qc_do_question_panel( $post_id, $quiz_text_strings ) {
		
		$max_questions = 4;
					
		$questions = get_post_meta ( $post_id, 'quiz_cat_questions', true );
		
		forEach ( $questions as $question ) {
			if ( count ( $question['answers'] ) > $max_questions ) {
				$max_questions = count ( $question['answers'] );
			}
		}

		$html = "<div class='fca-qc-front' id='fca_qc_answer_container'>";
			$html .= "<p id='fca_qc_question'>" . $quiz_text_strings['question'] . "</p>";
			$html .= "<img class='fca_qc_quiz_question_img' src=''>";
			for ( $i = 1; $i <= $max_questions; $i++ ) {
				$html .= "<div class='fca_qc_answer_div' data-question='$i'>";
				$html .= "<img class='fca_qc_quiz_answer_img' src=''>";
				$html .= "<span class='fca_qc_answer_span'></span></div>";
				
			}
			
		$html .= "</div>";
		
		return $html;

	}

	function fca_qc_do_answer_panel( $quiz_text_strings ) {
		
		$html = "<div class='fca-qc-back' id='fca_qc_back_container'>";
			$html .= "<p id='fca_qc_question_right_or_wrong'></p>";
			$html .= "<img class='fca_qc_quiz_question_img' src=''>";
			$html .= "<span id='fca_qc_question_back'></span>";
			$html .= "<p id='fca_qc_back_response_p' class='fca_qc_back_response'>" . $quiz_text_strings['your_answer'] . " <span id='fca_qc_your_answer'></span></p>";
			$html .= "<p id='fca_qc_correct_answer_p' class='fca_qc_back_response'>" . $quiz_text_strings['correct_answer'] . " <span id='fca_qc_correct_answer'></span></p>";
			$html .= "<p id='fca_qc_hint_p' class='fca_qc_back_response'><span id='fca_qc_hint'></span></p>";
			$html .= "<button type='button' class='fca_qc_next_question'>" . $quiz_text_strings['next'] . "</button>";
		$html .= "</div>";
		
		return $html;

	}

	function fca_qc_do_score_panel( $post_id, $quiz_text_strings ) {
		
		$html = "<div class='fca_qc_score_container' style='display:none;'>";
			$html .= "<p class='fca_qc_score_text'>" . $quiz_text_strings['you_got'] . " {{SCORE_CORRECT}} " . $quiz_text_strings['out_of'] . " {{SCORE_TOTAL}} </p>";
			$html .= "<h3 class='fca_qc_score_title'></h3>";
			$html .= "<img class='fca_qc_score_img' src=''>";
			$html .= "<p class='fca_qc_score_desc'></p>";			
		$html .= "</div>";
		
		return apply_filters ( 'fca_qc_result_filter', $html, $post_id, $quiz_text_strings );

	}

	function fca_qc_do_your_answers_panel( $quiz_text_strings ) {
		
		$html = "<div class='fca_qc_your_answer_container' style='display:none;'>";
			$html .= "<p class='fca_qc_your_answers_text'>" . $quiz_text_strings['your_answers'] . "</p>";
			//THIS IS WHERE EACH RESPONSE WILL BE INSERTED
			$html .= "<div class='fca_qc_insert_response_above'></div>";
		$html .= "</div>";
		
		return $html;

	}

	function fca_qc_convert_entities ( $array ) {
		$array = is_array($array) ? array_map('fca_qc_convert_entities', $array) : html_entity_decode( $array, ENT_QUOTES );

		return $array;
	}
	//OUTPUTS HTML FOR IMAGE ADD/CHANGE
	function fca_qc_add_image_input($img = '', $name = '', $id = '', $hidden = false) {
		$hidden = $hidden ? "style='display:none;'" : '';
		
		$html = '';
		
		$html .= "<input type='text' class='fca_qc_image_input' name='$name' id='$id' style='display: none;' value='$img'>";
		$html .= "<button $hidden title='" . __('Adds an image (optional).  For best results, use images at least 250px wide and use the same image resolution for each image you add to an answer.', 'quiz-cat') . "' type='button' class='button-secondary fca_qc_quiz_image_upload_btn'>" . __('Add Image', 'quiz-cat') . "</button>";
		$html .= "<img class='fca_qc_image' style='max-width: 252px' src='$img'>";
			
		$html .= "<div class='fca_qc_image_hover_controls'>";
			
			//IF PLACEHOLDER IS THERE DON'T SHOW THE "REMOVE OR CHANGE" BUTTON
			if ( empty ( $img ) ) {
				$html .= "<button type='button' class='button-secondary fca_qc_quiz_image_change_btn' $hidden>" . __('Change', 'quiz-cat') . "</button>";
				$html .= "<button type='button' class='button-secondary fca_qc_quiz_image_revert_btn' $hidden>" . __('Remove', 'quiz-cat') . "</button>";
			}else {
				$html .= "<button type='button' class='button-secondary fca_qc_quiz_image_change_btn'>" . __('Change', 'quiz-cat') . "</button>";
				$html .= "<button type='button' class='button-secondary fca_qc_quiz_image_revert_btn'>" . __('Remove', 'quiz-cat') . "</button>";
			}
			
		$html .=  '</div>';
		
		return $html;
	}
	
	
	
	function fca_qc_add_wysiwyg ( $value = '', $name = '' ) {
		
		$html = '';
			$html .= "<div class='fca-wysiwyg-nav' style='display:none'>";
				$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-text-group">';
					$html .= '<button type="button" data-wysihtml5-command="bold" class="fca-nav-bold fca-nav-rounded-left" ><span class="dashicons dashicons-editor-bold"></span></button>';
					$html .= '<button type="button" data-wysihtml5-command="italic" class="fca-nav-italic fca-nav-no-border" ><span class="dashicons dashicons-editor-italic"></span></button>';
					$html .= '<button type="button" data-wysihtml5-command="underline" class="fca-nav-underline fca-nav-rounded-right" ><span class="dashicons dashicons-editor-underline"></span></button>';
				$html .= "</div>";
				$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-alignment-group">';
					$html .= '<button type="button" data-wysihtml5-command="justifyLeft" class="fca-nav-justifyLeft fca-nav-rounded-left" ><span class="dashicons dashicons-editor-alignleft"></span></button>';
					$html .= '<button type="button" data-wysihtml5-command="justifyCenter" class="fca-nav-justifyCenter fca-nav-no-border" ><span class="dashicons dashicons-editor-aligncenter"></span></button>';
					$html .= '<button type="button" data-wysihtml5-command="justifyRight" class="fca-nav-justifyRight fca-nav-rounded-right" ><span class="dashicons dashicons-editor-alignright"></span></button>';
				$html .= "</div>";
				
				$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-link-group">';
					$html .= '<button type="button" data-wysihtml5-command="createLink" style="border-right: 0;" class="fca-wysiwyg-link-group fca-nav-rounded-left"><span class="dashicons dashicons-admin-links"></span></button>';
					$html .= '<button type="button" data-wysihtml5-command="unlink" class="fca-wysiwyg-link-group fca-nav-rounded-right"><span class="dashicons dashicons-editor-unlink"></span></button>';
					$html .= '<div class="fca-wysiwyg-url-dialog" data-wysihtml5-dialog="createLink" style="display: none">';
						$html .= '<input data-wysihtml5-dialog-field="href" value="http://">';
						$html .= '<a class="button button-secondary" data-wysihtml5-dialog-action="cancel">' . __('Cancel', 'quiz-cat') . '</a>';
						$html .= '<a class="button button-primary" data-wysihtml5-dialog-action="save">' . __('OK', 'quiz-cat') . '</a>';
					$html .= "</div>";
				$html .= "</div>";
				
				$html .= '<button class="fca-wysiwyg-view-html action" type="button" data-wysihtml5-action="change_view">HTML</button>';
		
			$html .= "</div>";
			$html .= "<textarea class='fca-wysiwyg-html fca-lpc-input-wysi fca-lpc-$name' name='$name'>$value</textarea>";

		return $html;
	}

	/* Localization */
	function fca_qc_load_localization() {
		load_plugin_textdomain( 'quiz-cat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'fca_qc_load_localization' );
	
	
	////////////////////////////
	// RANDOM STUFF
	////////////////////////////
	
	function fca_qc_remove_screen_options_tab ( $show_screen, $screen ) {
		if ( $screen->id == 'fca_qc_quiz' ) {
			return false;
		}
		return $show_screen;
	}	
	add_filter('screen_options_show_screen', 'fca_qc_remove_screen_options_tab', 10, 2);
	
	function fca_qc_tooltip( $text = 'Tooltip', $icon = 'dashicons dashicons-editor-help' ) {
		return "<span class='$icon fca_qc_tooltip' title='" . htmlentities($text) . "'></span>";
	}
	
	function fca_qc_maybe_show_merge_tag_notice ( $posts ) {
		
		$dismissed = get_option( 'fca_qc_dismissed_merge_tag_change_notice' );
		if ( $dismissed !== true ) {
			forEach ( $posts as $post ) {
				$optin_settings = get_post_meta( $post->ID, 'quiz_cat_optins', true );
				if ( !empty ( $optin_settings['provider'] ) ) {
					if ( $optin_settings['provider'] === 'mailchimp' ) {
						update_option( 'fca_qc_dismissed_merge_tag_change_notice', $optin_settings['provider'] );
						return false;
					}
				}
			}
		}
	}
	
	function fca_qc_convert_question_meta( $posts ) {
		
		forEach ( $posts as $post ) {
			
			$settings = get_post_meta( $post->ID, 'quiz_cat_settings', true );
			
			//set default quiz type to 'mc' for multiple choice
			$settings['quiz_type'] = empty ( $settings['quiz_type'] ) ? 'mc' : $settings['quiz_type'];
			
			update_post_meta( $post->ID, 'quiz_cat_settings', $settings );
			
			$questions = get_post_meta( $post->ID, 'quiz_cat_questions', true );

			if ( !empty ( $questions[0]['answer'] ) ) {
				$new_questions = array();
				
				foreach ( $questions as $question ) {
					
					$answers = array(
						array(
							'answer' => empty ( $question['answer'] ) ? '' : $question['answer'],
							'img' => empty ( $question['imgAnswer1'] ) ? '' : $question['imgAnswer1'],
						)
					);
					
					for ( $i = 1; $i < 4; $i++ ) {
						$answerKey = "wrong$i";
						$imgKey = "imgAnswer" . ( $i + 1 );
						if ( !empty ( $question[$answerKey] ) OR !empty( $question[$imgKey] ) ) {
							$answers[] = array(
								'answer' => empty ( $question[$answerKey] ) ? '' : $question[$answerKey],
								'img' => empty ( $question[$imgKey] ) ? '' : $question[$imgKey],
							);
						}
					}
						
					$new_questions[] = array(
						'question' => empty ( $question['question'] ) ? '' : $question['question'],
						'img' => empty ( $question['img'] ) ? '' : $question['img'],
						'answers' => $answers,					
					);
					
				}
				update_post_meta( $post->ID, 'quiz_cat_questions', $new_questions );
				
			}

		}
	}
	
	function fca_qc_convert_csv() {
		
		$meta_version = get_option ( 'fca_qc_meta_version' );
		
		if ( $meta_version !== '1.3.1' ) {
			
			$upload_dir = wp_upload_dir();
			$upload_dir = $upload_dir['basedir'] . '/quizcat/*';
			
			//CONVERT COMMAS TO TABS
			
			forEach ( glob($upload_dir) as $file ) {
				$str = file_get_contents ( $file );
				$str = str_replace( ",", "\t", $str );
				$str = html_entity_decode( $str, ENT_QUOTES, 'UTF-8');
				file_put_contents( $file, $str );
			}
			
			update_option( 'fca_qc_meta_version', '1.3.1');
		
		}

	}	
	
	function fca_qc_upgrade_quiz_tables ( $posts ) {
		
		global $wpdb;
		
		$old_table_name = $wpdb->prefix.'fca_qc_activity';
		
		$has_old_table = $wpdb->get_var("SHOW TABLES LIKE '$old_table_name'") !== null;
		
		$post_count = count ( $posts );
		
		$rows = 0;
		
		forEach ( $posts as $post ) {
			
			$quiz_id = $post->ID;
				
			//CREATE A ROW IN THE ACTIVITY TABLE FOR EACH QUIZ IF IT DOESNT EXIST
			if ( function_exists( 'fca_qc_insert_quiz_to_db' ) && !defined ( 'fca_qc_disable_activity' ) ) {
				fca_qc_insert_quiz_to_db( $quiz_id );
			}
			
			if( $has_old_table ) {
			
				$sql = esc_sql ("SELECT * FROM `$old_table_name` WHERE quiz_id = $quiz_id");
				$activity = $wpdb->get_row( $sql, ARRAY_A );
				
				$newActivity = array();
				
				$newActivity['impressions'] = empty ( $activity['impressions'] ) ? 0 : $activity['impressions'];
				$newActivity['starts'] = empty ( $activity['starts'] ) ? 0 : $activity['starts'];
				$newActivity['optins'] = empty ( $activity['optins'] ) ? 0 : $activity['optins'];
				$newActivity['completions'] = empty ( $activity['completions'] ) ? 0 : $activity['completions'];
				$newActivity['shares'] = empty ( $activity['shares'] ) ? 0 : $activity['shares'];
				
				$results = empty ( $activity['results'] ) ? array() : json_decode ( $activity['results'] );
				
				$rows += $wpdb->update( fca_qc_table(), array('stats' => json_encode ( $newActivity ), 'results' => json_encode ( $results ) ), array('quiz_id' => $quiz_id) );
			
			}

		}
		
		if ( $rows === $post_count && $has_old_table ) {
			//CONVERSION OK, REMOVE OLD TABLE
			$sql = esc_sql ("DROP TABLE `$old_table_name`");
			$rows = $wpdb->query($sql);
		}

		
	}
}