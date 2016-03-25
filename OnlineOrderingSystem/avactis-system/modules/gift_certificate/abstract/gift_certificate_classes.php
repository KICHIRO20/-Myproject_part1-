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

/*
 *              .                                         .
 */
class GiftCertificateStruct
{
    function getMap()
    {
        return array(
            "gc_id" => $this->id,
            "gc_code" => $this->code,
            "gc_from" => $this->from,
            "gc_to" => $this->to,
            'gc_message' => $this->message,
            'gc_amount' => $this->amount,
            'gc_remainder' => $this->remainder,
            'gc_sendtype' => $this->sendtype,
            'gc_date_created' => $this->date_created,
            'gc_status' => $this->status,
            'gc_fname'      => $this->fname,
            'gc_lname'      => $this->lname,
            'gc_email'      => $this->email,
            'gc_address'    => $this->address,
            'gc_city'       => $this->city,
            'gc_state_id'   => $this->state_id,
            'gc_country_id' => $this->country_id,
            'gc_zip'        => $this->zip,
            'gc_phone'      => $this->phone,
            'gc_purchased_order_id'      => $this->purchased_order_id,
        );
    }

    function getEscapedMap()
    {
        $map = $this->getMap();
        $map['gc_from'] = escapeAttrHTML($map['gc_from']);
        $map['gc_to'] = escapeAttrHTML($map['gc_to']);
        $map['gc_message'] = escapeAttrHTML($map['gc_message']);
        $map['gc_fname'] = escapeAttrHTML($map['gc_fname']);
        $map['gc_lname'] = escapeAttrHTML($map['gc_lname']);
        $map['gc_email'] = escapeAttrHTML($map['gc_email']);
        $map['gc_address'] = escapeAttrHTML($map['gc_address']);
        $map['gc_city'] = escapeAttrHTML($map['gc_city']);
        $map['gc_zip'] = escapeAttrHTML($map['gc_zip']);
        $map['gc_phone'] = escapeAttrHTML($map['gc_phone']);
        return $map;
    }

    function initByMap($map)
    {
        if (isset($map['gc_id'])) $this->id = $map['gc_id'];
        if (isset($map['gc_code'])) $this->code = $map['gc_code'];
        if (isset($map['gc_to'])) $this->to = $map['gc_to'];
        if (isset($map['gc_from'])) $this->from = $map['gc_from'];
        if (isset($map['gc_message'])) $this->message = $map['gc_message'];
        if (isset($map['gc_amount'])) $this->amount = (float) $map['gc_amount'];
        if (isset($map['gc_remainder'])) $this->remainder = (float) $map['gc_remainder'];
        if (isset($map['gc_date_created'])) $this->date_created = $map['gc_date_created'];
        if (isset($map['gc_sendtype'])) $this->sendtype = $map['gc_sendtype'];
        if (isset($map['gc_status'])) $this->status = $map['gc_status'];

        if (isset($map['gc_fname'])) $this->fname = $map['gc_fname'];
        if (isset($map['gc_lname'])) $this->lname = $map['gc_lname'];
        if (isset($map['gc_email'])) $this->email = $map['gc_email'];
        if (isset($map['gc_address'])) $this->address = $map['gc_address'];
        if (isset($map['gc_city'])) $this->city = $map['gc_city'];
        if (isset($map['gc_state_id'])) $this->state_id = $map['gc_state_id'];
        if (isset($map['gc_country_id'])) $this->country_id = $map['gc_country_id'];
        if (isset($map['gc_zip'])) $this->zip = $map['gc_zip'];
        if (isset($map['gc_phone'])) $this->phone = $map['gc_phone'];

        if (isset($map['gc_purchased_order_id'])) $this->purchased_order_id = $map['gc_purchased_order_id'];
    }

    var $id = null;
    var $code = null;
    var $to = null;
    var $from = null;
    var $message = null;
    var $amount = null;
    var $remainder = null;
    var $date_created = null;
    var $sendtype = null;
    var $status = null;

    var $fname = null;
    var $lname = null;
    var $email = null;
    var $address = null;
    var $city = null;
    var $state_id = null;
    var $country_id = null;
    var $zip = null;
    var $phone = null;
    var $purchased_order_id = null;
}

class GiftCertificateBase extends GiftCertificateStruct
{
    function GiftCertificateBase()
    {
    }

