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
 * Taxes Module, ProductTaxClassSettings View.
 *
 * @package Taxes
 * @author Alexander Girin
 */
class ProductTaxClassSettings
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
    function ProductTaxClassSettings()
    {
        $this->ClassesList = modApiFunc("Taxes", "getClassesList");
    }

    function outputClassesList()
    {
        global $application;

        $retval = "";
        if ($this->ClassesList == NULL)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-class-settings/","item_na.tpl.html", array());
            for ($i=0; $i<8; $i++)
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-class-settings/","item_empty.tpl.html", array());
            }
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-class-settings/","item_empty_last.tpl.html", array());
        }
        else
        {
            $n = sizeof($this->ClassesList);
            $i=1;
            foreach ($this->ClassesList as $ClassInfo)
            {
                $ClassInfo["i"] = $i;
                $ClassInfo["Disabled"] = $ClassInfo["Type"] == 'standard'? "DISABLED":"";
                $this->_Template_Contents = $ClassInfo;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-class-settings/","item.tpl.html", array());
                $i++;
            }
            if ($n<10)
            {
                for ($i=0; $i<(9-$n); $i++)
                {
                    $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-class-settings/","item_empty.tpl.html", array());
                }
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-class-settings/","item_empty_last.tpl.html", array());
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

        $request = new Request();
        $request->setView  ( 'ProductTaxClassSettings' );
        $request->setAction( 'AddProdTaxClass' );
        $addAction = $request->getURL();

        $request = new Request();
        $request->setView  ( 'ProductTaxClassSettings' );
        $request->setAction( 'UpdateProdTaxClass' );
        $updateAction = $request->getURL();

        $request = new Request();
        $request->setView  ( 'ProductTaxClassSettings' );
        $request->setAction( 'DeleteProdTaxClass' );
        $request->setKey( 'ptc_id', '' );
        $deleteLink = $request->getURL();

        $this->_Template_Contents = array(
                                          'Items' => $this->outputClassesList()
                                         ,'AddAction' => $addAction
                                         ,'UpdateAction' => $updateAction
                                         ,'DeleteLink' => $deleteLink
                                         ,'AddProdTaxClassFromCatalog' => modApiFunc("Taxes", "getAddProdTaxClassFromCatalog")
                                         );
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "taxes/prod-tax-class-settings/","container.tpl.html", array());
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