/**
 * 
 */
$(function() {
	// administrative form
	$('#adminForm').submit(save);
	var backupSelect = $('select[name=backup]');
	for(i in backups)
		backupSelect.append('<option value="'+backups[i].filename+'">'+backups[i].date+'</option>');
	backupSelect.append('<option value="">empty portfolio</option>');
	
	// configuration form
	$('#configBtn').click(function(){ $('#config').slideToggle(100); return false; });
	$(document).click(function(){ $('#config').slideUp(100); });
	$('#config').click(function(e){ e.stopImmediatePropagation(); });
	
	// sorting
	$('.menu, #static ul')
		.sortable({axis:'y'})
		.disableSelection();
	
	// deleting
	addContextItem('#static li, #menus li', 'Delete', deleteItem);
	
	// logo admin
	addContextItem('#logo', 'Delete Logo', function(){$('#logo img').attr('src', '').hide()});
	var logoHandles = ['html', '#logo'];
	for (i in logoHandles) {
		var logoMenuItem = addContextItem(logoHandles[i], 'Change Logo')[0];
		logoMenuItem.attr('id', 'changeLogoLink'+i);
		new AjaxUpload('changeLogoLink'+i, {
			action: 'admin/upload',
			name: 'newImage',
			autoSubmit: true,
			onComplete: function(file, response){
				if (response != 'success') 
					return alert(response);
				$('#logo img').attr('src', 'content/' + file).show();
			}
		});
	}
	// edit menu column
	addContextItem('#menus li', 'Edit Group', editGroupHandler);
	addContextItem('#menus li', 'Add Sub Group', addGroupPopup);
	addContextItem('#menus li', 'Rename Group', renameGroupPopup);
	//@todo $('#menus li').dblclick(editGroupHandler);
	
	// general admin
	addContextItem('html', 'Add Menu Group', addGroupPopup);
	addContextItem('html, #static, #static li', 'Add Info', addStaticText);
	//@todo addContextItem('#wrapper', 'Add Image', addStaticImage);
	
	// input defaults
	$('input.labeled')
		.each(function(){
			if (this.value == '') {
				$(this).addClass('empty');
				this.value = this.title;
			}
		})
		.blur(function(){
			if (this.value == '' || this.value == this.title) {
				$(this).addClass('empty');
				this.value = this.title;
			}
		})
		.focus(function(){
			if (this.value == this.title) {
				$(this).removeClass('empty');
				this.value = '';
			}
		});
	$('#configForm').submit(function(){
		$('#configForm input[type="text"]').each(function(){
			if (this.value == this.title)
				this.value = '';
		});
		return true;
	});
});

function deleteItem(e) {
	getTargetItem(e).remove();
}

function addStaticText(e) {
	popupTextField(e, {type:'text', name:'info'}, function(){ 
		var info = $('<li>'+$(this).children('input').val()+'</li>');
		addContextItem(info, 'Delete', deleteItem);
		addContextItem(info, 'Add Info', addStaticText);
		$('#static ul').append(info);
		$(this).remove();
		return false;
	});
}

function addStaticImage() {
	
}

function addGroupPopup(e) {
	popupTextField(e, {type:'text', name:'group'}, function(){ 
		var target = getTargetItem(e);
		var parentMenu = (!target.hasClass('group'))? $('.menu.top') : $('#menu' + target.data('gid'));
		var data = {title:$(this).children('input').val(), items:[], children:[]};
		var group = makeGroup(data, parentMenu);
		if (target.is('li'))
			target.addClass('parent');
			
		addContextItem(group, 'Delete', deleteItem);
		addContextItem(group, 'Edit Group', editGroupHandler);
		addContextItem(group, 'Add Sub Group', addGroupPopup);
		addContextItem(group, 'Rename Group', renameGroupPopup);
		
		group.children('.clickHandle').click();
		editGroup(group);
		
		$(this).remove();
		return false;
	});
}

function renameGroupPopup(e) {
	popupTextField(e, {type:'text', name:'group'}, function(){
		var group = getTargetItem(e);
		var newTitle = $(this).children('input').val();
		group.children('.title').html(newTitle);
		group.data('title', newTitle);
		$(this).remove();
		return false;
	});
}

function editGroupHandler(e) {
	var group = getTargetItem(e);
	editGroup(group);
}

function editGroup(group) {
	// remove old admin items to avoid conflict with the uploader
	//@todo find cause of this bug
	$('.adminPage').remove();
	
	$('.item').hide().removeClass('active');
	$('.counter').hide();
	
	group.data('currentItem', 0);
	group.children('.counter.current').text('admin');
	group.children('.counter').show();
	$('.' + group.data('gid') + 'Item').remove(); // deletes group items
	
	var ul = $('<ul />');
	var itemDiv = $('<div />');
	itemDiv.attr('id', group.data('gid') + 'Admin')
		   .addClass('adminPage')
		   .addClass('item')
		   .append(ul);
	$('#canvas').append(itemDiv);
		   
	// list items
	for (i in group.data('items')) {
		makeAdminItem(group.data('items')[i], group, ul);
	}

	var addItemBtnId = group.attr('id')+'uploadButton';
	var newItemsForm = $('<div class="uploadForm" />');
	var addTextBtn = $('<button>Add Text</button>');
	addTextBtn.click(function(){
		makeAdminItem({src:'', type:'html', text:'', width:'', height:''}, group, ul);
		saveItems(group, ul);
	});
	newItemsForm.append('<button id="'+addItemBtnId+'">Add Item</button>');
	newItemsForm.append('<br />');
	newItemsForm.append(addTextBtn);
	itemDiv.append(newItemsForm);
	
	ul.sortable({axis: 'y', update:function(){saveItems(group, ul)}})
	  .disableSelection();
	
	new AjaxUpload(addItemBtnId, 
				  {action:'admin/upload', 
					name:'newImage', 
					autoSubmit:true, 
					onComplete:function(file, response){
						if (response != 'success')
							return alert(response);
						makeAdminItem({src:'content/'+file, type:file.split('.').pop().toLowerCase(), text:'', width:'', height:''}, group, ul);
						saveItems(group, ul);
					}});
}

