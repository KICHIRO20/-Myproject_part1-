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

loadCoreFile('db_multiple_insert.php');
loadCoreFile('db_multiple_replace.php');

// ---------------------------
// Select queries
// ---------------------------

class SELECT_ML_LABEL_VALUE extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addSelectField($mltable['value']);
        $this -> WhereValue($mltable['label'], DB_EQ, @$params['label']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['label_key'], DB_EQ, @$params['label_key']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['lng'], DB_EQ, @$params['lng']);
    }
}

class SELECT_ML_LABEL_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addSelectField($mltable['ml_id']);
        $this -> WhereValue($mltable['label'], DB_EQ, @$params['label']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['label_key'], DB_EQ, @$params['label_key']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['lng'], DB_EQ, @$params['lng']);
    }
}

class SELECT_LIST_OF_LANGUAGES extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> addSelectField($altable['lng_number'], 'number');
        $this -> addSelectField($altable['lng'], 'lng');
        $this -> addSelectValue('IF(' . $altable['lng_name_mb'] . ' IS NULL OR ' . $altable['lng_name_mb'] . '=\'\', ' . $altable['lng_name'] . ', ' . $altable['lng_name_mb'] . ')', 'lng_name');
        $this -> addSelectField($altable['codepage'], 'codepage');
        $this -> addSelectField($altable['is_active'], 'is_active');
        $this -> addSelectField($altable['is_default'], 'is_default');

        if (isset($params['lng']))
            $this -> WhereValue($altable['lng'], DB_EQ, $params['lng']);

        if (isset($params['lng']) && $params['active_only'])
            $this -> WhereAND();

        if ($params['active_only'])
            $this -> WhereValue($altable['is_active'], DB_EQ, 'Y');

        $this -> SelectOrder('lng_name');
    }
}

class SELECT_DEFAULT_LANGUAGE extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> addSelectField($altable['lng'], 'lng');
        $this -> WhereValue($altable['is_default'], DB_EQ, 'Y');
    }
}

class SELECT_LANGUAGE_NUMBER extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> addSelectField($altable['lng_number'], 'number');
        $this -> WhereValue($altable['lng'], DB_EQ, $params['lng']);
    }
}

class SELECT_ALL_LANGUAGES extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $ltable = $tables['multilang_languages']['columns'];

        $this -> addSelectField($ltable['lng'], 'lng');
        $this -> addSelectField($ltable['lng_name'], 'lng_name');
        $this -> addSelectField($ltable['codepage'], 'codepage');

        if (isset($params['lng_codes'])
            && is_array($params['lng_codes'])
            && !empty($params['lng_codes']))
            $this -> Where($ltable['lng'], DB_IN, '(\'' . join('\',\'', $params['lng_codes']) . '\')');
        elseif (isset($params['exception'])
            && is_array($params['exception'])
            && !empty($params['exception']))
            $this -> Where($ltable['lng'], DB_NIN, '(\'' . join('\',\'', $params['exception']) . '\')');

        $this -> SelectOrder('lng_name');
    }
}

class SELECT_RESOURCE_MODULES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $rmtable = $tables['resource_meta']['columns'];

        $this -> addSelectField($rmtable['shortname'], 'shortname');
        $this -> addSelectField($rmtable['module'], 'module');

        $this -> WhereValue($rmtable['flag'], DB_NEQ, 'CZ');

        $this -> SelectOrder($rmtable['module']);
    }
}

class SELECT_COUNT_OF_ALL_LABELS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $rltable = $tables['resource_labels']['columns'];

        if (isset($params['prefix']))
            $this -> WhereValue($rltable['res_prefix'], DB_EQ,
                                $params['prefix']);

        if (isset($params['prefix']) && isset($params['begin_with']))
            $this -> WhereAND();

        if (isset($params['begin_with']))
            $this -> WhereValue($rltable['res_label'], DB_LIKE,
                                str_replace('_', '\_', $params['begin_with']) .
                                '%');

        $this -> addSelectField($this -> fCount($rltable['id']), 'count_id');
    }
}

class SELECT_COUNT_OF_TRANSLATED_LABELS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $rltable = $tables['resource_labels']['columns'];

        $this -> addSelectField($this -> fCount($rltable['id']), 'count_id');

        $this -> setMultiLangAlias('_ml_', 'resource_labels',
                                   $rltable['res_text'], $rltable['id'],
                                   'Resources', 0, true, $params['lng']);

        if ($params['translated'] == 'Y')
            $this -> Where('_ml_.ml_id', DB_NNULL, '');
        else
            $this -> Where('_ml_.ml_id', DB_IS_NULL, '');
    }
}

