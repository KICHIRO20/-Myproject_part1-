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

loadModuleFile('gift_certificate/abstract/gift_certificate_classes.php');
loadModuleFile('gift_certificate/gift_certificate_api.php');

class SELECT_MAX_GC_ID extends DB_Select
{
    function initQuery()
    {
        $tables = GiftCertificateApi::getTables();
        $c = $tables['gc_list']['columns'];

        $this->addSelectTable('gc_list');
        $this->addSelectField($this->fMax($c['gc_purchased_order_id']), 'gc_purchased_order_id');
    }
}

class DELETE_GC_BY_CODE extends DB_Delete
{
    function DELETE_GC_BY_CODE()
    {
        parent :: DB_Delete('gc_list');
    }

    function initQuery($params)
    {
        $tables = GiftCertificateApi::getTables();
        $c = $tables['gc_list']['columns'];

        $this -> WhereValue($c['gc_code'], DB_EQ, $params['gc_code']);
    }
}

class SELECT_GC_LIST_BY_FILTER extends DB_Select
{
    function initQuery($params)
    {
        $tables = GiftCertificateApi::getTables();
        $c = $tables['gc_list']['columns'];

        $this->addSelectField($c["gc_id"], "gc_id");
        $this->addSelectField($c["gc_code"], "gc_code");
        $this->addSelectField($c["gc_from"], "gc_from");
        $this->addSelectField($c["gc_to"], "gc_to");
        $this->addSelectField($c["gc_message"], "gc_message");
        $this->addSelectField($c["gc_amount"], "gc_amount");
        $this->addSelectField($c["gc_remainder"], "gc_remainder");
        $this->addSelectField($c["gc_sendtype"], "gc_sendtype");
        $this->addSelectField($c["gc_date_created"], "gc_date_created");
        $this->addSelectField($c["gc_status"], "gc_status");

        $this->addSelectField($c["gc_fname"],      "gc_fname");
        $this->addSelectField($c["gc_lname"],      "gc_lname");
        $this->addSelectField($c["gc_email"],      "gc_email");
        $this->addSelectField($c["gc_address"],    "gc_address");
        $this->addSelectField($c["gc_city"],       "gc_city");
        $this->addSelectField($c["gc_state_id"],   "gc_state_id");
        $this->addSelectField($c["gc_country_id"], "gc_country_id");
        $this->addSelectField($c["gc_zip"],        "gc_zip");
        $this->addSelectField($c["gc_phone"],      "gc_phone");
        $this->addSelectField($c["gc_purchased_order_id"],      "gc_purchased_order_id");

        if (isset($params['gc_id']) && !empty($params['gc_id']))
        {
            $this->WhereValue($c["gc_id"], DB_EQ, $params['gc_id']);
            $this->WhereAND();
        }
        if (isset($params['gc_code']) && !empty($params['gc_code']))
        {
            $this->WhereValue($c["gc_code"], DB_EQ, $params['gc_code']);
            $this->WhereAND();
        }
        if (isset($params['gc_from']) && !empty($params['gc_from']))
        {
            $this->WhereValue($c["gc_from"], DB_LIKE, '%'.$params['gc_from'].'%');
            $this->WhereAND();
        }
        if (isset($params['gc_to']) && !empty($params['gc_to']))
        {
            $this->WhereValue($c["gc_to"], DB_LIKE, '%'.$params['gc_to'].'%');
            $this->WhereAND();
        }
        if (isset($params['gc_message']) && !empty($params['gc_message']))
        {
            $this->WhereValue($c["gc_message"], DB_LIKE, '%'.$params['gc_message'].'%');
            $this->WhereAND();
        }
        if (isset($params['gc_amount']) && !empty($params['gc_amount']))
        {
            $this->WhereValue($c["gc_amount"], DB_EQ, $params['gc_amount']);
            $this->WhereAND();
        }
        if (isset($params['gc_amount_min']) && !empty($params['gc_amount_min']))
        {
            $this->WhereValue($c["gc_amount"], DB_GTE, $params['gc_amount_min']);
            $this->WhereAND();
        }
        if (isset($params['gc_amount_max']) && !empty($params['gc_amount_max']))
        {
            $this->WhereValue($c["gc_amount"], DB_LTE, $params['gc_amount_max']);
            $this->WhereAND();
        }
        if (isset($params['gc_remainder']) && !empty($params['gc_remainder']))
        {
            $this->WhereValue($c["gc_remainder"], DB_EQ, $params['gc_remainder']);
            $this->WhereAND();
        }
        if (isset($params['gc_remainder_min']) && !empty($params['gc_remaindert_min']))
        {
            $this->WhereValue($c["gc_remainder"], DB_GTE, $params['gc_remainder_min']);
            $this->WhereAND();
        }
        if (isset($params['gc_remainder_max']) && !empty($params['gc_remainder_max']))
        {
            $this->WhereValue($c["gc_remainder"], DB_LTE, $params['gc_remainder_max']);
            $this->WhereAND();
        }
        if (isset($params['gc_sendtype']) && !empty($params['gc_sendtype']))
        {
            $this->WhereValue($c["gc_sendtype"], DB_EQ, $params['gc_sendtype']);
            $this->WhereAND();
        }
        if (isset($params['gc_status']) && !empty($params['gc_status']))
        {
            $this->WhereValue($c["gc_status"], DB_EQ, $params['gc_status']);
            $this->WhereAND();
        }
        if (isset($params['gc_date_created']) && !empty($params['gc_date_created']))
        {
            $this->WhereValue($c["gc_date_created"], DB_EQ, $params['gc_date_created']);
            $this->WhereAND();
        }
        if (isset($params['gc_date_created_min']) && !empty($params['gc_date_created_min']))
        {
            $this->WhereValue($c["gc_date_created"], DB_GTE, $params['gc_date_created_min']);
            $this->WhereAND();
        }
        if (isset($params['gc_date_created_max']) && !empty($params['gc_date_created_max']))
        {
            $this->WhereValue($c["gc_date_created"], DB_LTE, $params['gc_date_created_max']);
            $this->WhereAND();
        }
        if (isset($params['purchased_order_id']) && !empty($params['purchased_order_id']))
        {
            $this->WhereValue($c["gc_purchased_order_id"], DB_EQ, $params['purchased_order_id']);
            $this->WhereAND();
        }

        $this->WhereValue('1', DB_LTE, '1');

        $sort_field = $c["gc_date_created"];
        $order = 'DESC';
        if (isset($params['sort_by']) && !empty($params['sort_by']))
        {
            switch ($params['sort_by'])
            {
                case GC_SORTBY_AMOUNT:
                        $sort_field = $c["gc_amount"];
                    break;
                case GC_SORTBY_DATECREATED:
                        $sort_field = $c["gc_date_created"];
                    break;
                case GC_SORTBY_FROM:
                        $sort_field = $c["gc_from"];
                    break;
                case GC_SORTBY_ID:
                        $sort_field = $c["gc_id"];
                    break;
                case GC_SORTBY_REMAINDER:
                        $sort_field = $c["gc_remainder"];
                    break;
                case GC_SORTBY_SENDTYPE:
                        $sort_field = $c["gc_sendtype"];
                    break;
                case GC_SORTBY_STATUS:
                        $sort_field = $c["gc_status"];
                    break;
                case GC_SORTBY_TO:
                        $sort_field = $c["gc_to"];
                    break;

                default:
                        $sort_field = $c["gc_date_created"];
                    break;
            }

            if (isset($params['sort_order']) && $params['sort_order'] == SORT_DIRECTION_ASC)
                $order = 'ASC';
            else
                $order = 'DESC';
        }
        $this->SelectOrder($sort_field, $order);

        if (isset($params['paginator']))
        {
            $paginator_limits = $params['paginator'];
            if ($paginator_limits !== null && is_array($paginator_limits) === true)
            {
                list($offset,$count) = $paginator_limits;
                $this->SelectLimit($offset,$count);
            }
        }
    }
}

