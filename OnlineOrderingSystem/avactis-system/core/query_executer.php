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


class CQueryExecuter
{
    var $cache = null;


    function CQueryExecuter()
    {
        global $application;
        $this->cache = CCacheFactory::getCache('database');
    }

    function getQueryCount($query_name, $params)
    {
        return $this->exec($query_name, $params, '-count-', true);
    }

    function __getCacheKey($query_name, $params, $cache_id_prefix, $b_count_only)
    {
        $params[] = modApiFunc('MultiLang', 'getLanguage');
        return $query_name.'-'.md5('exec-query-'.$cache_id_prefix.(($b_count_only === true) ? 'TRUE' : 'FALSE').serialize($params));
    }

    function exec($query_name, $params, $cache_id_prefix='', $b_count_only = false, $to_reset = CCACHE_USE_MEMORY_CACHE)
    {
        global $application;

        CProfiler::DBLayerStart($query_name);

        $cache_query_id = $this->__getCacheKey($query_name, $params, $cache_id_prefix, $b_count_only);
        $cache_query_result = $this->cache->read($cache_query_id);

        CProfiler::DBQueryStatistics($query_name, $params, $cache_query_id, $cache_query_result !== NULL);

        if ($cache_query_result !== NULL)
        {
            //CTrace::dbg(sprintf('Found: %s', $cache_query_id));
            CProfiler::DBLayerStop($query_name);
            CProfiler::DBCacheQueryRead();
            return $cache_query_result;
        }
        else
        {
            //CTrace::dbg(sprintf('Missing: %s', $cache_query_id));
            // Create query object
            if (!class_exists($query_name))
            {
                _fatal(__FUNCTION__.': Class does not exist: '.$query_name);
            }

            $query_obj = new $query_name();
            $query_obj->initQuery($params);

            $query_type = $query_obj->getQueryType();
            if ($query_type == DBQUERY_TYPE_SELECT)
            {
                $table_list_of_query = $this->__getTablesOfQuery($query_obj);
                $application->enterCriticalSection('database');
            }

            CProfiler::DBLayerStop($query_name);
            if($b_count_only === true)
            {
                $cache_query_result = $application->db->getDB_Result_num_rows($query_obj);
            }
            else
            {
                $cache_query_result = $application->db->getDB_Result($query_obj);
            }
            CProfiler::DBLayerStart($query_name);

            // If it is SELECT query then save all related tables
            if ($query_type == DBQUERY_TYPE_SELECT && $query_obj->isCachable() == true)
            {
                #         -                          ,                  ,                                        
                #                        ,                                                            
                #                                                      
                foreach ($table_list_of_query as $table) {
                    $this->cache->add($table, uniqid('table',true));
                }
                $this->cache->write($cache_query_id, $cache_query_result, 0, $table_list_of_query);
                CProfiler::DBCacheQuerySaved();

            }

            if ($query_type == DBQUERY_TYPE_SELECT)
            {
                $application->leaveCriticalSection();
            }

            CProfiler::DBLayerStop($query_name);
            return $cache_query_result;
        }
    }

    function __getTablesOfQuery(&$query_obj)
    {
        $table_list_of_query = array();

        //
        $table_list = $query_obj->getSelectTables();
        foreach ($table_list as $table_name=>$table_alias)
        {
            $table_list_of_query[] = $table_name;
        }

        //
        $table_list = $query_obj->getJoinTables();
        foreach ($table_list as $table_info)
        {
            /*
             *       getJoinTables                                                         .
             *                                                     .
             */
            $table = $table_info['TABLE'];
            if ( ($pos = _ml_strpos($table, ' ')) !== false)
            {
                $table = _ml_substr($table, 0, $pos);
            }

            $table_list_of_query[] = $table;
        }

        //
        return array_unique(array_filter($table_list_of_query));
    }

    function clearCache($table_list)
    {
        global $application;
        CProfiler::DBLayerStart();

        $application->enterCriticalSection('database');
        foreach ($table_list as $table_name)
        {
            $this->cache->write($table_name, uniqid('',true)); //  time()                    ,                                         
        }
        $application->leaveCriticalSection();

        CProfiler::DBLayerStop();
    }

}


