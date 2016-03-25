function prepareCssSelector(selector)
{
	return selector.replace(/(:hover|:active|:link|:visited|:focus|:first-child|:first-letter|:before|:after)/ig, '');
}

/* CSS Rule */

function CssRule(editor, selector)
{
	this.editor = editor;
	this.selector = selector;
	this.default_rules = [];
	this.editable_styles = [];
//	this.resetDefaultRules();
	this.resetCustomizedRule();
}

CssRule.prototype.resetDefaultRules = function()
{
	this.default_rules = [];
};

CssRule.prototype.resetCustomizedRule = function()
{
	this.customized_rule = undefined;
};

CssRule.prototype.addDefaultRule = function(default_rule)
{
	this.default_rules.push(default_rule);
};

CssRule.prototype.setCustomizedRule = function(customized_rule)
{
	this.customized_rule = customized_rule;
};

CssRule.prototype.setStyles = function(editable_styles)
{
	this.editable_styles = editable_styles;
};

CssRule.prototype.hasCustomizedRule = function ()
{
	return typeof this.customized_rule != 'undefined';
};

CssRule.prototype.getSelector = function()
{
	return this.selector;
};

CssRule.prototype.getPropertyDefaultValue = function(prop_name)
{
	var value = '';
	for (var i = 0; i < this.default_rules.length; i++) {
		try {
			var v = this.default_rules[i].style[prop_name];
			if (v != 'inherit' && v) {
				value = v;
			}
		}
		catch (e) {}
	}
	
	return value;
};

CssRule.prototype.getPropertyActualValue = function(prop_name)
{
	if (this.customized_rule) {
		var value = this.customized_rule.style[prop_name];
		return value == 'inherit' ? '' : value;
	}
	return '';
};
/*
CssRule.prototype.setActualPropertyValue = function(prop_name, prop_value, change)
{
	if (! this.customized_rule) {
		this.customized_rule = editor.createCustomizedRule(this.selector);
	}
	if (this.customized_rule) {
		if (this.customized_rule.style[prop_name] != prop_value) {
			try {
				var old_value = this.customized_rule.style[prop_name];
				this.customized_rule.style[prop_name] = prop_value;
				if (change) {
					change.addChange(this, old_value, prop_value);
				}
			}
			catch (e) {
			}
		}
	}
};
*/
CssRule.prototype.setActualPropertyValue = function(prop_name, prop_value, ed)
{
	if (! this.customized_rule) {
		this.customized_rule = this.editor.createCustomizedRule(this.selector);
	}
	if (this.customized_rule.style[prop_name] == prop_value) {
		return;
	}
	if (this.customized_rule) {
		if (ed) {
			var changes = new EdStyleChanges(this.editor, prop_name, ed);
			try {
				var old_value = this.customized_rule.style[prop_name];
				this.customized_rule.style[prop_name] = prop_value;
				changes.addChange(this, old_value, prop_value);
			}
			catch (e) {}
    		if (changes.hasChanges()) {
    			this.editor.addChanges(changes);
    		}
		}
		else {
			try {
				var old_value = this.customized_rule.style[prop_name];
				this.customized_rule.style[prop_name] = prop_value;
			}
			catch (e) {}
		}
	}
};

CssRule.prototype.getPropSetsNames = function ()
{
	var names_arr = [];
	if (this.editable_styles.length) {
    	for (var i = 0; i < editor.pnl_win.editors.length; i++) {
    		var ed = editor.pnl_win.editors[i];
    		if (this.editable_styles[0].isEditable(ed.setCode)) {
    			names_arr.push(ed.setName);
    		}
    	}
	}
	else {
	}
	return names_arr.join(', ');
};

