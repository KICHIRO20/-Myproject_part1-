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
 * @package Froogle
 * @author Egor Makarov
 */
class Froogle_Export
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Froogle_Export constructor
     */
    function Froogle_Export()
    {
        loadCoreFile('html_form.php');
    }

    function outputExpiresDate()
    {
    	$exp_date = time() + (2 * 7 * 24 * 60 * 60); // + 2
        return date('Y-m-d', $exp_date);
    }

    function outputPaymentAcceptedList()
    {
    	$allTypes = modApiFunc('Froogle', 'getPaymentAcceptedList');
        $froogleSettings = modApiFunc('Froogle', 'getSettings');
        $currentTypes = explode(',', $froogleSettings["PAYMENT_ACCEPTED"]);

        $result = "";
        foreach ($allTypes as $paymentType)
        {
        	$checked = (in_array($paymentType, $currentTypes)) ? " checked" : "";
            $result .= "<input type=\"checkbox\" name=\"PA_{$paymentType}\"{$checked}>&nbsp;{$paymentType}<br>";
        }
        return $result;
    }

    function outputStorefrontLinks()
    {
        $html_code = "";

        $config_array = LayoutConfigurationManager::static_get_cz_layouts_list();

        if(sizeof($config_array) > 0)
        {
            $i = 1;
            foreach ($config_array as $layout_config_ini_path => $config)
            {
                $layout_config_ini_path = str_replace('//', '/', $layout_config_ini_path);
                $html_code .= '<input style="margin: 1px;" name="storefront_link" type="radio" value="'.$layout_config_ini_path.'" '.($i==1?'checked':'').'>&nbsp;<A HREF="'.$config['SITE_URL'].'" target="_blank" style="font-size: 10pt; color: blue;">'.$config['SITE_URL'].'</A><br>';
                $i++;
            }
        }
        else
        {
            $html_code .= '<span style="color: red;">'.getMsg('FRG','FG_WRN_NOT_FOUND_STOREFRONTS').'</span>';
        }

        return $html_code;
    }

    function outputDateDropDowns()
    {
        $today = getdate(time() + (4 * 7 * 24 * 60 * 60));  // + 4 weeks

        $years_dd = array(
            'select_name' => 'date_year'
           ,'selected_value' => $today['year']
        	,'class'=> 'form-control input-sm input-xsmall'
           ,'values' => array()
        );

        for($i=0;$i<5;$i++)
            $years_dd['values'][] = array('value'=>$today['year']+$i,'contents'=>$today['year']+$i);

        $months_dd = array(
            'select_name' => 'date_month'
        	,'class'=> 'form-control input-sm input-small'
        	,'selected_value' => sprintf("%02d",$today['mon'])
           ,'values' => array()
        );

        for($i=1;$i<=12;$i++)
            $months_dd['values'][] = array('value'=>sprintf("%02d",$i),'contents'=>date("F",mktime(0,0,0,$i)));

        $days_dd = array(
            'select_name' => 'date_day'
           ,'selected_value' => sprintf("%02d",$today['mday'])
        	,'class'=> 'form-control input-sm input-xsmall'
        	,'values' => array()
        );

        for($i=1;$i<=31;$i++)
            $days_dd['values'][] = array('value'=>sprintf("%02d",$i),'contents'=>$i);

        $html_code = '<table class="form" border="0" cellpadding="0" cellspacing="0"><tr>';
        $html_code .= '<td class="value">'.HtmlForm::genDropdownSingleChoice($years_dd).'</td>';
        $html_code .= '<td class="value">'.HtmlForm::genDropdownSingleChoice($months_dd).'</td>';
        $html_code .= '<td class="value">'.HtmlForm::genDropdownSingleChoice($days_dd).'</td>';
        $html_code .= '</tr></table>';

        return $html_code;
    }

    /**
     *
     */
    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $this->_froogleSettings = modApiFunc('Froogle', 'getSettings');

        $cats_select = array(
            'select_name'    => 'ProductCategory'
           ,'id'             => 'ProductCategory'
           ,'class'			 =>	'form-control input-sm input-small'
           ,'selected_value' => 1
           ,'values'         => array()
        );

        $cats = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);

        foreach($cats as $cat)
            $cats_select['values'][]=array('value'=>$cat['id'],'contents'=>str_repeat('&nbsp;&nbsp;',$cat['level']).$cat['name']);

        $template_contents = array(
           'ExpirationDate' => $this->outputExpiresDate()
          ,'Location'       => $this->_froogleSettings['LOCATION']
          ,'PaymentNotes'   => $this->_froogleSettings['PAYMENT_NOTES']
          ,'PaymentAcceptedList' => $this->outputPaymentAcceptedList()
          ,'ProductListSubcategories' => HtmlForm::genDropdownSingleChoice($cats_select)
          ,'StorefrontLinks' => $this->outputStorefrontLinks()
          ,'DateSelector' => $this->outputDateDropDowns()
        );

        $this->_templateContents=$template_contents;
        $application->registerAttributes($this->_templateContents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("froogle/export/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
    	return getKeyIgnoreCase($tag, $this->_templateContents);
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $_templateContents;

    var $_froogleSettings;

	var $mTmplFiller;

    /**#@-*/

}
?>