    function __create()
    {
        execQuery('INSERT_GC_INFO', $this->getMap());
        GiftCertificateLogger::created(new GiftCertificate($this->code));
    }

    function __update()
    {
        $gc_old = new GiftCertificate($this->code);
        execQuery('UPDATE_GC_INFO', $this->getMap());
        $gc_new = new GiftCertificate($this->code);
        GiftCertificateLogger::updated($gc_old, $gc_new);
    }

    function __load()
    {
        $res = execQuery('SELECT_GC_LIST_BY_FILTER', array('gc_code' => $this->code));
        if (empty($res) || !isset($res[0])) return false;

        $this->initByMap($res[0]);
        return true;
    }

    function isError()
    {
        return !empty($this->errors);
    }

    var $errors = array();
}

class GiftCertificateUpdater extends GiftCertificateBase
{
    function GiftCertificateUpdater($gc_code)
    {
        parent::GiftCertificateBase();
        $this->code = $gc_code;
        if ($this->__load() == false)
        {
            $this->errors[] = GC_E_FAILED_LOAD;
        }
    }

    function check()
    {
        $this->errors = array();

        if (empty($this->id))
        {
            $this->errors[] = GC_E_FIELD_ID;
        }

        if (empty($this->code))
        {
            $this->errors[] = GC_E_FIELD_CODE;
        }

        if (empty($this->to))
        {
            $this->errors[] = GC_E_FIELD_TO;
        }

        if (empty($this->from))
        {
            $this->errors[] = GC_E_FIELD_FROM;
        }

        if (Validator::isValidFloat($this->amount) == false || $this->amount < 0.01)
        {
            $this->errors[] = GC_E_FIELD_AMOUNT;
        }
        if (!preg_match('/^[0-9\.]+$/', $this->amount))
        {
            $this->errors[] = GC_E_FIELD_AMOUNT_SEPARATOR;
        }
        $this->amount = floor($this->amount * 100) / 100;

        if (Validator::isValidFloat($this->remainder) == false || $this->remainder < 0)
        {
            $this->errors[] = GC_E_FIELD_REMAINDER;
        }
        $this->remainder = floor($this->remainder * 100) / 100;

        if ($this->remainder > $this->amount)
        {
            $this->errors[] = GC_E_AMOUNT_LESS_REMAINDER;
        }

        if (Validator::isValidInt($this->date_created) == false)
        {
            $this->errors[] = GC_E_FIELD_DATE_CREATED;
        }

        if ($this->sendtype != GC_SENDTYPE_EMAIL && $this->sendtype != GC_SENDTYPE_POST)
        {
            $this->errors[] = GC_E_FIELD_SENDTYPE;
        }

        if ($this->status != GC_STATUS_ACTIVE && $this->status != GC_STATUS_BLOCKED && $this->status != GC_STATUS_PENDING)
        {
            $this->errors[] = GC_E_FIELD_STATUS;
        }

        if ($this->sendtype == GC_SENDTYPE_EMAIL && !Validator::isValidEmail($this->email))
        {
            $this->errors[] = GC_E_FIELD_EMAIL;
        }

        if ($this->sendtype == GC_SENDTYPE_POST)
        {
            if (empty($this->fname))         $this->errors[] = GC_E_FIELD_FNAME;
            if (empty($this->lname))         $this->errors[] = GC_E_FIELD_LNAME;
            if (empty($this->address))       $this->errors[] = GC_E_FIELD_ADDRESS;
            if (empty($this->city))          $this->errors[] = GC_E_FIELD_CITY;
            if (empty($this->zip))           $this->errors[] = GC_E_FIELD_ZIP;
            if (empty($this->country_id))    $this->errors[] = GC_E_FIELD_COUNTRYID;
            if (empty($this->state_id))      $this->errors[] = GC_E_FIELD_STATEID;
        }

        return empty($this->errors);
    }

    function save()
    {
        if ($this->check() == true)
        {
            $this->__update();
            return true;
        }
        else
        {
            return false;
        }
    }
}

class GiftCertificate extends GiftCertificateUpdater
{
    function GiftCertificate($gc_code)
    {
        parent::GiftCertificateUpdater($gc_code);
    }

    function isApplicable()
    {
        //                           ,                                             Active
        if ($this->remainder > 0 && $this->status == GC_STATUS_ACTIVE)
            return true;
        else
            return false;
    }
}

