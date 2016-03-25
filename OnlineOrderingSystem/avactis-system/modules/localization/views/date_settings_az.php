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
 * Localization Module, DateTimeSettings View.
 *
 * @package Localization
 * @author Alexey Florinsky
 */
class DateTimeSettings
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
    function DateTimeSettings()
    {
    }

    /**
     *
     * @return
     */
    function outputDateTimeFormats($entity)
    {
        $retval = "";

        $Formats = modApiFunc("Localization", "getFormatsList", $entity);
        $CurrentFormat = modApiFunc("Localization", "getFormat", $entity);

        foreach ($Formats as $format)
        {
            $retval.= "<option value=\"".$format["format"]."\"";
            $retval.= (($CurrentFormat == $format["format"])? " SELECTED ":"");
            $retval.= ">".date($format["format"],  1232708132)."</option>";
        }
        return $retval;
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
            return $this->mTmplFiller->fill("localization/date_settings/", "result-message.tpl.html",array());
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
        $request->setView  ('DateTimeFormat');
        $request->setAction('UpdateDateTimeFormat');
        $formAction = $request->getURL();

        $template_contents = array(
                                   "FORM"         => $HtmlForm->genForm($formAction, "POST", "DateTimeForm")
                                  ,"DateFormats"  => $this->outputDateTimeFormats("date")
                                  ,"TimeFormats"  => $this->outputDateTimeFormats("time")
                                  ,"ResultMessage"      => $this->outputResultMessage()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "localization/date_settings/","container.tpl.html", array());
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