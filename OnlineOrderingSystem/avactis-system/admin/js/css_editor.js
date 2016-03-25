/*** EDITOR ***/
function FrameCSSEditor(options)
{
	this.mode = undefined;
	
	this.form_id = options.form_id;
	this.panel_id = options.panel_frame_id;
	this.doc_id = options.storefront_frame_id;
	this.theme_filename_head = options.theme_filename_head;
	this.theme_filename_tail = options.theme_filename_tail;
	this.labels = options.labels;
	this.editables_text = options.editables_text;
	
	this.started = false;
	
	this.edited_theme = options.edited_theme || {};
	typeof console != 'undefined' && console.debug && console.debug('Initial edited theme: ', this.edited_theme);
	
	this.edited_element = undefined;
	this.edited_special = undefined;
	this.editable_rules = {};
	this.special_editors = [];
	
	this.doc_loaded = false;
	this.pnl_loaded = false;

	var _this = this;
	
	this.history = new HistoryStack();
	this.history.onSave(function (history_stack) {
		_this.saveChanges(history_stack);
	});
	
	$('#'+this.panel_id).load(function(event) { _this.onLoadPanel(); });
	$('#'+this.doc_id).load(function(event) { _this.onLoadStorefront(); });
};

/*** PROCESS PAGE LOADING ***/
FrameCSSEditor.prototype.onLoadPanel = function()
{
	typeof console != 'undefined' && console.debug && console.debug('Panel loaded');
	this.panel_frame = document.getElementById(this.panel_id);
	this.pnl_win = this.panel_frame.contentWindow;
	this.pnl_doc = this.pnl_win.document;
	this.$pnl = this.pnl_win.$;
	
	this.debug = this.$pnl('#rulesDebug');
	
	this.loadEditables();
	
	var editor = this;
	window.setTimeout(function () {
		editor.pnl_loaded = true;
		editor.onLoadBoth();
	}, 200);
	
	this.history.setButtons(this.$pnl('.toolbar .undo'), this.$pnl('.toolbar .redo'));
	this.$pnl('.editor_panel .status .status_what_to_do .save, .toolbar .save').click(function () {
		editor.$pnl('.editor_panel').SkinsPanelStatus('.saving_theme');
		editor.saveChanges(editor.history);
	});
};

FrameCSSEditor.prototype.onLoadStorefront = function()
{
	typeof console != 'undefined' && console.debug && console.debug('Storefront loaded');
	var editor = this;
	try {
    	this.doc_frame = document.getElementById(this.doc_id);
    	this.win = this.doc_frame.contentWindow;
    	this.doc = this.win.document;
    	this.$ = this.win.$;
    	
    	initCommonExtensions(this.$);
    	
    	this.current_target = undefined;
    	this.upper_editable_element = undefined;
    	this.selected_element = undefined;
    	this.edited_elements = [];
    	
    	this.hover = new CSSEditorHover(this.$, 'element_hover', '#777', 0.2);
    	this.selection = new CSSEditorHover(this.$, 'element_selection', '#777', 0.7);
    	this.fog = new CSSEditorFog(this.$);
    	
    	this.win.onbeforeunload = function () { return editor.confirmUnloadStorefront(); };
    	//this.$(this.win).unload(function (event) { editor.onUnloadStorefront(event); });
    	this.$(this.win).resize(function (event) { editor.onResizeStorefront(event); });
    	this.reloadEditedTheme();
	}
	catch (e) {}
	
	this.doc_loaded = true;
	this.changeEditedTheme();
	window.setTimeout(function () {
		editor.onLoadBoth();
	}, 200);
};

FrameCSSEditor.prototype.onLoadBoth = function()
{
	if (this.pnl_loaded && this.doc_loaded) {
//		this.fog.fall();
		if (this.$) {
			this.$pnl('.editor_panel').trigger('env_mode', ['ready']);
    		this.$pnl('.editor_panel').SkinsPanelStatus('.working');
    		this.current_target = undefined;
    		this.loadDefaultRules();
    		this.$pnl('.editor_panel').SkinsPanelStatus();
		}
		else {
			this.$pnl('.editor_panel').trigger('env_mode', ['https_error']);
		}
		
//		editor.fog.lift();
	}
};

