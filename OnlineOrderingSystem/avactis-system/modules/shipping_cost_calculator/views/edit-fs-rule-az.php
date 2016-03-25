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
_use(dirname(__FILE__).'/add-fs-rule-az.php');

class EditFsRule extends AddFsRule
{
    function EditFsRule()
    {
        global $application;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initDBFormData();
        }
    }

    /**
     *
     * @return
     */
    function initDBFormData()
    {
        global $application;
        $request = $application->getInstance('Request');
        $this->fsr_id = $request->getValueByKey('FsRule_id', -1);
        $this->FsRuleInfo = modApiFunc('Shipping_Cost_Calculator', 'getFsRuleInfo', $this->fsr_id);

        $this->ViewState =
            array(
                "hasCloseScript"   => "false"
                 );

        if ($this->FsRuleInfo !== false)
        {
            $this->POST =
                array(
                    "FsRule_id"         => $this->fsr_id,
                    "FsRuleName"        => $this->FsRuleInfo['rule_name'],
                    "FsRuleMinSubtotal" => $this->FsRuleInfo['min_subtotal'],
                    "FsRuleStrictCart"  => $this->FsRuleInfo['dirty_cart']
                );
        }
        else // error
        {
            $this->POST  =
                array(
                    "FsRule_id"         => -1,
                    "FsRuleName"        => 'error!!!',
                    "FsRuleMinSubtotal" => '-1',
                    "FsRuleStrictCart"  => 0
                );
        }
    }

    function copyFormData()
    {
        AddFsRule::copyFormData();
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        if (isset($SessionPost['FsRule_id']))
        {
            $this->POST['FsRule_id'] = $SessionPost['FsRule_id'];
            $this->fsr_id = $this->POST['FsRule_id'];
        }
        else
        {
            global $application;
            $request = $application->getInstance('Request');
            $this->POST['FsRule_id'] = $request->getValueByKey('FsRule_id', -1);
            $this->fsr_id = $this->POST['FsRule_id'];
        }
    }

    /**
     *
     */
    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents = array(
                           "BookmarksBlock"   => $this->outputBookmarksBlock("scc_details", 1, "edit"),
                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddFsRuleForm")
                    );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("shipping_cost_calculator/edit_fs_rule/", "subtitle.tpl.html", array());
    }

    function outputEffectiveAreaLaconic()
    {
        global $application;

        $area = getMsg('SCC', 'FS_RULE_EFFECTIVE_AREA_MSG');
        $affected = modApiFunc('Shipping_Cost_Calculator', "getCatsProductsAffected", $this->fsr_id);
        $prod_num = (empty($affected['prods'])) ? 0 : count($affected['prods']);
        $cat_num = (empty($affected['cats'])) ? 0 : count($affected['cats']);
        $area = str_replace('{NCAT}', $cat_num, $area);
        $area = str_replace('{NPROD}', $prod_num, $area);

        if ($cat_num == 1)
            $area = str_replace('{CAT_LABEL}', getMsg('SCC', 'FS_RULE_CATEGORY_LABEL'), $area);
        else
            $area = str_replace('{CAT_LABEL}', getMsg('SCC', 'FS_RULE_CATEGORIES_LABEL'), $area);

        if ($prod_num == 1)
            $area = str_replace('{PRODUCT_LABEL}', getMsg('SCC', 'FS_RULE_PRODUCT_LABEL'), $area);
        else
            $area = str_replace('{PRODUCT_LABEL}', getMsg('SCC', 'FS_RULE_PRODUCTS_LABEL'), $area);

        $this->_Template_Contents = array
        (
           "FsRuleEffectiveArea" => $area,
           "FsRuleEffectiveAreaDetails" => $this->outputFsRuleEffectiveAreaDetails($affected, $cat_num, $prod_num)
        );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("shipping_cost_calculator/edit_fs_rule/", "fs_rule_effective_area.tpl.html",array());
    }

    function outputFsRuleEffectiveAreaDetails($affected, $cat_num, $prod_num)
    {
        global $application;

        if ($cat_num == 0 && $prod_num == 0)
            return '';

        $cat_list = array();
        $cat_str = '';
        if ($cat_num != 0)
        {
            loadClass("CCategoryInfo");
            foreach ($affected['cats'] as $cat)
            {
                $obj = new CCategoryInfo($cat);

                // CCategoryInfo::isCategoryIdCorrect() does not exist
                if ($obj->_fCategoryIDIsIncorrect !== true)
                {
                    $cat_list[] = $obj->getCategoryTagValue('Name');
                }
            }
            $cat_str = implode('<br />', $cat_list);
        }

        $prod_list = array();
        $prod_str = '';
        if ($prod_num != 0)
        {
            loadClass("CProductInfo");
            foreach ($affected['prods'] as $prod)
            {
                $obj = new CProductInfo($prod);

                if ($obj->isProductIdCorrect())
                {
                    $prod_list[] = $obj->getProductTagValue('Name');
                }
            }
            $prod_str = implode('<br />', $prod_list);
        }

        $this->_Template_Contents = array
        (
           "CategoriesAffectedList" => $cat_str,
           "ProductsAffectedList" => $prod_str
        );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("shipping_cost_calculator/edit_fs_rule/", "fs_rule_effective_area_details.tpl.html",array());
    }

    function outputFsRuleStrictCartSelect()
    {
        global $application;

        $this->_Template_Contents = array
        (

           "FsRuleStrictCartFieldHint" => $this->Hints->getHintLink(array('SCC_DIRTY_CART_LABEL', 'shipping-cost-calculator-messages')),
           "FsRuleStrictCartOptions"   => $this->outputStrictCartOptions(),
        );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("shipping_cost_calculator/edit_fs_rule/", "fs_rule_strict_cart_select.tpl.html",array());
    }

    /**
     * returns Strict Cart select options
     *
     */
    function outputStrictCartOptions()
    {
        $checked_1 = ' selected';
        $checked_0 = '';

        if ($this->POST['FsRuleStrictCart'] == SCC_DIRTY_CART)
        {
            $checked_0 = ' selected';
            $checked_1 = '';
        }

        $output = "<option value='".SCC_STRICT_CART."'$checked_1>".getMsg('SCC', 'SCC_STRICT_CART_OPTION_NO')."</option>";
        $output .= "<option value='".SCC_DIRTY_CART."'$checked_0>".getMsg('SCC', 'SCC_DIRTY_CART_OPTION_YES')."</option>";
        return $output;
    }

    function getAction()
    {
        return "UpdateFsRuleInfo";
    }

    /**
     *                     ViewState
     */
    function outputViewStateConstants()
    {
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "UpdateFsRuleInfo") . ">";
        $retval.= "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("FsRule_id", $this->POST["FsRule_id"]) . ">";
        return $retval;
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    // currently viewed fs rule id
    var $fsr_id;

    /**
     * Pointer to the template filler object.
     * Needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;
    /**#@-*/

    /**
     * Pointer to the received from action or prepared FORM data.
     */
    var $POST;

    /**
     * View state structure. Comes from action.
     * $SessionPost["ViewState"] structure example:
     * array
     * (
     *     "hasCloseScript"  = "false"           //true/false
     *     "ErrorsArray"     =  array()          //true/false
     *     "LargeImage"      = "image.jpg"       //
     *     "SmallImage"      = "image_small.jpg" //
     * )
     */
    var $ViewState;

    /**
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
}
?>