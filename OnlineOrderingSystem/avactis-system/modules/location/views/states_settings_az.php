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
 * Configuration Module, StatesSettings View.
 *
 * @package Location
 * @author Alexander Girin
 */
class StatesSettings
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
    function StatesSettings()
    {
        $paginator = modAPIFunc('paginator', 'setCurrentPaginatorName', "States");
        $this->countries = modApiFunc("Location", "getCountries");
        foreach ($this->countries as $id => $name)
        {
            if (modApiFunc("Location", "getCountStatesInCountry", $id) == 0)
            {
                unset($this->countries[$id]);
            }
        }
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
        $this->POST = $SessionPost;
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
        $default_country = modApiFunc("Location", "getDefaultCountryId");
        if (isset($this->countries[$default_country]))
        {
            $this->POST["c_id"] = $default_country;
        }
        else
        {
        $c_id_array = array_keys($this->countries);
        if (isset($c_id_array[0]))
        {
            $this->POST["c_id"] = $c_id_array[0];
        }
            else
            {
                $this->POST["c_id"] = 0;
            }
        }
    }

    /**
     * @return String Return html code for hidden form fields representing
     * @var $this->ViewState array.
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
     * Outputs the list of states.
     *
     * @
     * @param
     * @return
     */
    function outputStates($c_id)
    {
        global $application;

        $states = modApiFunc("Location", "getStatesFullList", $c_id, false);

        $items = "";
        if (sizeof($states)!=0)
        {
            foreach ($states as $state)
            {
                $this->_Template_Contents = array("StateId" => $state["id"]
                                               ,"StateCode" => $state["code"]
                                               ,"StateCodeStyle" => ($state["active"] == "true")? "bold":"normal; color: #808080"
                                               ,"StateStyle" => ($state["active"] == "true")? "#FFFFFF":"#f5f5f5; border-color: #f5f5f5"
                                               ,"TDStyle" => ($state["active"] == "true")? "#eef2f8":"#f5f5f5"
                                               ,"StateName" => $state["name"]//prepareHTMLDisplay($state["name"]) -             ,  . .               getStatesFullList
                                               ,"StateEnabled" => ($state["active"] == "true")? "":" DISABLED "
                                               ,"StateActive" => ($state["active"] == "true")? " CHECKED ":""
                                               ,"StateDefault" => ($state["dflt"] == "true")? " CHECKED ":""
                                                );
                $application->registerAttributes($this->_Template_Contents);
                $items .= modApiFunc('TmplFiller', 'fill', "location/states_settings/", "item.tpl.html", array());
            }
        }
        else
        {
            $items .= modApiFunc('TmplFiller', 'fill', "location/states_settings/", "item_empty.tpl.html", array());
        }

        return $items;
    }

    /**
     * Outputs the list of available coutries, that have states.
     *
     * @author Alexandr Girin
     * @
     * @param
     * @return
     */
    function outputCountries()
    {
        $retval = "";
        foreach ($this->countries as $id => $name)
        {
            $retval.= "<option value=\"".$id."\"";
            if ($id == $this->POST["c_id"])
            {
                $retval.= " SELECTED";
            }
            $retval.= ">";
            $retval.= $name;
            $retval.= "</option>";
        }
        return $retval;
    }

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
            return $this->mTmplFiller->fill("location/states_settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    /**
     * Outputs the list of states.
     *
     * @
     * @param
     * @return
     */
    function outputList()
    {
        if (sizeof($this->countries)!=0)
        {
            global $application;
            loadCoreFile('html_form.php');
            $HtmlForm1 = new HtmlForm();

            $request = new Request();
            $request->setView('StatesList');
            $request->setAction("UpdateStates");
            $form_action = $request->getURL();
            $template_contents = array(
                                        "UpdateStatesForm"      => $HtmlForm1->genForm($form_action, "POST", "UpdateStatesForm")
                                       ,"HiddenArrayViewState"  => $this->outputViewState()
                                       ,"CountriesList"         => $this->outputCountries()
                                       ,'ResultMessage'			=> $this->outputResultMessage()
                                       ,"Items"                 => $this->outputStates($this->POST["c_id"])
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $retval = modApiFunc('TmplFiller', 'fill', "location/states_settings/","list.tpl.html", array());
        }
        else
        {
            $retval = modApiFunc('TmplFiller', 'fill', "location/states_settings/","list_empty.tpl.html", array());
        }
        return $retval;
    }

    /**
     * Outputs the view.
     */
    function output()
    {
        global $application;

        $template_contents = array(
                                   "ListItems"             => $this->outputList()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "location/states_settings/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
/*
            case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("States", "StatesList");
                break;
            # overload the PaginatorRows tag
            case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("States", "StatesList", "MNG_STATE_OBJECT_NAME");
                break;
*/
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