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

class SELECT_TAX_NAMES_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Taxes::getTables();
        $tn = $tables['tax_names']['columns'];
        $ta = $tables['tax_addresses']['columns'];

        $this->addSelectField($tn['id'],    'Id');
        $this->addSelectField($tn['included_into_price'],  'included_into_price');
        $this->setMultiLangAlias('_name', 'tax_names', $tn['name'], $tn['id'], 'Taxes');
        $this->addSelectField($this -> getMultiLangAlias('_name'),  'Name');
        $this->addSelectField($tn['ta_id'],  'AddressId');
        $this->addSelectField($ta['name'],  'Address');
        $this->addSelectField($tn['needs_address'],  'NeedsAddress');

        $this->addLeftJoin('tax_addresses', $ta['id'], DB_EQ, $tn['ta_id']);
    }
}

class SELECT_TAX_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Taxes::getTables();
        $tn = $tables['tax_names']['columns'];

        $this->addSelectField($tn['id'],    'id');
        $this->addSelectField($tn['included_into_price'],  'included_into_price');
        $this->setMultiLangAlias('_name', 'tax_names', $tn['name'], $tn['id'], 'Taxes');
        $this->addSelectField($this -> getMultiLangAlias('_name'),  'name');
        $this->addSelectField($tn['ta_id'], 'addressId');
        $this->addSelectField($tn['needs_address'], 'needs_address');
    }
}

class SELECT_PRODUCT_TAX_CLASSES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Taxes::getTables();
        $ptc = $tables['product_tax_classes']['columns'];

        $this->addSelectField($ptc['id'],    'id');
        $this->addSelectField($ptc['name'],  'value');
        if (!$params['with_nottaxable'])
        {
           $this->WhereValue($ptc['id'], DB_NEQ, TAX_CLASS_ID_NOT_TAXABLE);
        }
    }
}

class SELECT_TAX_DISPLAY_OPTIONS_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Taxes::getTables();
        $td = $tables['tax_display']['columns'];
        $tdo = $tables['tax_display_options']['columns'];

        $this->addSelectField($td['id'], 'Id');
        $this->addSelectField($td['formula'], 'Formula');
        $this->setMultiLangAlias('_view', 'tax_display', $td['view'], $td['id'], 'Taxes');
        $this->addSelectField($this -> getMultiLangAlias('_view'),  'View');
        $this->addSelectField($tdo['id'], 'tdoId');
        $this->addSelectField($tdo['name'], 'OptionName');
        $this->addLeftJoin('tax_display_options', $tdo['id'], DB_EQ, $td['tdo_id']);
    }
}

class SELECT_TAX_SETTING extends DB_Select
{
    function initQuery($params)
    {
        $tables = Taxes::getTables();
        $ts = $tables['tax_settings']['columns'];

        $this->addSelectField($ts['val'], 'val');
        $this->WhereValue($ts['key'], DB_EQ, $params['key']);
    }
}

class SELECT_APPLICABLE_TAX_RATES extends DB_Select
{
    function initQuery($params)
    {
        $AddressArray = $params['AddressArray'];
        $ProductTaxClassArray = $params['ProductTaxClassArray'];

        $tables = Taxes::getTables();
        $tr = $tables['tax_rates']['columns'];

        $this->addSelectField($tr['id'],     'Id');
        $this->addSelectField($tr['c_id'],   'CountryId');
        $this->addSelectField($tr['s_id'],   'StateId');
        $this->addSelectField($tr['ptc_id'], 'ProductTaxClassId');
        $this->addSelectField($tr['tn_id'],  'TaxNameId');
        $this->addSelectField($tr['rate'],   'Rate');
        $this->addSelectField($tr['formula'],'Formula');
        $this->addSelectField($tr['applicable'],'Applicable');
        $this->addSelectField($tr['rates_set'],'rates_set');
        $this->WhereField($tr['ptc_id'], DB_IN, "(".implode(", ", $ProductTaxClassArray).")");
        $this->WhereAnd();
        $this->addWhereOpenSection();
        if(!empty($AddressArray["CountryId"]) && !empty($AddressArray["CountryId"][0]))
        {
            $this->addWhereOpenSection();
            $this->WhereField($tr['c_id'], DB_IN, "(".implode(", ", $AddressArray["CountryId"]).")");
            if(!empty($AddressArray["StateId"]) && !empty($AddressArray["StateId"][0]))
            {
                $this->WhereAnd();
                $this->WhereField($tr['s_id'], DB_IN, "(\"".implode("\", \"", $AddressArray["StateId"])."\")");
            }
            $this->addWhereCloseSection();
            $this->WhereOr();
        }
        $this->addWhereOpenSection();
        //      ,                          .   -                                           (tax_names)
        //             'needs_address',                               ,                (rates)
        //                         'tax_rates'              TAXES_ALL_COUNTRIES_ID        c_id.
        $this->WhereField($tr['c_id'], DB_EQ, TAXES_COUNTRY_NOT_NEEDED_ID);
        $this->addWhereCloseSection();
        $this->addWhereCloseSection();
    }
}

class SELECT_TAXES_WHICH_USE_SET extends DB_Select
{
    function initQuery($params)
    {
        $tables = Taxes::getTables();
        $c = $tables["tax_rates"]["columns"];

        $this->addSelectField($c['id'], 'id');
        $this->WhereValue($c['rates_set'], DB_EQ, $params['sid']);
    }
}

?>