/*** INITIALIZING ***/
FrameCSSEditor.prototype.loadEditables = function()
{
	this.editable_rules = {};
	this.editable_styles = {};
	
	if (! this.editables_text) {
		this.$pnl('.no_rules').show();
		return;
	}
	var r = this.editables_text.match(/[^\}]+\}/gi);
	if (typeof r != 'object' || r == null) {
		return;
	}
	
	for (var i = 0; i < r.length; i++) {
		var e = r[i].match(/\[([^\]]+)\](.+)\{([^\}]+)\}/mi);
		if (e && e.length == 4) {
			var title = jQuery.trim(e[1]);
			var selectors_str = this.normalizeSelector(jQuery.trim(e[2]));
			if (selectors_str) {
				var editable_props = {};
				var eps = e[3].match(/([^\s;]+)/gi);
				if (eps && eps.length > 0) {
					for (var j = 0; j < eps.length; j++) {
						editable_props[ eps[j] ] = true;
					}
				}
				var ed_style = new EdStyle(this, title, editable_props);
				/*
				for(var j = 0; j < selectors.length; j++) {
					var selector = this.normalizeSelector(selectors[j]);
					if (selector) {
    					var css_rule = this.editable_rules[selector];
    					if (! css_rule) {
    						css_rule = new CssRule(this, selector);
    						this.editable_rules[selector] = css_rule;
    					}
    					ed_style.addCssRule(css_rule);
					}
				}
			    */
				if (typeof this.editable_styles[selectors_str] == 'undefined') {
					this.editable_styles[selectors_str] = [];
				}
				this.editable_styles[selectors_str].push(ed_style);
			}
		}
	}
};

FrameCSSEditor.prototype.loadDefaultRules = function()
{
	// reset old default rules
	this.editable_rules = {};
	/*
	for (var i in this.editable_rules) {
		this.editable_rules[i].resetDefaultRules();
	}
	*/
	
	// load actual editable styles
	for (var selector in this.editable_styles) {
		if (typeof this.editable_rules[selector] == 'undefined') {
			this.editable_rules[selector] = new CssRule(this, selector);
		}
		this.editable_rules[selector].setStyles(this.editable_styles[selector]);
	}
	
	// load actual default rules
	for (var i = 0; i < this.doc.styleSheets.length; i++) {
		var sheet = this.doc.styleSheets.item(i);
		if (sheet.type == 'text/css' && sheet.href && ! sheet.href.match(/\/([^\/]*)\.customized\.css/i)) {
			var rules = sheet.cssRules || sheet.rules;
                        if(rules.length) {continue;}else{
			for (var j = 0; j < rules.length; j++) {
				var rule = rules.item(j);
				var selector = rule.selectorText;
				if (typeof this.editable_rules[selector] == 'undefined') {
					this.editable_rules[selector] = new CssRule(this, selector);
				}
				this.editable_rules[selector].addDefaultRule(rule);
			}
                   }
		}
	}
};

FrameCSSEditor.prototype.getCustomizedRules = function()
{
	sheet_customized = undefined;
	var re = new RegExp('\/'+this.theme_filename_head.replace('.', '\.')+'[^\/]+'+this.theme_filename_tail.replace('.', '\.'), 'i');
	for (var i = 0; i < this.doc.styleSheets.length; i++) {
		var sheet = this.doc.styleSheets.item(i);
		if (sheet.type == 'text/css' && sheet.href && sheet.href.match(re)) {
			sheet_customized = sheet;
		}
	}
	return sheet_customized;
};

