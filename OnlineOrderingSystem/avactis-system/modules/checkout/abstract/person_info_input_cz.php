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

class Checkout_PersonInfo_InputCZ_Base {

    function Checkout_PersonInfo_InputCZ_Base()
    {
        $this->HTML_LOCAL_TAGS_PREFIX = "Local_";
        //: hard coded values. Move to database.
        $this->TEXT_AREA_ROWS = 10;
        $this->TEXT_AREA_COLS = 27;

        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("Checkout" . $this->HTML_TAGS_PREFIX . "Input"))
        {
            $this->NoView = true;
        }

        $this->MessageResources = &$application->getInstance('MessageResources',"messages");
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

/*        if ($this->NoView)
        {
            $application->outputTagErrors(true, "CartContent", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "CartContent", "Warnings");
        }*/
//block items

        $AttributesArray = array('CreditCardInfoJSAttrRules' => '',
                                 'SubmitedCheckoutStoreBlocksListItemName' => "",
                                 $this->HTML_TAGS_PREFIX . 'InputFormFieldSize' => "",
                                 $this->HTML_TAGS_PREFIX . 'InputFormFieldMaxlength' => "",
//                                 $this->HTML_TAGS_PREFIX . 'InputAllAttributes' => "",
                                 $this->HTML_TAGS_PREFIX . 'InputAllAttributesRows' => "",
                                 $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'InputAllAttributesRows' => "",
                                 $this->HTML_TAGS_PREFIX . 'JavascriptFunctions' => "",
                                 $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'JavascriptFunctions' => "");

        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);

        //Custom templates. As for now (2006 mar) they are not used.
        foreach($prerequisiteValidationResults['validatedData'] as $data)
        {
//            $AttributesArray[$this->HTML_TAGS_PREFIX . $data['view_tag']  . "LabelCssClass"] = '';
            $AttributesArray[$this->HTML_TAGS_PREFIX . $data['view_tag']  . "FieldLabel"] = '';
            $AttributesArray[$this->HTML_TAGS_PREFIX . $data['view_tag']           ] = '';
//            $AttributesArray[$this->HTML_TAGS_PREFIX . $data['view_tag']  . "ErrorFlagClass"] = '';
            $AttributesArray[$this->HTML_TAGS_PREFIX . $data['view_tag']  . "InputFormFieldSize"] = '';
            $AttributesArray[$this->HTML_TAGS_PREFIX . $data['view_tag']  . "InputFormFieldMaxlength"] = '';
        }

        $application->registerAttributes($AttributesArray);

        $this->templateFiller = &$application->getInstance('TemplateFiller');


        loadCoreFile('UUIDUtils.php');
        $this->template = $application->getBlockTemplate(UUIDUtils::cut_uuid_suffix($this->BLOCK_TAG_NAME, "js")); //__CLASS__ is lowercase class name, get_class() also returns class name in lowercase.
        $this->templateFiller->setTemplate($this->template);

        //"Section" config.ini mechanism allowed to use two different templates with the same
        //  container name simultaneously. E.g. "Custom PersonInfo" and "Automatic AllAttributes
        //  PersonInfo" templates/views.
        //As for now (2006 mar) - there is no more even "Container" template : output is hardcoded
        //  to wrap and return single tag:
////        $retval = $this->templateFiller->fill("Container");

