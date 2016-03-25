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

loadModuleFile('subscriptions/subscriptions_api.php');
loadModuleFile('customer_account/customer_account_api.php');
loadModuleFile('newsletter/newsletter_api.php');
loadModuleFile('checkout/checkout_api.php');

class SUBSCR_GET_SUBSCRIPTION_EMAILS extends DB_Select
{
    function initQuery($params)
    {
        $stables = Subscriptions::getTables();
        $atable = 'email_address';
        $acolumns = $stables[$atable]['columns'];

        $this->addSelectField($acolumns['email']);
        $this->addSelectField($acolumns['lng']);

        if (isset($params['account'])) {
            $ctables = Customer_Account::getTables();
            $ctable = 'ca_customers';
            $ccolumns = & $ctables[$ctable]['columns'];

            $this->WhereField($acolumns['customer_id'], DB_EQ, $ccolumns['customer_id']);
            $this->WhereAND();
            $this->WhereValue($ccolumns['customer_account'], DB_EQ, $params['account']);
        }
        elseif (isset($params['customer_id'])) {
            $this->WhereField($acolumns['customer_id'], DB_EQ, $params['customer_id']);
        }
    }
}

class SUBSCR_GET_SUBSCRIBED_TOPICS_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();

        $ttable = 'subscription_topic';
        $tcolumns = & $tables[$ttable]['columns'];

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $this->addSelectTable($ttable);
        $this->addSelectField($acolumns['email_id']);
        $this->addSelectField($acolumns['email']);
        $this->addSelectField($ecolumns['topic_id']);
        $this->SelectGroup($ecolumns['email_id']);
        $this->SelectGroup($ecolumns['topic_id']);

        $this->addInnerJoin($etable, $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);

        if (isset($params['email_id'])) {
            $this->WhereValue($ecolumns['email_id'], DB_EQ, $email);
        }
        else {
            $this->addInnerJoin($atable, $acolumns['email_id'], DB_EQ, $ecolumns['email_id']);
            $this->Where($acolumns['email'], DB_IN, DBQuery::arrayToIn($params['emails']));
        }
        if (isset($params['signed_in'])) {
            $this->WhereAND();
            $access = array(SUBSCRIPTION_TOPIC_FULL_ACCESS,
                    $params['signed_in'] ? SUBSCRIPTION_TOPIC_REGISTERED_ONLY : SUBSCRIPTION_TOPIC_GUEST_ONLY);
            $this->Where($tcolumns['topic_access'], DB_IN, DBQuery::arrayToIn($access));
        }
        if (! empty($params['active_only'])) {
            $this->WhereAND();
            $this->WhereValue($tcolumns['topic_status'], DB_EQ, SUBSCRIPTION_TOPIC_ACTIVE);
        }
    }
}

// @:
class SUBSCR_GET_SUBSCRIPTION_EMAIL_BY_CUSTOMER extends DB_Select
{
    function initQuery($params)
    {
        $ctables = Customer_Account::getTables();
        $stables = Subscriptions::getTables();

        $ctable = 'ca_customers';
        $atable = 'email_address';

        $ccolumns = $ctables[$ctable]['columns'];
        $acolumns = $stables[$atable]['columns'];

        $this->addSelectField($acolumns['email']);
        $this->addSelectField($acolumns['lng']);
        $this->WhereField($acolumns['customer_id'], DB_EQ, $ccolumns['customer_id']);
        $this->WhereAND();
        $this->WhereValue($ccolumns['customer_account'], DB_EQ, $params['customer']);
    }
}

class SUBSCR_GET_AUTO_SUBSCRIBE_TOPICS extends DB_Select
{
    function initQuery($params = null)
    {
        $tables = Subscriptions::getTables();
        $table = 'subscription_topic';
        $columns = & $tables[$table]['columns'];

        $this->addSelectField($columns['topic_id']);
        $this->WhereValue($columns['topic_auto'], DB_EQ, SUBSCRIPTION_TOPIC_AUTOSUBSCRIBE_YES);
        $this->WhereAND();
        $this->WhereValue($columns['topic_status'], DB_EQ, SUBSCRIPTION_TOPIC_ACTIVE);
        if (is_array($params) && isset($params['access'])) {
            $this->WhereAND();
            $this->Where($columns['topic_access'], DB_IN, $this->arrayToIn($params['access']));
        }
    }
}

class SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER extends DB_Update
{
    function SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER()
    {
        $this->DB_Update('email_address');
    }
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];
        if (isset($params['account'])) {
            $ctables = Customer_Account::getTables();
            $ctable = 'ca_customers';
            $ccolumns = & $ctables[$ctable]['columns'];

            $this->addUpdateTable($ctable);

            $this->addUpdateExpression($acolumns['customer_id'], $ccolumns['customer_id']);
            $this->WhereValue($ccolumns['customer_account'], DB_EQ, $params['account']);
            $this->WhereAND();
        }
        elseif (isset($params['customer_id'])) {
            $this->addUpdateValue($acolumns['customer_id'], $params['customer_id']);
        }
        $this->WhereValue($acolumns['email'], DB_EQ, $params['email']);
    }
}

class SUBSCR_IMPORT_ORDERS_EMAILS extends DB_Insert_Select
{
    function SUBSCR_IMPORT_ORDERS_EMAILS()
    {
        $this->DB_Insert_Select('subscription_temp');
    }
    function initQuery($params)
    {
        $key = $params['key'];

        $otables = Checkout::getTables();
        $atable = 'person_attributes';
        $dtable = 'order_person_data';
        $stables = Subscriptions::getTables();
        $stable = 'subscription_temp';

        $this->setModifiers(DB_IGNORE);
        $this->setInsertFields(array($stables[$stable]['columns']['action_key']));
        $this->setInsertFields(array($stables[$stable]['columns']['email']));

        $squery = new DB_Select($dtable);
        $squery->addSelectField(DBQuery::quoteValue($key));
        $squery->addSelectField(DBQuery::fLower($otables[$dtable]['columns']['value']));
        $squery->addInnerJoin($atable, $otables[$dtable]['columns']['attribute_id'], DB_EQ, $otables[$atable]['columns']['id']);
        $squery->WhereValue($otables[$atable]['columns']['tag'], DB_EQ, 'Email');

        $this->setSelectQuery($squery);
        unset($squery);
    }
}

class SUBSCR_LINK_ORDERS_EMAILS extends DB_Update
{
    function SUBSCR_LINK_ORDERS_EMAILS()
    {
        $this->DB_Update('email_address');
    }
    function initQuery($params = null)
    {
        $stables = Subscriptions::getTables();
        $otables = Checkout::getTables();
        $etable = 'email_address';
        $otable = 'orders';
        $atable = 'person_attributes';
        $dtable = 'order_person_data';
        $this->addUpdateTable($otable);
        $this->addUpdateTable($atable);
        $this->addUpdateTable($dtable);
        $this->addUpdateExpression($stables[$etable]['columns']['customer_id'], $otables[$otable]['columns']['person_id']);
        $this->WhereField($otables[$otable]['columns']['id'], DB_EQ, $otables[$dtable]['columns']['order_id']);
        $this->WhereAND();
        $this->WhereField($stables[$etable]['columns']['email'], DB_EQ, DBQuery::fLower($otables[$dtable]['columns']['value']));
        $this->WhereAND();
        $this->WhereField($otables[$dtable]['columns']['attribute_id'], DB_EQ, $otables[$atable]['columns']['id']);
        $this->WhereAND();
        $this->WhereValue($otables[$atable]['columns']['tag'], DB_EQ, 'Email');
    }
}

class SUBSCR_IMPORT_CUSTOMERS_EMAILS extends DB_Insert_Select
{
    function SUBSCR_IMPORT_CUSTOMERS_EMAILS()
    {
        $this->DB_Insert_Select('subscription_temp');
    }
    function initQuery($params)
    {
        $key = $params['key'];

        $otables = Customer_Account::getTables();
        $atable = 'ca_person_info_attrs';
        $gtable = 'ca_attrs_to_groups';
        $dtable = 'ca_person_info_data';
        $stables = Subscriptions::getTables();
        $stable = 'subscription_temp';

        $this->setModifiers(DB_IGNORE);
        $this->setInsertFields(array($stables[$stable]['columns']['action_key']));
        $this->setInsertFields(array($stables[$stable]['columns']['email']));

        $squery = new DB_Select($dtable);
        $squery->addSelectField(DBQuery::quoteValue($key));
        $squery->addSelectField(DBQuery::fLower($otables[$dtable]['columns']['data_value']));
        $squery->addInnerJoin($gtable, $otables[$dtable]['columns']['ag_id'], DB_EQ, $otables[$gtable]['columns']['ag_id']);
        $squery->addInnerJoin($atable, $otables[$gtable]['columns']['attr_id'], DB_EQ, $otables[$atable]['columns']['attr_id']);
        $squery->WhereValue($otables[$atable]['columns']['attr_name'], DB_EQ, 'Email');

        $this->setSelectQuery($squery);
        unset($squery);
    }
}

