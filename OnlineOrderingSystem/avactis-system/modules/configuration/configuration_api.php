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
 * Configuration module.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */
class Configuration
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * News constructor.
     */
    function Configuration()
    {
        global $application;
        $this->constant_values = array();
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Configuration::getTables() instead of $this->getTables()
     */
    function install()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $tables = Configuration::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'store_settings';            #the name of the filled table
        $columns = $tables[$table]['columns'];  #the array of field names of the table

        ##### General settings

        # Store Online/Offline
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_ONLINE, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);

        # Store offline key
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OFFLINE_KEY, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

        # Show products absent in store
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_SHOW_ABSENT, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue(STORE_SHOW_ABSENT_SHOW_NOT_BUY, $columns['value']);
        $application->db->getDB_Result($query);

        # Allow buy more, than available in stock
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);

        # Return product to stock when order is deleted
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_DELETED, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);

        # Return product to stock when order is cancelled or declined
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_CANCELLED, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);

        # Enable wish list
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_ENABLE_WISHLIST, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);


//        # Order products absent in store
//        $query = new DB_Insert($table);
//        $query->addInsertValue(SYSCONFIG_STORE_ORDER_ABSENT, $columns['name']);
//        $query->addInsertValue('boolean', $columns['type']);
//        $query->addInsertValue('1', $columns['value']);
//        $application->db->getDB_Result($query);

        # System time shift
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_TIME_SHIFT, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);

        # Admin zone signin count
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_SIGNIN_COUNT, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('10', $columns['value']);
        $application->db->getDB_Result($query);

        # Admin zone signin timeout
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_SIGNIN_TIMEOUT, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('5', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_SHOW_CART, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);

        #Min subtotal to begin checkout
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT, $columns['name']);
        $query->addInsertValue('price', $columns['type']);
        $query->addInsertValue(ZERO_PRICE, $columns['value']);
        $application->db->getDB_Result($query);
        #Paginators settings
        #AZ
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('10', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_AZ, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('10', $columns['value']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('a:4:{i:0;i:10;i:1;i:20;i:2;i:30;i:3;i:100;}', $columns['value']);
        $application->db->getDB_Result($query);

        #CZ
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('12', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_CZ, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('7', $columns['value']);
        $application->db->getDB_Result($query);

        #AddToCart Product Quantity settings
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_ADD_TO_CART_DEFAULT_QUANTITY, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_ADD_TO_CART_MAX_QUANTITY, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('30', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_ADD_TO_CART_LIMIT_MAX_QUANTITY_BY_STOCK, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE, $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);
        ##

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('a:4:{i:0;i:12;i:1;i:24;i:2;i:36;i:3;i:96;}', $columns['value']);
        $application->db->getDB_Result($query);

//        $query = new DB_Insert($table);
//        $query->addInsertValue(SYSCONFIG_STORE_DISPLAY_PRODUCT_PRICE_INCLUDING_TAXES, $columns['name']);
//        $query->addInsertValue('boolean', $columns['type']);
//        $query->addInsertValue('0', $columns['value']);
//        $application->db->getDB_Result($query);

//        $query = new DB_Insert($table);
//        $query->addInsertValue(SYSCONFIG_STORE_DISPLAY_TOTALS_INCLUDING_TAXES, $columns['name']);
//        $query->addInsertValue('boolean', $columns['type']);
//        $query->addInsertValue('1', $columns['value']);
//        $application->db->getDB_Result($query);

        ##### Store Owner's Profile

        # Name
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_NAME, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

       # Website
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_WEBSITE, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

      # Phones
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_PHONES, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

     # Fax
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_FAX, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

    # Street Line 1
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_STREET_LINE_1, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

    # Street Line 2
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_STREET_LINE_2, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

    # City
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_CITY, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('enter', $columns['value']);
        $application->db->getDB_Result($query);

    # State
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_STATE, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('406', $columns['value']); // 406 - NY
        $application->db->getDB_Result($query);

    # Zip/Postal code
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_POSTCODE, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('10001', $columns['value']); // NY Post Code
        $application->db->getDB_Result($query);

    # Country
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_COUNTRY, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        //: select the default country.
        $query->addInsertValue('223', $columns['value']); // 223 - USA
        $application->db->getDB_Result($query);

    # Store e-mail address
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_EMAIL, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('owner@avactis-demo.com', $columns['value']);
        $application->db->getDB_Result($query);

    # Store e-mail "from"
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_EMAIL_FROM, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('Avactis Store', $columns['value']);
        $application->db->getDB_Result($query);

    # Site administrator e-mail address
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('webmaster@avactis-demo.com', $columns['value']);
        $application->db->getDB_Result($query);

    # Site administrator e-mail "from"
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('Avactis Store Webmaster', $columns['value']);
        $application->db->getDB_Result($query);

    # Orders department e-mail address
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('orders@avactis-demo.com', $columns['value']);
        $application->db->getDB_Result($query);

    # Orders department e-mail "from"
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('Avactis Store CR Department', $columns['value']);
        $application->db->getDB_Result($query);

    # Store common page titles
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_STORE_OWNER_PAGE_TITLE, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('Avactis Demo Store', $columns['value']);
        $application->db->getDB_Result($query);

    # Cache Level Default Value
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_CACHE_LEVEL, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('2', $columns['value']);
        $application->db->getDB_Result($query);

    # Cache Level Default Value ????????                          :)
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_DB_VERSION, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue(PRODUCT_VERSION_NUMBER, $columns['value']);
        $application->db->getDB_Result($query);

    # related products per line
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_RP_PER_LINE, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('3', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_RP_RANDOM_CHECKBOX, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_RP_RANDOM_THRESHOLD, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('3', $columns['value']);
        $application->db->getDB_Result($query);

    # featured products per line
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_FP_PER_LINE, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('3', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_FP_RANDOM_CHECKBOX, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_FP_RANDOM_THRESHOLD, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('3', $columns['value']);
        $application->db->getDB_Result($query);

    # bestsellers per line
        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_BS_PER_LINE, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('3', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_BS_RANDOM_CHECKBOX, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_BS_RANDOM_THRESHOLD, $columns['name']);
        $query->addInsertValue('integer', $columns['type']);
        $query->addInsertValue('3', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_NEWSLETTERS_SIGNATURE, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(SYSCONFIG_CHECKOUT_FORM_HASH, $columns['name']);
        $query->addInsertValue('string', $columns['type']);
        $query->addInsertValue('', $columns['value']);
        $application->db->getDB_Result($query);


        $table = 'credit_card_settings';            #the name of the filled table
        $columns = $tables[$table]['columns'];  #the array of field names of the table

        ##### List of credit/debit cards accepted
        $query = new DB_Insert($table);
        $query->addInsertValue('Visa', $columns['name']);
        $query->addInsertValue('Visa', $columns['tag']);
        $query->addInsertValue('1', $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('MasterCard', $columns['name']);
        $query->addInsertValue('MasterCard', $columns['tag']);
        $query->addInsertValue('2', $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Discover', $columns['name']);
        $query->addInsertValue('Discover', $columns['tag']);
        $query->addInsertValue('3', $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('American Express', $columns['name']);
        $query->addInsertValue('Amex', $columns['tag']);
        $query->addInsertValue('4', $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Maestro', $columns['name']);
        $query->addInsertValue('Maestro', $columns['tag']);
        $query->addInsertValue('5', $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Solo', $columns['name']);
        $query->addInsertValue('Solo', $columns['tag']);
        $query->addInsertValue('6', $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Diners Club', $columns['name']);
        $query->addInsertValue('DC', $columns['tag']);
        $query->addInsertValue('7', $columns['sort_order']);
        $query->addInsertValue('false', $columns['visible']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('FirePay', $columns['name']);
        $query->addInsertValue('FirePay', $columns['tag']);
        $query->addInsertValue('8', $columns['sort_order']);
        $query->addInsertValue('false', $columns['visible']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('JCB', $columns['name']);
        $query->addInsertValue('JCB', $columns['tag']);
        $query->addInsertValue('9', $columns['sort_order']);
        $query->addInsertValue('false', $columns['visible']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Laser', $columns['name']);
        $query->addInsertValue('Laser', $columns['tag']);
        $query->addInsertValue('10', $columns['sort_order']);
        $query->addInsertValue('false', $columns['visible']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Switch', $columns['name']);
        $query->addInsertValue('Switch', $columns['tag']);
        $query->addInsertValue('11', $columns['sort_order']);
        $query->addInsertValue('false', $columns['visible']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Visa Delta', $columns['name']);
        $query->addInsertValue('VisaD', $columns['tag']);
        $query->addInsertValue('12', $columns['sort_order']);
        $query->addInsertValue('false', $columns['visible']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue('Visa Electron', $columns['name']);
        $query->addInsertValue('VisaE', $columns['tag']);
        $query->addInsertValue('13', $columns['sort_order']);
        $query->addInsertValue('false', $columns['visible']);
        $application->db->getDB_Result($query);


        $table = 'credit_card_attributes_to_types';
        $columns = $tables[$table]['columns'];

        $cards = array(5, 6, 11); // Solo, Maestro, Switch
        foreach ($cards as $i)
        {
          // turn off...
            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['type']);
            $query->addInsertValue('14', $columns['attr']);      // CVC2
            $query->addInsertValue('0', $columns['visible']);
            $query->addInsertValue('0', $columns['required']);
            $application->db->getDB_Result($query);

            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['type']);
            $query->addInsertValue('15', $columns['attr']);      // Exp Month
            $query->addInsertValue('0', $columns['visible']);
            $query->addInsertValue('0', $columns['required']);
            $application->db->getDB_Result($query);

            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['type']);
            $query->addInsertValue('16', $columns['attr']);      // Exp Year
            $query->addInsertValue('0', $columns['visible']);
            $query->addInsertValue('0', $columns['required']);
            $application->db->getDB_Result($query);

          // turn on...
            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['type']);
            $query->addInsertValue('22', $columns['attr']);      // From Month
            $query->addInsertValue('1', $columns['visible']);
            $query->addInsertValue('1', $columns['required']);
            $application->db->getDB_Result($query);

            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['type']);
            $query->addInsertValue('23', $columns['attr']);      // From Year
            $query->addInsertValue('1', $columns['visible']);
            $query->addInsertValue('1', $columns['required']);
            $application->db->getDB_Result($query);

            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['type']);
            $query->addInsertValue('24', $columns['attr']);      // Issue Number
            $query->addInsertValue('1', $columns['visible']);
            $query->addInsertValue('1', $columns['required']);
            $application->db->getDB_Result($query);
        }


        $table = 'mail_settings';            #the name of the filled table
        $columns = $tables[$table]['columns'];  #the array of field names of the table

        ##### List of credit/debit cards accepted
        $query = new DB_Insert($table);
        $query->addInsertValue('MAIL_TYPE', $columns['name']);
        $query->addInsertValue('1', $columns['value']);
        $application->db->getDB_Result($query);

        ##### List of credit/debit cards accepted
        $query = new DB_Insert($table);
        $query->addInsertValue('MAIL_HOST', $columns['name']);
        $query->addInsertValue('mail.domain.com', $columns['value']);
        $application->db->getDB_Result($query);

        ##### List of credit/debit cards accepted
        $query = new DB_Insert($table);
        $query->addInsertValue('MAIL_PORT', $columns['name']);
        $query->addInsertValue('25', $columns['value']);
        $application->db->getDB_Result($query);

        ##### List of credit/debit cards accepted
        $query = new DB_Insert($table);
        $query->addInsertValue('MAIL_AUTH', $columns['name']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);

        ##### List of credit/debit cards accepted
        $query = new DB_Insert($table);
        $query->addInsertValue('MAIL_USER', $columns['name']);
        $query->addInsertValue('user@domain.com', $columns['value']);
        $application->db->getDB_Result($query);

        ##### List of credit/debit cards accepted
        $query = new DB_Insert($table);
        $query->addInsertValue('MAIL_PASS', $columns['name']);
        $query->addInsertValue('password', $columns['value']);
        $application->db->getDB_Result($query);

        $group_info = array('GROUP_NAME'        => 'DEBUG_STORE_BLOCK',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CFG', 'DEBUG_STORE_BLOCK_NAME'),
                                                            'DESCRIPTION'   => array('CFG', 'DEBUG_STORE_BLOCK_DESCR')),
                            'GROUP_VISIBILITY'  => 'SHOW');
        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ADD_TEMPLATE_PATHES',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADD_TEMPLATE_PATHES_NAME'),
                                                       'DESCRIPTION' => array('CFG', 'ADD_TEMPLATE_PATHES_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'Yes',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADD_TEMPLATE_PATHES_YES_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADD_TEMPLATE_PATHES_YES_NAME') ),
                                       ),
                                 array(  'VALUE' => 'No',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADD_TEMPLATE_PATHES_NO_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADD_TEMPLATE_PATHES_NO_NAME') ),
                                       ),
                           ),
                         'PARAM_CURRENT_VALUE' => 'No',
                         'PARAM_DEFAULT_VALUE' => 'No',
        );
        modApiFunc('Settings','createParam', $param_info);

        // Timeline group
        $group_info = array('GROUP_NAME'        => 'TIMELINE',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('TL', 'ADV_CFG_TIMELINE_GROUP_NAME'),
                                                            'DESCRIPTION'   => array('TL', 'ADV_CFG_TIMELINE_GROUP_DESCR')),
                            'GROUP_VISIBILITY'  => 'SHOW');
        modApiFunc('Settings','createGroup', $group_info);

        // Tax Settings group
        $group_info = array('GROUP_NAME'        => 'TAXES_PARAMS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('TAXEXEMPTS', 'TAXES_GROUP_NAME'),
                                                            'DESCRIPTION'   => array('TAXEXEMPTS', 'TAXES_GROUP_DESCR')),
                            'GROUP_VISIBILITY'  => 'SHOW');
        modApiFunc('Settings','createGroup', $group_info);

        //Admin session duration
        $group_info = array('GROUP_NAME'        => 'ADMIN_SESSION_DURATION',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CFG', 'ADMIN_SESSION_CFG_NAME'),
                                                            'DESCRIPTION'   => array('CFG', 'ADMIN_SESSION_CFG_DESC')),
                            'GROUP_VISIBILITY'  => 'SHOW');

        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ADM_SESSION_DURATION_VALUE',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADMIN_SESSION_CFG_NAME'),
                                                       'DESCRIPTION' => array('CFG', 'ADMIN_SESSION_CFG_PARAM_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => '3600',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADM_SESSION_1_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADM_SESSION_1_DESC')),
                                       ),
                                 array(  'VALUE' => '6400',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADM_SESSION_2_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADM_SESSION_2_DESC')),
                                       ),
                                 array(  'VALUE' => '14400',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADM_SESSION_4_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADM_SESSION_4_DESC')),
                                       ),
                                 array(  'VALUE' => '21600',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADM_SESSION_6_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADM_SESSION_6_DESC')),
                                       ),
                                 array(  'VALUE' => '43200',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADM_SESSION_12_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADM_SESSION_12_DESC')),
                                       ),
                                 array(  'VALUE' => '86400',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADM_SESSION_24_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADM_SESSION_24_DESC')),
                                       ),
                                 array(  'VALUE' => '172800',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CFG', 'ADM_SESSION_48_NAME'),
                                                                       'DESCRIPTION' => array('CFG', 'ADM_SESSION_48_DESC')),
                                       )),
                         'PARAM_CURRENT_VALUE' => '172800',
                         'PARAM_DEFAULT_VALUE' => '172800',
        );
        modApiFunc('Settings','createParam', $param_info);

        	//For inserting latest Core version,last_build_date,ttl in settings table
		 $params = array(
						 	'group_name' => 'AVACTIS_LATEST_VERSION',
							'param_name' => 'AVACTIS_LATEST_VERSION',
							'param_description_id' => '0',
							'param_type' => 'STRING',
							'param_validator_class' => 'Validator',
							'param_validator_method' => 'alwaysValid',
							'param_current_value' => '',
							'param_default_value' => '',
						);

		 execQuery('INSERT_SETTING',$params);

		 $params = array(
						 	'group_name' => 'MARKETPLACE_LAST_BUILD_DATE',
							'param_name' => 'MARKETPLACE_LAST_BUILD_DATE',
							'param_description_id' => '0',
							'param_type' => 'STRING',
							'param_validator_class' => 'Validator',
							'param_validator_method' => 'alwaysValid',
							'param_current_value' => '',
							'param_default_value' => '',
						);

		 execQuery('INSERT_SETTING',$params);

		 $params = array(
						 	'group_name' => 'MARKETPLACE_TTL',
							'param_name' => 'MARKETPLACE_TTL',
							'param_description_id' => '0',
							'param_type' => 'STRING',
							'param_validator_class' => 'Validator',
							'param_validator_method' => 'alwaysValid',
							'param_current_value' => '1440',
							'param_default_value' => '1440',
						);

		 execQuery('INSERT_SETTING',$params);
                 do_dbDelta(Configuration::getQueries());
    }

    /**
     * Installs the specified module in the system.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Configuration::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Configuration::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Create options table during upgradation using avactis_db_delta query structure.
     *
     * getQueries() function is called from get_table function.
     * e.g. avactis_db_delta(Configuration::getQueries())
     *
     * @return query string
     */
     function getQueries(){
		   	global $application;
		   	$table_prefix=$application->getAppIni('DB_TABLE_PREFIX');
		   	$queries="CREATE TABLE IF NOT EXISTS {$table_prefix}options
                       (option_id int NOT NULL auto_increment,
                        option_name varchar(255) NOT NULL DEFAULT '',
                        option_value longtext NOT NULL DEFAULT '',
                        autoload varchar(16) NOT NULL DEFAULT 'yes' ,
                        PRIMARY KEY  (option_id))";
                        return $queries;
    }

    /**
     * Called in UpdateModule function using call_user_func
     * Update specified module
     */
    function update(){
		 do_dbDelta(Configuration::getQueries());
     }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of meta description of the table:
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
     *          'fn1'       # several key fields may be used, e.g. - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      # several fields can be used in one index, e.g. - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -  the meta description of module tables
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tables = array ();

        $store_settings = 'store_settings';
        $tables[$store_settings] = array();
        $tables[$store_settings]['columns'] = array
            (
                'id'                => 'store_settings.variable_id'
               ,'name'              => 'store_settings.variable_name'
               ,'type'              => 'store_settings.variable_type'
               ,'value'             => 'store_settings.variable_value'
            );
        $tables[$store_settings]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR20 .' NOT NULL DEFAULT \'\''
               ,'value'             => DBQUERY_FIELD_TYPE_LONGTEXT .' NOT NULL '
            );
        $tables[$store_settings]['primary'] = array
            (
                'id'
            );
        $tables[$store_settings]['indexes'] = array
            (
                'IDX_lrl' => 'name'
            );

        $credit_card_settings = 'credit_card_settings';
        $tables[$credit_card_settings] = array();
        $tables[$credit_card_settings]['columns'] = array
            (
                'id'                => 'credit_card_settings.credit_card_type_id'
               ,'name'              => 'credit_card_settings.credit_card_type_name'
               ,'tag'               => 'credit_card_settings.credit_card_type_tag'
               ,'sort_order'        => 'credit_card_settings.credit_card_type_sort_order'
               ,'visible'           => 'credit_card_settings.credit_card_type_visible'
            );
        $tables[$credit_card_settings]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''
               ,'tag'               => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''
               ,'sort_order'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'visible'           => DBQUERY_FIELD_BOOLEAN_DEFAULT_TRUE
            );
        $tables[$credit_card_settings]['primary'] = array
            (
                'id'
            );

        $layout_https_settings = 'layout_https_settings';
        $tables[$layout_https_settings] = array();
        $tables[$layout_https_settings]['columns'] = array
            (
                'id'                    => 'layout_https_settings.layout_https_settings_id'
               ,'layout_full_file_name' => 'layout_https_settings.layout_https_settings_layout_full_file_name'

               ,'catalog'               => 'layout_https_settings.layout_https_settings_catalog'
               ,'cart'                  => 'layout_https_settings.layout_https_settings_cart'
               ,'checkout'              => 'layout_https_settings.layout_https_settings_checkout'
               ,'download'              => 'layout_https_settings.layout_https_settings_download'
               ,'customer_data'         => 'layout_https_settings.layout_https_settings_customer_data'
               ,'customer_login'        => 'layout_https_settings.layout_https_settings_customer_login'
               ,'whole_cz'              => 'layout_https_settings.layout_https_settings_whole_cz'
            );
        $tables[$layout_https_settings]['types'] = array
            (
                'id'                    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'layout_full_file_name' => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''

               ,'catalog'               => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'cart'                  => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'checkout'              => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'download'              => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'customer_data'         => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'customer_login'        => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE

               ,'whole_cz'              => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
            );
        $tables[$layout_https_settings]['primary'] = array
            (
                'id'
            );
        $tables[$layout_https_settings]['indexes'] = array
            (
                'UNIQUE IDX_unique' => 'layout_full_file_name'
            );

        $tbl = 'settings';
        $tables[$tbl] = array();
        $tables[$tbl]['columns'] = array
            (
                'group_name'              => $tbl.'.group_name',
                'param_name'              => $tbl.'.param_name',
                'param_description_id'    => $tbl.'.param_description_id',
                'param_type'              => $tbl.'.param_type',
                'param_validator_class'   => $tbl.'.param_validator_class',
                'param_validator_method'  => $tbl.'.param_validator_method',
                'param_current_value'     => $tbl.'.param_current_value',
                'param_default_value'     => $tbl.'.param_default_value',
            );
        $tables[$tbl]['types'] = array
            (
                'group_name'            => DBQUERY_FIELD_TYPE_CHAR100 .' NOT NULL DEFAULT \'\''
               ,'param_name'            => DBQUERY_FIELD_TYPE_CHAR100 .' NOT NULL DEFAULT \'\''
               ,'param_description_id'  => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'param_type'            => DBQUERY_FIELD_TYPE_ENUM_PARAM_TYPES
               ,'param_validator_class' => DBQUERY_FIELD_TYPE_CHAR255
               ,'param_validator_method'=> DBQUERY_FIELD_TYPE_CHAR255
               ,'param_current_value'   => DBQUERY_FIELD_TYPE_LONGTEXT .' NOT NULL '
               ,'param_default_value'   => DBQUERY_FIELD_TYPE_LONGTEXT .' NOT NULL '
            );
        $tables[$tbl]['primary'] = array
            (
                'group_name', 'param_name'
            );
        $tables[$tbl]['indexes'] = array
            (
            );

        $tbl = 'settings_descriptions';
        $tables[$tbl] = array();
        $tables[$tbl]['columns'] = array
            (
                'description_id'                => $tbl.'.description_id',
                'name_module_short_name'        => $tbl.'.name_module_short_name',
                'name_resource_name'            => $tbl.'.name_resource_name',
                'description_module_short_name' => $tbl.'.description_module_short_name',
                'description_resource_name'     => $tbl.'.description_resource_name',
            );
        $tables[$tbl]['types'] = array
            (
                'description_id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment',
                'name_module_short_name'        => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\'',
                'name_resource_name'            => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\'',
                'description_module_short_name' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\'',
                'description_resource_name'     => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\'',
            );
        $tables[$tbl]['primary'] = array
            (
            );
        $tables[$tbl]['indexes'] = array
            (
                'IDX_description_id'            => 'description_id',
            );

        $tbl = 'settings_list_values';
        $tables[$tbl] = array();
        $tables[$tbl]['columns'] = array
            (
                'group_name'                        => $tbl.'.group_name',
                'param_name'                        => $tbl.'.param_name',
                'param_list_value'                  => $tbl.'.param_list_value',
                'param_list_value_description_id'   => $tbl.'.param_list_value_description_id',
            );
        $tables[$tbl]['types'] = array
            (
                'group_name'                        => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\'',
                'param_name'                        => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\'',
                'param_list_value'                  => DBQUERY_FIELD_TYPE_LONGTEXT.' NOT NULL ',
                'param_list_value_description_id'   => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
            );
        $tables[$tbl]['primary'] = array
            (
            );
        $tables[$tbl]['indexes'] = array
            (
                'IDX_group_name'                      => 'group_name',
                'IDX_param_name'                      => 'param_name',
                'IDX_param_list_value_description_id' => 'param_list_value_description_id',
            );

        $tbl = 'settings_groups';
        $tables[$tbl] = array();
        $tables[$tbl]['columns'] = array
            (
                'group_name'            => $tbl.'.group_name',
                'group_description_id'  => $tbl.'.group_description_id',
                'group_visibility'      => $tbl.'.group_visibility',
            );
        $tables[$tbl]['types'] = array
            (
                'group_name'            => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\'',
                'group_description_id'  => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
                'group_visibility'      => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
            );
        $tables[$tbl]['primary'] = array
            (
                'group_name'
            );
        $tables[$tbl]['indexes'] = array
            (
            );

        $mail_settings = 'mail_settings';
        $tables[$mail_settings] = array();
        $tables[$mail_settings]['columns'] = array
            (
                'id'                => 'mail_settings.id'
               ,'name'              => 'mail_settings.parameter_name'
               ,'value'             => 'mail_settings.parameter_value'
            );
        $tables[$mail_settings]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''
               ,'value'             => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''
            );
        $tables[$mail_settings]['primary'] = array
            (
                'id'
            );

        $attributes = 'credit_card_attributes_to_types';
        $tables[$attributes] = array();
        $tables[$attributes]['columns'] = array
            (
                'id'                => 'credit_card_attributes_to_types.id'
               ,'type'              => 'credit_card_attributes_to_types.type'
               ,'attr'              => 'credit_card_attributes_to_types.attr'
               ,'visible'           => 'credit_card_attributes_to_types.visible'
               ,'required'          => 'credit_card_attributes_to_types.required'
            );
        $tables[$attributes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'type'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'attr'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'visible'           => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'required'          => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
            );
        $tables[$attributes]['primary'] = array
            (
                'id'
            );

        $tblop = 'options';
        $tables[$tblop] = array();
        $tables[$tblop]['columns'] = array
            (
                'option_id'         => $tblop.'.option_id'
                ,'option_name'      => $tblop.'.option_name'
                ,'option_value'     => $tblop.'.option_value'
                ,'autoload'         => $tblop.'.autoload'
            );
        $tables[$tblop]['types'] = array
            (
                'option_id'         => DBQUERY_FIELD_TYPE_INT ." NOT NULL auto_increment"
                ,'option_name'      => DBQUERY_FIELD_TYPE_CHAR256 ." NOT NULL DEFAULT ''"
                ,'option_value'     => DBQUERY_FIELD_TYPE_LONGTEXT ." NOT NULL DEFAULT ''"
                ,'autoload'         => DBQUERY_FIELD_TYPE_CHAR16."NOT NULL DEFAULT 'yes'"
            );
        $tables[$tblop]['primary'] = array
            (
                'option_id'
            );
        $tables[$tblop]['indexes'] = array
            (
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getCZHTTPS($layout_block_name, $settings)
    {
	    if(modApiFunc("look_feel","isFacebook"))
		    return "YES";

	    $default_res = ($settings['whole_cz'] == DB_TRUE) ? "YES" : "NO";
    	$map = modApiFunc("Configuration", "getLayoutSettingNameByCZLayoutSectionNameMap");
        if(isset($map[$layout_block_name]))
        {
        	if(isset($settings[$map[$layout_block_name]]))
        	{
        		return ($settings[$map[$layout_block_name]] == DB_TRUE) ? "YES" : "NO";
        	}
        	else
        	{
        		return $default_res;
        	}
        }
        else
        {
        	return $default_res;
        }
    }

    function getCZSiteHTTPSURL($SiteURL)
    {
    	global $application;
    	//                                - HTTPS               AVACTIS' .
    	//                                - HTTP               AVACTIS' .
    	//                                      SiteURL           .                                            SiteHTTPSURL.

    	$AZ_AVACTIS_ROOT_HTTP_URL =  $application->getAppIni("HTTP_URL_CONFIG.PHP");
        $AZ_AVACTIS_ROOT_HTTPS_URL = $application->getAppIni("HTTPS_URL");

        $url_cz = parse_url($SiteURL);
        $url_core = parse_url($AZ_AVACTIS_ROOT_HTTP_URL);

        $p_cz = explode("/",$url_cz['path']);
        $p_core = explode("/",$url_core['path']);

        foreach ($p_cz as $i => $p)
        {
            if (isset($p_core[$i]) && $p_cz[$i] == $p_core[$i] && !isset($p_cz[$i-1]))
            {
                unset($p_cz[$i]);
                unset($p_core[$i]);
            }
        }

       $Path_CZ = implode("/", $p_cz);
       $Path_Core = implode("/", $p_core);

       $cz_https_url = preg_replace("/".addcslashes($Path_Core,"/")."/i","",$AZ_AVACTIS_ROOT_HTTPS_URL);
       $cz_https_url .= $Path_CZ;

       if(_ml_strcasecmp("https://", _ml_substr($AZ_AVACTIS_ROOT_HTTPS_URL, 0, _ml_strlen("https://"))) !== 0)
       {
           return NULL;
       }
       else
       {
           return $cz_https_url;
       }
    }

    function getLayoutSettingNameByCZLayoutSectionNameMap()
    {
    	return array
        (
            'ProductList'                => 'catalog'
           ,'ProductInfo'                => 'catalog'
           ,'SearchResult'               => 'catalog'

           ,'Cart'                       => 'cart'

           ,'Checkout'                   => 'checkout'

           ,'Download'                   => 'download'

           ,'CustomerPersonalInfo'       => 'customer_data'
           ,'CustomerAccountHome'        => 'customer_data'
           ,'CustomerOrdersHistory'      => 'customer_data'
           ,'CustomerOrderInfo'          => 'customer_data'
           ,'CustomerOrderInvoice'       => 'customer_data'
           ,'CustomerOrderPackingSlip'   => 'customer_data'
           ,'CustomerOrderDownloadLinks' => 'customer_data'

           ,'CustomerSignIn'             => 'customer_login'
           ,'CustomerNewPassword'        => 'customer_login'
           ,'CustomerChangePassword'     => 'customer_login'
           ,'CustomerForgotPassword'     => 'customer_login'
           ,'Registration'               => 'customer_login'
           ,'AccountActivation'          => 'customer_login'

           ,'Closed'                     => 'whole_cz'
           ,'whole_cz'                   => 'whole_cz'
        );
    }

    function getLayoutSettingNameByCZLayoutSectionName($section)
    {
    	$map = modApiFunc("Configuration", "getLayoutSettingNameByCZLayoutSectionNameMap");

    	if(isset($map[$section]))
    	{
    		return $map[$section];
    	}
    	else
    	{
    		return NULL;
    	}
    }

    function getLayoutSettings($layout_file_path = NULL)
    {
        $result = execQuery('SELECT_LAYOUT_HTTPS_SETTINGS', array());

        $array = array();
        for ($i = 0; $i < sizeof($result); $i++)
        {
            $array[$result[$i]['layout_full_file_name']] = $result[$i];
        }

        if($layout_file_path == NULL)
        {
        	return $array;
        }
        else
        {
        	//           :
	        //Search for current layout path entry
	        $target_layout_file_path = NULL;
	        foreach($array as $fpath => $info)
	        {
	            if(file_path_cmp($fpath, $layout_file_path) === 0)
	            {
	                $target_layout_file_path = $fpath;
	                break;
	            }
	        }

	        if($target_layout_file_path !== NULL)
	        {
                return $array[$target_layout_file_path];
	        }
	        else
	        {
	        	return NULL;
	        }
        }
    }

    function setLayoutSettings($settings)
    {
    	foreach($settings as $layout_full_file_name => $info)
    	{
            execQuery('UPDATE_LAYOUT_HTTPS_SETTINGS', $info);
    	}
    }

    /**
     * Returns a real value of system variable.
     */
    function getValue($constant)
    {
        global $application;

        if($constant == STOREFRONT_ACTIVE_SKIN)
        {
        	if (modApiFunc('Look_Feel','isCSSEditor'))
			return $_COOKIE['edit_skin'];
        	if (modApiFunc ('Look_Feel','isFacebook'))
            return 'facebook';
        	if (modApiFunc('Look_Feel','isWP'))
        	return 'wp';
        	if (modApiFunc('Look_Feel','isMobile'))
        	return 'system_mobile';
        }



        if (! isset(self::$cache))
        {
            $result = execQuery('SELECT_STORE_SETTINGS', array());
            foreach ($result as $row)
            {
                switch ($row['type'])
                {
                    case 'integer':
                        $value = intval($row['value']);
                        break;
                    case 'price':
                        $value = floatval($row['value']);
                        break;
                    case 'boolean':
                        $value = (boolean)($row['value']);
                        break;
                    case 'string':
                        $value = (string)($row['value']);
                        break;
                    default:
                        $value = $row['value'];
                        break;
                }
                self::$cache[ $row['name'] ] = $value;
            }
        }

        if (! isset(self::$cache[$constant])) {
            CTrace::wrn('Configuration parameter is undefined: '.$constant);
        }
        return self::$cache[$constant];
    }

    function resetCache()
    {
        self::$cache = null;
    }

    function getCreditCardSettings($visible_only = true)
    {
        //
        global $application;


        $tables = $this->getTables();
        $columns = $tables['credit_card_settings']['columns'];

        $query = new DB_Select();
        $query->addSelectField($columns["id"], "id");
        $query->addSelectField($columns["name"], "name");
        $query->addSelectField($columns["tag"], "tag");
        $query->addSelectField($columns["sort_order"], "sort_order");
        $query->addSelectField($columns["visible"], "visible");
        if($visible_only === true)
        {
            $query->WhereValue($columns["visible"], DB_EQ, DB_TRUE);
        }
        $query->SelectOrder($columns['sort_order']);

        $result = $application->db->getDB_Result($query);
        $res = array();
        foreach ($result as $credit_card_info)
        {
            $res[$credit_card_info["tag"]] = array
            (
                "id"         =>          $credit_card_info["id"]
               ,"sort_order" =>          $credit_card_info["sort_order"]
               ,"name"       =>  (string)$credit_card_info["name"]
               ,"tag"        =>  (string)$credit_card_info["tag"]
               ,"visible"    =>  (string)$credit_card_info["visible"]
            );
        }
        return $res;
    }


    /**
     * function gets current mail settings from DB
     *
     */
    function getMailSettings()
    {
        $query = ExecQuery('SELECT_MAIL_SETTINGS', array());
        foreach ($query as $piece) {
            $settings[$piece["name"]] = $piece["value"];
        }
        return $settings;
    }


    /**
     * function sets mail settings,
     * using $values array
     *
     * @param array $values
     */
    function setMailSettings($values)
    {
        foreach ($values as $name => $value)
        {
            $params = array("name" => $name, "value" => $value);
            ExecQuery("UPDATE_MAIL_SETTINGS", $params);
        }
    }


    /**
     * @ describe the function Configuration->.
     */
    function setValue($value)
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['store_settings']['columns'];

        $values = array();
    	if (is_array($value))
    	{
    	    $values = $value;
    	}
    	else
    	{
    	    list($name, $val) = $value;
    		$values[$name] = $val;
    	}
    	foreach ($values as $name => $value)
    	{
    		$query = new DB_Update('store_settings');
            $query->addUpdateValue($columns["value"], $value);
            $query->WhereValue($columns["name"], DB_EQ, $name);
            $result = $application->db->getDB_Result($query);

            $this->constant_values[$name] = $value;
    	}
    }

    /**
     * @ describe the function Configuration->.
     */
    function setCreditCardSettings($values)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['credit_card_settings']['columns'];

        foreach ($values as $id => $info)
        {
            $query = new DB_Update('credit_card_settings');
            if(isset($info["name"]))
            {
                $query->addUpdateValue($columns["name"], $info["name"]);
            }
            if(isset($info["visible"]))
            {
                $query->addUpdateValue($columns["visible"], $info["visible"]);
            }
            $query->WhereValue($columns["id"], DB_EQ, $id);
            $result = $application->db->getDB_Result($query);
        }
    }

    /**
     *        -                                   .  . .           sort_order
     *                                   .
     *
     */
    function generateCreditCardTypeSortOrder()
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['credit_card_settings']['columns'];
        $query = new DB_Select();
        $query->addSelectField($query->fMax($columns['sort_order']), 'max');
        $result = $application->db->getDB_Result($query);
        return ($result[0]['max']+1);
    }

    function addCreditCardType($new_credit_card_type_name)
    {
        global $application;
        $short_name = preg_replace("/[^a-zA-Z0-9]/", "", $new_credit_card_type_name);
        //                            .
        $new_credit_card_type_tag = "without_validation_" . $short_name . "_" . time();
        $new_credit_card_type_sort_order = $this->generateCreditCardTypeSortOrder();

        $tables =  $this->getTables();
        $table = 'credit_card_settings';
        $columns = $tables[$table]['columns'];
        $query = new DB_Insert($table);
        $query->addInsertValue($new_credit_card_type_name, $columns['name']);
        $query->addInsertValue($new_credit_card_type_tag,  $columns['tag']);
        $query->addInsertValue($new_credit_card_type_sort_order,  $columns['sort_order']);

        $application->db->getDB_Result($query);
    }

    /**
     * Saves the sort of credit card types.
     *
     * @return
     */
    function setCreditCardTypesSortOrder($ccTypesSortOrderArray)
    {
        global $application;

        foreach ($ccTypesSortOrderArray as $sort_order => $cc_type_id)
        {
            $tables = $this->getTables();
            $columns = $tables['credit_card_settings']['columns'];
            $query = new DB_Update('credit_card_settings');
            $query->addUpdateValue($columns['sort_order'], $sort_order);
            $query->WhereValue($columns['id'], DB_EQ, $cc_type_id);
            $application->db->getDB_Result($query);
        }
    }
    /**#@-*/

    /**
     * Get Support Mode.
     *
     * @return
     */
    function getSupportMode($flag=0)
    {
        if (!modApiFunc('Session','is_Set','SupportMode'))
        {
            if (!defined('SUPPORT_MODE'))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return ((modApiFunc('Session','get','SupportMode') & $flag) || defined('SUPPORT_MODE'));
        }
    }

    /**
     * Returns values for system-wide (including CZ) info tags.
     */
    function getTagValue($tag)
    {
        global $application;
        $value = null;
        switch($tag)
        {
            case "StoreOwnerState":
            case "StoreOwnerStateCode":
                //menu_select
                $state_id = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE);
                if((!empty($state_id)) && is_numeric($state_id))
                {
                    if (modApiFunc('Location', 'getCountStatesInCountry', modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY)) > 0)
                    {
                        if ($tag == "StoreOwnerState")
                        {
                            $value = modApiFunc("Location", "getState", $state_id);
                        }
                        else
                        {
                            $value = modApiFunc("Location", "getStateCode", $state_id);
                        }
                    }
                    else
                    {
                        $value = $state_id;
                    }
                }
                else
                {
                    $value = $state_id;
                }
                break;
            case "StoreOwnerCountry":
            case "StoreOwnerCountryCode":
                $country_id = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY);
                if ($tag == "StoreOwnerCountry")
                {
                    $value = modApiFunc("Location", "getCountry", $country_id);
                }
                else
                {
                    $value = modApiFunc("Location", "getCountryCode", $country_id);
                }
                break;
            case "StoreOwnerName":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_NAME);
                break;
            case "StoreOwnerWebsite":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_WEBSITE);
                break;
            case "StoreOwnerPhones":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_PHONES);
                break;
            case "StoreOwnerFax":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_FAX);
                break;
            case "StoreOwnerStreetLine1":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STREET_LINE_1);
                break;
            case "StoreOwnerStreetLine2":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STREET_LINE_2);
                break;
            case "StoreOwnerCity":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_CITY);
                break;
            case "StoreOwnerPostcode":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_POSTCODE);
                break;
            case "StoreOwnerEmail":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_EMAIL);
                break;
            case "StoreOwnerSiteAdministratorEmail":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL);
                break;
            case "StoreOwnerOrdersDepartmentEmail":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL);
                break;
            case "StoreOwnerPageTitle":
                $value = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_PAGE_TITLE);
                break;
            default:
                $value = modApiFunc('Configuration', 'getValue', $tag);
                break;
        }
        return $value;
    }


    function clearAttributesForCardType($type)
    {
        global $application;
        $tables = Configuration::getTables();
        $attributes = $tables['credit_card_attributes_to_types']['columns'];
        $query = new DB_Delete('credit_card_attributes_to_types');
        $query->WhereValue($attributes['type'], DB_EQ, $type);
        $application->db->getDB_Result($query);
    }

    function addAttributesForCardType($attr)
    {
        $cc_id = $attr['type'];
        foreach ($attr['visible'] as $attr_id => $vis)
        {
            $params = array(
                 'type' => $cc_id
                ,'attr' => $attr_id
                ,'visible' => $vis
                ,'required' => $attr['required'][$attr_id]
            );
            execQuery('INSERT_CREDIT_CARD_ATTRIBUTE', $params);
        }
    }

    function getAttributesForCardType($type)
    {
        $hardcoded = array(
             12 => array('visible' => 1, 'required' => 0)
            ,13 => array('visible' => 1, 'required' => 1)
            ,14 => array('visible' => 1, 'required' => 1)
            ,15 => array('visible' => 1, 'required' => 1)
            ,16 => array('visible' => 1, 'required' => 1)
        );

        $rlt = execQuery('SELECT_CREDIT_CARD_ATTRIBUTES_BY_CC_TYPE', array('type' => $type));
        $override_attr = array();
        foreach ($rlt as $el)
        {
            $override_attr[$el['attr']] = $el;
        }

        $ids = modApiFunc('Checkout', 'getPersonInfoAttributeIdList', 4, ALL_ATTRIBUTES);
        foreach ($ids as $id)
        {
            $field = modApiFunc('Checkout', 'getPersonInfoFieldsList', 4, $id);
            $attr[$field['attribute_id']] = $field;

            if (isset($hardcoded[$id]))
            {
                $attr[$field['attribute_id']]['visible']  = $hardcoded[$id]['visible'];
                $attr[$field['attribute_id']]['required'] = $hardcoded[$id]['required'];
            }
            else
            {
                $attr[$field['attribute_id']]['visible']  = 0;
                $attr[$field['attribute_id']]['required'] = 0;
            }

            if (isset($override_attr[$id]['visible']))
                $attr[$field['attribute_id']]['visible'] = $override_attr[$id]['visible'];

            if (isset($override_attr[$id]['visible']))
                $attr[$field['attribute_id']]['required'] = $override_attr[$id]['required'];
        }

        return $attr;
    }

    function getCacheSize($formated=false)
    {
        return 0; // because new cache system can't calculate cache size
        //$cs = $this->getDirectorySize();
        //return ($formated ? $this->sizeFormat($cs) : $cs);
    }

    function getDirectorySize($d=null)
    {
        global $application;
        if ($d == null)
        {
            $path = $application->getAppINI('PATH_CACHE_DIR');
        }
        else
        {
            $path = $d;
        }
        $totalsize = 0;
        if ($handle = opendir ($path))
        {
            while (false !== ($file = readdir($handle)))
            {
                $nextpath = $path . '/' . $file;
                if ($file != '.' && $file != '..' && !is_link ($nextpath) &&
                    is_file ($nextpath) && _ml_strpos($file, '_cache')===0 )
                {
                    $totalsize += filesize ($nextpath);
                }
                else if ($file != '.' && $file != '..' && !is_link ($nextpath) && is_dir($nextpath))
                {
                    $totalsize += $this->getDirectorySize($nextpath);
                }
            }
      }
      closedir ($handle);
      return $totalsize;
    }

    function sizeFormat($size)
    {
        if($size<1024)
        {
            return $size.' '.$this->MessageResources->getMessage("CACHE_SETTINGS_SIZE_B");
        }
        else if($size<(1024*1024))
        {
            $size=round($size/1024,1);
            return $size.' '.$this->MessageResources->getMessage("CACHE_SETTINGS_SIZE_KB");
        }
        else if($size<(1024*1024*1024))
        {
            $size=round($size/(1024*1024),1);
            return $size.' '.$this->MessageResources->getMessage("CACHE_SETTINGS_SIZE_MB");
        }
        else
        {
            $size=round($size/(1024*1024*1024),1);
            return $size.' '.$this->MessageResources->getMessage("CACHE_SETTINGS_SIZE_GB");
        }
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    static $cache = null;

    /**#@-*/

}
?>