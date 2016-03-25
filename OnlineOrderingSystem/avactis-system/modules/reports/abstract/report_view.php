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
 * CReportView class
 *
 * Base class for all report views.
 *
 * @author Alexey Florinsky
 * @version $Id: report_view.php 8118 2010-03-12 10:41:23Z melkor $
 * @package Reports
 */
class CReportView
{
    function CReportView()
    {
        loadClass('CProductInfo');
    }

    function init()
    {
        loadClass($this->__source_class_name);
        loadClass($this->__render_settings_class_name);
        loadClass($this->__render_class_name);

        $source_class = $this->__source_class_name;
        $this->__source = new $source_class();
        $this->initSource();

        $settings_class = $this->__render_settings_class_name;
        $this->__render_settings = new $settings_class();
        $this->__render_settings->setReportID(get_class($this));

        $render_class = $this->__render_class_name;
        $this->__render = new $render_class();
    }

    function initSource()
    {
        $this->applyReportDatetimePeriod();
    }

    function applyReportDatetimePeriod()
    {
        if ($this->isDatetimePeriodSelectorApplicable() === true)
        {
            $period = modApiFunc('Reports','getReportPeriodTimestamps',get_class($this));
            if ($period !== null)
            {
                list($from, $to) = $period;
                $from = toMySQLDatetime($from);
                $to = toMySQLDatetime($to);
                $this->__source->setDatetimePeriod($from, $to);
            }
        }
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return true;
    }

    function isExportToExcelApplicable()
    {
        return true;
    }

