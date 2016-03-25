// File Name: shuttle.js
//
// Description: Contains all JS logic for shuttle controls. 
//
// Author: wzhang
// Created: May 21 2003
// Modified May 21 2003

//adjust vertical scroll
function scrollToOption(oShuttleWindow, option_index)
{
    y = oShuttleWindow.options[option_index].offsetTop;
    if(y < oShuttleWindow.offsetTop)
    {
        y = oShuttleWindow.offsetTop;
    }
    oShuttleWindow.scrollTop = y - oShuttleWindow.offsetTop;
}

//Remove prefix from element's name being moved 
function moveItemsExtToAvailable(srcShuttleWindow, tgtShuttleWindow) 
{	
	var oSrcWindow = document.getElementById(srcShuttleWindow);
	if (oSrcWindow == null || typeof(oSrcWindow) == "undefined")
		return;

	var oTgtWindow = document.getElementById(tgtShuttleWindow);
	if (oTgtWindow == null || typeof(oTgtWindow) == "undefined")
		return;

    if (oSrcWindow.disabled == true || oTgtWindow.disabled == true)
        return;

    var j = 0;
    for (i = 0; i < oSrcWindow.options.length; i++) 
    {        
        if (oSrcWindow.options[i].selected) 
        { 
            // Add to target shuttle window
            var oOption = document.createElement("OPTION");
            oTgtWindow.options.add(oOption); 			
            oOption.value = oSrcWindow.options[i].value;
            //Get short name for this payment module by its ID (option.value)
            oOption.text = getPaymentModuleInfo(oSrcWindow.options[i].value, 'ShortName');
            //End Get short name ...
            if (oSrcWindow.options[i].className  != null)
                oOption.className = oSrcWindow.options[i].className ;
        }
    }
	
    for (i = 0; i < oSrcWindow.options.length + j; i++) 
    {        
        if (oSrcWindow.options[i - j].selected) 
        {   			
            // Remove from source shuttle window
            oSrcWindow.remove(i - j);
            j++;
        }
    }	

    updateHiddenInput(oSrcWindow, oSrcWindow.name + ".hidden");
    updateHiddenInput(oTgtWindow, oTgtWindow.name + ".hidden");
}

//Append prefix to element't name being moved
function moveItemsExtToSelected(srcShuttleWindow, tgtShuttleWindow) 
{	
//alert('srcShuttleWindow='+srcShuttleWindow+' tgtShuttleWindow='+tgtShuttleWindow);
	var oSrcWindow = document.getElementById(srcShuttleWindow);
	if (oSrcWindow == null || typeof(oSrcWindow) == "undefined")
		return;

	var oTgtWindow = document.getElementById(tgtShuttleWindow);
	if (oTgtWindow == null || typeof(oTgtWindow) == "undefined")
		return;

    if (oSrcWindow.disabled == true || oTgtWindow.disabled == true)
        return;

    var j = 0;
    for (i = 0; i < oSrcWindow.options.length; i++) 
    {        
        if (oSrcWindow.options[i].selected) 
        { 
            // Add to target shuttle window
            var oOption = document.createElement("OPTION");
            oTgtWindow.options.add(oOption); 			
            oOption.value = oSrcWindow.options[i].value;
            //Get full (with prefix) name for this payment module by its ID (option.value)
//alert(oOption.value);
            oOption.text = getPaymentModuleInfo(oSrcWindow.options[i].value, 'FullName');

            //End Get full (with prefix) name ...

            if (oSrcWindow.options[i].className  != null)
                oOption.className = oSrcWindow.options[i].className ;
        }
    }
	
    for (i = 0; i < oSrcWindow.options.length + j; i++) 
    {        
        if (oSrcWindow.options[i - j].selected) 
        {   			
            // Remove from source shuttle window
             oSrcWindow.remove(i - j);
            // bag fix for IE9
            //$('select optgroup option[value="' + oSrcWindow.options[i - j].value + '"]').remove();
            j++;
        }
    }	

    updateHiddenInput(oSrcWindow, oSrcWindow.name + ".hidden");
    updateHiddenInput(oTgtWindow, oTgtWindow.name + ".hidden");
}

