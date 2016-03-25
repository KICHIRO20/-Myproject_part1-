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
 * Module "TaxExempts"
 *
 * @package TaxExempts
 * @author Vadim Lyalikov
 */
class TaxExempts
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * TaxExempts  constructor
     */
    function TaxExempts()
    {
        if(modApiFunc('Session', 'is_Set', 'FullTaxExemptStatus'))
        {
            $this->FullTaxExemptStatus = modApiFunc('Session', 'get', 'FullTaxExemptStatus');
            $this->FullTaxExemptCustomerInput = modApiFunc('Session', 'get', 'FullTaxExemptCustomerInput');
            //          ,                                     VAT        .
            $this->FullTaxExemptStatusError = modApiFunc('Session', 'get', 'FullTaxExemptStatusError');
        }
        else
        {
            $this->FullTaxExemptStatus = DB_FALSE;
            modApiFunc('Session', 'set', 'FullTaxExemptStatus', $this->FullTaxExemptStatus);
            $this->FullTaxExemptCustomerInput = "";
            modApiFunc('Session', 'set', 'FullTaxExemptCustomerInput', $this->FullTaxExemptCustomerInput);
            $this->FullTaxExemptStatusError = 0;
            modApiFunc('Session', 'set', 'FullTaxExemptStatusError', $this->FullTaxExemptStatusError);
        }

    }

    function getFullTaxExemptStatus()
    {
        return $this->FullTaxExemptStatus;
    }

    function getFullTaxExemptCustomerInput()
    {
        return _ml_html_entity_decode($this->FullTaxExemptCustomerInput);
    }

    function getFullTaxExemptStatusError()
    {
        return $this->FullTaxExemptStatusError;
    }


    function setFullTaxExemptStatus($v)
    {
        $this->FullTaxExemptStatus = $v;
        modApiFunc('Session', 'set', 'FullTaxExemptStatus', $this->FullTaxExemptStatus);
    }

    function setFullTaxExemptCustomerInput($v)
    {
        $this->FullTaxExemptCustomerInput = prepareHTMLDisplay($v);
        modApiFunc('Session', 'set', 'FullTaxExemptCustomerInput', $this->FullTaxExemptCustomerInput);
    }

    function setFullTaxExemptStatusError($v)
    {
        $this->FullTaxExemptStatusError = $v;
        modApiFunc('Session', 'set', 'FullTaxExemptStatusError', $this->FullTaxExemptStatusError);
    }

    /**
     *                                     .
     *
     *       install()                      .
     *
     *                                          ,         ,
     * TaxExempts::getTables()        $this->getTables()
     */
    function install()
    {
        global $application;

        $tables = TaxExempts::getTables();
        $query = new DB_Table_Create($tables);

        // advanced settings parameter
        $param_info = array(
                         'GROUP_NAME'        => 'TAXES_PARAMS',
                         'PARAM_NAME'        => 'ALLOW_FULL_TAX_EXEMPTS',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('TAXEXEMPTS', 'TAXEXEMPTS_ALLOW_FULL_TAX_EXEMPTS_PARAM_NAME'),
                                                       'DESCRIPTION' => array('TAXEXEMPTS', 'TAXEXEMPTS_ALLOW_FULL_TAX_EXEMPTS_PARAM_DESCRIPTION') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => DB_FALSE,
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TAXEXEMPTS', 'TAXEXEMPTS_NO'),
                                                                       'DESCRIPTION' => array('TAXEXEMPTS', 'TAXEXEMPTS_NO') ),
                                       ),
                                 array(  'VALUE' => DB_TRUE,
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('TAXEXEMPTS', 'TAXEXEMPTS_YES'),
                                                                       'DESCRIPTION' => array('TAXEXEMPTS', 'TAXEXEMPTS_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => DB_TRUE,
                         'PARAM_DEFAULT_VALUE' => DB_TRUE,
        );
        modApiFunc('Settings','createParam', $param_info);
    }

    /**
     * Uninstall the module.
     *                           .
     *
     *       uninstall()                      .
     *
     *                                          ,         ,
     * TaxExempts::getTables()        $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(TaxExempts::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     *
     *
     *                                        :
     * <code>
     *      $tables = array ();
     *      $table_name = 'table_name';
     *      $tables[$table_name] = array();
     *      $tables[$table_name]['columns'] = array
     *      (
     *          'fn1'               => 'table_name.field_name_1'
     *         ,'fn2'               => 'table_name.field_name_2'
     *         ,'fn3'               => 'table_name.field_name_3'
     *         ,'fn4'               => 'table_name.field_name_4'
     *      );
     *      $tables[$table_name]['types'] = array
     *      (
     *          'fn1'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
     *         ,'fn2'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
     *         ,'fn3'               => DBQUERY_FIELD_TYPE_CHAR255
     *         ,'fn4'               => DBQUERY_FIELD_TYPE_TEXT
     *      );
     *      $tables[$table_name]['primary'] = array
     *      (
     *          'fn1'       #                                            ,          - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      #                                                   ,          - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $table_order_full_tax_exempts = 'order_full_tax_exempts';
        $tables[$table_order_full_tax_exempts] = array();
        $tables[$table_order_full_tax_exempts]['columns'] = array
            (
                'order_id'                     => $table_order_full_tax_exempts.'.order_id'
               ,'exempt_status'                => $table_order_full_tax_exempts.'.exempt_status'
               ,'exempt_reason_customer_input' => $table_order_full_tax_exempts.'.exempt_reason_customer_input'
            );
        $tables[$table_order_full_tax_exempts]['types'] = array
            (
                'order_id'                     => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'exempt_status'                => DBQUERY_FIELD_TYPE_BOOL .' NOT NULL DEFAULT FALSE'
               ,'exempt_reason_customer_input' => DBQUERY_FIELD_TYPE_CHAR255
            );
        $tables[$table_order_full_tax_exempts]['primary'] = array
            (
                'order_id'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function insertOrderFullTaxExempt($order_id, $exempt_reason_customer_input, $exempt_status)
    {
        global $application;
        $tables = $this->getTables();

        $tr = $tables['order_full_tax_exempts']['columns'];

        $query = new DB_Insert('order_full_tax_exempts');
        $query->addInsertValue($order_id, $tr['order_id']);
        $query->addInsertValue($exempt_reason_customer_input, $tr['exempt_status']);
        $query->addInsertValue($exempt_status, $tr['exempt_reason_customer_input']);
        $result = $application->db->getDB_Result($query);
    }

    function getOrderFullTaxExempts($order_id = NULL, $b_exempted_only = true)
    {
        global $application;
        $result_rows = execQuery('SELECT_ORDERS_FULL_TAX_EXEMPTION_DATA', array('order_id' => $order_id, 'b_exempted_only' => $b_exempted_only));
        return $result_rows;
    }

    function DeleteOrders($ordersId)
    {
        global $application;

        $tables = $this->getTables();
        $opc = $tables['order_full_tax_exempts']['columns'];
        $DB_IN_string = "('".implode("', '", $ordersId)."')";

        $query = new DB_Delete('order_full_tax_exempts');
        $query->WhereField($opc['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $is_debug;
    var $_debug_info;

    /**#@-*/
}
?>