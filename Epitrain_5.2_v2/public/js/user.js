$(document).ready(function(){
	$('[data-remodal-id="requestquote"] form').submit(function(e){e.preventDefault(); requestQuote($(this))});
	$(document).on('click', '.item-list .item [data-act="like"]', function(){likeItem($(this).closest('.item'));});
	$(document).on('click', '.item-list .item [data-act="buy"]', function(){addToCart($(this).closest('.item'));});
	$(document).on('click', '.cart-item-list .item [data-act="remove"]', function(){removeFromCart($(this).closest('.item'));});
	$(document).on('click', '[data-act="empty-cart"]', function(){emptyCart();});
	$(document).on('click', '[data-act="finish-cart"]', function(){finishCart();});
	setInterval(getRequestUpdate, 5000);
	
	$.routes.add('/cart/', fetchCart);
	$.routes.add('/myitems/', fetchMyItems);
	tmpl.cartitem = $.templates("[data-template='cart-item']");
	tmpl.request = $.templates("[data-template='request-user']");
});

function fetchCart(){
	$('[data-role="content"]').load('pages/content/user-shoppingcart.php', function(){
		$('body').removeClass('main');
		$('[data-role="title"]').html('<h2 class="title">Shopping Cart</h2><p class="subtitle">review your cart before your next step</p>');
		var results = $('[data-tcid="shopping-cart-results"] .item-list');
		var alerts = $('[data-tcid="shopping-cart-results"] .alerts');
		$.post('api/user/getItems.php', {getcart: true}, function(data){
			alerts.empty();
			var total_price = 0;
			if (data.items.length > 0){
				$.each(data.items, function(key, item){
					results.append(tmpl.cartitem.render(item));
					total_price += parseInt(item.Cost);
				});
			} else {
				alerts.html(tmpl.alert.render({type: 'info', text: 'Your cart is empty'}));
			}
			$('[data-role="total-price"]').html(total_price);
		}, 'json').fail(function(){
			alerts.html(tmpl.alert.render({type: 'error', text: 'There was an internal erorr while fetching this page. Please refresh the page.'}));
		});
	});
}

function fetchMyItems(){
	$('[data-role="content"]').load('pages/content/user-myitems.php', function(){
		$('body').removeClass('main');
		$('[data-role="title"]').html('<h2 class="title">My items</h2><p class="subtitle">view the items you purchased</p>');
		var results1 = $('[data-tcid="my-items-purchased-results"] .item-list');
		var results2 = $('[data-tcid="my-items-custom-results"] .requests-list');
		var alerts1 = $('[data-tcid="my-items-purchased-results"] .alerts');
		var alerts2 = $('[data-tcid="my-items-custom-results"] .alerts');
		alerts1.html(tmpl.alert.render({type: 'info', text: 'Please wait while we are fetching your items.'}));
		alerts2.html(tmpl.alert.render({type: 'info', text: 'Please wait while we are fetching your items.'}));
		$.post('api/user/getItems.php', {getmyitems: true}, function(data){
			$('[data-role="requestStatus"] span').removeClass('fadeIn').addClass('fadeOut');;
			alerts1.empty();
			alerts2.empty();
			if (data.purchases.length > 0){
				$.each(data.purchases, function(key, item){
					item.Dl = true;
					results1.append(tmpl.item.render(item));
				});
			} else {
				alerts1.html(tmpl.alert.render({type: 'info', text: 'You havent purchased any items yet.'}));
			}
			if (data.requests.length > 0){
				$.each(data.requests, function(key, item){
					results2.append(tmpl.request.render(item));
				});
			} else {
				alerts2.html(tmpl.alert.render({type: 'info', text: 'You havent requested any items yet.'}));
			}
		}, 'json').fail(function(){
			alerts1.html(tmpl.alert.render({type: 'error', text: 'There was an internal erorr while fetching this content. Please refresh the page.'}));
			alerts2.html(tmpl.alert.render({type: 'error', text: 'There was an internal erorr while fetching this content. Please refresh the page.'}));
		});
	});
}

