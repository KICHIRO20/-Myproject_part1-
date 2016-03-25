jQuery.fn.SkinsPanel = function (settings)
{
	var editor_mode, theme_mode, env_mode, working_mode;

	var edited_theme = settings.edited_theme;
	var active_theme = settings.active_theme;
	var themes_indexed = {};
	
	var $panel = $(this);
	$panel.bind('env_mode', function (event, mode) {
		env_mode = mode;
		syncMode();
	});
	
	loadSkins();
	
	try {
		$panel.find('.mode').buttonset();
	}
	catch(e) {}
	$panel.find('.btn.mode').click(function () {
		var $this = $(this);
		if (! $this.hasClass('disabled')) {
			if ($this.hasClass('checked') && ! confirmLoosingChanges(settings.labels.alert_not_saved_theme_nav)) {
				return;
			}
			setEditorMode($this.hasClass('checked') ? 'navigation' : 'editing');
		}
	});
	
	$panel.find('.toolbar .follow').click(function () {
		$(this).toggleClass('stop');
	});
	$panel.find('.add_skin input[type=text]')
	.bind('keypress', function (event) {
		var $this = $(this);
		if (event.charCode > 0 && String.fromCharCode(event.charCode).match(/[^A-Za-z0-9\.\-_]/)) {
			event.preventDefault();
			return false;
		}
		return true;
	})
	.bind('change _click blur keyup _mouseup', function () {
		var $this = $(this);
		$this.val($this.val().replace(/[^A-Za-z0-9\.\-_]/g, ''));
		$this.focus();
	});
	$panel.find('.add_skin .submit').click(addSkin);
	
	$panel.find('.status_what_to_do .close a').click(function () {
		$panel.find('.status_what_to_do').fadeOut(100, function () {
			$panel.find('.accordion').trigger('resize');
		});
	});
	
	$panel.find('.themes_panel .make_theme_active').click(function () {
		$panel.SkinsPanelStatus('.working');
		$.ajax({ 
			url: 'edit_request.php', 
			data: { request: 'set_active_theme', name: edited_theme },
			cache: false,
			dataType: 'text',
			success: function (data) {
				if (data == 'ok') {
					$panel.SkinsPanelStatus('.set_active_ok');
					active_theme = edited_theme;
					$panel.find('.existing ul li').removeClass('active');
					$panel.find('.existing ul li[skin_name=' + active_theme + ']').addClass('active');
				}
				else {
					$panel.SkinsPanelStatus('.set_active_fail', settings.labels.set_active_fail_wtd);
				}
			},
			error: function () {
				$panel.SkinsPanelStatus('.set_active_fail', settings.labels.set_active_fail_wtd);
			}
		});
	});
	
	$panel.find('.bookmarks div.button_go').click(function () {
		var url = $panel.find('.bookmarks .url input[type=text]').val();
		var frame = window.parent.document.getElementById('storefront');
		if (frame && url) {
			frame.contentWindow.location.href = url;
		}
	});
	
	var $accordion = $panel.find('.accordion');
	$accordion.accordion({ fillSpace: true, header: 'h2' }).bind('resize', function () {
		setAccordionHeight();
	});
	$(window).resize(function () {
		setAccordionHeight(); 
	});

	$panel.find('.editor_panel_loading').hide();
	setAccordionHeight();
	setEditorMode('editing');



	function loadSkins()
	{
		$panel.find('.loading').show();
		$.ajax({
			url: 'edit_request.php',
			data: { request: 'get_theme_list' },
			complete: function () { $panel.find('.loading').hide(); },
			cache: false,
			dataType: 'json',
			success: function (data) {
				if (data && data != null) {
					themes_indexed = data;
					showThemes();
				}
			},
			error: function (XMLHttpRequest, textStatus) {
				$panel.find('.loading_failed').show();
			}
		});
	}
	
	function showThemes()
	{
		var c = 0;
		for (var k in themes_indexed) c++;
		if (c > 0) {
			$panel.find('.no_skins').hide();
			var $list = $panel.find('.existing ul');
			$list.find('li:not(.default)').remove();
			var edited_theme_exists = false;
			for (var k in themes_indexed) {
				var skin = themes_indexed[k];
				var $item = $($panel.find('.theme_item_template').html().replace('%skin_name%', skin.name));
				$item
					.attr('skin_name', skin.name)
					.appendTo($list)
					.find('.remove').click(function (event) {
						event.stopPropagation();
						$(this).parent().find('.error,.message,.what_to_do').hide().end().find('.confirm_remove').toggle();
					}).end()
					.find('.yes').click(function (event) {
						$(this).parent().hide();
						removeSkin($(this).parents('li'));
					}).end()
					.find('.no').click(function (event) {
						$(this).parent().hide();
					}).end()
					.find('.confirm_remove').click(function (event) {
						event.stopPropagation();
					}).end();
				$item.addClass(skin.editable ? 'editable' : 'ro');
			}
			
			$panel.find('.existing ul li[skin_name=' + active_theme + ']').addClass('active');
			setEditedTheme(edited_theme);
			$panel.find('.existing ul li').click(function () {
				if (! confirmLoosingChanges(settings.labels.alert_not_saved_theme)) {
					return false;
				}
				var $this = $(this);
				var name = $this.attr('skin_name');
				$.ajax({ url: 'edit_request.php', data: { request: 'set_edited_theme', name: name } });
				var theme = setEditedTheme(name);
				if (window.parent && window.parent.editor) {
					window.parent.editor.setTheme(theme);
				}
			});
			$panel.find('.existing ul').find('li:first').addClass('ui-corner-top').end().find('li:last').addClass('ui-corner-bottom');
			$panel.find('.make_theme_active').show();
		}
		else {
			$panel.find('.make_theme_active').hide();
			$panel.find('.no_skins').show();
		}
	}
	
	function addSkin()
	{
		var $name = $('.add_skin input');
		if ($name.val() == '') {
			$name.focus();
			return;
		}
		$panel.SkinsPanelStatus();
		$panel.find('.add_skin .what_to_do, .add_skin .message').hide();
		$panel.find('.add_skin .submit').hide();
		$panel.find('.add_skin .adding').show();
		$.ajax({
			url: 'edit_request.php', 
			data: { request: 'add_theme', name: $name.val() },
			complete: function () {
				$panel.find('.add_skin .adding').hide();
				$panel.find('.add_skin .submit').show();
			},
			cache: false,
			dataType: 'json',
			success: function (data) {
				$panel.find('.add_skin .message')
					.addClass(data.result ? 'success' : 'error')
					.removeClass(data.result ? 'error' : 'success')
					.html(data.message)
					.show();
				if (data.result) {
					loadSkins();
					$('.add_skin input').val('');
				}
				if (data.what_to_do) {
					$panel.find('.add_skin .what_to_do').html(data.what_to_do).show();
				}
			},
			error: function () {
				$panel.find('.add_skin .what_to_do').find('.text').html(data.what_to_do).end().show();
			}
		});
	}

	function setEditedTheme(new_theme)
	{
		edited_theme = themes_indexed[new_theme] ? new_theme : '';
		$panel.find('.existing ul li').removeClass('edited');
		$panel.find('.existing ul li[skin_name='+edited_theme+']').addClass('edited');
		if (edited_theme == '') {
			$panel.find('.read_only').hide();
			$panel.find('.default_chosen').show();
			setThemeMode('default');
		}
		else if (themes_indexed[edited_theme].editable) {
			$panel.find('.read_only').hide();
			$panel.find('.default_chosen').hide();
			setThemeMode('editable');
			$panel.find('.theme_editor .choose_element').show();
		}
		else {
			$panel.find('.default_chosen').hide();
			$panel.find('.read_only u.theme_file').text(themes_indexed[edited_theme].path);
			$panel.find('.read_only').show();
			setThemeMode('readonly');
		}
		return edited_theme == '' ? {} : themes_indexed[edited_theme];
	}
	
	function removeSkin($skin_li)
	{
		var $removing = $skin_li.find('.removing').show();
		
		$.ajax({
			url: 'edit_request.php',
			data: { request: 'remove_theme', name: $skin_li.attr('skin_name') },
			complete: function () { $removing.hide(); },
			cache: false,
			dataType: 'json',
			success: function (data) {
				if (data.result) {
					loadSkins();
				}
				else {
					$skin_li.find('.message').html(data.message).fadeIn(100);
					if (data.what_to_do) {
						$skin_li.find('.what_to_do').html(data.what_to_do).show();
					}
				}
			},
			error: function (XMLHttpRequest, textStatus) {
				$skin_li.find('.error').show();
			}
		});
	}

	function setEditorMode(mode)
	{
		if (editor_mode != mode) {
			editor_mode = mode;
			syncMode();
    		$panel.find('.toolbar .btn.mode')[editor_mode == 'editing' ? 'addClass' : 'removeClass']('checked');
		}
	}
	
	function setThemeMode(mode)
	{
		if (theme_mode != mode) {
			theme_mode = mode;
			syncMode();
		}
	}
	
	function syncMode()
	{
		typeof console != 'undefined' && console.debug && console.debug('syncMode(): editor mode: ', editor_mode, ', theme mode: ', theme_mode, ', environment mode:', env_mode);
		$panel.find('.toolbar .btn.mode')[theme_mode == 'editable' ? 'removeClass' : 'addClass']('disabled');
		applyMode(editor_mode == 'editing' && theme_mode == 'editable' && env_mode == 'ready' ? 'editing' : 'navigation');
	}
	
	function applyMode(mode)
	{
		typeof console != 'undefined' && console.debug && console.debug('css editor panel::applyMode():', mode, working_mode, env_mode);
		if (working_mode == 'editing') {
			if (working_mode != mode) {
				$panel.find('.navigation_mode').hide()
				$panel.find('h2.customization, h2.properties').show();
	    		$panel.find('.accordion').show().trigger('resize');
	    		if (window.parent && window.parent.editor) {
	    			window.parent.editor.setEditorMode('editing');
	    		}
			}
		}
		else {
			var msg;
			if (env_mode == 'https_error') {
				msg = '.https';
			}
			else if (theme_mode == 'editable') {
				msg = '.editable';
			}
			else {
				msg = '.readonly';
			}
			typeof console != 'undefined' && console.debug && console.debug('show message:', msg);
			$panel.find('.navigation_mode').show().children().hide().filter(msg).show();
			$panel.find('h2.customization, h2.properties').hide();
    		$panel.find('.accordion').show().trigger('resize').accordion('activate', 0);
    		if (working_mode != mode && window.parent && window.parent.editor) {
    			window.parent.editor.setEditorMode('navigation');
    		}
		}
		working_mode = mode;
	}
	
	function setAccordionHeight()
	{
		var static_panels_height = 5;
		$panel.find('.editor_panel > *:not(.accordionResizer):visible').each(function () {
			static_panels_height += $(this).outerHeight(true);
		});
		var new_height = ($('html').height() - static_panels_height)+'px';
		typeof console != 'undefined' && console.debug && console.debug('New accordion height:', new_height, $panel.find('.accordionResizer').css('height')); 
		if (1 || $panel.find('.accordionResizer').css('height') != new_height) {
			$panel.find('.accordionResizer').css('height', new_height);
			$accordion.accordion('resize');
		}
	}
	
	function confirmLoosingChanges(message)
	{
		if (window.parent && window.parent.editor) {
			if (window.parent.editor.hasUnsavedChanges()) {
				return window.confirm(message);
			}
		}
		return true;
	}
	
};

jQuery.fn.SkinsPanelStatus = function (message, what_to_do, show_save_button)
{
	var $panel = $(this);
	var $status = $panel.find('.status');
	var $wtd = $status.find('.status_what_to_do');
	var $save = $wtd.find('.save');
	
	if (message) {
		$status.children(':not('+message+')').hide();
		$status.children(message).show();
    	if (what_to_do) {
    		$wtd.find('.text').html(what_to_do);
    		$save [show_save_button ? 'show' : 'hide'] ();
    		$wtd.show();
    	}
    	else {
    		$wtd.hide();
    	}
		$status.show();
	}
	else {
		if ($status.is(':visible')) {
			$status.hide();
		}
	}
	
	$panel.find('.accordion').trigger('resize');
};