class SUBSCR_LINK_CUSTOMER_EMAILS extends DB_Update
{
    function SUBSCR_LINK_CUSTOMER_EMAILS()
    {
        $this->DB_Update('email_address');
    }
    function initQuery($params = null)
    {
        $stables = Subscriptions::getTables();
        $otables = Customer_Account::getTables();
        $etable = 'email_address';
        $atable = 'ca_person_info_attrs';
        $gtable = 'ca_attrs_to_groups';
        $dtable = 'ca_person_info_data';
        $this->addUpdateTable($atable);
        $this->addUpdateTable($gtable);
        $this->addUpdateTable($dtable);
        $this->addUpdateExpression($stables[$etable]['columns']['customer_id'], $otables[$dtable]['columns']['customer_id']);
        $this->WhereField($stables[$etable]['columns']['email'], DB_EQ, DBQuery::fLower($otables[$dtable]['columns']['data_value']));
        $this->WhereAND();
        $this->WhereField($otables[$dtable]['columns']['ag_id'], DB_EQ, $otables[$gtable]['columns']['ag_id']);
        $this->WhereAND();
        $this->WhereField($otables[$gtable]['columns']['attr_id'], DB_EQ, $otables[$atable]['columns']['attr_id']);
        $this->WhereAND();
        $this->WhereValue($otables[$atable]['columns']['attr_name'], DB_EQ, 'Email');
    }
}

class SUBSCR_GET_CUSTOMER_SUBSCRIBED_TO extends DB_Select
{
    function initQuery($params)
    {
        $stables = Subscriptions::getTables();
        $ctables = Customer_Account::getTables();
        $stable = 'subscription_email';
        $atable = 'email_address';
        $ctable = 'ca_customers';
        $this->addSelectField($stables[$atable]['columns']['email']);
        $this->addSelectField($stables[$atable]['columns']['lng']);
        $this->addSelectField($stables[$stable]['columns']['topic_id']);
        $this->WhereField($stables[$stable]['columns']['email_id'], DB_EQ, $stables[$atable]['columns']['email_id']);
        $this->WhereAND();
        $this->WhereField($stables[$atable]['columns']['customer_id'], DB_EQ, $ctables[$ctable]['columns']['customer_id']);
        $this->WhereAND();
        $this->WhereValue($ctables[$ctable]['columns']['customer_account'], DB_EQ, $params['customer']);
    }
}

class SUBSCR_SELECT_TOPIC_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $tcolumns = $tables['subscription_topic']['columns'];
        $ecolumns = $tables['subscription_email']['columns'];

        $this -> addSelectField($tcolumns['topic_id']);

        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $tcolumns['topic_name'],
                                   $tcolumns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');

        $this -> addSelectField($tcolumns['sort_order']);
        $this -> addSelectField($tcolumns['topic_status']);
        $this -> addSelectField($tcolumns['topic_access']);
        $this -> addSelectField($tcolumns['topic_auto']);
        $this -> addSelectField($this -> fCount($ecolumns['email_id']), 'topic_emails');
        $this -> WhereValue($tcolumns['topic_id'], DB_EQ, $params['topic_id']);
        $this -> addLeftJoin('subscription_email', $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        $this -> SelectGroup($tcolumns['topic_id']);
    }
}

class SUBSCR_SELECT_TOPIC_LIST_BY_EMAIL extends DB_Select
{
    function initQuery($params)
    {
        $tables =Subscriptions::getTables();

        $tcolumns = $tables['subscription_topic']['columns'];
        $ecolumns = $tables['subscription_email']['columns'];

        $this -> addSelectField($tcolumns['topic_id']);

        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $tcolumns['topic_name'],
                                   $tcolumns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');

        $this -> addSelectField($tcolumns['sort_order']);
        $this -> addSelectField($tcolumns['topic_status']);
        $this -> addSelectField($tcolumns['topic_access']);
        $this -> addSelectField($tcolumns['topic_auto']);
        $this -> addSelectField($this -> fCount($ecolumns['email_id']), 'topic_emails');
        $this -> addLeftJoin('subscription_email', $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        $this -> SelectGroup($tcolumns['topic_id']);
        $this -> SelectOrder($tcolumns['sort_order'], 'ASC');

        if (!empty($params['for_emails'])) {
            $acolumns = $tables['email_address']['columns'];
            if (!is_array($for_emails))
                $for_emails = array($for_emails);
            $this -> addLeftJoin('email_address', $acolumns['email_id'], DB_EQ, $ecolumns['email_id']);
            $this -> Where($acolumns['email'], DB_IN, $this -> arrayToIn($for_emails));
        }
    }
}

class SUBSCR_SELECT_TOPIC_LIST_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $tcolumns = $tables['subscription_topic']['columns'];
        $ecolumns = $tables['subscription_email']['columns'];