// Move selected items in a shuttle window to another shuttle window
function moveItems(srcShuttleWindow, tgtShuttleWindow) 
{	
	var oSrcWindow = document.getElementById(srcShuttleWindow);
	if (oSrcWindow == null || typeof(oSrcWindow) == "undefined")
		return;

	var oTgtWindow = document.getElementById(tgtShuttleWindow);
	if (oTgtWindow == null || typeof(oTgtWindow) == "undefined")
		return;

    if (oSrcWindow.disabled == true || oTgtWindow.disabled == true)
        return;

    var j = 0;
    for (i = 0; i < oSrcWindow.options.length; i++) 
    {        
        if (oSrcWindow.options[i].selected) 
        { 
            // Add to target shuttle window
            var oOption = document.createElement("OPTION");
            oTgtWindow.options.add(oOption); 			
            oOption.value = oSrcWindow.options[i].value;
            oOption.text = oSrcWindow.options[i].text;
            if (oSrcWindow.options[i].className  != null)
                oOption.className = oSrcWindow.options[i].className ;
        }
    }
	
    for (i = 0; i < oSrcWindow.options.length + j; i++) 
    {        
        if (oSrcWindow.options[i - j].selected) 
        {   			
            // Remove from source shuttle window
            oSrcWindow.remove(i - j);
            j++;
        }
    }	

    updateHiddenInput(oSrcWindow, oSrcWindow.name + ".hidden");
    updateHiddenInput(oTgtWindow, oTgtWindow.name + ".hidden");
}

// Move all items in a shuttle window to another shuttle window
function moveAllItems(srcShuttleWindow, tgtShuttleWindow) 
{	
	var oSrcWindow = document.getElementById(srcShuttleWindow);
	if (oSrcWindow == null || typeof(oSrcWindow) == "undefined")
		return;

	var oTgtWindow = document.getElementById(tgtShuttleWindow);
	if (oTgtWindow == null || typeof(oTgtWindow) == "undefined")
		return;

    if (oSrcWindow.disabled == true || oTgtWindow.disabled == true)
        return;
	
    for (i = 0; i < oSrcWindow.options.length; i++) 
    {        
        // Add to target shuttle window
        var oOption = document.createElement("OPTION");
        oTgtWindow.options.add(oOption); 			
        oOption.value = oSrcWindow.options[i].value;
        oOption.text = oSrcWindow.options[i].text;
        if (oSrcWindow.options[i].className  != null)
            oOption.className = oSrcWindow.options[i].className ;
    }
	
    while (oSrcWindow.options.length > 0) 
    {        
        // Remove from source shuttle window
        oSrcWindow.options.remove(0);
    }	

    updateHiddenInput(oSrcWindow, oSrcWindow.name + ".hidden");
    updateHiddenInput(oTgtWindow, oTgtWindow.name + ".hidden");
}

// Swap positions of two items in a shuttle window
function swapItems(shuttleWindow, i, j) 
{
	if (shuttleWindow == null || typeof(shuttleWindow) == "undefined")
		return;
		
    if (shuttleWindow.options[i].selected)
    {
        var o = shuttleWindow.options; 
    }
    
    var i_className = new String (o[i].className);
    var i_text = o[i].text;
    var i_value = o[i].value;
    var i_defaultSelected = o[i].defaultSelected;
    var i_selected = o[i].selected;
    
    o[i].className = new String (o[j].className);
    o[i].text = o[j].text;
    o[i].value = o[j].value;
    o[i].defaultSelected = o[j].defaultSelected;
    o[i].selected = o[j].selected;
    
    o[j].className = i_className;
    o[j].text = i_text;
    o[j].value = i_value;
    o[j].defaultSelected = i_defaultSelected;
    o[j].selected = i_selected;
}