class GiftCertificateCreator extends GiftCertificateBase
{
    function GiftCertificateCreator()
    {
        parent::GiftCertificateBase();
        // default values
        $this->amount = 10.00;
        $this->remainder = $this->amount;
        $this->date_created = time();
        $this->status = GC_STATUS_PENDING;
        $this->from = '';
        $this->to = '';
        $this->message = '';
        $this->sendtype = GC_SENDTYPE_EMAIL;

        $this->fname = '';
        $this->lname = '';
        $this->address = '';
        $this->city = '';
        $this->country_id = 0;
        $this->email = '';
        $this->phone = '';
        $this->state_id = 0;
        $this->zip = '';
        $this->purchased_order_id = '';
    }

    function check()
    {
        $this->errors = array();

        if (empty($this->to))
        {
            $this->errors[] = GC_E_FIELD_TO;
        }

        if (empty($this->from))
        {
            $this->errors[] = GC_E_FIELD_FROM;
        }

        if (Validator::isValidFloat($this->amount) == false || $this->amount < 0.01)
        {
            $this->errors[] = GC_E_FIELD_AMOUNT;
        }
        $this->amount = floor($this->amount * 100) / 100;

        if ($this->sendtype != GC_SENDTYPE_EMAIL && $this->sendtype != GC_SENDTYPE_POST)
        {
            $this->errors[] = GC_E_FIELD_SENDTYPE;
        }

        if ($this->sendtype == GC_SENDTYPE_EMAIL && !Validator::isValidEmail($this->email))
        {
            $this->errors[] = GC_E_FIELD_EMAIL;
        }

        if ($this->sendtype == GC_SENDTYPE_POST)
        {
            if (empty($this->fname))         $this->errors[] = GC_E_FIELD_FNAME;
            if (empty($this->lname))         $this->errors[] = GC_E_FIELD_LNAME;
            if (empty($this->address))       $this->errors[] = GC_E_FIELD_ADDRESS;
            if (empty($this->city))          $this->errors[] = GC_E_FIELD_CITY;
            if (empty($this->zip))           $this->errors[] = GC_E_FIELD_ZIP;
            if (empty($this->country_id))    $this->errors[] = GC_E_FIELD_COUNTRYID;
            if (empty($this->state_id))      $this->errors[] = GC_E_FIELD_STATEID;
        }

        return empty($this->errors);
    }

    function save()
    {
        if ($this->check() == true)
        {
            $this->remainder = $this->amount;
            $this->code = modApiFunc('GiftCertificateApi', 'generateCode');
            $this->__create();
            return true;
        }
        else
        {
            return false;
        }
    }
}


class GiftCertificateFilter
{
    function GiftCertificateFilter()
    {

    }

    function getMap()
    {
        $params = array(
            "gc_id" => $this->id,
            "gc_code" => $this->code,

            "gc_from" => $this->from_like,
            "gc_to" => $this->to_like,
            'gc_message' => $this->message_like,

            'gc_amount' => $this->amount,
            'gc_amount_min' => $this->amount_min,
            'gc_amount_max' => $this->amount_max,

            'gc_remainder' => $this->remainder,
            'gc_remainder_min' => $this->remainder_min,
            'gc_remainder_max' => $this->remainder_max,

            'gc_date_created' => $this->date_created,
            'gc_date_created_min' => $this->date_created_min,
            'gc_date_created_max' => $this->date_created_max,

            'gc_sendtype' => $this->sendtype,
            'gc_status' => $this->status,

            'sort_by' => $this->sort_by,
            'sort_order' => $this->sort_order,
            'paginator' => $this->paginator,

            'purchased_order_id' => $this->purchased_order_id
        );

        if ($this->use_paginator === true)
        {
            $params['paginator'] = null;
            $params['paginator'] = execQueryPaginator('SELECT_GC_LIST_BY_FILTER', $params);
        }
        return $params;
    }

    var $id = null;
    var $code = null;

    var $to_like = null;
    var $from_like = null;
    var $message_like = null;

    var $amount = null;
    var $amount_min = null;
    var $amount_max = null;

    var $remainder = null;
    var $remainder_min = null;
    var $remainder_max = null;

    var $date_created = null;
    var $date_created_min = null;
    var $date_created_max = null;

    var $sendtype = null;
    var $status = null;

    var $sort_by = null;
    var $sort_order = null;

