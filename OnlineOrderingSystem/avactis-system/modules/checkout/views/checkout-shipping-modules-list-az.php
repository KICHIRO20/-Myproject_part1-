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
 * Checkout Shipping Modules List view.
 *getShippingModuleGroup
 * @package Checkout
 * @author Girin Alexander
 */

class CheckoutShippingModulesList
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  CheckoutShippingModulesList constructor.
     */
    function CheckoutShippingModulesList()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutShippingModulesList"))
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
        $groups[] = "ShippingModule";
        $sm_list = modApiFunc("Modules_Manager", "getActiveModules", $groups);
        return $sm_list;
    }

    /**
     * Prepares data for the View "Modules List".
     */
    function getModulesList($groups = null)
    {
        global $application;

        $SelectedModules = modApiFunc("Checkout", "getSelectedModules", "shipping");

        $pm_list = $this->getInstalledModulesListData($groups);

        $items = array();
        $new_selected_module_sort_order = 0;
        foreach ($pm_list as $pm_item)
        {
            // create/use some mm function to convert class names.
            $name = _ml_strtolower($pm_item->name);

            $shipping_group = modApiFunc("Checkout", "getShippingModuleGroup", $pm_item);
            /* Don't show the "All Inactive" shipping module */
            if($shipping_group == "")
            {
                continue;
            }

            $pmInfo = modApiFunc($name, "getInfo");

            if(!array_key_exists($pmInfo['GlobalUniqueShippingModuleID'], $SelectedModules))
                continue;

            $this->_Current_Module = $pmInfo;

            $request = new Request();
            $request->setView( 'CheckoutShippingModuleSettings' );
            $request->setAction( "SetCurrentShippingModuleSettingsViewName" );
            $request->setKey   ( "pm_viewclassname", $this->_Current_Module["PreferencesAZViewClassName"]);
            $request->setKey   ( "sm_uid", $this->_Current_Module["GlobalUniqueShippingModuleID"]);
            $pmInfo['InfoLink'] = $request->getURL();

            //product without small image or small image file is corrupted or absent.
            $this->_Current_Module['ShippingModuleStatusMessage'] = $pmInfo["StatusMessage"];
            $this->_Current_Module['ShippingModuleName'] = prepareHTMLDisplay($pmInfo["Name"]);
            $this->_Current_Module['ShippingModuleID'] = $pmInfo["GlobalUniqueShippingModuleID"];

            $filename_messages = str_replace('_', '-', $name)."-messages";
            $MessageResources = &$application->getInstance('MessageResources',$filename_messages, "AdminZone");
            $description = $MessageResources->getMessage("MODULE_DESCR");
            $this->_Current_Module['ShippingModuleDescription'] = $description;

//            $this->_Current_Module['ShippingModuleDescription'] = $pmInfo["Description"];




            $this->_Current_Module['InfoLink'] =  $pmInfo['InfoLink'];

            $application->registerAttributes($this->_Current_Module);

            //If sort id is not defined then assign the highest possible sort id to this item: 0, -1, -2 ...
            $sort_id = empty($SelectedModules[$pmInfo["GlobalUniqueShippingModuleID"]]["sort_order"]) ? $new_selected_module_sort_order-- : $SelectedModules[$pmInfo["GlobalUniqueShippingModuleID"]]["sort_order"];

            $items[$sort_id] = modApiFunc('TmplFiller', 'fill', "checkout/shipping_modules_list/", "list_item.tpl.html", array());
        }

        //Sort items by sort_id and implode them.
        ksort($items, SORT_NUMERIC);
        $value = implode("", $items);
        return $value;
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
            return $this->mTmplFiller->fill("checkout/shipping_modules_list/", "result-message.tpl.html",array());
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
            $application->outputTagErrors(true, "CheckoutShipppingModulesList", "Errors");
            return "";
        }

        $application->registerAttributes(array('Items'                                      => "",
                                               'ShippingModuleGroupList'                     => "",

                                               'ShippingModuleGroupList_Offline'             => "",
                                               'HiddenAvailable_Offline'                    => "",
                                               'List_SelectedModules'                       => "",
                                               'HiddenSelectedModules'                      => "",

                                               'SaveSelectedShippingModulesListHref'         => "",
                                               'HiddenFieldAction'                          => "",
                                               'HiddenArrayViewState'                       => "",
                                               'getShippingModuleInfoItemsJS'                => "",
                                               'getShippingModuleGroupsItemsJS'              => "",
                                               'ShippingModulesLink'                         => "",
                                               'ResultMessageRow'                           => "",
					       'ShippingModuleCheckGroupList'		    =>"",
					       'ShippingModuleCheckGroupList_Offline'	    =>""));