FrameCSSEditor.prototype.loadCustomizedRules = function()
{
	// reset old default rules
	for (var i in this.editable_rules) {
		this.editable_rules[i].resetCustomizedRule();
	}
	
	// find customized stylesheet
	this.sheet_customized = this.getCustomizedRules();
	
	// reset history
	this.history.init();

	if (this.sheet_customized) {
		// load actual customized rules

		/*
		var customized_rules = {};
		
		// index rules in this.sheet_customized
		var rules = this.sheet_customized.cssRules || this.sheet_customized.rules;
		if (typeof rules != 'undefined') {
			for (var i = 0; i < rules.length; i++) {
				var rule = rules.item(i);
				customized_rules[rule.selectorText] = rule;
			}
		}
		
		// create missing rules in this.sheet_customized
		for (var selector in this.editable_rules) {
			if (! customized_rules[selector]) {
				var rule = undefined;
				try {
					if (this.sheet_customized.insertRule) {
						rule = this.sheet_customized.insertRule(selector+' {}', rules.length);
					}
					else if (this.sheet_customized.addRule) {
						rule = this.sheet_customized.addRule(selector, ' ');
					}
				}
				catch (e) {
					// @    : process invalid selector
				}
				var qwe = 1;
			}
		}
		*/
		
		// assign customized rules to according editable
		var rules = this.sheet_customized.cssRules || this.sheet_customized.rules;
		if (typeof rules != 'undefined') {
			for (var i = 0; i < rules.length; i++) {
				var rule = rules.item(i);
				var selector = rule.selectorText;
				if (typeof this.editable_rules[selector] == 'undefined') {
					this.editable_rules[selector] = new CssRule(this, selector);
				}
				this.editable_rules[selector].setCustomizedRule(rule);
			}
		}
	}
};

FrameCSSEditor.prototype.confirmUnloadStorefront = function()
{
	if (this.history.canSave()) {
		return this.labels.alert_not_saved_theme;
	}
};

FrameCSSEditor.prototype.onUnloadStorefront = function(event)
{
	if (this.history.canSave()) {
		this.saveChanges(this.history);
	}
	event.preventDefault();
	this.doc_loaded = false;
	
	this.setEditableElements();
};

FrameCSSEditor.prototype.onResizeStorefront = function(event)
{
	if (this.mode == 'editing' && this.selection && this.selected_element) {
		this.selection.show(this.selected_element);
	}
};

/* prepare loaded page */
FrameCSSEditor.prototype.prepareStyleSheets = function()
{
	typeof console != 'undefined' && console.debug && console.debug('Initializing storefront for editing');
	var editor = this;

	for (var selector in this.editable_rules) {
		var elems = this.$(prepareCssSelector(selector));
		elems.each(function (i, domElement) {
			var el = editor.$(domElement);
			el.data('editable_rules', []).unbind('click').unbind('mousedown').unbind('mouseup');
			el.attr('onclick') && el.attr('onclick', '');
			el.attr('onmousedown') && el.attr('onmousedown', '');
			el.attr('onmouseup') && el.attr('onmouseup', '');
		});
	}
	
	// assign editable rules to according editable elements
	for (var selector in this.editable_rules) {
		var elems = this.$(prepareCssSelector(selector));
		elems.each(function (i, domElement) {
			var el = editor.$(domElement);
			el.data('editable_rules').push(editor.editable_rules[selector]);
		});
	}
	
	// assign special editors rules to according special elements
	for (var i = 0; i < this.special_editors.length; i++) {
		var se = this.special_editors[i];
		var elems = this.$(se.root_selector);
		elems.each(function (_i, domElement) {
			var el = editor.$(domElement);
			var f = true;
			for (var j = 0; f && j < se.children_selectors.length; j++) {
				if (el.find(se.children_selectors[j]).size() == 0) {
					f = false;
				}
			}
			if (f) {
				el.data('special_editor', se);
				el.find(se.children_selectors.join(',')).each(function (_j, domElement) {
					var em = editor.$(domElement);
					em.removeData('editable_rules');
					em.data('root_element', el);
				});
			}
		});
	}
	
	// outline editable elements on mouse hover
	this.$('body')
		.live('mouseover', function (event) { return editor.onItemMouseOver(event); })
		.live('mouseout', function (event) { return editor.onItemMouseOut(event); })
		.live('click', function (event) { return editor.onClickEditable(event); });
};