class SELECT_COUNT_OF_LANGUAGE_RECORDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addSelectField($this -> fCount($mltable['ml_id']), 'count_id');
        $this -> WhereValue($mltable['lng'], DB_EQ, $params['lng']);
        if (isset($params['labels'])
            && is_array($params['labels'])
            && !empty($params['labels']))
        {
            $this -> WhereAND();
            $this -> Where($mltable['label'], DB_IN,
                           '(\'' . implode('\',\'', $params['labels']) . '\')');
        }
    }
}

class SELECT_LABEL_TRANSLATION_BY_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addSelectField($mltable['label_key'], 'id');
        $this -> addSelectField($mltable['value'], 'value');

        $this -> WhereValue($mltable['lng'], DB_EQ, $params['lng']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['label'], DB_EQ, $params['label']);
        $this -> WhereAND();
        $this -> Where($mltable['label_key'], DB_IN, '(\'' . join('\',\'', $params['ids']) . '\')');
    }
}

class SELECT_LABELS_BY_FILTER extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $rltable = $tables['resource_labels']['columns'];

        $this -> addSelectField($rltable['id'], 'id');
        $this -> addSelectField($rltable['res_prefix'], 'prefix');
        $this -> addSelectField($rltable['res_label'], 'label');
        $this -> addSelectField($rltable['res_text'], 'def_value');
        $this -> addSelectField('_ml_.value', 'value');

        $this -> setMultiLangAlias('_ml_', 'resource_labels',
                                   $rltable['res_text'], $rltable['id'],
                                   'Resources', '', true, $params['lng']);

        // filter conditions
        $where = array();
        if (isset($params['label']))
        {
            if (isset($params['label']['exactly'])
                && $params['label']['exactly'] == 'Y')
                $where[] = array($rltable['res_label'], DB_EQ,
                                 '\'' . $this -> DBAddSlashes($params['label']['value']) . '\'');
            else
                $where[] = array($rltable['res_label'], DB_LIKE, '\'%' .
                                 str_replace('_', '\_',
                                             $this -> DBAddSlashes($params['label']['value'])) .
                                 '%\'');
        }

        if (isset($params['pattern']))
        {
            if (isset($params['pattern']['exactly'])
                && $params['pattern']['exactly'] == 'Y')
                $where[] = array('(', $rltable['res_text'], DB_EQ,
                                 '\'' . $this -> DBAddSlashes($params['pattern']['value']) . '\'',
                                 DB_OR, '_ml_.value', DB_EQ,
                                 '\'' . $this -> DBAddSlashes($params['pattern']['value']) . '\'',
                                 ')');
            else
                $where[] = array('(', $rltable['res_text'], DB_LIKE, '\'%' .
                                 str_replace('_', '\_',
                                             $this -> DBAddSlashes($params['pattern']['value'])) .
                                 '%\'', DB_OR, '_ml_.value', DB_LIKE, '\'%' .
                                 str_replace('_', '\_',
                                             $this -> DBAddSlashes($params['pattern']['value'])) .
                                 '%\'', ')');
        }

        if (isset($params['type']))
        {
            switch($params['type'])
            {
                case 'all':
                    break;

                case 'storefront':
                    $where[] = array($rltable['res_prefix'], DB_EQ,
                                     '\'CZ\'');
                    break;

                case 'storefront_cz':
                    $where[] = array($rltable['res_prefix'], DB_EQ,
                                     '\'CZ\'');
                    $where[] = array($rltable['res_label'], DB_NLIKE,
                                     '\'CUSTOM\_%\'');
                    break;

                case 'admin':
                    $where[] = array($rltable['res_prefix'], DB_NEQ,
                                     '\'CZ\'');
                    break;

                case 'CZ_CUSTOM':
                    $where[] = array($rltable['res_prefix'], DB_EQ,
                                     '\'CZ\'');
                    $where[] = array($rltable['res_label'], DB_LIKE,
                                     '\'CUSTOM\_%\'');
                    break;

                default:
                    $where[] = array($rltable['res_prefix'], DB_EQ,
                                     '\'' . $params['type'] . '\'');
                    break;
            }
        }

        if (isset($params['status']))
        {
            if ($params['status'] == 'translated')
                $where[] = array('_ml_.ml_id', DB_NNULL);
            elseif ($params['status'] == 'nontranslated')
                $where[] = array('_ml_.ml_id', DB_IS_NULL);
        }

        if (isset($params['label_id']))
            $where[] = array($rltable['id'], DB_EQ,
                             '\'' . $params['label_id'] . '\'');

        // building the where condition
        if (!empty($where))
            foreach($where as $k => $v)
            {
                if ($k != 0)
                    array_push($this -> WhereList, DB_AND);
                foreach($v as $vv)
                    array_push($this -> WhereList, $vv);
            }

        $this -> SelectOrder($rltable['res_label']);

        if (isset($params['paginator']) && is_array($params['paginator']))
        {
            list($offset, $count) = $params['paginator'];
            $this -> SelectLimit($offset, $count);
        }
        elseif (isset($params['limit']) && is_array($params['limit']))
        {
            list($offset, $count) = $params['limit'];
            $this -> SelectLimit($offset, $count);
        }
    }
}

