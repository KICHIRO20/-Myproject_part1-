var numeric = "0123456789";

/* This array will be redefined in validate.msgs.js.tpl */
var messages = ['{INTEGER}', '{FLOAT}', '{CURRENCY}', '{STRING1024}', '{STRING128}', '{STRING256}', '{STRING512}', '{WEIGHT}', '{ITEM}'];

function formatInput(input) {
    var type = input.getAttribute('patternType');
    if (type == null || type == "") return;
    if (type == 'currency') return formatCurrency(input, messages[2]);
    if (type == 'float') return formatFloat(input, messages[1]);
    if (type == 'integer') return formatInteger(input, messages[0]);
    if (type == 'string1024') return formatString1024(input, messages[3]);
    if (type == 'string128') return formatString128(input, messages[4]);
    if (type == 'string256') return formatString256(input, messages[5]);
    if (type == 'string512') return formatString512(input, messages[6]);
    if (type == 'currency') return formatCurrency(input, messages[2]);
    if (type == 'weight') return formatWeight(input, messages[7]);
    if (type == 'item') return formatInteger(input, messages[8]);
    unit_select = document.getElementById(type);
    if (!unit_select) return;
    pattern = unit_select.options[unit_select.selectedIndex].getAttribute('patternType');
    if (pattern == 'currency') return formatCurrency(input, messages[2]);
    if (pattern == 'integer') return formatInteger(input, messages[0]);
    if (pattern == 'float') return formatFloat(input, messages[1]);
}

function showPopup (input, text) {
    if (window.createPopup) {
        var oPopup = window.createPopup();
        var oPopupBody = oPopup.document.body;
        oPopupBody.style.backgroundColor = "lightyellow";
        oPopupBody.style.border = "solid black 1px";
        oPopupBody.style.fontFamily = "Tahoma";
        oPopupBody.style.fontSize = "11px";
        oPopupBody.innerHTML = "&nbsp;<strong>" + text + "</strong>";
        oPopup.show(15, 15, 320, 34, input);
        input.focus();
        //input.select();
    } else {
        alert(text);
    }
    return;
}

function showPopup_addProduct (input, text) {
    if (window.createPopup) {
        var oPopup = window.createPopup();
        var oPopupBody = oPopup.document.body;
        oPopupBody.style.backgroundColor = "lightyellow";
        oPopupBody.style.border = "solid black 1px";
        oPopupBody.style.fontFamily = "Tahoma";
        oPopupBody.style.fontSize = "11px";
        oPopupBody.innerHTML = "&nbsp;<strong>" + text + "</strong>";
        oPopup.show(15, 15, 320, 34, input);
/*        input.focus();
        input.select(); */
    } else {
        alert(text);
    }
    return;
}

function isNumber (num) {
    for(i = 0, found = false; i < num.length; i++) {
        ch = num.charAt(i);
        if (numeric.indexOf(ch) > -1) {
            found = true;
            break;
        }
    }
    if (!found) {
        return false;
    }
    return true;
}

function formatCurrency(input, text) {

	var decimals = input.getAttribute('decimals');
	var dec_point = input.getAttribute('dec_point');
    return formatFloat(input, text, decimals, dec_point);
}

function formatWeight(input, text) {

	var decimals = input.getAttribute('decimals');
	var dec_point = input.getAttribute('dec_point');
    return formatFloat(input, text, decimals, dec_point);
}

function formatFloat(input, text, fractional_part_length, dec_point) {
    var num = input.value;
    if (num.length == 0)
        return;
    var num = input.value.replace(/[^0123456789]/g, '.').replace(/ /g, '').replace(/\,/g, '.')
    																	  .replace(/\-/g, '.')
    																	  .replace(/\=/g, '.')
    																	  .replace(/\//g, '.')
    																	  .replace(/\;/g, '.')
    																	  .replace(/\:/g, '.')
    																	  .replace(/\'/g, '.');

    if (!isNumber(num.replace(/\./g, ''))) {
        input.value = '';
        showPopup (input, text);
        return;
    }


    var num_parts = num.split('.');
    if(num_parts.length >0 && num_parts[0] == '')
    {
        //alert('[' + num_parts[0] + ']');
        num_parts[0] = '0';
    }

    var integer_part_has_been_already_found = 0;
    var num = '';
    for(i = 0; i < num_parts.length; i++) {
        if(num_parts[i] != '')
        {
            if(integer_part_has_been_already_found == 1)
            {
                num_parts[i] = (num_parts[i] + '0000000000').substring(0,fractional_part_length);
            }

            num = num + num_parts[i];
            if(integer_part_has_been_already_found == 1)
            {
                break;
            }
            else
            {
                integer_part_has_been_already_found = 1;
                num = num + dec_point;
            }
        }
    }

    //: LOCALIZATION , not only '.' may be delimiter
    if(num.charAt(num.length-1) == dec_point)
    {   if(fractional_part_length != null)
        {
            num = num + ('0000000000'.substring(0,fractional_part_length));
        }
        else
        {
            num = num + '000';
        }
    }

    input.value = num;
}

function formatInteger(input, text) {
    var negative = false;
    var num = input.value;
    if (num.length == 0)
        return;
    if ((num*1)<0)
    {
        negative = true;
    }

    var num = input.value.replace(/\,/g, '.').replace(/ /g, '').replace(/[^0123456789\.\,]/g, '');
    var num_parts = num.split('.');
    var num = num_parts[0];

    if (!isNumber(num)) {
        input.value = '';
        showPopup (input, text);
        return;
    }

    if (negative)
        input.value = '-'+num;
    else
        input.value = num;
}

function formatString1024(input, text) {
    var num = input.value;

    if (num.length > 1024){
        input.value = num.substr(0, 1024);
        showPopup (input, text);
        return;
    }
}

function formatString128(input, text) {
    var num = input.value;

    if (num.length > 128){
        input.value = num.substr(0, 128);
        showPopup (input, text);
        return;
    }
}

function formatString256(input, text) {
    var num = input.value;

    if (num.length > 256){
        input.value = num.substr(0, 256);
        showPopup (input, text);
        return;
    }
}

function formatString512(input, text) {
    var num = input.value;

    if (num.length > 512){
        input.value = num.substr(0, 512);
        showPopup (input, text);
        return;
    }
}