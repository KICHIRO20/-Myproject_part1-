//function initRuleExtensions($)
//{

function createProperties(set, rule, props_names)
{
	var opts_border_width = ['thin', 'medium', 'thick'];
	var opts_font_size = ['xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large', 'larger', 'smaller'];
	var opts_line_height = ['normal'];

	for (var i = 0; i < props_names.length; i++) {
		switch(props_names[i])
		{
		case 'color': set.CSSEditorProperty('color', rule, 'Color').CSSEditorColorPicker('color', rule); break;
		case 'fontFamily': set.CSSEditorProperty('fontFamily', rule, 'Font').CSSEditorFontPicker('fontFamily', rule); break;
		case 'fontSize': set.CSSEditorProperty('fontSize', rule, 'Size').CSSEditorLength('fontSize', rule, true).CSSEditorAddOptions(opts_font_size); break;
		case 'lineHeight': set.CSSEditorProperty('lineHeight', rule, 'Line').CSSEditorLength('lineHeight', rule, true).CSSEditorAddOptions(opts_line_height); break;
		case 'fontWeight': set.CSSEditorProperty('fontWeight', rule, 'Bold').CSSEditorFontWeight('fontWeight', rule); break;
		case 'fontStyle': set.CSSEditorProperty('fontStyle', rule, 'Italic').CSSEditorFontStyle('fontStyle', rule); break;
		case 'textDecoration': set.CSSEditorProperty('textDecoration', rule, 'Deco').CSSEditorFontDeco('textDecoration', rule); break;
		case 'textAlign': set.CSSEditorProperty('textAlign', rule, 'Align').CSSEditorTextAlign('textAlign', rule); break;
		case 'textIndent': set.CSSEditorProperty('textIndent', rule, 'Indent').CSSEditorLength('textIndent', rule); break;
		
		case 'backgroundColor': set.CSSEditorProperty('backgroundColor', rule, 'Color').CSSEditorTransparency('backgroundColor', rule).CSSEditorColorPicker('backgroundColor', rule); break;
		case 'backgroundImage': set.CSSEditorProperty('backgroundImage', rule, 'Image').CSSEditorBackgroundImage('backgroundImage', rule); break;
		case 'backgroundRepeat': set.CSSEditorProperty('backgroundRepeat', rule, 'Repeat').CSSEditorBackgroundRepeat('backgroundRepeat', rule); break;
		case 'backgroundAttachment': set.CSSEditorProperty('backgroundAttachment', rule, 'Attachment').CSSEditorBackgroundAttachment('backgroundAttachment', rule); break;
		case 'backgroundPosition': set.CSSEditorProperty('backgroundPosition', rule, 'Position').CSSEditorBackgroundPosition('backgroundPosition', rule).CSSEditorAddOptions(['left','center','right'], 'x_axis').CSSEditorAddOptions(['top','center','bottom'], 'y_axis'); break;
		
		case 'borderWidth': 
		case 'borderTopWidth': 
		case 'borderRightWidth': 
		case 'borderBottomWidth': 
		case 'borderLeftWidth': set.CSSEditorProperty(props_names[i], rule, 'Width').CSSEditorLength(props_names[i], rule).CSSEditorAddOptions(opts_border_width); break;
		
		case 'borderColor': 
		case 'borderTopColor': 
		case 'borderRightColor':
		case 'borderBottomColor':
		case 'borderLeftColor': set.CSSEditorProperty(props_names[i], rule, 'Color').CSSEditorTransparency(props_names[i], rule).CSSEditorColorPicker(props_names[i], rule); break;
		
		case 'borderStyle':
		case 'borderTopStyle':
		case 'borderRightStyle':
		case 'borderBottomStyle':
		case 'borderLeftStyle': set.CSSEditorProperty(props_names[i], rule, 'Style').CSSEditorBorderStyle(props_names[i], rule); break;
		
		case 'width': set.CSSEditorProperty('width', rule, 'Width').CSSEditorLength('width', rule, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'height': set.CSSEditorProperty('height', rule, 'Height').CSSEditorLength('height', rule, true); break;
		
		case 'left': set.CSSEditorProperty('left', rule, 'Left').CSSEditorLength('left', rule, true, true); break;
		case 'right': set.CSSEditorProperty('right', rule, 'Right').CSSEditorLength('right', rule, true, true); break;
		case 'top': set.CSSEditorProperty('top', rule, 'Top').CSSEditorLength('top', rule, true, true); break;
		case 'bottom': set.CSSEditorProperty('bottom', rule, 'Bottom').CSSEditorLength('bottom', rule, true, true); break;
		
		case 'paddingTop': set.CSSEditorProperty('paddingTop', rule, 'Top').CSSEditorLength('paddingTop', rule, true); break;
		case 'paddingRight': set.CSSEditorProperty('paddingRight', rule, 'Right').CSSEditorLength('paddingRight', rule, true); break;
		case 'paddingBottom': set.CSSEditorProperty('paddingBottom', rule, 'Bottom').CSSEditorLength('paddingBottom', rule, true); break;
		case 'paddingLeft': set.CSSEditorProperty('paddingLeft', rule, 'Left').CSSEditorLength('paddingLeft', rule, true); break;
		
		case 'marginTop': set.CSSEditorProperty('marginTop', rule, 'Top').CSSEditorLength('marginTop', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'marginRight': set.CSSEditorProperty('marginRight', rule, 'Right').CSSEditorLength('marginRight', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'marginBottom': set.CSSEditorProperty('marginBottom', rule, 'Bottom').CSSEditorLength('marginBottom', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		case 'marginLeft': set.CSSEditorProperty('marginLeft', rule, 'Left').CSSEditorLength('marginLeft', rule, true, true).CSSEditorAddOptions({auto: 'auto'}); break;
		}
	}
}

var editors = 
	[
	 {	setName: 'Font', setCode: 'text', properties: [ 'fontSize', 'fontFamily', 'fontWeight', 'fontStyle', 'textDecoration', 'color' ] },
	 {  setName: 'Paragraph', setCode: 'para', properties: [ 'textAlign', 'textIndent', 'lineHeight' ] },
//	 {	setName: 'Align', setCode: 'text-align', properties: [ 'textAlign' ] },
//	 {	setName: 'Line Height', setCode: 'line-height', properties: [ 'lineHeight' ] },
	 
	 {	setName: 'Background', setCode: 'background', properties: [ 'backgroundColor', 'backgroundImage', 'backgroundRepeat', 'backgroundAttachment', 'backgroundPosition' ] },
	 
	 {	setName: 'Top Border', setCode: 'border-top', alias: 'border', properties: [ 'borderTopColor', 'borderTopWidth', 'borderTopStyle' ] },
	 {	setName: 'Right Border', setCode: 'border-right', alias: 'border', properties: [ 'borderRightColor', 'borderRightWidth', 'borderRightStyle' ] },
	 {	setName: 'Bottom Border', setCode: 'border-bottom', alias: 'border', properties: [ 'borderBottomColor', 'borderBottomWidth', 'borderBottomStyle' ] },
	 {	setName: 'Left Border', setCode: 'border-left', alias: 'border', properties: [ 'borderLeftColor', 'borderLeftWidth', 'borderLeftStyle' ] },
	 
	 {	setName: 'Padding', setCode: 'padding', properties: [ 'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft' ] },
	 {	setName: 'Margin', setCode: 'margin', properties: [ 'marginTop', 'marginRight', 'marginBottom', 'marginLeft' ] },
	 
	 {	setName: 'Size', setCode: 'size', properties: [ 'width', 'height' ] },
	 {	setName: 'Position', setCode: 'position', properties: [ 'left', 'top', 'right', 'bottom' ] }
	];

function createEditors(rule, properties)
{
	for (var i = 0; i < editors.length; i++) {
		var ed = editors[i];
		if (true || rule.isEditable(ed.setCode)) {
			if (ed.properties) {
				var set = properties.CSSEditorPropertySet(ed.setName);
				createProperties(set, rule, ed.properties);
			}
			if (ed.sets) {
				for (var j = 0; j < editors.length; j++) {
					var _ed = editors[j];
					if (jQuery.inArray(_ed.setCode, ed.sets) >= 0) {
						if (_ed.properties) {
							var set = properties.CSSEditorPropertySet(_ed.setName);
							createProperties(set, rule, _ed.properties);
						}
					}
					
				}
			}
		}
	}
}

$.fn.CSSEditorSingleRule = function(rule)
{
	createEditors(rule, $(this));
	return this;
};

$.fn.CSSEditorRuleCommon = function(rule, open)
{
	var created = false;
	var name = rule.getHumanName();
	var widget = $('<div class="rule"></div>').prependTo(this);
	var header = $('<div class="header"></div>').appendTo(widget);
	var icon = $('<span class="plus-minus">Collapse</span>').appendTo(header);
	var name = $('<a href="javascript:void(0)">'+name+'</a>').appendTo(header);
	var properties = $('<div class="properties"></div>').appendTo(widget);

	header.click(function () {
		if (properties.is(':hidden') && ! created) {
			created = true;
			createEditors(rule, properties);
		}
		properties.slideToggle(100);
		icon.toggleClass('minus');
	});
	
	if (open) {
		header.click();
		properties.click();
	}
	
	return this;
};

$.fn.CSSEditorRuleSpecial = function(special_editor)
{
	for (var i = 0; i < special_editor.sets.length; i++) {
		var widget = $('<div class="special"></div>').appendTo(this);
		var header = $('<div class="header"></div>').appendTo(widget).append(special_editor.sets[i].name);
		var properties = $('<div class="properties"></div>').appendTo(widget);
	 	for (var j = 0; j < special_editor.sets[i].rules.length; j++) {
	 		var rule = special_editor.sets[i].rules[j];
		 	createEditors(rule.rule, properties);
	 	}
		properties.slideDown(100);
	}
	
};

//}