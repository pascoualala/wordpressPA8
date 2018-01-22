<?php
/**  virtual console before initializing */
add_action('vc_before_init', 'surveyanyplace_embed_vc');

function surveyanyplace_embed_vc()
{
    vc_map(
        array(
            "name" => __("SurveyAnyplace", "surveyanyplace"),
            "base" => "sa_embed",
            "icon" => SA_BASE . "../../public_html/images/sa-logo.png",
            "category" => __("Content", "surveyanyplace"),
            "description" => __("Embed beautiful forms and surveys", "surveyanyplace"),
            "params" => array(
                array(
                    "type" => "textfield",
                    "class" => "",
                    "heading" => __("SurveyAnyplace URL", "surveyanyplace"),
                    "param_name" => "url",
                    "admin_label" => true,
                    "description" => __("Your surveyanyplace's url found in Share section.", "surveyanyplace")
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Embed Type", "surveyanyplace"),
                    "admin_label" => true,
                    "value" => array(
                        __("Embed", "surveyanyplace") => "embed",
                        __("Classic", "surveyanyplace") => "classic",
                        __("Drawer", "surveyanyplace") => "drawer"
                    ),
                    "param_name" => "type"
                ),
                array(
                    "type" => "textfield",
                    "class" => "",
                    "heading" => __("Width", "surveyanyplace"),
                    "param_name" => "width",
                    "dependency" => array(
                        "element" => "type",
                        "value" => array("embed")
                    )
                ),
                array(
                    "type" => "textfield",
                    "class" => "",
                    "heading" => __("Height", "surveyanyplace"),
                    "param_name" => "height",
                    "value" => "100px",
                    "dependency" => array(
                        "element" => "type",
                        "value" => array("embed")
                    )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Link style", "surveyanyplace"),
                    "value" => array(
                        __("Link", "surveyanyplace") => "link",
                        __("Button", "surveyanyplace") => "button"
                    ),
                    "param_name" => "style",
                    "dependency" => array(
                        "element" => "type",
                        "value" => array("classic", "drawer")
                    )
                ),
                array(
                    "type" => "textfield",
                    "class" => "",
                    "heading" => __("Button Text", "surveyanyplace"),
                    "param_name" => "button_text",
                    "dependency" => array(
                        "element" => "type",
                        "value" => array("classic", "drawer")
                    )
                )
            )
        )
    );
}
