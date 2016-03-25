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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 *
 */

class add_inv_record_to_entity extends AjaxAction
{
    function add_inv_record_to_entity()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $data = array(
            'parent_entity' => $request->getValueByKey('parent_entity')
           ,'entity_id' => $request->getValueByKey('entity_id')
           ,'sku' => $request->getValueByKey('sku')
           ,'quantity' => $request->getValueByKey('quantity')
        );

        $side = array_shift(modApiFunc("Product_Options","_convSides2Combinations",array('side'=>$request->getValueByKey('side'))));

        $trust_add = true;
        $fault_by = '';

        if(!modApiFunc("Product_Options","checkByCRules",$data['parent_entity'],$data['entity_id'],$side,true))
        {
            $trust_add = false;
            $fault_by = 'crules';
        };

        if($trust_add)
        {
            if(modApiFunc("Product_Options","getInventoryIDByCombination",$data['parent_entity'],$data['entity_id'],$side)!=null)
            {
                $trust_add = false;
                $fault_by = 'inventory';
            };
        };

        if($trust_add)
        {
            $data['side'] = $side;
            modApiFunc("Product_Options","addInvRecordToEntity",$data);
        };

        global $_RESULT;
        $_RESULT['fault_by']=$fault_by;
    }
};

?>