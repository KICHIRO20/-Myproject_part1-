<?php
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $tables = Checkout::getTables();
        $query = new DB_Table_Create($tables);

        # Dump data for table 'order_status'
        $table = 'order_status';
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue('ORDER_STATUS_001', $columns['name']);
        $query->addInsertValue('ORDER_STATUS_DESCR_001', $columns['descr']);
        $query->addInsertValue(1, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue('ORDER_STATUS_002', $columns['name']);
        $query->addInsertValue('ORDER_STATUS_DESCR_002', $columns['descr']);
        $query->addInsertValue(2, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue('ORDER_STATUS_003', $columns['name']);
        $query->addInsertValue('ORDER_STATUS_DESCR_003', $columns['descr']);
        $query->addInsertValue(3, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue('ORDER_STATUS_004', $columns['name']);
        $query->addInsertValue('ORDER_STATUS_DESCR_004', $columns['descr']);
        $query->addInsertValue(4, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue('ORDER_STATUS_005', $columns['name']);
        $query->addInsertValue('ORDER_STATUS_DESCR_005', $columns['descr']);
        $query->addInsertValue(5, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(6, $columns['id']);
        $query->addInsertValue('ORDER_STATUS_006', $columns['name']);
        $query->addInsertValue('ORDER_STATUS_DESCR_006', $columns['descr']);
        $query->addInsertValue(6, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(7, $columns['id']);
        $query->addInsertValue('ORDER_STATUS_007', $columns['name']);
        $query->addInsertValue('ORDER_STATUS_DESCR_007', $columns['descr']);
        $query->addInsertValue(7, $columns['sort']);
        $application->db->getDB_Result($query);

        # Dump data for table 'order_payment_status'
        $table = 'order_payment_status';
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_001', $columns['name']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_DESCR_001', $columns['descr']);
        $query->addInsertValue(1, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_002', $columns['name']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_DESCR_002', $columns['descr']);
        $query->addInsertValue(2, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_003', $columns['name']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_DESCR_003', $columns['descr']);
        $query->addInsertValue(3, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_004', $columns['name']);
        $query->addInsertValue('ORDER_PAYMENT_STATUS_DESCR_004', $columns['descr']);
        $query->addInsertValue(4, $columns['sort']);
        $application->db->getDB_Result($query);

        # Dump data for table 'person_attributes'
        $table = 'person_attributes';
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue('Firstname', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_first_name', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue('Lastname', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_last_name', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue('Email', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_email', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(30, $columns['size']);
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue('Streetline1', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(30, $columns['size']);
        $query->addInsertValue('is_valid_street_line_1', $columns['input_validation_func_name']);        
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue('Streetline2', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(30, $columns['size']);
        $query->addInsertValue('is_valid_street_line_2', $columns['input_validation_func_name']);                
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(6, $columns['id']);
        $query->addInsertValue('City', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_city', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(7, $columns['id']);
        $query->addInsertValue('State', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_state', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(8, $columns['id']);
        $query->addInsertValue('Postcode', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_postcode', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(12, $columns['size']);
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(9, $columns['id']);
        $query->addInsertValue('Country', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(3, $columns['input_type_id']);
        $query->addInsertValue('is_valid_country', $columns['input_validation_func_name']);
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(10, $columns['id']);
        $query->addInsertValue('Phone', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_phone', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);        
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(11, $columns['id']);
        $query->addInsertValue('CommentArea', $columns['tag']);
        $query->addInsertValue(6, $columns['pattern_id']); //String1024
        $query->addInsertValue(2, $columns['input_type_id']); //TEXTAREA
        $query->addInsertValue('is_valid_string1024_null', $columns['input_validation_func_name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(12, $columns['id']);
        $query->addInsertValue('CreditCardType', $columns['tag']);
        $query->addInsertValue(8, $columns['pattern_id']);
        $query->addInsertValue(3, $columns['input_type_id']);
        $query->addInsertValue('is_valid_credit_card_type', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);        
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(13, $columns['id']);
        $query->addInsertValue('CreditCardNumber', $columns['tag']);
        $query->addInsertValue(1, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_credit_card_number', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);        
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(14, $columns['id']);
        $query->addInsertValue('CreditCardVerificationNumber', $columns['tag']);
        $query->addInsertValue(1, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_credit_card_verification_number', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);        
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(15, $columns['id']);
        $query->addInsertValue('ExpirationMonth', $columns['tag']);
        $query->addInsertValue(1, $columns['pattern_id']);
        $query->addInsertValue(3, $columns['input_type_id']);
        $query->addInsertValue('is_valid_month', $columns['input_validation_func_name']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(16, $columns['id']);
        $query->addInsertValue('ExpirationYear', $columns['tag']);
        $query->addInsertValue(1, $columns['pattern_id']);
        $query->addInsertValue(3, $columns['input_type_id']);
        $query->addInsertValue('is_valid_year', $columns['input_validation_func_name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(22, $columns['id']);
        $query->addInsertValue('ValidFromMonth', $columns['tag']);
        $query->addInsertValue(1, $columns['pattern_id']);
        $query->addInsertValue(3, $columns['input_type_id']);
        $query->addInsertValue('is_valid_month_valid_from', $columns['input_validation_func_name']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(23, $columns['id']);
        $query->addInsertValue('ValidFromYear', $columns['tag']);
        $query->addInsertValue(1, $columns['pattern_id']);
        $query->addInsertValue(3, $columns['input_type_id']);
        $query->addInsertValue('is_valid_year_valid_from', $columns['input_validation_func_name']);
        $application->db->getDB_Result($query);
                
        $query = new DB_Insert($table);
        $query->addInsertValue(24, $columns['id']);
        $query->addInsertValue('CreditCardIssueNumber', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_credit_card_issue_number', $columns['input_validation_func_name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(17, $columns['id']);
        $query->addInsertValue('BankAccountName', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_bank_account_name', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(18, $columns['id']);
        $query->addInsertValue('BankRoutingNumber', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_bank_routing_number', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(19, $columns['id']);
        $query->addInsertValue('BankAccountNumber', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_bank_account_number', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(20, $columns['id']);
        $query->addInsertValue('BankStateBranch', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_bank_state_branch', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(21, $columns['id']);
        $query->addInsertValue('BankInternationalBankAccountNumber', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue('is_valid_bank_international_bank_account_number', $columns['input_validation_func_name']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(20, $columns['size']);
        $application->db->getDB_Result($query);
    
        $query = new DB_Insert($table);
        $query->addInsertValue(25, $columns['id']);
        $query->addInsertValue('CommentLine', $columns['tag']);
        $query->addInsertValue(3, $columns['pattern_id']);
        $query->addInsertValue(1, $columns['input_type_id']);
        $query->addInsertValue(0, $columns['min']);
        $query->addInsertValue(128, $columns['max']);
        $query->addInsertValue(30, $columns['size']);
        $query->addInsertValue('is_valid_text_line', $columns['input_validation_func_name']);                
        $application->db->getDB_Result($query);
        
        # Dump data for table 'person_info_types'
        $table = 'person_info_types';
        $columns = $tables[$table]['columns'];

/*
        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue('customerInfo', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_CUSTOMER_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_CUSTOMER_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_CUSTOMER_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);
*/

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue('billingInfo', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_BILLING_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_BILLING_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_BILLING_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue('shippingInfo', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_SHIPPING_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_SHIPPING_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_SHIPPING_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue('creditCardInfo', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_CREDIT_CARD_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_CREDIT_CARD_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_CREDIT_CARD_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue('bankAccountInfo', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_BANK_ACCOUNT_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_BANK_ACCOUNT_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_BANK_ACCOUNT_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);        
        
        //��� ������ ��� �������� ������ � Checkout Form Editor. �� ����� �� ������
        //  person info variant'�.
        $query = new DB_Insert($table);
        $query->addInsertValue(PERSON_INFO_TYPE_PAYMENT_MODULE_ID, $columns['id']);
        $query->addInsertValue('paymentModule', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_PAYMENT_MODULE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_PAYMENT_MODULE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_PAYMENT_MODULE_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);
        
        //��� ������ ��� �������� ������ � Checkout Form Editor. �� ����� �� ������
        //  person info variant'�.
        $query = new DB_Insert($table);
        $query->addInsertValue(PERSON_INFO_TYPE_SHIPPING_MODULE_AND_METHOD_ID, $columns['id']);
        $query->addInsertValue('shippingModuleAndMethod', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_SHIPPING_MODULE_AND_METHOD_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_SHIPPING_MODULE_AND_METHOD_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_SHIPPING_MODULE_AND_METHOD_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);
        
//        $query = new DB_Insert($table);
//        $query->addInsertValue(PERSON_INFO_TYPE_STORE_OWNER_INFO_ID, $columns['id']);
//        $query->addInsertValue('storeOwnerInfo', $columns['tag']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_STORE_OWNER_INFORMATION_NAME'), $columns['name']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_STORE_OWNER_INFORMATION_DESCRIPTION'), $columns['descr']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_STORE_OWNER_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
//        $application->db->getDB_Result($query);

        # Dump data for table 'person_info_variants'
        $table = 'person_info_variants';
        $columns = $tables[$table]['columns'];

/*
        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue(1, $columns['type_id']);
        $query->addInsertValue('default', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_CUSTOMER_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_CUSTOMER_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_CUSTOMER_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);
*/

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue(2, $columns['type_id']);
        $query->addInsertValue('default', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_BILLING_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_BILLING_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_BILLING_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue(3, $columns['type_id']);
        $query->addInsertValue('default', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_SHIPPING_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_SHIPPING_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_SHIPPING_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue(4, $columns['type_id']);
        $query->addInsertValue('default', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_CREDIT_CARD_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_CREDIT_CARD_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_CREDIT_CARD_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue(5, $columns['type_id']);
        $query->addInsertValue('default', $columns['tag']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_BANK_ACCOUNT_INFORMATION_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_BANK_ACCOUNT_INFORMATION_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_BANK_ACCOUNT_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
        $application->db->getDB_Result($query);
        
//        $query = new DB_Insert($table);
//        $query->addInsertValue(6, $columns['id']);
//        $query->addInsertValue(PERSON_INFO_TYPE_STORE_OWNER_INFO_ID, $columns['type_id']);
//        $query->addInsertValue('default', $columns['tag']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_STORE_OWNER_INFORMATION_NAME'), $columns['name']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_STORE_OWNER_INFORMATION_DESCRIPTION'), $columns['descr']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_PERSON_INFO_VARIANT_DEFAULT_STORE_OWNER_INFORMATION_VISIBLE_NAME'), $columns['visible_name']);
//        $application->db->getDB_Result($query);

        # Dump data for table 'person_info_types_to_attributes'
        $table = 'person_info_variants_to_attributes';
        $columns = $tables[$table]['columns'];

/*
        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(1, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_FIRSTNAME_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_FIRSTNAME_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(1, $columns['sort']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(2, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_LASTNAME_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_LASTNAME_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(2, $columns['sort']);
        $application->db->getDB_Result($query);        

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(3, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_EMAIL_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_EMAIL_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(3, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(9, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_COUNTRY_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_COUNTRY_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(4, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(8, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_POSTCODE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_POSTCODE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(5, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(7, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_STATE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_STATE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(6, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(6, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_CITY_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_CITY_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(7, $columns['sort']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(4, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_STREETLINE1_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_STREETLINE1_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(8, $columns['sort']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(5, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_STREETLINE2_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_STREETLINE2_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(9, $columns['sort']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['variant_id']);
        $query->addInsertValue(10, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_PHONE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CUSTOMER_INFO_PHONE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(10, $columns['sort']);
        $application->db->getDB_Result($query);
*/


        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(1, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_FIRSTNAME_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_FIRSTNAME_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(1, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(2, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_LASTNAME_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_LASTNAME_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(2, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);        

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(3, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_EMAIL_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_EMAIL_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(3, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(9, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_COUNTRY_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_COUNTRY_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(4, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(8, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_POSTCODE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_POSTCODE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(5, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(7, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_STATE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_STATE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(6, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(6, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_CITY_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_CITY_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(7, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(4, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_STREETLINE1_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_STREETLINE1_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(8, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(5, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_STREETLINE2_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_STREETLINE2_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(9, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(10, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_PHONE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_PHONE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(10, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
                
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(25, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_COMMENT_LINE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_COMMENT_LINE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(11, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
                
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['variant_id']);
        $query->addInsertValue(11, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_COMMENT_AREA_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BILLING_INFO_COMMENT_AREA_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(12, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        


        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(1, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_FIRSTNAME_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_FIRSTNAME_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(1, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(2, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_LASTNAME_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_LASTNAME_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(2, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);        

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(3, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_EMAIL_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_EMAIL_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(3, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(9, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_COUNTRY_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_COUNTRY_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(4, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(8, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_POSTCODE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_POSTCODE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(5, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(7, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_STATE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_STATE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(6, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(6, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_CITY_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_CITY_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(7, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(4, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_STREETLINE1_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_STREETLINE1_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(8, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(5, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_STREETLINE2_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_STREETLINE2_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(9, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(10, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_PHONE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_PHONE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(10, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(25, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_COMMENT_LINE_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_COMMENT_LINE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(11, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['variant_id']);
        $query->addInsertValue(11, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_COMMENT_AREA_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_SHIPPING_INFO_COMMENT_AREA_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(0, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(12, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(12, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_TYPE'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_TYPE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(1, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(13, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_NUMBER'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_NUMBER_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(2, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(14, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_VERIFICATION_CODE'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_VERIFICATION_CODE_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(3, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(15, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_EXPIRY_MONTH'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_EXPIRY_MONTH_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(4, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(16, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_EXPIRY_YEAR'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_EXPIRY_YEAR_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(5, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(22, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_FROM_MONTH'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_FROM_MONTH_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(6, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(23, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_FROM_YEAR'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_FROM_YEAR_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(7, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['variant_id']);
        $query->addInsertValue(24, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_ISSUE_NUMBER'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_CREDIT_CARD_INFO_CREDIT_CARD_ISSUE_NUMBER_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(1, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(8, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['variant_id']);
        $query->addInsertValue(17, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_ACCOUNT_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_ACCOUNT_NAME_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(1, $columns['required']);
        $query->addInsertValue(1, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['variant_id']);
        $query->addInsertValue(18, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_ROUTING_NUMBER'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_ROUTING_NUMBER_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(2, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['variant_id']);
        $query->addInsertValue(19, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_ACCOUNT_NUMBER'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_ACCOUNT_NUMBER_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(3, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['variant_id']);
        $query->addInsertValue(20, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_BANK_STATE_BRANCH'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_BANK_STATE_BRANCH_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(4, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['variant_id']);
        $query->addInsertValue(21, $columns['attribute_id']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_INTERNATIONAL_BANK_ACCOUNT_NUMBER'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CHECKOUT_BANK_ACCOUNT_INFO_INTERNATIONAL_BANK_ACCOUNT_NUMBER_DESCRIPTION'), $columns['descr']);
        $query->addInsertValue(0, $columns['unremovable']);
        $query->addInsertValue(1, $columns['visible']);
        $query->addInsertValue(0, $columns['required']);
        $query->addInsertValue(5, $columns['sort']);
        $query->addInsertValue('', $columns['field_params']);
        $application->db->getDB_Result($query);
        
//        $group_info = array('GROUP_NAME'        => 'UPDATE_INVENTORY_ON_ORDER_CHANGE',
//                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_CHANGE_NAME'),
//                                                            'DESCRIPTION'   => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_CHANGE_DESCR')));
//        modApiFunc('Settings','createGroup', $group_info);
//
//        $param_info = array( 
//                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
//                         'PARAM_NAME'        => 'UPDATE_INVENTORY_ON_ORDER_DELETED',
//                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_NAME'),
//                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_DESCR') ),
//                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
//                         'PARAM_VALUE_LIST'    => array( 
//                                 array(  'VALUE' => 'NO',
//                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_NO_NAME'),
//                                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_NO_NAME') ),
//                                       ), 
//                                 array(  'VALUE' => 'YES',
//                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_YES_NAME'),
//                                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_YES_NAME') ),
//                                       )), 
//                         'PARAM_CURRENT_VALUE' => 'NO',
//                         'PARAM_DEFAULT_VALUE' => 'NO',
//        );
//        modApiFunc('Settings','createParam', $param_info);
//
//        $param_info = array( 
//                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
//                         'PARAM_NAME'        => 'UPDATE_INVENTORY_ON_ORDER_STATUS_CHANGED',
//                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_STATUS_CHANGE_NAME'),
//                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_STATUS_CHANGE_DESCR') ),
//                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
//                         'PARAM_VALUE_LIST'    => array( 
//                                 array(  'VALUE' => 'NO',
//                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_NO_NAME'),
//                                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_NO_NAME') ),
//                                       ), 
//                                 array(  'VALUE' => 'YES',
//                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_YES_NAME'),
//                                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_INVENTORY_ON_ORDER_DELETED_YES_NAME') ),
//                                       )), 
//                         'PARAM_CURRENT_VALUE' => 'NO',
//                         'PARAM_DEFAULT_VALUE' => 'NO',
//        );
//        modApiFunc('Settings','createParam', $param_info);
        
        $param_info = array( 
                         'GROUP_NAME'        => 'TIMELINE',
                         'PARAM_NAME'        => 'LOG_ORDER_CREATION',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_ORDER_CREATION_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_ORDER_CREATION_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_ORDER_CREATION_NO'),
                                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_ORDER_CREATION_NO') ),
                                       ), 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_ORDER_CREATION_YES'),
                                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_ORDER_CREATION_YES') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);
        
        
        $param_info = array( 
                         'GROUP_NAME'        => 'TIMELINE',
                         'PARAM_NAME'        => 'LOG_PM_INPUT',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_PM_INPUT_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_PM_INPUT_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_PM_INPUT_NO'),
                                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_PM_INPUT_NO') ),
                                       ), 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_PM_INPUT_YES'),
                                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_PM_INPUT_YES') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);
        
        
        $param_info = array( 
                         'GROUP_NAME'        => 'TIMELINE',
                         'PARAM_NAME'        => 'LOG_PM_OUTPUT',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_PM_OUTPUT_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_PM_OUTPUT_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_PM_OUTPUT_NO'),
                                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_PM_OUTPUT_NO') ),
                                       ), 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'ADV_CFG_LOG_PM_OUTPUT_YES'),
                                                                       'DESCRIPTION' => array('CHCKT', 'ADV_CFG_LOG_PM_OUTPUT_YES') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

        /*New Advanced Settings group: Checkout Process*/
        $group_info = array('GROUP_NAME'        => 'CHECKOUT_PROCESS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CHCKT', 'CHECKOUT_PROCCESS_NAME'),
                                                            'DESCRIPTION'   => array('CHCKT', 'CHECKOUT_PROCCESS_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW');
        
        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'DO_NOT_SHOW_EMPTY_STATE_FIELD',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'DO_NOT_SHOW_EMPTY_STATE_FIELD_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'DO_NOT_SHOW_EMPTY_STATE_FIELD_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'DO_NOT_SHOW_EMPTY_STATE_FIELD_NO'),
                                                                       'DESCRIPTION' => array('CHCKT', 'DO_NOT_SHOW_EMPTY_STATE_FIELD_NO') ),
                                       ), 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'DO_NOT_SHOW_EMPTY_STATE_FIELD_YES'),
                                                                       'DESCRIPTION' => array('CHCKT', 'DO_NOT_SHOW_EMPTY_STATE_FIELD_YES') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);
        
        /*New Advanced Settings group: Quantity Discount*/
        $group_info = array('GROUP_NAME'        => 'QUANTITY_DISCOUNT',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CHCKT', 'QUANTITY_DISCOUNT_NAME'),
                                                            'DESCRIPTION'   => array('CHCKT', 'QUANTITY_DISCOUNT_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW');
        
        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'QUANTITY_DISCOUNT_BEHAVIOR',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'QUANTITY_DISCOUNT_BEHAVIOR_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'QUANTITY_DISCOUNT_BEHAVIOR_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'QUANTITY_DISCOUNT_BEHAVIOR_NO'),
                                                                       'DESCRIPTION' => array('CHCKT', 'QUANTITY_DISCOUNT_BEHAVIOR_NO') ),
                                       ), 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'QUANTITY_DISCOUNT_BEHAVIOR_YES'),
                                                                       'DESCRIPTION' => array('CHCKT', 'QUANTITY_DISCOUNT_BEHAVIOR_YES') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );

        modApiFunc('Settings','createParam', $param_info);

        /*New Advanced Settings group: Visual Interface Settings*/
        $group_info = array('GROUP_NAME'        => 'VISUAL_INTERFACE',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CHCKT', 'VISUAL_INTERFACE_NAME'),
                                                            'DESCRIPTION'   => array('CHCKT', 'VISUAL_INTERFACE_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW');

        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'SEARCH_START_YEAR',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'SEARCH_START_YEAR_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'SEARCH_START_YEAR_DESCR') ),
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => '2001',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_0'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_0')),
                                      ),
                                 array(  'VALUE' => '2002',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_1'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_1')),
                                      ),
                                 array(  'VALUE' => '2003',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_2'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_2')),
                                      ),
                                 array(  'VALUE' => '2004',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_3'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_3')),
                                      ),
                                 array(  'VALUE' => '2005',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_4'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_4')),
                                      ),
                                 array(  'VALUE' => '2006',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_5'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_5')),
                                      ),
                                 array(  'VALUE' => '2007',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_6'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_6')),
                                      ),
                                 array(  'VALUE' => '2008',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_7'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_7')),
                                      ),
                                 array(  'VALUE' => '2009',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_8'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_8')),
                                      ),
                                 array(  'VALUE' => '2010',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_9'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_9')),
                                      ),
                                 array(  'VALUE' => '2011',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_10'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_10')),
                                      ),
                                 array(  'VALUE' => '2012',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_11'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_11')),
                                      ),
                                 array(  'VALUE' => '2013',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_12'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_12')),
                                      ),
                                 array(  'VALUE' => '2014',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_13'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_13')),
                                      ),
                                 array(  'VALUE' => '2015',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_START_14'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_START_14')),
                                       )), 
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_CURRENT_VALUE' => '2008',
                         'PARAM_DEFAULT_VALUE' => '2008',
        );

        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'SEARCH_YEAR_OFFSET',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'SEARCH_YEAR_OFFSET_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'SEARCH_YEAR_OFFSET_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => '0',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_0'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_0')),
                                      ),
                                 array(  'VALUE' => '1',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_1'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_1')),
                                      ),
                                 array(  'VALUE' => '2',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_2'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_2')),
                                      ),
                                 array(  'VALUE' => '3',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_3'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_3')),
                                      ),
                                 array(  'VALUE' => '4',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_4'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_4')),
                                      ),
                                 array(  'VALUE' => '5',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_5'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_5')),
                                      ),
                                 array(  'VALUE' => '6',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_6'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_6')),
                                      ),
                                 array(  'VALUE' => '7',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_7'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_7')),
                                      ),
                                 array(  'VALUE' => '8',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_8'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_8')),
                                      ),
                                 array(  'VALUE' => '9',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_9'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_9')),
                                      ),
                                 array(  'VALUE' => '10',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'YEAR_SHIFT_10'),
                                                                       'DESCRIPTION' => array('CHCKT', 'YEAR_SHIFT_10')),
                                       )), 
                         'PARAM_CURRENT_VALUE' => '2',
                         'PARAM_DEFAULT_VALUE' => '2',
        );

        /*New Advanced Settings group: Affiliate Tracking Settings*/
        $group_info = array('GROUP_NAME'        => 'AFFILIATE_SETTINGS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CHCKT', 'AFFILIATE_SETTINGS_NAME'),
                                                            'DESCRIPTION'   => array('CHCKT', 'AFFILIATE_SETTINGS_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW');

        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'FIRST_ORDER_ONLY',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'FIRST_ORDER_ONLY_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'FIRST_ORDER_ONLY_DESCR') ),
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'FIRST_ORDER_ONLY_YES'),
                                                                       'DESCRIPTION' => array('CHCKT', 'FIRST_ORDER_ONLY_YES')),
                                      ),
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'FIRST_ORDER_ONLY_NO'),
                                                                       'DESCRIPTION' => array('CHCKT', 'FIRST_ORDER_ONLY_NO')),
                                      )),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );

        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'UPDATE_AFFILIATE_ID',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_AFFILIATE_ID_NAME'),
                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_AFFILIATE_ID_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'CURRENT',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_AFFILIATE_ID_CURRENT'),
                                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_AFFILIATE_ID_CURRENT')),
                                      ),
                                 array(  'VALUE' => 'NEW',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CHCKT', 'UPDATE_AFFILIATE_ID_NEW'),
                                                                       'DESCRIPTION' => array('CHCKT', 'UPDATE_AFFILIATE_ID_NEW')),
                                      )),
                         'PARAM_CURRENT_VALUE' => 'CURRENT',
                         'PARAM_DEFAULT_VALUE' => 'CURRENT',
        );

        modApiFunc('Settings','createParam', $param_info);
        
        modApiFunc('EventsManager', 'addEventHandler',
               'OrdersWereUpdated',
               'Checkout',
               'OnOrderStatusCreatedOrUpdatedOrDeleted');
        modApiFunc('EventsManager', 'addEventHandler',
               'OrderCreated',
               'Checkout',
               'OnOrderStatusCreatedOrUpdatedOrDeleted');
        modApiFunc('EventsManager', 'addEventHandler',
               'OrdersWillBeDeleted',
               'Checkout',
               'OnOrderStatusCreatedOrUpdatedOrDeleted');
        modApiFunc('EventsManager', 'addEventHandler',
               'OrderStatusUpdated',
               'Notifications',
               'OnOrderStatusUpdated');
        modApiFunc('EventsManager', 'addEventHandler',
               'CartChanged',
               'Checkout',
               'OnCartChanged');

        # Adding demo payment methods

        $table = 'checkout_pm_sm_settings';
        $columns = $tables[$table]['columns']; 
        
        # COD (Cash on Delivery)          
        $db_insert = new DB_Insert('checkout_pm_sm_settings');
        $db_insert->addInsertValue('AAE28225-3EEF-58E9-318F-92FCA6766E6B', $columns['module_id']);
        $db_insert->addInsertValue('Payment_Module_Cod', $columns['module_class_name']);
        $db_insert->addInsertValue('payment', $columns['module_group']);
        $db_insert->addInsertValue('1', $columns['status_active_value_id']);
        $db_insert->addInsertValue('1', $columns['status_selected_value_id']);
        $db_insert->addInsertValue('1', $columns['sort_order']);
        $application->db->PrepareSQL($db_insert);
        $application->db->DB_Exec();

        # Direct Deposit
        $db_insert = new DB_Insert('checkout_pm_sm_settings');
        $db_insert->addInsertValue('CB532ED3-E330-690E-C5FB-7147B27CA89C', $columns['module_id']);
        $db_insert->addInsertValue('Payment_Module_Direct_Deposit', $columns['module_class_name']);
        $db_insert->addInsertValue('payment', $columns['module_group']);
        $db_insert->addInsertValue('1', $columns['status_active_value_id']);
        $db_insert->addInsertValue('1', $columns['status_selected_value_id']);
        $db_insert->addInsertValue('2', $columns['sort_order']);
        $application->db->PrepareSQL($db_insert);
        $application->db->DB_Exec();

        # Adding demo shipping method (IMPORTANT: settings and methods will be added in appropriate shipping calcualton module (custom shipping rates for now))

        $db_insert = new DB_Insert('checkout_pm_sm_settings');
        $db_insert->addInsertValue('BCE5D24D-666C-43CA-94A0-D6F775903BE2', $columns['module_id']);
        $db_insert->addInsertValue('Shipping_Module_DSR', $columns['module_class_name']);
        $db_insert->addInsertValue('shipping', $columns['module_group']);
        $db_insert->addInsertValue('1', $columns['status_active_value_id']);
        $db_insert->addInsertValue('1', $columns['status_selected_value_id']);
        $db_insert->addInsertValue('3', $columns['sort_order']);
        $application->db->PrepareSQL($db_insert);
        $application->db->DB_Exec(); 


//        $query = new DB_Insert($table);
//        $query->addInsertValue(6, $columns['variant_id']);
//        $query->addInsertValue(9, $columns['attribute_id']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_STORE_OWNER_INFO_COUNTRY_NAME'), $columns['name']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_STORE_OWNER_INFO_COUNTRY_DESCRIPTION'), $columns['descr']);
//        $query->addInsertValue(0, $columns['unremovable']);
//        $query->addInsertValue(1, $columns['visible']);
//        $query->addInsertValue(1, $columns['required']);
//        $query->addInsertValue(1, $columns['sort']);
//        $application->db->getDB_Result($query);
//
//        $query = new DB_Insert($table);
//        $query->addInsertValue(6, $columns['variant_id']);
//        $query->addInsertValue(7, $columns['attribute_id']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_STORE_OWNER_INFO_STATE_NAME'), $columns['name']);
//        $query->addInsertValue($obj->getMessage('CHECKOUT_STORE_OWNER_INFO_STATE_DESCRIPTION'), $columns['descr']);
//        $query->addInsertValue(0, $columns['unremovable']);
//        $query->addInsertValue(1, $columns['visible']);
//        $query->addInsertValue(0, $columns['required']);
//        $query->addInsertValue(2, $columns['sort']);
//        $application->db->getDB_Result($query);
?>