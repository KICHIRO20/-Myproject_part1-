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
 * Module EventsManager
 *
 *       EventsManager
 *                                      .
 *
 *                                :
 * modApiFunc('EventsManager','addEventHandler','EventName','HandlerClassName','HandlerClassMethod');
 *
 *                                                                     :
 *  -             ,                                              ,
 *  -
 *
 * @see EventsManager::addEventHandler()
 *
 *
 *                             :
 * modApiFunc('EventsManager','removeEventHandler','EventName','HandlerClassName','HandlerClassMethod');
 *
 *
 *                             :
 * modApiFunc('EventsManager','throwEvent', 'EventName');
 *
 *                                                                     .
 *             ,
 *                                   .
 *
 *
 *                             :
 * $handler_results = modApiFunc('EventsManager','processEvent', 'EventName');
 *
 *                         $handler_results
 *                                                  .                       ,
 *                                                       .
 *
 *
 *             ,
 *                                            .
 *
 * @see EventsManager::processEvent(), EventsManager::throwEvent()
 *
 *
 *                             :
 *                                    ,
 *                                ,
 *          .
 *                                        .
 *
 *                                                .
 *
 *       :
 *   $data = array(...);
 *   $handler_list = modApiFunc('EventsManager','getEventHandlers','EventName');
 *   foreach($handler_list as $handler)
 *   {
 *       $handler_obj = modApiFunc('EventsManager','getEventHandlerObject',$handler);
 *       $handler_result  = $handler_obj->$handler['handler_method']($data);
 *       // process $handler_result ...
 *   }
 *
 *         ,              $data                      ,
 *                                             ,  . .
 *            &                 .
 *
 *
 * @package EventsManager
 * @author Alexey Florinsky
 */
class EventsManager
{
    function EventsManager()
    {
        $this->__events_stack = array();
    }

    /**
     *                                   .
     *
     *         ,                               ,                        .
     *                                                  : $event_name,
     * $handler_class, $handler_method.  . .
     *                      .
     *
     *                                               :
     *
     * $include_path -                      ,                           $handler_class.
     *                                    ,            $handler_class            ,
     * EventManager                                                     .
     *   -         ,                    NULL.     API                ,
     *                  ,         ModulesManager "     "                 .
     *
     * $sort_order -                  ,
     *                                             .                 ,
     *                       -       .                                  100.
     *                                   ,
     *                                           .
     *
     * @param string $event_name                 ,
     * @param string $handler_class
     * @param string $handler_method
     * @param string $include_path                      ,                      ,
     *
     * @param int $sort_order                      ,
     */
    function addEventHandler($event_name, $handler_class, $handler_method,  $include_path=null, $sort_order=null)
    {
        $tables = EventsManager::getTables();
        $tbl_events = $tables['events_manager']['columns'];

        $query = new DB_Replace('events_manager');
        $query->addReplaceValue($event_name,     $tbl_events['event_name']);
        $query->addReplaceValue($handler_class,  $tbl_events['handler_class']);
        $query->addReplaceValue($handler_method, $tbl_events['handler_method']);

        if ($sort_order != null)
            $query->addReplaceValue($sort_order, $tbl_events['handler_order']);

        if ($include_path != null)
            $query->addReplaceValue($include_path, $tbl_events['handler_include_path']);

        global $application;
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();
    }

    /**
     *                                            .
     *
     * @param string $event_name
     * @param string $handler_class
     * @param string $handler_method
     */
    function removeEventHandler($event_name, $handler_class, $handler_method)
    {
        global $application;
        $tables = EventsManager::getTables();
        $tbl_events = $tables['events_manager']['columns'];

        $query = new DB_Delete('events_manager');
        $query->WhereValue($tbl_events['event_name'], DB_EQ, $event_name);
        $query->WhereAND();
        $query->WhereValue($tbl_events['handler_class'], DB_EQ, $handler_class);
        $query->WhereAND();
        $query->WhereValue($tbl_events['handler_method'], DB_EQ, $handler_method);
        $application->db->getDB_Result($query);
    }

