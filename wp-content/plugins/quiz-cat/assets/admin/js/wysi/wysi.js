/* jshint asi: true */

//////////////////
//WYSIWYG EDITOR
//////////////////


jQuery(document).ready(function($){
	fca_attach_wysiwyg()
})

function detectIE() {
	var ua = window.navigator.userAgent
	var msie = ua.indexOf('MSIE ')
	if (msie > 0) {
		// IE 10 or older
		return true
	}
	var trident = ua.indexOf('Trident/')
	if (trident > 0) {
		// IE 11
		return true
	}
	
	if (document.documentMode || /Edge/.test(navigator.userAgent)) {
		//EDGE
		return true
	}
	// other browser
	return false
}
var usingIE = detectIE()


var tidy_settings = {
	"indent": "auto",
	"indent-spaces": 2,
	"wrap": 80,
	"markup": true,
	"show-errors": 0,
	"show-warnings": false,
	"show-body-only": true,
	"drop-font-tags": false,
	"break-before-br": true,
	"uppercase-tags": false,
	"uppercase-attributes": false,
	"tidy-mark": false
}

function html_tidy( $target ){
	$target.val( tidy_html5( $target.val(), tidy_settings ) )
}

var wysihtmlParserRules = {
	"classes": {
		"wysiwyg-text-align-center": 1,
		"wysiwyg-text-align-left": 1,
		"wysiwyg-text-align-right": 1
	},
	'attributes': {
		'style': 'any',
		'class': 'any',
		'data-*': 'any',
	},
	
	'tags': {
		strong: 1,
		b:		1,
		i:		1,
		u:		1,
		div:	1,
		span:	1,
		ul:		1,
		li:		1,
		ol:		1,
		p:		1,
		br:		1,
		a:		{
			'set_attributes': {
				'target': '_blank',
				'rel':	'nofollow'
			},
			'check_attributes': {
				'href':	'url' // important to avoid XSS
			}
		}
	}
}

function fca_attach_wysiwyg() {
	var $ = jQuery
	if ( !usingIE ) {
		$('.fca-wysiwyg-html').not('.editorActive').each(function (index, element) {
			var editor = new wysihtml5.Editor( element, { // element
				toolbar:	  $(element).siblings('.fca-wysiwyg-nav')[0], // toolbar element
				parserRules:  wysihtmlParserRules, // defined in parser rules set
				stylesheets: [fcaQcAdminData.stylesheet],
				useLineBreaks:  false
			})
			html_tidy( $(element) )
			$(element).siblings('.fca-wysiwyg-nav').find('.fca-wysiwyg-view-html').click(function(){
				html_tidy( $(element) )
				$(this).siblings('.fca-wysiwyg-group').toggle()
			})
			$(element).addClass('editorActive')
			
		})
	} else {
		//DISABLE FOR IE
		$('.fca-wysiwyg-html').not('.editorActive').each(function (index, element) {
			html_tidy( $(element) )
			$(element).addClass('editorActive')
			$(element).siblings('.fca-wysiwyg-group').hide()
		})
		
	}
}