/*** SAVING CHANGES ***/
FrameCSSEditor.prototype.saveChanges = function(history_stack)
{
	if (! this.sheet_customized) {
		history_stack.SavingSucceeded();
		return;
	}
	
	var display_saved = false;
	if (this.$pnl('.editor_panel .saving_failed:visible, .editor_panel .saving_theme:visible').size() > 0) {
		display_saved = true;
	}
	
	var text = '';
	var rules = this.sheet_customized.cssRules || this.sheet_customized.rules;
	if (rules) {
		for (var j = 0; j < rules.length; j++) {
			var rule = rules.item(j);
			if ((rule.type == 1 || rule.type == undefined) && rule.style.cssText) {
				text += rule.selectorText+' { '+rule.style.cssText+" }\n";
			}
		}
	}
	
	var editor = this;
	$.ajax({
		url: 'edit_request.php',
		type: 'POST',
		data: { request: 'save_theme', name: this.edited_theme.name, css: text, __ASC_FORM_ID__: this.form_id },
		error: function() {
			editor.$pnl('.editor_panel').SkinsPanelStatus('.saving_failed', editor.labels.saving_failed_network, true);
			history_stack.SavingFailed();
		},
		dataType: 'json',
		success: function(data) {
			if (data.result) {
				if (display_saved) {
					editor.$pnl('.editor_panel').SkinsPanelStatus('.theme_saved');
				}
				else {
					editor.$pnl('.editor_panel').SkinsPanelStatus();
				}
				history_stack.SavingSucceeded();
			}
			else {
				editor.$pnl('.editor_panel').SkinsPanelStatus('.saving_failed', data.what_to_do, true);
				history_stack.SavingFailed();
			}
		}
	});
};

FrameCSSEditor.prototype.addChanges = function(changes)
{
	this.history.add(changes);
	this.syncBorders();
};

FrameCSSEditor.prototype.hasUnsavedChanges = function(changes)
{
	return this.history.canSave();
};

FrameCSSEditor.prototype.syncBorders = function()
{
	if (this.mode == 'editing') {
		this.selection.show(this.selected_element);
		this.hover.show(this.hover_element);
	}
};

/*** EDITING ***/
FrameCSSEditor.prototype.startEditing = function()
{
	if (! this.started) {
		this.started = true;
	}
	this.$pnl('.theme_editor .choose_element').hide();
};

/* show properties editors */

FrameCSSEditor.prototype.onClickEditable = function(event)
{
	if (this.mode != 'editing') {
		// navigation mode
		return true;
	}
	
	event.preventDefault();
	event.stopImmediatePropagation();
	event.stopPropagation();
	
	if (! this.sheet_customized) {
		// no custom styles found, emergency
		return false;
	}
	
	var editable_elements = this.getAllEditableElements(this.$(event.target));
	if (editable_elements.size() == 0) {
		// no editable elements under cursor
		return false;
	}

	if (this.current_target && this.current_target == event.target) {
		// same element clicked
		return false;
	}
	this.current_target = event.target;
	
	this.setEditableElements(editable_elements);
	
	return false;
};

FrameCSSEditor.prototype.getAllEditableElements = function($target)
{
	var editor = this;
	var editables = this.$();
	$target.parents().add($target).each(function () {
		var $el = editor.$(this);
		if ($el.data('editable_rules') || $el.data('root_element')) {
			editables = editables.add($el);
		}
	});
	return editables;
};

FrameCSSEditor.prototype.setEditableElements = function(editable_elements)
{
	if (! this.$) {
		return;
	}
	this.setEditedRule();
	
	var $upper_panel = this.$pnl('.theme_editor .element.upper');
	var $underlying_panel = this.$pnl('.theme_editor .underlying_elements');
	var $underlying_list = $underlying_panel.children('ul');
	$upper_panel.hide();
	$underlying_list.empty();
	$underlying_panel.hide();
	this.selection.hide();
	
	if (! editable_elements) {
		return;
	}
	
	this.editable_elements = editable_elements;
	this.upper_editable_element = editable_elements.last();
	this.selected_element = this.upper_editable_element;
	
	var editor = this;
	this.startEditing();
	
	
	this.createElementEditingPanel($upper_panel, this.upper_editable_element);
	this.selection.show(this.selected_element);
	$upper_panel.show();

	var underlying = editable_elements.slice(0, -1);
	if (underlying.size() > 0) {
		$underlying_panel.find('.title').html(underlying.size() == 1 ? this.labels.underlying_element : this.labels.underlying_elements);
		
		underlying.each(function (i, dom_element) {
			var element = editor.$(dom_element);
			$underlying_list.prepend('<li class="element underlying"><a class="name" href="javascript:void(0)"></a><div class="styles_title"></div><ul class="styles"></ul></li>');
			var element_panel = $underlying_list.children('li:first');
			editor.createElementEditingPanel(element_panel, element);
		});
		
		$underlying_panel.show();
	}
	this.$pnl('.accordion').accordion('activate', 1);
};

