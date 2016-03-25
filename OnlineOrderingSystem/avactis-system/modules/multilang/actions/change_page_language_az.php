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
 * @package MultiLang
 * @author Sergey Kulitsky
 *
 */

class ChangePageLanguage extends AjaxAction
{
    function ChangePageLanguage()
    {
    }

    function onAction()
    {
        global $application;

        // getting the request data
        $request = &$application -> getInstance('Request');
        $return_url = $request -> getValueByKey('returnURL');
        $lng = $request -> getValueByKey('lng');
        $pages = $request -> getValueByKey('pages');
        $views = $request -> getValueByKey('views');
        $actions = $request -> getValueByKey('actions');

        // forcing pages, views, actions to be arrays
        if (!is_array($pages))
            $pages = array($pages);
        if (!is_array($views))
            $views = array($views);
        if (!is_array($actions))
            $actions = array($actions);

        // getting PageLanguages variable from the session
        if (modApiFunc('Session', 'is_set', 'PageLanguages'))
            $PageLanguages = modApiFunc('Session', 'get', 'PageLanguages');
        else
            $PageLanguages = array('pages'   => array(),
                                   'views'   => array(),
                                   'actions' => array());

        // adding the new data to PageLanguages array
        foreach($pages as $v)
            if ($v)
                $PageLanguages['pages'][$v] = $lng;
        foreach($views as $v)
            if ($v)
                $PageLanguages['views'][$v] = $lng;
        foreach($actions as $v)
            if ($v)
                $PageLanguages['actions'][$v] = $lng;

        // saving the updated data in the session
        modApiFunc('Session', 'set', 'PageLanguages', $PageLanguages);

        // redirecting
        $req_to_redirect = new Request($return_url);
        $application -> redirect($req_to_redirect);
    }
}