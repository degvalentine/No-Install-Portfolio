// @todo wrap all js vars/funcs in portfolio object
var speed = 250;
var runIntro, isIntro = false;
var gid = 1;
var groupList = [];

$(function() {
	loadPage();
	
	// context menus
	var contextMenus = $('<div id="contextMenus" />');
	$('body').append(contextMenus);
	
	// load specific item if set
	if (window.location.hash) {
		var hash = window.location.hash.substring(1).split('-');
		var group = $('#group' + hash[0]);
		group.data('currentItem', hash[1]-1);
		displayItem(group);
	}
	else {
		intro();
	}
});

function loadPage() {
	$('#wrapper').hide();
	
	var topLevelMenu = $('.menu.top');
	
	// build menu
	$(groups).each(function(i, data) {
		var group = makeGroup(data, topLevelMenu, 0);
	});
	
	$('#wrapper').fadeIn(speed);
}

function intro() {
	switch (settings.intro) {
		case 'first':
			$('.menu.top li:first-child > .clickHandle').click();
			break;
		case 'cycle':
			runIntro = true;
			cycleIntro();
			break;
	}
}

function cycleIntro() {
	if (runIntro) {
		var randomGroup = groupList[Math.floor(Math.random() * (groupList.length-1))];
		if (!randomGroup.data('items') || !randomGroup.data('items').length) {
			cycleIntro();
			return;
		}
		randomGroup.data('currentItem', Math.ceil(Math.random() * (randomGroup.data('items').length-1)));
		isIntro = true;
		displayItem(randomGroup);
		setTimeout(cycleIntro, 6000);
	}
}

/**
 * Recursively creates jQuery <li> elements from data attached to parent element.
 * Represents a menu item with associated portfolio items and submenus.
 * Data about items in this group is stored on the <li> to be created on request
 * @param data an object with the following properties: title (string), items (array), children (array)
 * @param parent the parent <li> element
 * @returns jQuery <li> element
 */
function makeGroup(data, parentMenu, depth) {
	var id = data.gid? data.gid : gid++;
	
	// make group
	var group = $('<li />');
	
	// make title
	var title = $('<span class="title clickHandle">' + data.title + '</span>');
	title.click(displayCurrentItem)
		.click(displaySubMenu)
		.data('target', group);
	
	// make nav and counter
	var prev = $('<span class="counter prev">&lt;</span>');
	prev.click(function(e){
		e.stopPropagation();
		if (group.data('currentItem') == 0)
			group.data('currentItem', group.data('items').length-1);
		else
			group.data('currentItem', group.data('currentItem')-1);
		displayItem(group);
	});
	var next = $('<span class="counter next">&gt;</span>');
	next.click(function(e){
		e.stopPropagation();
		group.data('currentItem', (group.data('currentItem')+1)%group.data('items').length);
		displayItem(group);
	});
	
	// make submenu for children
	var menuId = 'menu' + id;
	var menu = $('<ul />');
	menu.attr('id', menuId)
		.data('gid', id)
		.data('parentMenu', parentMenu)
		.data('parentGroup', group)
		.addClass('menu')
		.addClass(menuId);
	
	// finish group
	group.data('title', data.title)
		 .data('items', data.items)
		 .data('gid', id)
		 .data('currentItem', 0)
		 .addClass('group')
		 .addClass('group' + id)
		 .attr('id', 'group' + id)
		 .append(title)
		 .append(prev)
		 .append('<span class="counter current" />')
		 .append(next);
	
	parentMenu.append(group);
	
	// switch on menu style
	if (settings.menu == 'inline') {
		group.append(menu);
	}
	else {
		var tierId = 'tier'+(depth+1);
		var tier = $('#menus #'+tierId);
		if (tier.length < 1) {
			tier = $('<div id="'+tierId+'" class="menuTier" />')
			$('#menus').append(tier);
		}
		tier.append(menu);
	}
	
	// if it has children, for styling 
	if (data.children && data.children.length > 0)
		group.addClass('parent');
	
	// recursively make children
	$(data.children).each(function(i, childData){
		makeGroup(childData, menu, depth+1);
	});
	
	// add menu's class to parent menus (for opening this menu)
	while (parentMenu != null) {
		parentMenu.addClass(menuId);
		if (parentGroup = parentMenu.data('parentGroup'))
			parentGroup.addClass('group'+id);
		parentMenu = parentMenu.data('parentMenu');
	}
	
	// for animations
	groupList.push(group);
	
	return group;
}