/*        "AvailableSelect_Offline",
        "AvailableSelect_OnlineCC",
        "AvailableSelect_OnlineECheck",
        "AvailableSelect_OnlineShippingSystem" */

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

        return $output.$this->mTmplFiller->fill("checkout/shipping_modules_list/", "list.tpl.html",array());
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

            case 'ShippingModulesList':
                $value = "Shipping Modules List";
                break;
/*            case 'SubmitedCheckoutStoreBlocksListItemName':
                $value = "SubmitedCheckoutStoreBlocksList[shipping-method-list-input]";
                break;*/
            case 'List_SelectedModules':
            {
                $items = array();
                $SelectedModules = modApiFunc("Checkout", "getSelectedModules", "shipping");
                $new_selected_module_sort_order = 0;

                $modules = $this->getInstalledModulesListData();
                foreach($modules as $module)
                {
                    $name = _ml_strtolower($module->name);
                    $Shipping_group = modApiFunc("Checkout", "getShippingModuleGroup", $module);
                    if($Shipping_group != '')
                    {
                        $smInfo = modApiFunc($name, "getInfo");
                        //// INCORRECT method to get group name! Remove group name from id at all.
                        //$groups_array = explode(',', $module->groups);

                        if(array_key_exists($smInfo['GlobalUniqueShippingModuleID'], $SelectedModules))
                        {
                            $ShippingModulesGroupsInfo = modApiFunc("Checkout", "getShippingModulesGroupsInfo");
                            //If sort id is not defined then assign the highest possible sort id to this item: 0, -1, -2 ...
                            $sort_id = empty($SelectedModules[$smInfo["GlobalUniqueShippingModuleID"]]["sort_order"]) ? $new_selected_module_sort_order-- : $SelectedModules[$smInfo["GlobalUniqueShippingModuleID"]]["sort_order"];
                            $items[$sort_id] = "<option value=\"" . $smInfo['GlobalUniqueShippingModuleID'] . "\" id=\"" . $smInfo['GlobalUniqueShippingModuleID'] . "\">" . prepareHTMLDisplay($smInfo["Name"]) . "</option>";
                        }
                    }
                }

                //Sort items by sort id and implode them.
                ksort($items, SORT_NUMERIC);
                $value = implode("", $items);
                break;
            }
            case 'HiddenSelectedModules':
                //Hidden field to store selected ("Selected Shipping Modules") select state
                $value = "";

                $SelectedModules = modApiFunc("Checkout", "getSelectedModules", "shipping");
                $new_selected_module_sort_order = 0;

                $modules = $this->getInstalledModulesListData();
                foreach($modules as $module)
                {
                    $name = _ml_strtolower($module->name);
                    $Shipping_group = modApiFunc("Checkout", "getShippingModuleGroup", $module);
                    if($Shipping_group != '')
                    {
                        $smInfo = modApiFunc($name, "getInfo");
                        //// INCORRECT method to get group name! Remove group name from id at all.
                        //$groups_array = explode(',', $module->groups);

                        if(array_key_exists($smInfo['GlobalUniqueShippingModuleID'], $SelectedModules))
                        {
                            $ShippingModulesGroupsInfo = modApiFunc("Checkout", "getShippingModulesGroupsInfo");
                            //If sort id is not defined then assign the highest possible sort id to this item: 0, -1, -2 ...
                            $sort_id = empty($SelectedModules[$smInfo["GlobalUniqueShippingModuleID"]]["sort_order"]) ? $new_selected_module_sort_order-- : $SelectedModules[$smInfo["GlobalUniqueShippingModuleID"]]["sort_order"];

                            $value .= (($value == "") ? $smInfo['GlobalUniqueShippingModuleID'] :
                                                  "," . $smInfo['GlobalUniqueShippingModuleID']);
                        }
                    }
                }
                break;

            case 'SaveSelectedShippingModulesListHref':
////                $request = new Request();
////                $request->setView  ('CheckoutShippingModulesList');
                $value = $application->getPagenameByViewname("StoreSettingsPage",-1,-1,'AdminZone');
