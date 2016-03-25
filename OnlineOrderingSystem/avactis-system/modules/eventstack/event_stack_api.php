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
 * @package EventStack
 * @author Alexey Florinsky
 */
class EventStack
{
    function __construct()
    {
    }

    function install()
    {
        $tables = self::getTables();
        $query = new DB_Table_Create($tables);
        modApiFunc('EventsManager','addEventHandler','OrderCreated','EventStack','onGeneralEvent');
        modApiFunc('EventsManager','addEventHandler','OrderStatusUpdated','EventStack','onGeneralEvent');
        modApiFunc('EventsManager','addEventHandler','OrdersWillBeDeleted','EventStack','onGeneralEvent');
        modApiFunc('EventsManager','addEventHandler','CustomerRegistered','EventStack','onGeneralEvent');
    }

    function onGeneralEvent($param1=null , $param2=null)
    {
    	$event_name = modApiFunc('EventsManager', 'getCurrentEventName');
        $class_name = 'EventInfo_' . $event_name;
        loadClass($class_name);
        if(class_exists($class_name))
        {
            $obj = new $class_name();
            if ($param1 != null && $param2 != null)
                $res = $obj->onEvent($param1, $param2);
            elseif ($param1 != null)
                $res = $obj->onEvent($param1);
            else
                $res = $obj->onEvent();

            $this->saveEvent($obj);
        }
        else
        {
        	echo $class_name . " not found!"; die();
        }
    }

    function saveEvent($e)
    {
        global $application;
        $e_name = execQuery('SELECT_EVENTSTACK_NAME', array('event_name'=>$e->getName()));
        if (is_array($e_name) && isset($e_name[0]) && isset($e_name[0]['id_event_name']))
        {
            $id_event_name = $e_name[0]['id_event_name'];
        }
        else
        {
            execQuery('INSERT_EVENTNAME', array('event_name'=>$e->getName()));
            $id_event_name = $application->db->DB_Insert_Id();
        }

        execQuery('INSERT_EVENT_IN_STACK', array(
        											'event_time'=>$e->getTime(),
        											'id_event_name'=>$id_event_name,
        											'data'=>serialize($e->getFields()),
        ));
        $id_event = $application->db->DB_Insert_Id();
    }

    function getEvents($since=null, $limit=null)
    {
        $params = array();
        if ($since != null) $params['since'] = $since;
        if ($limit != null) $params['limit'] = $limit;
        $event_stack = execQuery('SELECT_EVENTSTACK', $params);

        foreach ($event_stack as $key=>$event)
        {
            $event_stack[$key]['fields'] = unserialize($event['data']);
            unset($event_stack[$key]['data']);
            if (empty($event_stack[$key]['fields'])) {
            	$event_stack[$key]['fields'] = null;
            }
        }
        return $event_stack;
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tbl_events = 'eventstack_event_names';
        $tables[$tbl_events] = array();
        $tables[$tbl_events]['columns'] = array
            (
                'id_event_name'    => $tbl_events.'.id_event_name',
                'event_name'  => $tbl_events.'.event_name',
            );
        $tables[$tbl_events]['types'] = array
            (
                'id_event_name' => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment',
                'event_name'  => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\'',
            );
        $tables[$tbl_events]['primary'] = array
            (
                'id_event_name'
            );
        $tables[$tbl_events]['indexes'] = array
            (
                'IDX_event_name' => 'event_name'
            );

        $tbl_events = 'eventstack';
        $tables[$tbl_events] = array();
        $tables[$tbl_events]['columns'] = array
            (
                'id_event'        => $tbl_events.'.id_event',
                'id_event_name'  => $tbl_events.'.id_event_name',
                'event_time'     => $tbl_events.'.event_time',
                'data'     => $tbl_events.'.data',
            );
        $tables[$tbl_events]['types'] = array
            (
                'id_event'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment',
                'id_event_name'  => DBQUERY_FIELD_TYPE_INT,
                'event_time'     => DBQUERY_FIELD_TYPE_INT,
                'data'     => DBQUERY_FIELD_TYPE_TEXT,
            );
        $tables[$tbl_events]['primary'] = array
            (
                'id_event'
            );
        $tables[$tbl_events]['indexes'] = array
            (
                'IDX_etime' => 'event_time'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(self::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

}