    /**
     *                                                        .
     *
     *                                            :
     *
     *        $event_name -             ,                 ,
     *                        .
     *                ,                                                  .
     *
     *        $handler_class -             ,                            ,
     *                      ,
     *                .
     *
     *        $handler_method -             ,                         ,
     *                      ,
     *                          .
     *
     *        ,
     *                     .
     *
     *                                                          :
     * array(
     *      array
     *      (
     *          [event_id] => int
     *          [handler_class] => string
     *          [handler_method] => string
     *          [handler_include_path] => string
     *      )
     *      ...
     * )
     *
     * @param string $event_name
     * @param string $handler_class                      ,
     * @param string $handler_method                      ,
     * @return array
     */
    function getEventHandlers($event_name, $handler_class = null, $handler_method = null)
    {
        $params = array(
            "event_name" => $event_name,
            "handler_class" => $handler_class,
            "handler_method" => $handler_method
        );

        return execQuery('SELECT_EVENT_HANDLER',$params);
    }

    /**
     *                                                              .
     *
     *                                    -    .
     *             ,                                              ,
     *                             .
     *
     *          $handler                      ,                        ,
     *                               :
     * array
     * (
     *     [handler_class] => string
     *     [handler_method] => string
     *     [handler_include_path] => string
     * )
     *
     *
     *         EventsManager::getEventHandlers(...).
     *                                                            .
     *
     *                          $param1   $param2,                ,
     *  . .             NULL,                                    .
     *
     *                                  ,
     *                    .                                   ,
     *                 NULL.
     *
     *                                :
     *  -        $handler
     *  -
     *  -
     *
     * @param array $handler          ,
     * @param mixed $param1
     * @param mixed $param2
     * @return mixed NULL or
     */
    function executeEventHandler($handler, $param1=null , $param2=null)
    {
        if (
             isset($handler['handler_class']) &&
             isset($handler['handler_method']) &&
             $this->_tryInclude($handler['handler_class'], $handler['handler_include_path']) &&
             is_callable($handler['handler_class'], $handler['handler_method'])
           )
        {
            global $application;
            $obj = &$application->getInstance($handler['handler_class']);
            $method = $handler['handler_method'];
            $this->__pushEventName($handler['event_name']);
            if ($param1 != null && $param2 != null)
                $res = $obj->$method($param1, $param2);
            elseif ($param1 != null)
                $res = $obj->$method($param1);
            else
                $res = $obj->$method();
            $this->__popEventName();
            return $res;
        }
        else
        {
            return null;
        }
    }

    /**
     *                                            .
     *
     *          $handler                      ,                              ,
     *                               :
     * array
     * (
     *     [handler_class] => string
     *     [handler_method] => string
     *     [handler_include_path] => string
     * )
     *
     *
     *         EventsManager::getEventHandlers(...).
     *
     *                                  ,                     NULL:
     *  -        $handler
     *  -
     *  -
     *
     * @param array $handler Handler description
     * @return object Handler object or NULL
     */
    function getEventHandlerObject($handler)
    {
        if (
             isset($handler['handler_class']) &&
             isset($handler['handler_method']) &&
             $this->_tryInclude($handler['handler_class'], $handler['handler_include_path']) &&
             is_callable($handler['handler_class'], $handler['handler_method'])
           )
        {
            global $application;
            $obj = new $handler['handler_class'];
            return $obj;
        }
        else
        {
            return null;
        }
    }

    /**
     *                                      .
     *
     *                                                 .
     *                                   $param1   $param2,
     *                                   .
     *
     *
     *                             .  . .              -
     *                                     ,
     *                                         .
     *
     * @param string $event_name
     * @param mixed $param1
     * @param mixed $param2
     */
    function throwEvent($event_name, $param1=null , $param2=null)
    {
        $eHandlers = $this->getEventHandlers($event_name);
        foreach($eHandlers as $handler)
        {
            $this->executeEventHandler($handler, $param1, $param2);
        }
    }

