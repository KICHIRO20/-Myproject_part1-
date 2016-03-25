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
loadModuleFile('reports/reports_api.php');
loadModuleFile('reports/abstract/report_data_collector.php');

/**
 * CCartsStatisticCollector class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class CCartsStatisticCollector extends CReportDataCollector
{
    function CCartsStatisticCollector()
    {
    }

    function addRecord($qty = 1)
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        $params = array(
            'datetime'                  => date('Y-m-d H:00:00', $this->getTimestamp()),
            'carts_created_qty'         => $qty,
        );

        $record = execQuery('SELECT_CARTS_STAT_RECORD_BY_PK', $params);
        if (!empty($record) and isset($record[0]))
        {
            $record = $record[0];
            $params['carts_created_qty'] += $record['carts_created_qty'];
        }

        execQuery('REPLACE_CARTS_STAT_RECORD', $params);
    }

    function onCartCreated()
    {
        $this->addRecord();
    }

}


?>