function displaySubMenu(e) {
	var target = getTargetItem(e);
	$('li').removeClass('active')
	var activeMenus = $('.menu' + target.data('gid'));
	$('.menu').not(activeMenus).hide();
	
	$('.group' + target.data('gid')).addClass('active');
	activeMenus.each(function(i, el) {
		if ($(el).children('li').length > 0) {
			if (settings.effect == 'fade')
				$(el).fadeIn(speed);
			else if (settings.effect == 'slide')
				$(el).slideDown(speed);
			else
				$(el).show();
		}
	});
}

function displayCurrentItem(e) {
	var target = getTargetItem(e);
	target.data('currentItem', 0);
	displayItem(target);
}

function displayItem(target) {
	if (!isIntro)
		runIntro = false;
	isIntro = false;
	
	// hide items
	$('.item').hide().removeClass('active');
	$('.counter').hide();
	
	// return if nothing to show 
	if (target.data('items').length < 1)
		return;
	
	$('.loader').remove();
	
	// set window location for direct access
	var itemId = target.data('gid') + '-' + (target.data('currentItem')+1);
	window.location.hash = itemId;
	
	// display item
	if (target.data('items').length > 1) {
		target.children('.counter.current').text((target.data('currentItem') + 1) + '/' + target.data('items').length);
		target.children('.counter').show();
	}
	var itemId = target.data('gid') + 'Item' + target.data('currentItem');
	var itemDiv = $('#'+itemId);
	if (itemDiv.length < 1) {
		var data = target.data('items')[target.data('currentItem')];
		
		// make item
		itemDiv = $('<div />');
		itemDiv.data('target', target)
			   .attr('id', itemId)
			   .addClass('item')
			   .addClass('active')
			   .addClass('media-' + data.type)
			   .addClass(target.data('gid') + 'Item')
			   .hide();
		
		// embed media
		if (data.type == 'html') {
			$('#canvas').append(itemDiv);
			itemDiv
				.append(data.text)
				.fadeIn(speed);
		}
		else if (requiresDimensions(data.type)) {
			itemDiv.addClass('media').show();
			$('#canvas').append(itemDiv);
			
			var mediaId = 'media' + target.data('gid');
			var mediaLink = $('<a />');
			mediaLink
				.attr('href', data.src)
				.attr('id', mediaId);
			itemDiv.append(mediaLink);
			
			// add text
			if (data.text) {
				var text = $('<p style="overflow:visible" />');
				text.text(data.text);
				itemDiv.append(text);
			}
			
			mediaLink.media({width:data.width, height:data.height, src:webBase+'/'+data.src, bgColor:settings.color});
		}
		else {
			// loading spinner
			var loaderId = "loader-" + itemId;
			var loader = $('<div class="loader" id="'+loaderId+'" />');
			$('#canvas').append(loader);
			loader.media({width:60, height:60, src:webBase+'/lib/loader/loader.swf', bgColor:settings.color});
			
			var image = $('<img />');
			itemDiv.append(image);
			image
				.load(function(){
					$('#'+loaderId).remove();
					if (itemDiv.hasClass('active')) {
						itemDiv.fadeIn(speed);
					}
				})
				.click(function(){
					target.data('currentItem', (target.data('currentItem') + 1) % target.data('items').length);
					displayItem(target);
				});
			
			// add text
			if (data.text) {
				var text = $('<p style="overflow:visible" />');
				text.text(data.text);
				itemDiv.append(text);
			}
			
			// show
			$('#canvas').append(itemDiv);
			image.attr('src', data.src);
		}
	}
	else {
		itemDiv.addClass('active');
		if (itemDiv.hasClass('media'))
			itemDiv.fadeIn(speed);
		else
			itemDiv.show();
	}
}

function getTargetItem(e) {
	var target = $(e.target);
	if (target.data('target') != null)
		return target.data('target');
	return target;
}

function requiresDimensions(type) {
	if (type == 'png' || type == 'jpg' || type == 'jpeg' || type == 'gif' || type == 'html')
		return false;
	return true;
}

var rnd = 0;
function randomId() {
	return 'id-' + rnd++;
}