class INSERT_GC_INFO extends DB_Insert
{
    function INSERT_GC_INFO()
    {
        parent::DB_Insert('gc_list');
    }

    function initQuery($params)
    {
        $tables = GiftCertificateApi::getTables();
        $c = $tables['gc_list']['columns'];

        $this->addInsertValue(DB_NULL,                      $c['gc_id']);
        $this->addInsertValue($params["gc_code"],           $c['gc_code']);
        $this->addInsertValue($params["gc_from"],           $c["gc_from"]);
        $this->addInsertValue($params["gc_to"],             $c["gc_to"]);
        $this->addInsertValue($params["gc_message"],        $c["gc_message"]);
        $this->addInsertValue($params["gc_amount"],         $c["gc_amount"]);
        $this->addInsertValue($params["gc_remainder"],      $c["gc_remainder"]);
        $this->addInsertValue($params["gc_sendtype"],       $c["gc_sendtype"]);
        $this->addInsertValue($params["gc_date_created"],   $c["gc_date_created"]);
        $this->addInsertValue($params["gc_status"],         $c["gc_status"]);
        $this->addInsertValue($params["gc_fname"],      $c["gc_fname"]);
        $this->addInsertValue($params["gc_lname"],      $c["gc_lname"]);
        $this->addInsertValue($params["gc_email"],      $c["gc_email"]);
        $this->addInsertValue($params["gc_address"],    $c["gc_address"]);
        $this->addInsertValue($params["gc_city"],       $c["gc_city"]);
        $this->addInsertValue($params["gc_state_id"],   $c["gc_state_id"]);
        $this->addInsertValue($params["gc_country_id"], $c["gc_country_id"]);
        $this->addInsertValue($params["gc_zip"],        $c["gc_zip"]);
        $this->addInsertValue($params["gc_phone"],      $c["gc_phone"]);
        $this->addInsertValue($params["gc_purchased_order_id"],      $c["gc_purchased_order_id"]);
    }
}