/*        $ResultTableRows = $this->getTag($this->HTML_TAGS_PREFIX . 'InputAllAttributesRows');
        $ResultJavascriptFunctions = $this->getTag($this->HTML_TAGS_PREFIX . 'JavascriptFunctions');
        //The name of the outputted data block is passed as a hidden field,
        // to know what data should have come to Action, and how to check it.
        $HiddenField = "<input type=\"hidden\" name=\"". $this->getTag("SubmitedCheckoutStoreBlocksListItemName") . "\">";

///        $retval =
              $ResultTableRows . "\n"
//            .'</table>';
//: can a javascript code be added the same way? For example, if templates
// are table strings, the whole template is a few concatenated table strings,
//  and appended to the end a javascript code, then wouldn't it result in
// changing the table structure?
            . $ResultJavascriptFunctions
            . $HiddenField;
*/
        $retval = $this->templateFiller->fill("InputContainer");
        return $retval;
    }

    function getCurrentAttributeTemplateName($attrInfo, $bRequired, $bInputError)
    {
        $template_name = "";
        switch($attrInfo['input_type_id'])
        {
            case '1'://input. (Considered: type = text)
            {
                $template_name = "InputText";
                if ($bRequired)
                    $template_name .= "Required";
                if ($bInputError)
                    $template_name .= "Error";
                break;
            }

            case '2'://TEXTAREA
            {
                $template_name = "Textarea";
                if ($bRequired)
                    $template_name .= "Required";
                if ($bInputError)
                    $template_name .= "Error";
                break;
            }

            case '3'://input. (type = select)
            {
                $template_name = "InputSelect";
                if ($bRequired)
                    $template_name .= "Required";
                if ($bInputError)
                    $template_name .= "Error";
                break;
            }
            default:
            {
                if (preg_match("/".CUSTOM_FIELD_TAG_NAME_PREFIX."/i", $attrInfo['view_tag']) != 0)
                {
                    // this is a custom field with select or checkbox type
                    $r = execQuery('SELECT_INPUT_TYPE_DATA', array('it_id'=>$attrInfo['input_type_id']));
                    if (count($r) != 0 && $r[0]['input_type_name'] == "SELECT")
                    {
                        $template_name = "InputSelect";
                        if ($bRequired)
                            $template_name .= "Required";
                        if ($bInputError)
                            $template_name .= "Error";
                    }
                    else if (count($r) != 0 && $r[0]['input_type_name'] == "CHECKBOX")
                    {
                        //: Not Yet Implemented
                    }

                    return $template_name;
                }

                $err_params = array(
                                    "CODE"    => "CHECKOUT_007"
                                   );
                _fatal($err_params, $attrInfo['input_type_id']);
                break;
            }
        }
        return $template_name;
    }

    function genCurrentAttributeHTMLData($attrName, $attrInfo)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"messages");

        //Get one more copy of prerequisite validation results, to process
        // the countries (Country) and state (State) correctly.
        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);

        $template_name = "";
