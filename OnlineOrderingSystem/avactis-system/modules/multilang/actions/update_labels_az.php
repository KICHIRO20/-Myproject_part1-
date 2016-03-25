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

class ML_UpdateLabels extends AjaxAction
{
    function ML_UpdateLabels()
    {
    }

    function onAction()
    {
        global $application;

        $default_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        $application -> enterCriticalSection('ML_UpdateLabels');

        // getting the request data
        $request = &$application -> getInstance('Request');
        $mode = $request -> getValueByKey('mode');
        $lng = $request -> getValueByKey('lng');

        switch($mode)
        {
            case 'update':
                // updating the labels
                $posted_data = $request -> getValueByKey('posted_data');
                $labels = $request -> getValueByKey('label_id');

                // if labels is empty -> no changes is needed
                if (!is_array($labels) || empty($labels))
                    break;

                if (is_array($posted_data))
                    foreach($posted_data as $id => $data)
                    {
                        // if the label is not selected
                        // no changes is needed
                        if (!in_array($id, $labels))
                            continue;

                        if ($lng == $default_lng)
                        {
                            // if language is default -> updating
                            // resource_labels table
                            modApiFunc('Resources', 'updateLabelText',
                                       $id, $data['def_value']);
                        }
                        else
                        {
                            // updating the multilang entry for the label
                            $ml_label = modApiFunc('MultiLang', 'mapMLField',
                                                   'resource_labels',
                                                   'res_text',
                                                   'Resources');
                            modApiFunc('MultiLang', 'setMLValue',
                                       $ml_label, $id, $data['value'], $lng);
                        }
                    }
                modApiFunc('Session', 'set', 'ResultMessage',
                                             'ML_LABELS_UPDATED');
                break;

            case 'delete':
                // deleting...
                $labels = $request -> getValueByKey('label_id');
                if (is_array($labels))
                    foreach($labels as $id)
                    {
                            $ml_label = modApiFunc('MultiLang', 'mapMLField',
                                                   'resource_labels',
                                                   'res_text',
                                                   'Resources');
                            modApiFunc('MultiLang', 'deleteMLRecord',
                                       $ml_label, $id, $lng);
                    }
                modApiFunc('Session', 'set', 'ResultMessage',
                                             'ML_LABELS_DELETED');
                break;
        }

        $application -> leaveCriticalSection();

        $req_to_redirect = new Request();
        $req_to_redirect -> setView('LabelEditor');
        $application -> redirect($req_to_redirect);
    }
}