FrameCSSEditor.prototype.createElementEditingPanel = function($panel, $element)
{
	var editor = this;
	
	// create element's name button
	$panel.find('.name')
		.html(this.getElementName($element))
		.hover(function () {
			var editable_elements = editor.getAllEditableElements($element);
			if (editable_elements.size() == 0) {
				editor.hover.hide();
			}
			else {
    			editor.hover_element = editable_elements.last();
    			editor.hover.show(editor.hover_element);
			}
		}, function () {
			editor.hover_element = null;
			editor.hover.hide();
		})
		.prepend('<div class="show"></div>')
		.find('.show').attr('title', this.labels.show_this_element)
		.click(function (event) {
			var animation = {};
			var $body = editor.$('html:not(:animated),body:not(:animated)');
			var clientHeight = $body.attr('clientHeight');
			var clientWidth = $body.attr('clientWidth');
			
			if ($body.scrollTop() > $element.offset().top ||
					$body.scrollTop()+clientHeight < $element.offset().top+$element.outerHeight()) {
				animation.scrollTop = $element.offset().top - clientHeight/2;
			}
			
			if ($body.scrollLeft() > $element.offset().left ||
					$body.scrollLeft()+clientWidth < $element.offset().left+$element.outerWidth()) {
				animation.scrollLeft = $element.offset().left - clientWidth/2;
			}
			
			$body.animate(animation, 300, function () {
					$element.CSSEditorLightning();
				});

			event.stopImmediatePropagation();
		});
	
	// create list of associated styles
	var $styles = $panel.find('ul.styles');
	$styles.empty();
	var editable_rules = $element.data('editable_rules');
	if (editable_rules && editable_rules.length) {
		$panel.find('.styles_title').html(editable_rules.length == 1 ? this.labels.associated_style : this.labels.associated_styles);
		for (var i = 0; i < editable_rules.length; i++) {
			var title = editable_rules[i].hasTitle()
				? editable_rules[i].getTitle() + '<div class="rule-selector">' + editable_rules[i].getSelector() + '</div>'
				: editable_rules[i].getSelector();
			var prop_names = editable_rules[i].getAffectedProperties();
			if (prop_names != '') {
				prop_names = '<div class="affects">'+prop_names+'</div>';
			}
			$styles.append('<li class="style"><a class="name" href="javascript:void(0)" title="Handle Properties: ' +
					editable_rules[i].getPropSetsNames()+'"><div class="show" title="' +
					this.labels.show_elements_used_style+'"></div>' + title + prop_names + '</a></li>');
			
			(function () {
				var ed_rule = editable_rules[i];
				$styles.find('li.style:last .show').click(function (event) {
					editor.$(prepareCssSelector(ed_rule.getSelector())).CSSEditorLightning();
					event.stopImmediatePropagation();
				}).end()
				.find('li.style:last .name').click(function (event) {
					editor.setEditedRule($element, ed_rule);
					editor.selected_element = $element;
					editor.selection.show(editor.selected_element);
				});
			})();
		}
		
	}
	else if ($element.data('root_element')) {
	}
};

FrameCSSEditor.prototype.setEditedRule = function ($element, ed_style)
{
	this.$pnl('.rule_editor').find('.properties').empty();
	if (! $element) {
		return;
	}
	
	this.$pnl('.rule_editor .properties').CSSEditorSingleRule(ed_style);
	this.$pnl('.rule_editor .element_style').html(
			this.labels.editing_element_style
				.replace('%element%', this.getElementName($element))
				.replace('%style%', ed_style.getTitle())
			);
	this.$pnl('.accordion').accordion('activate', 2);
};