class UPDATE_GC_INFO extends DB_Update
{
    function UPDATE_GC_INFO()
    {
        parent::DB_Update('gc_list');
    }

    function initQuery($params)
    {
        $tables = GiftCertificateApi::getTables();
        $c = $tables['gc_list']['columns'];

        if (isset($params['gc_code']))      $this->addUpdateValue($c["gc_code"],        $params['gc_code']);
        if (isset($params['gc_from']))      $this->addUpdateValue($c["gc_from"],        $params["gc_from"]);
        if (isset($params['gc_to']))        $this->addUpdateValue($c["gc_to"],          $params["gc_to"]);
        if (isset($params['gc_message']))   $this->addUpdateValue($c["gc_message"],     $params["gc_message"]);
        if (isset($params['gc_amount']))    $this->addUpdateValue($c["gc_amount"],      $params["gc_amount"]);
        if (isset($params['gc_remainder'])) $this->addUpdateValue($c["gc_remainder"],   $params["gc_remainder"]);
        if (isset($params['gc_sendtype']))  $this->addUpdateValue($c["gc_sendtype"],    $params["gc_sendtype"]);
        if (isset($params['gc_status']))    $this->addUpdateValue($c["gc_status"],      $params["gc_status"]);

        if (isset($params['gc_fname']))         $this->addUpdateValue($c["gc_fname"],      $params["gc_fname"]);
        if (isset($params['gc_lname']))         $this->addUpdateValue($c["gc_lname"],      $params["gc_lname"]);
        if (isset($params['gc_email']))         $this->addUpdateValue($c["gc_email"],      $params["gc_email"]);
        if (isset($params['gc_address']))       $this->addUpdateValue($c["gc_address"],    $params["gc_address"]);
        if (isset($params['gc_city']))          $this->addUpdateValue($c["gc_city"],       $params["gc_city"]);
        if (isset($params['gc_state_id']))      $this->addUpdateValue($c["gc_state_id"],   $params["gc_state_id"]);
        if (isset($params['gc_country_id']))    $this->addUpdateValue($c["gc_country_id"], $params["gc_country_id"]);
        if (isset($params['gc_zip']))           $this->addUpdateValue($c["gc_zip"],        $params["gc_zip"]);
        if (isset($params['gc_phone']))         $this->addUpdateValue($c["gc_phone"],      $params["gc_phone"]);

        if (isset($params['gc_purchased_order_id']))         $this->addUpdateValue($c["gc_purchased_order_id"],      $params["gc_purchased_order_id"]);

        $this->WhereValue($c['gc_id'], DB_EQ, $params["gc_id"]);
    }
}

?>