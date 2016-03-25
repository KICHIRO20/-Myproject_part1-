jQuery.fn.ProductOptionsChoice = function (modifiers, inventory, combinations_formula)
{
	var options_box = $(this);
	
	// На некоторые элементы формы нужно навесить обработчики события 'change',
	// чтобы сгенерировать событие 'change_option' ("одна из опций изменена").
	options_box.find('.form_row')
	
	.filter('.multiselect,.dropdown').each(function () {
		$(this).find('select').change(function () {
			options_box.trigger('one_option_change');
		});
	}).end()
	
	.filter('.radio').each(function () {
		$(this).find('input[type=radio]').change(function () {
			options_box.trigger('one_option_change');
		});
	}).end()
	
	.filter('.checkbox_set,.checkbox_input,.checkbox_text').each(function () {
		$(this).find('input[type=checkbox]').change(function () {
			options_box.trigger('one_option_change');
		});
	}).end()
	
	.filter('.file').each(function () {
		$(this).find('input[type=file]').change(function () {
			options_box.trigger('one_option_change');
		});
	});
	
	// Обработчик события "одна из опций изменена".
	options_box.bind('one_option_change', function() {
		var values = {};
		
		// fetch values of all options 
		options_box.find('.form_row')
		
		.filter('.checkbox_set').each(function () {
			// set of checkboxes
			var cb_name = $(this).find('input[type=checkbox]').attr('name');
			var option_id = cb_name.match(/^po\[(\d+)\]/)[1];
			var value = [];
			$(this).find('input[type=checkbox]:checked').each(function () {
				var opt_name = $(this).attr('name');
				var option_value = opt_name.match(/^po\[\d+\]\[(\d+)\]/)[1];
				value.push(option_value);
			});
			values[ option_id ] = value;
		}).end()
		
		.filter('.radio').each(function () {
			// set of radio buttons
			var option_id = $(this).find('input[type=radio]').attr('name').match(/^po\[(\d+)\]/)[1];
			var value = [];
			$(this).find('input[type=radio]:checked').each(function () {
				value.push($(this).val());
			});
			values[ option_id ] = value;
		}).end()
		
		.filter('.multiselect,.dropdown').each(function () {
			// single select dropdown or multiple select list
			var $sel = $(this).find('select');
			var option_id = $sel.attr('name').match(/^po\[(\d+)\]/)[1];
			var value = $sel.val();
			if (value == null) {
				value = [];
			}
			else
			if (typeof value == 'string') {
				value = [ value ];
			}
			values[ option_id ] = value;
		}).end()
		
		.filter('.checkbox_input,.checkbox_text').each(function () {
			// optional text (with checkbox)
			var $cb = $(this).find('input[type=checkbox]');
			var option_id = $cb.attr('name').match(/^po\[(\d+)\]/)[1];
			values[ option_id ] = [ $cb.attr('checked') ? 'on' : 'off' ];
		}).end()
		
		.filter('.file').each(function () {
			// file
			var $file = $(this).find('input[type=file]');
			var option_id = $file.attr('name').match(/^po\[(\d+)\]/)[1];
			values[ option_id ] = [ $file.val() ? 'on' : 'off' ];
		}).end()

        .find('input[type=text], textarea').each(function () {
            var option_id = $(this).attr('name').match(/^po\[(\d+)\]/)[1];
            if(!values[option_id]) values[ option_id ] = [ $(this).val() ? 'on' : 'off' ];
        });
		
		// Вычисление суммы модификаторов согласно комбинации значений опций
		var values_ids = [];
		for(var option_id in values) {
			values_ids = values_ids.concat(values[option_id]);
		}
		var values_ids_re = new RegExp('\\{' + values_ids.join('\\}|\\{') + '\\}', 'g');
		
		var result = {
				modifiers: calcModifiers(modifiers, values),
				quantity: calcQuantity(inventory, values_ids_re),
				combination: checkCombinations(combinations_formula, values_ids_re)
		};
		options_box.trigger('options_change', [result]);
	})
	.trigger('one_option_change');
};

function calcModifiers(modifiers, options_values)
{
	var price = 0;
	var weight = 0;
	
	for (var option_id in modifiers) {
		var option_modifier = modifiers[option_id];
		if (option_id in options_values) {
			for (var value_num in options_values[option_id]) {
				var value_id = options_values[option_id][value_num];
				if (typeof option_modifier[value_id] == 'object') {
					price += option_modifier[value_id].price;
					weight += option_modifier[value_id].weight;
				}
			}
		}
	}
	return {price: price, weight: weight};
}

function calcQuantity(inventory, values_ids_re)
{
	var stock_limit = null;
    var any_selected = /[0-9]+/.test(values_ids_re);
	for (var i = 0; i < inventory.length; i++) {
		var formula = inventory[i].formula;
        if(any_selected && !(/[0-9]+/.test(formula))) continue;
		formula = formula.replace(values_ids_re, 'true');
		formula = formula.replace(/\{\d+\}/g, 'false');
		if (eval(formula)) {
			stock_limit = parseInt(inventory[i].quantity);
			break;
		}
	}
	return stock_limit;
}

function checkCombinations(formula, values_ids_re)
{
	formula = formula.replace(values_ids_re, 'true');
	formula = formula.replace(/\{\d+\}/g, 'false');
	return eval(formula);
}
