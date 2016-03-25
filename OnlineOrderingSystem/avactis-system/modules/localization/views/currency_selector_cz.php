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
 * @package Localization
 * @author Vadim Lyalikov
 *
 */

class CurrencySelector
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'currency-selector.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
               ,'Item' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function CurrencySelector()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CurrencySelector"))
        {
            $this->NoView = true;
        }
    }

    function outputItems()
    {
    	$res = "";
    	foreach($this->_Items as $item)
    	{
    		$this->_Currency = $item;
            $item_res = $this->templateFiller->fill('Item');
            $res .= $item_res;
    	}
    	return $res;
    }

//    function getViewCacheKey()
//    {
//        return md5(/*serialize($this->getCurrenciesToOutput()) .*/ modApiFunc("Localization", "getSessionDisplayCurrency"));
//    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_FormActionURL'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('CurrencySelector');
        $this->templateFiller->setTemplate($this->template);

        $this->_Items = modApiFunc('Localization','getActiveCurrenciesList', RETURN_AS_OBJECT_LIST);
        foreach ($this->_Items as $cid => $item)
        {
            if ($item['visible'] !== 'true')
                unset($this->_Items[$cid]);
        }
        reset($this->_Items);

        if( count($this->_Items) < 2)
        {
            return $this->templateFiller->fill('ContainerEmpty');
        }
        else
        {
            $application->registerAttributes
            (
                array
                (
                    'Local_Items' => ''
                   ,'Local_CurrencyName' => ''
                   ,'Local_CurrencyId' => ''
                   ,'Local_SetCurrencyLink' => ''
                   ,'Local_CurrencySelected' => ''
                   ,'Local_CurrentCurrency' => ''
                   ,'Local_CurrencyCode' => ''
                )
            );
            return $this->templateFiller->fill('Container');

        };
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
        	case 'Local_SetCurrencyLink':
                $r = new Request();
                $r->setView(CURRENT_REQUEST_URL);
                $r->setAction('SetDisplayCurrency');
                $r->setKey( 'currency_id', '%currency_id_value%');
                $value = $r->getURL();
                break;
        	case 'Local_CurrencyName':
        		$value = $this->_Currency['name'];
        		break;
        	case 'Local_CurrencySelected':
        		$b = $this->_Currency['id'] == modApiFunc("Localization", "getSessionDisplayCurrency");
        		$value = $b ? 'selected="selected"' : "";
        		break;
            case 'Local_CurrencyId':
                $value = $this->_Currency['id'];
                break;
        	case 'Local_Items':
        		$value = $this->outputItems();
        		break;
        	case 'Local_CurrentCurrency':
                $value = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getSessionDisplayCurrency"));
                break;
        	case 'Local_CurrencyCode':
                $value = $this->_Currency['code'];
                break;
        };

        return $value;
    }

    var $_Currency;
    var $_Items;
}



?>