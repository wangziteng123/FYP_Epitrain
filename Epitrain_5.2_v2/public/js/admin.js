$(document).ready(function(){
	$(document).on('click', '[data-modal="requestquote"]', function(){sweetAlert({title: "Requesting quotes is avaible only to users", confirmButtonColor: "#2980b9", type: "info"}); });
	$(document).on('click', '[data-tcid="search-results"] .item [data-act="delete"]', function(){deleteItem($(this).closest('.item'));});
	$(document).on('click', '[data-tcid="custom-requests"] .request [data-act="decline"]', function(){declineRequest($(this).closest('.request'));});
	$(document).on('click', '[data-tcid="custom-requests"] .request [data-act="accept"]', function(){acceptRequest($(this).closest('.request'));});
	$(document).on('submit', '[data-tcid="custom-requests"] .request form', function(e){e.preventDefault(); acceptRequest($(this).closest('.request'));});
	$('[data-remodal-id="additem"] form').submit(function(e){e.preventDefault(); addItem($(this))});
	
	$.routes.add('/requests/', fetchRequests);
	
	tmpl.request = $.templates("[data-template='request-admin']");
});

function addItem(form){
	var submit = form.find('button[type=submit]');
	var alerts = form.siblings('.alerts');
	
	var query = new FormData(form[0]);
	query.append(submit.attr('name'), submit.text());
	
	alerts.html(tmpl.alert.render({type: 'info', text: "Please wait while file is getting uploaded."}));
	$.ajax({url: 'api/admin/addItem.php',
		type: 'POST',
		data: query,
		success: function(data){
			alerts.empty();
			switch (data.state){
				case 0: alerts.html(tmpl.alert.render({type: 'success', text: "<b>You have successfully added new item.</b>"})); form[0].reset(); break;
				case -1: alerts.html(tmpl.alert.render({type: 'error', text: "<b>Please fill in all fields.</b>"})); break;
				case -2: alerts.html(tmpl.alert.render({type: 'error', text: "<b><b>Invalid cost specified.</b> Cost must be in range of 0-999</b>"})); break;
				case -3: alerts.html(tmpl.alert.render({type: 'error', text: "<b>Invalid category specified.</b> Please select a proper category."})); break;
				case -4: alerts.html(tmpl.alert.render({type: 'error', text: "<b>There was an error uploading file or you haven't selected a file to upload.</b>"})); break;
				case -5: alerts.html(tmpl.alert.render({type: 'error', text: "<b>This file format is not allowed!</b>"})); break;
				case -6: alerts.html(tmpl.alert.render({type: 'error', text: "<b>The file you are trying to upload is too big.</b>"})); break;
				case -7: alerts.html(tmpl.alert.render({type: 'error', text: "<b>Error uploading file to server.</b>"})); break;
				default: alerts.html(tmpl.alert.render({type: 'error', text: "<b>There was an internal error, please try again.</b>"})); break;
			}
		},
		dataType: 'json',
		cache: false,
		contentType: false,
		processData: false
	}).fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function deleteItem(item){
	var id = item.data('itemid');
	$.post('api/admin/deleteItem.php', {id: id}, function(data){
		switch (data.state){
			case 0: item.closest('.item-container').removeClass('fadeIn').addClass('zoomOut');
					setTimeout(function(){item.closest('.item-container').remove(); if ($('[data-tcid="search-results"] .item-list .item-container').length == 0) $('[data-tcid="search-results"] .alerts').html(tmpl.alert.render({type: 'info', text: 'No more items to show for this search query.'}))}, 500); break;
			default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
		}
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function fetchRequests(){
	$('[data-role="content"]').load('pages/content/admin-customrequests.php', function(){
		$('[data-role="title"]').html('<h2 class="title">Custom requests</h2><p class="subtitle">view and handle custom quotes requests</p>');
		$('body').removeClass('main');
		var results = $('[data-tcid="custom-requests"] .requests-list');
		var alerts = $('[data-tcid="custom-requests"] .alerts');
		
		$.post('api/admin/requests.php', {get: true}, function(data){
			if (data.requests.length > 0){
				$.each(data.requests, function(k, v){
					results.append(tmpl.request.render(v));
				});
			} else {
				alerts.html(tmpl.alert.render({type: 'info', text: "No new requests to handle"}));
			}
		}, 'json').fail(function(){
			alerts.html(tmpl.alert.render({type: 'error', text: "There was an internal error while loading this content, please try again."}));
		});
	});
}

function declineRequest(req){
	var id = req.data('reqid');
	$.post('api/admin/requests.php', {id: id, decline: true}, function(data){
		switch(data.state){
			case 0: req.removeClass('fadeIn').addClass('zoomOut');
					setTimeout(function(){req.remove(); if ($('[data-tcid="custom-requests"] .requests-list .request').length == 0) $('[data-tcid="custom-requests"] .alerts').html(tmpl.alert.render({type: 'info', text: "No new requests to handle"}));}, 500); break;
			default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
		}
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function acceptRequest(req){
	var id = req.data('reqid');
	var form = req.find('form');
	var query = new FormData(form[0]);
	query.append('accept', 'true');
	query.append('id', id);
	
	sweetAlert({title: "Please wait while file is getting uploaded.", confirmButtonColor: "#2980b9", type: "info"});
	$.ajax({url: 'api/admin/requests.php',
		type: 'POST',
		data: query,
		success: function(data){
			switch (data.state){
				case 0: sweetAlert({title: "Request accepted", confirmButtonColor: "#aad122", type: "success"});
						req.removeClass('fadeIn').addClass('zoomOut');
						setTimeout(function(){req.remove(); if ($('[data-tcid="custom-requests"] .requests-list .request').length == 0) $('[data-tcid="custom-requests"] .alerts').html(tmpl.alert.render({type: 'info', text: "No new requests to handle"}));}, 500); break;
				case -1: sweetAlert({title: "There was an error uploading file or you haven't selected a file to upload.", confirmButtonColor: "#c0392b", type: "error"}); break;
				case -2: sweetAlert({title: "This file format is not allowed!", confirmButtonColor: "#c0392b", type: "error"}); break;
				case -3: sweetAlert({title: "The file you are trying to upload is too big.", confirmButtonColor: "#c0392b", type: "error"}); break;
				case -4: sweetAlert({title: "Error uploading file to server.", confirmButtonColor: "#c0392b", type: "error"}); break;
				default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"}); break;
			}
			if ($('[data-tcid="custom-requests"] .requests-list .request').length == 0) $('[data-tcid="custom-requests"] .alerts').html(tmpl.alert.render({type: 'info', text: "No new requests to handle"}));
		},
		dataType: 'json',
		cache: false,
		contentType: false,
		processData: false
	}).fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}