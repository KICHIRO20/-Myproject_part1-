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
 * Location Module, CountriesSettings View.
 *
 * @package Location
 * @author Alexey Florinsky
 */
class CountriesSettings
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
    function CountriesSettings()
    {
        $paginator = modAPIFunc('paginator', 'setCurrentPaginatorName', "Countries");
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
     * Outputs the Result Message.
     *
     */
	function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("location/countries_settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }
    /**
     * Outputs the list of countries.
     *
     * @
     * @param
     * @return
     */
    function outputCountries()
    {
        global $application;

        $countries = modApiFunc("Location", "getCountriesFullList");

        $items = "";
        foreach ($countries as $country)
        {
            $this->_Template_Contents = array("CountryId" => $country["id"]
                                           ,"CountryCode" => $country["code"]
                                           ,"CountryCodeStyle" => ($country["active"] == "true")? "bold":"normal; color: #808080"
                                           ,"CountryStyle" => ($country["active"] == "true")? "#FFFFFF":"#f5f5f5; border-color: #f5f5f5"
                                           ,"TDStyle" => ($country["active"] == "true")? "#eef2f8":"#f5f5f5"
                                           ,"CountryName" => $country["name"]//prepareHTMLDisplay($country["name"]) -             ,  . .               getCountriesFullList
                                           ,"CountryEnabled" => ($country["active"] == "true")? "":" DISABLED "
                                           ,"CountryActive" => ($country["active"] == "true")? " CHECKED ":""
                                           ,"CountryDefault" => ($country["dflt"] == "true")? " CHECKED ":""
                                            );
            $application->registerAttributes($this->_Template_Contents);
            $items .= modApiFunc('TmplFiller', 'fill', "location/countries_settings/", "item.tpl.html", array());
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


        $request = new Request();
        $request->setView('CountriesList');
        $request->setAction("UpdateCountries");
        $form_action = $request->getURL();

        $template_contents = array(
                                    "UpdateCountriesForm"   => $HtmlForm1->genForm($form_action, "POST", "UpdateCountriesForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()
                                   ,'ResultMessage'			=> $this->outputResultMessage()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "location/countries_settings/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Items':
                $value = $this->outputCountries();
                break;
            case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("Countries", "CountriesList");
                break;
            # overload the PaginatorRows tag
            case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("Countries", 'CountriesList', 'MNG_CNTR_OBJECT_NAME');
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