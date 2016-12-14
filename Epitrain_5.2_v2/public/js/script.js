var tmpl = [];
$(document).ready(function(){
	window.location.hash = '';
	readyMainPage();
	$(window).resize(function(){animateMain();});
	$(document).on('opening', '.remodal', function (){selectBoxWidthFix($('.formselect .selectboxit-btn'));});
	$(document).on('closed', '.remodal', function (){$(this).find('.alerts').empty();});
	$('[data-role="mailer-subscribe"]').submit(function(e){e.preventDefault(); subscribeToMailer($(this));});
	
	selectBoxWidthFix($('.formselect .selectboxit-btn:visible'));
	
	$.routes.add('/', function(){
		$('body').addClass('main');
		$('[data-role="title"]').load('pages/titles/main.php', function(){readyMainPage();});
		$('[data-role="content"]').load('pages/content/main.php');
	});
	
	tmpl.item = $.templates("[data-template='item']");
	tmpl.alert = $.templates("[data-template='alert']");
});

function readyMainPage(){
	$('select:not(.tokenize').selectBoxIt({showEffect: "slideDown", hideEffect: "slideUp"});
	$('header .search-box form select.tokenize').tokenize({maxElements: 4, searchMaxLength: 20, placeholder: "Looking for... (4 queries max, separate with comma)", onAddToken: main_search, onRemoveToken: main_search});
	$('header .search-box form').submit(function(e){e.preventDefault(); main_search();});
	$('header .search-box form select:not(.tokenize)').change(function(){main_search();});
	$('header .search-box form input[type=reset]').click(function(){$('header .search-box form select.tokenize').tokenize().clear(); $('header .search-box select:not(.tokenize)').each(function(){$(this).data("selectBox-selectBoxIt").selectOption(0);})});
	animateMain(10);
}

function animateMain(to){
	setTimeout(function(){
		var el = $('header [data-role="main-wrap-end"]');
		if (el.length > 0){
			var browser_offset = el.position().top + parseInt(el.parents('.wrap').css('padding-bottom'));
			if (browser_offset > 0) $('header .browser').css('top', browser_offset);
		}
	}, (typeof to == 'undefined' ? 1100 : to));
};

function main_search(){
	var form = $('header .search-box form');
	var title = $('header .title');
	var results = $('[data-tcid="search-results"] .item-list');
	var alerts = $('[data-tcid="search-results"] .alerts');
	var query = form.serialize();
	
	if (query != "category=any&price=any&added=any&sort=any"){
		$('body').removeClass('main');
		if (typeof title.data('default-title') == 'undefined') title.data('default-title', title.html());
		$.post('api/searchItems.php', query, function(data){
			console.log(data);
			results.empty();
			title.html('<strong>' + data.count + (data.count == 1 ? ' result' : ' results') + '</strong> for: ');
			if (data.count > 0){
				alerts.empty();
				$.each(data.items, function(key, item){
					results.append(tmpl.item.render(item));
				});
			} else {
				alerts.html(tmpl.alert.render({type: 'info', text: 'No results found. Try a different search query.'})).hide().fadeIn();
			}
		}, 'json').fail(function(){
			alerts.html(tmpl.alert.render({type: 'error', text: 'There was an internal erorr while processing search request. Please try again.'}));
		});
	} else {
		$('body').addClass('main');
		title.html(title.data('default-title'));
	}
}

function selectBoxWidthFix(elems){
	elems.each(function(){
		var elem = $(this);
		elem.width(elem.closest('.formselect').width() - (parseInt(elem.css('padding-left')) + parseInt(elem.css('padding-right'))));
	})
}

function subscribeToMailer(form){
	console.log(form.serialize());
	$.post('api/mailersubscribe.php', form.serialize() , function(data){
		console.log(data);
		switch(data.state){
			case 0: sweetAlert({title: "Successfully subscribed.", confirmButtonColor: "#aad122", type: "success"}); break;
			case -1: sweetAlert({title: "Incorrect email address format.", confirmButtonColor: "#c0392b", type: "error"}); break;
			case -2: sweetAlert({title: "This email address is already subscribed", confirmButtonColor: "#c0392b", type: "error"}); break;
			default: sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
		}
	}, 'json').fail(function(){
		sweetAlert({title: "There was an internal error, please try again.", confirmButtonColor: "#c0392b", type: "error"});
	});
}