FrameCSSEditor.prototype.element_names = {
		H1: 'Header Level 1',
		H2: 'Header Level 2',
		H3: 'Header Level 3',
		H4: 'Header Level 4',
		H5: 'Header Level 5',
		H6: 'Header Level 6',
		TABLE: 'Table',
		THEAD: 'Table Header',
		TBODY: 'Table Body',
		TFOOT: 'Table Footer',
		TR: 'Table Row',
		TH: 'Table Header Cell',
		TD: 'Table Cell',
		A: 'Link',
		DIV: 'Rectangular Block',
		SPAN: 'Text Block',
		P: 'Paragraph of a Text',
		LABEL: 'Label for an Input Field',
		UL: 'List',
		OL: 'List',
		LI: 'List Item',
		BODY: 'Web Page',
		BUTTON: 'Button',
		FORM: 'Input Form',
		IMG: 'Image',
		TEXTAREA: 'Text Area',
		INPUT_submit: 'Submit Button',
		INPUT_image: 'Image Button',
		INPUT_button: 'Button',
		INPUT_text: 'Text Input',
		INPUT_password: 'Password Input',
		INPUT_checkbox: 'Checkbox',
		INPUT_radio: 'Radio Button',
		SELECT_D: 'Drop-Down Input',
		SELECT_1: 'Single Select Input',
		SELECT_M: 'Multiple Select Input',
		HR: 'Horizontal Rule'
};

FrameCSSEditor.prototype.getElementName = function(element)
{
	var tag_name = element.get(0).nodeName;
	if (tag_name == 'INPUT') {
		return this.element_names[tag_name+'_'+element.attr('type')];
	}
	else if (tag_name == 'SELECT') {
		var index = parseInt(element.attr('size')) > 1 
				? (element.attr('multiple') != '' ? 'M' : '1') 
				: 'D';
		return this.element_names[tag_name+'_'+index];
	}
	else if (typeof this.element_names[tag_name] != 'undefined') {
		return this.element_names[tag_name];
		
	}
	return tag_name;
};

FrameCSSEditor.prototype.getPropertiesList = function(rule)
{
	var properties = {};
	var style = rule.style;
	if (style.length != undefined) {
		for (var prop_name in style) {
			if (isNaN(prop_name) && prop_name != 'length' && prop_name != 'cssText') {
				var prop_value = style[prop_name];
				if (typeof(prop_value) == 'string' && prop_value != '' && prop_value != 'inherit') {
					properties[prop_name] = prop_value;
				}
			}
		}
	}
	else {
		for (var prop_name in style) {
			var prop_value = style[prop_name];
			if (prop_value != '' && prop_value != 'inherit') {
				properties[prop_name] = prop_value;
			}
		}
	}
	return properties;	
};

/* mouse moving */
FrameCSSEditor.prototype.onItemMouseOver = function(event)
{
	if (this.mode != 'editing' || ! this.sheet_customized) {
		return true;
	}
	
	var editable_elements = this.getAllEditableElements(this.$(event.target));
	if (editable_elements.size() == 0) {
		this.hover.hide();
		return true;
	}
	this.hover_element = editable_elements.last();
	this.hover.show(this.hover_element);
	
	return true;
};

FrameCSSEditor.prototype.onItemMouseOut = function(event)
{
	this.hover_element = null;
	this.hover.hide();
	
	return true;
};

FrameCSSEditor.prototype.setTheme = function(theme)
{
	this.edited_theme = theme;
	this.changeEditedTheme();
	this.applyEditedTheme();
};

FrameCSSEditor.prototype.reloadEditedTheme = function()
{
	var $css_link = this.$('#CurrentThemeStylesheet');
	var css_url = $css_link.size() ? $css_link.attr('href') : '';
	if (css_url) {
		var now = new Date();
		css_url = css_url.replace(/\?.*$/, '') + '?v=' + now.getTime();
		$css_link.attr('href', css_url);
	}
};

