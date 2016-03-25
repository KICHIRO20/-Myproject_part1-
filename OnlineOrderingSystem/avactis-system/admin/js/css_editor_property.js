//function initPropertyExtensions($)
//{

function getCSSEdNextId ()
{
	if (! window.css_id) {
		window.css_id = 1;
	}
	return 'cssed_' + window.css_id++;
};

function getUnitsOptions (units)
{
	var names = {
			'px': 'pixels',
			'in': 'inches',
			'pc': 'picas',
			'pt': 'points'
	};
	var options = '';
	for (var i = 0; i < units.length; i++) {
		var name = names[units[i]] ? names[units[i]] : units[i];
		options += '<option value="'+units[i]+'">'+name+'</option>';
	}
	return options;
}

function parseCSSColor (css_color)
{
	var r = css_color.match(/rgb\s*\(\s*(\d+),\s*(\d+),\s*(\d+)\s*\)/);
	if (r) {
		return {r:r[1], g:r[2], b:r[3]};
	}
	r = css_color.match(/\#(......)$/);
	if (r) {
		return r[1];
	}
	r = css_color.match(/\#(.)(.)(.)$/);
	if (r) {
		return r[1]+r[1]+r[2]+r[2]+r[3]+r[3];
	}
	return '';
};

/*
 * Titled Set of Properties
 */
$.fn.CSSEditorPropertySet = function (title) 
{
	var set = $('<div class="property_editor"><div class="title">'+title+'</div></div>').appendTo(this);
	return set;
};

/*
 * Common Empty Property Editor 
 */
$.fn.CSSEditorProperty = function (prop_name, rule, title)
{
	var save;
	var ed = $('<div class="prop_ed"></div>').appendTo(this);
	var label = $('<div class="prop_lbl">'+title+'</div>').appendTo(ed);
	var reset = $('<div class="reset"></div>').appendTo(ed);
	var value = $('<div class="prop_val"></div>').appendTo(ed);
	var val_h = $('<div class="prop_val_h">'+rule.getPropertyDefaultValue(prop_name)+'&nbsp;</div>').appendTo(ed);
	ed.append('<div class="clear"></div>');
	ed.data('value', value);
	
	function hideValueVisual() {
		value.hide();
		val_h.show();
		reset.hide();
		ed.addClass('prop_btn');
		ed.one('click', function(event) {
			event.stopPropagation();
			showValue();
		});
	}
	function hideValue() {
		save = rule.getPropertyActualValue(prop_name);
		ed.trigger('hide');
		rule.setActualPropertyValue(prop_name, '', ed);
		hideValueVisual();
	}
	function showValueVisual() {
		ed.removeClass('prop_btn');
		ed.unbind('click');
		val_h.hide();
		value.show();
		reset.show();
	}
	function showValue() {
		if (save === undefined) {
			var v = rule.getPropertyDefaultValue(prop_name);
			if (v != '') {
				rule.setActualPropertyValue(prop_name, v, ed);
			}
			else {
				ed.trigger('default');
			}
		}
		else {
			rule.setActualPropertyValue(prop_name, save, ed);
		}
		ed.trigger('show');
		showValueVisual();
	}
	
	reset.click(function(event) {
		event.stopPropagation();
		hideValue();
	});
	
	ed.bind('syncdisplay', function () {
		rule.getPropertyActualValue(prop_name) ? showValueVisual() : hideValueVisual();
	})
	.trigger('syncdisplay');
	
	
	return ed;
};

/*
 * Add Transparency Option into Property Editor
 */
$.fn.CSSEditorTransparency = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var save;
	var radio_name = getCSSEdNextId();
	
	var choose_color = $('<input type="radio" name="'+radio_name+'" id="'+radio_name+'_1" class="float_left input_radio choose_color" />').appendTo(value);
	var color_value = $('<label class="float_left color_value" for="'+radio_name+'_1"></label>').appendTo(value);
	var choose_trans = $('<input type="radio" name="'+radio_name+'" id="'+radio_name+'_2" class="float_left input_radio choose_trans" />').appendTo(value);
	value.append('<label class="float_left color_value" for="'+radio_name+'_2">Transparent</label>');
	this.data('value', color_value);

	function isTransparent() {
		var color = rule.getPropertyActualValue(prop_name);
		return color == 'transparent';
	}
	function fetchPropertyValue() {
		(isTransparent() ? choose_trans : choose_color).attr('checked', true);
	}
	
	function setTransparent() {
		save = rule.getPropertyActualValue(prop_name);
		rule.setActualPropertyValue(prop_name, 'transparent', _this);
		_this.trigger('transparent');
	}
	function setOpaque() {
		if (save === undefined) {
			_this.trigger('default');
		}
		else {
			rule.setActualPropertyValue(prop_name, save, _this);
		}
		_this.trigger('color');
	}
	choose_color.bind('change keyup click', function (event) {
		event.stopPropagation();
		if (isTransparent()) {
			setOpaque();
		}
	});
	choose_trans.bind('change keyup click', function (event) {
		event.stopPropagation();
		if (! isTransparent()) {
			setTransparent();
		}
	});
	
	this.bind('picker', function (event) {
		if (! choose_color.attr('checked')) {
			choose_color.attr('checked', true);
			setOpaque();
		}
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Common Color Picker 
 */
$.fn.CSSEditorColorPicker = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	var preview = $('<div class="preview_color"></div>').appendTo(value);
	var preview_c = $('<div></div>').appendTo(preview);

	var picker = $('<div class="color_picker"></div>').appendTo(this);
	
	function fetchPropertyValue() {
		var clr = rule.getPropertyActualValue(prop_name);
		preview_c.css('backgroundColor', clr);
		picker.ColorPickerSetColor(parseCSSColor(clr));
	}
	
	picker.ColorPicker({			
		flat: true,
		color: '#00ff00',
		onChange: function(hsb, hex, rgb) {
			rule.setActualPropertyValue(prop_name, '#' + hex, _this);
			preview_c.css('backgroundColor', '#' + hex);
			_this.trigger('change_prop');
		},
		livePreview: false
	});
	preview.bind('click', function() {
		if (! picker.is(':visible')) {
			_this.trigger('picker');
		}
		picker.slideToggle(100);
	});
	
	this.bind('hide transparent', function (event) {
		picker.slideUp(0);
	});
	this.bind('default', function (event) {
		clr = prop_name == 'backgroundColor' ? '#fff' : '#000';
		rule.setActualPropertyValue(prop_name, clr, _this);
	});
	this.bind('show restore transparent color', function(event){
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

$.fn.CSSEditorFontPicker = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="font_family">' +
			'<option value="Arial,Sans-serif" style="font-family:Arial,Sans-serif">Arial</option>' +
			'<option value="Verdana,Sans-serif" style="font-family:Verdana,Sans-serif">Verdana</option>' +
			'<option value="Tahoma,Verdana,Sans-serif" style="font-family:Tahoma,Verdana,Sans-serif">Tahoma</option>' +
			'<option value="\'Arial Black\',Sans-serif" style="font-family:\'Arial Black\',Sans-serif">Arial Black</option>' +
			'<option value="\'Times New Roman\',serif" style="font-family:\'Times New Roman\',serif">Times New Roman</option>' +
			'<option value="Georgia,serif" style="font-family:Georgia,serif">Georgia</option>' +
			'<option value="\'Courier New\',monospace" style="font-family:\'Courier New\',monospace">Courier New</option>' +
			'<option value="\'Comic Sans MS\',Sans-serif" style="font-family:\'Comic Sans MS\',Sans-serif">Comic Sans MS</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		select.val(rule.getPropertyActualValue(prop_name));
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'Arial,Sans-serif', _this);
	});
	this.bind('show restore', function(event){
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Font Wheight Checkbox (Normal / Bold)
 */
$.fn.CSSEditorFontWeight = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	
	var weight_value = $('<input type="checkbox" class="input_cb font_weight" />').appendTo(value);
	
	function fetchPropertyValue() {
		weight_value.attr('checked', rule.getPropertyActualValue(prop_name) == 'bold');
	}
	function setPropertyValue() {
		rule.setActualPropertyValue(prop_name, weight_value.is(':checked') ? 'bold' : 'normal', _this);
	}
	
	weight_value.bind('change keyup click', function (event) {
		setPropertyValue();
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'normal', _this);
	});
	this.bind('show restore', function(event){
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Font Style Checkbox (Normal / Italic)
 */
$.fn.CSSEditorFontStyle = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	
	var style_value = $('<input type="checkbox" class="input_cb font_style" />').appendTo(value);
	
	function fetchPropertyValue() {
		style_value.attr('checked', rule.getPropertyActualValue(prop_name) == 'italic');
	}
	function setPropertyValue() {
		rule.setActualPropertyValue(prop_name, style_value.is(':checked') ? 'italic' : 'normal', _this);
	}
	
	style_value.bind('change keyup click', function (event) {
		setPropertyValue();
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'normal', _this);
	});
	this.bind('show restore', function(event){
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Four Font Decoration Checkboxes (Underline, Overline, Strike-through, Blink)
 */
$.fn.CSSEditorFontDeco = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');
	var id;
	
	id = getCSSEdNextId();
	var under_value = $('<input type="checkbox" class="input_cb text_underline" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Underline</label><br />');
	id = getCSSEdNextId();
	var over_value = $('<input type="checkbox" class="input_cb text_overline" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Overline</label><br />');
	id = getCSSEdNextId();
	var throu_value = $('<input type="checkbox" class="input_cb text_linethrough" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Line-through</label><br />');
	id = getCSSEdNextId();
	var blink_value = $('<input type="checkbox" class="input_cb text_blink" id="'+id+'" />').appendTo(value);
	value.append(' <label for="'+id+'">Blink</label>');
	var all_values = value.children('.input_cb');
	
	function fetchPropertyValue() {
		var v = rule.getPropertyActualValue(prop_name);
		under_value.attr('checked', v.indexOf('underline') >= 0);
		over_value.attr('checked', v.indexOf('overline') >= 0);
		throu_value.attr('checked', v.indexOf('line-through') >= 0);
		blink_value.attr('checked', v.indexOf('blink') >= 0);
	}
	function setPropertyValue() {
		rule.setActualPropertyValue(prop_name, 
				(under_value.is(':checked') ? 'underline ' : '') +
				(over_value.is(':checked') ? 'overline ' : '') +
				(throu_value.is(':checked') ? 'line-through ' : '') +
				(blink_value.is(':checked') ? 'blink ' : '') || 'none', _this);
	}
	
	all_values.bind('change keyup click', function (event) {
		setPropertyValue();
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function(event){
		rule.setActualPropertyValue(prop_name, 'none', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Horizontal Text Align
 */
$.fn.CSSEditorTextAlign = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="text_align">' +
			'<option value="left">left</option>' +
			'<option value="right">right</option>' +
			'<option value="center">center</option>' +
			'<option value="justify">justify</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		var align = rule.getPropertyActualValue(prop_name);
		select.val(align);
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'left', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Choose Existing or Upload New Background Image
 */
$.fn.CSSEditorBackgroundImage = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var preview = $('<div class="image_name"></div>').appendTo(value);
	var upload = $('<a class="float_left btn24 btn_upload" title="Upload New Image" href="javascript:void(0)"></a>').appendTo(value);
	var choose = $('<a class="float_left btn24 btn_choose" title="Choose Uploaded Image" href="javascript:void(0)"></a>').appendTo(value);
	var none = $('<a class="float_left btn24 btn_none" title="No Image" href="javascript:void(0)"></a>').appendTo(value);
	
	function fetchPropertyValue() {
		var image = rule.getPropertyActualValue(prop_name);
		if (image == '') {
		}
		else
		if (image == 'none') {
			preview.html('- None -');
		}
		else {
			var r = image.match(/url\(['"]?\.\.\/images\/([^'"]+)['"]?\)/);
			if (r) {
				preview.html(r[1]);
			}
		}
	}
	function setPropertyValue() {
	}
	
	upload.click(function (event) {
		openURLinNewWindow('edit_upload_image.php', 'upload_bg_image');
		window.onImageUploaded = function(url) {
			rule.setActualPropertyValue(prop_name, 'url('+url+')', _this);
			_this.trigger('change_prop');
		};
	});
	choose.click(function (event) {
		openURLinNewWindow('edit_choose_image.php', 'choose_bg_image');
		window.onImageChoosed = function(url) {
			rule.setActualPropertyValue(prop_name, 'url('+url+')', _this);
			_this.trigger('change_prop');
		};
	});
	none.click(function (event) {
		rule.setActualPropertyValue(prop_name, 'none', _this);
		_this.trigger('change_prop');
	});
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'none', _this);
	});
	this.bind('show restore change_prop', function (event) {
		fetchPropertyValue();
	});
	
	fetchPropertyValue();
	
	return this;
};

/*
 * Background Repeat Option
 */
$.fn.CSSEditorBackgroundRepeat = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="backgnd_repeat">' +
			'<option value="repeat">repeat</option>' +
			'<option value="repeat-x">repeat-x</option>' +
			'<option value="repeat-y">repeat-y</option>' +
			'<option value="no-repeat">no-repeat</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		var align = rule.getPropertyActualValue(prop_name);
		select.val(align);
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'repeat', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Scroll or Fixed Background Image
 */
$.fn.CSSEditorBackgroundAttachment = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="backgnd_attach">' +
			'<option value="scroll">scroll</option>' +
			'<option value="fixed">fixed</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		var align = rule.getPropertyActualValue(prop_name);
		select.val(align);
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'scroll', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Align of Background Image
 */
$.fn.CSSEditorBackgroundPosition = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var x_axis = $('<div class="bkgnd_pos_axis x">').appendTo(value).CSSEditorWidgetLength(true, true);
	var y_axis = $('<div class="bkgnd_pos_axis y">').appendTo(value).CSSEditorWidgetLength(true, true);
	this.data('x_axis', x_axis.data('select'));
	this.data('y_axis', y_axis.data('select'));
	
	function fetchPropertyValue() {
		var pos = rule.getPropertyActualValue(prop_name);
		if (pos != '') {
			var p = pos.split(/\s+/, 2);
			x_axis.trigger('set', [p[0]]);
			y_axis.trigger('set', [p[1]]);
		}
	}
	function setPositionValue(index, value) {
		var pos = rule.getPropertyActualValue(prop_name);
		if (pos != '') {
			var p = pos.split(/\s+/, 2);
			p[index] = value;
			rule.setActualPropertyValue(prop_name, p.join(' '), _this);
		}
	}
	x_axis.bind('change_value', function (event, value) {
		setPositionValue(0, value);
		_this.trigger('change_prop');
	});
	y_axis.bind('change_value', function (event, value) {
		setPositionValue(1, value);
		_this.trigger('change_prop');
	});
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'left top', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Border Line Style
 */
$.fn.CSSEditorBorderStyle = function (prop_name, rule)
{
	var _this = this;
	var value = this.data('value');

	var select = $(
		'<select class="border_style">' +
			'<option value="outset">none</option>' +
			'<option value="hidden">hidden</option>' +
			'<option value="solid">solid</option>' +
			'<option value="dotted">dotted</option>' +
			'<option value="dashed">dashed</option>' +
			'<option value="double">double</option>' +
			'<option value="groove">groove</option>' +
			'<option value="ridge">ridge</option>' +
			'<option value="inset">inset</option>' +
			'<option value="outset">outset</option>' +
		'</select>').appendTo(value);
	
	function fetchPropertyValue() {
		select.val(rule.getPropertyActualValue(prop_name));
	}
	
	select.bind('change keyup', function (event) {
		rule.setActualPropertyValue(prop_name, select.val(), _this);
		_this.trigger('change_prop');
		event.stopPropagation();
	});
	
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, 'none', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Common Length Input Widget
 */
$.fn.CSSEditorWidgetLength = function (percentage, negative)
{
	var _this = this;
	var units_str = 'em|ex|px|cm|mm|in|pc|pt';
	if (percentage) {
		units_str += '|%';
	}
	var units_array = units_str.split('|');
	var units_regexp = new RegExp('([+-]?\\d*[\\.,]?\\d+)\\s*('+units_str+')', 'i');
	
	var up_down = $('<input class="text_button up-down" readonly="readonly" />').appendTo(this);
	var input = $('<input class="input_text size_value" size="3" type="text" />').appendTo(this);
	var predefined = $('<input class="text_button predefined" readonly="readonly" />').appendTo(this);
	var dd_pt = CSSEditorCustomDropdown([5,6,7,8,9,10,11,12,14,16,18,20,24,28,32,36,40,48,72,120]).appendTo(this);
	var dd_px = CSSEditorCustomDropdown([0,1,2,3,4,5,6,7,8,9,10,11,12,14,16,18,20,22,24,26,28,30,35,40,45,50,75,100,150]).appendTo(this);
	var dd_mm = CSSEditorCustomDropdown([0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1,1.1,1.2,1.3,1.4,1.5,1.6,1.7,1.9,2,2.2,2.4,2.6,2.8,3,3.5,4,4.5,5,7.5,10,15]).appendTo(this);
	var dd_pr = CSSEditorCustomDropdown([0,1,2,3,4,5,10,15,20,25,30,40,50,60,70,80,90,100]).appendTo(this);
	var select = $('<select class="backgnd_position">' + getUnitsOptions(units_array) + '</select>').appendTo(this);
	this.data('select', select);
	
	function getValue() {
		if (input.is(':visible')) {
			var v = $.trim(input.val());
			return (v == '' ? '0' : v) + select.val();
		}
		else {
			return select.val();
		}
	}
	
	input.bind('keyup mouseup up_down predefined', function (event) {
		if (+ $(this).val() < 0 && ! negative) {
			$(this).val(0);
		}
		_this.trigger('change_value', [getValue()]);
	});
	up_down.bind('click', function (event) {
		var $btn = $(this);
		var offset = $btn.offset();
		var position = $btn.position();
		var val = + input.val();
		if (event.clientY - offset.top < 10) {
			val += getDelta(val, '+');
		}
		else {
			val -= getDelta(val, '-');
		}
		if (val < 0 && ! negative) {
			val = 0;
		}
		input.val(Math.round(val*100)/100);
		input.trigger('up_down');
	});
	
	predefined.bind('click', function (event) {
		if (event.target == window.css_editor_dropdown_button) {
			hideDropdown();
		}
		else {
    		switch (select.val()) {
    			case 'px': showDropdown(dd_px[0], predefined[0]); break;
    			case 'pt': showDropdown(dd_pt[0], predefined[0]); break;
    			case '%':  showDropdown(dd_pr[0], predefined[0]); break;
    			case 'ex':
    			case 'em':
    			case 'cm':
    			case 'mm':
    			case 'pc':
    			case 'in': showDropdown(dd_mm[0], predefined[0]);
    		}
		}
	});
	
	select.bind('click keyup change', function (event) {
		if (jQuery.inArray(select.val(), units_array) >= 0) {
			if (input.val() == '') {
				input.val(0);
			}
			up_down.show();
			input.show();
			predefined.show();
		}
		else {
			up_down.hide();
			input.hide();
			predefined.hide();
		}
		_this.trigger('change_value', [getValue()]);
	});
	this.bind('set', function (event, value) {
		var r = value.match(units_regexp);
		if (r) {
			input.val(r[1]);
			select.val(r[2]);
			up_down.show();
			input.show();
			predefined.show();
		}
		else {
			select.val(value);
			up_down.hide();
			input.hide();
			predefined.hide();
		}
	});
	
	function getDelta(val, dir)
	{
		if (val < 0) {
			dir = dir == '+' ? '-' : '+';
		}
		val = Math.abs(val);
		switch (select.val()) {
			case 'px':
			case 'pt':
			case 'mm':
			case '%':
        		if (val < (dir == '+' ? 20 : 21)) return 1;
        		if (val < (dir == '+' ? 40 : 41)) return 5;
        		if (val < (dir == '+' ? 100 : 101)) return 10;
        		return 25;

			case 'ex':
			case 'em':
			case 'cm':
			case 'in':
			case 'pc':
        		if (val < (dir == '+' ? 2 : 2.1)) return 0.1;
        		if (val < (dir == '+' ? 4 : 4.1)) return 0.2;
        		if (val < (dir == '+' ? 10 : 10.1)) return 0.5;
        		return 1;
		}

	}
	function CSSEditorCustomDropdown(options)
	{
		for (var i = 0; i < options.length; i++) {
			options[i] = '<li>' + options[i] + '</li>';
		}
		var div = $('<div class="drop-down"><ul>' + options.join('') + '</ul></div>');
		div.bind('click', function (event) {
			event.stopPropagation();
			div.hide();
			input.val($(event.target).html());
			input.trigger('up_down');
		});
		if (! window.css_editor_dropdown_initialized) {
    		$(document).bind('mousedown', function (event) {
    			if (! window.css_editor_dropdown_current)
    				return;
    			if (event.target == window.css_editor_dropdown_current ||
    					event.target.parentNode && event.target.parentNode.parentNode == window.css_editor_dropdown_current)
    				return;
    			if (event.target == window.css_editor_dropdown_button) {
    				event.stopPropagation();
    				event.stopImmediatePropagation();
    				event.preventDefault();
    				return;
    			}
    				
    			hideDropdown();
    		});
    		window.css_editor_dropdown_initialized = true;
		}
		return div;
	}
	
	function showDropdown(dd, btn)
	{
		window.css_editor_dropdown_current = dd;
		window.css_editor_dropdown_button = btn;
		$(dd).show();
	}
	
	function hideDropdown()
	{
		$(window.css_editor_dropdown_current).hide();
		window.css_editor_dropdown_current = undefined;
		window.css_editor_dropdown_button = undefined;
	}
	
	return this;
};

/*
 * Common Editor for Values of Type <Length>
 */
$.fn.CSSEditorLength = function (prop_name, rule, percentage, negative)
{
	var _this = this;
	var value = this.data('value');
	if (value === undefined || value === null) {
		return this;
	}
	
	value.CSSEditorWidgetLength(percentage, negative);
	this.data('select', value.data('select'));

	function fetchPropertyValue() {
		value.trigger('set', [ rule.getPropertyActualValue(prop_name) ]);
	}
	value.bind('change_value', function (event, value) {
		rule.setActualPropertyValue(prop_name, value, _this);
		_this.trigger('change_prop');
	});
	this.bind('default', function (event) {
		rule.setActualPropertyValue(prop_name, '0px', _this);
	});
	this.bind('show restore', function (event) {
		fetchPropertyValue();
	});

	fetchPropertyValue();
	
	return this;
};

/*
 * Add Options into <Length> Property Editor
 */
$.fn.CSSEditorAddOptions = function (options, selector, to_begin)
{
	var _this = this;
	var select = this.data(selector ? selector : 'select');
	if (select === undefined) {
		return this;
	}
	var options_str = '';
	if (options.length) {
		for (var i = 0; i < options.length; i++) {
			options_str += '<option value="'+options[i]+'">'+options[i]+'</option>';
		}
	}
	else {
		for (var k in options) {
			options_str += '<option value="'+k+'">'+options[k]+'</option>';
		}
	}
	if (to_begin) {
		select.prepend(options_str);
	}
	else {
		select.append(options_str);
	}
	this.trigger('restore');
	
	return this;
};

//}