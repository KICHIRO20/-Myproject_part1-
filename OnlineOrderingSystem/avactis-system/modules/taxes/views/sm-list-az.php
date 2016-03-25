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
 * Taxes Module, ShippingModulesListForTaxes View.
 *
 * @package Taxes
 * @author Alexander Girin
 */
class ShippingModulesListForTaxes
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     */
    function ShippingModulesListForTaxes()
    {
        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }
    }

    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];

        $this->POST = array();
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );
        $this->POST = array();
    }

    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    function outputShippingModulesList()
    {
        global $application;

        $retval = "";

        $sm_list = modApiFunc("Taxes", "getShippingModulesList");

        $n = sizeof($sm_list);
        if ($n == 0)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-sm-list/","item_na.tpl.html", array());
            for ($i=0; $i<9; $i++)
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-sm-list/","item_empty.tpl.html", array());
            }
        }
        else
        {
            $i = 0;
            foreach ($sm_list as $sm_id => $sm_info)
            {
                $this->_Template_Contents = array(
                                                  'Name' => prepareHTMLDisplay($sm_info["Name"])
                                                 ,'I'    => $i
                                                 ,'Id'   => $sm_id
                                                 ,'Checked' => ($sm_info["Checked"])? "CHECKED":""
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-sm-list/","item.tpl.html", array());
                $i++;
            }
            if ($n<10)
            {
                for ($i=0; $i<(10-$n); $i++)
                {
                    $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-sm-list/","item_empty.tpl.html", array());
                }
            }
        }
        return $retval;
    }


    /**
     *
     */
    function output()
    {
        global $application;

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $request = new Request();
        $request->setView  ('ShippingTaxes');
        $request->setAction('SetShippingTaxes');
        $formAction = $request->getURL();

        $this->_Template_Contents = array(
                                          'FormAction' => $formAction
                                         ,'HiddenArrayViewState' => $this->outputViewState()
                                         ,'Items' => $this->outputShippingModulesList()
                                         );
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-sm-list/","container.tpl.html", array());
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