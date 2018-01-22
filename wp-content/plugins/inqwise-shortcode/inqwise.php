<?php
/*
Plugin Name: Inqwise Shortcode
Description: Enables shortcode to embed Inqwise online surveys and forms. Usage: <code>[inqwise guid="036b6bbd-949e-4669-b880-26fac57963fd"]</code>. This code is available to copy and paste directly from the Inqwise.
Version: 1.0.1
License: GPL
Author: Basil Goldman / Inqwise
Author URI: http://www.inqwise.com
*/

function createInqwiseEmbedJS($atts, $content = null) {
	extract(shortcode_atts(array(
		'guid'   => '',
		'url_params'   => '',
		'entsource'  => 'wordpress'
	), $atts));
	
	if (!$guid) {

		$error = "
			<div style=\"border: 1px solid red; padding: 10px;\">
				<p style=\"margin: 0;\">Something is wrong with your Inqwise shortcode.</p>
			</div>";

		return $error;

	} else {
		
		$embed = "\n<script type=\"text/javascript\" src=\"//c7.inqwise.com/scripts/widget/1.1.2/survey.js\">\n";
		$embed .= "{\n";
		$embed .= "    \"guid\" : \"$guid\",\n";
		$embed .= "    \"collectorUrl\" : \"http://api.inqwise.com\",\n";
		$embed .= "    \"collectorSecureUrl\" : \"https://api.inqwise.com\",\n";
		$embed .= "    \"url\" : \"http://c7.inqwise.com/c/1/$guid/1\",\n";
		$embed .= "    \"entsource\" : \"$entsource\"\n";
		$embed .= "}\n";
		$embed .= "</script>\n";
		$embed .= "<noscript>\n";
		$embed .= "    <p>Please fill out my <a href=\"http://c7.inqwise.com/c/1/$guid/1\">survey</a>.</p>\n";
		$embed .= "</noscript>\n";
		
		/**
		* Return embed
		*/
		return $embed;

	}
}

add_shortcode('inqwise', 'createInqwiseEmbedJS');

?>
