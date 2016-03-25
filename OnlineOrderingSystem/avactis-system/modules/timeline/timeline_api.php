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
 * Module Timeline
 *
 * @package Timeline
 * @author Alexey Florinsky
 */
class Timeline
{
    function Timeline()
    {
    }

    function install()
    {
        $tables = Timeline::getTables();
        $query = new DB_Table_Create($tables);

        modApiFunc('EventsManager','addEventHandler','ApplicationStarted','Timeline','onApplicationStarted');

        $param_info = array(
                         'GROUP_NAME'        => 'TIMELINE',
                         'PARAM_NAME'        => 'LOG_STOREFRONT_ACCESS',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_STOREFRONT_ACCESS_NAME'),
                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_STOREFRONT_ACCESS_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_STOREFRONT_ACCESS_NO'),
                                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_STOREFRONT_ACCESS_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_STOREFRONT_ACCESS_YES'),
                                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_STOREFRONT_ACCESS_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => 'TIMELINE',
                         'PARAM_NAME'        => 'LOG_BACKEND_ACCESS',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_BACKEND_ACCESS_NAME'),
                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_BACKEND_ACCESS_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_BACKEND_ACCESS_NO'),
                                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_BACKEND_ACCESS_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_BACKEND_ACCESS_YES'),
                                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_BACKEND_ACCESS_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings','createParam', $param_info);
/*
        $param_info = array(
                         'GROUP_NAME'        => 'TIMELINE',
                         'PARAM_NAME'        => 'LOG_CATEGORY_TREE_CHANGES',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_CATEGORY_TREE_CHANGES_NAME'),
                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_CATEGORY_TREE_CHANGES_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_CATEGORY_TREE_CHANGES_NO'),
                                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_CATEGORY_TREE_CHANGES_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TL', 'ADV_CFG_LOG_CATEGORY_TREE_CHANGES_YES'),
                                                                       'DESCRIPTION' => array('TL', 'ADV_CFG_LOG_CATEGORY_TREE_CHANGES_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings','createParam', $param_info);
*/
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tbl = 'timeline';
        $tables[$tbl] = array();
        $tables[$tbl]['columns'] = array
            (
                'id'            => $tbl.'.id',
                'datetime'      => $tbl.'.datetime',
                'type'          => $tbl.'.type',
                'header'        => $tbl.'.header',
                'body'          => $tbl.'.body',
            );
        $tables[$tbl]['types'] = array
            (
                'id'            => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment',
                'datetime'      => DBQUERY_FIELD_TYPE_DATETIME,
                'type'          => DBQUERY_FIELD_TYPE_CHAR200,
                'header'        => DBQUERY_FIELD_TYPE_LONGTEXT,
                'body'          => DBQUERY_FIELD_TYPE_BLOB,
            );
        $tables[$tbl]['primary'] = array
            (
                'id'
            );
        $tables[$tbl]['indexes'] = array
            (
                'IDX_type' => 'type',
                'IDX_datetime'  => 'datetime',
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function onApplicationStarted()
    {
        global $zone;
        if ($zone == 'AdminZone')
        {
            if (modApiFunc('Settings','getParamValue','TIMELINE','LOG_BACKEND_ACCESS') === 'NO')
            {
                return;
            }
            $type = getMsg('TL','TL_AZ_ACCESS');
        }
        else
        {
            if (modApiFunc('Settings','getParamValue','TIMELINE','LOG_STOREFRONT_ACCESS') === 'NO')
            {
                return;
            }
            $type = getMsg('TL','TL_CZ_ACCESS');
        }

        $header = str_replace(
                                array('{IP}','{URL}'),
                                array(getVisitorIP(), _ml_htmlentities(str_replace("&","\t&",getCurrentURL()))),
                                getMsg('TL','TL_VISITOR_HEADER')
                             );

        $body  = prepareArrayDisplay($_GET,'$_GET');
        $body .= prepareArrayDisplay($_POST,'$_POST');
        $body .= prepareArrayDisplay($_SESSION,'$_SESSION');

        $this->addLog($type, $header, $body);
    }

    function addLog($type, $header, $body)
    {
        global $zone;
        if ($body == null or empty($body))
        {
            $body = null;
        }
        else
        {
            $body = gzdeflate($body,3);
        }
        $params = array(
            'datetime'  => date('Y-m-d H:i:s', time()),
            'type'      => $type,
            'header'    => $header,
            'body'      => $body,
        );
        execQuery('INSERT_TIMELINE_ITEM', $params);
    }

    function getTimelineRecsCount()
    {
        return count(execQuery('SELECT_COUNT_TIMELINE', array()));
    }

    function clearTimeline($log_clear_type)
    {
	$sys_log_clear_param = array();
       	$sys_log_clear_param['log_type'] = $log_clear_type;
       	execQuery('DELETE_TIMELINE',$sys_log_clear_param);
    }

    function getTimelineHeaders()
    {
        modAPIFunc('paginator', 'setCurrentPaginatorName', "TimelinePaginator");

        $text_filter_data = modApiFunc('Timeline','getFilterByText');
        if ($text_filter_data != null and isset($text_filter_data[0]) and isset($text_filter_data[1]) and is_array($text_filter_data[1]))
        {
            list($user_input, $index_words_list) = $text_filter_data;
        }
        else
        {
            $index_words_list = null;
        }
        $params = array(
            'index_words_list' => $index_words_list,
            'types' => $this->getFilterByTypes(),
            'paginator' => null,
        );
        $params['paginator'] = execQueryPaginator('SELECT_TIMELINE_HEADERS', $params);
        return execQuery('SELECT_TIMELINE_HEADERS', $params);
    }

    function getTimelineTypes()
    {
        return execQuery('SELECT_TIMELINE_TYPES', array());
    }

    function setFilterByTypes($types_list)
    {
        if (Validator::isValidArray($types_list))
        {
            $real_types_list = $this->getTimelineTypes();
            $real_types_plain_list = array();
            foreach ($real_types_list as $item)
            {
                $real_types_plain_list[] = $item['types'];
            }
            $real_types_plain_list[] = getMsg('TL', 'TL_CATTREE_TITLE');

            $user_selected_types = array_intersect($real_types_plain_list, $types_list);
            if (!empty($user_selected_types))
            {
                modApiFunc('Session','set','TimelineTypesFilter', $user_selected_types);
            }
        }
    }

    function getFilterByTypes()
    {
        if(modApiFunc('Session', 'is_Set', 'TimelineTypesFilter'))
        {
            return modApiFunc('Session', 'get', 'TimelineTypesFilter');
        }
        else
        {
            return array(getMsg('NTFCTN','NTFCTN_TL_TYPE'), getMsg('CHCKT', 'TL_ORDER_CREATED_TYPE') ); // hard-coded default
        }
    }

    function setFilterByText($text)
    {
        if (Validator::isValidStringMinLength($text, 2))
        {
            $user_input = $text;
            $index_words_list = getIndexWordsFromText($text);
            modApiFunc('Session','set','TimelineTextFilter', array($user_input, $index_words_list));
        }
        else
        {
            modApiFunc('Session','un_set','TimelineTextFilter');
        }
    }

    function getFilterByText()
    {
        if(modApiFunc('Session', 'is_Set', 'TimelineTextFilter'))
        {
            return modApiFunc('Session', 'get', 'TimelineTextFilter');
        }
        else
        {
            return null;
        }
    }

    function getTimelineItemById($item_id)
    {
        $params = array('id'=>$item_id);
        $r = execQuery('SELECT_TIMELINE_ITEM_BY_ID', $params);
        if (isset($r[0]))
        {
            return $r[0];
        }
        else
        {
            return null;
        }
    }

    function addCatTreeLog(&$before, &$after, $query)
    {
        global $application;

        $_type = getMsg('TL', 'TL_CATTREE_TITLE');
        $_body = '';

        if (!empty($before))
        {
            $tmp = '';
            foreach($before as $k => $v)
                $tmp .= str_replace(
                            array('{ID}',
                                  '{LEFT}',
                                  '{RIGHT}',
                                  '{LEVEL}',
                                  '{BGCOLOR}'),
                            array($v['catid'],
                                  $v['catleft'],
                                  $v['catright'],
                                  $v['catlevel'],
                                  (($k % 2) ? '#BBBBBB' : 'white')),
                            getMsg('TL', 'TL_CATTREE_BODY_TABLE_TR')
                        );

            $_body .= str_replace(
                          array('{TREE_NAME}',
                                '{LIST}'),
                          array(getMsg('TL', 'TL_CATTREE_TREE_BEFORE'),
                                $tmp),
                          getMsg('TL', 'TL_CATTREE_BODY_TABLE')
                      );
            $_status_before = getMsg('TL', 'TL_CATTREE_ERROR');
            $_sbcolor = 'red';
        }
        else
        {
            $_status_before = getMsg('TL', 'TL_CATTREE_OK');
            $_sbcolor = 'green';
        }

        if (!empty($after))
        {
            $tmp = '';
            foreach($after as $k => $v)
                $tmp .= str_replace(
                            array('{ID}',
                                  '{LEFT}',
                                  '{RIGHT}',
                                  '{LEVEL}',
                                  '{BGCOLOR}'),
                            array($v['catid'],
                                  $v['catleft'],
                                  $v['catright'],
                                  $v['catlevel'],
                                  (($k % 2) ? '#BBBBBB' : 'white')),
                            getMsg('TL', 'TL_CATTREE_BODY_TABLE_TR')
                        );

            $_body .= str_replace(
                          array('{TREE_NAME}',
                                '{LIST}'),
                          array(getMsg('TL', 'TL_CATTREE_TREE_AFTER'),
                                $tmp),
                          getMsg('TL', 'TL_CATTREE_BODY_TABLE')
                      );
            $_status_after = getMsg('TL', 'TL_CATTREE_ERROR');
            $_sacolor = 'red';
        }
        else
        {
            $_status_after = getMsg('TL', 'TL_CATTREE_OK');
            $_sacolor = 'green';
        }

        $_header = str_replace(
                       array('{REQUEST_URL}',
                             '{STATUS_BEFORE_COLOR}',
                             '{STATUS_BEFORE}',
                             '{QUERY}',
                             '{STATUS_AFTER_COLOR}',
                             '{STATUS_AFTER}'),
                       array(getCurrentURL(),
                             $_sbcolor,
                             $_status_before,
                             $query,
                             $_sacolor,
                             $_status_after),
                       getMsg('TL', 'TL_CATTREE')
                   );

        if ($_body == null or empty($_body))
        {
            $_body = null;
        }
        else
        {
            $_body = gzdeflate($_body, 3);
        }

        $prefix = $application -> getAppIni('DB_TABLE_PREFIX');
        $t = $this -> getTables();
        $t = $t['timeline']['columns'];

        $_query = 'INSERT INTO ' . $prefix . 'timeline (' .
                 $t['datetime'] . ', ' . $t['type'] . ', ' .
                 $t['header'] . ', ' . $t['body'] . ') VALUES (\'' .
                 date('Y-m-d H:i:s', time()) . '\', \'' .
                 addslashes($_type) . '\', \'' .
                 addslashes($_header) . '\', \'' .
                 addslashes($_body) . '\')';

        clearQueriesCache(array($prefix . 'timeline'));
        $application -> db -> DB_Query($_query, 'db_link', false);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Timeline::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

}

?>