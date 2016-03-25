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
 * License Module, LicenseInfo View
 *
 * @package License
 * @author Alexander Girin
 */
class LicenseInfo
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * CurrencySettings constructor.
     */
    function LicenseInfo()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
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
            return $this->mTmplFiller->fill("license/license_info/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }


    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView  ('LicenseInfo');
        $request->setAction('UpdateLicenseKey');
        $formAction = $request->getURL();

        $_state = modApiFunc("License", "checkLicense");
        $licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
        $cert = modApiFunc("License","getCertificateData");

        $retval = '';

        #trying to update certificate automatically.
        if ($_state === STATE_3 || $_state === STATE_5)
        {
            // update certificate data in background
            $result = $this->updateCertificate();

            if ($result !== "UPDATE_SUCCESS")
            {
                // update failed
                if ($result === "RESPONSE_5") // new valid and not registered key is used. Assuming unregistered application
                {
                    $_state = STATE_2;
                    $licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
                    modApiFunc('Session','un_set','ResultMessage');                 // do not show message
                }
            }
            else
            {
                //  update success
                $cert = modApiFunc("License","getCertificateData");
                if ($_state === STATE_5)
                {
                    $_state = STATE_3;  # if the certificate was updated/fixed succesfully, state should be STATE_3
                    $licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
                    modApiFunc('Session','set','ResultMessage',$result);
                }
            }
        }


        if (is_array($cert) && !empty($cert) && $_state === STATE_3) // update
        {

            foreach ($cert as $fld_name => $fld_data)
            {
                if (is_array($fld_data) && $fld_data['visibility'] === 'visible')
                {
                     // multilinguas support
                     $fld_data['title'] = /*(isset($fld_data['title']) && */$this->MessageResources->isDefined('CERT_FLD_'._ml_strtoupper($fld_name))?$this->MessageResources->getMessage('CERT_FLD_'._ml_strtoupper($fld_name)):$fld_data['title'];
                     $fld_data['hint'] =  /*(isset($fld_data['hint'])  && */$this->MessageResources->isDefined('CERT_FLD_'._ml_strtoupper($fld_name).'_DESCR')?$this->MessageResources->getMessage('CERT_FLD_'._ml_strtoupper($fld_name).'_DESCR'):$fld_data['hint'];

                     $obj = &$application->getInstance('Hint');
                     $hint = $obj->getHintLink(array(urlencode($fld_data['title']), urlencode($fld_data['hint'])));

                     $tags = array(
                                 'FieldName' =>$fld_data['title'],
                                 'FieldValue'=>$fld_data['value'],
                                 'FieldStyle'=>$fld_data['style'],
                                 'FieldHint' =>$fld_data['hint'],
                                 'FieldHintLink' =>$hint
                     );
                     $this->_Template_Contents = $tags;
                     $application->registerAttributes($tags);
                     $retval.= modApiFunc('TmplFiller', 'fill', "license/license_info/","license_field.tpl.html", array());
                }
            }
        }
        else if ($_state === STATE_2 || $_state === STATE_1 || $_state === STATE_4) //registration
        {
            $template_contents = array(
                                   "FormAction" => $formAction
                                  ,"LicenseKey" => $licenseInfo["license_key"]
                                  ,"CurrentURL" => $licenseInfo["current_url"]
                                  ,"CurrentVersionType" => $licenseInfo["current_version_type"]
                                  ,"NewLicenseKey" => ""
                                  ,"HideRegisterButton"=>($_state === STATE_1)?"true":"false"
                                  
                                  ,"Message" => $licenseInfo["license_message"]? $licenseInfo['license_message']:""
                                  
                                  
                                  ,"ResultMessage" => $this->outputResultMessage()
            );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            return modApiFunc('TmplFiller', 'fill', "license/license_info/","container.tpl.html", array());
        }
        else if($_state === STATE_5)
        {
            $template_contents = array(
                                   "FormAction" => $formAction
                                   ,"LicenseKey" => $licenseInfo["license_key"]
                                   ,"CurrentURL" => $licenseInfo["current_url"]
                                   ,"CurrentVersionType" => $licenseInfo["current_version_type"]
                                   ,"NewLicenseKey" => ""
                                   ,"Message" => ($licenseInfo["license_message"]? $licenseInfo['license_message']:"")
                                   ,"ResultMessage" => $this->outputResultMessage()
            );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            return modApiFunc('TmplFiller', 'fill', "license/license_info/","state_5.tpl.html", array());
        }
        else
        {
            // unknown state
            _fatal("unknown store state");
        }

        $tags = array(
                                 'CertificateData'=>$retval,
                                 'Message'=>"",
                                 'ResultMessage'=> $this->outputResultMessage()
                     );
        $this->_Template_Contents = $tags;
        $application->registerAttributes($tags);
        return modApiFunc('TmplFiller', 'fill', "license/license_info/","registered_container.tpl.html", array());
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

    function updateCertificate()
    {
        loadCoreFile('licensekey.php');
        loadCoreFile('licenseaccount.php');

        // sending the registration request
        $lk = new LicenseKey();
        $lac = new LicenseAccountClient();

        $response = $lac->requestUpdateLicense(array("LicenseKey"=>$lk->getLicenseKey(), "LicenseDomain"=>$lk->getLicenseURL(), "LicenseType"=>PRODUCT_VERSION_INTERNAL_TYPE));
        $result_msg = "UNKNOWN_ERROR";

        if ($response['CODE'] == 0 && !empty($response['CERT'])) //success
        {
            loadCoreFile('licensecert.php');

            $lc = new LicenseCertificate();
            if ($lc->_save_cert_to_file($response['CERT']) === false)
            {
                // error saving certificate
                $result_msg = "CERT_ERROR_CANNOT_SAVE";
            }
            else if ($lc->certificate_status === CERTIFICATE_OK)
            {
                // certificate saved successfully
                $result_msg = "UPDATE_SUCCESS"; // message should not be shown
            }
            else
            {
                // invalid certificate received
                $result_msg = "CERT_ERROR_INVALID";
            }
        }
        else if (!empty($response['CODE']))
        {
            // error handling
            $result_msg = "RESPONSE_".$response['CODE'];
            if ($response['CODE'] == 3)
                 $result_msg = "U_RESPONSE_3"; // another message for update is used
        }

        if ($result_msg !== "UPDATE_SUCCESS")
            modApiFunc('Session','set','ResultMessage',$result_msg);

        return $result_msg;
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