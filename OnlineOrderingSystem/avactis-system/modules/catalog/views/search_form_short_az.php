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
_use(dirname(__FILE__) . '/search_form_az.php');
/**
 * View-class to create search form of the products in the Admin Zone.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Florinsky
 */
class SearchFormShort extends SearchForm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */


    function SearchFormShort()
    {
        $this->container_filename = "search-form-container-short.tpl.html";
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