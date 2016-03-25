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

class RESOURCES_GET_ALL_LABELS_BY_PREFIX extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_labels']['columns'];
        $this->addSelectField($p['res_label'], 'res_label');
        $this->WhereValue($p['res_prefix'], DB_EQ, $params['prefix']);
    }
}


class RESOURCES_GET_MESSAGE_GROUP_BY_PREFIX extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_labels']['columns'];

        $this->addSelectField($p['res_prefix'], 'res_prefix');
        $this->addSelectField($p['res_label'], 'res_label');

        $this->setMultiLangAlias('_ml', 'resource_labels',
                                 $p['res_text'], $p['id'], 'Resources',
                                 0, true, $params['res_lng']);
        $this->addSelectField($this->getMultiLangAlias('_ml'), 'res_text');

        $this->WhereValue($p['res_prefix'], DB_EQ, $params['prefix']);
    }
}

class RESOURCES_GET_MESSAGE_BY_KEY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_labels']['columns'];

        $this->addSelectField($p['res_prefix'], 'res_prefix');
        $this->addSelectField($p['res_label'], 'res_label');

        $this->setMultiLangAlias('_ml', 'resource_labels',
                                 $p['res_text'], $p['id'], 'Resources',
                                 0, true, $params['res_lng']);
        $this->addSelectField($this->getMultiLangAlias('_ml'), 'res_text');

        $this->WhereValue($p['res_prefix'], DB_EQ, $params['prefix']);
        $this->WhereAnd();
        $this->WhereValue($p['res_label'], DB_EQ, $params['label']);
    }
}

class RESOURCES_GET_MESSAGE_IDS_BY_KEY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_labels']['columns'];

        $this->addSelectField($p['id'], 'id');
        $this->addSelectField($p['res_prefix'], 'prefix');
        $this->addSelectField($p['res_label'], 'label');
        $this->addSelectField($p['res_text'], 'text');

        $fl = 0;
        foreach($params['prefix'] as $k => $v)
        {
            if ($fl)
                $this->WhereOR();
            else
                $fl = 1;

            $this->addWhereOpenSection();
            $this->WhereValue($p['res_prefix'], DB_EQ, $v);
            $this->WhereAnd();
            $this->WhereValue($p['res_label'], DB_EQ, $params['label'][$k]);
            $this->addWhereCloseSection();
        }
    }
}

class RESOURCES_GET_META_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_meta']['columns'];

        $this->addSelectField($p['id'], 'id');
        $this->addSelectField($p['shortname'], 'shortname');
        $this->addSelectField($p['filename'], 'filename');
        $this->addSelectField($p['module'], 'module');
        $this->addSelectField($p['flag'], 'flag');
        $this->addSelectField($p['md5'], 'md5');
        $this->WhereValue($p['id'], DB_EQ, $params['id']);
    }
}

class RESOURCES_GET_META_BY_SHORTNAMES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_meta']['columns'];

        $this->addSelectField($p['id'], 'id');
        $this->addSelectField($p['shortname'], 'shortname');
        $this->Where($p['shortname'], DB_IN,
                     '(\'' . join('\',\'', $params['shortnames']) . '\')');
    }
}

class RESOURCES_GET_META_BY_FILENAME extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_meta']['columns'];

        $this->addSelectField($p['id'], 'id');
        $this->addSelectField($p['shortname'], 'shortname');
        $this->addSelectField($p['filename'], 'filename');
        $this->addSelectField($p['module'], 'module');
        $this->addSelectField($p['flag'], 'flag');
        $this->addSelectField($p['md5'], 'md5');
        $this->WhereValue($p['filename'], DB_EQ, $params['filename']);
    }
}

class RESOURCES_SELECT_CUSTOM_META_DATA extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources :: getTables();
        $p = $tables['resource_meta']['columns'];

        $this->addSelectField($p['id'], 'id');
        $this->addSelectField($p['shortname'], 'shortname');
        $this->WhereValue($p['flag'], DB_EQ, 'CZ');
    }
}

class RESOURCES_UPDATE_LABEL_BY_KEY extends DB_Update
{
    function RESOURCES_UPDATE_LABEL_BY_KEY()
    {
        parent::DB_Update('resource_labels');
    }

    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_labels']['columns'];

        $this->addUpdateValue($p['res_text'], $params['text']);
        if (isset($params['label']) && $params['label'])
            $this->addUpdateValue($p['res_label'], $params['label']);

        $this->WhereValue($p['id'], DB_EQ, $params['id']);
    }
}

class DELETE_SYS_RES_LABELS extends DB_Delete
{

 	function DELETE_SYS_RES_LABELS()
    {
        parent :: DB_Delete('resource_labels');
    }
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_labels']['columns'];

        $this->WhereValue($p['res_prefix'], DB_EQ, $params['res_prefix']);
    }
}
//23sep
class RESOURCES_GET_META_BY_RES_MODULE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_meta']['columns'];


        $this->addSelectField($p['shortname'], 'shortname');

        $this->WhereValue($p['module'], DB_EQ, $params['module']);
    }
}

class RESOURCES_GET_MESSAGE_BY_PRE_LBL extends DB_Select
{
    function initQuery($params)
    {
        $tables = Resources::getTables();
        $p = $tables['resource_labels']['columns'];

        $this->addSelectField($p['res_prefix'], 'res_prefix');
        $this->addSelectField($p['res_label'], 'res_label');

        $this->setMultiLangAlias('_ml', 'resource_labels',
                                 $p['res_text'], $p['id'], 'Resources',
                                 0, true, $params['res_lng']);
        $this->addSelectField($this->getMultiLangAlias('_ml'), 'res_text');

        $this->WhereValue($p['res_prefix'], DB_EQ, $params['prefix']);
        $this->WhereAnd();
        $this->WhereValue($p['res_label'], DB_EQ, $params['label']);

    }
}

?>