        $this -> addSelectField($tcolumns['topic_id']);

        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $tcolumns['topic_name'],
                                   $tcolumns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');

        $this -> addSelectField($tcolumns['sort_order']);
        $this -> addSelectField($tcolumns['topic_status']);
        $this -> addSelectField($tcolumns['topic_access']);
        $this -> addSelectField($tcolumns['topic_auto']);
        $this -> addSelectField($this -> fCount($ecolumns['email_id']), 'topic_emails');
        $this -> addLeftJoin('subscription_email', $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        $this -> Where($tcolumns['topic_id'], DB_IN, $this -> arrayToIn($params['topics_ids']));
        $this -> SelectGroup($tcolumns['topic_id']);
        $this -> SelectOrder($tcolumns['sort_order'], 'ASC');
    }
}

class SUBSCR_SELECT_TOPIC_BY_KEY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $columns = $tables['subscription_topic']['columns'];
        $ecolumns = $tables['subscription_email']['columns'];
        $pcolumns = $tables['subscription_temp']['columns'];

        $this -> addSelectField($columns['topic_id']);

        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $columns['topic_name'],
                                   $columns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');

        $this -> addSelectField($columns['sort_order']);
        $this -> addSelectField($columns['topic_status']);
        $this -> addSelectField($columns['topic_access']);
        $this -> addSelectField($columns['topic_auto']);
        $this -> addSelectField($this -> fCount($ecolumns['email_id']), 'topic_emails');
        $this -> addSelectField($this -> fCount($pcolumns['email_id']), 'existing_emails');
        $this -> addLeftJoin('subscription_email', $ecolumns['topic_id'], DB_EQ, $columns['topic_id']);
        $this -> addLeftJoinOnConditions('subscription_temp', array(
                 $pcolumns['email_id'], DB_EQ, $ecolumns['email_id'], DB_AND,
                 $pcolumns['action_key'], DB_EQ, $this -> quoteValue($params['key'])));
        $this -> SelectGroup($columns['topic_id']);
        $this -> SelectOrder($columns['sort_order'], 'ASC');
        $this -> Where($columns['topic_id'], DB_IN, $this -> arrayToIn($params['topics_ids']));
    }
}

class SUBSCR_SELECT_CUSTOMER_TOPICS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $columns = $tables['subscription_topic']['columns'];

        $this -> addSelectField($columns['topic_id']);
        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $columns['topic_name'],
                                   $columns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');
        $this -> SelectOrder($columns['sort_order'], 'ASC');
        $this -> WhereValue($columns['topic_status'], DB_EQ, $params['status']);
        $this -> WhereAND();
        $this -> Where($columns['topic_access'], DB_IN, $this -> arrayToIn($params['access']));
    }
}

class SUBSCR_SELECT_SEND_TOPICS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $tcolumns = $tables['subscription_topic']['columns'];
        $ecolumns = $tables['subscription_email']['columns'];

        $this -> addSelectField($tcolumns['topic_id']);

        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $tcolumns['topic_name'],
                                   $tcolumns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');

        $this -> addSelectField($this -> fCount($ecolumns['email_id']), 'topic_emails');
        $this -> addLeftJoin('subscription_email', $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        $this -> SelectGroup($tcolumns['topic_id']);
        $this -> SelectOrder($tcolumns['sort_order'], 'ASC');
        $this -> Where($tcolumns['topic_status'], DB_IN, $this -> arrayToIn($params['access']));
    }
}

class SUBSCR_SELECT_TOPIC_BY_LETTERS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $ntables = Newsletter::getTables();
        $tcolumns = $tables['subscription_topic']['columns'];
        $lcolumns = $ntables['newsletter_topics']['columns'];

        $this -> addSelectField($lcolumns['letter_id']);
        $this -> addSelectField($lcolumns['topic_id']);
        $this -> addInnerJoin('subscription_topic', $lcolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);

        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $tcolumns['topic_name'],
                                   $tcolumns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');

        $this -> Where($lcolumns['letter_id'], DB_IN, $this -> arrayToIn($params['letters_ids']));
        $this -> SelectOrder($tcolumns['sort_order'], 'ASC');
    }
}

