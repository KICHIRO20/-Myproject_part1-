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
 * Checkout module.
 * Group Sort.
 *
 * @author Oleg Vlasenko
 * @package Checkout
 * @access  public
 */
class CheckoutInfoSortGroup
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function CheckoutInfoSortGroup()
    {
        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            // eliminate copying on construction
            $SessionPost = modApiFunc("Session", "get", "SessionPost");
            $this->ViewState = $SessionPost["ViewState"];

            //Remove some data, that should not be sent to action one more time, from ViewState.
            if($this->ViewState["hasError"] == "true")
            {
                $this->ErrorsArray = $this->ViewState["ErrorsArray"];
                unset($this->ViewState["ErrorsArray"]);
            }

            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->ViewState =
                array(
                    "hasError"          => "false",
                    "hasCloseScript"    => "false"
                     );
            $this->POST = array();

        }

        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"checkout-messages", "AdminZone");
    }

    /**
     * Returns the HTML code of the hidden fields of the array ViewState.
     *
     * @return HTML code
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    /**
     * Returns the HTML code of the sorted objects.
     *
     * @return HTML code
     */
    function outputSortObject($type)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        switch ($type)
        {
            case "CS": $res = 'SORT_CTGR_OBJ_CAP_SNGL'; break;
            case "CP": $res = 'SORT_CTGR_OBJ_CAP_PLRL'; break;
            case "S": $res = 'SORT_CTGR_OBJ_SNGL'; break;
            case "P": $res = 'SORT_CTGR_OBJ_PLRL'; break;
        }
        return $obj->getMessage(new ActionMessage($res));
    }

    /**
     * Returns the HTML code of the sorted objects.
     *
     * @return HTML code
     */
    function outputSortSubject()
    {
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return "attributes";
    }

    /**
     * Returns the HTML code of the sorted category list.
     *
     * @return HTML code
     */
    function outputOptionsList($variantId)
    {
        # Get CCategoryInfo Object List
        $ids = modApiFunc('Checkout', 'getPersonInfoAttributeIdList', $variantId, ALL_ATTRIBUTES);
        $OptionsList = '';
        foreach ($ids as $attributeId)
        {
            $fields = modApiFunc('Checkout', 'getPersonInfoFieldsList', $variantId, $attributeId);
            $OptionsList.= '<option value='.$attributeId.'>'.$fields['name'].'</option>';
        }
        return $OptionsList;
    }


    /**
     * Returns the ordered array of the sorted category ids.
     *
     * @return HTML
     */
    function getOptionsListHiddenArray($variantId)
    {
        $ids = modApiFunc('Checkout', 'getPersonInfoAttributeIdList', $variantId);
        return implode('|', $ids);
    }


   /**
    * Returns the reference.
    *
    * @return
    */
    function outputSaveSortHref()
    {
        $request = new Request();
        $request->setView( 'CheckoutInfoSortGroup' );
        $request->setAction( 'SaveSortedAttributes' );
        return $request->getURL();
    }


    /**
     * .
     *
     * @ finish the functions on this page
     */
    function output()
    {
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();

        $variantId =  $_GET['VariantId'];;

        $this->_Template_Contents = array(
            'HiddenArrayViewState'  => $this->outputViewState()
           ,'HiddenFieldAction'     => $HtmlForm->genHiddenField('asc_action', 'SaveSortedAttributes')
           ,'OptionsList'           => $this->outputOptionsList($variantId)
           ,'OptionsListHidden'     => $this->getOptionsListHiddenArray($variantId)
           ,'SaveAttrHref'          => $this->outputSaveSortHref()
           ,'Sort_Object_CP'        => $this->outputSortObject('CP')
           ,'Sort_Object_CS'        => $this->outputSortObject('CS')
           ,'Sort_Object_P'         => $this->outputSortObject('P')
           ,'Sort_Object_S'         => $this->outputSortObject('S')
           ,'Sort_Subject'          => $this->outputSortSubject()
           ,'PageHeader'            => $this->MessageResources->getMessage("PAGE_HEADER")
           ,'PageName'              => $this->MessageResources->getMessage("PAGE_NAME_SORT_ATTRIBUTES")
           ,'GroupName'             => trim(modApiFunc("Checkout", "getVariantNameById" , $variantId))
           ,'VariantId'             => $variantId
        );

        $application->registerAttributes($this->_Template_Contents);


        $retval = modApiFunc('TmplFiller', 'fill', "checkout/checkout-info/","sort-group.tpl.html", array());
        return $retval;
    }



    function getTag($tag)
    {
        return $this->_Template_Contents[$tag];
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    var $MessageResources;

    /**#@-*/

}
?>