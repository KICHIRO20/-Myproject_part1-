    function NewWindow(windowName, windowURL, action, showWindow, alert_message)
    {
        if (!showWindow)
        {
            return;
        }
        var newwin;
        var URL = windowURL;
        if (action == 'Info' || action == 'Edit' || action == 'Del' || action == 'Move')
        {
            var i=0;
            var elem = document.catListForm.category_id;
            if (elem)
            {
                if (!elem.length)
                {
                    URL = windowURL+elem.value;
                }
                else
                {
                    while (elem[i])
                    {
                        if (elem[i].checked)
                        {
                            URL = windowURL+elem[i].value;
                            break;
                        }
                        i++;
                    }
                }
                if (URL == windowURL)
                {
                    alert(alert_message);
                    return;
                }
            }
            else
            {
                alert(alert_message);
                return;
            }
        }
/*        newwin = window.open(URL, windowName); */
        var newWin = openURLinNewWindow(URL, windowName);
        //newWin.focus();
    }

    function NewWindowExt(windowName, windowURL, action, showWindow, alert_message, form_name, el_name)
    {
        if (!showWindow)
        {
            return;
        }
        var newwin;
        var URL = windowURL;
        if (action == 'Info' || action == 'Edit' || action == 'Del' || action == 'Move')
        {
            var i=0;
            var elem = document.forms[form_name][el_name];
            if (elem)
            {
                if (!elem.length)
                {
                    URL = windowURL+elem.value;
                }
                else
                {
                    while (elem[i])
                    {
                        if (elem[i].checked)
                        {
                            URL = windowURL+elem[i].value;
                            break;
                        }
                        i++;
                    }
                }
                if (URL == windowURL)
                {
                    alert(alert_message);
                    return;
                }
            }
            else
            {
                alert(alert_message);
                return;
            }
        }
	newwin = window.location = URL;
    }
