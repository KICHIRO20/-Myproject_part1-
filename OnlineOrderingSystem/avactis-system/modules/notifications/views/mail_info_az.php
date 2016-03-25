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

/**
 * Notifications Module, SecurityNotifications View.
 *
 * @package Notifications
 * @author Alexander Girin
 */
class MailInfo
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * MailInfo constructor
     */
    function MailInfo()
    {
        global $application;
        $MR = &$application->getInstance('MessageResources');

        //initialize form data with null values when adding a new notification
        $this->currentNotificationId = modApiFunc("Notifications", "getCurrentNotificationId");
        if ($this->currentNotificationId == 'Add')
        {
            $this->notificationInfo = array(
                                             'Id' => ''
                                            ,'Name' => ''
                                            ,'Subject' => ''
                                            ,'Body' => ''
                                            ,'JavascriptBody' => ''
                                            ,'From_addr' => ''
                                            ,'Email_Code' => ''
                                            ,'Active' => 'checked'
                                            ,'Action_id' => 1
                                           );
            $request = new Request();
            $request->setView('NotificationInfo');
            $request->setAction('AddNotification');
            $formAction = $request->getURL();

            $this->properties = array(
                                      'SubmitButton' => $MR->getMessage('BTN_ADD')
                                     ,'FormAction' => $formAction
                                     );
        }
        else
        {
            //initialize form data with database values when editing the notification
            $this->notificationInfo = modApiFunc("Notifications", "getNotificationInfo", $this->currentNotificationId);

            if (sizeof($this->notificationInfo) == 1)
            {
                $this->notificationInfo = $this->notificationInfo[0];
                $this->notificationInfo['JavascriptBody'] = addcslashes(addslashes($this->notificationInfo['Body']), "\0..\31");
                $this->notificationInfo['Body'] = prepareHTMLDisplay($this->notificationInfo['Body']);
            }
            else
            {
                $this->currentNotificationId = 'Add';
                $this->notificationInfo = array(
                                                 'Id' => ''
                                                ,'Name' => ''
                                                ,'Subject' => ''
                                                ,'Body' => ''
                                                ,'JavascriptBody' => ''
                                                ,'From_addr' => ''
                                                ,'Email_Code' => ''
                                                ,'Active' => 'checked'
                                                ,'Action_id' => 1
                                               );
            }
            $request = new Request();
            $request->setView('NotificationInfo');
            $request->setAction('SaveNotification');
            $formAction = $request->getURL();

            $this->properties = array(
                                      'SubmitButton' => $MR->getMessage('BTN_SAVE')
                                     ,'FormAction' => $formAction
                                     );
        }
        $this->actionsList = modApiFunc("Notifications", "getActionsList");
        $this->InfoTags = modApiFunc("Notifications", "getAvailableTagsList", $this->actionsList);
    }

    /**
     * Prepares data to output a list of specified actions.
     *
     * @return string - HTML code
     */
    function outputActionsList()
    {
        $retval = "";
        foreach ($this->actionsList as $action)
        {
            $retval.= "<option value='".$action["Id"]."' ".($action["Id"] == $this->notificationInfo["Action_id"]? "selected":"").">".$action["Name"]."</option>";
        }
        return $retval;
    }

    /**
     * Prepares data to output a list of mailing list addresses.
     *
     * @return string - HTML code
     */
    function outputSendToList()
    {
        $this->usedSendToEmails = array();
        $this->hiddenSendToEmails = "";
        $retval = "";
        $list = modApiFunc("Notifications", "getSendToList", $this->notificationInfo["Id"]);
        foreach ($list as $address)
        {
            $uid = modApiFunc("Notifications", "getEmail", $address["Email"], $address["Code"]);
            $str = modApiFunc("Notifications", "getExtendedEmail", $address["Email"], $address["Code"], false, $uid);
            if ($str != '')
            {
                $value = $address["Code"] . "=" . $uid;
                $retval.= "<option value='".$value."' >".$str."</option>";
                $this->usedSendToEmails[] = $value;
            }
            else
            {
                modApiFunc("Notifications", "deleteDeadAdminFromNotifications", $uid);
            }
        }
        $this->hiddenSendToEmails = implode("|", $this->usedSendToEmails);
        return $retval;
    }

    /**
     * Prepares data to output of possible mailing list.
     *
     * @return string - HTML code
     */
    function outputSendSourceList($dirrection)
    {
        $retval = "";
        $list = modApiFunc("Notifications", "getSendSourceList", $dirrection);
        $email_code_strlen = _ml_strlen($this->notificationInfo['Email_Code']);
        if($this->notificationInfo['Email_Code'] == "EMAIL_ADMINISTRATOR")
        {
        	$selected_key = "EMAIL_ADMINISTRATOR=" . $this->notificationInfo['Admin_ID'];
        }

        foreach ($list as $key => $address)
        {
            if ($dirrection == 'to')
            {
                if (!in_array($key, $this->usedSendToEmails))
                {
                    $retval.= "<option value='".$key."' >".$address."</option>";
                }
            }
            else
            {
                if($this->currentNotificationId == 'Add')
                {
                    $retval.= "<option value='".$key."' >".$address."</option>";
                }
                else
                {
                    //"Edit" notification.
                    if($this->notificationInfo['Email_Code'] == "EMAIL_ADMINISTRATOR")
                    {
                        if($key == $selected_key)
                        {
                            $retval.= "<option selected value='".$key."' >".$address."</option>";
                        }
                        else
                        {
                            $retval.= "<option value='".$key."' >".$address."</option>";
                        }
                    }
                    else if($this->notificationInfo['Email_Code'] != "EMAIL_CUSTOM")
                    {
                        if(_ml_substr($key, 0, $email_code_strlen) == $this->notificationInfo['Email_Code'])
                        {
                            $retval.= "<option selected value='".$key."' >".$address."</option>";
                        }
                        else
                        {
                            $retval.= "<option value='".$key."' >".$address."</option>";
                        }
                    }
                    else
                    {
                        $retval.= "<option value='".$key."' >".$address."</option>";
                    }
                }
            }
        }
        return $retval;
    }

    /**
     * Prepares data to ouptput a list of action option list.
     *
     * @return string - HTML code
     */
    function outputActionOptions()
    {
        global $application;
        $retval = "";
        $Hint = &$application->getInstance('Hint');
        foreach ($this->actionsList as $action)
        {
            $optionsList = modApiFunc("Notifications", "getActionOptionInfo", $action['Id']);
            if (sizeof($optionsList)>0)
            {
                $i = 0;
                foreach ($optionsList as $optionInfo)
                {
                    $template_contents = array(
                                                'ActionId' => $action['Id']
                                               ,'I' => $i
                                               ,'OptionName' => getMsg('NTFCTN',$optionInfo['Name'])
                                               ,'OptionDescr' => $Hint->getHintLink(array($optionInfo['Name']))
                                               ,'Display'  => $action['Id'] == $this->notificationInfo["Action_id"]? "":"display: none;"
                                               ,'Items'  => $this->outputActionOptionValues($this->notificationInfo["Id"], $action['Id'], $optionInfo['Id'], $optionInfo['InputType'])
                                              );
                    $this->_Template_Contents = $template_contents;
                    $application->registerAttributes($this->_Template_Contents);
                    $retval.= modApiFunc('TmplFiller', 'fill', "notifications/mail_info/","action_option.tpl.html", array());
                    $i++;
                }
            }
        }
        return $retval;
    }

    /**
     * Prepares data to output action option values.
     *
     * @return string - HTML code
     */
    function outputActionOptionValues($n_id, $a_id, $o_id, $input_type)
    {
        global $application;
        $retval = "";
        $optionValuesList = modApiFunc("Notifications", "getActionOptionValuesList", $n_id, $o_id);
        foreach ($optionValuesList as $value)
        {
            if ($this->currentNotificationId == 'Add')
            {
                $Checked = 'checked';
            }
            else
            {
                $OptionInfo = modApiFunc("Notifications", "getNotificationActionOptionValue", $n_id, $value['Id']);
                $Checked = $OptionInfo['value'] == 'true'? "checked":"";
            }
            $template_contents = array(
                                        'Id' => $value['Id']
                                       ,'Name' => $value['Name']
                                       ,'Value' => ''
                                       ,'InputType'  => $input_type
                                       ,'InputTypeName' => $input_type == 'checkbox'? 'notification_option_value_'.$a_id.'['.$value['Id'].']':'notification_option_value_'.$a_id
                                       ,'Checked'  => $Checked
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $retval.= modApiFunc('TmplFiller', 'fill', "notifications/mail_info/","option_value.tpl.html", array());
        }
        return $retval;
    }

    /**
     * Prepares data to output the available infotags for the action.
     *
     * @return string - HTML code
     */
    function outputAvailableInfoTags()
    {
        $retval = "";

        foreach ($this->InfoTags[$this->notificationInfo["Action_id"]]['InfoTags'] as $id => $name)
        {
            $retval.= "<option value='".$id."'>".$name."</option>";
        }
        return $retval;
    }

    /**
     * Prepares data to output the available blocktags for the action.
     *
     * @return string - HTML code
     */
    function outputAvailableBlockTags()
    {
        $retval = "";

        foreach ($this->InfoTags[$this->notificationInfo["Action_id"]]['BlockTags'] as $id => $BlockTagInfo)
        {
            $retval.= "<option value='".$id."'>".$BlockTagInfo['BlockTag']."</option>";
        }
        return $retval;
    }

    /**
     * Prepares data to output a blocktag body.
     *
     * @return string - HTML code
     */
    function outputBlockTemplates()
    {
        global $application;
        $retval = "";

        foreach ($this->InfoTags as $actionId => $tags)
        {
            $i = 0;
            foreach ($tags['BlockTags'] as $blocktagId => $blocktagInfo)
            {
                $InfoTags = "";
                foreach ($blocktagInfo['BlockInfoTags'] as $infotagId => $infotagName)
                {
                    $InfoTags.= "<option value='".$infotagId."'>".$infotagName."</option>";
                }
                $template_contents = array(
                                            'I' => $i
                                           ,'BlockTagName' => $blocktagInfo['BlockTag']
                                           ,'BlocktagId' => $blocktagId
                                           ,'InfoTags' => $InfoTags
                                           ,'Display' => $this->notificationInfo["Action_id"] == $actionId? "":"display: none;"
                                           ,'ActionId' => $actionId
                                           ,'Body' => prepareHTMLDisplay(modApiFunc("Notifications", "getNotificationBlockBody", $this->notificationInfo["Id"], $blocktagId))
                                          );
                $this->_Template_Contents = $template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "notifications/mail_info/","blocktag_template.tpl.html", array());
                $i++;
            }
        }
        return $retval;
    }

    /**
     * Prepares data to output a javascript array of available infotags and
     * blocktags.
     *
     * @return string - HTML code
     */
    function outputJavascriptTagArrays()
    {
        $infoTags = "var infoTags = new Array();\n";
        $blockTags = "var blockTags = new Array();\n";
        $infoTags_to_blockTags = "var infoTags_to_blockTags = new Array();\n";
        $used_blocks = array();
        foreach ($this->InfoTags as $actionId => $tags)
        {
            $infoTags.= "infoTags[".$actionId."] = new Array();\n";
            $i = 0;
            foreach ($tags['InfoTags'] as $infotagId => $infotagName)
            {
                $infoTags.= "infoTags[".$actionId."][".$i."] = new Array();\n";
                $infoTags.= "infoTags[".$actionId."][".$i."]['tagId'] = '".$infotagId."';\n";
                $infoTags.= "infoTags[".$actionId."][".$i."]['tagName'] = '".$infotagName."';\n";
                $i++;
            }
            $blockTags.= "blockTags[".$actionId."] = new Array();\n";
            $i = 0;
            foreach ($tags['BlockTags'] as $blocktagId => $blocktagInfo)
            {
                $blockTags.= "blockTags[".$actionId."][".$i."] = new Array();\n";
                $blockTags.= "blockTags[".$actionId."][".$i."]['tagId'] = '".$blocktagId."';\n";
                $blockTags.= "blockTags[".$actionId."][".$i."]['tagName'] = '".$blocktagInfo['BlockTag']."';\n";
                if (!in_array($blocktagId, $used_blocks))
                {
                    $infoTags_to_blockTags.= "infoTags_to_blockTags[".$blocktagId."] = new Array();\n";
                    $j = 0;
                    foreach ($blocktagInfo['BlockInfoTags'] as $infotagId => $infotagName)
                    {
                        $infoTags_to_blockTags.= "infoTags_to_blockTags[".$blocktagId."][".$j."] = new Array();\n";
                        $infoTags_to_blockTags.= "infoTags_to_blockTags[".$blocktagId."][".$j."]['tagId'] = '".$infotagId."';\n";
                        $infoTags_to_blockTags.= "infoTags_to_blockTags[".$blocktagId."][".$j."]['tagName'] = '".$infotagName."';\n";
                        $j++;
                    }
                    $used_blocks[] = $blocktagId;
                }
                $i++;
            }
        }
        return $infoTags."\n".$blockTags."\n".$infoTags_to_blockTags;
    }

    function outputFromRadioValue($opt_name)
    {
        if ($this->currentNotificationId == 'Add')
        {
            switch($opt_name)
            {
                case "SendFrom_Select_Value":
                    {
                        //Not Used.
                        //@ remove.
                        $value = "";
                        break;
                    }
                case "SendFrom_InputText_Value":
                    {
                        $value = "";
                        break;
                    }
                default:
                    {
                        //@ output error message
                    }
            }
        }
        else
        {
            //"Edit" notification
            switch($opt_name)
            {
                case "SendFrom_Select_Value":
                    {
                        //Not used
                        //@ remove.
                        $value = "";
                        break;
                    }
                case "SendFrom_InputText_Value":
                    {
                        $value = $this->notificationInfo['From_addr'];
                        break;
                    }
                default:
                    {
                        //@ output error message
                    }
            }


        }
        return $value;
    }

    function outputFromRadioOption($opt_name)
    {
        if ($this->currentNotificationId == 'Add')
        {
            $select_is_selected = true;
        }
        else
        {
            $select_is_selected = ($this->notificationInfo['Email_Code'] != "EMAIL_CUSTOM");
        }

        $input_text_is_selected = !$select_is_selected;
        switch($opt_name)
        {
            case "SendFrom_Select_Checked":
                {
                    $value = $select_is_selected == true ? "checked" : "";
                    break;
                }
            case "SendFrom_InputText_Checked":
                {
                    $value = $input_text_is_selected == true ? "checked" : "";
                    break;
                }
            case "SendFrom_Select_Disabled":
                {
                    $value = $select_is_selected == true ? "" : "disabled style='background-color =\"rgb(215, 215, 215)\"; width: 330px;'";
                    break;
                }
            case "SendFrom_InputText_Disabled":
                {
                    $value = $input_text_is_selected == true ? "" : "disabled style='background-color =\"rgb(215, 215, 215)\"; width: 325px;'";
                    break;
                }
            default:
                {
                    //@ output error message
                }
        }
        return $value;
    }

    function ouputTemplates()
    {
      	global $application;
         $retval = "";
         $path=dirname(dirname(__FILE__)).'/includes/templates/';

         $notification_name=str_replace(" ","",modApiFunc("Notifications", "__getNotificationNameById", $this->currentNotificationId));
         $notification_name=str_replace("/","",$notification_name);
         $notification_name=str_replace(":","",$notification_name);
         $scandir=preg_grep('/^([^.])/',scandir($path));

         foreach($scandir as $dir){
          $notification_file=$path.$dir.'/'.$notification_name.'.txt';

         if(file_exists($notification_file)){
		$getTemplate=file_get_contents($notification_file);

         $template_contents = array(
                                      'getTemplate'=>$getTemplate,
                                      'FileExist'=>$notification_file,
                                      'RadioName'=>$dir
                                  );
                $this->_Template_Contents = $template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "notifications/mail_info/","template_options.tpl.html", array());
         }
       }
      return $retval;
     }

    /**
     * Outputs a view.
     */
    function output()
    {
        global $application;

        $request = $application->getInstance('Request');
        if($request->getValueByKey('hasCloseScript') == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $template_contents = array(
                                    'Actions_List' => $this->outputActionsList()
                                   ,'CurrentAction' => $this->notificationInfo["Action_id"]
                                   ,'SendTo_List'  => $this->outputSendToList()
                                   ,'HiddenSendToList'  => $this->hiddenSendToEmails
                                   ,'SendTo_SourceList'  => $this->outputSendSourceList('to')
                                   ,'SendFrom_SourceList'  => $this->outputSendSourceList('from')
                                   ,'SendFrom_Select_Checked' => $this->outputFromRadioOption('SendFrom_Select_Checked')
                                   ,'SendFrom_InputText_Checked' => $this->outputFromRadioOption('SendFrom_InputText_Checked')
                                   ,'SendFrom_Select_Disabled' => $this->outputFromRadioOption('SendFrom_Select_Disabled')
                                   ,'SendFrom_InputText_Disabled' => $this->outputFromRadioOption('SendFrom_InputText_Disabled')
                                   ,'SendFrom_InputText_Value' => $this->outputFromRadioValue('SendFrom_InputText_Value')
                                   ,'From' => modApiFunc("Notifications", "getExtendedEmail", $this->notificationInfo['From_addr'], $this->notificationInfo['Email_Code'], false, (!empty($this->notificationInfo['Admin_ID']) ? $this->notificationInfo['Admin_ID'] : NULL))
                                   ,'From_Email' => $this->notificationInfo['From_addr']
                                   ,'From_Email_Code' => $this->notificationInfo['Email_Code']
                                   ,'ActionOptions' => $this->outputActionOptions()
                                   ,'AvailableInfoTags' => $this->outputAvailableInfoTags()
                                   ,'AvailableBlockTags' => $this->outputAvailableBlockTags()
                                   ,'BlockTemplates' => $this->outputBlockTemplates()
                                   ,'JavascriptTagArrays' => $this->outputJavascriptTagArrays()
                                   ,'Active' => $this->notificationInfo['Active']
                                   ,'AscAction' => $this->currentNotificationId
                                   ,'ResponsiveTemplates'=>$this->ouputTemplates()
                                  );
        $template_contents = array_merge($template_contents, $this->notificationInfo);
        $this->_Template_Contents = array_merge($template_contents, $this->properties);
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "notifications/mail_info/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
        }
        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>