// Move selected option in a shuttle window up one
function moveItemUp(shuttleWindow) 
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;

    // If nothing is selected return
    if(oShuttleWindow.options.selectedIndex < 0)
    {	
        return;
    }
  
    var selectedCount=0;
    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {
        if (oShuttleWindow.options[i].selected) 
        {
            selectedCount++;
        }
    }
    if (selectedCount > 1) 
    {
        return;
    }
   
    // If this is the first item in the list, do nothing
    var i = oShuttleWindow.selectedIndex;
    if (i == 0) 
    {
        return;
    }
	
    swapItems(oShuttleWindow, i, i-1);
    oShuttleWindow.options[i-1].selected = true;
    scrollToOption(oShuttleWindow, i-1);

    updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden");
}

// Move selected option in a shuttle window up one
function moveFirstItemUp(shuttleWindow, tgtShuttleWindow) 
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;

    // If nothing is selected return
    if(oShuttleWindow.options.selectedIndex < 0)
    {	
        return;
    }
  
    var selectedCount=0;
    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {
        if (oShuttleWindow.options[i].selected) 
        {
            selectedCount++;
        }
    }
    if (selectedCount > 1) 
    {
        return;
    }
   
    var i = oShuttleWindow.selectedIndex;
    // If this is the first item in the list, try to move to the window above
    if (i == 0) 
    {
        var oTgtWindow = document.getElementById(tgtShuttleWindow);
	    if (oTgtWindow == null || typeof(oTgtWindow) == "undefined")
		    return;

        // Add to target shuttle window
        var oOption = document.createElement("OPTION");
        oTgtWindow.options.add(oOption); 			
        oOption.value = oShuttleWindow.options[i].value;
        oOption.text = oShuttleWindow.options[i].text;
        if (oShuttleWindow.options[i].className  != null)
            oOption.className = oShuttleWindow.options[i].className ;
     	
        // Remove from source shuttle window
        oShuttleWindow.options.remove(i);

        updateHiddenInput(oTgtWindow, oTgtWindow.name + ".hidden");
        updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden");        
        return;
    }
	
    swapItems(oShuttleWindow, i, i-1);
    oShuttleWindow.options[i-1].selected = true;
    scrollToOption(oShuttleWindow, i-1);

    updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden");
}

// Move selected option in a shuttle window down one
function moveLastItemDown(shuttleWindow, tgtShuttleWindow)  
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;

    // If nothing is selected return
    if(oShuttleWindow.options.selectedIndex < 0)
    {	
        return;
    }
    var selectedCount=0;
    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {        
        if (oShuttleWindow.options[i].selected) 
        { 
            selectedCount++;
        }
    }
    
    // If > 1 option selected, do nothing
    if (selectedCount > 1) 
    {
    	return;
    }
	
    // If this is the last item in the list, try to move to the window below
    var i = oShuttleWindow.selectedIndex;
    if (i == oShuttleWindow.options.length - 1) 
    {
        var oTgtWindow = document.getElementById(tgtShuttleWindow);
	    if (oTgtWindow == null || typeof(oTgtWindow) == "undefined")
		    return;

        // Add to target shuttle window
        var oOption = document.createElement("OPTION");
        oTgtWindow.options.add(oOption); 			
        oOption.value = oShuttleWindow.options[i].value;
        oOption.text = oShuttleWindow.options[i].text;
        if (oShuttleWindow.options[i].className  != null)
            oOption.className = oShuttleWindow.options[i].className ;
     	
        // Remove from source shuttle window
        oShuttleWindow.options.remove(i);

        updateHiddenInput(oTgtWindow, oTgtWindow.name + ".hidden");
        updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden"); 
        return;
    }

    swapItems(oShuttleWindow, i, i+1);
    oShuttleWindow.options[i+1].selected = true;
    scrollToOption(oShuttleWindow, i+1);

    updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden");
}

// Move selected option in a shuttle window up to the top
function moveItemUpTop(shuttleWindow) 
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;

    // If nothing is selected return
    if(oShuttleWindow.options.selectedIndex < 0)
    {	
        return;
    }
  
    var selectedCount=0;
    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {
        if (oShuttleWindow.options[i].selected) 
        {
            selectedCount++;
        }
    }
    
    if (selectedCount > 1) 
    {
        return;
    }
   
    // If this is the first item in the list, do nothing
    var i = oShuttleWindow.selectedIndex;
    if (i == 0) 
    {
        return;
    }

    for (j = i; j > 0; j--) 	
        swapItems(oShuttleWindow, j, j-1);

    oShuttleWindow.options[j].selected = true;
    scrollToOption(oShuttleWindow, j);

    updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden");
}

