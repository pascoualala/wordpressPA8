<?php
add_shortcode('surveyanyplace_embed', 'surveyanyplace_embed_iframe');

function surveyanyplace_embed_iframe($atts)
{
    extract(shortcode_atts(array(
        'url' => '',
        'height' => '600px',
        'width' => '100%',
        'type' => 'embed',
        'style' => '',
        'builder' => '',
        'button_text' => __('Start Survey!', 'surveyanyplace')
    ), $atts));

    /** if string doesn't contain units */
    if (strpos($height, '%') === false && strpos($height, 'px') === false) {
        $height = (string)$height . 'px';
    }

    if (strpos($width, '%') === false && strpos($width, 'px') === false) {
        $width = (string)$width . 'px';
    }

    $id = uniqid();

    ob_start();

    /** display the survey anyplace template */
    include(dirname(__DIR__) . '/templates/surveyanyplace.php');

    /** getting the form */
    $html = ob_get_contents();
    @ob_end_clean();

    /** Returning the output buffer */
    return $html;
}