function makeAdminItem(data, target, parentMenu) {
	var form = $('<form onSubmit="return false;" />');
	form.addClass('media-'+data.type);
	var textarea = $('<textarea name="text" />');
	textarea.val(data.text);
	
	form.append(textarea);
	if (requiresDimensions(data.type)) {
		var width = $('<input type="text" name="media width" title="width" value="'+data.width+'" />');
		var height = $('<input type="text" name="media height" title="height" value="'+data.height+'" />');
		form.append('<br />');
		form.append('<label>width:</label>');
		form.append(width);
		form.append('<label>height:</label>');
		form.append(height);
		width.blur(function(e){
			saveItems(target, parentMenu);
		});
		height.blur(function(e){
			saveItems(target, parentMenu);
		});
	}
	textarea.blur(function(e){
		saveItems(target, parentMenu);
	});
	var li = $('<li />');
	li.append(makeThumb(data))
	  .append(form)
	  .data('src', data.src)
	  .data('type', data.type);
	li.data('textInput', textarea); // chaining this with above throws js error in IE
	if (requiresDimensions(data.type))
		li.data('widthInput', width)
		  .data('heightInput', height);
	addContextItem(li, 'Delete', function(e){
		deleteItem(e);
		saveItems(target, parentMenu);
	});
	
	// append new item to menu
	parentMenu.append(li);
	
	// add wysiwyg for HTML
	if (data.type == 'html') {
		textarea.wysiwyg();
		var saveButton = $('<input type="button" class="save-button" value="Save" />');
		form.append(saveButton);
		saveButton.click(function(){
			saveItems(target, parentMenu);
			$('<span class="alert">Saved</span>').appendTo(form).delay(1000).fadeOut();
		});
	}
	
	return li; 
}

function makeThumb(data) {
	if (data.type == 'html')
		return '<div class="media-thumb">page</div>';
	if (!requiresDimensions(data.type))
		return '<img src="'+data.src+'" />';
	return '<div class="media-thumb">' + data.src.substring(8) + '<br />(no preview available)</div>';
}

function saveItems(target, menu) {
	var items = [];
	menu.children('li').each(function(){
		el = $(this);
		if (el.hasClass('uploadForm')) return;
		var data = {
			src: el.data('src'), 
			type: el.data('type'), 
			text: el.data('textInput').val()
		};
		if (requiresDimensions(el.data('type'))) {
			data.width = el.data('widthInput').val();
			data.height = el.data('heightInput').val();
		}
		items.push(data);
	});
	target.data('items', items);
}

function save() {
	var data = {};
	
	// save gid
	data.nextGid = gid;
	
	// save logo
	var logo = $('#logo img');
	data.logo = (logo.length > 0)? logo.attr('src') : '';
	
	// save info
	data.info = [];
	$('#static li').each(function(i){
		data.info[i] = $(this).text();
	});
	
	// save menu
	data.groups = [];
	$('.menu.top > li').each(function(i, el){
		data.groups.push(getGroupData($(el)));
	});
	
	// serialize to form
	var serializedData = escape($.toJSON(data));
	//@todo writing serializedData is slow - use $.post and return false
	$(this).append('<input type="hidden" name="data" value="' + serializedData + '" />');
	
	return true;
}

function getGroupData(group) {
	var data = {
		gid: group.data('gid'), 
		title: group.data('title'),
		items: group.data('items'),
		children: []
	};
	$('#menu' + group.data('gid') + ' > li').each(function(i, el){
		data.children.push(getGroupData($(el)));
	});
	return data;
}

/**
 * Adds an item to a right-click context menu.
 * note: doesn't check if item is a duplicate.
 * 
 * @param {Object} selector
 * @param {Object} content
 * @param {Object} callback
 * @return {Array} items created
 */
function addContextItem(selector, content, callback) {
	var items = [];
	
	// append item to matched elements
	$(selector).each(function(i) {
		var el = $(this);
		
		// find menu
		if (!el.data('gid'))
			el.data('gid', randomId());
		var menuId = 'ContextMenu' + el.data('gid');
		var menu = $('#'+menuId);
		// create menu if needed
		if (menu.length < 1) {
			menu = $('<ul id="'+menuId+'" class="contextMenu"></ul>');
			$('#contextMenus').append(menu);
			el.jeegoocontext(menuId);
		}
		
		// make the context item
		var contextItem = $('<li />');
		contextItem
			.data('target', el)
			.append(content);
		if (callback)
			contextItem.click(callback);
		
		// append item
		items.push(contextItem);
		menu.append(contextItem);
	});

	return items;
}


function popupTextField(e, input, callback) {
	var form = $('<form method="post" class="popup" />');
	form.append('<input type="'+input.type+'" name="'+input.name+'" />');
	if(callback)
		form.submit(callback);
	$('body').append(form);
	form.css('left', e.pageX).css('top', e.pageY+10).show();
	form.children('input')
		.focus()
		.blur(function(){ form.remove() });
	return form;
}


