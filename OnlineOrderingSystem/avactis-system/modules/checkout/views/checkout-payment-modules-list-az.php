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
 * Checkout Payment Modules List view.
 *getPaymentModuleGroup
 * @package Checkout
 * @author Vadim Lyalikov
 */

class CheckoutPaymentModulesList
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  CheckoutPaymentModulesList constructor.
     */
    function CheckoutPaymentModulesList()
    {
        global $application;

        $this->ModulesList = array();
        $this->SelectedPaymentModules = array();

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutPaymentModulesList"))
        {
            $this->NoView = true;
        }
    }

    /**
     * Outputs a list of all payment modules existing in the system, i.e. modules
     * loaded to AVACTIS with 'groups' and that have the PaymentModule group.
     */
    function getInstalledModulesListData($groups = NULL)
    {
        global $application;
        if($groups == null)
        {
            $groups = array();
        }
        $groups[] = "PaymentModule";
        $pm_list = modApiFunc("Modules_Manager", "getActiveModules", $groups);
        return $pm_list;
    }

    /**
     * function implies a functional cache for
     * Checkout::getSelectedModules("payment");
     *
     * @return array()
     */
    function getSelectedPaymentModules()
    {
        if (!empty($this->SelectedPaymentModules))
            return $this->SelectedPaymentModules;

        return ($this->SelectedPaymentModules = modApiFunc("Checkout", "getSelectedModules", "payment"));
    }

    /**
     * Prepares data for the View "Modules List".
     */
    function getModulesList($groups = null)
    {
        global $application;

        $SelectedModules = $this->getSelectedPaymentModules();

        $modules = $this->getPaymentModulesListPrepared();

        $items = array();
        $new_selected_module_sort_order = 0;
        foreach ($modules as $module)
        {
            $uid = $module['uid'];
            if(!array_key_exists($uid, $SelectedModules))
                continue;

            $name = $module['module_class_name'];
            $pmInfo = modApiFunc($name, "getInfo");
            $this->_Current_Module = $pmInfo;


            $request = new Request();
            $request->setView( 'CheckoutPaymentModuleSettings' );
            $request->setAction( "SetCurrentPaymentModuleSettingsViewName" );
            $request->setKey   ( "pm_viewclassname", $this->_Current_Module["PreferencesAZViewClassName"]);
            $request->setKey   ( "pm_uid", $this->_Current_Module["GlobalUniquePaymentModuleID"]);
            $pmInfo['InfoLink'] = $request->getURL();

            //product without small image or small image file is corrupted or absent.
            $pmInfouuid = $pmInfo["GlobalUniquePaymentModuleID"];
            $moduleclassname = modApiFunc("Checkout","getModululeNameByUUID","payment",$pmInfouuid);

            $shortname = modApiFunc("Resources","getShortnameByPaymentModuleName",$moduleclassname);

			$paymentsettings = modApiFunc("$moduleclassname", "getSettings");

    		$res_labelnumber = "MODE_00".$paymentsettings['MODULE_MODE'];
    		$res_labeltext = "MODE_TEST";
    		$restext_dibs = "TRNS_MODE_TEST";

    		$res_labels_array = array($res_labelnumber,$res_labeltext,$restext_dibs);

    		if (!array_key_exists("MODULE_MODE",$paymentsettings))
    		{
    			$this->_Current_Module['PaymentModuleStatusMessage'] = $pmInfo["StatusMessage"];
    		}


    		foreach($res_labels_array as $row)
    		{

    			$modulemodearray = modApiFunc('Resources','getPaymentModeName',$shortname[0]['shortname'],$row);

    			if(!empty($modulemodearray))
    			{
    				$modulemodename = $modulemodearray[0]['res_text'];

					$modulestatusarr = explode("<",($this->_Current_Module['StatusMessage']));

					$modulestatus = $modulestatusarr[0];

					if ((stripos($modulemodename,'test') !== false) && $modulestatus==""  )
					{

						$this->_Current_Module['PaymentModuleStatusMessage'] = "<span class='required'>Test Mode</span>";
					}

				    else
				    {

				     $this->_Current_Module['PaymentModuleStatusMessage'] = $pmInfo["StatusMessage"];

				     }

             	}

    		}


            $this->_Current_Module['PaymentModuleName'] = prepareHTMLDisplay($pmInfo["Name"]);
            $this->_Current_Module['PaymentModuleID'] = $pmInfo["GlobalUniquePaymentModuleID"];

            $module_info = modApiFunc('Modules_Manager','getModuleInfoByName',$pmInfo["APIClassName"]);

            $MessageResources = &$application->getInstance('MessageResources', $module_info->resFile, 'AdminZone');
            $module_descr = "MODULE_DESCR";

            $description = $MessageResources->getMessage($module_descr);
            $this->_Current_Module['PaymentModuleDescription'] = $description;


            $this->_Current_Module['InfoLink'] =  $pmInfo['InfoLink'];

            $application->registerAttributes($this->_Current_Module);

            //If sort id is not defined then assign the highest possible sort id to this item: 0, -1, -2 ...
            $sort_id = empty($SelectedModules[$pmInfo["GlobalUniquePaymentModuleID"]]["sort_order"]) ? $new_selected_module_sort_order-- : $SelectedModules[$pmInfo["GlobalUniquePaymentModuleID"]]["sort_order"];

            $items[$sort_id] = modApiFunc('TmplFiller', 'fill', "checkout/payment_modules_list/", "list_item.tpl.html", array());
        }
        //Sort items by sort id and implode them.
        ksort($items, SORT_NUMERIC);
        $value = implode("", $items);
        return $value;
    }

    function getPaymentModulesListPrepared()
    {
        global $application;

        if (!empty($this->ModulesList))
            return $this->ModulesList;

	$modules = $this->getInstalledModulesListData();
        $PaymentModulesGroupsInfo = modApiFunc("Checkout", "getPaymentModulesGroupsInfo");

        foreach($modules as $module)
        {
            $name = _ml_strtolower($module->name);

            // include $table; $uid;
            include($application->getAppIni("PATH_ASC_ROOT").($module->directory)."/includes/uid.php");
		if (isset($table)){
            $query = new DB_Select();
            $query->addSelectTable($table);
            $fields = $application->db->getDB_Result($query);

            $module_label = '';
            foreach ($fields as $row)
            {
                $i = 0;
                $list = array();
                foreach ($row as $field)
                {
                    $list[$i] = $field;
                    $i++;
                }
                if ($list[1] == "MODULE_NAME")
                    $module_label = $list[2];
            }
            $unserialized_label = @unserialize($module_label);
            if ($unserialized_label === FALSE)
                $unserialized_label = $module_label;
}
            $payment_group = modApiFunc("Checkout", "getPaymentModuleGroup", $module);

            if($payment_group != "")
            {
                $this->ModulesList[] = array(
                     'uid'               => $uid
                    ,'module_class_name' => $module->name
                    ,'module_label_name' => $unserialized_label
                    ,'payment_group'     => $payment_group
                    ,'group_short_name'  => $PaymentModulesGroupsInfo[$payment_group]['short_name']
                );
            }
        }

        return $this->ModulesList;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("checkout/payment_modules_list/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    /**
     * Otputs the view.
     *
     * @ $request->setView  ( '' ) - define the view name
     */
    function output()
    {
        global $application;

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "CheckoutPaymentModulesList", "Errors");
            return "";
        }

        $application->registerAttributes(array('Items'                                      => "",
                                               'PaymentModuleGroupList'                     => "",

                                               'PaymentModuleGroupList_Merged'             => "",
                                               'HiddenAvailable_Merged'                    => "",
                                               'List_SelectedModules'                       => "",
                                               'HiddenSelectedModules'                      => "",

                                               'SaveSelectedPaymentModulesListHref'         => "",
                                               'HiddenFieldAction'                          => "",
                                               'HiddenArrayViewState'                       => "",
                                               'getPaymentModuleInfoItemsJS'                => "",
                                               'getPaymentModuleGroupsItemsJS'              => "",
                                               'PaymentModulesLink'                         => "",
                                               'ResultMessageRow'                           => "",
					       'PaymentModuleCheckGroupList'		    => "",
                                               'PaymentModuleCheckGroupList_Merged'         => ""));

/*        "AvailableSelect_Offline",
        "AvailableSelect_OnlineCC",
        "AvailableSelect_OnlineECheck",
        "AvailableSelect_OnlinePaymentSystem" */

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        $this->MessageResources = &$application->getInstance('MessageResources');
        //: correct error codes
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "STRING1024"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_001')) ),
                                    "STRING128"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_002')) ),
                                    "STRING256"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_003')) ),
                                    "STRING512"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_004')) )
                                   )
                            );

        $result = $output.$this->mTmplFiller->fill("checkout/payment_modules_list/", "list.tpl.html",array());
        return $result;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;

        switch ($tag)
        {
    		case 'Items':
    			$value = $this->getModulesList();
    			break;

            case 'List_SelectedModules':
            {
                $value = "";
                $SelectedModules = $this->getSelectedPaymentModules();
                $new_selected_module_sort_order = 0;
                $modules = $this->getPaymentModulesListPrepared();

                foreach($modules as $module)
                {
                    $label = $module['module_label_name'];
                    $payment_group = $module['payment_group'];
                    $uid = $module['uid'];
                    $group_short_name = $module['group_short_name'];

                    if(array_key_exists($uid, $SelectedModules))
                    {
                        //If sort id is not defined then assign the highest possible sort id to this item: 0, -1, -2 ...
                        $sort_id =
                            empty($SelectedModules[$uid]["sort_order"])
                            ?
                            $new_selected_module_sort_order--
                            :
                            $SelectedModules[$uid]["sort_order"];

                        $items[$sort_id] =
                            "<option value=\"" . $uid . "\" id=\"" . $uid . "\">"
                            . "[" . $group_short_name . "] " .  prepareHTMLDisplay($label)
                            . "</option>";
                    }
                }

                //Sort items by sort id and implode them.
                if (!empty($items))
                {
                    ksort($items, SORT_NUMERIC);
                    $value = implode("", $items);
                }
                break;
            }
            case 'HiddenSelectedModules':
                //Hidden field to store selected ("Selected Payment Modules") select state
                $value = "";

                $SelectedModules = $this->getSelectedPaymentModules();
                $new_selected_module_sort_order = 0;

                $modules = $this->getPaymentModulesListPrepared();
                foreach($modules as $module)
                {
                    $payment_group = $module['payment_group'];
                    $uid = $module['uid'];

                    if(array_key_exists($uid, $SelectedModules))
                    {
                        //If sort id is not defined then assign the highest possible sort id to this item: 0, -1, -2 ...
                        $sort_id =
                            empty($SelectedModules[$uid]["sort_order"])
                            ?
                            $new_selected_module_sort_order--
                            :
                            $SelectedModules[$uid]["sort_order"];

                        $value .= (($value == "") ? $uid : "," . $uid);
                    }
                }
                break;

            case 'SaveSelectedPaymentModulesListHref':
                $value = $application->getPagenameByViewname("StoreSettingsPage",-1,-1,'AdminZone');
                break;

            case 'HiddenFieldAction':
                loadCoreFile('html_form.php');
                $HtmlForm = new HtmlForm();
                $value = $HtmlForm->genHiddenField('asc_action', 'SaveSelectedPaymentModulesList');
                break;
            case 'HiddenArrayViewState':
                break;

            case 'getPaymentModuleInfoItemsJS':
                $value = "";
                $modules = $this->getPaymentModulesListPrepared();
                foreach ($modules as $module)
                {
                        $value .= "case '"
                            . $module['uid']
                            . "': switch(key){case 'PaymentModulesGroupID': value = '"
                            . $module['payment_group']
                            . "'; break; case 'ShortName': value ='"
                            . addslashes($module['module_label_name'])
                            . "'; break; case 'FullName': value ='["
                            . $module['group_short_name'] . "] "
                            . addslashes($module['module_label_name']) . "'; break;}; break;\n";
                        //e.g. PaymentModulesGroupID = OnlineCC
                        //     ShortName             = Paypal
                        //     FullName              = [Online CC] Paypal
                        //Notice whitespace in "[Online CC]"
                }
                break;

            case 'getPaymentModuleGroupsItemsJS':
                    $value = "";
                    $PaymentModulesGroupsInfo = modApiFunc("Checkout", "getPaymentModulesGroupsInfo");
                    $bFirstItem = true;
                    foreach($PaymentModulesGroupsInfo as $PaymentModulesGroupInfo)
                    {
                        if($bFirstItem)
                        {
                            $value .= "'" . $PaymentModulesGroupInfo['group_id'] . "'";
                            $bFirstItem = false;
                        }
                        else
                        {
                            $value .= ", '" . $PaymentModulesGroupInfo['group_id'] . "'";
                        }
                    }
                break;

            case 'PaymentModulesLink':
                $request = new Request();
                $request->setView(CURRENT_REQUEST_URL);
                $value = $request->getURL();
                break;
            case 'ResultMessageRow':
            	$value = $this->outputResultMessage();
            	break;
            case 'ResultMessage':
                $value = $this->_Template_Contents['ResultMessage'];
                break;

            default:
                $value = "";
                $pos = _ml_strpos($tag, "_");
                if($pos != FALSE)
                {
                    $prefix = _ml_substr($tag, 0, $pos);
                    switch($prefix)
                    {
			case "PaymentModuleCheckGroupList":
				 $groups = array(0 => 'Offline', 1 => 'OnlineCC', 2 => 'OnlineECheck');
                            	 $titles = array(0 => 'PAYM_METH_HEADER_005', 1 => 'PAYM_METH_HEADER_006', 2 => 'PAYM_METH_HEADER_007');

				 $SelectedModules = $this->getSelectedPaymentModules();
	                         $modules = $this->getPaymentModulesListPrepared();

				for ($cgr = 0; $cgr < 3; $cgr++)
                            	{
				    $value .= "<li style='list-style-type:none;' id='optgroup_" .$groups[$cgr]. "_id'><b>".getMsg('SYS', $titles[$cgr])."</b></li>";
				    reset($modules);

				    foreach($modules as $module)
	                            {

	                                if ($module['payment_group'] != $groups[$cgr])
	                                    continue;

	                                $name = $module['module_label_name'];
	                                $uid = $module['uid'];
					$value .="<ul id='isSelect' class='list-inline'><li style='list-style-type: none;'>";
					if(array_key_exists($uid, $SelectedModules))
					{
	                                  $value .="<label id='module-name'><input type='checkbox' class='checkbox-inline' name='sel_payment' value='".$uid."' id='chk_".$uid."' style='margin: 0px 0px 2px;' checked>".prepareHTMLDisplay($name). "</label></li></ul>";
					}
					else{
					$value .="<label id='module-name'><input type='checkbox' class='checkbox-inline' name='sel_payment' value='".$uid."' id='chk_".$uid."' style='margin: 0px 0px 2px;'>".prepareHTMLDisplay($name). "</label></li></ul>";
					}
	                            }
                            	}
                            	break;

                        case "PaymentModuleGroupList":
                            $groups = array(0 => 'Offline', 1 => 'OnlineCC', 2 => 'OnlineECheck');
                            $titles = array(0 => 'PAYM_METH_HEADER_005', 1 => 'PAYM_METH_HEADER_006', 2 => 'PAYM_METH_HEADER_007');
                            //Options for "Offline" "Online CC" "Online eCheck" and "Online Payment" <select> control.

                            $SelectedModules = $this->getSelectedPaymentModules();
	                        $modules = $this->getPaymentModulesListPrepared();

                            for ($cgr = 0; $cgr < 3; $cgr++)
                            {
	                            $value .= "<optgroup label='" . getMsg('SYS', $titles[$cgr]) . "' id='optgroup_" . $groups[$cgr] . "_id'>";
                                reset($modules);
	                            foreach($modules as $module)
	                            {
	                                if ($module['payment_group'] != $groups[$cgr])
	                                    continue;

	                                $name = $module['module_label_name'];
	                                $uid = $module['uid'];

	                                if(!array_key_exists($uid, $SelectedModules))
	                                {
	                                    $value .= "<option value=\"" . $uid . "\">" . prepareHTMLDisplay($name) . "</option>";
	                                }
	                            }
	                            $value .= '</optgroup>';
                            }
                            break;

                        case "HiddenAvailable":
                            break;
                        default:
                            ////// can it be Current Module (Modules List Item) details?
                            ////_fatal(__CLASS__ .'::'. __FUNCTION__. ': prefix = ' . $prefix);
                            break;
                    }
                }
                else
                {
                    //Current Module (Modules List Item) details
                    $value = getKeyIgnoreCase($tag, $this->_Current_Module);
                }
                break;
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

    /**
     * A reference to the object TemplateFiller.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current selected template.
     *
     * @var array
     */
    var $template;

    /**
     * Current selected module info. It is used for the internal processing.
     *
     * @var array
     */
    var $_Current_Module;
    var $ModulesList;
    var $SelectedPaymentModules;

    /**#@-*/
}
?>