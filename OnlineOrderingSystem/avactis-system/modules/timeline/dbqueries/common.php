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

class SELECT_TIMELINE_HEADERS extends DB_Select
{
    function initQuery($params)
    {
        $index_words_list = $params['index_words_list'];
        $types_list = $params['types'];

        $tables = Timeline::getTables();
        $c = $tables['timeline']['columns'];

        $this->addSelectField($c['id'],         'id');
        $this->addSelectField($c['type'],       'type');
        $this->addSelectField($c['header'],     'header');
        $this->addSelectField($c['datetime'],   'datetime');

        //  -                            ?
        $this->addSelectField('IF(body is NULL, 0, 1)', 'is_body_empty');

        if ($index_words_list !== null and is_array($index_words_list))
        {
            $this->addWhereOpenSection();
            for($i=0; $i<count($index_words_list); $i++)
            {
                $this->WhereValue($c['header'], DB_LIKE, '%'.$index_words_list[$i].'%');
                if ($i < count($index_words_list)-1)
                {
                    $this->WhereOr();
                }
            }
            $this->addWhereCloseSection();

            $this->WhereAND();
        }

        if ($types_list !== null and is_array($types_list))
        {
            $this->WhereField($c['type'], DB_IN, ' ('.implode(',',array_map(array($this, '__addQuotes'),$types_list)).') ');
        }

        $this->SelectOrder($c['datetime'], 'DESC');
        $this->SelectOrder($c['id'], 'DESC');

        $paginator_limits = $params['paginator'];
        if ($paginator_limits !== null && is_array($paginator_limits) === true)
        {
            list($offset,$count) = $paginator_limits;
            $this->SelectLimit($offset,$count);
        }
    }

    function __addQuotes($value)
    {
        return "'".$value."'";
    }
}

class SELECT_TIMELINE_TYPES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Timeline::getTables();
        $c = $tables['timeline']['columns'];

        $this->addSelectField($c['type'], 'types');
        $this->WhereValue($c['type'], DB_NEQ, getMsg('TL', 'TL_CATTREE_TITLE'));
        $this->SelectOrder($c['type']);
        $this->SelectGroup($c['type']);
    }
}

class SELECT_TIMELINE_ITEM_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Timeline::getTables();
        $c = $tables['timeline']['columns'];

        $this->addSelectField($c['id'],         'id');
        $this->addSelectField($c['type'],       'type');
        $this->addSelectField($c['header'],     'header');
        $this->addSelectField($c['datetime'],   'datetime');
        $this->addSelectField($c['body'],       'body');

        $this->WhereValue($c['id'], DB_EQ, $params['id']);
    }
}



class INSERT_TIMELINE_ITEM extends DB_Insert
{
    function INSERT_TIMELINE_ITEM()
    {
        parent::DB_Insert('timeline');
    }

    function initQuery($params)
    {
        $tables = Timeline::getTables();
        $columns = $tables['timeline']['columns'];

        $this->addInsertValue($params['datetime'],  $columns['datetime']);
        $this->addInsertValue($params['type'],      $columns['type']);
        $this->addInsertValue($params['header'],    $columns['header']);
        if ($params['body'] !== null)
        {
            $this->addInsertValue($params['body'],      $columns['body']);
        }
    }
}

class SELECT_COUNT_TIMELINE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Timeline::getTables();
        $this->addSelectField($tables['timeline']['columns']['id'], 'count');
    }
}

class DELETE_TIMELINE extends DB_Delete
{
    function DELETE_TIMELINE()
    {
        parent :: DB_Delete('timeline');
    }

    function initQuery($params)
    {
    	$tables = Timeline::getTables();
        $o = $tables['timeline']['columns'];
	if($params['log_type']!='All')
	$this -> WhereField($o['type'] , DB_EQ , "'".$params['log_type']."'");
    }
}

?>