    var $use_paginator = true;
    var $paginator = null;

    var $purchased_order_id = null;
}

class GiftCertificateList
{
    function GiftCertificateList($filter)
    {
        $this->filter = $filter;
        $this->gc_list = execQuery('SELECT_GC_LIST_BY_FILTER', $this->filter->getMap());
    }

    function reset()
    {
        $this->__index = 0;
    }

    function next()
    {
        if (isset($this->gc_list[$this->__index]))
        {
            return $this->gc_list[$this->__index++];
        }
        else
        {
            return false;
        }
    }

    var $filter = null;
    var $gc_list = array();
    var $__index = 0;
}

class GiftCertificateLogger
{
    function GiftCertificateLogger()
    {

    }

    function used($gc_code_list, $order_id)
    {
        if (!sizeof($gc_code_list))
            return;
        $type = getMsg('GCT','GC_LOG_TYPE');
        $header = str_replace(
                                array('{codes}', '{orderid}'),
                                array(implode(',&nbsp;', $gc_code_list), $order_id),
                                getMsg('GCT','GC_LOG_USED')
                );
        $body = '';
        modApiFunc('Timeline','addLog', $type, $header, $body);
    }

    function created($gc)
    {
        $type = getMsg('GCT','GC_LOG_TYPE');
        $header = str_replace('{code}', $gc->code, getMsg('GCT','GC_LOG_CREATED'));
        $body = '';
        $tpl = getMsg('GCT', 'GC_LOG_ATTR');

        $sendtype = array(
            GC_SENDTYPE_EMAIL => getMsg('GCT','GC_SENDTYPE_EMAIL'),
            GC_SENDTYPE_POST => getMsg('GCT','GC_SENDTYPE_POST'),
        );

        $status = array(
            GC_STATUS_ACTIVE => getMsg('GCT','GC_STATUS_ACTIVE'),
            GC_STATUS_PENDING => getMsg('GCT','GC_STATUS_PENDING'),
            GC_STATUS_BLOCKED => getMsg('GCT','GC_STATUS_BLOCKED'),
        );
        $amount = modApiFunc('Localization', 'currency_format', $gc->amount);
        $remainder = modApiFunc('Localization', 'currency_format', $gc->remainder);
        $country = modApiFunc('Location','getCountry',$gc->country_id);
        $state = modApiFunc('Location','getState',$gc->state_id);

        $fields = array(
            array($gc->from,                  getMsg('GCT','GC_FROM')),
            array($gc->to,                    getMsg('GCT','GC_TO')),
            array($gc->message,               getMsg('GCT','GC_MESSAGE')),
            array($amount,                    getMsg('GCT','GC_AMOUNT')),
            array($remainder,                 getMsg('GCT','GC_REMAINDER')),
            array($sendtype[$gc->sendtype],   getMsg('GCT','GC_SENDTYPE')),
            array($status[$gc->status],       getMsg('GCT','GC_STATUS')),
        );

        if ($gc->sendtype == GC_SENDTYPE_EMAIL)
        {
            $fields[] = array($gc->email, getMsg('GCT','GC_EMAIL'));
        }
        else
        {
            $fields[] = array($gc->fname,   getMsg('GCT','GC_FNAME'));
            $fields[] = array($gc->lname,   getMsg('GCT','GC_LNAME'));
            $fields[] = array($gc->zip,     getMsg('GCT','GC_ZIP'));
            $fields[] = array($gc->city,    getMsg('GCT','GC_CITY'));
            $fields[] = array($gc->address, getMsg('GCT','GC_ADDRESS'));
            $fields[] = array($gc->phone,   getMsg('GCT','GC_PHONE'));
            $fields[] = array($country,     getMsg('GCT','GC_COUNTRY'));
            $fields[] = array($state,       getMsg('GCT','GC_STATE'));
        }

        foreach($fields as $f)
        {
            $body .= GiftCertificateLogger::__prepareLogMessageOnCreate($f[0], $f[1]);
        }

        modApiFunc('Timeline','addLog', $type, $header, $body, $gc->id, GC_LOG_REFSPACE);
    }

