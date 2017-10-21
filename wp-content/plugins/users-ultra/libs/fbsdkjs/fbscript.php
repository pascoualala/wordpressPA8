<script type="text/javascript" >

window.fbAsyncInit = function() {
	//SDK loaded, initialize it
	FB.init({
		appId      : '<?php echo $api?>',
		xfbml      : true,
		version    : 'v2.2'
	});

	//check user session and refresh it
	FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
			//user is authorized
			//document.getElementById('loginBtn').style.display = 'none';
			//getUserData();
			
			
		} else {
			//user is not authorized
		}
	});
};

//load the JavaScript SDK
(function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.com/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

//add event listener to login button
document.getElementById('uupro-facebook-login-bt').addEventListener('click', function() {
	
	//do the login
	FB.login(function(response) {
		
		if (response.authResponse) {
			
					
			FB.api('/me', {fields: 'id,name,email'}, function(response) {
  				console.log(response);
							
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {action: "uupro_handle_social_facebook", 'name':response.name,  'email':response.email ,  'id':response.id  },
							
							success: function(data){
								
								window.location.href = data;
								
								}
						});
				
			});

			
		}
		
	}, {scope: 'email,public_profile', return_scopes: true});
}, false);



</script>