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
 * Localization Module, NumberSettings View.
 *
 * @package Localization
 * @author Alexey Florinsky
 */
class NumberSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * NumberSettings constructor.
     */
    function NumberSettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     *
     */
    function outputSelect($key, $sep="", $entity="number")
    {
        $retval = "";

        $CurrentFormat = explode("|", modApiFunc("Localization", "getFormat", $entity));
        switch ($key)
        {
            case "separators":
                $i = 1;
                while ($this->MessageResources->isDefined(sprintf("NUM_FORMAT_SEP_VAL_".$sep."%02d", $i)))
                {
                    $value = $this->MessageResources->getMessage(sprintf("NUM_FORMAT_SEP_VAL_".$sep."%02d", $i));
                    $retval.= "<option value=\"".$value."\"";
                    $retval.= (($CurrentFormat[$sep] == $value)? " SELECTED ":"");
                    $retval.= ">".$this->MessageResources->getMessage(sprintf("NUM_FORMAT_SEP_".$sep."%02d", $i))."</option>";
                    $i++;
                }
                break;
            case "digits":
                for ($i=0; $i<=9; $i++)
                {
                    $retval.= "<option value=\"".$i."\"";
                    $retval.= (($CurrentFormat[0] == $i)? " SELECTED ":"");
                    $retval.= ">".$i."</option>";
                }
                break;
            case "currency_digits":
                for ($i=0; $i<=5; $i++)
                {
                    $retval.= "<option value=\"".$i."\"";
                    $retval.= (($CurrentFormat[0] == $i)? " SELECTED ":"");
                    $retval.= ">".$i."</option>";
                }
                break;
            case "negative":
                $CurrentNegativeFormat = modApiFunc("Localization", "getFormat", "negative");
                $Formats = modApiFunc("Localization", "getFormatsList", "negative");
                foreach ($Formats as $format)
                {
                    $retval.= "<option value=\"".$format["format"]."\"";
                    $retval.= (($CurrentNegativeFormat == $format["format"])? " SELECTED ":"");
                    $retval.= ">".(sprintf($format["format"], number_format(23.95, $CurrentFormat[0], $CurrentFormat[1], $CurrentFormat[2])))."</option>";
                }
                break;
            case "positive_currency":
                $CurrentPositiveCurrencyFormat = modApiFunc("Localization", "getFormat", "currency_positive_format");
                $Formats = modApiFunc("Localization", "getFormatsList", "positive_currency");
                foreach ($Formats as $format)
                {
                    $retval.= "<option value=\"".$format["format"]."\"";
                    $retval.= (($CurrentPositiveCurrencyFormat == $format["format"])? " SELECTED ":"");
                    $retval.= ">".(strtr($format["format"], array('{s}' => prepareHTMLDisplay($this->CurrentCurrency[1]), '{v}'=> number_format(23.95, $CurrentFormat[0], $CurrentFormat[1], $CurrentFormat[2]))))."</option>";
                }
                break;
            case "negative_currency":
                $CurrentNegativeCurrencyFormat = modApiFunc("Localization", "getFormat", "currency_negative_format");
                $Formats = modApiFunc("Localization", "getFormatsList", "negative_currency");
                foreach ($Formats as $format)
                {
                    $retval.= "<option value=\"".$format["format"]."\"";
                    $retval.= (($CurrentNegativeCurrencyFormat == $format["format"])? " SELECTED ":"");
                    $retval.= ">".(strtr($format["format"], array('{s}' => prepareHTMLDisplay($this->CurrentCurrency[1]), '{v}'=> number_format(23.95, $CurrentFormat[0], $CurrentFormat[1], $CurrentFormat[2]))))."</option>";
                }
                break;
            default:

                break;
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
            return $this->mTmplFiller->fill("localization/number_settings/", "result-message.tpl.html",array());
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
        $request->setView  ('NumberFormat');
        $request->setAction('UpdateNumberFormat');
        $formAction = $request->getURL();

        $template_contents = array(
                                   "FORM"               => $HtmlForm->genForm($formAction, "POST", "NumberForm")
                                  ,"DecimalSeparators"  => $this->outputSelect("separators", 1)
                                  ,"DigitSeparators"    => $this->outputSelect("separators", 2)
                                  ,"Digits"             => $this->outputSelect("digits")
                                  ,"Negative"           => $this->outputSelect("negative")
                                  ,"ResultMessage"      => $this->outputResultMessage()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "localization/number_settings/","container.tpl.html", array());
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