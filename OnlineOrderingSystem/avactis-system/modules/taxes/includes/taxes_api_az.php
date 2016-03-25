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

class Taxes_AZ extends TaxesBase
{
    function setTaxClassId($tc_id)
    {
        $this->TaxClassId = $tc_id;
    }

    function getTaxClassId()
    {
        return $this->TaxClassId;
    }

    function unsetTaxClassId()
    {
        $this->TaxClassId = 0;
    }

    function setEditableTaxId($entity, $id)
    {
        eval('$this->editable'.$entity.' = $id;');
    }

    function getEditableTaxId($entity)
    {
        return eval('return $this->editable'.$entity.';');
    }

    function unsetEditableTaxId($entity)
    {
        return eval('$this->editable'.$entity.' = 0;');
    }

    function setCountryId($c_id)
    {
        $this->country_id = $c_id;
    }

    function getCountryId()
    {
        return $this->country_id;
    }

    function unsetCountryId()
    {
        $this->country_id = 0;
    }

    ##########
    ##########

    /**
     * Gets a Product Tax Classes list.
     *
     * @param
     * @return
     */
    function getClassesList()
    {
        global$application;

        $tables = $this->getTables();
        $ptc = $tables['product_tax_classes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($ptc['id'],    'Id');
        $query->addSelectField($ptc['name'],  'Name');
        $query->addSelectField($ptc['descr'], 'Descr');
        $query->addSelectField($ptc['type'],  'Type');
        return $application->db->getDB_Result($query);
    }

    /**
     * Adds Product Tax Class.
     *
     * @param string $name - class name
     * @param string $descr - class description
     * @return
     */
    function addProdTaxClass($name, $descr)
    {
        global$application;

        $tables = $this->getTables();
        $ptc = $tables['product_tax_classes']['columns'];

        $query = new DB_Insert('product_tax_classes');
        $query->addInsertValue($name, $ptc['name']);
        $query->addInsertValue($descr, $ptc['descr']);
        $query->addInsertValue('custom', $ptc['type']);
        $application->db->getDB_Result($query);
    }

    /**
     * Updates Product Tax Class.
     *
     * @param integer $id - class id
     * @param string $name - class name
     * @param string $descr - class description
     * @return
     */
    function updateProdTaxClass($id, $name, $descr)
    {
        global$application;

        $tables = $this->getTables();
        $ptc = $tables['product_tax_classes']['columns'];

        $query = new DB_Update('product_tax_classes');
        $query->addUpdateValue($ptc['name'], $name);
        $query->addUpdateValue($ptc['descr'], $descr);
        $query->WhereValue($ptc['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);
    }

    /**
     * Deletes the Product Tax Class.
     *
     * @param integer $id - class id
     * @return
     */
    function deleteProdTaxClass($id)
    {
        global$application;

        $tables = $this->getTables();
        $ptc = $tables['product_tax_classes']['columns'];

        $query = new DB_Delete('product_tax_classes');
        $query->WhereValue($ptc['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);

        $tr = $tables['tax_rates']['columns'];

        $query = new DB_Delete('tax_rates');
        $query->WhereValue($tr['ptc_id'], DB_EQ, $id);
        $application->db->getDB_Result($query);

        modApiFunc("Catalog", "changeAttributeValueForAllProducts", 8, $id, 1);
    }


    /**
     *  Gets information about Tax Classes.
     */
    function getTaxClassInfo($ptc_id)
    {
        global$application;

        $tables = $this->getTables();
        $ptc = $tables['product_tax_classes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($ptc['id'],    'id');
        $query->addSelectField($ptc['name'],  'name');
        $query->addSelectField($ptc['descr'],  'descr');
        $query->WhereValue($ptc['id'], DB_EQ, $ptc_id);
        $result = $application->db->getDB_Result($query);
        return (sizeof($result) == 1)? $result[0]:array('id' => '0', 'name' => "", 'descr' => "");
    }

    /**
     *
     */
    function setAddProdTaxClassFromCatalog()
    {
        global $application;
        $session = $application->getInstance('Session');
        $session->set('AddProdTaxClassFromCatalog', '1');
    }

    /**
     *
     */
    function getAddProdTaxClassFromCatalog()
    {
        global $application;
        $session = $application->getInstance('Session');
        if ($session->is_set('AddProdTaxClassFromCatalog'))
        {
            return $session->get('AddProdTaxClassFromCatalog');
        }
        return '0';
    }

    /**
     *
     */
    function unsetAddProdTaxClassFromCatalog()
    {
        global $application;
        $session = $application->getInstance('Session');
        return $session->un_set('AddProdTaxClassFromCatalog');
    }

    ##########
    ##########

    /**
     * Gets an addresses type list (Shipping and Billing).
     *
     * @param
     * @return
     */
    function getAddressesList()
    {
        global$application;

        $tables = $this->getTables();
        $ta = $tables['tax_addresses']['columns'];

        $query = new DB_Select();
        $query->addSelectField($ta['id'],    'id');
        $query->addSelectField($ta['name'],  'name');
        return $application->db->getDB_Result($query);
    }

    /**
     * Adds tax.
     *
     * @param
     * @return
     */
    function addTaxName($included_into_price, $name, $address_id, $need_address)
    {
        global$application;

        if($address_id == TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID)
        {
            $address_id = '0';
        }

        $tables = $this->getTables();
        $tn = $tables['tax_names']['columns'];

        $query = new DB_Insert('tax_names');
        $query->addInsertValue($included_into_price, $tn['included_into_price']);
        $query->addMultiLangInsertValue($name, $tn['name'], $tn['id'], 'Taxes');
        $query->addInsertValue($address_id, $tn['ta_id']);
        $query->addInsertValue($need_address === true ? "true" : "false", $tn['needs_address']);
        $application->db->getDB_Result($query);
        return $application->db->DB_Insert_Id();
    }

    /**
     * Updates tax information.
     *
     * @param
     * @return
     */
    function updateTaxName($id, $name, $address_id, $need_address)
    {
        global $application;

        $tables = $this->getTables();
        $tn = $tables['tax_names']['columns'];

        $query = new DB_Update('tax_names');
//        $query->addUpdateValue($tn['included_into_price'], $included_into_price);
        $query->addMultiLangUpdateValue($tn['name'], $name, $tn['id'], $id, 'Taxes');
        $query->addUpdateValue($tn['ta_id'], $address_id);
        $query->addUpdateValue($tn['needs_address'], $need_address === true ? "true" : "false");
        $query->WhereValue($tn['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);
    }

    /**
     * Deletes tax.
     *
     * @param
     * @return
     */
    function deleteTaxName($id)
    {
        global$application;

        $tables = $this->getTables();
        $tn = $tables['tax_names']['columns'];

        $query = new DB_Delete('tax_names');
        $query->deleteMultiLangField($tn['name'], $tn['id'], 'Taxes');
        $query->WhereValue($tn['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);

        $td = $tables['tax_display']['columns'];

        $query = new DB_Delete('tax_display');
        $query->deleteMultiLangField($td['view'], $td['id'], 'Taxes');
        $query->WhereValue($td['formula'], DB_LIKE, '%{'.$id.'}%');
        $application->db->getDB_Result($query);

        $tr = $tables['tax_rates']['columns'];

        $query = new DB_Delete('tax_rates');
        $query->WhereValue($tr['tn_id'], DB_EQ, $id);
        $query->WhereOr();
        $query->WhereValue($tr['formula'], DB_LIKE, '%{t_'.$id.'}%');
        $application->db->getDB_Result($query);
    }

    /**
     * Gets tax information.
     *
     * @param
     * @return
     */
    function getTaxNameInfo($id)
    {
        global$application;

        $tables = $this->getTables();
        $tn = $tables['tax_names']['columns'];

        $query = new DB_Select();
        $query->addSelectField($tn['id'],    'Id');
        $query->addSelectField($tn['included_into_price'],  'included_into_price');
        $query->setMultiLangAlias('_name', 'tax_names', $tn['name'], $tn['id'], 'Taxes');
        $query->addSelectField($query->getMultiLangAlias('_name'),  'Name');
        $query->addSelectField($tn['needs_address'],  'needs_address');
        $query->addSelectField($tn['ta_id'],  'AddressId');
        $query->WhereValue($tn['id'], DB_EQ, $id);
        $result = $application->db->getDB_Result($query);
        if (sizeof($result))
        {
            $result = $result[0];
        }
        else
        {
            $result = array(
                            "included_into_price"=> "false"
                           ,"Name"               => ""
                           ,"Id"                 => ""
                           ,"needs_address"      => "true"
                           ,"AddressId"          => "1"
                           );
        }
        return $result;
    }

    /**
     * Gets a Display Options list.
     *
     * @param
     * @return
     */
    function getDisplayOptionsList()
    {
        global$application;

        $tables = $this->getTables();
        $tdo = $tables['tax_display_options']['columns'];

        $query = new DB_Select();
        $query->addSelectField($tdo['id'], 'id');
        $query->addSelectField($tdo['name'],  'name');
        return $application->db->getDB_Result($query);
    }

    /**
     * Adds Display Option.
     *
     * @param
     * @return
     */
    function addTaxDisplayOption($formula, $option_id, $display_view)
    {
        global$application;

        $tables = $this->getTables();
        $td = $tables['tax_display']['columns'];

        $query = new DB_Insert('tax_display');
        $query->addInsertValue($formula, $td['formula']);
        $query->addInsertValue($option_id, $td['tdo_id']);
        $query->addMultiLangInsertValue($display_view, $td['view'], $td['id'], 'Taxes');
        return $application->db->getDB_Result($query);
    }

    /**
     * Updates Display Option.
     *
     * @param
     * @return
     */
    function updateTaxDisplayOption($id, $formula, $option_id, $display_view)
    {
        global $application;

        $tables = $this->getTables();
        $td = $tables['tax_display']['columns'];

        $query = new DB_Update('tax_display');
        $query->addUpdateValue($td['formula'], $formula);
        $query->addUpdateValue($td['tdo_id'], $option_id);
        $query->addMultiLangUpdateValue($td['view'], $display_view, $td['id'], $id, 'Taxes');
        $query->WhereValue($td['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);
    }

    /**
     * Deletes Display Option.
     *
     * @param
     * @return
     */
    function deleteTaxDisplayOption($id)
    {
        global$application;

        $tables = $this->getTables();
        $td = $tables['tax_display']['columns'];

        $query = new DB_Delete('tax_display');
        $query->deleteMultiLangField($td['view'], $td['id'], 'Taxes');
        $query->WhereValue($td['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);
    }

    /**
     * Gets Display Option information.
     *
     * @param
     * @return
     */
    function getTaxDisplayOptionInfo($id)
    {
        global$application;

        $tables = $this->getTables();
        $td = $tables['tax_display']['columns'];

        $query = new DB_Select();
        $query->addSelectField($td['id'],    'Id');
        $query->addSelectField($td['formula'],  'Formula');
        $query->addSelectField($td['tdo_id'],  'OptionId');
        $query->setMultiLangAlias('_view', 'tax_display', $td['view'], $td['id'], 'Taxes');
        $query->addSelectField($query->getMultiLangAlias('_view'),  'Display');
        $query->WhereValue($td['id'], DB_EQ, $id);
        $result = $application->db->getDB_Result($query);
        if (sizeof($result))
        {
            $result = $result[0];
        }
        else
        {
            $result = array(
                            "Formula"               => ""
                           ,"Id"                    => ""
                           ,"OptionId"              => "1"
                           ,"Display"               => ""
                           );
        }
        return $result;
    }

    /**
     * Gets a Shipping Modules list to calculate a shipping tax for.
     *
     * @param
     * @return
     */
    function getShippingModulesList($full = true)
    {
        global $application;

        $ShippingModulesList = array();

        $selected_sm_list = array();

        $tables = $this->getTables();
        $ts = $tables['tax_settings']['columns'];

        $query = new DB_Select();
        $query->addSelectField($ts['val'], 'SelectedSM');
        $query->WhereValue($ts['key'], DB_EQ, 'SELECTED_SHIPPING_MODULES_LIST');
        $result = $application->db->getDB_Result($query);
        $selected_sm_list = unserialize($result[0]['SelectedSM']);
//$sm_list = modApiFunc("Checkout", "getInstalledAndActiveModulesListData", "shipping");
        $sm_list = modApiFunc("Modules_Manager", "getActiveModules", array("ShippingModule"));
        foreach ($sm_list as $sm_item)
        {
            $smInfo = modApiFunc($sm_item->name, "getInfo");
            if ($smInfo['GlobalUniqueShippingModuleID'] == modApiFunc("Checkout", "getAllInactiveModuleId", "shipping"))
            {
                continue;
            }
            if ($full)
            {
                $ShippingModulesList[$smInfo['GlobalUniqueShippingModuleID']] = array("Name" => $smInfo['Name'],
                                    "Checked" => (in_array($smInfo['GlobalUniqueShippingModuleID'], $selected_sm_list))? true:false);
            }
            elseif (in_array($smInfo['GlobalUniqueShippingModuleID'], $selected_sm_list))
            {
                $ShippingModulesList[$smInfo['GlobalUniqueShippingModuleID']] = $smInfo['Name'];
            }
        }
        return $ShippingModulesList;
    }

    function setTaxSetting($key, $val)
    {
        global $application;

        $tables = $this->getTables();
        $ts = $tables['tax_settings']['columns'];

        $query = new DB_Update('tax_settings');
        $query->addUpdateValue($ts['val'], $val);
        $query->WhereValue($ts['key'], DB_EQ, $key);
        $application->db->getDB_Result($query);
    }

    ##########
    ##########

    /**
     * Gets a price list to calculate taxes for.
     *
     * @return
     */
    function getTaxCostsList()
    {
        global$application;

        $tables = $this->getTables();
        $tc = $tables['tax_costs']['columns'];

        $query = new DB_Select();
        $query->addSelectField($tc['id'],    'id');
        $query->addSelectField($tc['name'],  'name');
        return $application->db->getDB_Result($query);
    }

    /**
     * Returns a tax formulas/rates list for given country, state and
     * tax class.
     * Warning: the entries from
     * state_id = STATE_ID_ALL
     * and
     * tax_class_id = TAX_CLASS_ID_ANY
     * will be returned independently of $state_id and $tax_class_id.
     */
    function getTaxRatesList($country_id = -1, $state_id = -1, $tax_class_id = -1, $tax_name_id = -1)
    {
        global$application;

        $tables = $this->getTables();
        $tr = $tables['tax_rates']['columns'];
        $ptc = $tables['product_tax_classes']['columns'];
        $tn = $tables['tax_names']['columns'];

        $query = new DB_Select();
        $query->addSelectField($tr['id'], 'Id');
        $query->addSelectField($tr['c_id'], 'c_id');
        $query->addSelectField($tr['s_id'], 's_id');
        $query->addSelectField($ptc['name'], 'ProductTaxClass');
        $query->addSelectField($ptc['id'], 'tax_class_id');
        $query->addLeftJoin('product_tax_classes', $ptc['id'], DB_EQ, $tr['ptc_id']);
        $query->addLeftJoin('tax_names', $tn['id'], DB_EQ, $tr['tn_id']);
        $query->setMultiLangAlias('_name', 'tax_names', $tn['name'], $tn['id'], 'Taxes');
        $query->addSelectField($query->getMultiLangAlias('_name'),  'TaxName');
        $query->addSelectField($tn['id'], 'tax_name_id');
        $query->addSelectField($tr['rate'], 'Rate');
        $query->addSelectField($tr['formula'], 'Formula');
        $query->addSelectField($tr['applicable'], 'Applicable');
        $query->addSelectField($tr['rates_set'], 'rates_set');

        $query->WhereValue('', '', '1');
        if($country_id != -1 &&
           $country_id != TAXES_COUNTRY_NOT_NEEDED_ID)
        {
            $query->WhereAnd();
            $query->addWhereOpenSection();
            $query->WhereValue($tr['c_id'], DB_EQ, $country_id);
            $query->WhereOR();
            $query->WhereValue($tr['c_id'], DB_EQ, TAXES_COUNTRY_NOT_NEEDED_ID);
            $query->addWhereCloseSection();
        }
        if($state_id != -1 &&
           $state_id != TAXES_STATE_NOT_NEEDED_ID)
        {
            $query->WhereAnd();
            $query->addWhereOpenSection();
            $query->WhereValue($tr['s_id'], DB_EQ, $state_id);
            $query->WhereOR();
            $query->WhereValue($tr['s_id'], DB_EQ, STATE_ID_ALL);
            $query->WhereOR();
            $query->WhereValue($tr['s_id'], DB_EQ, TAXES_STATE_NOT_NEEDED_ID);
            $query->addWhereCloseSection();
        }
        if($tax_class_id != -1)
        {
            $query->WhereAnd();
            $query->addWhereOpenSection();
            $query->WhereValue($tr['ptc_id'], DB_EQ, $tax_class_id);
            $query->WhereOR();
            $query->WhereValue($tr['ptc_id'], DB_EQ, TAX_CLASS_ID_ANY);
            $query->addWhereCloseSection();
        }
        if($tax_name_id != -1)
        {
            $query->WhereAnd();
            $query->WhereValue($tr['tn_id'], DB_EQ, $tax_name_id);
        }
        return $application->db->getDB_Result($query);
    }

    /**
     *
     *
     * @param
     * @return
     */
    function getTaxRateInfo($id)
    {
        global$application;

        $tables = $this->getTables();
        $tr = $tables['tax_rates']['columns'];
        $ptc = $tables['product_tax_classes']['columns'];
        $tn = $tables['tax_names']['columns'];

        $query = new DB_Select();
        $query->addSelectField($tr['id'],     'Id');
        $query->addSelectField($tr['c_id'],   'CountryId');
        $query->addSelectField($tr['s_id'],   'StateId');
        $query->addSelectField($tr['ptc_id'], 'ProductTaxClassId');
        $query->addSelectField($tr['tn_id'],  'TaxNameId');
        $query->addSelectField($tr['rate'],   'Rate');
        $query->addSelectField($tr['formula'],'Formula');
        $query->addSelectField($tr['applicable'], 'Applicable');
        $query->addSelectField($tr['rates_set'], 'rates_set');
        $query->addSelectField($ptc['name'], 'ProductTaxClass');
        $query->addLeftJoin('product_tax_classes', $ptc['id'], DB_EQ, $tr['ptc_id']);
        $query->addLeftJoin('tax_names', $tn['id'], DB_EQ, $tr['tn_id']);
        $query->setMultiLangAlias('_name', 'tax_names', $tn['name'], $tn['id'], 'Taxes');
        $query->addSelectField($query->getMultiLangAlias('_name'),  'TaxName');
        $query->WhereValue($tr['id'], DB_EQ, $id);
        $result = $application->db->getDB_Result($query);
        if (sizeof($result))
        {
            $result = $result[0];
        }
        else
        {
            $result = array(
                            "Id"                    => ""
                           ,"CountryId"             => "0"
                           ,"StateId"               => "-1"
                           ,"ProductTaxClassId"     => "1"
                           ,"TaxNameId"             => "0"
                           ,"Rate"                  => ""
                           ,"Formula"               => ""
                           ,"Applicable"            => "true"
                           );
        }
        return $result;
    }

    /**
     *
     *
     * @param
     * @return
     */
    function addTaxRate($c_id, $s_id, $ptc_id, $tn_id, $rate, $formula, $applicable, $rate_set_id)
    {
        global$application;

        $tables = $this->getTables();
        $tr = $tables['tax_rates']['columns'];

        $query = new DB_Insert('tax_rates');
        $query->addInsertValue($c_id, $tr['c_id']);
        $query->addInsertValue($s_id, $tr['s_id']);
        $query->addInsertValue($ptc_id, $tr['ptc_id']);
        $query->addInsertValue($tn_id, $tr['tn_id']);
        $query->addInsertValue($rate, $tr['rate']);
        $query->addInsertValue($formula, $tr['formula']);
        $query->addInsertValue($applicable, $tr['applicable']);
        $query->addInsertValue($rate_set_id, $tr['rates_set']);
        $application->db->getDB_Result($query);
    }

    /**
     *
     *
     * @param
     * @return
     */
    function updateTaxRate($id, $c_id, $s_id, $ptc_id, $tn_id, $rate, $formula, $applicable, $rates_set)
    {
        global$application;

        $tables = $this->getTables();
        $tr = $tables['tax_rates']['columns'];

        $query = new DB_Update('tax_rates');
        $query->addUpdateValue($tr['c_id'], $c_id);
        $query->addUpdateValue($tr['s_id'], $s_id);
        $query->addUpdateValue($tr['ptc_id'], $ptc_id);
        $query->addUpdateValue($tr['tn_id'], $tn_id);
        $query->addUpdateValue($tr['rate'], $rate);
        $query->addUpdateValue($tr['formula'], $formula);
        $query->addUpdateValue($tr['applicable'], $applicable);
        $query->addUpdateValue($tr['rates_set'], $rates_set);
        $query->WhereValue($tr['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);
    }

    function deleteTaxRate($id)
    {
        global$application;

        $tables = $this->getTables();
        $tr = $tables['tax_rates']['columns'];

        $query = new DB_Delete('tax_rates');
        $query->WhereValue($tr['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);
    }

    function getTaxFormula($tax_rate_id)
    {
        if (!$tax_rate_id||$tax_rate_id==0)
        {
            return "";
        }

        global$application;

        $tables = $this->getTables();
        $tr = $tables['tax_rates']['columns'];
        $query = new DB_Select();
        $query->addSelectField($tr['rate'],   'Rate');
        $query->addSelectField($tr['formula'],'Formula');
        $query->WhereValue($tr['id'], DB_EQ, $tax_rate_id);
        $result = $application->db->getDB_Result($query);

        if (sizeof($result) == 0)
        {
            return "";
        }
        return $result[0];
    }

    function getTaxFormulaViewFull($tax_rate_id, $specific_rate = "")
    {
        if (!$tax_rate_id||$tax_rate_id==0)
        {
            return "";
        }

        global$application;
        $MessageResources = &$application->getInstance('MessageResources');

        $tables = $this->getTables();
        $tr = $tables['tax_rates']['columns'];
        $tn = $tables['tax_names']['columns'];

        $query = new DB_Select();
        $query->addSelectField($tr['rate'],   'Rate');
        $query->addSelectField($tr['formula'],'Formula');
        $query->addSelectField($tr['applicable'],'Applicable');
        $query->addLeftJoin('tax_names', $tn['id'], DB_EQ, $tr['tn_id']);
        $query->setMultiLangAlias('_name', 'tax_names', $tn['name'], $tn['id'], 'Taxes');
        $query->addSelectField($query->getMultiLangAlias('_name'),  'TaxName');
        $query->WhereValue($tr['id'], DB_EQ, $tax_rate_id);
        $result = $application->db->getDB_Result($query);

        if (sizeof($result) == 0)
        {
            return "";
        }
        $result = $result[0];
        if ($specific_rate != "")
        {
            $result['Rate'] = "[$specific_rate]";
        }
        if ($result["Applicable"] == "false")
        {
            return prepareHTMLDisplay($result['TaxName'])." = ".$MessageResources->getMessage('TAX_RATE_NOT_APPLICABLE_LABEL');
        }
        $replace = array();
        foreach ($this->getTaxNamesList() as $taxNameInfo)
        {
            $replace['{t_'.$taxNameInfo['Id'].'}'] = prepareHTMLDisplay($taxNameInfo['Name']);
        }
        foreach ($this->getTaxCostsList() as $cost)
        {
            $replace['{p_'.$cost['id'].'}'] = $MessageResources->getMessage($cost['name']);
        }
        preg_match_all("/([0-9]+\.?[0-9]+)/", $result['Formula'], $numbers);
        for ($j=0; $j<sizeof($numbers[0]); $j++)
        {
            $replace[$numbers[0][$j]] = modApiFunc("Localization", "num_format", $numbers[0][$j]);
        }
        $result['Formula'] = strtr($result['Formula'], $replace);
        return prepareHTMLDisplay($result['TaxName'])." = ".$result['Rate']."% * (".$result['Formula'].")";
    }

    ##########
    ##########

    /**
     *
     *
     * @param
     * @return
     */
    function addTraceInfo($stage, $TraceInfo)
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');

        $TemplateContent = array();
        switch ($stage)
        {
            case "1":
                $TemplateContent["ProductTaxClassesList"] = "";
                foreach ($TraceInfo["ProductList"] as $ProductInfo)
                {
                    $ptcInfo = $this->getProductTaxClassInfo($ProductInfo["attributes"]["TaxClass"]["value"]);
                    $TemplateContent["ProductTaxClassesList"].= "<TR><TD>Product ".$ProductInfo["CartID"]."</TD><TD>".prepareHTMLDisplay($ptcInfo['value'])."</TD></TR>";
                }
                $TemplateContent["ProductTaxClassesList"].= "<TR><TD valign='top'><b>".$MessageResources->getMessage('TAX_CALCULATOR_TRACE_INFO_001_1')."</b></TD><TD><b>";
                $TaxClassArray = array();
                foreach ($TraceInfo["ProductTaxClassArray"] as $ProductTaxClassId)
                {
                    $ptcInfo = $this->getProductTaxClassInfo($ProductTaxClassId);
                    $TaxClassArray[] = prepareHTMLDisplay($ptcInfo['value']);
                }
                $TemplateContent["ProductTaxClassesList"].= implode("<br>", $TaxClassArray)."</b></td></tr>";
                $TemplateContent["AddressesList"] = "";
                $AddressArray = array();
                foreach ($TraceInfo["AddressesList"] as $key => $AddressInfo)
                {
                    if (!in_array(array($AddressInfo["CountryId"], 0), $AddressArray))
                    {
                        $AddressArray[] = array($AddressInfo["CountryId"], 0);
                    }
                    if (!in_array(array($AddressInfo["CountryId"], $AddressInfo["StateId"]), $AddressArray))
                    {
                        $AddressArray[] = array($AddressInfo["CountryId"], $AddressInfo["StateId"]);
                    }
                    $TemplateContent["AddressesList"].= "<tr><td>".$MessageResources->getMessage(sprintf("TAX_ADDRESS_NAME_%03d", $key))."</td><td>";
                    $CountryName = modApiFunc("Location", "getCountry", $AddressInfo["CountryId"]);
                    $TemplateContent["AddressesList"].= $CountryName.", ".modApiFunc("Location", "getState", $AddressInfo["StateId"])."</td></tr>";
                }
                $TemplateContent["AddressesList"].= "<tr><td valign='top'><b>".$MessageResources->getMessage('TAX_CALCULATOR_TRACE_INFO_002_1')."</b></td><td><b>";
                foreach ($AddressArray as $AddressInfo)
                {
                    $CountryName = modApiFunc("Location", "getCountry", $AddressInfo[0]);
                    $StateName = modApiFunc("Location", "getState", $AddressInfo[1]);
                    $TemplateContent["AddressesList"].= $CountryName.", ".(($StateName == $MessageResources->getMessage('STATE_ALL_OTHER'))? $MessageResources->getMessage('STATE_ALL_LABEL'):$StateName)."<br>";
                }
                $TemplateContent["AddressesList"].= "</b></td></tr>";
                $TemplateContent["TaxRatesListStage1"] = $this->outputTaxRatesList($TraceInfo["TaxRatesList"]);
                break;
            case '2':
                $TemplateContent["ApplicableTaxes"] = "";
                $TemplateContent["NotApplicableTaxes"] = "";
                foreach ($TraceInfo["TaxesArray"] as $TaxInfo)
                {
                    if ($TaxInfo["applicable"])
                    {
                        $TemplateContent["ApplicableTaxes"].= prepareHTMLDisplay($TaxInfo["name"])." (".$MessageResources->getMessage(sprintf('TAX_ADDRESS_NAME_%03d', $TaxInfo["address"])).")<BR>";
                    }
                    else
                    {
                        $TemplateContent["NotApplicableTaxes"].= prepareHTMLDisplay($TaxInfo["name"])." (".$MessageResources->getMessage(sprintf('TAX_ADDRESS_NAME_%03d', $TaxInfo["address"])).")<BR>";
                    }
                }
                break;
            case "3":
                if (!isset($this->TraceInfo["TaxRatesListStage3"]))
                {
                    $this->TraceInfo["TaxRatesListStage3"] = array();
                }
                if (!isset($this->TraceInfo["TaxName3"]))
                {
                    $this->TraceInfo["TaxName3"] = array();
                }
                $this->TraceInfo["TaxRatesListStage3"][$TraceInfo["TaxId"]] = $this->outputTaxRatesList($TraceInfo["TaxRatesList"]);
                $this->TraceInfo["TaxName3"][$TraceInfo["TaxId"]] = prepareHTMLDisplay($TraceInfo["TaxName"]);//." (".$TraceInfo["Address"].")";
                break;
            case "4":
                if (!isset($this->TraceInfo["TaxRatesListStage4"]))
                {
                    $this->TraceInfo["TaxRatesListStage4"] = array();
                }
                if (!isset($this->TraceInfo["Address4"]))
                {
                    $this->TraceInfo["Address4"] = array();
                }
                $CountryName = modApiFunc("Location", "getCountry", $TraceInfo["CountryId"]);
                $StateName = modApiFunc("Location", "getState", $TraceInfo["StateId"]);
                if ($StateName == $MessageResources->getMessage('STATE_ALL_OTHER'))
                {
                    $StateName = $MessageResources->getMessage('STATE_ALL_LABEL');
                    $country_state = $CountryName.", ".$StateName;
                }
                else
                {
                    $country_state = $CountryName.", ".$StateName."' or '".$CountryName.", ".$MessageResources->getMessage('STATE_ALL_LABEL');
                }
                $this->TraceInfo["TaxRatesListStage4"][$TraceInfo["TaxId"]] = $this->outputTaxRatesList($TraceInfo["TaxRatesList"]);
                $this->TraceInfo["Address4"][$TraceInfo["TaxId"]] = $TraceInfo["Address"]." - '".$country_state."'";
                break;
            case "5":
                if (!isset($this->TraceInfo["TaxRatesListStage5"]))
                {
                    $this->TraceInfo["TaxRatesListStage5"] = array();
                }
                if (!isset($this->TraceInfo["TaxRatesListStage5"][$TraceInfo["TaxId"]]))
                {
                    $this->TraceInfo["TaxRatesListStage5"][$TraceInfo["TaxId"]] = array();
                }
                $this->TraceInfo["TaxRatesListStage5"][$TraceInfo["TaxId"]][] = array("TaxRatesList" => $this->outputTaxRatesList($TraceInfo["TaxRatesList"]), "ProductTaxClass" => prepareHTMLDisplay($TraceInfo["ProductTaxClass"]), "ProdInfo" => $TraceInfo["ProdInfo"]);
                break;
            case "6":
                if (!isset($this->TraceInfo["Message"]))
                {
                    $this->TraceInfo["Message"] = array();
                }
                if (!isset($this->TraceInfo["Message"][$TraceInfo["TaxId"]]))
                {
                    $this->TraceInfo["Message"][$TraceInfo["TaxId"]] = array();
                }
                $this->TraceInfo["Message"][$TraceInfo["TaxId"]][] = $TraceInfo["Message"];
                break;
            case "7":
                if (!isset($this->TraceInfo["TaxCalculationOrder"]))
                {
                    $this->TraceInfo["TaxCalculationOrder"] = array();
                }
                if (!in_array($TraceInfo["TaxId"], $this->TraceInfo["TaxCalculationOrder"]))
                {
                    $this->TraceInfo["TaxCalculationOrder"][] = $TraceInfo["TaxId"];
                }
                break;
            default:
                break;
        }
        if (!$this->TraceInfo)
        {
            $this->TraceInfo = $TemplateContent;
        }
        else
        {
            $this->TraceInfo = array_merge($this->TraceInfo, $TemplateContent);
        }
    }

    function outputTaxRatesList($TaxRatesList)
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');
        $retval = "";
        $i = 0;
        foreach ($TaxRatesList as $TaxRateInfo)
        {
            $TaxRatesList[$i]['Country'] = modApiFunc("Location", "getCountry", $TaxRateInfo['CountryId']);
            $state = modApiFunc("Location", "getState", $TaxRateInfo['StateId']);
            $TaxRatesList[$i]['State'] = ($state == $MessageResources->getMessage('STATE_ALL_OTHER'))? "All":$state;
            $i++;
        }

        if (!function_exists('cmp'))
        {
            function cmp ($a, $b)
            {
                return strcmp($a["Country"]." ".$a["State"], $b["Country"]." ".$b["State"]);
            }
        }
        usort($TaxRatesList, "cmp");

        foreach ($TaxRatesList as $TaxRateInfo)
        {
            $TaxRateInfo['CountryState'] = $TaxRateInfo['Country'].", ".$TaxRateInfo['State'];
            $ProductTaxClass = $this->getProductTaxClassInfo($TaxRateInfo['ProductTaxClassId']);
            $TaxRateInfo['ProductTaxClass'] = prepareHTMLDisplay($ProductTaxClass['value']);
            $TaxRateInfo['Formula'] = $this->getTaxFormulaViewFull($TaxRateInfo['Id']);
            $retval.= "<tr><td>".$TaxRateInfo['CountryState']."</td><td>".$TaxRateInfo["ProductTaxClass"]."</td><td>".$TaxRateInfo["Formula"]."</td>";
        }
        return $retval;
    }

    function getTraceInfo()
    {
        return $this->TraceInfo;
    }

    /**
     * Returns tax formula priority.
     * See comments to the functions
     * isTaxFormula ontradictory()
     * and
     * isTaxFormulaCyclic().
     */
    function getTaxFormulaPriority($state_id, $tax_class_id)
    {
        return ((($state_id     == STATE_ID_ALL) ? 0 : 2) +
                (($tax_class_id == TAX_CLASS_ID_ANY) ? 0 : 1));
    }


    /**
     * Returns true, if tax-formulae are equivalent.
     * fals, otherwise.
     */
    function areTwoFormulaeEquivalent($tax_name_id_1
                                     ,$state_id_1
                                     ,$tax_class_id_1
                                     ,$tax_formula_priority_1

                                     ,$tax_name_id_2
                                     ,$state_id_2
                                     ,$tax_class_id_2
                                     ,$tax_formula_priority_2)
    {
        //         TAXES_STATE_NOT_NEEDED_ID                          .
        if($state_id_1 == TAXES_STATE_NOT_NEEDED_ID ||
           $state_id_2 == TAXES_STATE_NOT_NEEDED_ID)
        {
            return ( ($tax_name_id_1          == $tax_name_id_2)
                  && ($tax_class_id_1         == $tax_class_id_2)
                   );
        }
        else
        {
            return ( ($tax_name_id_1          == $tax_name_id_2)
                  && ($state_id_1             == $state_id_2)
                  && ($tax_class_id_1         == $tax_class_id_2)
                  && ($tax_formula_priority_1 == $tax_formula_priority_2)
                   );
        }
    }




// !!!!                         !!!!!!

    /**
     * I.                                        tax        .                     ,
     *                                                                        .
     *                         ,                                                         .
     *
     * II.       .
     *
     * II.1                      .
     *
     *                                        TaxRates.                     :
     *
     *     1) tax_name
     *     2) apply_to
     *
     *     3) tax_class
     *     4) country
     *     5) state
     *
     *          apply_to                        tax_name,
     *             ,                                 tax_name.
     *                                     ,                    tax_class, country   state
     *              .
     *
     * II.2                  .
     *                                                    .
     *
     *                                 ,              state
     *     ALL.                       "           "    N        ,
     *                                         ,                               . N -
     *                     country.
     *                              tax_class.                              ANY.
     *                       Q      ,     Q -
     *     tax_class.
     *              ALL   ANY               ,                                                  .
     *
     *                 II.2.1:                                ,           state          ALL,
     *         tax_class          ANY.
     *
     *                                   (           ALL     ANY)
     *                            (     ) -           II.2     .
     *
     *                                (                   ,                        tax_class),
     *                                     ALL  /    ANY,               priority (         )
     *                                         :
     *
     *        STATE   TAX CLASS  PRIORITY
     *     +---------+---------+
     *     |   ALL   |   ANY   |    0
     *     +---------+---------+
     *     |   ALL   |  CLASS  |    1
     *     +---------+---------+
     *     |  STATE  |   ANY   |    2
     *     +---------+---------+
     *     |  STATE  |  CLASS  |    3
     *     +---------+---------+
     *
     * III. "                  "       .
     *
     * III.1             "                "            .
     *              "                "            .
     *
     *                  III.1.1:
     *          tax-                                  ,                          VII.1.( 4).
     *
     *                          "                "            :
     *          Canada Quebec ANY PST = 0.01
     *          Canada Quebec ANY PST = 0.02
     *
     * IV. "           "       .
     *
     * IV.1             "           "       .
     *              "           "       .
     *
     *
     *         tax_name,
     *         tax_class,
     *         country,
     *         state
     *                                     -                         .
     *                   .
     *
     *                 VI.1.1:
     *            tax                 "         ",                        VIII.1.( 6)          .
     *
     *                         "     ":
     *         PST = ProductPrice + GST
     *         GST = PST
     *      . .    PST,    GST                                 .
     *
     *                             ,                      tax_name_1      ,
     *                        tax_name_2
     *     ( . . tax_name_2          apply_to     tax_name_1),
     *                              ,      ,                    tax_name_2
     *     ( . . tax_name_1          apply_to     tax_name_2).
     *
     * IV.2           (                     )               .
     *
     *              ,                                                            .
     *
     * IV.3           (                     )               .
     *
     *              ,                                                               .
     *
     * V.
     *
     * V.1                       "           "       ?
     *
     *                                                     ,                  "           "       .
     *                                                :
     *         1)                 .
     *         2)                .
     *         3)                 .
     *
     * VI.
     *                                                    ,
     *                                                    ,
     *    "           "       .
     *
     * VI.1        1.
     *                       .
     *
     *                 V.1:                                        tax_name_new
     *     tax-                  "           ",
     *                                              tax_name_new.
     *     (                  )
     *
     *                                   V.1                  ,
     *
     *                                             ("        " ANY   ALL)
     *
     *     isTaxFormulaContradictoryOrCyclic ( new_formula ),     new_formula -
     *                                          .
     *                                                    -
     *     (               )                    "           "       .  . .                    .
     *
     *                   (               )               .
     *
     * VI.2        2.
     *                     .
     *                                           "           "       .
     *            VI.2.1:
     *
     *
     *                   s_1, s_2
     *                   (id)         tn_1, tn_2, tn_3
     *
     *             1) tn_1 =={s_1)==> tn_2
     *             2) tn_2 =={s_1}==> tn_3
     *             3) tn_2 =={s_2}==> tn_3
     *             4) tn_3 =={ALL}==> ShippingCost
     *             5) tn_2 =={ALL}==> tn_1
     *
     *                              (2)                 tn_1 ==> tn_2 ==> tn_1     s_1.
     *
     *                            ,                                  (VI.1).         -
     *                         ,
     *                  ,                         ,
     *             .
     *
     * VI.3        3.
     *                      .                                                        .
     *     : :        .
     *
     * VII.          isTaxFormulaContradictory ( new_formula )
     *
     *        1)
     *
     * VII.1 isTaxFormulaContradictory ( new_formula )
     *
     *                  :
     *        new_formula -                     .                 ,                   I.1
     *                   :
     *        true -                                        new_formula
     *                      tax-       (                   )
     *            "            ".                  .
     *        false -                                        new_formula
     *                      tax-                                "            ".  . .
     *                               .
     *
     *             (          ,                   ):
     *
     *    ( 1)            tax_rates                    ,
     *                . (   II.2)                            priority
     *                             .
     *    ( 2)                ,
     *             tax_name
     *
     *             tax_class
     *             country
     *             state
     *
     *             priority
     *
     *                                                   new_formula
     *    ( 3)             ( 2)        ,    new_formula
     *         -                            "             "         .
     *        (new_formula is contradictory).
     *                false.
     *                      true.
     *
     *        : :                  "                     " (
     *                                                    ).
     *                false.
     *
     * VIII.          isTaxFormulaCyclic ( new_formula )
     *
     *        1)                                       "           " tax-      .
     *
     * VIII.1 isTaxFormulaCyclic ( new_formula )
     *    //  isTaxFormulaSetCyclic ( list of formulae, source tax name id )
     *
     *                  :
     *        new_formula -                     .                 ,                   I.1
     *        !!!WARNING!!!               ,
     *                  tax-       i.e. isTaxFormulaContradictory ( new_formula ) == false
     *
     *                   :
     *        true -                                        new_formula
     *                      tax-       (                   )
     *            "           ".                  .
     *        false -                                        new_formula
     *                      tax-                                "           ".  . .
     *                               .
     *
     *             (          ,                   ):
     *
     *    ( 1)            tax_rates                    ,
     *                . (   II.2)                            priority
     *                             .
     *    ( 2)                ,
     *             tax_class
     *             country
     *             state
     *                                                   new_formula
     *    ( 3)               ( 2)                           new_formula
     *    ( 4)          ( 3)
     *
     *             tax_name
     *
     *             tax_class
     *             country
     *             state
     *
     *             priority
     *
     *         (             )!                                .
     *    ( 5)
     *
     *             tax_class
     *             country
     *             state
     *
     *                           .
     *
     *
     *            tax_name
     *            apply_to
     *            priority
     *
     *         "         "    priority.                      tax_name
     *                               ,
     *           .                           .           -                 .
     *    ( 6)
     *
     *             tax_name
     *             apply_to
     *
     *                                                 tax-      .
     *                        tax_name.        -      tax_name_1 -> tax_name_2,
     *                             apply_to. E.g.              :
     *
     *             tax_name = tax_name_1
     *             apply_to = tax_name_2, tax_name_3, tax_name_5
     *
     *
     *             tax_name_1
     *             tax_name_2
     *             tax_name_3
     *             tax_name_5
     *
     *
     *             tax_name_1 -> tax_name_2
     *             tax_name_1 -> tax_name_3
     *             tax_name_1 -> tax_name_5
     *
     *    ( 7)
     *                        V.1
     *                 new_tax_name (         tax_name             new_formula).
     *                          ,                            (Depth First Search)
     *                    new_tax_name         ( 6).
     *
     *                                -
     *                     false.            new_formula
     *             "           " tax-      .                    .
     *               -
     *                     true.         new_formula               .
     *
     *                   .
     *
     * IX.            .
     */

// !!!!                         !!!!!!
    /**
     * I.  Administrator is trying to add a new tax-formula. It is necessary
     *     to check, whether at the same time formulae cyclicity occurrs during
     *     tax calculations.If such occurrs, then the formula is false and
     *     can not be added.
     *
     * II. Data.
     *
     * II.1 Database structure.
     *
     *     Use the TaxRates table to check. It consists of the fields:
     *
     *     1) tax_name
     *     2) apply_to
     *
     *     3) tax_class
     *     4) country
     *     5) state
     *
     *     The field apply_to contains a list of other tax_name, whose values
     *     are used to calculate the tax tax_name.
     *     Check only those strings, whose values tax_class, country and state
     *     match.
     *
     * II.2 Tax priority.
     *      The semantics of some special values in formulae.
     *
     *     What gets it complicated, is that the field state can take the value
     *     ALL. In this case, the string is divided into N strings. Each of them
     *     contains a specified state from the database, all other fields match.
     *     N is a number of states in the country country.
     *     The same is with tax_class. It can take the value ANY. Then the string
     *     must be divided into Q strings. Q is a number of all possible
     *     tax_class values in the system.
     *     The values ALL and ANY are used to set various priority to different
     *     taxes.
     *
     *     Definition II.2.1: A formula is prime, if state is not equal to ALL, and
     *         tax_class is not equal to ANY.
     *
     *     To get a list of prime formulae (strings) from formulae, which contain
     *     ALL or ANY, see II.2 above.
     *
     *     To each prime string containing a specified state and the defined value
     *     tax_class and taken from the ALL and/or ANY string, the priority is
     *     assigned, according to the following table:
     *
     *        STATE   TAX CLASS  PRIORITY
     *     +---------+---------+
     *     |   ALL   |   ANY   |    0
     *     +---------+---------+
     *     |   ALL   |  CLASS  |    1
     *     +---------+---------+
     *     |  STATE  |   ANY   |    2
     *     +---------+---------+
     *     |  STATE  |  CLASS  |    3
     *     +---------+---------+
     *
     * III. Formulae contradiction.
     *
     * III.1 The definition of two contradictory formulae.
     *       An example of two contradictory formulae.
     *
     *      The definition III.1.1:
     *      Two tax-formulae are called contradictory, if item VII.1.( 4)
     *      is fulfilled.
     *
     *      An informal example of two contradictory formulae:
     *          Canada Quebec ANY PST = 0.01
     *          Canada Quebec ANY PST = 0.02
     *
     * IV. A formulae cyclicity.
     *
     * IV.1 The definition of formulae cyclicity.
     *      An example of formulae cyclicity.
     *
     *     If several strings have similar fields
     *         tax_name,
     *         tax_class,
     *         country,
     *         state
     *     and they have the same priority at the same time, this is an error.
     *     Such cases shouldn't exist in the table.
     *
     *     Definition VI.1.1:
     *     The tax-formulae list is cyclic, if a graph in item VIII.1.( 6)
     *     contains a cycle.
     *
     *     An informal example of a cycle:
     *         PST = ProductPrice + GST
     *         GST = PST
     *     i.e. neither PST, nor GST can't be determined in this example.
     *
     *     Such situation occurs,when to determine tax_name_1,
     *      tax_name_2 should be determined first
     *     (i.e. tax_name_2 is included in apply_to for tax_name_1),
     *     and it itself needs tax_name_2 to be determined
     *     (i.e. tax_name_1 is included in apply_to for tax_name_2).
     *
     * IV.2 A formula table invariant (a required feature).
     *
     *      At any moment no cyclic formula exists in the table.
     *
     * IV.3 A formula table invariant (a required feature).
     *
     *     At any moment no contradictory formula exists in the table.
     *
     * V. A problem.
     *
     * V.1 When can a formulae cyclicity occur?
     *
     *     At any table change it should be checked, whether a formulae cyclicity
     *     occurrs.
     *     The table can be changed in three ways:
     *         1) To add a formula.
     *         2) To delete a formula.
     *         3) To change a formula.
     *
     * VI. The problem solution.
     *    An algorithm to check the existing of the formulae cyclicity.
     *    Such checkings should be done every time the formulae cyclicity
     *    may occur.
     *
     * VI.1 The first way.
     *     Adding a formula.
     *
     *     Statement V.1: If adding a formula for the tax_name_new tax
     *     a formulae cyclicity occurrs in the tax formula table, then it must
     *     occur during defining a formula for the tax_name_new tax.
     *     (without any proof)
     *
     *     According to statement V.1, to check the fact, that when adding a
     *     new formula no formulae cyclicity occurrs, it is necessary to divide
     *     the new formula into prime ones ("        " ANY   ALL) and check
     *     each of them by the algorithm isTaxFormulaContradictoryOrCyclic
     *     ( new_formula ), where new_formula is one of the specified prime
     *     formulae. If the algorithm reports an error to even one of
     *     prime formulae, then a new added non-prime formula creates a
     *     formulae cyclicity. So it can not be added.
     *
     *     Otherwise the non-prime formula can be added.
     *
     * VI.2 The second way.
     *     Deleting a formula.
     *     The deleting a formula can create a formulae cyclicity.
     *     Example VI.2.1:
     *         one tax class
     *         one country
     *         two states s_1, s_2
     *         three tax ids tn_1, tn_2, tn_3
     *         five formulae
     *             1) tn_1 =={s_1)==> tn_2
     *             2) tn_2 =={s_1}==> tn_3
     *             3) tn_2 =={s_2}==> tn_3
     *             4) tn_3 =={ALL}==> ShippingCost
     *             5) tn_2 =={ALL}==> tn_1
     *
     *      the deleting of rule (2) will create a cycle tn_1 ==> tn_2 ==> tn_1 for s_1.
     *
     *      Check the way it is done in item (VI.1). The difference is that a new
     *     formula is not added to the list of all the formulae, that satisfy
     *     the address and the class of the taxes, but is deleted the one, which
     *     is checked on deleting.
     *
     * VI.3 The third way.
     *     Changing a formula. It is equal to deletig an old formula and adding
     *     a new one.
     *     : : to do.
     *
     * VII. The algorithm isTaxFormulaContradictory ( new_formula )
     *    Checks
     *        1) if the formula is contradictory to the one that has already
     *           existed in the database.
     *
     * VII.1 isTaxFormulaContradictory ( new_formula )
     *
     *    Input data:
     *        new_formula - a prime formula. It consists of fields, described in item I.1
     *    Output data:
     *        true if adding the prime formula new_formula to the current number of
     *           tax-formulae (the database table) will not create a contradiction.
     *           It can be added.
     *        false if adding the prime formula new_formula to the current number
     *           tax-formulae will create a contradiction. So it can't be added.
     *
     *    The algorithm (simplified, non-optimized):
     *
     *    ( 1) Select all the formulae from the table tax_rates, divide them into
     *         the prime formulae. (See item II.2) The priority for each of them
     *         will be calculated at the same time.
     *    ( 2) Select formulae, whose values
     *             tax_name
     *
     *             tax_class
     *             country
     *             state
     *
     *             priority
     *
     *         match the values in the formula new_formula.
     *    ( 3) If list ( 2) is not empty, then new_formula can't be added.
     *         It will be contradictory to the equivalent formula.
     *        (new_formula is contradictory).
     *        Return false.
     *        Return true otherwise.
     *
     *        : : give the definition to the equivalent formula (list
     *        five fields to compare by).
     *        Return false.
     *
     * VIII.The algorithm isTaxFormulaCyclic ( new_formula )
     *    Checks
     *        1) if adding this formula to the base will create a tax-formulae
     *            cycle.
     *
     * VIII.1 isTaxFormulaCyclic ( new_formula )
     *    //  isTaxFormulaSetCyclic ( list of formulae, source tax name id )
     *
     *    Input data:
     *        new_formula - a prime formula. It consists of fields, described in item I.1
     *        !!!WARNING!!! It is well known, that its adding will not create
     *        contradictions in the tax-formula table i.e. isTaxFormulaContradictory
     *        ( new_formula ) == false
     *
     *    Output data:
     *        true if adding the prime formula new_formula to the current number of
     *           tax formulae (the database table) will not create a formulae
     *           cyclicity. It can be added.
     *        false if adding the prime formula new_formula to the current number
     *           tax formulae will create a formulae cyclicity. So it can't be added.
     *
     *    The algorithm (simlified, non-optimized):
     *
     *    ( 1) Select all the formulae from the table tax_rates, divide them into
     *         the prime formulae. (See item II.2) The priority for each of them
     *         will be calculated at the same time.
     *    ( 2) Select formulae, whose values
     *             tax_class
     *             country
     *             state
     *         match the values in the formula new_formula.
     *    ( 3) Add the formula new_formula to the selected in item ( 2) formulae.
     *    ( 4) In list ( 3) can't exist two formulae with the same values
     *
     *             tax_name
     *
     *             tax_class
     *             country
     *             state
     *
     *             priority
     *
     *         ( i.e equivalent ones)! It is specified in the item "input data".
     *    ( 5) Then the fields
     *
     *             tax_class
     *             country
     *             state
     *
     *         can be skipped.
     *         The remaining fields are
     *
     *            tax_name
     *            apply_to
     *            priority
     *
     *        Delete the priority. For each value tax_name of all given
     *        in the list, select only one formula that contains it with the
     *        max priority. Delete the rest of them from the list.
     *    ( 6) The remaining fields are
     *
     *             tax_name
     *             apply_to
     *
     *         Create a directed graph of tax-formulae dependences. Vertexes are
     *         the tax_name values. Arcs are the pairs tax_name_1 -> tax_name_2,
     *         created from apply_to. E.g. if values are:
     *
     *             tax_name = tax_name_1
     *             apply_to = tax_name_2, tax_name_3, tax_name_5
     *
     *         then there will be four vertexes in the graph
     *             tax_name_1
     *             tax_name_2
     *             tax_name_3
     *             tax_name_5
     *
     *         and three directed arcs
     *             tax_name_1 -> tax_name_2
     *             tax_name_1 -> tax_name_3
     *             tax_name_1 -> tax_name_5
     *
     *    ( 7)
     *         According to statement V.1, check the cycle existance in
     *         the vertex new_tax_name (value tax_name for formula new_formula).
     *         It can be done using Depth First Search
     *         from the vertex new_tax_name in item ( 6).
     *
     *         If the cycle will be found then
     *             return false. Adding new_formula will create a
     *             tax-formula cyclicity. It can't be added.
     *         Return true
     *             otherwise. The formula new_formula can be added.
     *
     *    The end of the algorithm.
     *
     * IX. The comments.
     */
    function isTaxFormulaContradictory($_tax_name_id,
                                       $_formula,

                                       $_tax_class_id,
                                       $_country_id,
                                       $_state_id,
                                       $_tax_formula_priority
                                      )
    {
        //VII.1.( 2)
        $rates = $this->getTaxRatesList($_country_id, $_state_id, $_tax_class_id, $_tax_name_id);
        //VII.1.( 3)
        foreach($rates as $rate)
        {
            //VII.1.( 1)
            $tax_formula_priority = $this->getTaxFormulaPriority($rate['s_id'], $rate['tax_class_id']);
            if($this->areTwoFormulaeEquivalent($_tax_name_id
                                              ,$_state_id
                                              ,$_tax_class_id
                                              ,$_tax_formula_priority

                                              ,$_tax_name_id
                                              ,$_state_id
                                              ,$_tax_class_id
                                              ,$tax_formula_priority))
            {
                $contradictory_formula_id = $rate['Id'];
                return $contradictory_formula_id;
            }
        }
        return false;
    }


    /**
     * Returns
     *     true if the addition of the specified tax-formula will contradict to
     *         the already existed formula,
     *     false otherwise.
     *     the difference from isTaxFormulaContradictory is that the optional
     *     parameter is passed, $_tax_rate_id - editted formula id.
     *     It should be cosidered as deleted one.
     */
    function isTaxFormulaEdidingContradictory(
                                       $tax_rate_id,
                                       $_tax_name_id,
                                       $_formula,

                                       $_tax_class_id,
                                       $_country_id,
                                       $_state_id,
                                       $_tax_formula_priority
                                      )
    {
        //VII.1.( 2)
        $rates = $this->getTaxRatesList($_country_id, $_state_id, $_tax_class_id, $_tax_name_id);
        //VII.1.( 3)
        foreach($rates as $rate)
        {
            //"Delete a formula".
            if($rate['Id'] == $tax_rate_id)
            {
                continue;
            }

            //VII.1.( 1)
            $tax_formula_priority = $this->getTaxFormulaPriority($rate['s_id'], $rate['tax_class_id']);
            if($this->areTwoFormulaeEquivalent($_tax_name_id
                                              ,$_state_id
                                              ,$_tax_class_id
                                              ,$_tax_formula_priority

                                              ,$_tax_name_id
                                              ,$_state_id
                                              ,$_tax_class_id
                                              ,$tax_formula_priority))
            {
                $contradictory_formula_id = $rate['Id'];
                return $contradictory_formula_id;
            }
        }
        return false;
    }

    /**
     * Returns
     *     true, if the deleting of the specified tax-formula will create
     *         tax-formulae cyclicity,
     *     false otherwise.
     */
    function doesDeletingTaxFormulaCreateCycle($tax_rate_id)
    {
        //Example of cyclicity when changing NotApplicable rule.
        //   Example VI.2.2:
        //       one tax class
        //       one country
        //       two atates s_1, s_2
        //       two tax ids tn_1, tn_2
        //       four formulae
        //       1) tn_1 =={s_1) NotApp
        //       2) tn_1 =={s_2} NotApp
        //       3) tn_1 =={ALL} ==> tn_2
        //       4) tn_2 =={ALL} ==> tn_1
        //
        //       the deleting of the rule
        //       will create a cycle tn_1 ==> tn_2 ==> tn_1 for s_1.
        //

        $tax_rate_info = modApiFunc("Taxes", "getTaxRateInfo", $tax_rate_id);

        //VIII.1.( 2)
        $rates = $this->getTaxRatesList($tax_rate_info['CountryId']
                                       ,$tax_rate_info['StateId']
                                       ,$tax_rate_info['ProductTaxClassId']);
        //VIII.1.( 5)
        //  Save a formula with the gratest priority for each $tax_name_id,
        //  specified in this address.
        $tax_name_id__to__formula = array();

        foreach($rates as $rate)
        {
            //"Delete a formula".
            if($rate['Id'] == $tax_rate_id)
            {
                continue;
            }

            //VIII.1.( 1)
            // If a formula with such $tax_name_id has already been added to the graph,
            // and if the next occurence has a greater priority than the previous one,
            // replace the old occurence with a current one.
            $tax_formula_priority = $this->getTaxFormulaPriority($rate['s_id'], $rate['tax_class_id']);
            if(!key_exists($rate['tax_name_id'], $tax_name_id__to__formula) ||
               $tax_name_id__to__formula[$rate['tax_name_id']]['tax_formula_priority'] < $tax_formula_priority
              )
            {
                $tax_name_id__to__formula[$rate['tax_name_id']] = array('tax_formula_priority' => $tax_formula_priority
                                                                       ,'formula' => $rate['Formula']
                                                                       ,'formula_id' => $rate['Id']
                                                                       ,'applicable' => $rate['Applicable']
                                                                       );
            }
        }

        //Remove "Not Applicable" rates.
        foreach($tax_name_id__to__formula as $key => $info)
        {
            //"Not Applicable" formula
            if($info['applicable'] == "false")
            {
                unset($tax_name_id__to__formula[$key]);
                continue;
            }
        }
        return $this->isTaxFormulaSetCyclic($tax_name_id__to__formula, $tax_rate_info['TaxNameId']);
    }

    /**
     * Returns
     *     true, if the addition of the specified tax-formula will create
     *         a tax-formulae cyclicity,
     *     false otherwise.
     */
    function doesAddingTaxFormulaCreateCycle($_tax_name_id,
                                $_formula,

                                $_tax_class_id,
                                $_country_id,
                                $_state_id,
                                $_tax_formula_priority,
                                $_applicable)
    {
        //Note about NotApplicable tax-rules.
        //May be in such case the cycle will not be created.
///        $_tax_formula_priority = $this->getTaxFormulaPriority($_state_id, $_tax_class_id);
        //VIII.1.( 2)
        $rates = $this->getTaxRatesList($_country_id, $_state_id, $_tax_class_id);
        //VIII.1.( 5)
        //  Save a formula with the gratest priority for each $tax_name_id,
        //  specified in this address.
        $tax_name_id__to__formula = array();
        $tax_name_id__to__formula[$_tax_name_id] = array('tax_formula_priority' => $_tax_formula_priority
                                                        ,'formula' => $_formula
                                                        ,'formula_id' => TAX_FORMULA_ID_UNKNOWN
                                                        ,'applicable' => $_applicable ? "true" : "false"
                                                        );

        foreach($rates as $rate)
        {
            //VIII.1.( 1)
            // If a formula with such $tax_name_id has already been added to the graph,
            // and if the next occurence has a greater priority than the previous one,
            // replace the old occurence with a current one.
            $tax_formula_priority = $this->getTaxFormulaPriority($rate['s_id'], $rate['tax_class_id']);
            if(!key_exists($rate['tax_name_id'], $tax_name_id__to__formula) ||
               $tax_name_id__to__formula[$rate['tax_name_id']]['tax_formula_priority'] < $tax_formula_priority
              )
            {
                $tax_name_id__to__formula[$rate['tax_name_id']] = array('tax_formula_priority' => $tax_formula_priority
                                                                       ,'formula' => $rate['Formula']
                                                                       ,'formula_id' => $rate['Id']
                                                                       ,'applicable' => $rate['Applicable']
                                                                       );
            }
        }

        //Remove "Not Applicable" rates.
        foreach($tax_name_id__to__formula as $key => $info)
        {
            //"Not Applicable" formula
            if($info['applicable'] == "false")
            {
                unset($tax_name_id__to__formula[$key]);
                continue;
            }
        }
        return $this->isTaxFormulaSetCyclic($tax_name_id__to__formula, $_tax_name_id);
    }

    /**
     * Returns
     *     true, if the editing of the specified tax-formula will create
     *         a tax-formulae cyclicity,
     *     false otherwise.
     */
    function doesEditingTaxFormulaCreateCycle($tax_rate_id,
                                $_tax_name_id,
                                $_formula,

                                $_tax_class_id,
                                $_country_id,
                                $_state_id,
                                $_tax_formula_priority,
                                $_applicable)
    {
        //Example of cyclicity when changing NotApplicable rule.
        //   Example VI.3.2: changing from Not_Applicable to non-Not_Applicable:
        //        one tax class
        //        one country
        //        two states s_1, s_2
        //        two tax ids tn_1, tn_2
        //        two formulae
        //       1) tn_1 =={s_1) NotApp
        //       2) tn_2 =={s_1} ==> tn_1
        //
        //       the changing of the rule (1) for
        //          tn_1 =={s_1} ==> tn_2
        //       will create a cycle tn_1 ==> tn_2 ==> tn_1 for s_1.

///        $_tax_formula_priority = $this->getTaxFormulaPriority($_state_id, $_tax_class_id);
        //VIII.1.( 2)
        $rates = $this->getTaxRatesList($_country_id, $_state_id, $_tax_class_id);
        //VIII.1.( 5)
        //  Save a formula with the gratest priority for each $tax_name_id,
        //  specified in this address.
        $tax_name_id__to__formula = array();
        //"Add a formula". A new variant.
        $tax_name_id__to__formula[$_tax_name_id] = array('tax_formula_priority' => $_tax_formula_priority
                                                        ,'formula' => $_formula
                                                        ,'formula_id' => TAX_FORMULA_ID_UNKNOWN
                                                        ,'applicable' => $_applicable ? "true" : "false"
                                                        );

        foreach($rates as $rate)
        {
            //"Delete a formula". It is its old variant.
            if($rate['Id'] == $tax_rate_id)
            {
                continue;
            }

            //VIII.1.( 1)
            // If a formula with such $tax_name_id has already been added to the graph,
            // and if the next occurence has a greater priority than the previous one,
            // replace the old occurence with a current one.
            $tax_formula_priority = $this->getTaxFormulaPriority($rate['s_id'], $rate['tax_class_id']);
            if(!key_exists($rate['tax_name_id'], $tax_name_id__to__formula) ||
               $tax_name_id__to__formula[$rate['tax_name_id']]['tax_formula_priority'] < $tax_formula_priority
              )
            {
                $tax_name_id__to__formula[$rate['tax_name_id']] = array('tax_formula_priority' => $tax_formula_priority
                                                                       ,'formula' => $rate['Formula']
                                                                       ,'formula_id' => $rate['Id']
                                                                       ,'applicable' => $rate['Applicable']
                                                                       );
            }
        }

        //Remove "Not Applicable" rates.
        foreach($tax_name_id__to__formula as $key => $info)
        {
            //"Not Applicable" formula.
            if($info['applicable'] == "false")
            {
                unset($tax_name_id__to__formula[$key]);
                continue;
            }
        }

        $TaxRateInfo = $this->getTaxRateInfo($tax_rate_id);
        $deletion_error = $this->isTaxFormulaSetCyclic($tax_name_id__to__formula, $TaxRateInfo['TaxNameId']);
        if($deletion_error === false)
        {
            $adding_error = $this->isTaxFormulaSetCyclic($tax_name_id__to__formula, $_tax_name_id);
            return $adding_error;
        }
        else
        {
            return $deletion_error;
        }
    }

    function isTaxFormulaSetCyclic($tax_name_id__to__formula, $source_tax_name_id)
    {
        loadCoreFile('tax_formula_translator.php');
        //VIII.1.( 6)
        //Create a graph. Vertexes are $tax_name_id. Arcs are directed relations
        //  between vertexes, taken from tax-formulae.
        $tax_names = modApiFunc("Taxes", "getTaxNamesList");
        //Graph vertex:
        $V = array();
        //Graph arcs:
        $ADJ = array();
        $ADJinfo = array();
        foreach($tax_names as $tax_name)
        {
            $V[] = (int)$tax_name["Id"];
            $ADJ[(int)$tax_name["Id"]] = array();
            $ADJinfo[(int)$tax_name["Id"]] = array();
        }

        //Graph arcs:
        //In the graph arcs is stored additional information: which tax formula contains
        //  this arc. It is necessary to show, which formulae the cycle (not arcs)
        //  consists of.

        foreach($tax_name_id__to__formula as $vertex_id => $vertex_info)
        {
//            //Add a vertex, if it still doesn't exist. There should be no
//            //iterations.
//            if(!in_array($vertex_id, $V))
//            {
//                $V[] = $vertex_id;
//            }

            //Add all arcs.
            //Parse the formula.
            $trans = new TaxRatesTranslator($vertex_info['formula']);
            $trans->parse();
            $ADJ[$vertex_id] = $trans->getTaxList();
            $ADJinfo[$vertex_id] = array("formula_id" => $vertex_info['formula_id']);
        }
        loadCoreFile('directed_graph.php');
        $graph = new DirectedGraph($V, $ADJ);
        if($graph->hasCycleFromGivenSource($source_tax_name_id, $cycle))
        {
            //It's necessary to restore formulae, which in this case
            // (i.e. depending on state and tax_class) match
            // specified arcs in the graph.
            $cycle_with_tax_info = array();

            //Suppose cycle is correct ring
            $previous_vertex_in_cycle = $cycle[sizeof($cycle)-1]['tax_name_id'];
            $i = 0;
            for(;$i < sizeof($cycle); $i++)
            {
                $vertex = $cycle[$i];

                $vertex_with_tax_info = array("tax_name_id" => $vertex,
                                              "formula_id" => $ADJinfo[$vertex]['formula_id'],
                                              "child_tax_name_id" => $previous_vertex_in_cycle);

                $cycle_with_tax_info[] = $vertex_with_tax_info;

                $previous_vertex_in_cycle = $vertex;
                //formula_id in this case formula_id is a formula id, which
                //is used up to the next vertex in the cycle.
            }
            $cycle = $cycle_with_tax_info;
            return $cycle_with_tax_info;//true;
        }
        else
        {
            return false;
        }
    }

    function areTaxNamesValid($formula)
    {
        return true;

/*        $trans = new TaxRatesTranslator($formula);
        $trans->parse();
        $FormulaTaxNames = $trans->getTaxList();
        $DBTaxNames = modApiFunc('Taxes', 'getTaxNamesList');
        foreach($FormulaTaxNames as $FormulaTaxNameId)
        {
        // -                $DBTaxNames
        //                                 ,                     ,
        //                       getTaxNamesList()
            if(!in_array($DBTaxNames, $FormulaTaxNameId))
            {
                return false;
            }
        }
        return true;
*/
    }

    function areProductNamesValid($formula)
    {
        //: to check. See areTaxNamesValid($formula)
        return true;
    }
}
?>