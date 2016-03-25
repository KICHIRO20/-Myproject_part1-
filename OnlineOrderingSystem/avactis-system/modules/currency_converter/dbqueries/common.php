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

class SELECT_CURRENCY_MANUAL_RATES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Currency_Converter::getTables();
        $mr_table = $tables['cconv_man_rates']['columns'];

        $this->addSelectTable('cconv_man_rates');
        $this->addSelectField('*');
        $this->SelectOrder($mr_table['rate_id'], 'ASC');
    }
}

class SELECT_CURRENCY_RATES_FROM_CACHE extends DB_Select
{
    function initQuery($params)
    {
        $from = $params['from'];
        $to = $params['to'];
        $expire = $params['expire'];

        $tables = Currency_Converter::getTables();
        $rc_table=$tables['cconv_rates_cache']['columns'];

        $this->addSelectTable('cconv_rates_cache');
        $this->addSelectField($rc_table['rate'],'rate');
        $this->WhereValue($rc_table['from'], DB_EQ, $from);
        $this->WhereAnd();
        $this->WhereValue($rc_table['to'], DB_EQ, $to);
        $this->WhereAND();
        $this->Where($rc_table['expire'], DB_GT, $expire);
    }
}

class REPLACE_TEMP_CURRENCY_RATE extends DB_Replace
{
    function REPLACE_TEMP_CURRENCY_RATE()
    {
        parent :: DB_Replace('cconv_temp_cur_rates');
    }

    function initQuery($params)
    {
        $tables = Currency_Converter :: getTables();
        $tcr = $tables['cconv_temp_cur_rates']['columns'];

        $this -> addReplaceValue($params['code'], $tcr['code']);
        $this -> addReplaceValue($params['rate'], $tcr['rate']);
    }
}

?>