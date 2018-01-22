<?php
	
function fca_qc_splash_page() {

	add_submenu_page(
		null,
		__('Activate', 'quiz-cat'),
		__('Activate', 'quiz-cat'),
		'manage_options',
		'quiz-cat-splash',
		'fca_qc_render_splash_page'
	);

}
add_action( 'admin_menu', 'fca_qc_splash_page' );

function fca_qc_render_splash_page() {
		
	wp_enqueue_style('fca_qc_splash_css', FCA_QC_PLUGINS_URL . '/assets/admin/css/splash.min.css', false, FCA_QC_PLUGIN_VER );
	wp_enqueue_script('fca_qc_splash_js', FCA_QC_PLUGINS_URL . '/assets/admin/js/splash.min.js', false, FCA_QC_PLUGIN_VER, true );
		
	$user = wp_get_current_user();
	$name = empty( $user->user_firstname ) ? '' : $user->user_firstname;
	$email = $user->user_email;
	$site_link = '<a href="' . get_site_url() . '">'. get_site_url() . '</a>';
	$website = get_site_url();
	
	echo '<form method="post" action="' . admin_url( '/admin.php?page=quiz-cat-splash' ) . '">';
		echo '<div id="fca-logo-wrapper">';
			echo '<div id="fca-logo-wrapper-inner">';
				echo '<img id="fca-logo-text" src="' . FCA_QC_PLUGINS_URL . '/assets/fatcatapps-logo-text.png' . '">';
			echo '</div>';
		echo '</div>';
		
		echo "<input type='hidden' name='fname' value='$name'>";
		echo "<input type='hidden' name='email' value='$email'>";
		
		echo '<div id="fca-splash">';
			echo '<h1>' . __( 'Welcome to Quiz Cat', 'quiz-cat' ) . '</h1>';
			
			echo '<div id="fca-splash-main" class="fca-splash-box">';
				echo '<p id="fca-splash-main-text">' .  sprintf ( __( 'In order to enjoy all our features and functionality, Quiz Cat needs to connect %1$s your user, %2$s at %3$s, to <strong>api.fatcatapps.com</strong>.', 'quiz-cat' ), '<br>', '<strong>' . $name . '</strong>', '<strong>' . $website . '</strong>'  ) . '</p>';
				echo "<button type='submit' id='fca-qc-submit-btn' class='fca-qc-button button button-primary' name='fca-qc-submit-optin' >" . __( 'Connect Quiz Cat', 'quiz-cat') . "</button><br>";
				echo "<button type='submit' id='fca-qc-optout-btn' name='fca-qc-submit-optout' >" . __( 'Skip This Step', 'quiz-cat') . "</button>";
			echo '</div>';
			
			echo '<div id="fca-splash-permissions" class="fca-splash-box">';
				echo '<a id="fca-splash-permissions-toggle" href="#" >' . __( 'What permission is being granted?', 'quiz-cat' ) . '</a>';
				echo '<div id="fca-splash-permissions-dropdown" style="display: none;">';
					echo '<h3>' .  __( 'Your Website Info', 'quiz-cat' ) . '</h3>';
					echo '<p>' .  __( 'Your URL, WordPress version, plugins & themes. This data lets us make sure this plugin always stays compatible with the most popular plugins and themes.', 'quiz-cat' ) . '</p>';
					
					echo '<h3>' .  __( 'Your Info', 'quiz-cat' ) . '</h3>';
					echo '<p>' .  __( 'Your name and email.', 'quiz-cat' ) . '</p>';
					
					echo '<h3>' .  __( 'Plugin Usage', 'quiz-cat' ) . '</h3>';
					echo '<p>' .  __( "How you use this plugin's features and settings. This data helps us learn which features are most popular, so we can improve the plugin further.", 'quiz-cat' ) . '</p>';				
				echo '</div>';
			echo '</div>';
			

		echo '</div>';
	
	echo '</form>';
	
	echo '<div id="fca-splash-footer">';
		echo '<a target="_blank" href="https://fatcatapps.com/legal/terms-service/">' . _x( 'Terms', 'as in terms and conditions', 'quiz-cat' ) . '</a> | <a target="_blank" href="https://fatcatapps.com/legal/privacy-policy/">' . _x( 'Privacy', 'as in privacy policy', 'quiz-cat' ) . '</a>';
	echo '</div>';
}

function fca_qc_admin_redirects() {

	if ( isset( $_POST['fca-qc-submit-optout'] ) ) {
		update_option( 'fca_qc_activation_status', 'disabled' );
		wp_redirect( admin_url( '/edit.php?post_type=fca_qc_quiz' ) );
		exit;
	} else if ( isset( $_POST['fca-qc-submit-optin'] ) ) {
		update_option( 'fca_qc_activation_status', 'active' );
		
		$email = urlencode ( sanitize_email ( $_POST['email'] ) );
		$name = urlencode ( esc_textarea ( $_POST['fname'] ) );
		$url =  "https://api.fatcatapps.com/api/quizcat.php?email=$email&fname=$name";
		$return = wp_remote_get( $url );
	
		wp_redirect( admin_url( '/edit.php?post_type=fca_qc_quiz' ) );
		exit;
	}
	
	$status = get_option( 'fca_qc_activation_status' );
	if ( empty($status) && isset($_GET['post']) && get_post_type( $_GET['post'] ) === 'fca_qc_quiz' || empty($status) && isset($_GET['post_type']) && $_GET['post_type'] === 'fca_qc_quiz' ) {
        wp_redirect( admin_url( '/admin.php?page=quiz-cat-splash' ) );
		exit;
    }

}
add_action('admin_init', 'fca_qc_admin_redirects');

