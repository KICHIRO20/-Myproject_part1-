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
 * Module "TaxRateByZip"
 *
 * @package TaxRateByZip
 * @author Garafutdinov Ravil
 */
class TaxRateByZip
{
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tax_zip_sets = 'tax_zip_sets';
        $tables[$tax_zip_sets] = array();
        $tables[$tax_zip_sets]['columns'] = array
            (
                'id'                => $tax_zip_sets.'.id'
               ,'name'              => $tax_zip_sets.'.name'
               ,'dt'                => $tax_zip_sets.'.dt'
               ,'isActive'          => $tax_zip_sets.'.isActive'
               ,"filename"          => $tax_zip_sets.".filename"
               ,"filecaption"       => $tax_zip_sets.".filecaption"
            );
        $tables[$tax_zip_sets]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'dt'                => DBQUERY_FIELD_TYPE_DATETIME
               ,"isActive"          => DBQUERY_FIELD_TYPE_INT .' NOT NULL default 0'
               ,'filename'          => DBQUERY_FIELD_TYPE_CHAR255
               ,'filecaption'       => DBQUERY_FIELD_TYPE_CHAR255
             );
        $tables[$tax_zip_sets]['primary'] = array
            (
                'id'
            );

        $tax_zip_rates = 'tax_zip_rates';
        $tables[$tax_zip_rates] = array();
        $tables[$tax_zip_rates]['columns'] = array
            (
                'id'                => $tax_zip_rates.'.id'
               ,'sid'               => $tax_zip_rates.'.sid'
               ,'zip5'              => $tax_zip_rates.'.zip5'
               ,'zip5low'           => $tax_zip_rates.'.zip5low'
               ,'zip5high'          => $tax_zip_rates.'.zip5high'
               ,'zip5mask'          => $tax_zip_rates.'.zip5mask'
               ,'zip4low'           => $tax_zip_rates.'.zip4low'
               ,'zip4high'          => $tax_zip_rates.'.zip4high'
               ,'rate'              => $tax_zip_rates.'.rate'
            );
        $tables[$tax_zip_rates]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'sid'               => DBQUERY_FIELD_TYPE_INT
               ,'zip5'              => DBQUERY_FIELD_TYPE_INT
               ,'zip5low'           => DBQUERY_FIELD_TYPE_INT
               ,'zip5high'          => DBQUERY_FIELD_TYPE_INT
               ,'zip5mask'          => DBQUERY_FIELD_TYPE_CHAR255
               ,'zip4low'           => DBQUERY_FIELD_TYPE_INT
               ,'zip4high'          => DBQUERY_FIELD_TYPE_INT
               ,'rate'              => DBQUERY_FIELD_TYPE_FLOAT
            );
        $tables[$tax_zip_rates]['primary'] = array
            (
                'id'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }


    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * News::getTables() instead of $this->getTables()
     */
    function install()
    {
        include_once dirname(__FILE__).'/install/install.php';
    }

    function uninstall()
    {
        return;
    }


    /**
     * general function
     * gets an unformatted zip
     * ----------------------- !!! CURRENTLY !!!
     * formats it as a US-postal code, 5 or 5+4 digits length
     * ----------------------- !!! CURRENTLY !!!
     * gets a tax rate from DB and zip set
     * returns it as a percent tax rate
     * or returns 0 if zip is illegal or rate not found
     *
     * @param char[] $zip
     * @return float
     */
    function getTaxRateByZip($sid, $zip)
    {
        $zip = preg_replace("/[^0-9]/", '', $zip);

        $len = _ml_strlen($zip);
        if ($len != 5 && $len != 9)
            return 0.0;

        if ($len == 5)
        {
            $zip5 = intval($zip);
            return $this->getTaxRateByZip5($sid, $zip5);
        }
        else
        {
            $zip5 = intval(_ml_substr($zip, 0, 5));
            $zip4 = intval(_ml_substr($zip, 5, 4));
            $rlt = $this->getTaxRateByZip9($sid, $zip5, $zip4);

            if ($rlt !== FALSE)
                return $rlt;

            return $this->getTaxRateByZip5($sid, $zip5);
        }

    }

    /**
     * finds the rate by sid and zip5
     * no zip4 used
     *
     * @param int $sid
     * @param int $zip5
     * @return unknown
     */
    function getTaxRateByZip5($sid, $zip5)
    {
        $rlt = $this->getTaxRateByZip5Strict($sid, $zip5);
        if ($rlt !== FALSE)
            return $rlt;

        $rlt = $this->getTaxRateByZip5Interval($sid, $zip5);
        if ($rlt !== FALSE)
            return $rlt;

        $rlt = $this->getTaxRateByZip5Mask($sid, $zip5);
        if ($rlt !== FALSE)
            return $rlt;

        return 0.0;
    }


    /**
     * returns tax rate by zip5 strict coincidence
     *
     * @param int $zip5
     */
    function getTaxRateByZip5Strict($sid, $zip5)
    {
        $params = array(
            'sid'  => $sid,
            'zip5' => $zip5
        );
        $result = execQuery('SELECT_TAX_RATES_BY_ZIP5_STRICT', $params);
        //printQuery('SELECT_TAX_RATES_BY_ZIP5_STRICT', $params);
        return $this->fetchRateResult($result);
    }

    /**
     * returns tax rate by zip5 falling into interval
     *
     * @param int $zip5
     */
    function getTaxRateByZip5Interval($sid, $zip5)
    {
        $params = array(
            'sid'  => $sid,
            'zip5' => $zip5
        );
        $result = execQuery('SELECT_TAX_RATES_BY_ZIP5_INTERVAL', $params);
        //printQuery('SELECT_TAX_RATES_BY_ZIP5_INTERVAL', $params);
        return $this->fetchRateResult($result);
    }


    /**
     * returns tax rate by zip5 mask
     *
     * @param int $zip5
     */
    function getTaxRateByZip5Mask($sid, $zip5)
    {
        $params = array(
            'sid'  => $sid,
            'zip5' => $zip5
        );
        $result = execQuery('SELECT_TAX_RATES_BY_ZIP5_MASK', $params);
        //printQuery('SELECT_TAX_RATES_BY_ZIP5_MASK', $params);
        return $this->fetchRateResult($result);
    }

    /**
     * finds the rate by sid, zip5 and zip4
     *
     * @param int $sid
     * @param int $zip5
     * @param int $zip4
     * @return float
     */
    function getTaxRateByZip9($sid, $zip5, $zip4)
    {
        $rlt = $this->getTaxRateByZip9Strict($sid, $zip5, $zip4);
        if ($rlt !== FALSE)
            return $rlt;

        $rlt = $this->getTaxRateByZip9Interval($sid, $zip5, $zip4);
        if ($rlt !== FALSE)
            return $rlt;

        $rlt = $this->getTaxRateByZip9Mask($sid, $zip5, $zip4);
        if ($rlt !== FALSE)
            return $rlt;

        return 0.0;
    }


    /**
     * returns tax rate by zip5 strict coincidence
     *
     * @param int $zip5
     * @param int $zip4
     */
    function getTaxRateByZip9Strict($sid, $zip5, $zip4)
    {
        $params = array(
            'sid'  => $sid,
            'zip5' => $zip5,
            'zip4' => $zip4
        );
        $result = execQuery('SELECT_TAX_RATES_BY_ZIP9_STRICT', $params);
        //printQuery('SELECT_TAX_RATES_BY_ZIP9_STRICT', $params);
        return $this->fetchRateResult($result);
    }

    /**
     * returns tax rate by zip5 falling into interval
     *
     * @param int $zip5
     * @param int $zip4
     */
    function getTaxRateByZip9Interval($sid, $zip5, $zip4)
    {
        $params = array(
            'sid'  => $sid,
            'zip5' => $zip5,
            'zip4' => $zip4
        );
        $result = execQuery('SELECT_TAX_RATES_BY_ZIP9_INTERVAL', $params);
        //printQuery('SELECT_TAX_RATES_BY_ZIP9_INTERVAL', $params);
        return $this->fetchRateResult($result);
    }


    /**
     * returns tax rate by zip5 mask
     *
     * @param int $zip5
     * @param int $zip4
     */
    function getTaxRateByZip9Mask($sid, $zip5, $zip4)
    {
        $params = array(
            'sid'  => $sid,
            'zip5' => $zip5,
            'zip4' => $zip4
        );
        $result = execQuery('SELECT_TAX_RATES_BY_ZIP9_MASK', $params);
        //printQuery('SELECT_TAX_RATES_BY_ZIP9_MASK', $params);
        return $this->fetchRateResult($result);
    }


    /**
     * checks result got from getTaxRateByZip*
     * for valid lines and the largest rate
     *
     * @param array $rlt
     */
    function fetchRateResult($rlt)
    {
        if ($rlt === FALSE || !array($rlt) || empty($rlt))
            return FALSE;

        $largest = 0;
        foreach ($rlt as $cell)
        {
            if ($cell["rate"] >= $largest)
                $largest = $cell['rate'];
        }
        return $largest;
    }


    /**
     * returns tax rate zip set properties from DB
     *
     * @param int $sid
     * @return array
     */
    function getSet($sid)
    {
        $params = array('sid' => $sid);
        $result = execQuery('SELECT_TAX_RATES_SET_BY_ID', $params);
        return $result;
    }


    /**
     * returns a simple list of [sid] => [set name]
     * active sets only
     *
     * @return array
     */
    function getSetsList()
    {
        $result = execQuery('SELECT_TAX_RATES_SETS_LIST', array());

        $arr = array();
        foreach ($result as $cell)
        {
            $arr[$cell["id"]] = $cell["name"];
        }

        return $arr;
    }


    /**
     * returns a complex list of all active zip sets
     *
     * @return unknown
     */
    function getSetsFullList()
    {
        $result = execQuery('SELECT_TAX_RATES_SETS_LIST', array());
        return $result;
    }

    /**
     * adds new rates set to DB
     * set is created as inactive
     *
     * @param char[] $descr
     */
    function addSetToDB($descr, $filename)
    {
        global $application;
        $tables = TaxRateByZip::getTables();
        $table = "tax_zip_sets";
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue($descr, $columns['name']);
        $query->addInsertExpression($query->fNow(), $columns['dt']);
        $query->addInsertValue("0", $columns['isActive']);
        $query->addInsertValue($filename, $columns["filecaption"]);

        $application->db->getDB_Result($query);
        $rlt = $application->db->DB_Insert_Id();

        return $rlt;
    }


    /**
     * activates locked set, so it is visible in list and can be used
     *
     * @param int $sid
     */
    function activateSetInDB($sid)
    {
        global $application;

        $base64 = '';
        $set = TaxRateByZip::getSet($sid);
        if (isset($set[0]['filecaption']))
        {
            $base64 = TaxRateByZip::copyCsvFilewnloads($set[0]['filecaption']);
            if ($application->getAppIni("PATH_CACHE_DIR") == dirname(dirname($set[0]['filecaption'])).'/')
            {
                if (is_file($set[0]['filecaption']))
                    unlink($set[0]['filecaption']);
                rmdir(dirname($set[0]['filecaption']));
            }
        }

        $tables = TaxRateByZip::getTables();
        $table = "tax_zip_sets";
        $columns = $tables[$table]['columns'];

        $query = new DB_Update($table);
        $query->addUpdateValue($columns['isActive'], 1);
        $query->addUpdateValue($columns['filename'], basename($base64));
        $query->addUpdateValue($columns['filecaption'], basename($set[0]['filecaption']));
        $query->WhereValue($columns['id'], DB_EQ, $sid);
        $application->db->getDB_Result($query);
    }


    /**
     * clears all rates belonging to zip set
     *
     * @param int $sid
     */
    function clearSetInDB($sid)
    {
        global $application;
        $tables = TaxRateByZip::getTables();
        $table = "tax_zip_rates";
        $columns = $tables[$table]['columns'];

        $query = new DB_Delete($table);
        $query->WhereValue($columns['sid'], DB_EQ, $sid);
        $application->db->getDB_Result($query);
    }

    /**
     * deletes set and it's rates from DB
     *
     * @param int $sid
     */
    function deleteSetFromDB($sid)
    {
        global $application;
        // delete file, if present
        $set = $this->getSet($sid);
        if (isset($set[0]['filename']))
        {
            $fullpath = $application->getAppIni("PRODUCT_FILES_DIR") . "TaxRateByZip_CSVs/" . $set[0]['filename'];
            if (is_file($fullpath))
                unlink($fullpath);
        }

        // delete set
        $tables = TaxRateByZip::getTables();
        $table = "tax_zip_sets";
        $columns = $tables[$table]['columns'];

        $query = new DB_Delete($table);
        $query->WhereValue($columns['id'], DB_EQ, $sid);
        $application->db->getDB_Result($query);

        // delete set rates
        $this->clearSetInDB($sid);
    }


    /**
     * updates set's rates in DB
     * imports new set, kills an old one
     * and switches id and sid to substitute
     *
     * @param int $upd_sid
     * @param int $sid
     */
    function substituteSetInDB($upd_sid, $sid)
    {
        global $application;

        // delete the old one
        $this->deleteSetFromDB($upd_sid);

        $tables = TaxRateByZip::getTables();

        // alter rates' sids
        $table = "tax_zip_rates";
        $columns = $tables[$table]['columns'];

        $query = new DB_Update($table);
        $query->addUpdateValue($columns['sid'], $upd_sid);
        $query->WhereValue($columns['sid'], DB_EQ, $sid);
        $application->db->getDB_Result($query);

        // alter set's id
        $table = "tax_zip_sets";
        $columns = $tables[$table]['columns'];

        $query = new DB_Update($table);
        $query->addUpdateValue($columns['id'], $upd_sid);
        $query->WhereValue($columns['id'], DB_EQ, $sid);
        $application->db->getDB_Result($query);
    }

    /**
     * adds rate into database
     *
     * @param array $rate
     */
    function addRateToDB($rate, $sid)
    {
        global $application;
        if ($sid < 0)
            return;

        $tables = TaxRateByZip::getTables();
        $table = "tax_zip_rates";
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue($sid,              $columns['sid']);
        $query->addInsertValue($rate["ZipCode"],  $columns['zip5']);
        $query->addInsertValue($rate["Zip5Low"],  $columns['zip5low']);
        $query->addInsertValue($rate["Zip5High"], $columns['zip5high']);
        $query->addInsertValue($rate["Zip5Mask"], $columns['zip5mask']);
        $query->addInsertValue($rate["Zip4Low"],  $columns['zip4low']);
        $query->addInsertValue($rate["Zip4High"], $columns['zip4high']);
        $query->addInsertValue($rate["SalesTaxRatePercent"], $columns['rate']);

        $application->db->getDB_Result($query);
    }


    /**
     * adds rates to DB from an array
     *
     * @param array $data
     */
    function addRatesArrayToDB($data, $sid)
    {
        global $application;

        if (empty($data))
            return;

        loadCoreFile('db_multiple_insert.php');
        $tables = TaxRateByZip::getTables();
        $table = "tax_zip_rates";
        $columns = $tables[$table]['columns'];
        $fields = array(
                        $columns["sid"],
                        $columns['zip5'],
                        $columns['zip5low'],
                        $columns['zip5high'],
                        $columns['zip5mask'],
                        $columns['zip4low'],
                        $columns['zip4high'],
                        $columns['rate']
            );

        $query = new DB_Multiple_Insert($table);
        $query->setInsertFields($fields);

        foreach ($data as $key => $value)
        {
            $params = array(
                        $columns["sid"] => $sid,
                        $columns['zip5'] => $value["ZipCode"],
                        $columns['zip5low'] => $value["Zip5Low"],
                        $columns['zip5high'] => $value["Zip5High"],
                        $columns['zip5mask'] => $value["Zip5Mask"],
                        $columns['zip4low'] => $value["Zip4Low"],
                        $columns['zip4high'] => $value["Zip4High"],
                        $columns['rate'] => $value["SalesTaxRatePercent"]
            );
            $query->addInsertValuesArray($params);
        }

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();
    }


    /**
     * returns tax set records number
     *
     * @param int $sid
     * @return int
     */
    function getTaxSetRecordsNumber($sid)
    {
        $result = execQuery("SELECT_TAX_SET_RECORDS_NUMBER", array("sid" => $sid));
        return $result[0]['rec_num'];
    }


    /**
     * finds an unique filename and checks that it is really unique
     *
     * @param char[] $filename
     */
    function getUniqueFilename($filename)
    {
        global $application;
        $dir = $application->getAppIni("PRODUCT_FILES_DIR");
        for ($i=0; $i < 10; $i++)
        {
            $base64 = base64_encode($filename.microtime());
            $base64 = str_replace("=", '', $base64);

            if (!file_exists($dir.$base64))
                break;
//            else
                //
        }
        return $base64.'.csv';
    }


    /**
     * function copies CSV file to avactis-downloads
     * under a new unique name
     * returns actual destination path
     *
     * @param char[] $srcpath
     */
    function copyCsvFilewnloads($srcpath)
    {
        global $application;
        $base64fn = $this->getUniqueFilename(basename($srcpath));
        $dest_dir = $application->getAppIni("PRODUCT_FILES_DIR") . "TaxRateByZip_CSVs/";
        $destpath = $dest_dir . $base64fn;

        if (!is_dir($dest_dir))
        {
            if (!mkdir($dest_dir))
            {
                return '';
            }
        }

        if (copy($srcpath, $destpath));
            return $destpath;

        return '';
    }


    /**
     * function clears inactive sets and their rates
     * select id from tax_zip_sets where UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(dt) > 3600;
     * select all, delete one by one
     *
     */
    function clearInactiveSets()
    {
        $result = execQuery("SELECT_TAX_RATES_INACTIVE_AND_OUTOFDATE", array());
        foreach ($result as $cell)
        {
            $this->deleteSetFromDB($cell["id"]);
        }
    }
};