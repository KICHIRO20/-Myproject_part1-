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

class UpdateLanguages extends AjaxAction
{
    function UpdateLanguages()
    {
    }

    function onAction()
    {
        global $application;

        $default_lng = '';
        $old_default_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        $application -> enterCriticalSection('UpdateLanguages');

        // getting the request data
        $request = &$application -> getInstance('Request');
        $mode = $request -> getValueByKey('mode');
        switch($mode)
        {
            case 'add_new':
                // adding new language
                $new_lng = $request -> getValueByKey('new_lng');

                // checking if the language exists as active
                if (modApiFunc('MultiLang', 'checkLanguage',
                                            $new_lng['lng'], false))
                {
                    // language already exists
                    modApiFunc('Session', 'set', 'ResultMessage',
                                                 'MNG_LNGS_LNG_EXISTS');
                }
                else
                {
                    $lng_data = modApiFunc('MultiLang', 'getLanguageData',
                                                        $new_lng['lng']);
                    if (isset($lng_data[0]['lng_name']))
                    {
                        $new_lng['lng_name'] = $lng_data[0]['lng_name'];
                        $new_lng['codepage'] = $lng_data[0]['codepage'];
                    }
                    else
                    {
                        $new_lng['lng_name'] = '';
                        $new_lng['codepage'] = '';
                    }
                    modApiFunc('MultiLang', 'addLanguage', $new_lng);

                    if ($new_lng['is_default'] == 'Y')
                        $default_lng = $new_lng['lng'];

                    modApiFunc('Session', 'set', 'ResultMessage',
                                                 'MNG_LNGS_LNG_ADDED');
                }
                break;

            case 'update':
                // updating the languages
                $posted_data = $request -> getValueByKey('posted_data');
                $default_lng = $request -> getValueByKey('default_lng');
                if (is_array($posted_data))
                    foreach($posted_data as $lng => $data)
                    {
                        $data['lng'] = $lng;
                        modApiFunc('MultiLang', 'updateLanguage', $data);
                    }
                modApiFunc('Session', 'set', 'ResultMessage',
                                             'MNG_LNGS_LNG_UPDATED');
                break;

            case 'delete':
                // deleting...
                $to_delete = $request -> getValueByKey('to_delete');
                if (is_array($to_delete))
                    foreach($to_delete as $lng => $v)
                        modApiFunc('MultiLang', 'deleteLanguage', $lng);

                modApiFunc('Session', 'set', 'ResultMessage',
                                             'MNG_LNGS_LNG_DELETED');
                break;
        }

        // setting the default language...
        // if default language is not set
        if (!$default_lng)
        {
            // getting it from the table
            $default_lng = modApiFunc('MultiLang', '_readDefaultLanguage');

            // if still not set trying to set it to the first active one
            if (!$default_lng)
                $default_lng = modApiFunc('MultiLang', '_getAnyLanguage', true);

            // if still not set trying to set it to first one
            if (!$default_lng)
                $default_lng = modApiFunc('MultiLang', '_getAnyLanguage', false);
        }

        $application -> leaveCriticalSection();

        if ($old_default_lng != $default_lng)
        {
            $req_to_redirect = new Request();
            $req_to_redirect -> setView('ChangeDefaultLanguage');
            $req_to_redirect -> setKey('target', $default_lng);
            $application -> redirect($req_to_redirect);
        }
        else
        {
            $req_to_redirect = new Request();
            $req_to_redirect -> setView('LanguageList');
            $application -> redirect($req_to_redirect);
        }
    }
}