//////: should set action?
////                $request->setAction('SaveSelectedShippingModulesList');
////                $value = modApiFunc('application', 'href', $request);
                break;
            case 'HiddenFieldAction':
                loadCoreFile('html_form.php');
                $HtmlForm = new HtmlForm();
                $value = $HtmlForm->genHiddenField('asc_action', 'SaveSelectedShippingModulesList');
                break;
            case 'HiddenArrayViewState':
                break;

            case 'getShippingModuleInfoItemsJS':
                $value = "";

                $modules = $this->getInstalledModulesListData();
                foreach($modules as $module)
                {
                    $name = _ml_strtolower($module->name);
                    $pmInfo = modApiFunc($name, "getInfo");
                    $Shipping_group = modApiFunc("Checkout", "getShippingModuleGroup", $module);
                    if($Shipping_group != '')
                    {
                        $ShippingModulesGroupsInfo = modApiFunc("Checkout", "getShippingModulesGroupsInfo");
                        $value .= "case '" .$pmInfo['GlobalUniqueShippingModuleID']. "': switch(key){case 'ShippingModulesGroupID': value = '" . $Shipping_group . "'; break; case 'ShortName': value ='" . $pmInfo["Name"] . "'; break; case 'FullName': value ='" .$pmInfo["Name"]. "'; break;}; break;";
                        //e.g. ShippingModulesGroupID = OnlineCC
                        //     ShortName             = Paypal
                        //     FullName              = [Online CC]Paypal
                        //Notice whitespace in "[Online CC]"
                    }
                }
                break;

            case 'getShippingModuleGroupsItemsJS':
                    $value = "";
                    $ShippingModulesGroupsInfo = modApiFunc("Checkout", "getShippingModulesGroupsInfo");
                    $bFirstItem = true;
                    foreach($ShippingModulesGroupsInfo as $ShippingModulesGroupInfo)
                    {
                        if($bFirstItem)
                        {
                            $value .= "'" . $ShippingModulesGroupInfo['group_id'] . "'";
                            $bFirstItem = false;
                        }
                        else
                        {
                            $value .= ", '" . $ShippingModulesGroupInfo['group_id'] . "'";
                        }
                    }
                break;

            case 'ShippingModulesLink':
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
			case "ShippingModuleCheckGroupList":
                            //Options for "Offline" "Online CC" "Online eCheck" and "Online Shipping" <select> control.
                            $SelectedModules = modApiFunc("Checkout", "getSelectedModules", "shipping");

                            $group_name = _ml_substr($tag, _ml_strlen($prefix)+1);
                            $modules = $this->getInstalledModulesListData(array($group_name));
                            $free_shipping_sm_guid = modApiFunc('Shipping_Module_Free_Shipping', 'getUID');

                            foreach($modules as $module)
                            {
                                $name = _ml_strtolower($module->name);
                                $pmInfo = modApiFunc($name, "getInfo");

                                if ($pmInfo['GlobalUniqueShippingModuleID'] == $free_shipping_sm_guid)
                                    continue;

				    $value .="<ul id='isSelect' class='list-inline'><li style='list-style-type: none;'>";
				if(array_key_exists($pmInfo['GlobalUniqueShippingModuleID'], $SelectedModules))
				{
                                    $value .= "<label id='module-name'><input type='checkbox' class='checkbox-inline' name='sel_shipping' value='".$pmInfo['GlobalUniqueShippingModuleID']."' id='chk_".$pmInfo['GlobalUniqueShippingModuleID']."' style='margin: 0px 0px 2px;' checked>".prepareHTMLDisplay($pmInfo["Name"]). "</label></li></ul>";
				}
				else{
					$value .= "<label id='module-name'><input type='checkbox' class='checkbox-inline' name='sel_shipping' value='".$pmInfo['GlobalUniqueShippingModuleID']."' id='chk_".$pmInfo['GlobalUniqueShippingModuleID']."' style='margin: 0px 0px 2px;'>".prepareHTMLDisplay($pmInfo["Name"]). "</label></li></ul>";
				}

                            }
                            break;

                        case "ShippingModuleGroupList":
                            //Options for "Offline" "Online CC" "Online eCheck" and "Online Shipping" <select> control.
                            $SelectedModules = modApiFunc("Checkout", "getSelectedModules", "shipping");

                            $group_name = _ml_substr($tag, _ml_strlen($prefix)+1);
                            $modules = $this->getInstalledModulesListData(array($group_name));
                            $free_shipping_sm_guid = modApiFunc('Shipping_Module_Free_Shipping', 'getUID');

                            foreach($modules as $module)
                            {
                                $name = _ml_strtolower($module->name);
                                $pmInfo = modApiFunc($name, "getInfo");

                                if ($pmInfo['GlobalUniqueShippingModuleID'] == $free_shipping_sm_guid)
                                    continue;

                                if(!array_key_exists($pmInfo['GlobalUniqueShippingModuleID'], $SelectedModules))
                                {
                                    $value .= "<option value=\"" . $pmInfo['GlobalUniqueShippingModuleID'] . "\">" . prepareHTMLDisplay($pmInfo["Name"]) . "</option>";
                                }
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

    /**#@-*/
}
?>