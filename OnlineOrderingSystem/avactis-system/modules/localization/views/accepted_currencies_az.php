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
 * @package Localization
 * @author Vadim Lyalikov
 */
class AcceptedCurrencies
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AvactisHomeNews constructor.
     */
    function AcceptedCurrencies()
    {
        $paginator = modAPIFunc('paginator', 'setCurrentPaginatorName', "AcceptedCurrencies");
        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }
    }

    /**
     * Initializes data from the POST array.
     */
    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
    }

    /**
     * Initializes data from the database.
     */
    function initFormData()
    {
        $this->ViewState =
            array("hasCloseScript"  => "false"
                 ,"FormSubmitValue" => "update"
                 );
    }

    /**
     * @return String Return html code for hidden form fields representing @var $this->ViewState array.
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    /**
     * Outputs errors.
     */
    function outputErrors()
    {
        global $application;
        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        $result = "";
        $application->registerAttributes(array('ErrorIndex', 'Error'));
        $this->_error_index = 0;
        foreach ($this->ErrorsArray as $error)
        {
            $this->_error_index++;
            $this->_error = $this->MessageResources->getMessage($error);
//            $result .= $this->mTmplFiller->fill("location/countries_settings/", "error.tpl.html", array());
        }
        return $result;
    }

    /**
     * Outputs the list of countries.
     *
     * @
     * @param
     * @return
     */
    function outputCurrencies()
    {
        global $application;

        $currency_list = modApiFunc("Localization", "getFormatsList", "currency");

        //
        $CurrenciesListSorted = array();
        foreach($currency_list as $value)
        {
            $CurrenciesListSorted[$value["code"]] = $value;
        }
        ksort($CurrenciesListSorted);

        //                                            -                       USD           .
        //       USD    ,                                     .
        $default_i = NULL;
        foreach($CurrenciesListSorted as $i => $info)
        {
        	if($info["dflt"] == DB_TRUE)
        	{
        		$default_i = $i;
        		break;
        	}
        }
        if($default_i === NULL)
        {
        	if(isset($CurrenciesListSorted[DEFAULT_CURRENCY_CODE]))
        	{
        		$default_i = DEFAULT_CURRENCY_CODE;
        	}
        }
        if($default_i === NULL)
        {
        	$incides = array_keys($CurrenciesListSorted);
        	$default_i = $incides[0];
        }
        $CurrenciesListSorted[$default_i]["dflt"] = DB_TRUE;

        reset($CurrenciesListSorted);

        $items = "";
        foreach ($CurrenciesListSorted as $currency)
        {
            $this->_Template_Contents = array("CurrencyId" => $currency["id"]
                                           ,"CurrencyCode" => $currency["code"]
                                           ,"CurrencyCodeStyle" => ($currency["active"] == DB_TRUE)? "bold":"normal; color: #808080"
                                           ,"CurrencyStyle" => ($currency["active"] == DB_TRUE)? "#FFFFFF":"#f5f5f5; border-color: #f5f5f5"
                                           ,"TDStyle" => ($currency["active"] == DB_TRUE)? "#eef2f8":"#f5f5f5"
                                           ,"CurrencyName" => $currency["name"]//prepareHTMLDisplay($country["name"]) -             ,  . .               getCountriesFullList
                                           ,"CurrencyEnabled" => ($currency["active"] == DB_TRUE)? "":" DISABLED "
                                           ,"CurrencyActive" => ($currency["active"] == DB_TRUE)? " CHECKED ":""
                                           ,"CurrencyDefault" => ($currency["dflt"] == DB_TRUE)? " CHECKED ":""
                                            );
            $application->registerAttributes($this->_Template_Contents);
            $items .= modApiFunc('TmplFiller', 'fill', "localization/accepted_currencies/", "item.tpl.html", array());
        }

        return $items;
    }

    /**
     * Outputs the view.
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild");
            return;
        }

        $request = new Request();
        $request->setView('AcceptedCurrencies');
        $request->setAction("UpdateAcceptedCurrencies");
        $form_action = $request->getURL();

        $template_contents = array(
                                    "UpdateAcceptedCurrenciesForm"   => $HtmlForm1->genForm($form_action, "POST", "UpdateAcceptedCurrenciesForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "localization/accepted_currencies/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Items':
                $value = $this->outputCurrencies();
                break;
            case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("AcceptedCurrencies", "AcceptedCurrenciesList");
                break;
            # overload the PaginatorRows tag
            case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("AcceptedCurrencies", 'AcceptedCurrenciesList', 'MNG_CNTR_OBJECT_NAME');
                break;
            default:
                if (array_key_exists($tag, $this->_Template_Contents))
                {
                    $value = $this->_Template_Contents[$tag];
                }
                break;
        }
        return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>