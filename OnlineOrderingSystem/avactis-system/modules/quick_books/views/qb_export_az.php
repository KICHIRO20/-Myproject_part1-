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
 * @package QuickBooks
 * @author Egor V. Derevyankin
 *
 */

class QB_Export
{
    function QB_Export()
    {
    }

    function output()
    {
        global $application;

        $order_id_list = $this->getOrderList();

        $template_contents = array(
            "OrdersIDs" =>  implode('|',$order_id_list)
           ,"OrdersCount" => count($order_id_list)
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("quick_books/export/", "container.tpl.html",array());
    }

    function getOrderList()
    {
        global $application;
        $tables = modApiFunc('Checkout','getTables');
        $o = $tables['orders']['columns'];

        $request = &$application->getInstance('Request');
        $order_id_list = $request->getValueByKey( 'order_id' );
        $export_all = $request->getValueByKey('export_all');

        //                                        id-
        $query = new DB_Select();
        $query->addSelectField($o['id'], 'id');
        if ($export_all == null || $export_all == 'false')
        {
            $query->WhereField($o['id'], DB_IN, ' ('.implode(',', $order_id_list).') ');
        }
        $result = $application->db->getDB_Result($query);

        $plain_list = array();
        foreach($result as $item)
        {
            $plain_list[] = $item['id'];
        }
        return $plain_list;
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }
};

?>