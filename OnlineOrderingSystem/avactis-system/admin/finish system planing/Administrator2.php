<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>System Startup</title>
<meta name="generator" content="WYSIWYG Web Builder 10 - http://www.wysiwygwebbuilder.com">
<link href="wb.validation.css" rel="stylesheet">
<link href="Untitled1.css" rel="stylesheet">
<link href="Administrator2.css" rel="stylesheet">
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
   $("#Editbox2").validate(
   {
      required: true,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
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
      error_text: 'Please Input the Mobile Number'
   });
   $("#Editbox4").validate(
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
      position: 'topright',
      offsetx: 0,
      offsety: 0,
      effect: 'fade',
      error_text: 'Please Input The Email like Yahoo or Gmail'
   });
   $("#Editbox5").validate(
   {
      required: true,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
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
      error_text: 'Please Input The Password'
   });
   $("#Editbox6").validate(
   {
      required: true,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
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
      error_text: 'Please Input The Password req.number '
   });
   $("#Combobox1").validate(
   {
      required: true,
      type: 'select',
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
      error_text: 'Please Input The Gender'
   });
});
</script>
</head>
<body>
<div id="container">
<div id="wb_Form1" style="position:absolute;left:275px;top:187px;width:356px;height:827px;z-index:20;">
<form name="Form2" method="post" action="" enctype="multipart/form-data" id="Form1">
<div id="wb_Text1" style="position:absolute;left:69px;top:15px;width:222px;height:34px;text-align:center;z-index:0;">
<span style="color:#000000;font-family:Impact;font-size:27px;">System Startup</span></div>
<div id="wb_Text2" style="position:absolute;left:16px;top:202px;width:118px;height:17px;text-align:center;z-index:1;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Lastname</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text7" style="position:absolute;left:16px;top:304px;width:118px;height:17px;text-align:center;z-index:2;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Contact No</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text6" style="position:absolute;left:16px;top:248px;width:118px;height:17px;text-align:center;z-index:3;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Gender</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text5" style="position:absolute;left:16px;top:367px;width:118px;height:17px;text-align:center;z-index:4;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Username</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text4" style="position:absolute;left:16px;top:422px;width:118px;height:17px;text-align:center;z-index:5;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Password</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="submit" id="Button1" name="Submit" value="Submit" style="position:absolute;left:134px;top:529px;width:96px;height:25px;z-index:6;">
<input type="text" id="Editbox2" style="position:absolute;left:161px;top:304px;width:168px;height:19px;line-height:19px;z-index:7;" name="CN" value="">
<input type="text" id="Editbox4" style="position:absolute;left:161px;top:364px;width:168px;height:18px;line-height:18px;z-index:8;" name="USER" value="">
<input type="password" id="Editbox5" style="position:absolute;left:161px;top:418px;width:168px;height:19px;line-height:19px;z-index:9;" name="PD" value="">
<div id="wb_Text9" style="position:absolute;left:32px;top:578px;width:137px;height:16px;z-index:10;text-align:left;">
<span style="color:#FF0000;font-family:Arial;font-size:13px;">* Required Field</span></div>
<div id="wb_Text8" style="position:absolute;left:16px;top:93px;width:118px;height:17px;text-align:center;z-index:11;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Firstname</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text11" style="position:absolute;left:16px;top:141px;width:118px;height:17px;text-align:center;z-index:12;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Middlename</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="text" id="Editbox8" style="position:absolute;left:161px;top:138px;width:168px;height:18px;line-height:18px;z-index:13;" name="MN" value="" required pattern="[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]*$">
<div id="wb_Text3" style="position:absolute;left:16px;top:481px;width:118px;height:17px;text-align:center;z-index:14;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Re-type Password</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="password" id="Editbox6" style="position:absolute;left:161px;top:477px;width:168px;height:19px;line-height:19px;z-index:15;" name="RPD" value="">
<div id="wb_Text10" style="position:absolute;left:21px;top:628px;width:315px;height:180px;text-align:center;z-index:16;">
<span style="color:#FF0000;font-family:'Bookman Old Style';font-size:13px;">Note; Your Administrator must conform to the following Company is real and legal policy:<br><br>&nbsp;&nbsp;&nbsp; it should be different from the Firstname<br>&nbsp;&nbsp;&nbsp; it should be different from Middlename<br>&nbsp;&nbsp;&nbsp; it should be different from Lastname<br>&nbsp;&nbsp;&nbsp; it should be different from Gender<br>&nbsp;&nbsp;&nbsp; it should be different from Contact No<br>&nbsp;&nbsp;&nbsp; it should be different from Username<br>it should be different from Password.</span></div>
<input type="text" id="Editbox9" style="position:absolute;left:161px;top:90px;width:168px;height:18px;line-height:18px;z-index:17;" name="FN" value="" required pattern="[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]*$">
<select name="Gender" size="1" id="Combobox1" style="position:absolute;left:161px;top:248px;width:170px;height:23px;z-index:18;">
<option selected>~Please Fillup Gender~</option>
<option>Male</option>
<option>Female</option>
</select>
<input type="text" id="Editbox1" style="position:absolute;left:161px;top:199px;width:168px;height:18px;line-height:18px;z-index:19;" name="Lastname" value="" required pattern="[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ]*$">
</form>
</div>
<img src="images/img0001.jpg" id="Banner1" alt="" style="border-width:0;position:absolute;left:0px;top:22px;width:985px;height:148px;z-index:21;">
</div>
</body>
</html>