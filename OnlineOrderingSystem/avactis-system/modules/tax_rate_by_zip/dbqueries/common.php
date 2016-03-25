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
class SELECT_TAX_RATES_SET_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_sets"]["columns"];

        $this->addSelectField($c['id'], 'id');
        $this->addSelectField($c['name'], 'name');
        $this->addSelectField($c['dt'], 'date');
        $this->addSelectField($c['isActive'], 'filesize');
        $this->addSelectField($c['filecaption'], 'filecaption');
        $this->addSelectField($c['filename'], 'filename');
//        $this->WhereValue($c['isActive'], DB_EQ, 1);
//        $this->WhereAnd();
        $this->WhereValue($c['id'], DB_EQ, $params['sid']);
    }
}

class SELECT_TAX_RATES_SETS_LIST extends DB_Select
{
    function initQuery()
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_sets"]["columns"];

        $this->addSelectField($c['id'], 'id');
        $this->addSelectField($c['name'], 'name');
        $this->addSelectField($c['dt'], 'date');
        $this->addSelectField($c['isActive'], 'filesize');
        $this->addSelectField($c['filecaption'], 'filecaption');
        $this->addSelectField($c['filename'], 'filename');
        $this->WhereValue($c['isActive'], DB_EQ, 1);
    }
}

class SELECT_TAX_RATES_BY_ZIP5_STRICT extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_rates"]["columns"];

        $this->addSelectField($c['rate'], 'rate');
        $this->WhereValue($c['sid'], DB_EQ, $params['sid']);
        $this->WhereAnd();
        $this->WhereValue($c['zip5'], DB_EQ, $params['zip5']);
    }
}

class SELECT_TAX_RATES_BY_ZIP5_INTERVAL extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_rates"]["columns"];

        $this->addSelectField($c['rate'], 'rate');
        $this->WhereValue($c['sid'], DB_EQ, $params['sid']);
        $this->WhereAnd();
        $this->WhereValue($c['zip5low'], DB_LTE, $params['zip5']);
        $this->WhereAnd();
        $this->WhereValue($c['zip5high'], DB_GTE, $params['zip5']);
    }
}

class SELECT_TAX_RATES_BY_ZIP5_MASK extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_rates"]["columns"];

        $this->addSelectField($c['rate'], 'rate');
        $this->WhereValue($c['sid'], DB_EQ, $params['sid']);
        $this->WhereAnd();
        $this->Where("'{$params['zip5']}'", DB_REGEXP, $c['zip5mask']);
    }
}

class SELECT_TAX_RATES_BY_ZIP9_STRICT extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_rates"]["columns"];

        $this->addSelectField($c['rate'], 'rate');
        $this->addSelectField("{$c['zip4high']} - {$c['zip4low']}", 'ival');
        $this->WhereValue($c['sid'], DB_EQ, $params['sid']);
        $this->WhereAnd();
        $this->WhereValue($c['zip5'], DB_EQ, $params['zip5']);

        $this->WhereAnd();
        $this->WhereValue($c['zip4low'], DB_LTE, $params['zip4']);
        $this->WhereAnd();
        $this->WhereValue($c['zip4high'], DB_GTE, $params['zip4']);
        $this->SelectOrder('ival');
        $this->SelectOrder('rate', "DESC");
        $this->SelectLimit(0, 1);
    }
}

class SELECT_TAX_RATES_BY_ZIP9_INTERVAL extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_rates"]["columns"];

        $this->addSelectField($c['rate'], 'rate');
        $this->addSelectField("{$c['zip4high']} - {$c['zip4low']}", 'ival');
        $this->WhereValue($c['sid'], DB_EQ, $params['sid']);
        $this->WhereAnd();
        $this->WhereValue($c['zip5low'], DB_LTE, $params['zip5']);
        $this->WhereAnd();
        $this->WhereValue($c['zip5high'], DB_GTE, $params['zip5']);

        $this->WhereAnd();
        $this->WhereValue($c['zip4low'], DB_LTE, $params['zip4']);
        $this->WhereAnd();
        $this->WhereValue($c['zip4high'], DB_GTE, $params['zip4']);
        $this->SelectOrder('ival');
        $this->SelectOrder('rate', "DESC");
        $this->SelectLimit(0, 1);
    }
}

class SELECT_TAX_RATES_BY_ZIP9_MASK extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_rates"]["columns"];

        $this->addSelectField($c['rate'], 'rate');
        $this->addSelectField("{$c['zip4high']} - {$c['zip4low']}", 'ival');
        $this->WhereValue($c['sid'], DB_EQ, $params['sid']);
        $this->WhereAnd();
        $this->Where("'{$params['zip5']}'", DB_REGEXP, $c['zip5mask']);

        $this->WhereAnd();
        $this->WhereValue($c['zip4low'], DB_LTE, $params['zip4']);
        $this->WhereAnd();
        $this->WhereValue($c['zip4high'], DB_GTE, $params['zip4']);
        $this->SelectOrder('ival');
        $this->SelectOrder('rate', "DESC");
        $this->SelectLimit(0, 1);
    }
}

class SELECT_TAX_SET_RECORDS_NUMBER extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_rates"]["columns"];

        $this->addSelectField($this->fCount($c['id']), 'rec_num');
        $this->WhereValue($c['sid'], DB_EQ, $params['sid']);
    }
}

class SELECT_TAX_RATES_INACTIVE_AND_OUTOFDATE extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxRateByZip::getTables();
        $c = $tables["tax_zip_sets"]["columns"];

        $this->addSelectField($c['id'], 'id');
        $this->WhereValue($c['isActive'], DB_EQ, 0);
        $this->WhereAND();
        $this->Where("UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP({$c['dt']})", DB_GT, 3600);
    }
}
?>