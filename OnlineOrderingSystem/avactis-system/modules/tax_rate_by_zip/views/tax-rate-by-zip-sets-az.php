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
 *
 * @package TaxRateByZip
 * @author Ravil Garafutdinov
 */

class TaxRateByZip_Sets
{
    function TaxRateByZip_Sets()
    {
        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/sets_list/');
    }

    function output_Errors()
    {
        global $application;

        $output = '';
        if (modApiFunc("Session", "is_set", "Errors"))
        {
            $messages = modApiFunc("Session", "get", "Errors");
            modApiFunc("Session", "un_set", "Errors");
            $i = 0;
            foreach($messages as $ekey => $eval)
            {
                $i++;
                $msg = '';
                if (count($messages) > 1)
                    $msg .= "$i. ";

                $template_contents=array(
                    "ErrorMessage" => $msg . $eval
                );
                $this->_Template_Contents = $template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $output .= $this->mTmplFiller->fill("", "error-message.tpl.html", $template_contents);
            }

        }
        return $output;
    }

    function output_CheckRatesBlock($formInners)
    {
        global $application;

        $sets_list = modApiFunc("TaxRateByZip", "getSetsList");
        if (empty($sets_list))
            return "";

        $checkRateByZip = array("sid" => 0, "zip" => '');
        if (modApiFunc("Session", "is_set", "CheckRateByZip"))
        {
            $checkRateByZip = modApiFunc("Session", "get", "CheckRateByZip");
        }

        $template_contents=array(
                       "ZipSetsSelect" => $this->output_ZipSetsSelect($checkRateByZip["sid"])
                      ,"CheckRateZipValue" => $checkRateByZip["zip"]
                      ,"WorkingRate" => modApiFunc("TaxRateByZip", "getTaxRateByZip", $checkRateByZip["sid"], $checkRateByZip["zip"])
                      ,"CheckRatesDetails" => $this->output_CheckRatesDetails()
                      ,"FormInners" => $formInners
                );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("", "check-rates-block.tpl.html", $template_contents);
    }

    function output_CheckRatesDetails()
    {
        global $application;

        if (!modApiFunc("Session", "is_set", "Results"))
            return "";

        $template_contents=array(
            "CheckRatesResults" => $this->output_CheckRatesResults()
        );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("", "check-rates-details.tpl.html", $template_contents);
    }

    function output_CheckRatesResults()
    {
        global $application;

        $output = '';
        $messages = array();
        if (modApiFunc("Session", "is_set", "Results"))
        {
            $messages = modApiFunc("Session", "get", "Results");
            modApiFunc("Session", "un_set", "Results");
        }

        $i = 0;
        if (count($messages) == 0)
        {
            $messages[] = getMsg("TAX_ZIP", "SETS_LIST_ENTER_ZIP");
        }

        foreach($messages as $ekey => $eval)
        {
            $template_contents=array(
                "ErrorMessage" => $eval
            );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $output .= $this->mTmplFiller->fill("", "result-message.tpl.html", $template_contents);
        }
        return $output;
    }

    function output_ZipSetsSelect($selected_sid)
    {
        $output = '';
        $sets = modApiFunc("TaxRateByZip", "getSetsList");
        if (empty($sets))
        {
            $output = "<option value=0 disabled>".getMsg("TAX_ZIP", "SETS_LIST_NO_SETS");
        }
        else
        {
            foreach ($sets as $sid => $value) {
                $selected = '';
                if ($selected_sid == $sid)
                    $selected = " selected";
            	$output .= "<option value='$sid'$selected>$value";
            }
        }
        return $output;
    }

    function output_Items()
    {
        global $application;
        $output = '';
        $sets_list = modApiFunc("TaxRateByZip", "getSetsFullList");
        $filled_lines = count($sets_list);
        $this->itemsCount = $filled_lines;

//        if (empty($sets_list))
//        {
//            return "empty"; // @ do an empty list
//        }
//        else
        {
            $i=0;
            foreach ($sets_list as $key => $value)
            {
                $name = $value["name"];
                $fullpath = $application->getAppIni("PRODUCT_FILES_DIR") . "TaxRateByZip_CSVs/" . $value["filename"];
                if ($value["filename"] != NULL && is_file($fullpath))
                {
                    $name = "<a href='#' onClick='openURLinNewWindow(\"download_csv.php?sid={$value["id"]}\", \"Download_CSV\");'><b>$name</b></a>";
                }

                $template_contents = array(
                                           "ITEM_ORDERED_ID" => $i
                                          ,"ItemId" => $value["id"]
                                          ,"ItemName"  => $name
                                          ,"ItemDate" => $value["date"]
                                          ,"ItemFilesize" => modApiFunc("TaxRateByZip", "getTaxSetRecordsNumber", $value["id"])
                                          );
                $this->_Template_Contents = $template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $output .= $this->mTmplFiller->fill("", "item.tpl.html", $template_contents);

                $i++;
            }
        }

        if ($filled_lines < 9)
        {
            for ($i = $filled_lines; $i < 10; $i++)
            {
                $output .= "<tr><td colspan=4>&nbsp;</td>";
            }
        }
        return $output;
    }

    function output()
    {
        global $application;

        modApiFunc("TaxRateByZip", "clearInactiveSets");

        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        $request = new Request();
        $request->setView  (CURRENT_REQUEST_URL);
        $request->setAction('TaxRatesByZipItemsAction');
        $formAction = $request->getURL();
        $formInners = $HtmlForm->genForm($formAction, "POST", "ItemsForm");

        $template_contents = array(
                                   "FormInners"  => $formInners
                                  ,"Errors" => $this->output_Errors()
                                  ,"Items" => $this->output_Items()
                                  ,"Buttons" => "Buttons"
                                  ,"Style" => ''
                                  ,"SETS_NUMBER" => $this->itemsCount
                                  ,"CheckRatesBlock" => $this->output_CheckRatesBlock($formInners)
                             );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        return $this->mTmplFiller->fill("", "container.tpl.html", $template_contents);
    }

    var $itemsCount;
}
?>