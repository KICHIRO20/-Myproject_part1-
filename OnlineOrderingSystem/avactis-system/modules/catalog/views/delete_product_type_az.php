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
 * Catalog module.
 * Catalog Product Type Delete view.
 *
 * @author Alexander Girin
 * @package Catalog
 * @access  public
 */
class DeleteProductType
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
    function DeleteProductType()
    {
        global $application;
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

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

    function outputDeleteSubject()
    {
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return 'product_type';
    }

    /**
     * Returns the HTML code of the warning message.
     *
     * @return HTML code
     */
    function outputListItems()
    {
        global $application;
        $items = '';

        $obj = &$application->getInstance('MessageResources');
        $ProdTypesFullList = modApiFunc('Catalog', 'getProductTypes');
        $i = 0;
        foreach ($ProdTypesFullList as $prodTypeInfo)
        {
            $this->_Current_ProductType = $prodTypeInfo;
            $this->_Current_ProductType['ProductTypeWarning'] = '';
            $this->_Current_ProductType['ProductTypeEnabled'] = '';
            if ($prodTypeInfo['description'])
            {
                $this->_Current_ProductType['ProductTypeWarning'] = '&nbsp;-&nbsp;'.$prodTypeInfo['description'];
            }
            if (in_array($prodTypeInfo['id'], array(1, GC_PRODUCT_TYPE_ID)))
            {
                $this->_Current_ProductType['name'] = "<B>".$this->_Current_ProductType['name']."</B>";
                $this->_Current_ProductType['ProductTypeEnabled'] = 'Disabled';
                $this->_Current_ProductType['ProductTypeWarning'].= $obj->getMessage( new ActionMessage('PRTYPE_DEL_GENPR_WARNING'));
            }
            else
            {
                $prod_num = modApiFunc("Catalog", "getProductsQuantityByType", $prodTypeInfo['id']);
                if ($prod_num||$prod_num!=0)
                {
                    $this->_Current_ProductType['name'] = "<B>".$this->_Current_ProductType['name']."</B>";
                    $this->_Current_ProductType['ProductTypeEnabled'] = 'Disabled';
                    $this->_Current_ProductType['ProductTypeWarning'] = "&nbsp;-&nbsp;<span class=\"required\">".$obj->getMessage( new ActionMessage( array('PRTYPE_DEL_WARNING', $prod_num)))."</span>";
                }
            }
            $this->_Current_ProductType['ProductTypeI'] = $i++;
            $application->registerAttributes($this->_Current_ProductType);
            $items.= modApiFunc('TmplFiller', 'fill', "catalog/product_type_delete/","list_item.tpl.html",array());
        }

        return $items;
    }


    /**
     * Returns the Form Action reference.
     *
     * @return Form Action
     */
    function outputDeleteHref()
    {
        $request = new Request();
        $request->setView  ( 'DeleteProductType' );
        $request->setAction( 'ConfirmDeleteProductTypes' );
        return $request->getURL();
    }

    /**
     * Returns the name of the action class.
     *
     * @return Form Action
     */
    function outputAction()
    {
        return 'ConfirmDeleteProductTypes';
    }

    /**
     * Returns the Catalog Category Info view.
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
        $application->registerAttributes(
            array(
                'HiddenArrayViewState'
//               ,'action'
               ,'HiddenFieldAction'
               ,'DeleteProdTypeHref'
               ,'Delete_Subject'
                )
        );

        $retval = modApiFunc('TmplFiller', 'fill', "catalog/product_type_delete/","list.tpl.html", array());
        return $retval;
    }

    /**
     * @ describe the function ProductList->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = "";
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        switch ($tag)
        {
            case 'HiddenArrayViewState':
                $value = $this->outputViewState();
                break;
            case 'Items':
                $value = $this->outputListItems();
                break;
            case 'HiddenFieldAction':
                $value = $HtmlForm->genHiddenField('asc_action', $this->outputAction());
                break;
            case 'DeleteProdTypeHref':
                $value = $this->outputDeleteHref();
                break;
            case 'Delete_Subject':
                $value = $this->outputDeleteSubject();
                break;
            case 'ProductTypeI':
                $value = $this->_Current_ProductType['ProductTypeI'];
                break;
            case 'ProductTypeID':
                $value = $this->_Current_ProductType['id'];
                break;
            case 'ProductTypeEnabled':
                $value = $this->_Current_ProductType['ProductTypeEnabled'];
                break;
            case 'ProductTypeName':
                $value = $this->_Current_ProductType['name'];
                break;
            case 'ProductTypeWarning':
                $value = $this->_Current_ProductType['ProductTypeWarning'];
                break;
            case 'ProductTypeInfoLink':
                $value = $this->_Current_Product['ProductInfoLink'];
                break;
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

    var $mTmplFiller;

    var $_Current_ProductType = array();
    /**#@-*/

}
?>