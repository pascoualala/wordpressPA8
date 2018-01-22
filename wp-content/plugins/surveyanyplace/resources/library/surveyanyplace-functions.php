<?php
function surveyanyplace_plugin_url()
{
    return plugin_dir_url(__FILE__);
}
add_action('admin_enqueue_scripts', 'surveyanyplace_add_admin_scripts');

/** post.php is the blog page in the backend */
function surveyanyplace_add_admin_scripts($hook)
{
    $register_pages = array('post.php', 'post-new.php');
    if (!in_array($hook, $register_pages)) {
        return;
    }

    /** We can retrieve user data in this array, eg: Include email in email field */
    $surveyanyplaceObject = array();

    wp_register_script('sa_tinymce', surveyanyplace_plugin_url() . '../../public_html/js/surveyanyplace.js', array());

    /**  Objects */
    wp_localize_script('sa_tinymce', 'surveyanyplaceObject', $surveyanyplaceObject);

    /** Gives result a bleu colored square */
    wp_enqueue_script('sa_tinymce');

    /** Linking CSS for styling */
    wp_enqueue_style('style', surveyanyplace_plugin_url() . '../../public_html/css/style.css');

    add_editor_style(surveyanyplace_plugin_url() . '../../public_html/css/custom-editor-style.css');


}
/** Register and load the widget */
add_action('widgets_init', 'surveyanyplace_load_widget');
function surveyanyplace_load_widget()
{
    register_widget('surveyanyplace_embed_widget');
}



/**
 * Registers an editor stylesheet for the theme.
 */
//add_action( 'admin_init', 'surveyanyplace_add_editor_styles' );
//function surveyanyplace_add_editor_styles() {
////    add_editor_style( 'custom-editor-style.css' );
//    add_editor_style(surveyanyplace_plugin_url() . '../../public_html/css/custom-editor-style.css');
//
//}




add_action('wp_enqueue_scripts', 'surveyanyplace_front_add_editor_style');
function surveyanyplace_front_add_editor_style() {
    wp_enqueue_style( 'style', surveyanyplace_plugin_url() . '../../public_html/css/front-style.css' );
}


/** Here we add the media button
 * --> add Survey
 */
add_action('media_buttons', 'surveyanyplace_add_media_button');
function surveyanyplace_add_media_button()
{
    echo '<a href="#" id="add-surveyanyplace" class="button"><span></span>' . __(' Add Survey', 'surveyanyplace') . '</a>';
}

add_action('admin_print_footer_scripts', 'surveyanyplace_hidden_shortcode_html');
function surveyanyplace_hidden_shortcode_html()
{
    ?>
    <div class="sa-embed-wrapper" id="" style="display:none">
            <div class="sa-content">
                <span class="title"><?php _e('Edit your surveyanyplace', 'surveyanyplace'); ?></span>
                <a href="#" class="link" target="_blank"><?php _e('Placeholder', 'surveyanyplace'); ?></a>
            </div>
        </div>
    <?php
}
add_action('admin_enqueue_scripts', 'surveyanyplace_print_media_template');
function surveyanyplace_print_media_template()
{
    ?>
    <script type="text/html" id="tmpl-editor-sa-banner">
        <?php include('display_added_survey.php'); ?>
    </script>
    <?php
}

/**
 * Here we add the url
 * @param: $url
 * return: url
 */
add_filter('surveyanyplace_embed_url', 'surveyanyplace_add_query_url');
function surveyanyplace_add_query_url($url)
{
    if (!isset($_GET) || empty($_GET)) {
        return $url;
    }

    $ignore = array("preview_id", "preview_nonce", "post_format", "_thumbnail_id", "preview");
    $params = array_filter($_GET, function ($k) use ($ignore) {
        return !in_array($k, $ignore, true);
    }, ARRAY_FILTER_USE_KEY);
    $query = http_build_query($params);

    $separator = strlen($query) ? strpos($url, '?') === false ? '?' : '&' : '';

    return sprintf("%s%s%s", $url, $separator, $query);
}

add_filter('surveyanyplace_embed_url', 'surveyanyplace_builder_template', 5, 2);
function surveyanyplace_builder_template($url, $builder)
{
    return ($builder !== '') ? SURVEYANYPLACE_TEMPLATE_URL . '?' . $builder : $url;
}
