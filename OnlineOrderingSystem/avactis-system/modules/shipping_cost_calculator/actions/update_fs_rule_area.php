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
 *
 * @package Shipping Cost Calculator
 * @access  public
 * @author Ravil Garafutdinov
 */
class UpdateFsRuleArea extends AjaxAction
{

    function UpdateFsRuleArea()
    {
    }

    function saveDataToDB($data)
    {
        modApiFunc("Shipping_Cost_Calculator", "updateFsRuleArea", $data);
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $fsr_id = $request->getValueByKey('FsRule_id');
        $categories_affected = $request->getValueByKey('cat_to_save', false);
        $products_affected   = $request->getValueByKey('prod_to_save', false);

        $data['fsr_id'] = $fsr_id;

        if ($categories_affected)
            $data['cats'] = implode('|', $categories_affected);
        else
            $data['cats'] = '';

        if ($products_affected)
            $data['prods'] = implode('|', $products_affected);
        else
            $data['prods'] = '';

        if ($fsr_id)
            $this->saveDataToDB($data);

        $SessionPost = array('ViewState' => array('hasCloseScript' => true));
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request->setView('EditFsRuleArea');
        $request->setKey('FsRule_id', $fsr_id);
        $application->redirect($request);
    }
};

?>