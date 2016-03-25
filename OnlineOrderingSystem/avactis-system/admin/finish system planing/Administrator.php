<?php
function ValidateEmail($email)
{
   $pattern = '/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i';
   return preg_match($pattern, $email);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['formid'] == 'form1')
{
   $mailto = 'yourname@yourdomain.com';
   $mailfrom = isset($_POST['email']) ? $_POST['email'] : $mailto;
   $subject = 'Website form';
   $message = 'Values submitted from web site form:';
   $success_url = '';
   $error_url = '';
   $error = '';
   $mysql_server = 'localhost';
   $mysql_database = 'oos1';
   $mysql_table = 'asc_admin';
   $mysql_username = 'root';
   $mysql_password = '';
   $eol = "\n";
   $max_filesize = isset($_POST['filesize']) ? $_POST['filesize'] * 1024 : 1024000;
   $boundary = md5(uniqid(time()));

   $header  = 'From: '.$mailfrom.$eol;
   $header .= 'Reply-To: '.$mailfrom.$eol;
   $header .= 'MIME-Version: 1.0'.$eol;
   $header .= 'Content-Type: multipart/mixed; boundary="'.$boundary.'"'.$eol;
   $header .= 'X-Mailer: PHP v'.phpversion().$eol;
   if (!ValidateEmail($mailfrom))
   {
      $error .= "The specified email address is invalid!\n<br>";
   }

   if (!empty($error))
   {
      $errorcode = file_get_contents($error_url);
      $replace = "##error##";
      $errorcode = str_replace($replace, $error, $errorcode);
      echo $errorcode;
      exit;
   }

   $internalfields = array ("submit", "reset", "send", "filesize", "formid", "captcha_code", "recaptcha_challenge_field", "recaptcha_response_field", "g-recaptcha-response");
   $message .= $eol;
   $message .= "IP Address : ";
   $message .= $_SERVER['REMOTE_ADDR'];
   $message .= $eol;
   foreach ($_POST as $key => $value)
   {
      if (!in_array(strtolower($key), $internalfields))
      {
         if (!is_array($value))
         {
            $message .= ucwords(str_replace("_", " ", $key)) . " : " . $value . $eol;
         }
         else
         {
            $message .= ucwords(str_replace("_", " ", $key)) . " : " . implode(",", $value) . $eol;
         }
      }
   }
   $body  = 'This is a multi-part message in MIME format.'.$eol.$eol;
   $body .= '--'.$boundary.$eol;
   $body .= 'Content-Type: text/plain; charset=ISO-8859-1'.$eol;
   $body .= 'Content-Transfer-Encoding: 8bit'.$eol;
   $body .= $eol.stripslashes($message).$eol;
   if (!empty($_FILES))
   {
       foreach ($_FILES as $key => $value)
       {
          if ($_FILES[$key]['error'] == 0 && $_FILES[$key]['size'] <= $max_filesize)
          {
             $body .= '--'.$boundary.$eol;
             $body .= 'Content-Type: '.$_FILES[$key]['type'].'; name='.$_FILES[$key]['name'].$eol;
             $body .= 'Content-Transfer-Encoding: base64'.$eol;
             $body .= 'Content-Disposition: attachment; filename='.$_FILES[$key]['name'].$eol;
             $body .= $eol.chunk_split(base64_encode(file_get_contents($_FILES[$key]['tmp_name']))).$eol;
          }
      }
   }
   $body .= '--'.$boundary.'--'.$eol;
   if ($mailto != '')
   {
      mail($mailto, $subject, $body, $header);
   }
   $search = array("ä", "Ä", "ö", "Ö", "ü", "Ü", "ß", "!", "§", "$", "%", "&", "/", "\x00", "^", "°", "\x1a", "-", "\"", " ", "\\", "\0", "\x0B", "\t", "\n", "\r", "(", ")", "=", "?", "`", "*", "'", ":", ";", ">", "<", "{", "}", "[", "]", "~", "²", "³", "~", "µ", "@", "|", "<", "+", "#", ".", "´", "+", ",");
   $replace = array("ae", "Ae", "oe", "Oe", "ue", "Ue", "ss");
   foreach($_POST as $name=>$value)
   {
      $name = str_replace($search, $replace, $name);
      $name = strtoupper($name);
      $form_data[$name] = $value;
   }
   $db = mysqli_connect($mysql_server, $mysql_username, $mysql_password) or die('Failed to connect to database server!<br>'.mysqli_error($db));
   mysqli_query($db, "CREATE DATABASE IF NOT EXISTS $mysql_database");
   mysqli_select_db($db, $mysql_database) or die('Failed to select database<br>'.mysqli_error($db));
   mysqli_query($db, "CREATE TABLE IF NOT EXISTS $mysql_table (ID int(9) NOT NULL auto_increment, `DATESTAMP` DATE, `TIME` VARCHAR(8), `IP` VARCHAR(15), `BROWSER` TINYTEXT, PRIMARY KEY (id))");
   foreach($form_data as $name=>$value)
   {
      mysqli_query($db ,"ALTER TABLE $mysql_table ADD $name VARCHAR(255)");
   }
   mysqli_query($db, "INSERT INTO $mysql_table (`DATESTAMP`, `TIME`, `IP`, `BROWSER`)
                VALUES ('".date("Y-m-d")."',
                '".date("G:i:s")."',
                '".$_SERVER['REMOTE_ADDR']."',
                '".$_SERVER['HTTP_USER_AGENT']."')")or die('Failed to insert data into table!<br>'.mysqli_error($db)); 
   $id = mysqli_insert_id($db);
   foreach($form_data as $name=>$value)
   {
      mysqli_query($db, "UPDATE $mysql_table SET $name='".mysqli_real_escape_string($db, $value)."' WHERE ID=$id") or die('Failed to update table!<br>'.mysqli_error($db));
   }
   mysqli_close($db);
   header('Location: '.$success_url);
   exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>System Startup</title>
<meta name="generator" content="WYSIWYG Web Builder 10 - http://www.wysiwygwebbuilder.com">
<link href="wb.validation.css" rel="stylesheet">
<link href="Untitled1.css" rel="stylesheet">
<link href="Administrator.css" rel="stylesheet">
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
   $("#Editbox5").validate(
   {
      required: true,
      type: 'number',
      expr_min: '',
      expr_max: '',
      value_min: '',
      value_max: '',
      length_max: '11',
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
      effect: 'none',
      error_text: 'Please Input the Mobile Number'
   });
   $("#Editbox6").validate(
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
   $("#Editbox7").validate(
   {
      required: true,
      type: 'custom',
      param: sadasdasd,
      length_min: '5',
      length_max: '100',
      color_text: '#000000',
      color_hint: '#00FF00',
      color_error: '#FF0000',
      color_border: '#808080',
      nohint: false,
      font_family: 'Arial',
      font_size: '13px',
      position: 'topleft',
      offsetx: 0,
      offsety: 0,
      effect: 'none',
      error_text: ''
   });
   $("#Combobox1").validate(
   {
      required: false,
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
   $("#Editbox8").validate(
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
});
</script>
</head>
<body>
<div id="container">
<div id="wb_Form1" style="position:absolute;left:276px;top:187px;width:356px;height:795px;z-index:19;">
<form name="Form1" method="post" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" id="Form1">
<input type="hidden" name="formid" value="form1">
<div id="wb_Text1" style="position:absolute;left:69px;top:15px;width:222px;height:68px;text-align:center;z-index:0;">
<span style="color:#000000;font-family:Impact;font-size:27px;">Create Administrator</span></div>
<div id="wb_Text2" style="position:absolute;left:16px;top:109px;width:118px;height:17px;text-align:center;z-index:1;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Firstname</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text7" style="position:absolute;left:16px;top:222px;width:118px;height:17px;text-align:center;z-index:2;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Lastname</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text6" style="position:absolute;left:16px;top:170px;width:118px;height:17px;text-align:center;z-index:3;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Middlename</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text5" style="position:absolute;left:16px;top:283px;width:118px;height:17px;text-align:center;z-index:4;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Gender</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text4" style="position:absolute;left:16px;top:335px;width:118px;height:17px;text-align:center;z-index:5;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Contact No</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<div id="wb_Text3" style="position:absolute;left:16px;top:390px;width:118px;height:17px;text-align:center;z-index:6;">
<span style="color:#000000;font-family:Impact;font-size:13px;">Username</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="submit" id="Button1" name="Submit" value="Submit" style="position:absolute;left:134px;top:538px;width:96px;height:25px;z-index:7;">
<input type="text" id="Editbox1" style="position:absolute;left:161px;top:109px;width:157px;height:18px;line-height:18px;z-index:8;" name="Fn" value="" placeholder="-Please Fillup Firstname-">
<input type="text" id="Editbox2" style="position:absolute;left:161px;top:218px;width:157px;height:19px;line-height:19px;z-index:9;" name="Ln" value="" placeholder="-Please Fillup Lastname-">
<input type="text" id="Editbox3" style="position:absolute;left:161px;top:166px;width:157px;height:19px;line-height:19px;z-index:10;" name="MN" value="" placeholder="-Please Fillup Middlename-">
<input type="text" id="Editbox5" style="position:absolute;left:161px;top:335px;width:157px;height:19px;line-height:19px;z-index:11;" name="Cn" value="" placeholder="-Please Fillup Contact No-">
<input type="text" id="Editbox6" style="position:absolute;left:161px;top:386px;width:157px;height:19px;line-height:19px;z-index:12;" name="User" value="" placeholder="-Please Fillup Username-">
<div id="wb_Text8" style="position:absolute;left:16px;top:496px;width:118px;height:17px;text-align:center;z-index:13;">
<span style="color:#000000;font-family:Impact;font-size:13px;">re-type password</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
<input type="password" id="Editbox7" style="position:absolute;left:161px;top:492px;width:157px;height:19px;line-height:19px;z-index:14;" name="RPd" value="" placeholder="-Please Fillup Password-">
<div id="wb_Text9" style="position:absolute;left:24px;top:572px;width:137px;height:16px;z-index:15;text-align:left;">
<span style="color:#FF0000;font-family:Arial;font-size:13px;">* = Required Field</span></div>
<select name="Gender" size="1" id="Combobox1" style="position:absolute;left:161px;top:283px;width:159px;height:23px;z-index:16;">
<option selected>~Please Fillup Gender~</option>
<option>Male</option>
<option>Female</option>
</select>
<div id="wb_Text10" style="position:absolute;left:24px;top:605px;width:315px;height:180px;text-align:center;z-index:17;">
<span style="color:#FF0000;font-family:'Bookman Old Style';font-size:13px;">Note; Your Administrator must conform to the following Company is real and legal policy:<br><br>&nbsp;&nbsp;&nbsp; it should be different from the Firstname<br>&nbsp;&nbsp;&nbsp; it should be different from Middlename<br>&nbsp;&nbsp;&nbsp; it should be different from Lastname<br>&nbsp;&nbsp;&nbsp; it should be different from Gender<br>&nbsp;&nbsp;&nbsp; it should be different from Contact No<br>&nbsp;&nbsp;&nbsp; it should be different from Username<br>it should be different from Password.</span></div>
<input type="text" id="Editbox8" style="position:absolute;left:161px;top:443px;width:157px;height:19px;line-height:19px;z-index:18;" name="User" value="" placeholder="-Please Fillup Username-">
</form>
</div>
<img src="images/img0002.jpg" id="Banner1" alt="" style="border-width:0;position:absolute;left:0px;top:22px;width:985px;height:148px;z-index:20;">
<div id="wb_Text11" style="position:absolute;left:292px;top:634px;width:118px;height:17px;text-align:center;z-index:21;">
<span style="color:#000000;font-family:Impact;font-size:13px;">password</span><span style="color:#FF0000;font-family:Impact;font-size:13px;">*</span></div>
</div>
</body>
</html>