// Move selected option in a shuttle window down one
function moveItemDown(shuttleWindow)  
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;

    // If nothing is selected return
    if(oShuttleWindow.options.selectedIndex < 0)
    {	
        return;
    }
    var selectedCount=0;
    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {        
        if (oShuttleWindow.options[i].selected) 
        { 
            selectedCount++;
        }
    }
    
    // If > 1 option selected, do nothing
    if (selectedCount > 1) 
    {
    	return;
    }
	
    // If this is the last item in the list, do nothing
    var i = oShuttleWindow.selectedIndex;
    if (i == oShuttleWindow.options.length - 1) 
    {
        return;
    }

    swapItems(oShuttleWindow, i, i+1);
    oShuttleWindow.options[i+1].selected = true;   
    scrollToOption(oShuttleWindow, i+1);

    updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden");
}

// Move selected option in a shuttle window down to the bottom
function moveItemDownBottom(shuttleWindow) 
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;

    // If nothing is selected return
    if(oShuttleWindow.options.selectedIndex < 0)
    {	
        return;
    }
    var selectedCount=0;
    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {        
        if (oShuttleWindow.options[i].selected) 
        { 
            selectedCount++;
        }
    }
    
    // If > 1 option selected, do nothing
    if (selectedCount > 1) 
    {
    	return;
    }
	
    // If this is the last item in the list, do nothing
    var i = oShuttleWindow.selectedIndex;
    if (i == oShuttleWindow.options.length - 1) 
    {
        return;
    }
    
    for (j = i; j < oShuttleWindow.options.length - 1; j++) 	
        swapItems(oShuttleWindow, j, j+1);

    //Remove next two lines?
    oShuttleWindow.options[oShuttleWindow.options.length - 1].selected = true;
    scrollToOption(oShuttleWindow, oShuttleWindow.options.length - 1);

    updateHiddenInput(oShuttleWindow, oShuttleWindow.name + ".hidden");
}

// Select all items in a shuttle window
function selectAll(shuttleWindow)
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;

    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {
        oShuttleWindow.options[i].selected = true; 
    }	
}

// De-Select all items in a shuttle window
function clearAll(shuttleWindow)
{
	var oShuttleWindow = document.getElementById(shuttleWindow);
	if (oShuttleWindow == null || typeof(oShuttleWindow) == "undefined")
		return;
	
    for (i = 0; i < oShuttleWindow.options.length; i++) 
    {
        oShuttleWindow.options[i].selected = false; 
    }
}

// Update the hidden input control, || is the list item separator
function updateHiddenInput(srcShuttleWindow, tgtHiddenInput)
{
	if (srcShuttleWindow == null || typeof(srcShuttleWindow) == "undefined")
		return;

    var itemSeparator = "|";
    var value = "";

    for (i = 0; i < srcShuttleWindow.options.length; i++) 
    {
        value += srcShuttleWindow.options[i].value;
        if (i < srcShuttleWindow.options.length - 1)
        {
            value += itemSeparator;
        }
    }	
	var HiddenField = document.getElementById(tgtHiddenInput);
//document.write(value+'\n');
//document.write(HiddenField.value+'\n');
	HiddenField.value=value;
//document.write(HiddenField.value+'\n');
//    setControlValue (tgtHiddenInput, value, true);
}

// Unselect all items so that only the current item can be selected
function unselectAll (stw)
{
    var oStw = document.getElementById(stw);
    if (oStw == null || typeof(oStw) == "undefined")
        return;

    var oColl = oStw.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.all;
    if (oColl == null || oColl.length == null)
        return;

    var i = 0;
    for (i = 0; i < oColl.length; i++)
    {
        if (oColl(i).name != stw && oColl(i).tagName == "SELECT" && oColl(i).multiple)
        {
            clearAll (oColl(i).name);
        }
    }
}
