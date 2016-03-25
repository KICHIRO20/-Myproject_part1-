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
 * Output Gift Certificate Editor
 *
 * @package GiftCertificate
 * @author Alexey Florinsky
 */
class GiftCertificateEditView
{
    function GiftCertificateEditView()
    {
        $this->filler = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/gc_edit/');
    }

    function getCurrentGC()
    {
        $gc_code = modApiFunc('Request', 'getValueByKey','gc_code');
        $gc = new GiftCertificate($gc_code);
        if (modApiFunc('Session','is_Set', 'gc_update_action_errors'))
        {
            $SessionPost = modApiFunc('Session', 'get', 'SessionPost');
            $gc->InitByMap($SessionPost);
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        return $gc;
    }

    function getActionName()
    {
        return 'GiftCertificateUpdateAction';
    }

    function output()
    {
        global $application;

        $gc = $this->getCurrentGC();
        if ($gc->isError())
        {
            return $this->filler->fill("", "container.failed.tpl.html",array());
        }
        else
        {
            $map = $gc->getEscapedMap();

            $map['CurrencySign'] = modApiFunc("Localization", "getCurrencySign");

            $map['gc_sendtype_value_email'] = GC_SENDTYPE_EMAIL;
            $map['gc_sendtype_value_post'] = GC_SENDTYPE_POST;

            if ($map['gc_sendtype'] == GC_SENDTYPE_EMAIL)
            {
                $map['gc_sendtype_email_selected'] = 'selected="selected"';
                $map['gc_sendtype_post_selected'] = '';
            }
            else
            {
                $map['gc_sendtype_email_selected'] = '';
                $map['gc_sendtype_post_selected'] = 'selected="selected"';
            }

            $map['gc_status_value_pending'] = GC_STATUS_PENDING;
            $map['gc_status_value_active'] = GC_STATUS_ACTIVE;
            $map['gc_status_value_blocked'] = GC_STATUS_BLOCKED;

            if ($map['gc_status'] == GC_STATUS_PENDING)
            {
                $map['gc_status_pending_selected'] = 'selected="selected"';
                $map['gc_status_active_selected'] = '';
                $map['gc_status_blocked_selected'] = '';
            }
            elseif ($map['gc_status'] == GC_STATUS_ACTIVE)
            {
                $map['gc_status_pending_selected'] = '';
                $map['gc_status_active_selected'] = 'selected="selected"';
                $map['gc_status_blocked_selected'] = '';
            }
            elseif ($map['gc_status'] == GC_STATUS_BLOCKED)
            {
                $map['gc_status_pending_selected'] = '';
                $map['gc_status_active_selected'] = '';
                $map['gc_status_blocked_selected'] = 'selected="selected"';
            }

	        $request = new Request();
	        $request->setView(CURRENT_REQUEST_URL);
			$form_action = $request->getURL();
            $map['form_action'] = $form_action.'?gc_code='.$map['gc_code'].'&asc_action='.$this->getActionName();

            $map['countries'] = modApiFunc("Checkout", "genCountrySelectList", $map['gc_country_id'], false, false);
            $map['states'] = modApiFunc("Checkout", "genStateSelectList", $map['gc_state_id'], $map['gc_country_id'], false);
            $map["JavascriptSynchronizeCountriesAndStatesLists"] = modApiFunc("Location", "getJavascriptCountriesStatesArrays") . modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists") .
                                    //Combine all the OnChange instructions and add them to body.onload()
                                    "<script type=\"text/javascript\">" . "\n" .
                                    "<!--\n" . "\n" .
                                    "var onload_bak = window.onload;" . "\n" .
                                    "window.onload = function()" . "\n" .
                                    "{" . "\n" .
                                    "    if(onload_bak){onload_bak();}" . "\n" .
                                    "    refreshStatesList('gc_country_id', 'gc_state_id', 'stub_state_text_input');" . //$onChangeStatements
                                    "}" . "\n" .
                                    "//-->" . "\n" .
                                    "</script>" . "\n";

            //                                        ,                                 .
            $map['message'] = '';
            $map['updateParent'] = '';
            if (modApiFunc('Session','is_Set', 'gc_update_action_result'))
            {
                $result = modApiFunc('Session','get', 'gc_update_action_result');
                modApiFunc('Session','un_Set', 'gc_update_action_result');
                if ($result == 'failed')
                {
                    //                  
                    $message = '';
                    if (modApiFunc('Session','is_Set', 'gc_update_action_errors'))
                    {
                        $errors = modApiFunc('Session','get', 'gc_update_action_errors');
                        modApiFunc('Session','un_Set', 'gc_update_action_errors');
                        foreach ($errors as $e)
                        {
                            switch($e)
                            {
                                case GC_E_INVALID_CODE:
                                    $txt = getMsg('GCT','GC_E_INVALID_CODE');
                                    break;
                                case GC_E_FIELD_TO:
                                    $txt = getMsg('GCT','GC_E_FIELD_TO');
                                    break;
                                case GC_E_FIELD_FROM:
                                    $txt = getMsg('GCT','GC_E_FIELD_FROM');
                                    break;
                                case GC_E_FIELD_AMOUNT:
                                    $txt = getMsg('GCT','GC_E_FIELD_AMOUNT');
                                    break;
                                case GC_E_FIELD_SENDTYPE:
                                    $txt = getMsg('GCT','GC_E_FIELD_SENDTYPE');
                                    break;
                                case GC_E_FIELD_CODE:
                                    $txt = getMsg('GCT','GC_E_FIELD_CODE');
                                    break;
                                case GC_E_FIELD_ID:
                                    $txt = getMsg('GCT','GC_E_FIELD_ID');
                                    break;
                                case GC_E_FIELD_REMAINDER:
                                    $txt = getMsg('GCT','GC_E_FIELD_REMAINDER');
                                    break;
                                case GC_E_FIELD_DATE_CREATED:
                                    $txt = getMsg('GCT','GC_E_FIELD_DATE_CREATED');
                                    break;
                                case GC_E_FIELD_STATUS:
                                    $txt = getMsg('GCT','GC_E_FIELD_STATUS');
                                    break;
                                case GC_E_FAILED_LOAD:
                                    $txt = getMsg('GCT','GC_E_FAILED_LOAD');
                                    break;
                                case GC_E_FIELD_ADDRESS:
                                    $txt = getMsg('GCT','GC_E_FIELD_ADDRESS');
                                    break;
                                case GC_E_FIELD_CITY:
                                    $txt = getMsg('GCT','GC_E_FIELD_CITY');
                                    break;
                                case GC_E_FIELD_FNAME:
                                    $txt = getMsg('GCT','GC_E_FIELD_FNAME');
                                    break;
                                case GC_E_FIELD_LNAME:
                                    $txt = getMsg('GCT','GC_E_FIELD_LNAME');
                                    break;
                                case GC_E_FIELD_EMAIL:
                                    $txt = getMsg('GCT','GC_E_FIELD_EMAIL');
                                    break;
                                case GC_E_FIELD_ZIP:
                                    $txt = getMsg('GCT','GC_E_FIELD_ZIP');
                                    break;
                                case GC_E_AMOUNT_LESS_REMAINDER:
                                    $txt = getMsg('GCT','GC_E_AMOUNT_LESS_REMAINDER');
                                    break;
                                default:
                                    $txt = getMsg('GCT','GC_E_FAILED_SAVE');
                            }
                            $message .= $this->filler->fill("", "error.tpl.html",array('msg'=>$txt));
                        }
                    }
                    //                                           
                    if (!empty($message))
                    {
                        $map['message'] = $this->filler->fill("", "errors.container.tpl.html",array('message'=>$message));
                    }
                }
                else
                {
                    //                                
                    $map['message'] = $this->filler->fill("", "success.container.tpl.html",array('message'=>getMsg('GCT','GC_UPDATED')));
                    global $application;
                    //$map['updateParent'] = $application->closeChild_UpdateParent();
                }
            }
        }

        $application->registerAttributes(array('Local_mode'));
        $res = $this->filler->fill("", "container.tpl.html",$map);
        return $res;
    }


    function getTag($tag)
    {
        global $application;

        $value = null;
        if ($tag == 'Local_mode') $value = 'edit';
        return $value;
    }

    var $filler;
    var $__gc_item;
    var $paginator_name;
}

?>