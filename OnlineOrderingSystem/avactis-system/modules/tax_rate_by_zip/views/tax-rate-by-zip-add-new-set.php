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

class TaxRateByZip_AddNewSet
{
    function TaxRateByZip_AddNewSet()
    {
        loadCoreFile('html_form.php');
        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/add_new_set/');
    }

    function outputResultMessage()
    {
        global $application;

        $output = '';
        if(modApiFunc("Session", "is_set", "SessionPost"))
        {
            $messages = modApiFunc("Session", "get", "SessionPost");
            modApiFunc("Session", "un_set", "SessionPost");
            $i = 0;
            if (isset($messages["Errors"]))
            {
                foreach($messages["Errors"] as $ekey => $eval)
                {
                    $i++;
                    $msg = '';
                    if (count($messages["Errors"]) > 1)
                        $msg .= "$i. ";

                    $template_contents=array(
                        "UniMessage" => $msg . $eval
                    );
                    $this->_Template_Contents = $template_contents;
                    $application->registerAttributes($this->_Template_Contents);

                    $output .= $this->mTmplFiller->fill("", "error-message.tpl.html", array());
                }
            }
        }
        return $output;
    }
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView  (CURRENT_REQUEST_URL);
        $request->setKey("page_view", "TaxRateByZip_AddNewSet");
        $request->setAction('TaxRatesRedirectToImportAction');
        $formAction = $request->getURL();

        $title = getMsg('TAX_ZIP','ADD_NEW_SET_PAGE_TITLE');
        $descr_value = '';
        $updateSid = $request->getValueByKey("updateSid", 0);
        if ($updateSid)
        {
            $set_to_update = modApiFunc("TaxRateByZip", "getSet", $updateSid);
            if (isset($set_to_update[0]["name"]))
            {
                $descr_value = $set_to_update[0]["name"];
                $title = getMsg('TAX_ZIP','UPDATE_SET_PAGE_TITLE');
                $title = str_replace("%1%", $descr_value, $title);
                $request->setKey("updateSid", $updateSid);
                $formAction = $request->getURL();
            }
        }

        $ptypes_select = array(
            'select_name'    => 'TargetPType'
           ,'id'             => 'TargetPType'
           ,'selected_value' => '0'
           ,'values'         => array()
        );

        $ptypes = modApiFunc('Catalog', 'getProductTypes');
        foreach($ptypes as $ptype)
            $ptypes_select['values'][]=array('value'=>$ptype['id'],'contents'=>$ptype['name']);


        $cats_select = array(
            'select_name'    => 'TargetCategory'
           ,'id'             => 'TargetCategory'
           ,'selected_value' => 1
           ,'values'         => array()
        );

        $cats = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);

        foreach($cats as $cat)
            $cats_select['values'][]=array('value'=>$cat['id'],'contents'=>str_repeat('&nbsp;&nbsp;',$cat['level']).$cat['name']);

        $template_contents = array(
            "FormAction" => $formAction
           ,"DescriptionValue" => $descr_value
           ,"Title" => $title
           ,"Errors" => $this->outputResultMessage()
           ,'PTypesList' => HtmlForm::genDropdownSingleChoice($ptypes_select,' style="width: 290px;"')
           ,'CategoriesList' => HtmlForm::genDropdownSingleChoice($cats_select,' style="width: 290px;"')
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);

        return $this->mTmplFiller->fill("", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }
};

?>