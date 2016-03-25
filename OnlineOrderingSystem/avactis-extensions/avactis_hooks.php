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
 //global $plugin_return_action;

add_action("notification_content_customer","get_Customer_obj",10,3);

add_action("notification_content_product","get_Product_obj",10,3);

add_action("notification_content_action","get_action");


add_action("checkout_view","Local_OrderLinkInvoicePDF_view",10,1);

add_action("customer_order_info","Local_InvoicePDF_info",10,2);

add_action("customer_order_list","Local_OrderInvoiceLinkPDF_list",10,2);

add_filter( "avactis_checkout_view_addAttributes", "invoice_addAttributes");

add_filter( "avactis_customer_order_info_addAttributes", "invoice_order_info_addAttributes");

add_filter( "avactis_customer_order_list_addAttributes", "invoice_order_list_addAttributes");
/*** All TPL-CZ Filters - Start *******/
/* Catalog */
add_filter('registerAttributes','AddTagToProductInfo',10,2);
add_filter('registerAttributes','AddTagToProductList',10,2);
add_action('AddTagValueToProductInfo','add_tag_value_prod_info');


/* Cart */
add_filter('registerAttributes','AddTagToCart',10,2);
add_action('AddTagValueToCart','add_tag_value_cart');

/* Checkout */
add_filter('registerAttributes','AddTagToCheckout',10,2);
add_action('AddTagValueToCheckout','add_tag_value_checkout');

/* Gift Certificate */
add_filter('registerAttributes','AddTagToGC',10,2);
add_action('AddTagValueToGC','add_tag_value_GC');
/*** All TPL-CZ Filters - End *******/

/**** All action Filters - Start ******/
/* Cart Action */
add_filter('registerPreOnAction','AddTagToActionCartPre');
add_action('PreAddToCart','add_tag_value_to_action_cart_pre');

add_filter('registerPostOnAction','AddTagToActionCartPost');
add_action('PostAddToCart','add_tag_value_to_action_cart_post');

/* Catalog Action */
add_filter('registerPreOnAction','AddTagToActionCatalogPre');
add_action('PreAddToCatalog','add_tag_value_to_action_catalog_pre');

add_filter('registerPostOnAction','AddTagToActionCatalogPost');
add_action('PostAddToCatalog','add_tag_value_to_action_catalog_post');

/* Checkout Action */
add_filter('registerPreOnAction','AddTagToActionCheckoutPre');
add_action('PreAddToCheckout','add_tag_value_to_action_checkout_pre');

add_filter('registerPostOnAction','AddTagToActionCheckoutPost');
add_action('PostAddToCheckout','add_tag_value_to_action_checkout_post');


/* GC Action */
add_filter('registerPreOnAction','AddTagToActionGCPre');
add_action('PreAddToGC','add_tag_value_to_action_GC_pre');

add_filter('registerPostOnAction','AddTagToActionGCPost');
add_action('PostAddToGC','add_tag_value_to_action_GC_post');


/**** All action Filters - End ******/


/******** Catalog - start *****************/
function AddTagToProductInfo($val,$viewname)
{
	if($viewname == 'ProductInfo')
	{
		$newval = array('InfoTestViewTag' => '');
		$val = array_merge($val,$newval);
	}
		return $val;
}

function AddTagToProductList($val,$viewname)
{
	if($viewname == 'ProductList')
	{
		$new_val=array('ListTestviewtag'=>'');
		$val=array_merge($val,$new_val);
	}
	return $val;
}

function add_tag_value_prod_info()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
    if($moduleexists == 1)
    {
	 	modApiFunc('Session', 'set', 'plugin_return_action','This is product info test tag value!!!');
    }
    else
    {
    	modApiFunc('Session', 'set', 'plugin_return_action','');
    }

}
/************* Catalog - end **************/

/************* Cart - start ***************/
function AddTagToCart($val,$viewname)
{
	if($viewname == 'ShoppingCart')
	{
		$newval = array('ShoppingCartTestViewTag' => '');
		$val = array_merge($val,$newval);
	}
		return $val;
}

function add_tag_value_cart()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Cart test tag value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}

}
/************ Cart - end *****************/

/********* Checkout -start *****************/
function AddTagToCheckout($val,$viewname)
{
	if($viewname == 'OneStepCheckout')
	{
		$newval = array('CheckoutViewTestViewTag' => '');
		$val = array_merge($val,$newval);
	}
	return $val;
}

function add_tag_value_checkout()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Checkout test tag value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}

}
/************ Checkout - end *****************/

/********* Gift Certificate - start *****************/
function AddTagToGC($val,$viewname)
{
	if($viewname == 'CreateGiftCertificateForm')
	{
		$newval = array('CreateGiftCertificateFormTestViewTag' => '');
		$val = array_merge($val,$newval);
	}
	return $val;
}

function add_tag_value_GC()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is GC test tag value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}

}
/************ Gift Certificate - end *****************************/
/********************* Action Cart - Start ***********************/
function AddTagToActionCartPre()
{
	$val = array('AddToCartActionTestTagPre' => '');
	return $val;
}