    function purchased($gc,$order_id)
    {
        $type = getMsg('GCT','GC_LOG_TYPE');

        $params = array(
            "{code}"=>$gc->code,
            "{order_id}"=>$order_id
        );

        $header = strtr(getMsg('GCT','GC_LOG_PURCHASED'),$params);
        $body = '';
        $tpl = getMsg('GCT', 'GC_LOG_ATTR');

        $sendtype = array(
            GC_SENDTYPE_EMAIL => getMsg('GCT','GC_SENDTYPE_EMAIL'),
            GC_SENDTYPE_POST => getMsg('GCT','GC_SENDTYPE_POST'),
        );

        $status = array(
            GC_STATUS_ACTIVE => getMsg('GCT','GC_STATUS_ACTIVE'),
            GC_STATUS_PENDING => getMsg('GCT','GC_STATUS_PENDING'),
            GC_STATUS_BLOCKED => getMsg('GCT','GC_STATUS_BLOCKED'),
        );
        $amount = modApiFunc('Localization', 'currency_format', $gc->amount);
        $remainder = modApiFunc('Localization', 'currency_format', $gc->remainder);
        $country = modApiFunc('Location','getCountry',$gc->country_id);
        $state = modApiFunc('Location','getState',$gc->state_id);

        $fields = array(
            array($gc->from,                  getMsg('GCT','GC_FROM')),
            array($gc->to,                    getMsg('GCT','GC_TO')),
            array($gc->message,               getMsg('GCT','GC_MESSAGE')),
            array($amount,                    getMsg('GCT','GC_AMOUNT')),
            array($remainder,                 getMsg('GCT','GC_REMAINDER')),
            array($sendtype[$gc->sendtype],   getMsg('GCT','GC_SENDTYPE')),
            array($status[$gc->status],       getMsg('GCT','GC_STATUS')),
        );

        if ($gc->sendtype == GC_SENDTYPE_EMAIL)
        {
            $fields[] = array($gc->email, getMsg('GCT','GC_EMAIL'));
        }
        else
        {
            $fields[] = array($gc->fname,   getMsg('GCT','GC_FNAME'));
            $fields[] = array($gc->lname,   getMsg('GCT','GC_LNAME'));
            $fields[] = array($gc->zip,     getMsg('GCT','GC_ZIP'));
            $fields[] = array($gc->city,    getMsg('GCT','GC_CITY'));
            $fields[] = array($gc->address, getMsg('GCT','GC_ADDRESS'));
            $fields[] = array($gc->phone,   getMsg('GCT','GC_PHONE'));
            $fields[] = array($country,     getMsg('GCT','GC_COUNTRY'));
            $fields[] = array($state,       getMsg('GCT','GC_STATE'));
        }

        foreach($fields as $f)
        {
            $body .= GiftCertificateLogger::__prepareLogMessageOnCreate($f[0], $f[1]);
        }

        modApiFunc('Timeline','addLog', $type, $header, $body, $gc->id, GC_LOG_REFSPACE);
    }

