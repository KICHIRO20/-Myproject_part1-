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

class Checkout_PersonInfo_OutputCZ_Base {

    function Checkout_PersonInfo_OutputCZ_Base()
    {
        global $application;

        $this->HTML_LOCAL_TAGS_PREFIX = "Local_";
        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("Checkout" . $this->HTML_TAGS_PREFIX . "Output"))
        {
            $this->NoView = true;
        }
    }

    /**
     * Outputs the view.
     *
     * @ $request->setView  ( '' ) -define the view name
     */
    function output()
    {
        global $application;

        if(!isset($this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID))
        {
            $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID = $this->HTML_TAGS_PREFIX;
        }
        //          -                             PersonInfoType,                   ,
        //                                    .
        $person_info_types = modApiFunc("Checkout", "getPersonInfoTypeList");
        foreach($person_info_types as $id => $info)
        {
            if($info['tag'] == $this->CHECKOUT_PREREQUISITE_NAME &&
               $info['active'] == DB_FALSE)
            {
                return "";
            }
        }

        //Read the decrypted credit card data from the session
        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);

        if($prerequisiteValidationResults["isMet"] == false)
        {
            return "";
        }

        // build direct tag list with values.
        // they are may be used in the container template.
        // Example: Local_Firstname();
        $this->direct_tags = modApiFunc("Checkout", "getPersonInfoTagList", $this->CHECKOUT_PREREQUISITE_NAME);

