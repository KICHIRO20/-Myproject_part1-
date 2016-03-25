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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataReaderDefault');

class DataReaderCustomersDB extends DataReaderDefault
{
    function DataReaderCustomersDB()
    {}

    function initWork($settings)
    {
        $this->clearWork();

        $this->_settings['customers_filter'] = $settings['customers_filter'];

        modApiFunc('Customer_Account','setCustomersSearchFilter',$this->_settings['customers_filter']);
       if(modApiFunc('Session','is_Set','Customer_Export')){
       	$this->_customers=modApiFunc('Session','get','Customer_Export');
	        modApiFunc('Session','un_Set','Customer_Export');
       }else{
	        $this->_customers = modApiFunc('Customer_Account','getSearchCustomersResult');
       }
        $this->_exported_count = 0;

        $this->_process_info['status'] = 'INITED';
        $this->_process_info['items_count'] = count($this->_customers);
        $this->_process_info['items_processing'] = 0;
    }

    function doWork()
    {
        $this->_process_info['items_count'] = count($this->_customers);
        $this->_process_info['status'] = 'HAVE_MORE_DATA';

        if($this->_customers === null)
        {
            $this->_process_info['items_count'] = 0;
            $this->_process_info['items_processing'] = $this->_exported_count;
            $this->_process_info['status'] = 'NO_MORE_DATA';
            return null;
        };

        if($this->_exported_count >= count($this->_customers))
        {
            $this->_process_info['items_processing'] = count($this->_customers);
            $this->_process_info['status']='NO_MORE_DATA';
            return null;
        };

        $customer = $this->_customers[$this->_exported_count++];
        $this->_process_info['items_processing'] = $this->_exported_count;
        if($this->_exported_count == count($this->_customers))
        {
             $this->_process_info['status'] = 'NO_MORE_DATA';
        };

        return $customer;
    }

    function finishWork()
    {
        $this->clearWork();
    }

    function loadWork()
    {
        foreach($this->_own_vars as $var_name)
        {
            $this->$var_name = modApiFunc('Session','is_set',__CLASS__.$var_name) ?  modApiFunc('Session','get',__CLASS__.$var_name) : null;
        };
    }

    function clearWork()
    {
        foreach($this->_own_vars as $var_name)
        {
            modApiFunc('Session','un_set',__CLASS__.$var_name);
            $this->$var_name = null;
        };
    }

    function saveWork()
    {
        foreach($this->_own_vars as $var_name)
        {
            if($this->$var_name !== null)
            {
                modApiFunc('Session','set',__CLASS__.$var_name,$this->$var_name);
            }
            elseif(modApiFunc('Session','is_set',__CLASS__.$var_name))
            {
                modApiFunc('Session','un_set',__CLASS__.$var_name);
            };
        };
    }

    var $_settings;
    var $_customers;
    var $_exported_count;
    var $_own_vars = array('_settings','_customers','_exported_count');
};

?>