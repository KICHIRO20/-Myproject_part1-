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
 * @package ShippingCostCalculator (old name is ShippingCostCorrector)
 * @author Egor V. Derevyankin, Ravil Garafutdinov
 */

$moduleInfo = array
(
        'name'         => 'Shipping_Cost_Calculator',
        'shortName'    => 'SCC',
        'groups'       => 'Main',
        'description'  => 'Shipping Cost Calculator',
        'version'      => '0.2.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'shipping_cost_calculator_api.php',
        'resFile'      => 'shipping-cost-calculator-messages',
        'constantsFile'=> 'const.php',
        'extraAPIFiles' => array(
            'ShippingCompositor' => 'abstract/shipping_compositor.php'),

        'actions'   => array
        (
            'AdminZone' => array(
                 'update_scc_settings' => 'update_scc_settings.php'
                ,'DeleteFsRule'     => 'delete_fs_rule.php'
                ,'AddFsRuleInfo'    => 'add_fs_rule_info.php'
                ,'UpdateFsRuleInfo' => 'update_fs_rule.php'
                ,'UpdateFsRuleArea' => 'update_fs_rule_area.php'
            ),
             "CalculateShippingCZ" => 'calculate_shipping_cz.php'
        ),

        'hooks'     => array
        (
        ),

        'views'     => array
        (
            'AdminZone' => array
            (
                 'ShippingCostCalculatorSection'  => 'shipping-cost-calculator-section.php'
                ,'ShippingCostCalculatorSettings' => 'shipping-cost-calculator-settings.php'
                ,'AddFsRule'        => 'add-fs-rule-az.php'
                ,'EditFsRule'       => 'edit-fs-rule-az.php'
                ,'EditFsRuleArea'   => 'edit-fs-rule-area-az.php'
                ,'FreeShippingRulesList' => 'fs-rules-list-az.php'
            ),
            'CustomerZone' => array
            (
                'ShippingCalculator' => 'shipping_calculator_cz.php'
            )
        )
);

?>