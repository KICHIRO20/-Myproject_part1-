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
 * Module "Currency Converter"
 *
 * @package CurrencyConverter
 * @author Egor V. Derevyankin
 */

class Currency_Converter
{
    function Currency_Converter()
    {
    }

    function getInfo()
    {}

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        $tables = array ();

        $rates_cache = 'cconv_rates_cache';
        $tables[$rates_cache] = array();
        $tables[$rates_cache]['columns'] = array
            (
                'id'                => $rates_cache.'.crate_id'
               ,'from'              => $rates_cache.'.crate_from'
               ,'to'                => $rates_cache.'.crate_to'
               ,'rate'              => $rates_cache.'.crate_rate'
               ,'expire'            => $rates_cache.'.crate_expire'
            );
        $tables[$rates_cache]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'from'              => DBQUERY_FIELD_TYPE_CHAR10 .' NOT NULL DEFAULT \'\''
               ,'to'                => DBQUERY_FIELD_TYPE_CHAR10 .' NOT NULL DEFAULT \'\''
               ,'rate'              => DBQUERY_FIELD_TYPE_DECIMAL12_4 .' NOT NULL DEFAULT 1'
               ,'expire'            => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
            );
        $tables[$rates_cache]['primary'] = array
            (
                'id'
            );

        $table = 'cconv_temp_cur_rates';
        $tables[$table] = array(
            'columns' => array(
                'code' => $table.'.code'
               ,'rate' => $table.'.rate'
            ),
            'types' => array(
                'code' => DBQUERY_FIELD_TYPE_CHAR5 .' NOT NULL DEFAULT \'\''
               ,'rate' => DBQUERY_FIELD_TYPE_DECIMAL12_4 .' NOT NULL DEFAULT 1'
            ),
            'primary' => array(
                'code'
            )
        );

        $table = 'cconv_man_rates';
        $tables[$table] = array(
            'columns' => array(
                'rate_id'        => $table.'.rate_id'
               ,'from'           => $table.'._from'
               ,'to'             => $table.'._to'
               ,'rate'           => $table.'.rate'
            ),
            'types' => array(
                'rate_id'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'from'           => DBQUERY_FIELD_TYPE_CHAR10 .' NOT NULL DEFAULT \'\''
               ,'to'             => DBQUERY_FIELD_TYPE_CHAR10 .' NOT NULL DEFAULT \'\''
               ,'rate'           => DBQUERY_FIELD_TYPE_CHAR32 .' NOT NULL DEFAULT \'\''
            ),
            'primary' => array(
                'rate_id'
            ),
            'indexes' => array(
                'UNIQUE KEY unq_ft' => 'from,to'
            )
        );

        global $application;
        return $application->addTablePrefix($tables);

    }

    function install()
    {
        $tables = Currency_Converter::getTables();
        $query = new DB_Table_Create($tables);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Currency_Converter::getTables());
    }

    function convert($count, $from, $to)
    {
        if(floatval($count) == 0 or $from == "" or $to == "")
        {
            return false;
        };

        if($count === PRICE_N_A)
        {
        	return PRICE_N_A;
        }

        if($from == $to)
        {
        	return $count;
        }

        if (isset($this->__cache[$from.$to]))
        {
            return $count * $this->__cache[$from.$to];
        }

        $man_rate_result = $this->__convertByManual($count, $from, $to);
        if($man_rate_result !== false) return $man_rate_result;

        $rate = false;

        $RATES_SOURCES = array("Cache");//, "WebServiceX", "Google"); // hidden convert not available anymore

        reset($RATES_SOURCES);
        foreach($RATES_SOURCES as $source)
        {
            $func_name = "__getRateFrom{$source}";
            $rate = $this->$func_name($from,$to);

            if($rate === false)
            {
                continue;
            }
            else
            {
                if($source != "Cache")
                {
                    $this->__saveRateToCache($from,$to,$rate);
                    modApiFunc('EventsManager','throwEvent','CurrencyCacheRatesUpdated');
                };
                break;
            };
        };
        $this->__cache[$from.$to] = $rate;
        return ($rate !== false ? $count * $rate : false);
    }

    function isConvertAvail($from, $to)
    {
        return ($this->convert(1, $from, $to) !== false);
    }

    function addManualRate($from, $to, $rate)
    {
        global $application;

        $tables = $this->getTables();
        $mr_table = $tables['cconv_man_rates']['columns'];

        $query = new DB_Replace('cconv_man_rates');
        $query->addReplaceValue($from, $mr_table['from']);
        $query->addReplaceValue($to, $mr_table['to']);
        $query->addReplaceValue($rate, $mr_table['rate']);

        $application->db->PrepareSQL($query);
        if($application->db->DB_Exec())
        {
            $id = $application->db->DB_Insert_Id();
        	modApiFunc("Currency_Converter", "__delRateFromCache", $from, $to);
        	modApiFunc('EventsManager','throwEvent','CurrencyManualRateAdded');
            return $id;
        }
        else
            return false;
    }

    function delManualRateByCode($cur_code)
    {
        global $application;

        $tables = $this->getTables();
        $mr_table = $tables['cconv_man_rates']['columns'];

        $query = new DB_Delete('cconv_man_rates');
        $query->WhereValue($mr_table['to'], DB_EQ, $cur_code);
        $query->WhereOR();
        $query->WhereValue($mr_table['from'], DB_EQ, $cur_code);

        $application->db->PrepareSQL($query);
        $res = $application->db->DB_Exec();
        modApiFunc('EventsManager','throwEvent','CurrencyManualRatesDeleted');
        return $res;
    }

    function delManualRate($rate_id)
    {
        return $this->delManualRates(array($rate_id));
    }

    function delManualRates($rates_ids)
    {
        if(!is_array($rates_ids) or empty($rates_ids))
        {
            return true;
        };

        global $application;

        $tables = $this->getTables();
        $mr_table = $tables['cconv_man_rates']['columns'];

        $query = new DB_Delete('cconv_man_rates');
        $query->Where($mr_table['rate_id'], DB_IN, '('.implode(', ',$rates_ids).')');

        $application->db->PrepareSQL($query);
        $res = $application->db->DB_Exec();
        modApiFunc('EventsManager','throwEvent','CurrencyManualRatesDeleted');
        return $res;
    }

    function delAllManualRates()
    {
        global $application;

        $tables = $this->getTables();
        $mr_table = $tables['cconv_man_rates']['columns'];

        $query = new DB_Delete('cconv_man_rates');

        $application->db->PrepareSQL($query);
        $res = $application->db->DB_Exec();
        modApiFunc('EventsManager','throwEvent','CurrencyManualRatesDeleted');
        return $res;
    }

    function getManualRates()
    {
        return execQuery('SELECT_CURRENCY_MANUAL_RATES', array());
    }

    function cacheManualRates()
    {
        if (! $this->all_manual_rates) {
            $this->all_manual_rates = $this->getManualRates();
        }
    }

    function doesManRateExists($from, $to)
    {
        $this->cacheManualRates();
        foreach ($this->all_manual_rates as $rate) {
            if ($rate['_from'] == $from && $rate['_to'] == $to || $rate['_from'] == $to && $rate['_to'] == $from) {
                return true;
            }
        }
        return false;
    }

    function updateTempCurrencyRates($currency_codes)
    {
        if (!is_array($currency_codes) || empty($currency_codes))
            return;

        $main_store_currency = modApiFunc('Localization', 'getCurrencyCodeById', modApiFunc('Localization', 'getMainStoreCurrency'));
        foreach($currency_codes as $currency_code)
        {
            // choosing if the code is the value or value['currency_code']
            if (isset($currency_code['currency_code']))
                $code = $currency_code['currency_code'];
            else
                $code = $currency_code;

            execQuery('REPLACE_TEMP_CURRENCY_RATE', array('code' => $code, 'rate' => $this -> getPlainRate($code, $main_store_currency)));
        }
    }

    /**
     * Returns rate between currencies. If rate cannot be obtained returns 0
     */
    function getPlainRate($from, $to)
    {
        if ($from == $to)
            return 1;

        if (isset($this->__cache[$from.$to]))
            return $this->__cache[$from.$to];

        $this->cacheManualRates();

        foreach ($this->all_manual_rates as $rate) {
            if ($rate['_from'] == $from && $rate['_to'] == $to) {
                return 1 / $rate['rate'];
            }
            if ($rate['_from'] == $to && $rate['_to'] == $from) {
                return $rate['rate'];
            }
        }

        $rate = false;

        $RATES_SOURCES = array("Cache", "WebServiceX", "Google");

        reset($RATES_SOURCES);
        foreach($RATES_SOURCES as $source)
        {
            $func_name = "__getRateFrom{$source}";
            $rate = $this->$func_name($from,$to);

            if($rate === false)
            {
                continue;
            }
            else
            {
                if($source != "Cache")
                {
                    $this->__saveRateToCache($from,$to,$rate);
                    modApiFunc('EventsManager','throwEvent','CurrencyCacheRatesUpdated');
                };
                break;
            };
        };
        $this->__cache[$from.$to] = $rate;
        return ($rate !== false ? $rate : 0);
    }

    function __saveRateToCache($from,$to,$rate)
    {
        $rev_rate = 1/$rate;

        global $application;
        $tables=$this->getTables();
        $rc_table=$tables['cconv_rates_cache']['columns'];

        $query = new DB_Delete('cconv_rates_cache');
        $query->addWhereOpenSection();
        $query->WhereValue($rc_table['from'], DB_EQ, $from);
        $query->WhereAnd();
        $query->WhereValue($rc_table['to'], DB_EQ, $to);
        $query->addWhereCloseSection();

        $query->WhereOR();

        $query->addWhereOpenSection();
        $query->WhereValue($rc_table['from'], DB_EQ, $to);
        $query->WhereAND();
        $query->WhereValue($rc_table['to'], DB_EQ, $from);
        $query->addWhereCloseSection();

        $query->WhereOR();
        $query->Where($rc_table['expire'], DB_LT, time());
        $application->db->getDB_Result($query);

        loadCoreFile('db_multiple_insert.php');
        $query = new DB_Multiple_Insert('cconv_rates_cache');
        $query->setInsertFields(array($rc_table['rate'], $rc_table['from'],$rc_table['to'], $rc_table['expire']));
        $query->addInsertValuesArray(array(
            $rc_table['from']    => $from
           ,$rc_table['to']      => $to
           ,$rc_table['rate']    => $rate
           ,$rc_table['expire']  => time()+CURRENCY_CONVERTER_RATE_EXPIRE_PERIOD
        ));
        $query->addInsertValuesArray(array(
            $rc_table['from']    => $to
           ,$rc_table['to']      => $from
           ,$rc_table['rate']    => $rev_rate
           ,$rc_table['expire']  => time()+CURRENCY_CONVERTER_RATE_EXPIRE_PERIOD
        ));
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return;
    }

    function __delRateFromCache($from,$to)
    {
        global $application;
        $tables=$this->getTables();
        $rc_table=$tables['cconv_rates_cache']['columns'];

        $query = new DB_Delete('cconv_rates_cache');
        $query->addWhereOpenSection();
        $query->WhereValue($rc_table['from'], DB_EQ, $from);
        $query->WhereAnd();
        $query->WhereValue($rc_table['to'], DB_EQ, $to);
        $query->addWhereCloseSection();

        $query->WhereOR();

        $query->addWhereOpenSection();
        $query->WhereValue($rc_table['from'], DB_EQ, $to);
        $query->WhereAND();
        $query->WhereValue($rc_table['to'], DB_EQ, $from);
        $query->addWhereCloseSection();

        $application->db->getDB_Result($query);
        return;
    }


    function __getRateFromCache($from,$to)
    {
        $params = array('from'=>$from, 'to'=>$to, 'expire'=>time());
        $cached=execQuery('SELECT_CURRENCY_RATES_FROM_CACHE', $params);
        if(isset($cached[0]["rate"]))
            return $cached[0]["rate"]*1;
        else
            return false;
    }

    function __getRateFromGoogle($from,$to)
    {
        return false; // it is better not to use it now, 'cause it's a bit erratical
        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();
        $bnc->setHTTPversion("1.0");
        $bnc->setMethod("GET");
        $bnc->setURL("http://www.google.com/search");
        $bnc->setSocketReadTimeout(20);
        $get_params=array(
            "hl" => "en",
            "q" => $from." in ".$to,
            "btnG" => "Search",
        );
        $bnc->setGETstring($bnc->prepareDATAstring($get_params));

        $bnc->setAdditionalHeaders(
                    array(
                            'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.2; ru; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1;fdnet',
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                            'Accept-Language' => 'ru,en-us;q=0.7,en;q=0.3',
                            'Accept-Encoding' => '',
                            'Accept-Charset' => 'windows-1251'
                    )
        );

        $res=$bnc->RunRequest();

        if($res != false)
        {
            $responce = $res["body"];
            preg_match("/= ([0-9\.]+)/i",$responce,$matches);
            if(isset($matches[1]) && Validator::isValidFloat($matches[1]))
            {
                $rate = round($matches[1],4);
                $this->__addRequestToTimeline('www.google.com', $from, $to, $rate, $bnc, $res);
                return $rate;
            }
        };

        $this->__addRequestToTimeline('www.google.com', $from, $to, false, $bnc, $res);
        return false;
    }

    function __addRequestToTimeline($service_name, $from, $to, $rate, $request, $responce)
    {
        $tl_type = getMsg('CC','TL_TYPE');
        $tl_header = str_replace(
                            array('{SERVICE}', '{FROM}', '{TO}'),
                            array($service_name, $from, $to),
                            getMsg('CC', 'TL_HEADER_REQUEST')
                        );

        if ($rate != false)
        {
            $tl_header .= str_replace(
                                array('{RATE}', '{FROM}', '{TO}'),
                                array($rate, $from, $to),
                                getMsg('CC', 'TL_HEADER_RESPONSE_OK')
                            );
        }
        else
        {
            $tl_header .= getMsg('CC', 'TL_HEADER_RESPONSE_FIALED');
        }

        $tl_body = prepareArrayDisplay($request, getMsg('CC', 'TL_REQUEST'));
        $tl_body .= prepareArrayDisplay($responce, getMsg('CC', 'TL_RESPONSE'));

        modApiFunc('Timeline', 'addLog', $tl_type, $tl_header, $tl_body);
    }

    function __getRateFromWebServiceX($from,$to)
    {
        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();
        $bnc->setHTTPversion("1.0");
        $bnc->setMethod("GET");
        $bnc->setURL("http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate");
        $bnc->setSocketReadTimeout(10);
        $get_params=array(
            "FromCurrency" => $from
           ,"ToCurrency" => $to
        );
        $bnc->setGETstring($bnc->prepareDATAstring($get_params));
        $res=$bnc->RunRequest();
        if($res != false)
        {
            $xml = $res["body"];
            loadCoreFile('obj_xml.php');
            $parser = new xml_doc($xml);
            $parser->parse();
            foreach($parser->xml_index as $node)
            {
                if($node->name = "DOUBLE")
                {
                    $rate = $node->contents;
                    if ($rate == 0) $rate = false;
                    $this->__addRequestToTimeline('www.webservicex.net', $from, $to, $rate, $bnc, $res);
                    return $rate;
                }
            };
        };

        $this->__addRequestToTimeline('www.webservicex.net', $from, $to, false, $bnc, $res);
        return false;
    }

    function __convertByManual($count, $from, $to)
    {
        $this->cacheManualRates();
        foreach ($this->all_manual_rates as $rate) {
            if ($rate['_from'] == $from && $rate['_to'] == $to) {
                return number_format($count / $rate['rate'], 4, '.', '');
            }
            if ($rate['_from'] == $to && $rate['_to'] == $from) {
                return number_format($count * $rate['rate'], 4, '.', '');
            }
        }
        return false;
    }

    function getRateFromWeb($from, $to)
    {
        return $this->__getRateFromWebServiceX($from, $to);
    }

    var $__cache = array();
    var $all_manual_rates;
};

?>