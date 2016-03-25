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

/*abstract*/class ReportTop10Products extends CReportView
{
    function ReportTop10Products()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'CProductTop10SellersByItems';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP10_SELLERS_BY_ITEMS');
    }

    function setColumns($field_key, $field_name)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $this->__render_settings->setColumnList( array(
                                                            'row_number',
                                                            'product_name',
                                                            $field_key,
                                                            'bar',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'row_number'    => getMsg('RPTS','COLUMN_NUMBER'),
                                                            'product_name'  => getMsg('RPTS','COLUMN_PRODUCT_NAME'),
                                                            $field_key => $field_name,
                ));
                break;

            default: // simple html table, binary excel or chart
                $this->__render_settings->setColumnList( array(
                                                            'row_number',
                                                            'product_name',
                                                            $field_key,
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'row_number'    => getMsg('RPTS','COLUMN_NUMBER'),
                                                            'product_name'  => getMsg('RPTS','COLUMN_PRODUCT_NAME'),
                                                            $field_key => $field_name,
                ));
                break;
        }
    }

    function setColumnStyles()
    {
        $this->__render_settings->setColumnStyles(array(
                                                    'row_number'    => 'font-weight: bold',
                                                    'product_name'  => 'font-weight: bold;',
        ));
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['bar'] = $this->__getBar($row['percent_pixel']);

                if (isset($row['items_sold']))
                {
                    $row['items_sold'] = modApiFunc("Localization", "num_format", $row['items_sold']);
                }

                if (isset($row['product_views']))
                {
                    $row['product_views'] = modApiFunc("Localization", "num_format", $row['product_views']);
                }

                if (isset($row['product_added_to_cart_times']))
                {
                    $row['product_added_to_cart_times'] = modApiFunc("Localization", "num_format", $row['product_added_to_cart_times']);
                }

                if (isset($row['product_deleted_from_cart_times']))
                {
                    $row['product_deleted_from_cart_times'] = modApiFunc("Localization", "num_format", $row['product_deleted_from_cart_times']);
                }

                if (isset($row['product_added_to_cart_qty']))
                {
                    $row['product_added_to_cart_qty'] = modApiFunc("Localization", "num_format", $row['product_added_to_cart_qty']);
                }

                if (isset($row['product_deleted_from_cart_qty']))
                {
                    $row['product_deleted_from_cart_qty'] = modApiFunc("Localization", "num_format", $row['product_deleted_from_cart_qty']);
                }

                if (isset($row['product_name']))
                {
                    $row['product_name'] = $this->getProductInfoLink($row['product_id'], $row['product_name']);
                }
                break;
        }

        return $row;
    }
}

class ReportTop10SellersByItems extends ReportTop10Products
{
    function ReportTop10SellersByItems()
    {
    	parent::ReportTop10Products();
        $this->__source_class_name = 'CProductTop10SellersByItemsFiltered';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP10_SELLERS_BY_ITEMS');
    }

    function setColumns()
    {
        parent::setColumns('items_sold', getMsg('RPTS','COLUMN_ITEMS_SOLD'));
    }
}

class ReportTop10SellersByItemsLast30Days extends ReportTop10SellersByItems
{
    function ReportTop10SellersByItemsLast30Days()
    {
        parent::ReportTop10SellersByItems();
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP10_SELLERS_BY_ITEMS_LAST_30_DAYS');
    }

    function isExportToExcelApplicable()
    {
        return false;
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return false;
    }

    function setColumns()
    {
        $this->__render_settings->setColumnList( array(
                                                    'row_number',
                                                    'product_name',
                                                    'items_sold',
        ));

        $this->__render_settings->setColumnHeaders( array(
                                                    'row_number'    => getMsg('RPTS','COLUMN_NUMBER'),
                                                    'product_name'  => getMsg('RPTS','COLUMN_PRODUCT_NAME'),
                                                    'items_sold' => getMsg('RPTS','COLUMN_ITEMS_SOLD')
        ));
    }

    function initSource()
    {
        parent::initSource();
        $period = modApiFunc('Reports','getTimestampPeriodByDatetimeLabel',DATETIME_PERIOD_DAY_LAST_30);
        if ($period !== null)
        {
            list($from, $to) = $period;
            $from = toMySQLDatetime($from);
            $to = toMySQLDatetime($to);
            $this->__source->setDatetimePeriod($from, $to);
        }
    }
}

class ReportTop10ByViews extends ReportTop10Products
{
    function ReportTop10ByViews()
    {
        parent::ReportTop10Products();
        $this->__source_class_name = 'CProductTop10Viewed';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP_10_BY_VIEWS');
    }

    function setColumns()
    {
        parent::setColumns('product_views', getMsg('RPTS','COLUMN_VIEWS'));
    }

}

class ReportTop10AddedToCartTimes extends ReportTop10Products
{
    function ReportTop10AddedToCartTimes()
    {
        parent::ReportTop10Products();
        $this->__source_class_name = 'CProductTop10AddedToCartTimes';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP_10_PRODUCTS_ADDED_TO_CART_BY_TIMES');
    }

    function setColumns()
    {
        parent::setColumns('product_added_to_cart_times', getMsg('RPTS','COLUMN_ADDED_TO_CART_TIMES'));
    }
}

class ReportTop10DeletedFromCartTimes extends ReportTop10Products
{
    function ReportTop10DeletedFromCartTimes()
    {
        parent::ReportTop10Products();
        $this->__source_class_name = 'CProductTop10DeletedFromCartTimes';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP_10_PRODUCTS_DELETED_FROM_CART_BY_TIMES');
    }

    function setColumns()
    {
        parent::setColumns('product_deleted_from_cart_times', getMsg('RPTS','COLUMN_DELETED_FROM_CART_TIMES'));
    }
}

class ReportTop10AddedToCartQuantity extends ReportTop10Products
{
    function ReportTop10AddedToCartQuantity()
    {
        parent::ReportTop10Products();
        $this->__source_class_name = 'CProductTop10AddedToCartQuantity';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP_10_PRODUCTS_ADDED_TO_CART_BY_QTY');
    }

    function setColumns()
    {
        parent::setColumns('product_added_to_cart_qty', getMsg('RPTS','COLUMN_ADDED_TO_CART_QTY'));
    }
}

class ReportTop10DeletedFromCartQuantity extends ReportTop10Products
{
    function ReportTop10DeletedFromCartQuantity()
    {
        parent::ReportTop10Products();
        $this->__source_class_name = 'CProductTop10DeletedFromCartQuantity';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP_10_PRODUCTS_DELETED_FROM_CART_BY_QTY');
    }

    function setColumns()
    {
        parent::setColumns('product_deleted_from_cart_qty', getMsg('RPTS','COLUMN_DELETED_FROM_CART_QTY'));
    }
}



?>