    /**
     *
     *                    .
     *
     *                         EventsManager::throwEvent(...)
     *         ,
     *         ,        ,                             ,             .
     *
     *                                             :
     *  -
     *  -
     *
     *                     ,
     *                                        .
     *
     * @param string $event_name
     * @param mixed $param1
     * @param mixed $param2
     * @return array
     */
    function processEvent($event_name, $param1=null , $param2=null)
    {
        $eResults = array();
        $eHandlers = $this->getEventHandlers($event_name);
        foreach($eHandlers as $handler)
        {
            $eResults[] = $this->executeEventHandler($handler, $param1, $param2);
        }
        return $eResults;
    }



    /**
     *                                           .
     *
     *                  :                   TRUE,             ,
     *                  $handler_class,      FALSE -          .
     *
     *                                                     ,
     *                                                                  .
     *
     *                                       :
     *  -            $handler_class                      ,                 TRUE
     *  -         ,                                        $handler_include_path
     *  -                    ,                                  $handler_include_path
     *  -                                          ,          ,                 TRUE
     *  -         ,               ModulesManager
     *    API        $handler_class
     *  -                                          ,          ,                 TRUE,
     *          FALSE
     *
     * @param string $handler_class                ,
     * @param string $handler_include_path             ,
     * @return boolean True if $handler_class exists or FALSE
     */
    function _tryInclude($handler_class, $handler_include_path)
    {
        if (class_exists($handler_class))
            return true;

        if (file_exists($handler_include_path) && is_file($handler_include_path) && is_readable($handler_include_path))
        {
            _use($handler_include_path);
            if (class_exists($handler_class))
                return true;
        }

        global $application;
        $objMM = &$application->getInstance('Modules_Manager');
        $objMM->includeAPIFileOnce($handler_class);

        if (class_exists($handler_class))
            return true;
        else
            return false;
    }

    function install()
    {
        $tables = EventsManager::getTables();
        $query = new DB_Table_Create($tables);
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tbl_events = 'events_manager';
        $tables[$tbl_events] = array();
        $tables[$tbl_events]['columns'] = array
            (
                'event_id'             => $tbl_events.'.event_id',
                'event_name'           => $tbl_events.'.event_name',
                'handler_class'        => $tbl_events.'.handler_class',
                'handler_method'       => $tbl_events.'.handler_method',
                'handler_include_path' => $tbl_events.'.handler_include_path',
                'handler_order'        => $tbl_events.'.handler_order',
            );
        $tables[$tbl_events]['types'] = array
            (
                'event_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment',
                'event_name'           => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\'',
                'handler_class'        => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\'',
                'handler_method'       => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\'',
                'handler_include_path' => DBQUERY_FIELD_TYPE_TEXT,
                'handler_order'        => DBQUERY_FIELD_TYPE_INT . ' default 100',
            );
        $tables[$tbl_events]['primary'] = array
            (
                'event_id'
            );
        $tables[$tbl_events]['indexes'] = array
            (
                'IDX_handler_order' => 'handler_order',
                'UNIQUE KEY UNQ_event_handler' => 'event_name(75),handler_class(75),handler_method(75)'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(EventsManager::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    function getCurrentEventName()
    {
        if (is_array($this->__events_stack) && !empty($this->__events_stack))
        {
            return $this->__events_stack[count($this->__events_stack) - 1];
        }
        else
        {
            return null;
        }
    }

    function __pushEventName($e)
    {
        array_push($this->__events_stack, $e);
    }

    function __popEventName()
    {
        if (is_array($this->__events_stack) && !empty($this->__events_stack))
        {
            return array_pop($this->__events_stack);
        }
    }

    var $__events_stack = array();
}

?>