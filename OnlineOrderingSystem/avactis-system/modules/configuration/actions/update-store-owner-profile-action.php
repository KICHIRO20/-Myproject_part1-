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
 * Action handler on update general settings.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Kolesnikov
 */
class UpdateStoreOwnerProfile extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function UpdateStoreOwnerProfile()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$store_owner_name = $request->getValueByKey( SYSCONFIG_STORE_OWNER_NAME );
    	$store_owner_website = $request->getValueByKey( SYSCONFIG_STORE_OWNER_WEBSITE );
    	$store_owner_phones = $request->getValueByKey( SYSCONFIG_STORE_OWNER_PHONES );
    	$store_owner_fax = $request->getValueByKey( SYSCONFIG_STORE_OWNER_FAX );
    	$store_owner_address_line_1 = $request->getValueByKey( SYSCONFIG_STORE_OWNER_STREET_LINE_1 );
    	$store_owner_address_line_2 = $request->getValueByKey( SYSCONFIG_STORE_OWNER_STREET_LINE_2 );
    	$store_owner_city = $request->getValueByKey( SYSCONFIG_STORE_OWNER_CITY );

    	$store_owner_state_menu = $request->getValueByKey( SYSCONFIG_STORE_OWNER_STATE . "_menu_select");
    	$store_owner_state_text = $request->getValueByKey( SYSCONFIG_STORE_OWNER_STATE . "_text_div");
    	$store_owner_state = (empty($store_owner_state_menu) || !is_numeric($store_owner_state_menu)) ? $store_owner_state_text : $store_owner_state_menu;

    	$store_owner_postcode = $request->getValueByKey( SYSCONFIG_STORE_OWNER_POSTCODE );
    	$store_owner_country = $request->getValueByKey( SYSCONFIG_STORE_OWNER_COUNTRY );
    	$store_owner_email = $request->getValueByKey( SYSCONFIG_STORE_OWNER_EMAIL );
        $store_owner_email_from = $request->getValueByKey( SYSCONFIG_STORE_OWNER_EMAIL_FROM );
    	$store_owner_site_administrator_email = $request->getValueByKey( SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL );
        $store_owner_site_administrator_email_from = $request->getValueByKey( SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM );
    	$store_owner_orders_department_email = $request->getValueByKey( SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL );
        $store_owner_orders_department_email_from = $request->getValueByKey( SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM );

        $store_owner_email_from = str_replace(',', '', $store_owner_email_from);
        $store_owner_site_administrator_email_from = str_replace(',', '', $store_owner_site_administrator_email_from);
        $store_owner_orders_department_email_from = str_replace(',', '', $store_owner_orders_department_email_from);

        $store_owner_page_title = $request->getValueByKey( SYSCONFIG_STORE_OWNER_PAGE_TITLE );

        $values = array(
    	    SYSCONFIG_STORE_OWNER_NAME => $store_owner_name
    	   ,SYSCONFIG_STORE_OWNER_WEBSITE => $store_owner_website
    	   ,SYSCONFIG_STORE_OWNER_PHONES => $store_owner_phones
    	   ,SYSCONFIG_STORE_OWNER_FAX => $store_owner_fax
    	   ,SYSCONFIG_STORE_OWNER_STREET_LINE_1 => $store_owner_address_line_1
    	   ,SYSCONFIG_STORE_OWNER_STREET_LINE_2 => $store_owner_address_line_2
    	   ,SYSCONFIG_STORE_OWNER_CITY => $store_owner_city
    	   ,SYSCONFIG_STORE_OWNER_STATE => $store_owner_state
    	   ,SYSCONFIG_STORE_OWNER_POSTCODE => $store_owner_postcode
    	   ,SYSCONFIG_STORE_OWNER_COUNTRY => $store_owner_country
    	   ,SYSCONFIG_STORE_OWNER_EMAIL => $store_owner_email
           ,SYSCONFIG_STORE_OWNER_EMAIL_FROM => $store_owner_email_from
    	   ,SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL => $store_owner_site_administrator_email
           ,SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM => $store_owner_site_administrator_email_from
    	   ,SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL => $store_owner_orders_department_email
           ,SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM => $store_owner_orders_department_email_from
           ,SYSCONFIG_STORE_OWNER_PAGE_TITLE => $store_owner_page_title
    	);

        $SessionPost = $_POST;
        $SessionPost[SYSCONFIG_STORE_OWNER_STATE] = $store_owner_state;
        $SessionPost["ViewState"] = array();
        $SessionPost["ViewState"]["ErrorsArray"] = array();
        if (!modApiFunc("Users", "isValidEmail", $store_owner_email))
        {
            $SessionPost["ViewState"]["ErrorsArray"][] = 'OWNER_PROFILE_WRN_001';
        }
        if (!modApiFunc("Users", "isValidEmail", $store_owner_site_administrator_email))
        {
            $SessionPost["ViewState"]["ErrorsArray"][] = 'OWNER_PROFILE_WRN_002';
        }
        if (!modApiFunc("Users", "isValidEmail", $store_owner_orders_department_email))
        {
            $SessionPost["ViewState"]["ErrorsArray"][] = 'OWNER_PROFILE_WRN_003';
        }

        if (sizeof($SessionPost["ViewState"]["ErrorsArray"]))
        {
            modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        }
        else
        {
            modApiFunc('Configuration', 'setValue', $values);
            modApiFunc('Session','set','ResultMessage','MSG_OWNER_PROFILE_UPDATED');
    		$request = new Request();
    		$request->setView(CURRENT_REQUEST_URL);
	    	$application->redirect($request);
        }
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}

?>