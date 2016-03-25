<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Image</title>
<!--
    CSS work around to remove unnacessary vertical scrollbar in IE
    http://archivist.incutio.com/viewlist/css-discuss/573

    Actually just complex css-filter (so it's initially bad)
    http://www.dithered.com/css_filters/index.html

<STYLE type="text/css">
    html,body { overflow: auto; }
    html>body { overflow: visible; }
    html      { height: 100%; }
</STYLE>
-->
<script language="JavaScript">
<!--

function setViewportSizeAndWindowPosition(width, height)
{
    //detect viewport width (inner size : without toolbars etc)
    if (self.innerWidth)
    {
        //FireFox
        frameWidth = self.innerWidth;
        frameHeight = self.innerHeight;

        windowWidth = window.outerWidth;
        windowHeight = window.outerHeight;
    }
    else if (document.documentElement && document.documentElement.clientWidth)
    {
        //IE 6+ in 'standards compliant mode'
        windowWidth=500;
        windowHeight=500;
        window.resizeTo(windowWidth,windowHeight);
        frameWidth = document.documentElement.clientWidth;
        frameHeight = document.documentElement.clientHeight;
    }else return;
/*    else if (document.body)
    {
        //IE 4 compatible
        frameWidth = document.body.clientWidth;
        frameHeight = document.body.clientHeight;
    }
    else return; */

    ToulbarsWidth = windowWidth - frameWidth;
    ToulbarsHeight = windowHeight - frameHeight;
    UpdatedWindowWidth = ToulbarsWidth + width;
    UpdatedWindowHeight = ToulbarsHeight + height;

    var MaxUpdatedWindowWidth = 0.80 * screen.width;
    var MaxUpdatedWindowHeight = 0.80 * screen.height;

    UpdatedWindowWidth = (UpdatedWindowWidth > MaxUpdatedWindowWidth) ? MaxUpdatedWindowWidth : UpdatedWindowWidth;
    UpdatedWindowHeight = (UpdatedWindowHeight > MaxUpdatedWindowHeight) ? MaxUpdatedWindowHeight : UpdatedWindowHeight;

    window.resizeTo(UpdatedWindowWidth, UpdatedWindowHeight);

    /* set window position: center window */
    var newPosLeft = 0.5 * (screen.availWidth - UpdatedWindowWidth);
    var newPosTop = 0.5 * (screen.availHeight - UpdatedWindowHeight);

    self.moveTo(newPosLeft, newPosTop);
}

function AdjustWindowSizeToImageSize()
{
    setViewportSizeAndWindowPosition(document.images[0].width, document.images[0].height);
}
//-->
</script>
</head>
<body onLoad="AdjustWindowSizeToImageSize();">
<img src="<?php echo $_GET['image_filename']; ?>" style="position: absolute; top: 0; left: 0;">
</body>
</html>