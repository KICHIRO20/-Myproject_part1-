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
 * Paginator module.
 * Action handler on SetCurrentCategory.
 *
 * @package Paginator
 * @access  public
 */
class Paginator_SetPage extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function Paginator_SetPage()
    {
        global $application;

        $this->pPaginator = &$application->getInstance('Paginator');
        $this->Pag_Name = array();
    }

    /**
     * Sets the current page of Paginator, that depends on Paginator Name.
     *
     * Action: setpage
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $pag_name = $request->getValueByKey('pgname');
        $pag_num = $request->getValueByKey('pgnum');
        if (strpos($pag_name, 'Catalog_ProdsList_')===0)
        {
            $cid = intval(substr($pag_name, 18));
            modApiFunc('CProductListFilter','changeCurrentCategoryId',$cid);
        }
        elseif (strpos($pag_name, 'Manufacturer_ProdsList_')===0)
        {
            $mnf_id = intval(substr($pag_name, 23));
            modApiFunc('CProductListFilter', 'changeCurrentManufactureId', $mnf_id, true);
        }
        $this->pPaginator->setPaginatorPage($pag_name, $pag_num);
//        $this->pPaginator->savePaginators();
    }

    /**
     * Gets the page number, that depends on Paginator Name.
     *
     * @return int Number of the page
     $ @param string $pag_name Name of the paginator
     */
    function getPageNum($pag_name)
    {
        return $this->Pag_Names[$pag_name];
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Pointer to the module object.
     */
    var $pPaginator;

    /**#@-*/

}

?>