CssRule.prototype.getAffectedProperties = function ()
{
	var prop_groups = [
        { name: 'font', props: [ 'font', 'fontSize', 'fontFamily', 'fontWeight', 'fontStyle', 'textDecoration', 'color' ] },
        { name: 'paragraph', props: [ 'textAlign', 'textIndent', 'lineHeight' ] },
    	{ name: 'background', props: [ 'background', 'backgroundColor', 'backgroundImage', 'backgroundRepeat', 'backgroundAttachment', 'backgroundPosition' ] },
    	{ name: 'border', props: [ 'border', 'borderTop', 'borderRight', 'bordeBottom', 'borderLeft', 'borderColor', 'borderWidth', 'borderStyle', 
    	                           'borderTopColor', 'borderTopWidth', 'borderTopStyle', 'borderRightColor', 'borderRightWidth', 'borderRightStyle', 
    	                           'borderBottomColor', 'borderBottomWidth', 'borderBottomStyle', 'borderLeftColor', 'borderLeftWidth', 'borderLeftStyle' ] },
    	{ name: 'margin', props: [ 'margin', 'marginTop', 'marginRight', 'marginBottom', 'marginLeft' ] },
    	{ name: 'padding', props: [ 'padding', 'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft' ] },
    	{ name: 'size', props: [ 'width', 'height' ] },
    	{ name: 'position', props: [ 'left', 'top', 'right', 'bottom' ] }
    ];
	var names = [];
	for (var i = 0; i < prop_groups.length; i++) {
		group_loop:
		for (var j =0; j < prop_groups[i].props.length; j++) {
			if (this.customized_rule && this.customized_rule.style[ prop_groups[i].props[j] ]) {
				names.push(prop_groups[i].name);
				break;
			}
			for (var k = 0; k < this.default_rules.length; k++) {
				if (this.default_rules[k].style[ prop_groups[i].props[j] ]) {
					names.push(prop_groups[i].name);
					break group_loop;
				}
			}
		}
	}
	return names.join(', ');
};

CssRule.prototype.hasTitle = function ()
{
	return !! this.editable_styles.length;
};

CssRule.prototype.getTitle = function ()
{
	if (this.editable_styles.length) {
		return this.editable_styles[0].getTitle();
	}
	return this.selector;
};

/* Editor Style (contains multiple CSS Rules) */

function EdStyle(editor, title, prop_sets)
{
	this.editor = editor;
	this.title = title;
	this.prop_sets = prop_sets; // editable properties sets
	this.css_rules = {};
}

EdStyle.prototype.getTitle = function()
{
	return this.title;
};

EdStyle.prototype.addCssRule = function(css_rule)
{
	this.css_rules[ css_rule.getSelector() ] = css_rule;
};

EdStyle.prototype.getSelector = function ()
{
	var selectors = [];
	if (this.css_rules) {
    	for (var i in this.css_rules) {
    		selectors.push(i);
    	}
	}
	return selectors.join(', ');
};

EdStyle.prototype.isEditable = function(set_code)
{
	return this.prop_sets && this.prop_sets[set_code];
};

EdStyle.prototype.getPropertyDefaultValue = function(prop_name)
{
	var value = '';
	if (this.css_rules) {
		for (var i in this.css_rules) {
			value = this.css_rules[i].getPropertyDefaultValue(prop_name);
			if (value != '') {
				break;
			}
		}
	}
	return value;
};

EdStyle.prototype.getPropertyActualValue = function(prop_name)
{
	var value = '';
	if (this.css_rules) {
		for (var i in this.css_rules) {
			value = this.css_rules[i].getPropertyActualValue(prop_name);
			if (value != '') {
				break;
			}
		}
	}
	return value;
};

EdStyle.prototype.setActualPropertyValue = function(prop_name, prop_value, ed)
{
	if (this.css_rules) {
		if (ed) {
			var changes = new EdStyleChanges(this.editor, prop_name, ed);
    		for (var i in this.css_rules) {
    			this.css_rules[i].setActualPropertyValue(prop_name, prop_value, changes);
    		}
    		if (changes.hasChanges()) {
    			this.editor.addChanges(changes);
    		}
		}
		else {
    		for (var i in this.css_rules) {
    			this.css_rules[i].setActualPropertyValue(prop_name, prop_value);
    		}
		}
	}
};

/* Undo-Redo History Change for EdStyle */

function EdStyleChanges(editor, prop_name, ed)
{
	this.editor = editor;
	this.prop_name = prop_name;
	this.ed = ed;
	this.changes = [];
}

EdStyleChanges.prototype.addChange = function (css_rule, old_value, new_value)
{
	this.changes.push({css_rule: css_rule, old_value: old_value, new_value: new_value});
}

EdStyleChanges.prototype.hasChanges = function ()
{
	return this.changes.length > 0;
}

