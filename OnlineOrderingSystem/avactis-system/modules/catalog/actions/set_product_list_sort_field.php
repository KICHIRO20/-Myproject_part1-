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
 * Action handler on SetProductListSortField.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Florinsky
 */
class SetProductListSortField extends AjaxAction
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
    function SetProductListSortField()
    {
    }

    /**
     * Sets current product sort field from Request.
     *
     * Action: SetProductListSortField
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        //          field                                                              ,
        $field = $request->getValueByKey('field');
        $direction = SORT_DIRECTION_ASC;
        if (_ml_strpos($field, ',') !== false)
        {
            $list = array_map('trim',explode(',',$field));
            $field = @$list[0];
            $direction = @$list[1];
        }

        if ($field != NULL)
        {
            modApiFunc('CProductListFilter', 'changeCurrentSortField', $field, $direction);
        }
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