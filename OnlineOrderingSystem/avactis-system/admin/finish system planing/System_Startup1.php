<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>System Startup</title>
<meta name="generator" content="WYSIWYG Web Builder 10 - http://www.wysiwygwebbuilder.com">
<link href="wb.validation.css" rel="stylesheet">
<link href="Untitled1.css" rel="stylesheet">
<link href="System_Startup1.css" rel="stylesheet">
<script src="jquery-1.11.1.min.js"></script>
<script src="wb.validation.min.js"></script>
<script>
$(document).ready(function()
{
   $("#Form1").submit(function(event)
   {
      var isValid = $.validate.form(this);
      return isValid;
   });
   $("#Editbox1").validate(
   {
      required: true,
      type: 'number',
      expr_min: '>=',
      expr_max: '<=',
      value_min: '',
      value_max: '',
      length_max: '9',
      color_text: '#000000',
      color_hint: '#00FF00',
      color_error: '#FF0000',
      color_border: '#808080',
      nohint: true,
      font_family: 'Arial',
      font_size: '13px',
      font_style: 'italic',
      position: 'topright',
      offsetx: 0,
      offsety: 0,
      effect: 'fade',
      error_text: 'Please Input TIN number'
   });
   $("#Editbox2").validate(
   {
      required: true,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
      length_max: '9',
      color_text: '#000000',
      color_hint: '#00FF00',
      color_error: '#FF0000',
      color_border: '#808080',
      nohint: true,
      font_family: 'Arial',
      font_size: '13px',
      position: 'topright',
      offsetx: 0,
      offsety: 0,
      effect: 'fade',
      error_text: 'Please Input the Business Permit'
   });
   $("#Editbox3").validate(
   {
      required: true,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
      length_max: '9',
      color_text: '#000000',
      color_hint: '#00FF00',
      color_error: '#FF0000',
      color_border: '#808080',
      nohint: true,
      font_family: 'Arial',
      font_size: '13px',
      position: 'topright',
      offsetx: 0,
      offsety: 0,
      effect: 'fade',
      error_text: 'Please Input The SSN number'
   });
   $("#Editbox4").validate(
   {
      required: true,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
      length_max: '9',
      color_text: '#000000',
      color_hint: '#00FF00',
      color_error: '#FF0000',
      color_border: '#808080',
      nohint: true,
      font_family: 'Arial',
      font_size: '13px',
      position: 'topright',
      offsetx: 0,
      offsety: 0,
      effect: 'fade',
      error_text: 'Please Input The Landline Number'
   });
   $("#Editbox5").validate(
   {
      required: true,
      type: 'email',
      color_text: '#000000',
      color_hint: '#00FF00',
      color_error: '#FF0000',
      color_border: '#808080',
      nohint: true,
      font_family: 'Arial',
      font_size: '13px',
      font_style: 'italic',
      position: 'topright',
      offsetx: 0,
      offsety: 0,
      effect: 'fade',
      error_text: 'Please Input The Email'
   });
});
</script>
</head>
<body>
<div id="container">
<div id="wb_Form1" style="position:absolute;left:275px;top:187px;width:356px;height:861px;z-index:20;">
<form name="Form1" method="POST" enctype="multipart/form-data" id="Form1">
<div id="wb_Text1" style="position:absolute;left:69px;top:15px;width:222px;height:34px;text-align:center;z-index:0;">
<span style="color:#000000;font-family:Impact;font-size:27px;">System Startup</span></div>
<div id="wb_Text2" style="position:absolute;left:16px;top:202px;width:118px;height:17px;text-align:center;z-index:1;">
<span style="color:#000000;font-family:Impact;font-size:13px;">TIN</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text7" style="position:absolute;left:16px;top:304px;width:118px;height:17px;text-align:center;z-index:2;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Bussiness Permit</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text6" style="position:absolute;left:16px;top:248px;width:118px;height:17px;text-align:center;z-index:3;">
<span style="color:#000000;font-family:Impact;font-size:13px;">SSN</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text5" style="position:absolute;left:16px;top:364px;width:118px;height:17px;text-align:center;z-index:4;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Telephone Number</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text4" style="position:absolute;left:16px;top:422px;width:118px;height:17px;text-align:center;z-index:5;">
<span style="color:#000000;font-family:Impact;font-size:13px;">E-mail</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text3" style="position:absolute;left:16px;top:473px;width:118px;height:17px;text-align:center;z-index:6;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Company Image</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="submit" id="Button1" name="Submit" value="Submit" style="position:absolute;left:134px;top:529px;width:96px;height:25px;z-index:7;">
<input type="text" id="Editbox1" style="position:absolute;left:161px;top:199px;width:168px;height:18px;line-height:18px;z-index:8;" name="TN" value="">
<input type="text" id="Editbox2" style="position:absolute;left:161px;top:304px;width:168px;height:19px;line-height:19px;z-index:9;" name="BP" value="">
<input type="text" id="Editbox3" style="position:absolute;left:161px;top:248px;width:168px;height:19px;line-height:19px;z-index:10;" name="SN" value="">
<input type="text" id="Editbox4" style="position:absolute;left:161px;top:361px;width:168px;height:18px;line-height:18px;z-index:11;" name="TN" value="">
<input type="email" id="Editbox5" style="position:absolute;left:161px;top:418px;width:168px;height:19px;line-height:19px;z-index:12;" name="EL" value="">
<div id="wb_Text9" style="position:absolute;left:32px;top:578px;width:137px;height:16px;z-index:13;text-align:left;">
<span style="color:#FF0000;font-family:Arial;font-size:13px;">* Required Field</span></div>
<div id="wb_Text10" style="position:absolute;left:16px;top:616px;width:320px;height:234px;text-align:center;z-index:14;">
<span style="color:#FF0000;font-family:'Bookman Old Style';font-size:13px;">Note; Your Administrator must conform to the following Company is real and legal policy:<br><br>&nbsp;&nbsp; It should be different from the Company Name<br> It should be different from the Company Address<br>It should be different from the TIN Number<br>It should be different from the TIN SSN<br>It should be different from the Business Permit<br>It should be different from the Telephone Number<br>It should be different from the Email<br>It should be different from the Company Image</span></div>
<div id="wb_Text8" style="position:absolute;left:16px;top:93px;width:118px;height:17px;text-align:center;z-index:15;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Company Name</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="text" id="Editbox7" style="position:absolute;left:161px;top:90px;width:168px;height:18px;line-height:18px;z-index:16;" name="Company_name" value="" required>
<div id="wb_Text11" style="position:absolute;left:16px;top:141px;width:118px;height:17px;text-align:center;z-index:17;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Company Address</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="text" id="Editbox8" style="position:absolute;left:161px;top:138px;width:168px;height:18px;line-height:18px;z-index:18;" name="Company_Address" value="" required>
<input type="file" id="FileUpload1" style="position:absolute;left:161px;top:473px;width:168px;height:21px;line-height:21px;z-index:19;" name="Company_Image" required>
</form>
<?php
	include("includes/connect.php");  
	
	if(isset($_POST['submit']))
	{
	 $post_company_name = $_POST['firstname'];
	 $post_company_address = $_POST['middlename'];
	 $post_tin = $_POST['lastname'];
	 $post_ssn = $_POST['birthdate'];
	 $post_bussiness_permit = $_POST['gender'];
	 $post_telephone_number = $_POST['contactNo'];
	 $post_company_image = $_FILES['image']['name'];
	 $image_tmp = $_FILES['image']['tmp_name'];
	 
		{
		
		move_uploaded_file($image_tmp,"../images/$post_image");
		
		$insert_query = "insert into asc_systemstartup(ss_company_name,ss_company_address,ss_tin,ss_ssn,ss_business_permit,ss_contact,ss_email,ss_company_image)

		values('post_company_name','$post_company_address',' $post_tin','post_ssn',
	'$post_business_permit','$post_telephone_number','$post_company_image')";
		
		if(mysql_query($insert_query))
		{
		echo "<center><h1>Post published successfully and User!</h1></center>";
		}
	 }
	}
	?>













</div>
<img src="images/img0003.jpg" id="Banner1" alt="" style="border-width:0;position:absolute;left:0px;top:22px;width:985px;height:148px;z-index:21;">
</div>
</body>
</html>