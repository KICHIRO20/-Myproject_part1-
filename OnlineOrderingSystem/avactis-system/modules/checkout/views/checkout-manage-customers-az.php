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

class ManageCustomers
{

	function ManageCustomers()
	{
            modApiFunc('paginator', 'setCurrentPaginatorName', "Checkout_Customers");
            $this->_customers = modApiFunc('Checkout', 'getCustomerList');
            $this->_filter = modApiFunc("Checkout", "getCustomerSearchFilter");
	}

	/**
	 * The main function to output the given view.
	 */
	function output()
	{
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $application->registerAttributes(array(
            'SearchCustomers'
           ,'SearchResults'
           ,'ResultCount'
           ,'CustomerId'
           ,'CustomerName'
           ,'CustomerEmail'
           ,'CustomerTotalOrders'
           ,'CustomerTotalAmount'
           ,'PaginatorLine'
           ,'PaginatorRows'
           ,'CustomerRegStatus'
        ));
        return $this->TemplateFiller->fill("checkout/customers/", "container.tpl.html", array());
	}

	/**
	 * @ describe the function ManageCustomers->.
	 */
	function getCustomers()
	{
	    $result = "";
		foreach ($this->_customers as $customer_id)
		{
		    $this->_customer = modApiFunc("Checkout", "getCustomerInfo", $customer_id); //                            $customer_id           $order_id
            $result .= modApiFunc('TmplFiller', 'fill', "checkout/customers/", "item_result.tpl.html", array());
		}
		$this->_customer = null;
		return $result;
	}

	/**
	 * @ describe the function ManageOrders->.
	 */
	function getTag($tag)
	{
	    global $application;
		$value = null;
		switch ($tag)
		{
		    case 'SearchCustomers':
                $application->registerAttributes(array(
                    'CustomersSearchByField'
                   ,'CustomersSearchByFieldValue'
                   ,'CustomersSearchByLetter'
                ));
				$value = $this->TemplateFiller->fill("checkout/customers/", "search.tpl.html", array());
				break;

            case 'CustomersSearchByLetter':
                $value = "<td style='vertical-align: bottom'><a href='customers.php?asc_action=CustomersSearchByLetter&letter='>".$this->MessageResources->getMessage('CUSTOMERS_SEARCH_ALL')."</a></td><td>";
                for ($i=65; $i<=90; $i++)
                {
                    $letter = _byte_chr($i);
                    if ($this->_filter['search_by'] == 'letter' && !empty($this->_filter['letter']))
                    {
                        if (_byte_chr($i+32) == $this->_filter['letter'])
                        {
                            $letter = "<span class='required'>"._byte_chr($i)."</span>";
                        }
                    }
                    $value.= "<a href='customers.php?asc_action=CustomersSearchByLetter&letter="._byte_chr($i+32)."'><b>".$letter."</b></a>&nbsp;";
                }
                $value.= "</td>";
                break;

            case 'CustomersSearchByField':
                $value = "<OPTION value='name'>".$this->MessageResources->getMessage("CUSTOMERS_SEARCH_BY_NAME")."</OPTION>";
                if ($this->_filter['search_by'] == 'field' && !empty($this->_filter['field_name']) && $this->_filter['field_name']=='Email')
                {
                    $value.= "<OPTION value='Email' selected>".$this->MessageResources->getMessage("CUSTOMERS_SEARCH_BY_EMAIL")."</OPTION>";
                }
                else
                {
                    $value.= "<OPTION value='Email'>".$this->MessageResources->getMessage("CUSTOMERS_SEARCH_BY_EMAIL")."</OPTION>";
                }
                break;

            case 'CustomersSearchByFieldValue':
                $value = '';
                if ($this->_filter['search_by'] == 'field' && !empty($this->_filter['field_name']) && !empty($this->_filter['field_value']))
                {
                    $value = $this->_filter['field_value'];
                }
                break;

		    case 'SearchResults':
        		if (count($this->_customers) == 0)
        		{
    				$value = $this->TemplateFiller->fill("checkout/customers/", "empty.tpl.html", array());
        		}
        		else
        		{
    				$value = $this->TemplateFiller->fill("checkout/customers/", "results.tpl.html", array());
        		}
				break;

		    case 'Items':
		        $value = $this->getCustomers();
		        break;

		    case 'CustomerId':
		        $value = getKeyIgnoreCase('Id', $this->_customer);
		        break;

		    case 'ResultCount':
                $from = modApiFunc("Paginator", "getCurrentPaginatorOffset")+1;
                $to = modApiFunc("Paginator", "getCurrentPaginatorOffset") +  modApiFunc("Paginator", "getPaginatorRowsPerPage", "Checkout_Customers");
                $total = modApiFunc("Paginator", "getCurrentPaginatorTotalRows");
                if ($to > $total)
                {
                    $to = $total;
                }
                if ($total <= modApiFunc("Paginator", "getPaginatorRowsPerPage", "Checkout_Customers"))
                {
                    $value = $this->MessageResources->getMessage(new ActionMessage(array("CUSTOMERS_RESULTS_LESS_THEN_ROWS_PER_PAGE_FOUND", $total)));
                }
                else
                {
                    $value = $this->MessageResources->getMessage(new ActionMessage(array("CUSTOMERS_RESULTS_MORE_THEN_ROWS_PER_PAGE_FOUND", $from, $to, $total)));
                }
		        break;

		    case 'CustomerTotalOrders':
		        $value = getKeyIgnoreCase('TotalOrders', $this->_customer);
		        break;

            case 'CustomerTotalAmount':
		        $value = modApiFunc("Localization", "currency_format", getKeyIgnoreCase('TotalAmount', $this->_customer));
		        break;

    		case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("Checkout_Customers", "Customers");
                break;

            #                               PaginatorRows
            case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("Checkout_Customers", 'Customers', 'PGNTR_CUST_ITEMS');
                break;

            case 'CustomerRegStatus':
                $customerInfo = getKeyIgnoreCase('Customer', $this->_customer);
                $email = getKeyIgnoreCase('Email',$customerInfo['attr']);
                if(modApiFunc('Customer_Account','doesAccountExists',$email['value']))
                {
                    $value = 'reg';
                }
                else
                {
                    $value = 'not-reg';
                };
                break;

		    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'customer')
        	    {
        	        $customerInfo = getKeyIgnoreCase('Customer', $this->_customer);
        	        if(!($tagvalue = getKeyIgnoreCase($tag, $customerInfo['attr']))) break;
        	        $value = $tagvalue['value'];
        	    }
				break;
		}
		return $value;
	}

    var $TemplateFiller;
    var $MessageResources;
    var $_customers;
    var $_customer;
}
?>