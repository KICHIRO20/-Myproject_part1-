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

_use(dirname(__FILE__).'/add_manufacturer_az.php');

/**
 * @package Manufacturers
 * @access  public
 *
 */
class EditManufacturer extends AddManufacturer
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     */
    function EditManufacturer()
    {
        global $application;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initDBFormData();
        }
    }

    /**
     *
     *
     * @return
     */
    function initDBFormData()
    {
            $mnf_id=modApiFunc('Manufacturers', 'getEditableManufacturerID');

            $manufacturerInfo = modApiFunc('Manufacturers', 'getManufacturerInfo', $mnf_id);
            $this->ViewState =
                array(
                    "hasCloseScript"   => "false"
                     );

            $this->POST  = array
            (
                "ManufacturerID" => $manufacturerInfo['manufacturer_id'],
                "ManufacturerNameText" => $manufacturerInfo['manufacturer_name'],
                "ManufacturerUrlText" => $manufacturerInfo['manufacturer_site_url'],
                "ManufacturerDescText" => $manufacturerInfo['manufacturer_descr'],
                "ManufacturerStatusValue" => $manufacturerInfo['manufacturer_active'],
                "ManufacturerImage" => new image_obj($manufacturerInfo['manufacturer_image_id'])
            );
    }

    /**
     *                 Manufacturers_AddManufacturer::copyFormData() -                     'ManufacturerID'          $this->POST
     */
    function copyFormData()
    {
        AddManufacturer::copyFormData();
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->POST['ManufacturerID'] = $SessionPost['ManufacturerID'];
    }

    /**
     *
     */
    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents["SubmitSaveScript"] = $HtmlForm1->genSubmitScript("AddManufacturerForm");
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("manufacturers/edit_manufacturer/", "subtitle.tpl.html", array());
    }

    function outputManufacturerId()
    {
        global $application;
		$retval = $this->POST['ManufacturerID'];
		return $retval;
    }

    /**
     *                     ViewState
     */
    function outputViewStateConstants()
    {
        //$retval = Catalog_AddManufacturer::outputViewStateConstants();
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "update_manufacturer") . ">";
        $retval.= "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("ManufacturerID", $this->POST["ManufacturerID"]) . ">";
        return $retval;
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $mTmplFiller;
    /**#@-*/

    /**
     * Pointer to the received from action or prepared FORM data.
     */
    var $POST;

    /**
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
}
?>