FrameCSSEditor.prototype.changeEditedTheme = function()
{
	typeof console != 'undefined' && console.debug && console.debug('FrameCSSEditor.changeEditedTheme()');
	if (! this.doc_loaded || typeof this.edited_theme == 'undefined' || ! this.$) {
		typeof console != 'undefined' && console.debug && console.debug('Cannot apply theme');
		return;
	}
	
	var $css_link = this.$('#CurrentThemeStylesheet');
	var css_url = $css_link.size() ? $css_link.attr('href') : '';
	if (css_url) {
		css_url = css_url.replace(/\?.*$/, '');
	}
	
	if (! this.edited_theme.url && css_url == '' || this.edited_theme.url == css_url) {
		return;
	}
	
	if (this.edited_theme.url) {
		var now = new Date();
		var url = this.edited_theme.url+'?v='+now.getTime();
		if ($css_link.size()) {
			var old_url = $css_link.attr('href');
			$css_link.attr('href', url);
			typeof console != 'undefined' && console.debug && console.debug('Theme file changed: ', url, ' (was ', old_url, ')');
		}
		else {
			this.$('head').append('<link href="' + url + '" rel="stylesheet" type="text/css" id="CurrentThemeStylesheet" />');
			typeof console != 'undefined' && console.debug && console.debug('Theme file appended: ', url);
		}
		
	}
	else {
		var old_url = $css_link.attr('href');
		$css_link.remove();
		typeof console != 'undefined' && console.debug && console.debug('Theme file unlinked (was ', old_url, ')');
	}
};

FrameCSSEditor.prototype.applyEditedTheme = function()
{
	this.setEditableElements();
	
	this.sheet_customized = undefined;
	this.current_target = undefined;
	this.upper_editable_element = undefined;
	this.selected_element = undefined;
	this.edited_elements = [];
	
	typeof console != 'undefined' && console.debug && console.debug('FrameCSSEditor.applyEditedTheme(): mode: ', this.mode);
	if (this.edited_theme.url && this.mode == 'editing') {
		var editor = this;
		this.$pnl('.editor_panel').SkinsPanelStatus('.working');
		window.setTimeout(function () {
			editor.loadCustomizedRules();
			editor.prepareStyleSheets();
			editor.$pnl('.editor_panel').SkinsPanelStatus();
		}, 1000);
	}
};

FrameCSSEditor.prototype.normalizeSelector = function(selector)
{
	var sheet = this.pnl_doc.styleSheets.item(0);
	var rules = sheet.cssRules || sheet.rules;
	var old_length = rules.length;
	try {
		if (sheet.insertRule) {
			sheet.insertRule(selector+' {}', old_length);
		}
		else if (sheet.addRule) {
			sheet.addRule(selector, ' ');
		}
	}
	catch (e) {
	}
	if (old_length < rules.length) {
		var rule = rules.item(old_length);
		if (rule.selectorText != selector) {
			typeof console != 'undefined' && console.debug && console.debug('Selector normalized: ', rule.selectorText, ' (was ', selector, ')');
		}
		return rule.selectorText;
	}
	return undefined;
};

FrameCSSEditor.prototype.createCustomizedRule = function(selector)
{
	if (this.sheet_customized) {
		var rules = this.sheet_customized.cssRules || this.sheet_customized.rules;
		var old_length = rules.length;
		try {
			if (this.sheet_customized.insertRule) {
				this.sheet_customized.insertRule(selector+' {}', rules.length);
			}
			else if (this.sheet_customized.addRule) {
				this.sheet_customized.addRule(selector, ' ');
			}
		}
		catch (e) {
			// @    : process invalid selector
		}
		if (old_length < rules.length) {
			return rules.item(old_length);
		}
	}
	return undefined;
};

FrameCSSEditor.prototype.setEditorMode = function (mode)
{
	typeof console != 'undefined' && console.debug && console.debug('FrameCSSEditor.setEditorMode(): ', mode);
	this.mode = mode;
	this.hover && this.hover.hide();
	this.selection && this.selection.hide();
	if (mode == 'editing') {
		var editor = this;
		window.setTimeout(function () { editor.applyEditedTheme(); }, 100);
	}
//		this.fog.lift();
};