    function failed($gc,$order_id)
    {
        $type = getMsg('GCT','GC_LOG_TYPE');

        $params = array(
            "{code}"=>$gc->code,
            "{order_id}"=>$order_id
        );

        $header = strtr(getMsg('GCT','GC_LOG_FAILED'),$params);
        $body = '';
        $tpl = getMsg('GCT', 'GC_LOG_ATTR');

        $sendtype = array(
            GC_SENDTYPE_EMAIL => getMsg('GCT','GC_SENDTYPE_EMAIL'),
            GC_SENDTYPE_POST => getMsg('GCT','GC_SENDTYPE_POST'),
        );

        $status = array(
            GC_STATUS_ACTIVE => getMsg('GCT','GC_STATUS_ACTIVE'),
            GC_STATUS_PENDING => getMsg('GCT','GC_STATUS_PENDING'),
            GC_STATUS_BLOCKED => getMsg('GCT','GC_STATUS_BLOCKED'),
        );
        $amount = modApiFunc('Localization', 'currency_format', $gc->amount);
        $remainder = modApiFunc('Localization', 'currency_format', $gc->remainder);
        $country = modApiFunc('Location','getCountry',$gc->country_id);
        $state = modApiFunc('Location','getState',$gc->state_id);

        $fields = array(
            array($gc->from,                  getMsg('GCT','GC_FROM')),
            array($gc->to,                    getMsg('GCT','GC_TO')),
            array($gc->message,               getMsg('GCT','GC_MESSAGE')),
            array($amount,                    getMsg('GCT','GC_AMOUNT')),
            array($remainder,                 getMsg('GCT','GC_REMAINDER')),
            array($sendtype[$gc->sendtype],   getMsg('GCT','GC_SENDTYPE')),
            array($status[$gc->status],       getMsg('GCT','GC_STATUS')),
        );

        if ($gc->sendtype == GC_SENDTYPE_EMAIL)
        {
            $fields[] = array($gc->email, getMsg('GCT','GC_EMAIL'));
        }
        else
        {
            $fields[] = array($gc->fname,   getMsg('GCT','GC_FNAME'));
            $fields[] = array($gc->lname,   getMsg('GCT','GC_LNAME'));
            $fields[] = array($gc->zip,     getMsg('GCT','GC_ZIP'));
            $fields[] = array($gc->city,    getMsg('GCT','GC_CITY'));
            $fields[] = array($gc->address, getMsg('GCT','GC_ADDRESS'));
            $fields[] = array($gc->phone,   getMsg('GCT','GC_PHONE'));
            $fields[] = array($country,     getMsg('GCT','GC_COUNTRY'));
            $fields[] = array($state,       getMsg('GCT','GC_STATE'));
        }

        # Errors logging
        if ($gc->isError())
        foreach($gc->errors as $i=>$err)
        {
            switch ($err)
            {
                case GC_E_FIELD_ID:
                    $fields[] = array($gc->id, getMsg('GCT','GC_E_FIELD_ID'));
                break;
                case GC_E_FIELD_CODE:
                    $fields[] = array($gc->code, getMsg('GCT','GC_E_FIELD_CODE'));
                break;
                case GC_E_FIELD_FROM:
                    $fields[] = array($gc->from, getMsg('GCT','GC_E_FIELD_FROM'));
                break;
                case GC_E_FIELD_TO:
                    $fields[] = array($gc->to, getMsg('GCT','GC_E_FIELD_TO'));
                break;
                case GC_E_FIELD_AMOUNT:
                    $fields[] = array($gc->amount, getMsg('GCT','GC_E_FIELD_AMOUNT'));
                break;
                case GC_E_FIELD_AMOUNT_SEPARATOR:
                    $fields[] = array($gc->amount, getMsg('GCT','GC_E_FIELD_AMOUNT_SEPARATOR'));
                break;
/*                case GC_E_FIELD_REMAINDER:
                    $fields[] = array(gc->remainder, getMsg('GCT','GC_E_FIELD_REMAINDER'));
                break; */
                case GC_E_FIELD_SENDTYPE:
                    $fields[] = array($gc->sendtype, getMsg('GCT','GC_E_FIELD_SENDTYPE'));
                break;
/*                case GC_E_FIELD_MESSAGE:
                    $fields[] = array($gc->message, getMsg('GCT','GC_E_FIELD_MESSAGE'));
                break; */
                case GC_E_FIELD_EMAIL:
                    $fields[] = array($gc->email, getMsg('GCT','GC_E_FIELD_EMAIL'));
                break;
                case GC_E_FIELD_LNAME:
                    $fields[] = array($gc->lname, getMsg('GCT','GC_E_FIELD_LNAME'));
                break;
                case GC_E_FIELD_FNAME:
                    $fields[] = array($gc->fname, getMsg('GCT','GC_E_FIELD_FNAME'));
                break;
                case GC_E_FIELD_ZIP:
                    $fields[] = array($gc->zip, getMsg('GCT','GC_E_FIELD_ZIP'));
                break;
                case GC_E_FIELD_CITY:
                    $fields[] = array($gc->city, getMsg('GCT','GC_E_FIELD_CITY'));
                break;
                case GC_E_FIELD_ADDRESS:
                    $fields[] = array($gc->address, getMsg('GCT','GC_E_FIELD_ADDRESS'));
                break;
                case GC_E_FIELD_PHONE:
                    $fields[] = array($gc->phone, getMsg('GCT','GC_E_FIELD_PHONE'));
                break;
                case GC_E_FIELD_COUNTRYID:
                    $fields[] = array($gc->country_id, getMsg('GCT','GC_E_FIELD_COUNTRYID'));
                break;
                case GC_E_FIELD_STATEID:
                    $fields[] = array($gc->state_id, getMsg('GCT','GC_E_FIELD_STATEID'));
                break;
            }
        }

        foreach($fields as $f)
        {
            $body .= GiftCertificateLogger::__prepareLogMessageOnCreate($f[0], $f[1]);
        }

        modApiFunc('Timeline','addLog', $type, $header, $body, $gc->id, GC_LOG_REFSPACE);
    }



