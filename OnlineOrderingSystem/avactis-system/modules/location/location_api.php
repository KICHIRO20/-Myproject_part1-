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
 * Location module.
 *
 * @package Location
 * @author Alexander Girin
 */
class Location
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
    function Location()
    {
    }

    function get_constant($key)
    {
        $constants = array
        (
            "ALL_OTHER_STATES_STATE_ID" => -1
           ,"STATE_UNDEFINED_STATE_ID" => -2
           ,"ALL_OTHER_COUNTRIES_COUNTRY_ID" => -3
           ,"SELECT_COUNTRY_DEFAULT_COUNTRY_ID" => 223 /* USA */
        );


        if(isset($constants[$key]))
        {
            return $constants[$key];
        }
        else
        {
            return NULL;
        }
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

        $tables = Location::getTables();
        $query = new DB_Table_Create($tables);

        //Add countries
        $table = 'countries';                   #the name of the filled table
        $columns = $tables[$table]['columns'];  #the array of field names of the table

        $active_countries = array(13, 14, 21, 39, 74, 82, 106, 125, 151, 193, 222, 223, 103, 172, 154, 139);
        for ($i=1; $i<=237; $i++)
        {
            $country_name_resource = sprintf('COUNTRY_%03d',$i);
            if (! $obj->hasMessage($country_name_resource)) {
                continue;
            }

            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['id']);
            $query->addInsertValue($obj->getMessage($country_name_resource), $columns['name']);
            $codes = $obj->getMessage(sprintf('COUNTRY_CODE_%03d',$i));
            $code_array = explode("/", $codes);
            $query->addInsertValue($code_array[0], $columns['code']);
            $query->addInsertValue($code_array[1], $columns['code3']);
            if (in_array($i, $active_countries))
            {
                $query->addInsertValue("true", $columns['active']);
            }
            else
            {
                $query->addInsertValue("false", $columns['active']);
            }
            if ($i == 223) // USA as default country
            {
                $query->addInsertValue("true", $columns['default']);
            }
            else
            {
                $query->addInsertValue("false", $columns['default']);
            }
            $application->db->getDB_Result($query);
        }

        //Add states
        $table = 'states';                   # the name of the filled table
        $columns = $tables[$table]['columns'];  # the array of field names of the table

        $states_to_countries = array(array('offset' => 8,   'country_id' => 13), //Australia
                                     array('offset' => 17,  'country_id' => 14), //Austria
                                     array('offset' => 28,  'country_id' => 21), //Belgium
                                     array('offset' => 40,  'country_id' => 39), //Canada
                                     array('offset' => 135, 'country_id' => 74), //France
                                     array('offset' => 151, 'country_id' => 82), //Germany
                                     array('offset' => 171, 'country_id' => 106),//Italy
                                     array('offset' => 174, 'country_id' => 125),//Luxembourg
                                     array('offset' => 186, 'country_id' => 151),//Netherlands
                                     array('offset' => 238, 'country_id' => 193),//Spain
                                     array('offset' => 372, 'country_id' => 222),//United Kingdom (Great Britain)
                                     array('offset' => 426, 'country_id' => 223),//United States
                                     array('offset' => 452, 'country_id' => 103),//Ireland
                                     array('offset' => 481, 'country_id' => 172),//Portugal
                                     array('offset' => 497, 'country_id' => 154),//New Zealand
                                     array('offset' => 529, 'country_id' => 139),//Mexico
                                     array('offset' => 553, 'country_id' => 10), //Argentina
                                     array('offset' => 572, 'country_id' => 225),//Uruguay
                                     array('offset' => 599, 'country_id' => 30), //Brasil
                                     array('offset' => 614, 'country_id' => 44), //Chile
                                     array('offset' => 615, 'country_id' => 39)  //Canada
                                 );
        $j = 0;

        for ($i=1; $i<=615; $i++)
        {
            if ($i > $states_to_countries[$j]["offset"])
            {
                $j++;
            }
            $query = new DB_Insert($table);
            $query->addInsertValue($i, $columns['id']);
            $query->addInsertValue($states_to_countries[$j]['country_id'], $columns['c_id']);
            $query->addInsertValue($obj->getMessage(sprintf('STATE_%03d',$i)), $columns['name']);
            $query->addInsertValue($obj->getMessage(sprintf('STATE_CODE_%03d',$i)), $columns['code']);
            $query->addInsertValue("true", $columns['active']);
            $query->addInsertValue("false", $columns['default']); //
            $application->db->getDB_Result($query);
        }
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
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of the meta description of the table:
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

        //-----------------------------------
        //  Database meta description for zone
        //-----------------------------------
        $table = 'countries';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.country_id'
               ,'name'              => $table.'.country_name'
               ,'code'              => $table.'.country_code'
               ,'code3'             => $table.'.country_code3'
               ,'active'             => $table.'.country_active'
               ,'default'           => $table.'.country_default'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
               ,'code'              => DBQUERY_FIELD_TYPE_CHAR5
               ,'code3'             => DBQUERY_FIELD_TYPE_CHAR5
               ,'active'            => DBQUERY_FIELD_TYPE_BOOL
               ,'default'           => DBQUERY_FIELD_TYPE_BOOL
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'states';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.state_id'
               ,'c_id'              => $table.'.country_id'
               ,'name'              => $table.'.state_name'
               ,'code'              => $table.'.state_code'
               ,'active'            => $table.'.state_active'
               ,'default'           => $table.'.state_default'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'c_id'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
               ,'code'              => DBQUERY_FIELD_TYPE_CHAR10
               ,'active'            => DBQUERY_FIELD_TYPE_BOOL
               ,'default'           => DBQUERY_FIELD_TYPE_BOOL
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_c' => 'c_id'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function getCountriesFullList()
    {
        $retval = execQuery('SELECT_COUNTRIES_FULL_LIST', array());
        foreach ($retval as $key => $countryInfo)
        {
            $retval[$key]["name"] = prepareHTMLDisplay($countryInfo["name"]);
        }
        return $retval;
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function getStatesFullList($c_id)
    {
        $params = array('country_id'=>(int)$c_id);
        $retval = execQuery('SELECT_STATES_FULL_LIST',$params);
        foreach ($retval as $key => $stateInfo)
        {
            $retval[$key]["name"] = prepareHTMLDisplay($stateInfo["name"]);
        }
        return $retval;
    }


    /**
     * Returns the list of countries.
     *
     * @
     * @param
     * @return
     */
    function getCountries($all_data = true, $ExclusionCountries = array(), $with_states_only = false)
    {
        $countriesArray = array();

        $params = array('all_data'=>$all_data,
                        'ExclusionCountries'=>$ExclusionCountries,
                        'with_states_only'=>$with_states_only);
        $Countries = execQuery('SELECT_COUNTRIES_LIST', $params);

        foreach ($Countries as $country)
        {
            $countriesArray[$country["id"]] = prepareHTMLDisplay($country["name"]);
        }
        return $countriesArray;
    }

    /**
     * Returns the default Country Id
     *
     * @return int country id
     */
    function getDefaultCountryId()
    {
        $result = execQuery('SELECT_DEFAULT_COUNTRY_ID', array());
        if (sizeof($result))
        {
            return $result[0]['id'];
        }
        return 0;
    }

    /**
     * Returns the default State Id in country
     *
     * @param int $c_id - country id
     * @return int country id
     */
    function getDefaultStateId($c_id)
    {
        $params = array('c_id'=>(int)$c_id);
        $result = execQuery('SELECT_DEFAULT_STATE_ID', $params);

        if (sizeof($result))
        {
            return $result[0]['id'];
        }
        return 0;
    }

    /**
     * Returns the list of states.
     *
     * @
     * @param
     * @return
     */
    function getStates($country_id, $all_data = true, $ExclusionStates = array())
    {
        $params = array('country_id'=>(int)$country_id,
                        'all_data'=>$all_data,
                        'ExclusionStates'=>$ExclusionStates);
        $States = execQuery('SELECT_STATES_LIST', $params);

        $statesArray = array();
        if (sizeof($States)>0)
        {
            foreach ($States as $state)
            {
                $statesArray[$state["id"]] = prepareHTMLDisplay($state["name"]);
            }
        }
        return $statesArray;
    }

    /**
     * Returns the number of the states in the country.
     *
     * @
     * @param
     * @return
     */
    function getCountStatesInCountry($country_id)
    {
        $params = array('country_id'=>(int)$country_id);
        $count = execQuery('SELECT_COUNT_STATES_IN_COUNTRY',$params);
        return $count[0]["count"];
    }

    /**
     * Returns javascript arrays, initialized with the data from the countries,
     * states table.
     *
     * @
     * @param
     * @return
     */
    function getJavascriptCountriesStatesArrays($all_data = true, $ExclusionCountries = array(), $ExclusionStates = array(), $bWithAllOtherState = false, $bWithAllOtherCountry = false)
    {
        $retval = "<script type=\"text/javascript\">\n";
        $retval.= "<!--\n";

        $countriesArray = "countriesArray = new Array();\n";
        $statesArray = "statesArray = new Array();\n";
        $countryIdToStatesIdArray = "countryIdToStatesIdArray = new Array();\n";
        $defaultStatesIdArray = "defaultStatesIdArray = new Array();\n";

        $params = array('all_data' => $all_data,
                        'ExclusionCountries' => $ExclusionCountries,
                        'with_states_only' => false);
        $Countries = execQuery('SELECT_COUNTRIES_LIST', $params);

        if($bWithAllOtherCountry === true)
        {
            array_unshift
            (
                $Countries
               ,array
                (
                    "id" => $this->get_constant("ALL_OTHER_COUNTRIES_COUNTRY_ID")
                   ,"name" => getMsg('SYS',"COUNTRY_ALL_LABEL")
                )
            );
        }

        foreach ($Countries as $country)
        {
            if($country["id"] == $this->get_constant("ALL_OTHER_COUNTRIES_COUNTRY_ID"))
            {
                $States = array
                (
                    array
                    (
                        "id" => $this->get_constant("STATE_UNDEFINED_STATE_ID")
                       ,"name" => getMsg('SYS',"STATE_UNDEFINED_LABEL")
                    )
                );
            }
            else
            {
                if ($defaultStateId = $this->getDefaultStateId($country["id"]))
                {
                    $defaultStatesIdArray.= "defaultStatesIdArray['".$country["id"]."'] = '".$defaultStateId."';\n";
                }
                $countriesArray.= "countriesArray['".$country["id"]."'] = '".addslashes($country["name"])."';\n";

                $params = array('country_id'=>(int)($country["id"]),
                                'all_data' => $all_data,
                                'ExclusionStates' => $ExclusionStates);
                $States = execQuery('SELECT_STATES_LIST', $params);

                if($bWithAllOtherState === true)
                {
                    array_unshift
                    (
                        $States
                       ,array
                        (
                            "id" => $this->get_constant("ALL_OTHER_STATES_STATE_ID")
                           ,"name" => getMsg('SYS',"STATE_ALL_LABEL")
                        )
                    );
                }
            }

            if (sizeof($States)>0)
            {
                $countryIdToStatesIdArray.= "countryIdToStatesIdArray['".$country["id"]."'] = new Array(";
                foreach ($States as $state)
                {
                    $statesArray.= "statesArray['".$state["id"]."'] = '".addslashes($state["name"])."';\n";
                    $countryIdToStatesIdArray.= "'".$state["id"]."', ";
                }
                $countryIdToStatesIdArray = _ml_substr($countryIdToStatesIdArray, 0, (_ml_strlen($countryIdToStatesIdArray)-2)).");\n";
            }
        }

        $defaultCountryId = "var defaultCountryId = ".$this->getDefaultCountryId().";\n";

        $retval.= $countriesArray;
        $retval.= $statesArray;
        $retval.= $countryIdToStatesIdArray;
        $retval.= $defaultCountryId;
        $retval.= $defaultStatesIdArray;
        $retval.= "//-->\n";
        $retval.= "</script>";
        return $retval;
    }

    /**
     * Returns the country name by id.
     *
     * @
     * @param
     * @return
     */
    function getCountry($cId)
    {
        if($cId == $this->get_constant("ALL_OTHER_COUNTRIES_COUNTRY_ID"))
        {
            return getMsg('SYS',"COUNTRY_ALL_LABEL");
        }
        else
        {
            $params = array('country_id' => (int)$cId);
            $country = execQuery('SELECT_COUNTRY_BY_ID',$params);
            return isset($country[0]["name"])? prepareHTMLDisplay($country[0]["name"]):"";
        }
    }

    /**
     * Returns the state name by id.
     *
     * @
     * @param
     * @return
     */
    function getState($sId)
    {
        global $application;

        if($sId == $this->get_constant("ALL_OTHER_STATES_STATE_ID"))
        {
            return getMsg('SYS',"STATE_ALL_LABEL");
        }
        else
        if($sId == $this->get_constant("STATE_UNDEFINED_STATE_ID"))
        {
            return getMsg('SYS',"STATE_UNDEFINED_LABEL");
        }
        else
        {
            $params = array('state_id'=>(int)$sId);
            $state = execQuery('SELECT_STATE_BY_ID', $params);

            $obj = &$application->getInstance('MessageResources');
            return isset($state[0]["name"])? prepareHTMLDisplay($state[0]["name"]):$obj->getMessage('STATE_ALL_OTHER');
        }
    }

    /**
     * Updates the country in the database.
     *
     * @
     * @param
     * @return
     */
    function updateCountry($c_id, $c_name, $c_active, $c_default)
    {
        $params = array('c_id'=>(int)$c_id,
                        'c_name'=>$c_name,
                        'c_active'=>$c_active,
                        'c_default'=>$c_default);

        execQuery('UPDATE_COUNTRY_INFO', $params);
    }

    /**
     * Updates the state in the database.
     *
     * @
     * @param
     * @return
     */
    function updateState($s_id, $s_name, $s_active, $s_default)
    {
        $params = array('s_id'=>(int)$s_id,
                        's_name'=>$s_name,
                        's_active'=>$s_active,
                        's_default'=>$s_default);
        execQuery('UPDATE_STATE_INFO', $params);
    }

    function getCountryCode($cId, $code="ISO2")
    {
        $params = array('country_id' => (int)$cId,
                        'code' => $code);
        $country = execQuery('SELECT_COUNTRY_CODE_BY_ID', $params);
        return isset($country[0]["code"])?($country[0]["code"]):"";
    }

    function getStateCode($sId)
    {
        global $application;

        $params = array('state_id' => (int)$sId);
        $state = execQuery('SELECT_STATE_CODE_BY_ID', $params);

        $obj = &$application->getInstance('MessageResources');
        return isset($state[0]["code"])?($state[0]["code"]):"";
    }

    function getCountryCodeByCountryName($cName, $code="ISO2")
    {
        $params = array('code'=>$code,
                        'country_name'=>$cName);
        $country = execQuery('SELECT_COUNTRY_CODE_BY_COUNTRY_NAME', $params);
        return isset($country[0]["code"])?($country[0]["code"]):"";
    }

    function getStateCodeByStateName($sName)
    {
    	global $application;
        $params = array('state_name'=>$sName);
        $state = execQuery('SELECT_STATE_CODE_BY_STATE_NAME', $params);
        $obj = &$application->getInstance('MessageResources');
        return isset($state[0]["code"])?($state[0]["code"]):"";
    }

    function getCountryIdByCountryCode($cCode)
    {
        $params = array('country_code'=>$cCode);
        $country = execQuery('SELECT_COUNTRY_ID_BY_COUNTRY_CODE',$params);
        return isset($country[0]["id"])?($country[0]["id"]):"";
    }

    function getStateIdByStateCode($sCode)
    {
        global $application;
        $params = array('state_code' => $sCode);
        $state = execQuery('SELECT_STATE_ID_BY_STATE_CODE', $params);
        $obj = &$application->getInstance('MessageResources');
        return isset($state[0]["id"])?($state[0]["id"]):"";
    }

    function getStateIdByStateCodeAndCID($sCode, $cId)
    {
        global $application;
        $params = array('state_code' => $sCode, 'country_id' => $cId );
        $state = execQuery('SELECT_STATE_ID_BY_STATE_CODE_AND_CID', $params);
        $obj = &$application->getInstance('MessageResources');
        return isset($state[0]["id"])?($state[0]["id"]):"";
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */
    var $constants = NULL;
    /**#@-*/

}
?>