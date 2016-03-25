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
 * Action is to search by products.
 * This action is waiting for getting a query string from _GET
 * or _POST array by the key 'search_pattern'.
 *
 * If the query string is not empty, then will be done a search
 * and the search id (Search ID) will be saved to the session
 * under the key 'search_result_id'.
 * If the string is empty, then the key 'search_result_id' will be deleted
 * from the session.
 *
 * @package Catalog
 * @author Alexey Florinsky
 */
class SearchProducts extends AjaxAction
{
    /**
     * Processes action.
     */
    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');
        $search_pattern = trim($request->getValueByKey('search_pattern'));
        if (_ml_strlen($search_pattern)==0)
        {
            modApiFunc('Session', 'un_Set', 'search_result_id');
        }
        else
        {
            $search_id = modApiFunc('CatalogSearch', 'prepareProductsSearch', $search_pattern);
            modApiFunc('CatalogSearch', 'deleteOldSearches');
            modApiFunc('Session', 'set', 'search_result_id', $search_id);
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->encodeURLs = false;

        if( !empty( $search_pattern ) )
        {
            $request->setKey( 'keyword', $search_pattern );
        }

        $application->redirect($request);
    }


}

?>