function requestQuote(form){
	var submit = form.find('button[type=submit]');
	var alerts = form.siblings('.alerts');
	var query = form.serialize() + '&' + encodeURI(submit.attr('name')) + '=' + encodeURI(submit.text());
	
	alerts.empty();
	$.post('api/user/requestQuote.php', query, function(data){
		switch (data.state){
			case 0: alerts.append(tmpl.alert.render({type: 'success', text: "<b>You have successfully submitted a custom request.</b>"})); form[0].reset(); break;
			case -1: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Please fill in all fields.</b>"})); break;
			case -2: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Too low budget</b>, minimum budget for custom requests is $30"})); break;
			case -3: alerts.append(tmpl.alert.render({type: 'error', text: "<b>Deadline must be in future.</b>"})); break;
			default: alerts.append(tmpl.alert.render({type: 'error', text: "<b>There was an internal error</b>, please try again."})); break;
		}
	}, 'json').fail(function(){
		alerts.append(tmpl.alert.render({type: 'error', text: "<b>There was an internal error</b>, please try again."}));
	});
}

function likeItem(item){
	var likes_count = item.find('.likes .count');
	var id = item.data('itemid');
	$.post('api/user/like.php', {id: id}, function(data){
		switch (data.state){
			case 0: likes_count.text(parseInt(likes_count.text()) + 1); sweetAlert({title: "Liked.", confirmButtonColor: "#aad122", type: "success"}); break;
			case -1: sweetAlert({title: "You already liked this item.", confirmButtonColor: "#2980b9", type: "info"}); break;
			default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
		}
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function addToCart(item){
	var id = item.data('itemid');
	$.post('api/user/buy.php', {id: id, addtocart: true}, function(data){
		switch (data.state){
			case 0: sweetAlert({title: "Item added to cart.", confirmButtonColor: "#aad122", type: "success"}); break;
			case -1: sweetAlert({title: "You already purchased this item or have it in cart.", confirmButtonColor: "#2980b9", type: "info"}); break;
			default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
		}
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function removeFromCart(item){
	var id = item.data('itemid');
	$.post('api/user/buy.php', {id: id, removefromcart: true}, function(data){
		switch (data.state){
			case 0: item.closest('.item-container').removeClass('fadeIn').addClass('zoomOut');
					setTimeout(function(){item.closest('.item-container').remove(); if ($('[data-tcid="shopping-cart-results"] .item-list .item-container').length == 0) $('[data-tcid="shopping-cart-results"] .cart .alerts').html(tmpl.alert.render({type: 'info', text: 'Your cart is empty'}))}, 500); break;
			default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
		}
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function emptyCart(){
	$.post('api/user/buy.php', {emptycart: true}, function(data){
		sweetAlert({title: "Cart emptied.", confirmButtonColor: "#aad122", type: "success"});
		$('[data-tcid="shopping-cart-results"] .item-list').empty();
		$('[data-tcid="shopping-cart-results"] .alerts').html(tmpl.alert.render({type: 'info', text: 'Your cart is empty'}));
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function finishCart(){
	$.post('api/user/buy.php', {finishcart: true}, function(data){
		switch (data.state){
			case 0: sweetAlert({title: "Items purchased.", confirmButtonColor: "#aad122", type: "success"});
					$('[data-role="total-price"]').html(0);
					$('[data-tcid="shopping-cart-results"] .item-list').empty();
					$('[data-tcid="shopping-cart-results"] .alerts').html(tmpl.alert.render({type: 'info', text: 'Your cart is empty'})); break;
			case -2: sweetAlert({title: "Cart is empty.", confirmButtonColor: "#2980b9", type: "info"}); break;
			default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
		}
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}

function getRequestUpdate(){
	var elem = $('[data-role="requestStatus"] span');
	$.post('api/user/getRequestUpdate.php', function(data){
		if (data.count > 0) elem.html(data.count).removeClass('fadeOut').addClass('fadeIn');
		else elem.html(0).removeClass('fadeIn').addClass('fadeOut');
	}, 'json').fail(function(){
		elem.html(0).removeClass('fadeIn').addClass('fadeOut');
	});
}