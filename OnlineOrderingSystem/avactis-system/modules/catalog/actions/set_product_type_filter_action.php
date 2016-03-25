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
 * Action handler on SetCurrentManufacturer.
 *
 * @package Catalog
 * @access  public
 */
class SetProductTypeFilter extends AjaxAction
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
    function SetProductTypeFilter()
    {
    }

    /**
     * Sets current manufacturer from Request.
     *
     * Action: SetProductTypeFilter
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $types = $request->getValueByKey('prodtype');

        $ids = null;
        foreach ($types as $t)
        {
            $ids[] = $t;
        }
        if ($ids == null)
        {
            $ids = array();
        }

        modApiFunc('CProductListFilter','changeCurrentProductTypeFilterIds', $ids);
        modApiFunc('Catalog', 'setCurrentProductTypeFilter', $ids);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
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