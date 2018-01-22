<?php
include_once(dirname(__DIR__).'/library/scripts.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<?php
if (!defined('ABSPATH')) {
    die('Access denied.');
}

/** Convert Inserted full URL or shortcode to Valid URL
 *  Example inserted URL: https://surveyanyplace.com/s/aqtcuvli or aqtcuvli will be converted to https://surveyanyplace.com/s/aqtcuvli
 */
$url_fixed = "https://surveyanyplace.com/s/";
$full_url = apply_filters('surveyanyplace_embed_url', $url, $builder);
$pos = strrpos($full_url, '/');
$shortcode = $pos === false ? $full_url : substr($full_url, $pos + 1);
$url = $url_fixed . $shortcode;

/** type --> drawer */
if (in_array($type, array('drawer'))) {
    $style = ($style) ? $style : 'link';
    $type = ($type == 'drawer') ? 2 : 1;
    $button = ($button_text) ? $button_text : __('Start Survey!', 'surveyanyplace');
    ?>
    <div class="drawer_overlay" id="drawer_overlay" style="display:none;">
        <div id="drawer" class="drawer">
            <a href="javascript:void(0)" class="closebtn" onclick="surveyanyplace_closeNav()">&times;</a>
            <iframe src="<?php echo $url; ?>" name="drawer_frame" id="drawer_frame" width="100%" height="100%"
                    scrolling="no" frameborder="0"></iframe>
        </div>
    </div>
    <a class="<?php echo $style; ?>" target="drawer_frame" onclick="surveyanyplace_openNav()" href="<?php echo $url ?>"
       id="drawer_activator"><?php echo $button; ?></a>
    <?php
} /** type --> sametab/popup/newtab */
elseif (in_array($type, array('sametab', 'popup', 'newtab'))) {
    $style = ($style) ? $style : 'link';
    $button = ($button_text) ? $button_text : __('Start Survey!', 'surveyanyplace');

    if ($type == 'sametab') {
        ?>
        <div class="sametab_overlay" id="sametab_overlay" style="display:none;">
            <div id="sametab" class="sametab">
                <a href="javascript:void(0)" class="closebtn_same" onclick="surveyanyplace_closeSame()">&times;</a>
                <iframe src="<?php echo $url; ?>" name="sametab_frame" id="sametab_frame" width="100%" height="100%"
                        scrolling="no" frameborder="0"></iframe>
            </div>
        </div>
        <a class="<?php echo $style; ?>" target="sametab_frame" onclick="surveyanyplace_openSame()" href="<?php echo $url; ?>"
           id="sametab_activator"><?php echo $button; ?></a>
        <?php
    } elseif ($type == 'popup') {
        ?>
        <div class="popup_overlay" id="popup_overlay" style="display:none;">
            <div id="popup" class="popup">
                <a href="javascript:void(0)" class="closebtn_popup" onclick="surveyanyplace_closePopup()">&times;</a>
                <iframe src="<?php echo $url; ?>" name="popup_frame" id="popup_frame" width="100%" height="100%"
                        scrolling="no" frameborder="0"></iframe>
            </div>
        </div>
        <a class="<?php echo $style; ?>" target="popup_frame" onclick="surveyanyplace_openPopup()" href="<?php echo $url; ?>"
           id="popup_activator"><?php echo $button; ?></a>
        <?php
    } else {
        /** newtab */
        ?>
        <a class="<?php echo $style; ?>" target="_blank" href="<?php echo $url; ?>"><?php echo $button; ?></a>
        <?php
    }
} else {
    /** type --> embed */
    ?>
    <?php
    ($height) ? 'height:' . $height .  ';' : '';
     ($width) ? 'width:' . $width . ';' : '';
    ?>
    <iframe id="sa-embed-<?php echo $id; ?>" src="<?php echo $url; ?>" frameborder="0" style="max-height: inherit; max-width: inherit"; height="<?php  echo ($height); ?>": width="<?php echo ($width); ?>"></iframe>
    <?php
}
?>
</body>
</html>