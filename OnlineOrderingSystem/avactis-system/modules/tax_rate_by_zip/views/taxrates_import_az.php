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

class TaxRatesImportView
{
    function TaxRatesImportView()
    {
    }

    function output()
    {
        global $application;

        $request = &$application->getInstance("Request");

        $filepath = "";
        $descr = "";
        $filesize = 0;

        if ($SessionPost = modApiFunc("Session", "is_set", "SessionPost"))
        {
            $SessionPost = modApiFunc("Session", "get", "SessionPost");
            if (isset($SessionPost["csv_file_name"]))
            {
                $filepath = $SessionPost["csv_file_name"];
                $filesize = filesize($filepath);
            }
            if (isset($SessionPost["file_description"]))
            {
                $descr = $SessionPost["file_description"];
            }
            //
            // if testing and need to be able to reload the import window
            // multiple times, comment the string below
            // must be uncommented normally
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $request->setView("PopupWindow");
            $request->setKey("page_view", "TaxRateByZip_AddNewSet");
            $application->jsRedirect($request);
        }

        $template_contents = array(
             "Local_InputCSVFilePath" =>  $filepath
            ,"Local_SID" => $request->getValueByKey("sid")
            ,"Local_Upd_SID" => $request->getValueByKey("updateSid", 0)
            ,"CSV_FILE_NAME" => basename($filepath)
            ,"FILE_DESCRIPTION" => $descr
            ,"FILE_SIZE" => modApiFunc("Localization", "formatFileSize", $filesize)
            );
        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/import_set/');

        return $this->mTmplFiller->fill("", "container.tpl.html", array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }
};

?>