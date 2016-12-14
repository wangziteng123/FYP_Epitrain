$(document).ready(function(){
	$('[data-remodal-id="register"] form').submit(function(e){e.preventDefault(); register($(this))});
	$('[data-remodal-id="login"] form').submit(function(e){e.preventDefault(); login($(this))});
});

function register(form){
	var submit = form.find('button[type=submit]');
	var alerts = form.siblings('.alerts');
	var query = form.serialize() + '&' + encodeURI(submit.attr('name')) + '=' + encodeURI(submit.text());
	
	alerts.empty();
	$.post('api/guest/register.php', query, function(data){
		switch (data.state){
			case 0: alerts.append(tmpl.alert.render({type: 'success', text: "<b>You have been registered</b>, please <a href='#login'><b>sign in</b></a> now."})); break;
			case -1: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Please fill in all fields.</b>"})); break;
			case -2: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Invalid login format</b>, login should be 3-20 alphanumeric characters."})); break;
			case -3: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Invalid password format</b>, password should be at least 6 characters."})); break;
			case -4: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Your passwords do not match.</b>"})); break;
			case -5: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Invalid email address format, or email address too long (more than 100 characters).</b>"})); break;
			case -6: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Username already used</b>"})); break;
			case -7: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Email address already used</b>"})); break;
			default: alerts.append(tmpl.alert.render({type: 'error', text: "<b>There was an internal error</b>, please try again."})); break;
		}
	}, 'json').fail(function(){
		alerts.append(tmpl.alert.render({type: 'error', text: "<b>There was an internal error</b>, please try again."}));
	});
}

function login(form){
	var submit = form.find('button[type=submit]');
	var alerts = form.siblings('.alerts');
	var query = form.serialize() + '&' + encodeURI(submit.attr('name')) + '=' + encodeURI(submit.text());
	
	alerts.empty();
	$.post('api/guest/login.php', query, function(data){
		switch (data.state){
			case 0: alerts.append(tmpl.alert.render({type: 'success', text: "<b>You have been logged in</b>, you will be redirected shortly."})); setTimeout(function(){window.location = window.location.href.split('#')[0];}, 2000); break;
			case -1: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Please fill in all fields.</b>"})); break;
			case -2: alerts.append(tmpl.alert.render({type: 'error', text: "<b><b>The details you have entered appear to be incorrect.</b>"})); break;
			case -3: window.location = window.location.href.split('#')[0]; break;
			default: alerts.append(tmpl.alert.render({type: 'error', text: "<b>There was an internal error</b>, please try again."})); break;
		}
	}, 'json').fail(function(){
		alerts.append(tmpl.alert.render({type: 'error', text: "<b>There was an internal error</b>, please try again."}));
	});
}