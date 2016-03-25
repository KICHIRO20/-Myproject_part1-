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

class CustomerInfo
{

	function CustomerInfo()
	{
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
            'CustomerInfo'
           ,'BillingInfo'
           ,'ShippingInfo'
           ,'CreditCardInfo'
           ,'Orders'
           ,'OrderId'
           ,'OrderCustomerName'
           ,'OrderDate'
           ,'OrderPriceTotal'
           ,'OrderStatus'
           ,'OrderPaymentStatus'
           ,'GroupName'
           ,'GroupId'
           ,'GroupPersonInfoVariantId'
           ,'GroupCVVPurged'
           ,'Counter'
           ,'LastOrderId'
        ));

        $this->customer_id = modApiFunc('Checkout', 'getCurrentCustomerID');
        $this->_customer           = modApiFunc('Checkout', 'getCustomerInfo', $this->customer_id, false);
        $this->_customer_encrypted = modApiFunc('Checkout', 'getCustomerInfo', $this->customer_id, true);
        return $this->TemplateFiller->fill("checkout/customer-info/", "container.tpl.html", array());
	}

	/**
	 * @ describe the function CustomerInfo->.
	 */
	function getCustomerInfo()
	{
	    $result = "";
		$this->_group = array(
		    'id' => 1
		   ,'name' => $this->MessageResources->getMessage(new ActionMessage('CUSTOMERS_INFO_GROUP1'))
		);
		$result .= $this->TemplateFiller->fill("checkout/customer-info/", "group.tpl.html", array());
		$this->_counter = 1;
		foreach ($this->_customer['Customer']['attr'] as $attr)
		{
		    if ($attr['id'] === null) continue;
		    $this->_counter++;
			$this->_attr = array(
			    'tag' => $attr['tag']
			   ,'name' => $attr['name']
			   ,'value' => $attr['value']
               ,'personattributeid' => $attr['person_attribute_id']
			);
	        $result .= $this->TemplateFiller->fill("checkout/customer-info/", "attr.tpl.html", array());
		}
		$result .= $this->TemplateFiller->fill("checkout/customer-info/", "show-state.tpl.html", array());
		return $result;
	}

	function getBillingInfo()
	{
	    $result = "";
		$this->_group = array(
		    'id' => 2
		   ,'name' => $this->MessageResources->getMessage(new ActionMessage('CUSTOMERS_INFO_GROUP2'))
		);
		$result .= $this->TemplateFiller->fill("checkout/customer-info/", "group.tpl.html", array());
		$this->_counter = 1;
		foreach ($this->_customer['Billing']['attr'] as $attr)
		{
		    $this->_counter++;
			$this->_attr = array(
			    'tag' => $attr['tag']
			   ,'name' => $attr['name']
			   ,'value' => $attr['value']
               ,'personattributeid' => $attr['person_attribute_id']
			);
	        $result .= $this->TemplateFiller->fill("checkout/customer-info/", "attr.tpl.html", array());
		}
		$result .= $this->TemplateFiller->fill("checkout/customer-info/", "show-state.tpl.html", array());
		return $result;
	}

    function getShippingInfo()
    {
	    $result = "";
		$this->_group = array(
		    'id' => 3
		   ,'name' => $this->MessageResources->getMessage(new ActionMessage('CUSTOMERS_INFO_GROUP3'))
		);
		$result .= $this->TemplateFiller->fill("checkout/customer-info/", "group.tpl.html", array());
		$this->_counter = 1;
		foreach ($this->_customer['Shipping']['attr'] as $attr)
		{
		    $this->_counter++;
			$this->_attr = array(
			    'tag' => $attr['tag']
			   ,'name' => $attr['name']
			   ,'value' => nl2br(_ml_htmlentities($attr['value']))
               ,'personattributeid' => $attr['person_attribute_id']
			);
	        $result .= $this->TemplateFiller->fill("checkout/customer-info/", "attr.tpl.html", array());
		}
		$result .= $this->TemplateFiller->fill("checkout/customer-info/", "show-state.tpl.html", array());
		return $result;
    }

    function getCreditCardInfo()
    {
        global $application;
        /* Credit Card Info can not exist for the specified order.
         * Then output an empty string.
         */
        if(isset($this->_customer['CreditCard']['id']))
        {
            $result = "";
            $this->_group = array(
                'id' => $this->_customer['CreditCard']['id']
               ,'name' => $this->MessageResources->getMessage(new ActionMessage('CUSTOMERS_INFO_GROUP4'))
               ,'personinfovariantid' => $this->_customer['CreditCard']['person_info_variant_id']
            );
            $GroupCVVPurged = 'true';
            $result .= $this->TemplateFiller->fill("checkout/customer-info/", "group.tpl.html", array());
            $this->_counter = 1;

            $remove_info_group_msg = getMsg('SYS','CHECKOUT_ORDER_INFO_REMOVE_CREDIT_CARD_INFO_MSG');
            $remove_info_group_confirm_msg = getMsg('SYS','CHECKOUT_ORDER_INFO_REMOVE_CREDIT_CARD_INFO_CONFIRM_MSG');

            $this->_msg['CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_MSG'] = $remove_info_group_msg;
            $this->_msg['CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_CONFIRM_MSG'] = $remove_info_group_confirm_msg;

            $application->registerAttributes(array('CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_MSG'
                                                  ,'CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_CONFIRM_MSG'));

            $this->_counter++;
            $result .= $this->TemplateFiller->fill("checkout/customer-info/", "group-decryption-controls-right.tpl.html", array());

            foreach ($this->_customer['CreditCard']['attr'] as $attr)
            {
                $this->_counter++;
                $this->_attr = array(
//                    'person_attribute_id' =>
                    'tag' => $attr['tag']
                   ,'name' => $attr['name']
                   ,'value' => nl2br(_ml_htmlentities($attr['value']))
                   ,'personattributeid' => $attr['person_attribute_id']
                );

                //The code is not correct. Output the warning about CVV.
                //It would be better to find the id by the tag in the table person_attributes.
                $cvv_person_attribute_id = 14;
                if($attr['person_attribute_id'] == $cvv_person_attribute_id &&
                   $attr['value'] != getMsg('SYS','CHECKOUT_ORDER_INFO_CVV_PURGED_MSG'))
                {
                    $GroupCVVPurged = 'false';
                    $this->_attr['value'] = $attr['value'] . ' <span style="color: red;">' . getMsg('SYS',"CHECKOUT_ORDER_INFO_CVV_WILL_BE_DELETED_WARNING_MSG") . '</span>';
                }

                $result .= $this->TemplateFiller->fill("checkout/customer-info/", "attr.tpl.html", array());
            }

            $this->_counter++;
            $this->_group['CVVPurged'] = $GroupCVVPurged;
            $result .= $this->TemplateFiller->fill("checkout/customer-info/", "group-decryption-controls.tpl.html", array());
            $result .= $this->TemplateFiller->fill("checkout/customer-info/", "show-state.tpl.html", array());
            return $result . $this->getEncryptedHiddenCreditCardInfo();
        }
        else
        {
            return "";
        }
    }

    /**
     * Outputs decrypted attribute values.
     * In the format of the hidden fields and as a javascript array.
     * array( variable_id => array
                             (
                                 variable_blowfish_encrypted_value,
                                 blowfish_rsa_encoded_key
     */
    function getEncryptedHiddenCreditCardInfo()
    {
        $result = "";
        if(isset($this->_customer['CreditCard']['id']))
        {
            $encrypted_data = array();
            foreach ($this->_customer_encrypted['CreditCard']['attr'] as $attr)
            {
                $this->_counter++;
                $this->_attr = array(
                    'tag' => $attr['tag']
                   ,'name' => $attr['name'] . "_encrypted"
                   ,'value' => nl2br(_ml_htmlentities($attr['value']))
                   ,'person_attribute_id' => $attr['person_attribute_id']
                   ,'encrypted_secret_key' => $attr['encrypted_secret_key']
                   ,'rsa_public_key_asc_format' => $attr['rsa_public_key_asc_format']
                );

                $encrypted_data[$attr["person_attribute_id"]] = array
                (
                    "variable_value__blowfish_encrypted" => $this->_attr["value"]
                   ,"blowfish_key__rsa_encrypted" => $this->_attr["encrypted_secret_key"]
                   ,"rsa_public_key_asc_format" => $this->_attr["rsa_public_key_asc_format"]
                );
            }
            $result = '<input type="hidden" name="encrypted_data['.$this->_group['id'].']" value="'.base64_encode(serialize($encrypted_data)).'">';
            return $result;
        }
        else
        {
            return "";
        }
    }


    function getOrders()
    {
	    $result = "";
		foreach ($this->_customer['Orders'] as $order)
		{
            $order["Date"] = modApiFunc("Localization", "SQL_date_format", $order["Date"]);
            $order["Total"] = modApiFunc("Localization", "currency_format", $order["Total"]);
			$this->_order = $order;
			$result .= $this->TemplateFiller->fill("checkout/customer-info/", "order.tpl.html", array());
		}
		return $result;
    }

    /**
	 * @ describe the function OrderInfo->.
	 */
	function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
		    case 'CustomerInfo':
		        $value = $this->getCustomerInfo();
		        break;

		    case 'BillingInfo':
		        $value = $this->getBillingInfo();
		        break;

		    case 'ShippingInfo':
		        $value = $this->getShippingInfo();
		        break;

		    case 'CreditCardInfo':

		        $value = $this->getCreditCardInfo();
		        break;

		    case 'Orders':
		        $value = $this->getOrders();
		        break;
            case 'LastOrderId':
                $value = $this->_customer['ID'];
                break;

            case 'CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_MSG':
            case 'CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_CONFIRM_MSG':
                $value = $this->_msg[$tag];
                break;

		    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'group')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->_group);
        	    }
        	    elseif ($entity == 'attribute')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->_attr);
        	    }
        	    elseif ($entity == 'order')
        	    {
            	    if (_ml_strpos($tag, 'price') === 0)
            	    {
            	        $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('price')));
            	        if ($tag == 'total')
            	        {
            	        	$value = $this->_order['Total'];
            	        }
            	        elseif ($tag == 'subtotal')
            	        {
            	        	$value = $this->_order['Subtotal'];
            	        }
            	        else
            	        {
                	        $prices = getKeyIgnoreCase('price', $this->_order);
                	        $value = getKeyIgnoreCase($tag, $prices);
            	        }
            	    }
            	    elseif (_ml_strpos($tag, 'customer') === 0)
            	    {
            	        $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('customer')));
            	        $customer = getKeyIgnoreCase('customer', $this->_order);
            	        $value = $customer['attr'][$tag]['value'];
            	    }
            	    else
            	    {
        	           $value = getKeyIgnoreCase($tag, $this->_order);
            	    }
        	    }
				break;
		}
		return $value;
	}

    var $TemplateFiller;
    var $MessageResources;
    var $customer_id;
    var $_customer;
    var $_order;
    var $_group;
    var $_counter;
    var $_attr;
}
?>