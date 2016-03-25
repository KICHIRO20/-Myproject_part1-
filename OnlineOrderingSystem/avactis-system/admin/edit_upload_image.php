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
?><?php
    include('../admin.php');
    if (! modApiFunc('Users', 'checkCurrentUserPermission', PERMISSION_DESIGN)) {
        echo '<div class="no_access">Sorry, You have no access to this content.</div>';
        die;
    }
?>
<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>Upload Theme Image</TITLE>
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
    include('part_htmlheader.php');

    if (isset($_FILES['new_image'])) {
    	if ($_FILES['new_image']['error'] == UPLOAD_ERR_OK) {
            $upload_path = $application->getAppIni('PATH_THEME').'images/upload/';
            $name = $_FILES['new_image']['name'];

            $info = pathinfo($name);
            $base = $info['filename'];
            $ext = $info['extension'];
            $i = 2;
            while (file_exists($upload_path.$name)) {
    	   	    $name = $base.'_'.($i++).'.'.$ext;
            }

            if (@ move_uploaded_file($_FILES['new_image']['tmp_name'], $upload_path.$name)) {
            	$url = '../images/upload/'.$name;
            	?>
<script>
    if (window.opener.onImageUploaded) {
        window.opener.onImageUploaded('<?php echo addslashes($url); ?>');
        window.close();
    }
</script>
            	<?php
                exit;
            }
            else {
            	$error = 'Unable to move the uploaded image into th folder '.$upload_path.'.';
            }
        }
        else {
        	$error = 'An error has occured while uploading an image.';
        }
    }
?>
</HEAD>

<BODY>
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-upload font-green-sharp"></i>
			<span class="caption-subject font-green-sharp bold uppercase">Upload Theme Image</span>
			<span class="caption-helper text-lowercase">Select image to upload...</span>
		</div>
	</div>
	<div class="portlet-body">
		<form action="edit_upload_image.php" method="post" enctype="multipart/form-data" id="UploadThemeImageForm">
		<!-- input type="hidden" name="asc_action" value="" />
		<input type="hidden" name="ViewState[hasCloseScript]" value="false" />
		<input type="hidden" name="topic" value="{TopicId}" / -->

		<?php if (isset($error)) { ?>
		  <div class="note note-bordered note-danger"><?php echo $error ?></div>
		<?php } ?>

		<div class="alert alert-success">
			<span class="required">Image &nbsp;*&nbsp;&nbsp;</span> <input type="file" name="new_image" size="55" class="input" style="display:inline;">
		</div>
		<div class="actions text-center">
			<div class="actions btn-set">
				<a id="SaveButton2" class="btn blue"><?php Msg('BTN_UPLOAD'); ?></a>
				<a id="CancelButton2" class="btn btn-default" onclick="closeAndFocusParent();"><?php Msg('BTN_CANCEL'); ?></a>
			</div>
		</div>
	</div>
</div>
</form>
<?php include('part_footer_popup.php') ?>
<script>
jQuery(function ($) {
    var btn = $('#SaveButton2');
    btn.one('click', function (event) {
        btn.addClass('button_disabled');
        $('#UploadThemeImageForm').submit();
    });
});
</script>
</BODY>
</HTML>