function add_tag_value_to_action_cart_pre()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Cart Action Pre test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}

function AddTagToActionCartPost()
{
	$val = array('AddToCartActionTestTagPost' => '');
	return $val;
}

function add_tag_value_to_action_cart_post()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Cart Action Post test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}
/******************** Action Cart - End **************************/

/********************* Action Catalog - Start ***********************/
function AddTagToActionCatalogPre()
{
	$val = array('AddToCatalogActionTestTagPre' => '');
	return $val;
}

function add_tag_value_to_action_catalog_pre()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Catalog Action Pre test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}

function AddTagToActionCatalogPost()
{
	$val = array('AddToCatalogActionTestTagPost' => '');
	return $val;
}

function add_tag_value_to_action_catalog_post()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Catalog Action Post test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}
/******************** Action Catalog - End **************************/

/********************* Action GC - Start ***********************/
function AddTagToActionGCPre()
{
	$val = array('AddToGCActionTestTagPre' => '');
	return $val;
}

function add_tag_value_to_action_GC_pre()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is GC Action Pre test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}

function AddTagToActionGCPost()
{
	$val = array('AddToGCActionTestTagPost' => '');
	return $val;
}

function add_tag_value_to_action_GC_post()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is GC Action Post test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}
/******************** Action GC - End **************************/

/********************* Action Checkout - Start ***********************/
function AddTagToActionCheckoutPre()
{
	$val = array('AddToCheckoutActionTestTagPre' => '');
	return $val;
}

function add_tag_value_to_action_checkout_pre()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Checkout Action Pre test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}

function AddTagToActionCheckoutPost()
{
	$val = array('AddToCheckoutActionTestTagPost' => '');
	return $val;
}

function add_tag_value_to_action_checkout_post()
{
	$moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','test_extension');
	if($moduleexists == 1)
	{
		modApiFunc('Session', 'set', 'plugin_return_action','This is Checkout Action Post test value!!!');
	}
	else
	{
		modApiFunc('Session', 'set', 'plugin_return_action','');
	}
}
/******************** Action Checkout - End **************************/


function get_Customer_obj($customerAccount,$val){
  global $application;
  if($val==18)
   {
      modApiFunc('Session', 'set', 'plugin_return_action',$application->getInstance('CCustomerInfo',$customerAccount));
   }
  else
      modApiFunc('Session', 'set', 'plugin_return_action','');

}

function get_Product_obj($prod_info,$val){
  global $application;
  if($val==18)
  {
     modApiFunc('Session', 'set', 'plugin_return_action',$application-> getInstance('CProductInfo', $prod_info));
  }
  else
      modApiFunc('Session', 'set', 'plugin_return_action','');
}

function get_action($val)
{
     $val_true=true;
      if ($val== 18) //Notify-Me
        {
        	modApiFunc('Session', 'set', 'plugin_return_action',$val_true);

        }
       else
       {
       	    modApiFunc('Session', 'set', 'plugin_return_action','');
       }
}

function Local_OrderLinkInvoicePDF_view($val){


      if(strcmp($val,"Local_OrderLinkInvoicePDF")==0)
      {

       $r = new Request();
       $r->setView(CURRENT_REQUEST_URL);
       $r->setAction('get_invoice_pdf');
       $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");
       $r->setKey('order_id',$lastPlacedOrderID);
       modApiFunc('Session', 'set', 'plugin_return_action', $r->getURL());

      }
      else
       modApiFunc('Session', 'set', 'plugin_return_action','');
}

function Local_InvoicePDF_info($order_id,$val)
{
  if(strcmp($val,"invoicepdf")==0)
  {
   $r = new Request();
   $r->setView(CURRENT_REQUEST_URL);
   $r->setAction('get_invoice_pdf');
   $r->setKey('order_id',$order_id);
   modApiFunc('Session', 'set', 'plugin_return_action', '<a target="_blank" href="'.$r->getURL().'">'.cz_getMsg('ACCOUNT_INVOICE_ORDER_PDF').'</a>');
  }
  else
     modApiFunc('Session', 'set', 'plugin_return_action','');
}

function Local_OrderInvoiceLinkPDF_list($current_order_id,$val)
{

  if(strcmp($val,"invoicelinkpdf")==0)
  {
     $r = new Request();
     $r->setView(CURRENT_REQUEST_URL);
     $r->setAction('get_invoice_pdf');
     $r->setKey('order_id',$current_order_id);

     modApiFunc('Session', 'set', 'plugin_return_action', $r->getURL());

  }
  else
    modApiFunc('Session', 'set', 'plugin_return_action','');
}

function invoice_addAttributes($val)
{

      $new_val=array("Local_OrderLinkInvoicePDF" =>'');

      $val=array_merge($val,$new_val);
      return $val;
}

function invoice_order_info_addAttributes($val)
{
      $new_val=array('Local_InvoicePDF');

      $val=array_merge($val,$new_val);

      return $val;
}

function invoice_order_list_addAttributes($val)
{

      $new_val=array('Local_OrderInvoiceLinkPDF');

      $val=array_merge($val,$new_val);

      return $val;
}

?>