    function isRefreshApplicable()
    {
        return false;
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function prepareData()
    {
        $this->__source->run();
        $render_data = array();
        $counter = 0;
        while ($row = $this->__source->fetchRecord())
        {
            $row['row_number'] = '&nbsp;'.++$counter.'.';
            $row = $this->__formatRow($row);
            $render_data[] = $row;
        }
        $this->__render_settings->setReportData($render_data);
    }

    function getReportName() {}

    function setColumns() {}

    function setColumnStyles() {}

    function output($flag = REPORT_OUTPUT_AJAX_LOADER, $report_placeholder_width = 450)
    {
        $this->init();
        $this->__render_settings->setReportPlaceholderWidth($report_placeholder_width);
        $this->__render_settings->setTitle($this->getReportName());

        if ($flag == REPORT_OUTPUT_AJAX_LOADER)
        {
            return $this->getReportAjaxLoader();
        }
        elseif ($flag == REPORT_OUTPUT_SIMPLE_AJAX_LOADER)
        {
            return $this->getReportSimpleAjaxLoader();
        }
        else
        {
            return $this->getReportContent();
        }
    }

    function getReportAjaxLoader()
    {
        //
        $TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-ajax-loader/');
        $container_tag_values = array(
                                        'ReportID' => $this->__render_settings->getReportID(),
                                        'ReportName' => $this->getReportName(),
                                        'Footer' => $this->getFooter(),
                                        'DatetimePeriodSelector' => $this->getDatetimePeriodSelectorHtml(),
                                     );
        return $TmplFiller->fill("", "container-ajax-loader.tpl.html", $container_tag_values);
    }

    function getReportSimpleAjaxLoader()
    {
        //
        $TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-simple-ajax-loader/');
        $container_tag_values = array(
                                        'ReportID' => $this->__render_settings->getReportID(),
                                        'ReportName' => $this->getReportName(),
                                        'Footer' => $this->getFooter(),
                                        'DatetimePeriodSelector' => $this->getDatetimePeriodSelectorHtml(),
                                     );
        return $TmplFiller->fill("", "container-ajax-loader.tpl.html", $container_tag_values);
    }

    function getDatetimePeriodSelectorHtml()
    {
        if ($this->isDatetimePeriodSelectorApplicable() === true)
        {
            return getDatetimePeriodSelector(
                                            array(  'ID' => $this->__render_settings->getReportID(),
                                                    'CALLBACK_JS_FUNCTION' => $this->__render_settings->getReportID().'_reportPeriodChanged',
                                                    'CURRENT_VALUE' => modApiFunc('Reports','getReportPeriodLabel', get_class($this)),
                                                    'MIN_DISCONTINUITY' => $this->getDatetimePeriodSelectorMinDiscontinuity(),
                                            )
                                         );
        }
        else
        {
            return '';
        }
    }

    function getFooter()
    {
        $html = '';
        if ($this->isExportToExcelApplicable() == true)
        {
            $html .= $this->__getExportButtonHtml();
        }
        if ($this->isRefreshApplicable() == true)
        {
            $html .= $this->__getRefreshButtonHtml();
        }
        if ($html == '')
        {
            $html .= "&nbsp;";
        }
        return $html;
    }

    function __getExportButtonHtml()
    {
        $filler = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-ajax-loader-buttons/');
        $href = 'reports.php?asc_action=getReportContent&type=Excel&reportName='.$this->__render_settings->getReportID();
        $name = getMsg('RPTS','EXPORT_TO_EXCEL_LINK_NAME');
        $title = getMsg('RPTS','EXPORT_TO_EXCEL_LINK_HINT');
        $html_link = $filler->fill('','link-with-image.tpl.html', array('Href'=>$href, 'Name'=>$name, 'Title'=>$title, 'IMG_SRC'=>'images/Excel-16.gif'));
        $html_button = $filler->fill('','button-container.tpl.html', array('Content'=>$html_link));
        return $html_button;
    }

    function __getRefreshButtonHtml()
    {
        $filler = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-ajax-loader-buttons/');
        $href = 'javascript:'.$this->__render_settings->getReportID().'_getReportContent();';
        $name = 'Refresh';
        $title = 'Update report';
        $html_link = $filler->fill('','link.tpl.html', array('Href'=>$href, 'Name'=>$name, 'Title'=>$title));
        $html_button = $filler->fill('','button-container.tpl.html', array('Content'=>$html_link));
        return $html_button;
    }

    function getReportContent()
    {
        global $application;

        $this->prepareData();
        $this->setColumns();
        $this->setColumnStyles();
        $content = $this->__render->output($this->__render_settings);
        return $content;
    }

    function getProductInfoLink($pid, $pname)
    {
        global $application;

        $product = &$application->getInstance('CProductInfo', $pid);
        if ($product->isProductIdCorrect() == true)
        {
            return '<a href="'.$product->getProductTagValue('InfoLink').'" target="_blank"><b>'.$pname.'</b></a>';
        }
        else
        {
            return $pname;
        }
    }

    function __getBar($percent)
    {
		$bar='<div class="progress">';
        $bar.='<div class="progress-bar progress-bar-striped col-md-12"  role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%">';
    	$bar.='<span class="progresslabel">'.$percent.'%</span>';
  		$bar.='</div></div>';
        return $bar;
    }

    function __prepareLongString($str, $len = 40, $add_hint = true)
    {
        $cut_str = str_rev_pad($str, $len);
        if ($add_hint and $cut_str != $str)
        {
            //              ,
            $cut_str = $this->__getHint($cut_str, $str);
        }
        return $cut_str;
    }

    function __getHint($str, $hint)
    {
        return '<span onmouseover="return overlib(\''.htmlspecialchars($hint).'\');" onmouseout="return nd();">'.$str.'</span>';
    }

    function __prepareLongUrl($url, $len = 40, $style = '', $add_hint = true)
    {
        $short_url = $this->__prepareLongString($url, $len, false);
        $html = '<A target="_blank" href="'.$url.'" style="text-decoration: none; '.$style.'">'.$short_url.'</A>';
        if ($add_hint and $short_url !== $url)
        {
            $html = $this->__getHint($html, $url);
        }
        return $html;
    }

    function __formatRow($row)
    {
        return $row;
    }

    var $__render_class_name = null;
    var $__source_class_name = null;
    var $__render_settings_class_name = null;
    var $__render = null;
    var $__source = null;
    var $__render_settings = null;
}

?>