class SUBSCR_SELECT_TOPIC_BY_LETTERS_TO_SEND extends DB_Select
{
    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $ntables =Newsletter::getTables();
        $tcolumns = $tables['subscription_topic']['columns'];
        $ecolumns = $tables['subscription_email']['columns'];
        $lcolumns = $ntables['newsletter_topics']['columns'];

        $this -> addSelectField($lcolumns['letter_id']);
        $this -> addSelectField($lcolumns['topic_id']);
        $this -> addInnerJoin('subscription_topic', $lcolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);

        $this -> setMultiLangAlias('_ml_name', 'subscription_topic',
                                   $tcolumns['topic_name'],
                                   $tcolumns['topic_id'], 'Subscriptions');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_name'),
                                'topic_name');

        $this -> addSelectField($this -> fCount($ecolumns['email_id']), 'topic_emails');
        $this -> addLeftJoin('subscription_email', $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        $this -> Where($lcolumns['letter_id'], DB_IN, $this -> arrayToIn($params['letters_ids']));
        $this -> SelectGroup($tcolumns['topic_id']);
        $this -> SelectOrder($tcolumns['sort_order'], 'ASC');
    }
}

class SUBSCR_GET_SUBSCRIPTION_EMAIL_BY_KEY extends DB_Select
{
    function initQuery($params)
    {
    $tables = Subscriptions::getTables();
        $columns = & $tables['subscription_guest']['columns'];

        $this->addSelectField('email');
        $this->WhereValue($columns['subscription_key'], DB_EQ, $params['key']);
        if (isset($params['limit']) && $params['limit'] != "no")
            $this->SelectLimit(0, 1);
    }
}

class SUBSCR_UPDATE_TOPIC extends DB_Update
{
    function SUBSCR_UPDATE_TOPIC()
    {
        parent :: DB_Update('subscription_topic');
    }

    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $columns = $tables['subscription_topic']['columns'];

        $this -> addMultiLangUpdateValue($columns['topic_name'], $params['topic_name'],
                                         $columns['topic_id'], $params['topic_id'], 'Subscriptions');
        $this -> addUpdateValue($columns['topic_access'], $params['topic_access']);
        $this -> addUpdateValue($columns['topic_status'], $params['topic_status']);
        $this -> addUpdateValue($columns['topic_auto'], $params['topic_auto']);
        $this -> WhereValue($columns['topic_id'], DB_EQ, $params['topic_id']);
    }
}

class SUBSCR_INSERT_NEW_TOPIC extends DB_Insert
{
    function SUBSCR_INSERT_NEW_TOPIC()
    {
        parent :: DB_Insert('subscription_topic');
    }

    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $columns = $tables['subscription_topic']['columns'];

        $this -> addMultiLangInsertValue($params['topic_name'], $columns['topic_name'],
                                         $columns['topic_id'], 'Subscriptions');
        $this -> addInsertValue($params['sort_order'], $columns['sort_order']);
        $this -> addInsertValue($params['topic_status'], $columns['topic_status']);
        $this -> addInsertValue($params['topic_access'], $columns['topic_access']);
        $this -> addInsertValue($params['topic_auto'], $columns['topic_auto']);
    }
}

class SUBSCR_DELETE_TOPIC_BY_ID extends DB_Delete
{
    function SUBSCR_DELETE_TOPIC_BY_ID()
    {
        parent :: DB_Delete('subscription_topic');
    }

    function initQuery($params)
    {
        $tables = Subscriptions::getTables();
        $columns = $tables['subscription_topic']['columns'];

        $this -> deleteMultiLangField($columns['topic_name'], $columns['topic_id'], 'Subscriptions');
        $this -> Where($columns['topic_id'], DB_IN, $this -> arrayToIn($params['topics_ids']));
    }
}

class SUBSCR_UNSUBSCRIBE_FROM_ALL extends DB_Delete
{
    function SUBSCR_UNSUBSCRIBE_FROM_ALL()
    {
        parent :: DB_Delete('subscription_email');
    }

    function initQuery($params)
    {
        $tables = Subscriptions::getTables();

        $table = 'subscription_email';
        $columns = & $tables[$table]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $this->addUsingTable($table);
        $this->addUsingTable($atable);
        $this->WhereField($columns['email_id'], DB_EQ, $acolumns['email_id']);
        $this->WhereAND();
        $this->WhereValue($acolumns['email'], DB_EQ, $params['email']);
    }
}

?>