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

loadModuleFile('reports/abstract/report_view.php');

class ReportSeanceClickPath extends CReportView
{
    function ReportSeanceClickPath()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';

        $type = modApiFunc('Request','getValueByKey','type');
        if ($type == "robot")
        {
        	$this->__source_class_name = 'CScannedPagesByCrawler';
        }
        else
        {
            $this->__source_class_name = 'CSeanceClickPathBySeanceId';
        }
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        $type = modApiFunc('Request','getValueByKey','type');
        if ($type == "robot")
        {
        	return getMsg('RPTS','REPORT_CRAWLER_SCANNED_PAGES');
        }
        else
        {
    	    return getMsg('RPTS','REPORT_SEANCE_CLICK_PATH');
        }
    }

    function initSource()
    {
        $type = modApiFunc('Request','getValueByKey','type');
        if ($type == "robot")
        {
        	$id = modApiFunc('Request','getValueByKey','id');
        	//$date = modApiFunc('Request','getValueByKey','date');
        	$this->__source->setParams('id', $id);
        	//$this->__source->setParams('date', $date);
        }
        else
        {
    	    parent::initSource();

            $seance_id = modApiFunc('Request','getValueByKey','sid');
            if (Validator::isValidInt($seance_id) == true)
            {
                $this->__source->setParams('seance_ids', array($seance_id));
            }
        }
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return false;
    }

    function setColumns()
    {
        $this->__render_settings->setColumnHeaders( array(
                                                    'row_number' => getMsg('RPTS','CLICK_NUMBER'),
                                                    'page_url' => getMsg('RPTS','URL_VISITED'),
                                                    'visit_time' => getMsg('RPTS','DURATION_OF_STAY'),
                ));
        $this->__render_settings->setColumnList( array(
                                                    'row_number',
                                                    'page_url',
                                                    'visit_time',
        ));
    }

    function prepareData()
    {
        $this->__source->run();
        $render_data = array();
        $counter = 0;
        while ($row = $this->__source->fetchRecord())
        {
            $row['row_number'] = '&nbsp;'.++$counter.'.';
            $render_data[] = $row;
        }

        for ($i=0; $i<count($render_data); $i++)
        {
            $render_data[$i]['next_visit_time'] = isset($render_data[$i+1]['visit_time']) ? $render_data[$i+1]['visit_time'] : null;
            $render_data[$i] = $this->__formatRow($render_data[$i]);
        }

        $this->__render_settings->setReportData($render_data);
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['page_url'] = $this->__prepareLongUrl($row['page_url'], 80, 'font-weight: bold; color: #666666;');
                if ($row['next_visit_time'] == null)
                {
                    $row['visit_time'] = getMsg('RPTS','NO_ACTIVITY');
                }
                else
                {
                    $duration = (strtotime($row['next_visit_time']) - strtotime($row['visit_time']));
                    $row['visit_time'] = modApiFunc("Localization", "formatTimeDuration", $duration);
                }

                break;

            default: // simple html table, binary excel or chart
                // Let's stay as is.
                break;

        }
        return $row;
    }
}

?>