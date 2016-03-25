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

loadModuleFile('eventstack/event_stack_api.php');

class SELECT_EVENTSTACK_NAME extends DB_select
{
    function initQuery($params)
    {
        $tables = EventStack::getTables();
        $tbl_events = $tables['eventstack_event_names']['columns'];

        $this->addSelectField($tbl_events['id_event_name'], 'id_event_name');
        $this->addSelectField($tbl_events['event_name'], 'event_name');

        if (isset($params['id_event_name']))
        {
            $this->WhereValue($tbl_events['id_event_name'], DB_EQ, $params['id_event_name']);
        }
        elseif(isset($params['event_name']))
        {
            $this->WhereValue($tbl_events['event_name'], DB_EQ, $params['event_name']);
        }
    }
}

class INSERT_EVENTNAME extends DB_Insert
{
    function INSERT_EVENTNAME()
    {
        parent::DB_Insert('eventstack_event_names');
    }

    function initQuery($params)
    {
        $tables = EventStack::getTables();
        $c = $tables['eventstack_event_names']['columns'];

        $this->addInsertValue(DB_NULL,               $c['id_event_name']);
        $this->addInsertValue($params["event_name"], $c['event_name']);
    }
}

class UPDATE_EVENTNAME extends DB_Update
{
    function UPDATE_EVENTNAME()
    {
        parent::DB_Update('eventstack_event_names');
    }

    function initQuery($params)
    {
        $tables = EventStack::getTables();
        $c = $tables['eventstack_event_names']['columns'];

        $this->addUpdateValue($c["event_name"], $params['event_name']);
        $this->WhereValue($c['id_event_name'], DB_EQ, $params["id_event_name"]);
    }
}




class SELECT_EVENTSTACK extends DB_select
{
    function initQuery($params)
    {
        $tables = EventStack::getTables();
        $tbl_events = $tables['eventstack']['columns'];
        $tbl_names = $tables['eventstack_event_names']['columns'];

        $this->addSelectField($tbl_events['id_event'], 'id_event');
        $this->addSelectField($tbl_events['event_time'], 'event_time');
        $this->addSelectField($tbl_names['event_name'], 'event_name');
        $this->addSelectField($tbl_events['data'], 'data');

        $this->WhereField($tbl_events['id_event_name'], DB_EQ, $tbl_names['id_event_name']);

        if (isset($params['since']))
        {
            $this->WhereAnd();
            $this->WhereValue($tbl_events['event_time'], DB_GT, $params['since']);
        }

        $this->SelectOrder('event_time', 'desc');

        $limit = 1000;
        if (isset($params['limit']))
        {
            $l = intval($params['limit']);
            if ($l>0 && $l<=1000) {
                $limit = $l;
            }
        }
        $this->SelectLimit(0,$limit);
    }
}

class INSERT_EVENT_IN_STACK extends DB_Insert
{
    function INSERT_EVENT_IN_STACK()
    {
        parent::DB_Insert('eventstack');
    }

    function initQuery($params)
    {
        $tables = EventStack::getTables();
        $c = $tables['eventstack']['columns'];

        $this->addInsertValue(DB_NULL,                  $c['id_event']);
        $this->addInsertValue($params["id_event_name"], $c['id_event_name']);
        $this->addInsertValue($params["event_time"],    $c['event_time']);
        $this->addInsertValue($params["data"],    $c['data']);
    }
}

