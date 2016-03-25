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

class SELECT_EVENT_HANDLER extends DB_select
{
    function initQuery($params)
    {
        $tables = EventsManager::getTables();
        $tbl_events = $tables['events_manager']['columns'];

        $event_name = $params['event_name'];
        $handler_class = $params['handler_class'];
        $handler_method = $params['handler_method'];

        $this->addSelectField($tbl_events['event_id'], 'event_id');
        $this->addSelectField($tbl_events['handler_class'], 'handler_class');
        $this->addSelectField($tbl_events['handler_method'], 'handler_method');
        $this->addSelectField($tbl_events['handler_include_path'], 'handler_include_path');
        $this->addSelectField($tbl_events['event_name'], 'event_name');

        $this->WhereValue($tbl_events['event_name'], DB_EQ, $event_name);

        if ($handler_class != null)
        {
            $this->WhereAND();
            $this->WhereValue($tbl_events['handler_class'], DB_EQ, $handler_class);
        }

        if ($handler_method != null)
        {
            $this->WhereAND();
            $this->WhereValue($tbl_events['handler_method'], DB_EQ, $handler_method);
        }

        $this->SelectOrder($tbl_events['handler_order']);
    }
}

?>