EdStyleChanges.prototype.undo = function ()
{
	for (var i = 0; i < this.changes.length; i++) {
		var c = this.changes[i];
		c.css_rule.setActualPropertyValue(this.prop_name, c.old_value);
	}
	this.ed.trigger('syncdisplay').trigger('restore');
	this.editor.syncBorders();
}

EdStyleChanges.prototype.redo = function ()
{
	for (var i = 0; i < this.changes.length; i++) {
		var c = this.changes[i];
		c.css_rule.setActualPropertyValue(this.prop_name, c.new_value);
	}
	this.ed.trigger('syncdisplay').trigger('restore');
	this.editor.syncBorders();
};


/* Undo-Redo History */

function HistoryStack()
{
	this.$undo = undefined;
	this.$redo = undefined;
	this.$save = undefined;
	
	this.on_save = undefined;
	this.saving = false;
	this.saving_scheduled = false;
	this.save_after_failure = undefined;
	
	this.init();
}

HistoryStack.prototype.init = function ()
{
	this.pointer = 0;
	this.pointer_saved = 0;
	this.stack = [];
	
	this.syncButtons();
};

HistoryStack.prototype.setButtons = function ($undo, $redo, $save)
{
	this.$undo = $undo;
	this.$redo = $redo;
	this.$save = $save;
	
	var _this = this;
	
	if (this.$undo) {
		$undo.click(function () {
			if (_this.canUndo()) {
				var changes = _this.stack[-- _this.pointer];
				_this.syncButtons();
				changes.undo();
				if (! _this.$save) {
					_this.Save();
				}
			}
		});
	}
	if (this.$redo) {
		this.$redo.click(function () {
			if (_this.canRedo()) {
				var changes = _this.stack[_this.pointer ++];
				_this.syncButtons();
				changes.redo();
				if (! _this.$save) {
					_this.Save();
				}
			}
		});
	}
	if (this.$save) {
		this.$save.click(function () {
			_this.Save();
		});
	}
};

HistoryStack.prototype.syncButtons = function ()
{
	if (this.$undo) {
		if (this.canUndo()) {
			this.$undo.removeClass('disabled');
		}
		else {
			this.$undo.addClass('disabled');
		}
	}
	if (this.$redo) {
		if (this.canRedo()) {
			this.$redo.removeClass('disabled');
		}
		else {
			this.$redo.addClass('disabled');
		}
	}
	if (this.$save) {
		if (this.canSave()) {
			this.$save.removeClass('disabled');
		}
		else {
			this.$save.addClass('disabled');
		}
	}
};

HistoryStack.prototype.onSave = function (callback)
{
	this.on_save = callback;
};

HistoryStack.prototype.add = function (changes)
{
	if (this.pointer < this.stack.length) {
		this.stack.length = this.pointer;
	}
	this.stack.push(changes);
	this.pointer ++;
	this.syncButtons();
	if (! this.$save) {
		this.Save();
	}
};

HistoryStack.prototype.setSaved = function ()
{
};

HistoryStack.prototype.canUndo = function ()
{
	return this.pointer > 0;
};

HistoryStack.prototype.canRedo = function ()
{
	return this.pointer < this.stack.length;
};

HistoryStack.prototype.canSave = function ()
{
	return this.on_save && this.pointer != this.pointer_saved;
};

HistoryStack.prototype.Save = function ()
{
	if (this.canSave()) {
		if (! this.saving && ! this.save_after_failure) {
    		this.saving = true;
    		this.on_save(this);
		}
		else {
			this.saving_scheduled = true;
		}
	}
};

HistoryStack.prototype.SavingSucceeded = function ()
{
	this.saving = false;
	this.pointer_saved = this.pointer;
	this.syncButtons();
	
	if (this.saving_scheduled) {
		this.saving_scheduled = false;
		this.Save();
	}
};

HistoryStack.prototype.SavingFailed = function ()
{
	this.saving = false;
	
	this.saving_scheduled = true;
	if (this.save_after_failure) {
		window.clearTimeout(this.save_after_failure);
	}
	var _this = this;
	this.save_after_failure = window.setTimeout(function () {
		_this.save_after_failure = undefined;
		_this.Save();
	}, 30000);
};



/*** HIDE STOREFRONT WHILE PROCESSING ***/

