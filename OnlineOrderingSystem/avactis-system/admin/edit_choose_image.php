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
?><?php include('../admin.php'); ?>
<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>Select Image From Server</TITLE>
<META NAME="Generator" CONTENT="">
<META NAME="Author" CONTENT="Avactis">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<META http-equiv="X-UA-Compatible" content="IE=edge">
<META content="width=device-width, initial-scale=1" name="viewport"/>
<META http-equiv="Content-Type" content="text/html; charset=<?php Charset('AZ'); ?>">
<style>
html, body { width:100%; height:100%; }
</style>

    <?php
    //
    // Insert HTML Header
    //
    $item = 'css_editor_panel';
    include('part_htmlheader.php');
    global $application;
    echo $application->combineAdminCSS(array(
        'styles/jquery.ui.core.css',
        'styles/jquery.ui.resizable.css',
        'styles/jquery.ui.accordion.css',
        'styles/jquery.ui.autocomplete.css',
        'styles/jquery.ui.button.css',
        'styles/jquery.ui.dialog.css',
        'styles/jquery.ui.slider.css',
        'styles/jquery.ui.tabs.css',
        'styles/jquery.ui.datepicker.css',
        'styles/jquery.ui.progressbar.css',
        'styles/jquery.ui.theme.css',
        'styles/css_editor_panel.css',
        'colorpicker/css/colorpicker.css',
    ));
    if (! modApiFunc('Users', 'checkCurrentUserPermission', PERMISSION_DESIGN)) {
        echo '<div class="no_access">Sorry, You have no access to this content.</div>';
        die;
    }

?>
</HEAD>
<BODY class="editor_choose_image">

<?php
    function enumerateImages(&$images_array, $path, $url)
    {
    	$images = glob($path.'/*');
    	$img_url = '../images/';
    	if (is_array($images)) {
    		foreach ($images as $i) {
    			$base_name = basename($i);
    			if (is_dir($i)) {
                    enumerateImages($images_array, $i, $url.$base_name.'/');
    			}
    			else {
                    $images_array[] = array(
                        'name' => $base_name,
                        'size' => modApiFunc("Localization", "num_format", round(filesize($i)/1024))." Kb",
                        'url' => $url.$base_name,
                    );
    			}
    		}
    	}
    }

    $images_array = array();
    enumerateImages($images_array, $application->getAppIni('PATH_THEME').'images', '../images/');
    $css_url = $application->getAppIni('URL_THEME').'css/';

    foreach ($images_array as $i) {
?>

<div class="box">
    <img src="<?php echo $css_url.$i['url'] ?>" class="thumb" />
    <div class="name"><?php echo $i['name'] ?></div>
    <div class="size"><?php echo $i['size'] ?></div>
    <div class="dim"></div>
    <div class="url"><?php echo $i['url'] ?></div>
</div>

<?php
    }
?>
<script>
var max_side = 160;
jQuery(function($){
	$('.box').each(function(){
	    var $this = $(this);
	    var $img = $this.children('.thumb');
        var w = $img.width(), _w = w;
        var h = $img.height(), _h = h;
	    $this.children('.dim').text(w + ' x ' + h);
	    if (_w > max_side || _h > max_side) {
		    var kw = max_side / w;
            var kh = max_side / h;
		    var k = Math.min(kw, kh);
		    _w = Math.round(w * k);
            _h = Math.round(h * k);
            if (_w < 1) {
                _w = 1;
            }
            if (_h < 1) {
                _h = 1;
            }
	    }
        $img.css('width', _w);
	    $img.css('height', _h);
	    $img.show();

        var url = $this.children('.url').text();
	    $this.click(function() {
		    if (window.opener.onImageChoosed) {
			    window.opener.onImageChoosed(url);
			    window.close();
		    }
	    });
	});
});

</script>
</BODY>
</HTML>