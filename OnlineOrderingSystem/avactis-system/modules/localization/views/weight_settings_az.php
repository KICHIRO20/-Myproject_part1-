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
 * Localization Module, WeightSettings View.
 *
 * @package Localization
 * @author Alexander Girin
 */
class WeightSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DateTimeSettings constructor.
     */
    function WeightSettings()
    {
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
            return $this->mTmplFiller->fill("localization/weight_settings/", "result-message.tpl.html",array());
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
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();

        $request = new Request();
        $request->setView  ('WeightUnit');
        $request->setAction('UpdateWeightUnit');
        $formAction = $request->getURL();

        $template_contents = array(
                                   "FORM"         => $HtmlForm->genForm($formAction, "POST", "WeightForm")
                                  ,"WeightUnit"   => modApiFunc("Localization", "getValue", "WEIGHT_UNIT")
                                  ,"WeightCoeff"  => modApiFunc("Localization", "FloatToFormatStr", modApiFunc("Localization", "getValue", "WEIGHT_COEFF"), "weight_coeff")
                                  ,"CoeffFormat"  => modApiFunc("Localization", "format_settings_for_js", "weight_coeff")
                                  ,"ResultMessage"      => $this->outputResultMessage()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "localization/weight_settings/","container.tpl.html", array());
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