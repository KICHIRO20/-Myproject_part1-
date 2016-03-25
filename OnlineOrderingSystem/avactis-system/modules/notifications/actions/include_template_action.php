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

class IncludeTemplate extends AjaxAction
{
  function IncludeTemplate()
  {
  }

  function onAction()
  {
      global $application;
      $request = $application->getInstance('Request');

      $data = array();
      $data['ThemeName'] = $request->getValueByKey('temp');
      $data['NotificationId'] = $request->getValueByKey('NotificationId');

      $notification_name=str_replace(" ","",modApiFunc("Notifications", "__getNotificationNameById", $data['NotificationId']));
      $notification_name=str_replace("/","",$notification_name);
      $notification_name=str_replace(":","",$notification_name);
      $notification_file=dirname(dirname(__FILE__)).'/includes/templates/'.$data['ThemeName'].'/'.$notification_name.'.txt';

      global $_RESULT;
      print(file_get_contents($notification_file));
	exit();
  }

}
?>