function CSSEditorFog($)
{
	this.$ = $;
	var opacity = 0.1;
	var style = 'display:none; position:absolute; background-color: #fff; filter:alpha(opacity='+(opacity*100)+'); -moz-opacity:'+opacity+'; -khtml-opacity: '+opacity+'; opacity: '+opacity+'; font-size:1px';
	style += 'position:fixed; width:100%; height:100%; left:0; top:0; z-index:1000; cursor:wait;';
	this.fog = $('<div id="fog" style="'+style+'"></div>').appendTo('body');
}

CSSEditorFog.prototype.fall = function ()
{
	var p = this.fog.parent();
	this.fog.css('width', p.outerWidth()+'px');
	this.fog.css('height', p.outerHeight()+'px');
	this.fog.css('display', 'block');
};

CSSEditorFog.prototype.lift = function ()
{
	this.fog.hide();
};

/*** ELEMENT HOVER ***/

function CSSEditorHover($, id, color, opacity)
{
	this.$ = $;
	var style = 'display:none; position:absolute; background-color: '+color+'; filter:alpha(opacity='+(opacity*100)+'); -moz-opacity:'+opacity+'; -khtml-opacity: '+opacity+'; opacity: '+opacity+'; font-size:1px';
	this.top = this.$('<div id="'+id+'_top" style="height:2px;'+style+'"></div>');
	this.bottom = this.$('<div id="'+id+'_bottom" style="height:2px;'+style+'"></div>');
	this.left = this.$('<div id="'+id+'_left" style="width:2px;'+style+'"></div>');
	this.right = this.$('<div id="'+id+'_left" style="width:2px;'+style+'"></div>');
	$('body').append(this.top).append(this.bottom).append(this.left).append(this.right);
};

CSSEditorHover.prototype.show = function(target)
{
	if (! target) {
		this.hide();
		return;
	}
	var P = 4; // padding
	var T = 2; // border thickness
	var offset = target.offset();
	var left = offset.left;
	var top = offset.top;
	var width = target.outerWidth();
	var height = target.outerHeight();
	this.top.css('top', top-P-T).css('left', left-P).css('width', width+P+P).css('height', T).show();
	this.bottom.css('top', top+height+P).css('left', left-P).css('width', width+P+P).show();
	this.left.css('top', top-P-T).css('left', left-P-T).css('height', height+P+P+T+T).show();
	this.right.css('top', top-P-T).css('left', left+width+P).css('height', height+P+P+T+T).show();
};

CSSEditorHover.prototype.hide = function()
{
	this.top.hide();
	this.bottom.hide();
	this.left.hide();
	this.right.hide();
};

function initCommonExtensions($)
{

$.fn.CSSEditorLightning = function ()
{
	var opacity = 0.6;
	var op = 'filter: alpha(opacity='+(opacity*100)+'); -moz-opacity:'+opacity+'; -khtml-opacity: '+opacity+'; opacity: '+opacity+';';

	try {
    	$(this).each(function () {
    		var $this = $(this);
    		var offset = $this.offset();
    	
    		try {
        		var $elem = $('<div style="position:absolute; left: '+offset.left+'px; top: '+offset.top+
        				'px; width: '+$this.outerWidth()+'px; height: '+$this.outerHeight()+
        				'px; background-color: #FFD39F; '+op+'"></div>')
        		.appendTo('body');
        		window.setTimeout(function () {
        			$elem.remove();
        		}, 200);
    		}
    		catch (e) {
    		}
    	});
	}
	catch (e) {
	}
};

$.fn.CSSEditorPopup = function(position)
{
	this.append('<div class="css_editor_popup"></div>');
	var popup = this.children('.css_editor_popup:last');
	
	popup.bind('show', function (event, delay) {
		var target = $(event.originalTarget);
		var offset = target.offset();
		var top, left;
		switch(this.position) {
		case 'top':
			top = offset.top - this.popup.outerHeight(true)-6;
			left = offset.left-6;
			break;
		case 'bottom':
		default:
			top = offset.top + target.outerHeight(true)+6;
			left = offset.left-6;
			break;
		}
		popup.css('top', top).css('left', left);
		popup.show(delay);
	});
	popup.bind('hide', function(event, delay) {
		popup.hide(delay);
	});
	
	return popup;
};

}