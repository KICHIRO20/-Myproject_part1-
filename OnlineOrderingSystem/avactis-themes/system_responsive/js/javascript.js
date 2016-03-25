 function asc_currency_selector_on_change(url)
 {
     sel = document.getElementById('currency_selector');
     currency_id = sel.options[sel.selectedIndex].value;
     url = url.replace('%currency_id_value%', currency_id);
     window.location = url;
 }

function asc_go(url)
{
    if (url == '') return false;
    location.href=url;
    return true;
}

function formatPrice(price, settings)
{
	var t = price.toFixed(settings.precision);

    t = t.split('.');
	var d = t[settings.precision == 0 ? 0 : 1].substr(0,settings.precision);
	
	var p;
	for (p = (t=t[0]).length; (p-=3)>=1;) {
		t = t.substr(0,p)+settings.thousands+t.substr(p);
	}

	var v = (settings.precision > 0)
        ? t+settings.decimal+d+Array((settings.precision+1)-d.length).join(0)
        : t;
    
    var format = price < 0 ? settings.negative : settings.positive;
    return format.replace('{s}', settings.symbol).replace('{v}', v);
}
