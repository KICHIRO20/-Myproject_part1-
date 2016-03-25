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
 * License Module, LicenseMention View.
 *
 * @package License
 * @author Alexander Girin
 */
class LicenseMention
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
    function LicenseMention()
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
            return $this->mTmplFiller->fill("license/license_mention/", "result-message.tpl.html",array());
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
        $retval = "";

        $request = new Request();
        $request->setView('Index');
        $request->setAction('UpdateLicenseKey');
        $formAction = $request->getURL();

        $_state = modApiFunc("License", "checkLicense");
        $licenseInfo = modApiFunc("License","getLicenseInfo", $_state);

        if($licenseInfo === null || $_state === STATE_3)
        {
            return '';
        };

        $this->_Template_Contents = array(
                                          "FormAction" => $formAction
                                         ,"Message" => $licenseInfo['license_message_home']
                                         ,"ResultMessage" => $this->outputResultMessage()
                                         );

        if (!empty($this->_Template_Contents["Message"]))
        {
            $application->registerAttributes($this->_Template_Contents);
            $retval = modApiFunc('TmplFiller', 'fill', "license/license_mention/","container.tpl.html", array());
        }

        return $retval;
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