    function updated($gc_old, $gc_new)
    {
        $type = getMsg('GCT','GC_LOG_TYPE');
        $header = str_replace('{code}', $gc_new->code, getMsg('GCT','GC_LOG_UPDATED'));
        $body = '';

        $sendtype = array(
            GC_SENDTYPE_EMAIL => getMsg('GCT','GC_SENDTYPE_EMAIL'),
            GC_SENDTYPE_POST => getMsg('GCT','GC_SENDTYPE_POST'),
        );
        $status = array(
            GC_STATUS_ACTIVE => getMsg('GCT','GC_STATUS_ACTIVE'),
            GC_STATUS_PENDING => getMsg('GCT','GC_STATUS_PENDING'),
            GC_STATUS_BLOCKED => getMsg('GCT','GC_STATUS_BLOCKED'),
        );

        $amount_old = modApiFunc('Localization', 'currency_format', $gc_old->amount);
        $amount_new = modApiFunc('Localization', 'currency_format', $gc_new->amount);
        $remainder_old = modApiFunc('Localization', 'currency_format', $gc_old->remainder);
        $remainder_new = modApiFunc('Localization', 'currency_format', $gc_new->remainder);
        $country_old = modApiFunc('Location','getCountry',$gc_old->country_id);
        $country_new = modApiFunc('Location','getCountry',$gc_new->country_id);
        $state_old = modApiFunc('Location','getState',$gc_old->state_id);
        $state_new = modApiFunc('Location','getState',$gc_new->state_id);

        $fields = array(
            array($gc_old->from,                $gc_new->from,                  getMsg('GCT','GC_FROM')),
            array($gc_old->to,                  $gc_new->to,                    getMsg('GCT','GC_TO')),
            array($gc_old->message,             $gc_new->message,               getMsg('GCT','GC_MESSAGE')),
            array($amount_old,                  $amount_new,                    getMsg('GCT','GC_AMOUNT')),
            array($remainder_old,               $remainder_new,                 getMsg('GCT','GC_REMAINDER')),
            array($sendtype[$gc_old->sendtype], $sendtype[$gc_new->sendtype],   getMsg('GCT','GC_SENDTYPE')),
            array($status[$gc_old->status],     $status[$gc_new->status],       getMsg('GCT','GC_STATUS')),
            array($gc_old->fname,               $gc_new->fname,                 getMsg('GCT','GC_FNAME')),
            array($gc_old->lname,               $gc_new->lname,                 getMsg('GCT','GC_LNAME')),
            array($gc_old->zip,                 $gc_new->zip,                   getMsg('GCT','GC_ZIP')),
            array($gc_old->city,                $gc_new->city,                  getMsg('GCT','GC_CITY')),
            array($gc_old->address,             $gc_new->address,               getMsg('GCT','GC_ADDRESS')),
            array($gc_old->phone,               $gc_new->phone,                 getMsg('GCT','GC_PHONE')),
            array($gc_old->email,               $gc_new->email,                 getMsg('GCT','GC_EMAIL')),
            array($country_old,                 $country_new,                   getMsg('GCT','GC_COUNTRY')),
            array($state_old,                   $state_new,                     getMsg('GCT','GC_STATE')),
        );

        foreach($fields as $f)
        {
            $body .= GiftCertificateLogger::__prepareLogMessageOnChange($f[0], $f[1], $f[2]);
        }

        $refid = $gc_new->id;
        modApiFunc('Timeline','addLog', $type, $header, $body, $refid, GC_LOG_REFSPACE);
    }

    function __prepareLogMessageOnChange($old_value, $new_value, $field_name)
    {
        if ($old_value != $new_value)
        {
            return str_replace(array('{f}','{o}','{n}'),
                               array($field_name, $old_value, $new_value),
                               getMsg('GCT', 'GC_LOG_DIFF'));
        }
        else
        {
            return '';
        }
    }

    function __prepareLogMessageOnCreate($value, $field_name)
    {
        if (!empty($value))
        {
            return str_replace(array('{f}','{v}'),
                               array($field_name, $value),
                               getMsg('GCT', 'GC_LOG_ATTR'));
        }
    }
}

?>