class SELECT_COUNT_OF_ML_RECORDS extends DB_Select
{
    function initQuery($params)
    {
        $this -> addSelectTable($params['table']);
        $this -> addSelectField($this -> fCount('*'), 'num');

        if (is_array($params['filter']) && !empty($params['filter']))
            $this -> Where($params['filter_field'], DB_IN,
                           '(\'' . implode('\',\'', $params['filter']) . '\')');
    }
}

class SELECT_ML_RECORDS extends DB_Select
{
    function initQuery($params)
    {
        $this -> addSelectTable($params['table']);
        $this -> addSelectField($params['key_field'], 'label_key');
        $this -> addSelectField($params['value_field'], 'value');

        if (isset($params['filter']) && is_array($params['filter'])
            && !empty($params['filter']))
            $this -> Where($params['filter_field'], DB_IN,
                           '(\'' . implode('\',\'', $params['filter']) . '\')');

        $this -> SelectOrder($params['key_field']);
        $this -> SelectLimit($params['pos'], $params['bulk']);
    }
}

class SELECT_LANGUAGE_RECORDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addSelectField($mltable['label'], 'label');
        $this -> addSelectField($mltable['label_key'], 'label_key');
        $this -> addSelectField($mltable['value'], 'value');

        $this -> WhereValue($mltable['lng'], DB_EQ, $params['lng']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['label'], DB_EQ, $params['label']);
        $this -> SelectOrder($mltable['ml_id']);
        $this -> SelectLimit($params['pos'], $params['bulk']);
    }
}

class SELECT_ML_ALL_RECORD_VALUES extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addSelectField($mltable['lng'], 'lng');
        $this -> addSelectField($mltable['value'], 'value');
        $this -> WhereValue($mltable['label'], DB_EQ, @$params['label']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['label_key'], DB_EQ,
                            @$params['label_key']);
    }
}

class SELECT_ML_ALL_LANGUAGES_RECORD_VALUES extends DB_Select
{
    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addSelectField($mltable['lng'], 'lng');
        $this -> addSelectField($mltable['value'], 'value');
        $this -> addSelectField($mltable['label'], 'label');
        $this -> addSelectField($mltable['label_key'], 'label_key');
        $this -> Where($mltable['label'], DB_IN, '(\'' . implode('\',\'', $params['label']) . '\')');
    }
}

// ---------------------------
// Update queries
// ---------------------------

class UPDATE_LANGUAGES_CLEAR_DEFAULT_LANGUAGE extends DB_Update
{
    function UPDATE_LANGUAGES_CLEAR_DEFAULT_LANGUAGE()
    {
        parent :: DB_Update('multilang_active_languages');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> addUpdateValue($altable['is_default'], 'N');
    }
}

class UPDATE_LANGUAGES_SET_DEFAULT_LANGUAGE extends DB_Update
{
    function UPDATE_LANGUAGES_SET_DEFAULT_LANGUAGE()
    {
        parent :: DB_Update('multilang_active_languages');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> addUpdateValue($altable['is_default'], 'Y');
        $this -> WhereValue($altable['lng'], DB_EQ, $params['lng']);
    }
}

class UPDATE_LANGUAGE_RECORD extends DB_Update
{
    function UPDATE_LANGUAGE_RECORD()
    {
        parent :: DB_Update('multilang_active_languages');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> addUpdateValue($altable['lng_name_mb'], $params['lng_name']);
        $this -> addUpdateValue($altable['is_active'], $params['is_active']);
        if (isset($params['codepage']))
            $this -> addUpdateValue($altable['codepage'], $params['codepage']);

        $this -> WhereValue($altable['lng'], DB_EQ, $params['lng']);
    }
}

class UPDATE_TMP_ML_RECORDS_LANGUAGE extends DB_Update
{
    function UPDATE_TMP_ML_RECORDS_LANGUAGE()
    {
        parent :: DB_Update('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addUpdateValue($mltable['lng'], $params['lng']);

        $this -> WhereValue($mltable['lng'], DB_EQ, '-1');
    }
}

class UPDATE_ML_RECORD extends DB_Update
{
    function UPDATE_ML_RECORD()
    {
        parent :: DB_Update('multilang_data');
    }