//        $Value = "";
        switch($attrInfo['input_type_id'])
        {
            case '1'://input. (Considered: type = text)
            {
                $application->registerAttributes(array('CreditCardInfoJSAttrRules' => '',
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'trID' => $attrInfo['id'],
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldID' => "empty_id_".rand(),//'DivID' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "",//'Label' => "",
                                                       #$this->HTML_LOCAL_TAGS_PREFIX . 'FieldClassName' => "",//'Label' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldError' => "",//'Label' => "",
//                                                       'LabelCssClass' => "",
//                                                       'CssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldName' => "", //'Name' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldValue' => "", //'Value' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldSize' => "", //'Size' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldMaxlength' => ""));//'Maxlength' => ""));

                //DIV id is used to process states: hide the field input type=text,
                // if there is a state list in the DB.
                $DivID = $attrInfo['view_tag'] == "Statetext" ? $this->CHECKOUT_STORE_BLOCK_NAME . "_state_text_div" : "empty_id_".rand();

                $this->_Current_Attribute = array('attr_id' => $attrInfo['id'],
                								  'FormFieldID' => $DivID, //'DivID' => $DivID,
//: Note that there is special templates for the required fields.
                                                  'FieldLabel' => /* 'Label' => */$attrInfo['attribute_visible_name'], //.
                                                  #'FieldClassName' => /* 'Label' => */$attrInfo['attribute_visible_name'], //.
                                                  'FieldError' => /*'Label' => */ $attrInfo['error_code_short'] != '' ? ($this->MessageResources->getMessage($attrInfo['error_code_short'], empty($attrInfo['error_message_parameters']) ? array() : $attrInfo['error_message_parameters'])) : '',

//                                                             ($attrInfo['attribute_required'] == true ? $obj->getMessage('CHECKOUT_REQUIRED_MARK') : ""),
//                                                  'LabelCssClass' => $attrInfo['attribute_required'] == true ? 'required' : '',
//                                                  'CssClass' => $attrInfo['error_code'] == '' ? '' : 'error',
                                                  'FormFieldName'  => /* 'Name'  => */ $this->CHECKOUT_PREREQUISITE_NAME . "[" . $attrName . "]",
                                                  'FormFieldValue' => /* 'Value' => */ prepareHTMLDisplay($attrInfo['value']),
//                                                                 //Obfuscate credit card info etc.
//                                                                 modApiFunc
//                                                                 (
//                                                                     "Checkout",
//                                                                     "get_public_view_of_secured_data",
//                                                                     $attrInfo['value'],
//                                                                     $attrInfo['id']
//                                                                 ),
                                                  'FormFieldSize'  => /* 'Size' => */ $attrInfo['size'],
//                                                  'Maxlength' => $attrInfo['max']
                                                  'FormFieldMaxlength' => $attrInfo['max']
                                                  );
                break;
            }
            case '2'://TEXTAREA
            {
                $application->registerAttributes(array('CreditCardInfoJSAttrRules' => '',
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'trID' => $attrInfo['id'],
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldID' => "empty_id_".rand(),//'DivID' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "",//'Label' => "",
                                                       #$this->HTML_LOCAL_TAGS_PREFIX . 'FieldClassName' => "",//'Label' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldError' => "",//'Label' => "",
//                                                       'LabelCssClass' => "",
//                                                       'CssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldName' => "", //'Name' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldValue' => "", //'Value' => "",
                                                       //$this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldSize' => "", //'Size' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldCols' => "", //'Size' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldRows' => "")); //'Size' => "",
                                                       //$this->HTML_LOCAL_TAGS_PREFIX . 'FormFieldMaxlength' => ""));//'Maxlength' => ""));
                //DIV id is used to process states: hide the field input type=text,
                // if there is a state list in the DB.
                $DivID = "empty_id_".rand();


                //is custom field
                $FormFieldCols = $this->TEXT_AREA_COLS;
                $FormFieldRows = $this->TEXT_AREA_ROWS;
                if (preg_match("/".CUSTOM_FIELD_TAG_NAME_PREFIX."/i", $attrInfo['view_tag']) != 0)
                {
                    $ad = modApiFunc("Checkout",'getPersonCustomAttributeData',$attrInfo['id']);
                    $params = unserialize($ad[0]['field_params']);
                    $FormFieldCols = $params['cols'];
                    $FormFieldRows = $params['rows'];
                    #$FormFieldClassName = $params['class'];
                }

                $this->_Current_Attribute = array('attr_id' => $attrInfo['id'],
                								  'FormFieldID' => $DivID, //'DivID' => $DivID,
//: Note that there is special templates for the required fields.
                                                  'FieldLabel' => /* 'Label' => */$attrInfo['attribute_visible_name'], //.
                                                  #'FieldClassName' => /* 'Label' => */$attrInfo['attribute_visible_name'], //.
                                                  'FieldError' => /*'Label' => */ $attrInfo['error_code_short'] != '' ? ($this->MessageResources->getMessage($attrInfo['error_code_short'], empty($attrInfo['error_message_parameters']) ? array() : $attrInfo['error_message_parameters'])) : '',

//                                                             ($attrInfo['attribute_required'] == true ? $obj->getMessage('CHECKOUT_REQUIRED_MARK') : ""),
//                                                  'LabelCssClass' => $attrInfo['attribute_required'] == true ? 'required' : '',
//                                                  'CssClass' => $attrInfo['error_code'] == '' ? '' : 'error',
                                                  'FormFieldName'  => /* 'Name'  => */ $this->CHECKOUT_PREREQUISITE_NAME . "[" . $attrName . "]",
                                                  'FormFieldValue' => /* 'Value' => */ $attrInfo['value'],
//                                                  'FormFieldSize'  => /* 'Size' => */ $attrInfo['size'],
//                                                  'Maxlength' => $attrInfo['max']
                                                  'FormFieldCols' => $FormFieldCols,
                                                  'FormFieldRows' => $FormFieldRows
                                                  //'FormFieldMaxlength' => $attrInfo['max']
                                                  );
                break;
            }
            case '3'://input. (type = select)
            {
/*                die(print_r(array($this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "", // 'Label' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldError' => "", // 'Label' => "",
//                                                       'LabelCssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectFieldID' => "", //'SelectID' => "",
//                                                       'CssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectFieldName' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectOnchange' => "", //'JavascriptOnchange' => "",

//                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'JavascriptFunctions' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectOptions' => ""),true));
*/

                $application->registerAttributes(array('CreditCardInfoJSAttrRules' => '',
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'trID' => $attrInfo['id'],
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "", // 'Label' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldError' => "", // 'Label' => "",
                                                       #$this->HTML_LOCAL_TAGS_PREFIX . 'FieldClassName' => "", // 'Label' => "",
//                                                       'LabelCssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectFieldID' => "", //'SelectID' => "",
//                                                       'CssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectFieldName' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectOnchange' => "", //'JavascriptOnchange' => "",

//                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'JavascriptFunctions' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectOptions' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . "CreditCardTypeMark" => ''));

                $JavascriptOnchange = "";
                $JavascriptFunctions = "";
                $Value = "";
                $mark = "";

                switch($attrInfo['view_tag'])
                {
                    case "Country":
                    {
                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . "_country_select";
                        $country_id = $attrInfo['value'];
                        //If the value is not specified, output it as at the first selection.
                        //If the value is specified, change the control elements:
                        //  extract selected before item from the dropped lists.
                        $JavascriptOnchange = "try {refreshStatesList('" .$this->CHECKOUT_STORE_BLOCK_NAME. "_country_select', '" .$this->CHECKOUT_STORE_BLOCK_NAME. "_state_menu_select', 'tr_" .$this->CHECKOUT_STORE_BLOCK_NAME. "_state_text_div');} catch(ex) {};";
                        $JavascriptFunctions = "";
                        $Value = modApiFunc("Checkout", "genCountrySelectList", $country_id);
                        break;
                    }
                    case "Statemenu":
                    {
                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . "_state_menu_select";
                        $state_id = $attrInfo['value'];
                        //If the value is not specified, output it as at the first selection.
                        //If the value is specified, change all control elements:
                        //  fill out text fields, if necessary.
                        //$state_id can be "" (NULL), i.e. the state hasn't been selected yet
                        $JavascriptOnchange = "";
                        //IMPORTANT: Javascript for synchronizing Country/State lists:
                        $JavascriptFunctions = modApiFunc("Location", "getJavaScriptCountriesStatesArrays") .
                                               modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists");
                        $country_id = $prerequisiteValidationResults['validatedData']['Country']['value'];
                        if(empty($country_id))
                        {
                            //: move the message to the resource.
                             /*_fatal(*/ //echo "The country is not specified yet. It's impossible to check if the state has been selected valid." /*)*/;
                            $country_id = 223; // USA
                            //The list will be updated automatically by the script body.onload()
                        }

                        $Value = modApiFunc("Checkout", "genStateSelectList", $state_id, $country_id);
                        break;
                    }
                    case "CreditCardType":
                    {
                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . "_credit_card_type_select";
                        $cc_type_id = $attrInfo['value'];
                        $JavascriptOnchange = "onCreditCardTypeChange(this.value, '{$this->CHECKOUT_PREREQUISITE_NAME}');";
                        $Value = modApiFunc("Checkout", "genCreditCardTypeSelectList", $cc_type_id);
                        $mark = $this->CHECKOUT_PREREQUISITE_NAME;
                        break;
                    }
                    case "ExpirationMonth":
                    {
                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . "_expiration_month_select";
                        $selected_id = $attrInfo['value'];
                        $Value = modApiFunc("Checkout", "genMonthSelectList", $selected_id);
                        break;
                    }
                    case "ExpirationYear":
                    {
                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . "_expiration_year_select";
                        $selected_id = $attrInfo['value'];
                        $Value = modApiFunc("Checkout", "genYearSelectList", $selected_id);
                        break;

                    }
                    case "ValidFromMonth":
                    {
                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . "_valid_from_month_select";
                        $selected_id = $attrInfo['value'];
                        $Value = modApiFunc("Checkout", "genMonthSelectList", $selected_id);
                        break;
                    }
                    case "ValidFromYear":
                    {
                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . "_valid_from_year_select";
                        $selected_id = $attrInfo['value'];
                        $Value = modApiFunc("Checkout", "genValidFromYearSelectList", $selected_id);
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

                $this->_Current_Attribute = array('attr_id' => $attrInfo['id'],
                								  'FieldLabel' => /*'Label' => */ $attrInfo['attribute_visible_name'], // .
                                                  #'FieldClassName' => /*'Label' => */ $attrInfo['attribute_visible_name'], // .
                                                  'FieldError' => /*'Label' => */ $attrInfo['error_code_short'] != '' ? ($this->MessageResources->getMessage($attrInfo['error_code_short'], empty($attrInfo['error_message_parameters']) ? array() : $attrInfo['error_message_parameters'])) : '', // .
//                                                             ($attrInfo['attribute_required'] == true ? $obj->getMessage('CHECKOUT_REQUIRED_MARK') : ""),
//                                                 'LabelCssClass' => $attrInfo['attribute_required'] == true ? 'required' : '',
                                                  'FormSelectFieldID' => /*'SelectID' =>*/ $SelectID,
//                                                  'CssClass' => $attrInfo['error_code'] == '' ? '' : 'error',
                                                  'FormSelectFieldName'  => $this->CHECKOUT_PREREQUISITE_NAME . "[" . $attrName . "]",
                                                  'FormSelectOnchange' => /*'JavascriptOnchange' =>*/ $JavascriptOnchange,
                                                  'JavascriptFunctions' => $JavascriptFunctions,
                                                  'FormSelectOptions' => /*'Value' =>*/ $Value,
                                                  'CreditCardTypeMark' => $mark
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
                        $application->registerAttributes(array('CreditCardInfoJSAttrRules' => '',
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'trID' => $attrInfo['id'],
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldLabel' => "", // 'Label' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FieldError' => "", // 'Label' => "",
                                                       #$this->HTML_LOCAL_TAGS_PREFIX . 'FieldClassName' => "", // 'Label' => "",
//                                                       'LabelCssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectFieldID' => "", //'SelectID' => "",
//                                                       'CssClass' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectFieldName' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectOnchange' => "", //'JavascriptOnchange' => "",

//                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'JavascriptFunctions' => "",
                                                       $this->HTML_LOCAL_TAGS_PREFIX . 'FormSelectOptions' => "")); //'Value' => ""));



                        //: a point for optimization
                        foreach ($r as $i=>$v)
                        {
                            $values[] = modApiFunc('Catalog', 'getInputTypeActualValue', $v['input_type_value']);
                        }

                        $JavascriptOnchange = "";
                        $JavascriptFunctions = "";

                        $SelectID = $this->CHECKOUT_STORE_BLOCK_NAME . $attrInfo['view_tag'];
                        $selected_id = $attrInfo['value'];
                        $Value = modApiFunc('Checkout','genCustomFieldValues', $values, $selected_id);

                        $this->_Current_Attribute = array('attr_id' => $attrInfo['id'],
                								  'FieldLabel' => /*'Label' => */ $attrInfo['attribute_visible_name'], // .
                                                  'FieldError' => /*'Label' => */ $attrInfo['error_code_short'] != '' ? ($this->MessageResources->getMessage($attrInfo['error_code_short'], empty($attrInfo['error_message_parameters']) ? array() : $attrInfo['error_message_parameters'])) : '', // .
                                                  #'FieldClassName' => /*'Label' => */ $attrInfo['error_code_short'],
//                                                             ($attrInfo['attribute_required'] == true ? $obj->getMessage('CHECKOUT_REQUIRED_MARK') : ""),
//                                                 'LabelCssClass' => $attrInfo['attribute_required'] == true ? 'required' : '',
                                                  'FormSelectFieldID' => /*'SelectID' =>*/ $SelectID,
//                                                  'CssClass' => $attrInfo['error_code'] == '' ? '' : 'error',
                                                  'FormSelectFieldName'  => $this->CHECKOUT_PREREQUISITE_NAME . "[" . $attrName . "]",
                                                  'FormSelectOnchange' => /*'JavascriptOnchange' =>*/ $JavascriptOnchange,
                                                  'JavascriptFunctions' => $JavascriptFunctions,
                                                  'FormSelectOptions' => /*'Value' =>*/ $Value
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
     * $tag is for example "method_code" in the "paymentModule" prerequisite.
     */
    function getAttrInfoByTagName($tag)
    {
        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);
        foreach($prerequisiteValidationResults['validatedData'] as $attrName => $attrInfo)
        {
            if($tag == $attrName)
                return $attrInfo;
            //$template_name = "";
            //switch($info['input_type_id'])
        }

        $err_params = array(
                            "CODE"    => "CHECKOUT_009"
                           );
        _fatal($err_params, $tag);
    }

    /**
     * Returns Javascript code, which should be outputted while loading the html
     * page. For example, it corrects the fields "Select State", if the selected
     * "Country" has already been known. The concatenated instructions "OnChange"
     * are used for all atributes, formed the given Person Info.
     */
    //: note,that there can be expressions such as "this" etc. in OnChange,
    // which become meaningless in <body onload
    function getOnLoadJavascript()
    {
        $onChangeStatements = "";
        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);

        foreach($prerequisiteValidationResults['validatedData'] as $attrName => $attrInfo)
        {
            $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
            $onChangeStatements .= empty($this->_Current_Attribute["FormSelectOnchange"]) ? "" : $this->_Current_Attribute["FormSelectOnchange"];
//            $onChangeStatements .= empty($this->_Current_Attribute["JavascriptOnchange"]) ? "" : $this->_Current_Attribute["JavascriptOnchange"];
        }
        //Concatenate all OnChange instructions and add them to body.onload()
        $onLoadJavascript =
            "<script type=\"text/javascript\">" . "\n" .
            "<!--\n" . "\n" .
            "var onload_bak_" . $this->BLOCK_TAG_NAME . " = window.onload;" . "\n" .
            "window.onload = function()" . "\n" .
            "{" . "\n" .
            "    if(onload_bak_" . $this->BLOCK_TAG_NAME . "){onload_bak_" . $this->BLOCK_TAG_NAME . "();}" . "\n" .
            $onChangeStatements .
            "}" . "\n" .
            "//-->" . "\n" .
//"alert(window.onload);" . "\n" .
            "</script>" . "\n" .
            modApiFunc("Checkout", "getJavascriptCopyPersonInfo");
        return $onLoadJavascript;
    }

    function getTag($tag)
    {
        global $application;

        $value = null;
        switch ($tag)
        {
            //They are not used.
            case $this->HTML_TAGS_PREFIX . 'InputAllAttributes':
            {
                $value = $this->templateFiller->fill("ContainerInputAllAttributes");
            }

            case $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'InputAllAttributesRows':
            case $this->HTML_TAGS_PREFIX . 'InputAllAttributesRows':
            {
                $value = "";
                $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);


                foreach($prerequisiteValidationResults['validatedData'] as $attrName => $attrInfo)
                {
                    $hide_state = modApiFunc('Settings', 'getParamValue', 'CHECKOUT_PROCESS', 'DO_NOT_SHOW_EMPTY_STATE_FIELD');
                    if($attrInfo['view_tag'] == "Statetext" && $hide_state == 'YES')
                    {
                        continue;
                    };
                    $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                    //Check if attribute is "required" and/or marked as "input error".
                    $bRequired = $attrInfo['attribute_required'];
                    $bInputError = ($attrInfo['error_code_full'] != '') ||
                                   ($attrInfo['error_code_short'] != '');

                    $templateName = $this->getCurrentAttributeTemplateName($attrInfo, $bRequired, $bInputError);

                    $value .= $this->templateFiller->fill($templateName);
                }
//: needed to be removed to work with new (2006 mar) cz-checkout templates scheme.
//                $value .= $this->getOnLoadJavascript();
                break;
            }

/*            case $this->HTML_TAGS_PREFIX . 'FormFieldSize': //'InputSize':
                //automated "all atributes" block. Where to store InputSize?
        		$value = "20";
        		break;*/

/*            case $this->HTML_TAGS_PREFIX . 'FormFieldMaxlength': //'InputMaxLength':
                //automated "all atributes" block. Where to store InputMaxLength?
                $value = "10";
                break;*/

            case 'SubmitedCheckoutStoreBlocksListItemName':
                $value = "SubmitedCheckoutStoreBlocksList[" . $this->CHECKOUT_STORE_BLOCK_NAME . "]";
                break;

            case $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID . 'JavascriptFunctions':
            case $this->HTML_TAGS_PREFIX . 'JavascriptFunctions':
                //Go through and output all their Javascript functions.
                //Including OnChange.
                //: optimize
                $value = "";
                $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $this->CHECKOUT_PREREQUISITE_NAME);
                foreach($prerequisiteValidationResults['validatedData'] as $attrName => $attrInfo)
                {
                    $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                    $value .= empty($this->_Current_Attribute["JavascriptFunctions"]) ? "" : $this->_Current_Attribute["JavascriptFunctions"];
                }
                //Concatenate all OnChange instructions and add them to body.onload()
                $value .= $this->getOnLoadJavascript();
                break;

            case 'Local_FormName':
            {
                $value = modApiFunc("CheckoutView", "getTag", $tag);
                break;
            }

            case 'Local_trID':
            {
                $value = $this->_Current_Attribute['attr_id'].'_'.$this->CHECKOUT_PREREQUISITE_NAME;
                break;
            }

            case 'Local_CreditCardTypeMark':
            {
                if ($this->_Current_Attribute['CreditCardTypeMark'] != '')
                {
                    $value = "<script type=\"text/javascript\">marks['{$this->_Current_Attribute['CreditCardTypeMark']}'] = '{$this->_Current_Attribute['CreditCardTypeMark']}';</script>";
                }
                break;
            }

            default:
            {
                //Define the attribute by the tag name. Get attribute info
                // from the session: the value, the value type, validation
                // errors, by the attribute name.
                //case 'CustomerInfoEmail' -> email
                //case 'CustomerInfoEmailErrorFlagClass' -> email (error)

                //It also can be a tag, consisting of the templates
                // <input type="text"> or
                // <select>
                //See else-branch

                //if(){} branch is not used for "all-attributes" templates. It is for custom-templates.
                if(_ml_substr($tag, 0, _ml_strlen($this->HTML_TAGS_PREFIX)) == $this->HTML_TAGS_PREFIX)
                {
                    //:give examples
                    $tag = _ml_substr($tag, _ml_strlen($this->HTML_TAGS_PREFIX));
//                    if(_ml_substr($tag, -_ml_strlen("ErrorFlagClass")) == "ErrorFlagClass")
//                    {
//                        //"Small tag" ("error")
//                        $tag = _ml_substr($tag, 0, -_ml_strlen("ErrorFlagClass"));
//                        $attrInfo = $this->getAttrInfoByTagName($tag);
//                        $value = $attrInfo['error_code'] == '' ? '' : 'error';
//                    }
//                    else
                    if(_ml_substr($tag, -_ml_strlen("FormFieldSize" /*"InputSize"*/)) == "FormFieldSize")//"InputSize")
                    {
                        //"Small tag" ("InputSize")
                        $tag = _ml_substr($tag, 0, -_ml_strlen("FormFieldSize"));//"InputSize"));
                        $attrInfo = $this->getAttrInfoByTagName($tag);
                        $value = $attrInfo['size'] == '' ? '' : $attrInfo['size'];
                    }
                    else
                    if(_ml_substr($tag, -_ml_strlen("FormFieldCols"/*"TEXTAREA Cols"*/)) == "FormFieldCols"/*"TEXTAREA Cols"*/)
                    {
                        //"Small tag" ("TEXTAREA Cols")
                        $tag = _ml_substr($tag, 0, -_ml_strlen("FormFieldCols"));//"TEXTAREA Cols"));
                        $attrInfo = $this->getAttrInfoByTagName($tag);
                        //As for now (2007.01.11) the attributes cols and rows are not stored in the DB.
                        //$value = $attrInfo['cols'] == '' ? '' : $attrInfo['cols'];
                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                        $value = $this->_Current_Attribute["FormFieldCols"];
                    }
                    else
                    if(_ml_substr($tag, -_ml_strlen("FormFieldRows"/*"TEXTAREA Rows"*/)) == "FormFieldRows"/*"TEXTAREA Rows"*/)
                    {
                        //"Small tag" ("TEXTAREA Rows")
                        $tag = _ml_substr($tag, 0, -_ml_strlen("FormFieldRows"));//"TEXTAREA Rows"));
                        $attrInfo = $this->getAttrInfoByTagName($tag);
                        //As for now (2007.01.11) the attributes cols and rows are not stored in the DB.
                        //$value = $attrInfo['rows'] == '' ? '' : $attrInfo['rows'];
                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                        $value = $this->_Current_Attribute["FormFieldRows"];
                    }
                    else
                    if(_ml_substr($tag, -_ml_strlen("FormFieldMaxlength"/*"InputMaxLength"*/)) == "FormFieldMaxlength"/*"InputMaxLength"*/)
                    {
                        //"Small tag" ("InputMaxLength")
                        $tag = _ml_substr($tag, 0, -_ml_strlen("FormFieldMaxlength"));//"InputMaxLength"));
                        $attrInfo = $this->getAttrInfoByTagName($tag);
                        $value = $attrInfo['max'] == '' ? '' : $attrInfo['max'];
                    }
//                    else
//                    if(_ml_substr($tag, -_ml_strlen("LabelCssClass")) == "LabelCssClass")
//                    {
//                        //"Small tag" ("LabelCssClass")
//                        $attrName = _ml_substr($tag, 0, -_ml_strlen("LabelCssClass"));
//                        $attrInfo = $this->getAttrInfoByTagName($attrName);
//                        //$attrName = $tag;
//                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
//                        $value = $this->_Current_Attribute["LabelCssClass"];
//                    }
                    else
                    if(_ml_substr($tag, -_ml_strlen("FieldLabel" /*"Label"*/)) == "FieldLabel")//"Label")
                    {
                        //"Small tag" ("Label")
                        $attrName = _ml_substr($tag, 0, -_ml_strlen("FieldLabel"));//"Label"));
                        $attrInfo = $this->getAttrInfoByTagName($attrName);
                        //It might be taken $attrInfo['attribute_visible_name']
                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                        $value = $this->_Current_Attribute["FieldLabel"];//"Label"];
                    }
                    else
                    if(_ml_substr($tag, -_ml_strlen("FieldError" /*"Label"*/)) == "FieldError")//"Label")
                    {
                        //"Small tag" ("Label")
                        $attrName = _ml_substr($tag, 0, -_ml_strlen("FieldError"));//"Label"));
                        $attrInfo = $this->getAttrInfoByTagName($attrName);
                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                        $value = $this->_Current_Attribute["FieldError"];//"Label"];
                    }
                    else
                    {
                        //"Small tag" ("value")
                        //Check, if it is that very tag, if possible.
                        $attrInfo = $this->getAttrInfoByTagName($tag);
                        $attrName = $tag;
                        $this->genCurrentAttributeHTMLData($attrName, $attrInfo);
                        $value = $this->_Current_Attribute["Value"];
                    }
                }
                else
                {
                    //Small tag
                    // : give examples
                    if(_ml_substr($tag, 0, _ml_strlen($this->HTML_LOCAL_TAGS_PREFIX)) == $this->HTML_LOCAL_TAGS_PREFIX)
                    {
                        //  : give examples
                        $tag = _ml_substr($tag, _ml_strlen($this->HTML_LOCAL_TAGS_PREFIX));

            	        if(array_key_exists($tag, $this->_Current_Attribute))
            	        {
            	            $value = $this->_Current_Attribute[$tag];
            	        }
            	        else
            	        {
                            $err_params = array(
                                                "CODE"    => "CHECKOUT_010"
                                               );
                            _fatal($err_params, $tag);
            	        }
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
    var $CHECKOUT_STORE_BLOCK_NAME;//e.g. "customer-info-input";

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
}
?>