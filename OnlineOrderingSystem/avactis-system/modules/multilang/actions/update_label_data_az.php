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

class ML_UpdateLabelData extends AjaxAction
{
    function ML_UpdateLabelData()
    {
    }

    function onAction()
    {
        global $application;

        $default_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        $application -> enterCriticalSection('ML_UpdateLabelData');

        // getting the request data
        $request = &$application -> getInstance('Request');
        $lng = $request -> getValueByKey('lng');
        $label_data = $request -> getValueByKey('label_data');

        // flag if there is an error
        $error = '';

        // checking the language
        if ($lng != $default_lng &&
            !modApiFunc('MultiLang', 'checkLanguage', $lng, false))
        {
            $error = 'ML_ERROR_INVALID_LANGUAGE';
            $lng = modApiFunc('MultiLang', 'getDefaultLanguage');
        }

        // checking the label id
        // and setting the label name for future use
        if ($label_data['id'])
        {
            $tmp = modApiFunc('MultiLang', 'searchLabels',
                              array('label_id' => $label_data['id'],
                                    'lng' => $lng));
            if (!$tmp)
            {
                // label_id is incorrect
                $label_data['id'] = 0;
                $error = 'ML_ERROR_INVALID_LABEL';
                $label_data['label'] = 'CUSTOM_';
                $label_data['prefix'] = 'CZ';
            }
            else
            {
                $label_data['label'] = $tmp[0]['label'];
                $label_data['prefix'] = $tmp[0]['prefix'];
            }

            // checking if the label is custom
            // while trying to change its name
            if (isset($label_data['custom_label'])
                && _ml_substr($tmp[0]['label'], 0, 7) != 'CUSTOM_')
                $error = 'ML_ERROR_NOT_CUSTOM_LABEL';
        }
        else
        {
            $label_data['label'] = 'CUSTOM_';
            $label_data['prefix'] = 'CZ';
        }

        // checking the label name for custom labels
        if (!$error
            && (!$label_data['id'] || isset($label_data['custom_label']))
            && !$label_data['custom_label'])
            $error = 'ML_ERROR_EMPTY_CUSTOM_LABEL_NAME';

        // ckecking if custom_name contain invalid symbols
        if (!$error && !$label_data['id'])
            for($i = 0; $i < _byte_strlen($label_data['custom_label']); $i++)
            {
                $tmp = _byte_ord($label_data['custom_label']{$i});
                if ($tmp < 48 || ($tmp > 57 && $tmp < 65)
                    || ($tmp > 90 && $tmp != 95))
                {
                    $error = 'ML_ERROR_INVALID_CUSTOM_LABEL_NAME';
                    break;
                }
            }

        // checking if the label name is unique
        if (!$error
            && (!$label_data['id'] || isset($label_data['custom_label'])))
        {
            $tmp = modApiFunc('MultiLang', 'searchLabels',
                              array(
                                'label' => array(
                                             'exactly' => 'Y',
                                             'value' => 'CUSTOM_' .
                                               $label_data['custom_label']
                                           ),
                                'type' => 'CZ_CUSTOM',
                                'lng' => $lng
                              ));
            if ($tmp && $tmp[0]['id'] != $label_data['id'])
                $error = 'ML_ERROR_LABEL_EXISTS';
        }

        if (!$error)
        {
            // we are ready to save the changes
            if (!$label_data['id'])
            {
                // inserting a new label
                modApiFunc('Resources', 'addLabelToDB',
                           'CUSTOM_' . $label_data['custom_label'],
                           $label_data['def_value']);
                $label_data['id'] = $application -> db -> DB_Insert_Id();

                // saving the result message
                modApiFunc('Session', 'set',
                           'ResultMessage', 'ML_SUCCESS_LABEL_ADDED');
            }
            else
            {
                // updating the label
                modApiFunc('Resources', 'updateLabelText',
                           $label_data['id'], $label_data['def_value'],
                           ((isset($label_data['custom_label']))
                             ? 'CUSTOM_' . $label_data['custom_label']
                             : ''));
                modApiFunc('Session', 'set',
                           'ResultMessage', 'ML_SUCCESS_LABEL_UPDATED');
            }

            // saving the multilang data if needed
            if ($lng != modApiFunc('MultiLang', 'getDefaultLanguage'))
            {
                $ml_label = modApiFunc('MultiLang', 'mapMLField',
                                       'resource_labels', 'res_text',
                                       'Resources');
                modApiFunc('MultiLang', 'setMLValue', $ml_label,
                           $label_data['id'], $label_data['value'], $lng);
            }

            modApiFunc('Session', 'set', 'ML_ReloadParentWindow', 'Y');
        }
        else
        {
            // form contain an error, saving it to session
            modApiFunc('Session', 'set', 'SavedLabelData', $label_data);

            // saving the result
            modApiFunc('Session', 'set', 'ResultMessage', $error);
        }

        $application -> leaveCriticalSection();

        $req_to_redirect = new Request();
        $req_to_redirect -> setView('PopupWindow');
        $req_to_redirect -> setKey('page_view', 'LabelData');
        $req_to_redirect -> setKey('label_id', $label_data['id']);
        $req_to_redirect -> setKey('lng', $lng);
        $application -> redirect($req_to_redirect);
    }
}