    function initQuery($params)
    {
        $this -> replaceUpdateTable($params['table']);
        $this -> addUpdateValue($params['value_field'], $params['value']);

        $this -> WhereValue($params['key_field'], DB_EQ, $params['key']);
    }
}

// ---------------------------
// Insert queries
// ---------------------------

class INSERT_NEW_LANGUAGE extends DB_Insert
{
    function INSERT_NEW_LANGUAGE()
    {
        parent :: DB_Insert('multilang_active_languages');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> addInsertValue($params['lng'], $altable['lng']);
        $this -> addInsertValue($params['lng_name'], $altable['lng_name']);
        $this -> addInsertValue(@$params['lng_name_mb'], $altable['lng_name_mb']);
        $this -> addInsertValue($params['codepage'], $altable['codepage']);
        $this -> addInsertValue($params['is_active'], $altable['is_active']);
        $this -> addInsertValue($params['is_default'], $altable['is_default']);
    }
}

// ---------------------------
// Multiple Insert queries
// ---------------------------

class MULTIPLE_INSERT_TMP_ML_RECORDS extends DB_Multiple_Insert
{
    function MULTIPLE_INSERT_TMP_ML_RECORDS()
    {
        parent :: DB_Multiple_Insert('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        foreach($params['data'] as $v)
            $this -> addInsertValuesArray($v);

        $this -> setInsertFields(array($mltable['label'],
                                       $mltable['label_key'],
                                       $mltable['lng'],
                                       $mltable['value']));
    }
}

// ---------------------------
// Replace queries
// ---------------------------

class REPLACE_ML_RECORD extends DB_Replace
{
    function REPLACE_ML_RECORD()
    {
        parent :: DB_Replace('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> addReplaceValue(@$params['label'], $mltable['label']);
        $this -> addReplaceValue(@$params['label_key'], $mltable['label_key']);
        $this -> addReplaceValue(@$params['lng'], $mltable['lng']);
        $this -> addReplaceValue(@$params['value'], $mltable['value']);
        $this -> addReplaceValue(@$params['id'], $mltable['ml_id']);
    }
}

// ---------------------------
// Multiple Replace queries
// ---------------------------

class MULTIPLE_REPLACE_ML_RECORDS extends DB_Multiple_Replace
{
    function MULTIPLE_REPLACE_ML_RECORDS()
    {
        parent :: DB_Multiple_Replace('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        foreach($params['data'] as $v)
            $this -> addReplaceValuesArray($v);

        $this -> setReplaceFields(array($mltable['label'],
                                        $mltable['label_key'],
                                        $mltable['lng'],
                                        $mltable['value']));
    }
}

// ---------------------------
// Delete queries
// ---------------------------

class DELETE_ML_RECORD extends DB_Delete
{
    function DELETE_ML_RECORD()
    {
        parent :: DB_Delete('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        if (isset($params['id']))
        {
            $this -> WhereValue($mltable['ml_id'], DB_EQ, $params['id']);
        }
        else
        {
            $this -> WhereValue($mltable['label'], DB_EQ, $params['label']);
            $this -> WhereAND();
            $this -> WhereValue($mltable['label_key'], DB_EQ, $params['label_key']);
            $this -> WhereAND();
            $this -> WhereValue($mltable['lng'], DB_EQ, $params['lng']);
        }
    }
}

class DELETE_ML_FIELD_DATA extends DB_Delete
{
    function DELETE_ML_FIELD_DATA()
    {
        parent :: DB_Delete('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> WhereValue($mltable['label'], DB_EQ, $params['field']);
    }
}

class DELETE_ML_LANGUAGE_DATA extends DB_Delete
{
    function DELETE_ML_LANGUAGE_DATA()
    {
        parent :: DB_Delete('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> WhereValue($mltable['lng'], DB_EQ, $params['lng']);
    }
}

class DELETE_LANGUAGE extends DB_Delete
{
    function DELETE_LANGUAGE()
    {
        parent :: DB_Delete('multilang_active_languages');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $altable = $tables['multilang_active_languages']['columns'];

        $this -> WhereValue($altable['lng'], DB_EQ, $params['lng']);
    }
}

class DELETE_TMP_ML_RECORDS extends DB_Delete
{
    function DELETE_TMP_ML_RECORDS()
    {
        parent :: DB_Delete('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> WhereValue($mltable['lng'], DB_EQ, '-1');
    }
}

class DELETE_ML_RECORDS_BY_LABEL_AND_LABEL_KEY extends DB_Delete
{
    function DELETE_ML_RECORDS_BY_LABEL_AND_LABEL_KEY()
    {
        parent :: DB_Delete('multilang_data');
    }

    function initQuery($params)
    {
        $tables = MultiLang::getTables();
        $mltable = $tables['multilang_data']['columns'];

        $this -> WhereValue($mltable['label'], DB_EQ, @$params['label']);
        $this -> WhereAND();
        $this -> WhereValue($mltable['label_key'], DB_EQ,
                            @$params['label_key']);
    }
}

?>