/*        if ($this->NoView)
        {
            $application->outputTagErrors(true, "CartContent", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "CartContent", "Warnings");
        }*/
        $AttributesArray = array($this->HTML_TAGS_PREFIX . 'OutputAllAttributes' => "",
                                 $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'OutputAllAttributes' => "",
                                 $this->HTML_TAGS_PREFIX . 'OutputAllAttributesRows' => "",
                                 $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'OutputAllAttributesRows' => ""
        );

        foreach($prerequisiteValidationResults['validatedData'] as $data_id => $data)
        {
            if($data['view_tag'] == "Statemenu")
            {
                $AttributesArray[$this->HTML_TAGS_PREFIX . "State"] = '';
            }
            else
            if($data['view_tag'] == "Statetext")
            {
            }
            else
            {
                $AttributesArray[$this->HTML_TAGS_PREFIX . $data['view_tag'] ] = '';
            }
        }

        $application->registerAttributes($AttributesArray);
        $application->registerAttributes($this->direct_tags);

        loadCoreFile('UUIDUtils.php');
        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate(UUIDUtils::cut_uuid_suffix($this->BLOCK_TAG_NAME, "js")); //__CLASS__ is lowercase class name, get_class() also returns class name in lowercase.
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("OutputContainer");
        return $retval;
    }

    function genCurrentAttributeHTMLData($attrName, $attrInfo)
    {
        global $application;
        //Get one more copy of validation results of all prerequisites, to process
        // the countries (Country) and state (State) correctly.
        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);

        $template_name = "";
        switch($attrInfo['input_type_id'])
        {
            case '1'://input. (Considered: type = text)
            {
                $application->registerAttributes(array($this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldValue' => ""));
                $this->_Current_Attribute = array('FieldLabel' => $attrInfo['attribute_visible_name'],
                                                  //Obfuscate credit card info etc.
                                                  'FieldValue' => htmlspecialchars(modApiFunc
                                                                 (
                                                                     "Checkout",
                                                                     "get_public_view_of_secured_data",
                                                                     $attrInfo['value'],
                                                                     $attrInfo['id']
                                                                 ))
                                                 );
                break;
            }
            case '2'://TEXTAREA
            {
                $application->registerAttributes(array($this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldValue' => ""));

                $this->_Current_Attribute = array('FieldLabel' => $attrInfo['attribute_visible_name'],
                                                  'FieldValue' => nl2br(htmlspecialchars($attrInfo['value'])));
                break;
            }
            case '3'://input. (type = select)
            {
                $application->registerAttributes(array($this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldValue' => ""));

                $Value = "";

                switch($attrInfo['view_tag'])
                {
                    case "Country":
                    {
                        $country_id = $attrInfo['value'];
                        //If the value is not specified, output it as at the first selection.
                        //If the value is specified, change the control elements:
                        //  extract selected before item from the dropped lists.
                        $Value = modApiFunc("Location", "getCountry", $country_id);
                        break;
                    }
                    case "Statemenu": //"State"
                    {
                        $state_id = $attrInfo['value'];
                        //$state_id            "" (NULL) -           ,
                        //If the value is not specified, output it as at the first selection.
                        //If the value is specified, change all control elements:
                        //  fill out text fields, if necessary.
                        //$state_id can be "" (NULL), i.e. the state hasn't been selected yet
                        if(!empty($state_id))
                        {
                            $Value = modApiFunc("Location", "getState", $state_id);
                        }
                        else
                        {
                            $Value = $prerequisiteValidationResults['validatedData']['Statetext']['value'];
                        }
                        break;
                    }
                    case "CreditCardType":
                    {
                        $id = $attrInfo['value'];
                        $names = modApiFunc("Configuration", "getCreditCardSettings");
                        $Value = $names[$id]["name"];
                        break;
                    }
                    case "ExpirationMonth":
                    {
                        $id = $attrInfo['value'];
                        $names = modApiFunc("Checkout", "getMonthNames");
                        $Value = $names[$id];
                        break;
                    }
                    case "ValidFromMonth":
                    {
                        $id = $attrInfo['value'];
                        $names = modApiFunc("Checkout", "getMonthNames");
                        $Value = $names[$id];
                        break;
                    }
                    case "ExpirationYear":
                    {
                        $id = $attrInfo['value'];
                        $names = modApiFunc("Checkout", "getCCYearNames");
                        $Value = $names[$id];
                        break;
                    }
                    case "ValidFromYear":
                    {
                        $id = $attrInfo['value'];
                        $names = modApiFunc("Checkout", "getCCValidFromYearNames");
                        $Value = $names[$id];
                        break;
                    }
                    default:
                    {
                        $err_params = array(
                                            "CODE"    => "CHECKOUT_008"
                                           );
                        _fatal($err_params, $attrInfo['view_tag']);
                        break;
                    }
                }

                $this->_Current_Attribute = array('FieldLabel' => $attrInfo['attribute_visible_name'],
                                                  'FieldValue' => $Value
                                                  );
                break;
            }
            default:
            {
                if (preg_match("/".CUSTOM_FIELD_TAG_NAME_PREFIX."/i", $attrInfo['view_tag']) != 0)
                {
                    // this is a custom field with select or checkbox type
                    $r = execQuery('SELECT_INPUT_TYPE_DATA', array('it_id'=>$attrInfo['input_type_id']));
                    if (count($r) != 0 && ($r[0]['input_type_name'] == "SELECT" || $r[0]['input_type_name'] == "CHECKBOX"))
                    {
                        $application->registerAttributes(array($this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldValue' => ""));

                        $this->_Current_Attribute = array('FieldLabel' => $attrInfo['attribute_visible_name'],
                                                  'FieldValue' => $attrInfo['value']
                                                  );
                    }

                    return $this->_Current_Attribute;
                }

                $err_params = array(
                                    "CODE"    => "CHECKOUT_007"
                                   );
                _fatal($err_params, $attrInfo['input_type_id']);
                break;
            }
        }
        return $this->_Current_Attribute;
    }

    /**
     * Defines the attribute name by the tag name, which is taken from the Html
     * templates. They are similar in most cases, but can be different in
     * underline, the letter case etc. The html tag format is fixed in AVACTIS,
     * the format of the attribute names corresponds to the conditions, taken for
     * the field names in the AVACTIS database.
     * For example:
     * "Firstname" -> "first_name"
     *
     */
    function getAttrInfoByTagName($tag)
    {
        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);
        foreach($prerequisiteValidationResults['validatedData'] as $attrName => $attrInfo)
        {
            if($tag == $attrName)
                return $attrInfo;
            if("Statemenu" == $attrName &&
               $tag == "State")
                return $attrInfo;
        }

        $err_params = array(
                            "CODE"    => "CHECKOUT_009"
                           );
        _fatal($err_params, $tag);
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            //Custom templates. As for now (Mar 2006 ) they are not used.
            case $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'OutputAllAttributes':
            case $this->HTML_TAGS_PREFIX . 'OutputAllAttributes':
            {
                $value = $this->templateFiller->fill("ContainerOutputAllAttributes");
            }

            case $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'OutputAllAttributesRows':
            case $this->HTML_TAGS_PREFIX . 'OutputAllAttributesRows':
            {
                $value = "";
                $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);

                // credit card info output: based on specific attributes settings
                if ($this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID == "CreditCardInfo")
                {
                    $cc_types = modApiFunc("Configuration", "getCreditCardSettings", true);
                    $card = $prerequisiteValidationResults['validatedData']['CreditCardType']['value'];
                    $cc_attrs = modApiFunc("Configuration", "getAttributesForCardType", $cc_types[$card]['id']);
                }

                foreach($prerequisiteValidationResults['validatedData'] as $attrName => $attrInfo)
                {
                    if ($this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID == "CreditCardInfo")
                    {
                        $attr_id = $attrInfo['id'];
                        if ($cc_attrs[$attr_id]['visible'] == 0)
                        {
                            unset($prerequisiteValidationResults['validatedData'][$attrInfo['view_tag']]);
                            continue;
                        }
                    }

                    //Output the state if "Statemenu" appears. "Statetext" is skipped.
                    if($attrName != "Statetext")
                    {
                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                        $tempateName = "OutputItem";// $this->getCurrentAttributeTemplateName($attrInfo);
                        $value .= $this->templateFiller->fill($tempateName);
                    }
                }
                break;
            }

            default:
            {
                if (isset($this->direct_tags[$tag]))
                {
                    $value = prepareHTMLDisplay($this->direct_tags[$tag]);
                }
                //Define the attribute by the tag name. Get attribute info
                // from the session: the value, the value type, validation
                // errors, by the attribute name.
                //case 'CustomerInfoEmail' -> email

                //It also can be a tag, consisting of the templates
                // <input type="text"> or
                // <select>
                //See else-branch
                elseif(substr($tag, 0, strlen($this->HTML_TAGS_PREFIX)) == $this->HTML_TAGS_PREFIX)
                {
                    //:give examples
                    $tag = substr($tag, strlen($this->HTML_TAGS_PREFIX));
                    {
                        //"Small tag" ("value")
                        $attrInfo = $this->getAttrInfoByTagName($tag);
                        $attrName = $tag;
                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
//                        if (empty($this->_Current_Attribute))
//                            $value = "";
//                        else
                            $value = prepareHTMLDisplay($this->_Current_Attribute["Value"]);
                    }
                }
                else
                {
                    //Small tag
                    // : give examples
                    $tag = substr($tag, strlen($this->HTML_LOCAL_TAGS_PREFIX));
        	        if (empty($this->_Current_Attribute))
        	        {
        	           $value = '';
        	        }
        	        else if(array_key_exists($tag, $this->_Current_Attribute))
        	        {
      	               $value = prepareHTMLDisplay($this->_Current_Attribute[$tag]);
        	        }
        	        else
        	        {
                        $err_params = array(
                                            "CODE"    => "CHECKOUT_010"
                                           );
                        _fatal($err_params, $tag);
        	        }
        	        break;
                }
            }
        }
        return $value;
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Reference to the TemplateFiller object.
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current selected template.
     * @var array
     */
    var $template;

    /**
     * The current person attribute.
     * @var array
     */
    var $_Current_Attribute;

    /**
     * The checkout prerequisite name appropriate to the current Person Info
     * (e.g. CustomerInfo).
     */
    var $CHECKOUT_PREREQUISITE_NAME;//e.g. "customerInfo";

    /**
     * The store block name appropriate to the given checkout prerequisite.
     */
    var $CHECKOUT_STORE_BLOCK_NAME;//e.g. "customer-info-output";

    /**
     * A html tag prefix appropriate to the attributes of the given Person Info.
     */
    var $HTML_TAGS_PREFIX;//e.g. "CustomerInfo";

    /**
     * A html tag prefix, added to the name of all Person Info local attributes.
     */
    var $HTML_LOCAL_TAGS_PREFIX;// = "Local_";

    /**
     * The block tag name. It matches the class name.
     * (e.g. CheckoutCustomerInfoInput)
     */
    var $BLOCK_TAG_NAME;


    var $direct_tags;
}
?>