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
 * Checkout module.
 *
 * @package Checkout
 * @access  public
 */
class ManageCustomFields
{
    var $TemplateFiller;
    var $MessageResources;
    var $HtmlForm;
    var $mode;
    var $var_id;
    var $attr_id;
    var $field_data;
    var $types = array(CUSTOM_FIELD_TYPE_TEXT, CUSTOM_FIELD_TYPE_CHECKBOX, CUSTOM_FIELD_TYPE_SELECT, CUSTOM_FIELD_TYPE_TEXTAREA);

    function ManageCustomFields()
    {
        global $application;
        loadCoreFile('html_form.php');

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $this->HtmlForm = new HtmlForm();

        $request = new Request();
        $this->mode = (isset($_POST["mode"]))?$_POST["mode"]:$request->getValueByKey("mode");
        $this->var_id = (isset($_POST["variant_id"]))?$_POST["variant_id"]:$request->getValueByKey("variant_id");
        $this->attr_id = (isset($_POST["attribute_id"]))?$_POST["attribute_id"]:$request->getValueByKey("attribute_id");

        $custom_fields_data = modApiFunc("Checkout", 'getPersonCustomAttributes', $this->var_id);

        if ($this->attr_id == null && $this->mode != "add")
        {
            if (count($custom_fields_data) != 0)
                $this->attr_id = $custom_fields_data[0]['person_attribute_id'];
        }

        if ($this->mode == null)
            $this->mode = "edit";

        if (count($custom_fields_data) == 0)
            $this->mode = "add";

        if ($this->attr_id != null)
            $this->field_data = modAPIFunc("Checkout","getPersonAttributeData",$this->var_id, $this->attr_id);

         if (modApiFunc("Session","is_set","FormData"))
        {
            $this->field_data['postdata'] = modApiFunc("Session","get","FormData");
            modApiFunc("Session","un_set","FormData");
        }

        if (modApiFunc("Session","is_set","UpdateParent") && modApiFunc("Session","get","UpdateParent") == true )
        {
            modApiFunc("Session","un_set","UpdateParent");
            $application->UpdateParent();
        }
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "Local_ResultMessage" => getMsg('SYS',$msg)
            );
            $mTmplFiller = &$application->getInstance('TmplFiller');
            return $mTmplFiller->fill("checkout/checkout-custom-field-editor/", "result-message.tpl.html",$template_contents);
        }
        else
        {
            return "";
        }
    }

    function output()
    {
        global $application;

        $application->registerAttributes(array(
            'CustomFieldsList', //select box with the list of all custom fields
            'CFldType',
            'CFldValues',
            'CFldParams',
            'CFldVisibleName',
            'CFldDescription',
            'Variant_Id',
            'Attribute_Id',
            'CFldVisible',
            'CFldRequired',
            'DeleteConfirmMsg',
            'PageName',
            'Mode',
            'JS_Code',
            'TxtFldName',
            'FldNumber',
            'ResultMessage',
            'CFldViewTag'
        ));

        return $this->TemplateFiller->fill("checkout/checkout-custom-field-editor/", "container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'CustomFieldsList': $value = $this->getCustomFieldsList(); break;
            case 'CFldVisibleName': $value = $this->getCFldVisibleName(); break;
            case 'CFldDescription': $value = $this->getCFldDescription(); break;
            case 'CFldType': $value = $this->getCFldType(); break;
            case 'CFldValues': $value = $this->getCFldValues(); break;
            case 'CFldParams': $value = $this->getCFldParams(); break;
            case 'Variant_Id': $value = $this->var_id; break;
            case 'CFldVisible': $value = $this->getCFldVisible(); break;
            case 'CFldRequired': $value = $this->getCFldRequired(); break;
            case 'Mode': $value = $this->mode; break;
            case 'DeleteConfirmMsg':
                 if ($this->mode == "edit")
                     $value = str_replace('{PRM}',$this->field_data[0]['person_attribute_visible_name'], getMsg('SYS','CONFIRM_CUSTOM_FIELD_REMOVE'));
                 else
                     $valeu = "";
                 break;
            case 'PageName':
                 $d = modApiFunc("Checkout","getPersonInfoVariantList");
                 $value = str_replace('{SNAME}',$d[$this->var_id]['visible_name'], getMsg('SYS','MNG_CUSTOM_FIELDS_PAGE_NAME'));
                 break;
            case 'JS_Code': $value .= "var sections = new Array();\n";
                 $value .= "sections[0] = 'CUSTOM_FIELD_TYPE_TEXT';\n";
                 $value .= "sections[1] = 'CUSTOM_FIELD_TYPE_TEXTAREA';\n";
                 /*foreach ($this->types as $i=>$t)
                 {
                     $value .= "sections[".$i."] = '".$t."';\n";
                 }*/
                 break;
             case 'TxtFldName': $value = $this->getFldName(); break;
             case 'FldNumber':  $value = $this->getFldNumber(); break;
             case 'ResultMessage': $value = $this->outputResultMessage(); break;
             case 'CFldViewTag': $value = @$this->field_data[0]['person_attribute_view_tag']; break;

        }
        return $value;
    }

    function getCustomFieldsList()
    {
        $params = "";
        if ($this->mode == "add")
        {
            $params = "disabled=disabled";

            return "";
        }

        $custom_fields_data = modApiFunc("Checkout", 'getPersonCustomAttributes', $this->var_id);

        if (empty($custom_fields_data))
        {
            $custom_fields = array(array("value" => "-1", "contents" => getMsg('SYS','LBL_NO_CUSTOM_FIELDS')));
        }
        else
        {
            #preparing the list of fields
            foreach($custom_fields_data as $i=>$cf)
            {
                $name = $cf['person_attribute_visible_name']; //."(".$cf['person_attribute_id'].")";
                $custom_fields[] = array("value" => $cf['person_attribute_id'], "contents" => $name);
            }
        }

        $data = array(
            'select_name' => 'customFieldsList',
            'onChange' => 'javascript: changeField();',
            'id' => 'customFieldsList',
            'selected_value' => $this->attr_id,
        	'class' => 'form-control input-sm input-medium',
            'values' => $custom_fields
        );

        $descr = "<td class=\"text-left\">". getMsg('SYS','LBL_CUSTOM_FIELDS_LIST')."</td><td>";

        return $descr.$this->HtmlForm->genDropdownSingleChoice($data, $params)."</td>";
    }

    function getCFldVisibleName()
    {
        if ($this->mode == "add")
        {
            $value = "";
            if (isset($this->field_data['postdata']))
                $value = $this->field_data['postdata']['customFieldVisibleName'];

        }
        else // mode == "edit"
        {
            $value = $this->field_data[0]['person_attribute_visible_name'];
        }

        return "<input type='text' class=\"form-control input-sm input-medium\" name='customFieldVisibleName' value='".$value."'>";
    }

    function getCFldDescription()
    {
        if ($this->mode == "add")
        {
            $value = "";
            if (isset($this->field_data['postdata']))
                $value = $this->field_data['postdata']['customFieldDescription'];
        }
        else // mode == "edit"
        {
            $value = $this->field_data[0]['person_attribute_description'];
        }

        return "<input type='text' class=\"form-control input-sm input-medium\" name='customFieldDescription' value='".$value."'>";
    }

    /*
     * Generates the list of available field types
     * : by now the list is hardcoded. Perhaps it can be dynamic in future.
     */
    function getCFldType()
    {
        $selected_value = CUSTOM_FIELD_TYPE_TEXT;

        if ($this->mode == 'edit')
            $selected_value = $this->field_data[0]['field_type'];

        if (isset($this->field_data['postdata']))
            $selected_value = $this->field_data['postdata']['customFieldType'];

        $data = array(
            'select_name' => 'customFieldType',
            'onChange' => 'javascript: changeStatus();',
            'id' => 'customTypeChoice',
        	'class' => 'form-control input-sm input-small',
            'selected_value' => $selected_value,
            'values' => array(
                              array("value" => CUSTOM_FIELD_TYPE_TEXT, "contents" => "Text"),
                              //array("value" => CUSTOM_FIELD_TYPE_CHECKBOX, "contents" => "Checkbox"),
                              array("value" => CUSTOM_FIELD_TYPE_SELECT, "contents" => "Single Choice List"),
                              array("value" => CUSTOM_FIELD_TYPE_TEXTAREA, "contents" => "Text Area"),
                        ),
        );

        return $this->HtmlForm->genDropdownSingleChoice($data);
    }

    function getCFldValues()
    {
        global $application;

        $values = "";
        if ($this->mode == "add")
        {
            $values = "";
            if (isset($this->field_data['postdata']['customFieldValues']))
                $values = $this->field_data['postdata']['customFieldValues'];
        }
        else // mode == "edit"
        {
            $itid = $this->field_data[0]['input_type_id'];
            $tables = modAPIFunc("Catalog","getTables");
            $t_input_type_values = $tables['input_type_values']['columns'];

            $query = new DB_Select();

            $query->setMultiLangAlias('_ml_value', 'input_type_values',
                                      $t_input_type_values['value'],
                                      $t_input_type_values['id'], 'Catalog');
            $query->addSelectField($query->getMultiLangAlias('_ml_value'), 'value');
            $query->WhereValue($t_input_type_values['it_id'], DB_EQ, $itid);
            $query->SelectOrder($t_input_type_values['id'], 'ASC');

            $result = $application->db->getDB_Result($query);
            foreach ($result as $r)
                $values .= modApiFunc('Catalog', 'getInputTypeActualValue', $r['value']) . "\n";
        }

        return "<textarea class=\"form-control\"".$this->HtmlForm->genInputTextAreaField(20, 'customFieldValues', 5)." id='customFieldValues' disabled=disabled>".$values."</textarea>";
    }

    function getCFldVisible()
    {
        if ($this->mode == "add")
        {
            $checked = true;
        }
        else // mode == "edit"
        {
            $checked = ($this->field_data[0]['person_attribute_visible'] == 0)?false:true;
        }

        $data = array("name" => "customFieldVisible", "id"=>"customFieldVisible", "is_checked"=>$checked, "value"=>"1");


        return $this->HtmlForm->genCheckbox($data);
    }

    function getCFldRequired()
    {
        if ($this->mode == "add")
        {
            $checked = false;
        }
        else // mode == "edit"
        {
            $checked = ($this->field_data[0]['person_attribute_required'] == 0)?false:true;
        }

        $data = array("name" => "customFieldRequired", "id"=>"customFieldRequired", "is_checked"=>$checked, "value"=>"1");

        return $this->HtmlForm->genCheckbox($data);
    }

    function getFldName()
    {
        if ($this->mode == "add")
        {
            return getMsg('SYS','NEW_CUSTOM_FIELD');
        }
        else // mode == "edit"
        {
            return $this->field_data[0]['person_attribute_visible_name'];
        }
    }

    function getFldNumber()
    {
        $custom_fields_data = modApiFunc("Checkout", 'getPersonCustomAttributes', $this->var_id);
        return(count($custom_fields_data));
    }

    function getCFldParams()
    {
        $params = "";
        $invalid = "";
        $data = array();
        if ($this->mode == "edit" && $this->field_data[0]['field_params'] != null)
        {
            $data = unserialize($this->field_data[0]['field_params']);
        }

        if (isset($this->field_data['postdata']))
            $data = $this->field_data['postdata']['params'];

        foreach ($this->types as $type)
        {
            $params .= "<span id='type_".$type."' style='visibility: hidden;display:none;align=left;'>";
            if (isset($this->field_data['postdata']['invalid_params']) && $type == $this->field_data['postdata']['invalid_params'])
                $invalid = "-invalid";
            else
                $invalid = "";

            switch ($type)
            {
                case 'CUSTOM_FIELD_TYPE_TEXT':
                        $template_params = array(
                            'Type'=>$type,
                            'CFldText_param_class'=>(isset($data['class']))?$data['class']:"Normal",
                            'CFldText_param_size'=>(isset($data['size']))?$data['size']:"20",
                            'CFldText_param_maxlength'=>(isset($data['maxlength']))?$data['maxlength']:"128"
                        );
                        $params .= $this->TemplateFiller->fill("checkout/checkout-custom-field-editor/", "text-params".$invalid.".tpl.html", $template_params);
                    break;
                case 'CUSTOM_FIELD_TYPE_CHECKBOX':
                        $template_params = array(
                            'Type'=>$type,
                            'CFldCheckbox_param_class'=>(isset($data['class']))?$data['class']:"Normal"
                        );
                        //$params .= $this->TemplateFiller->fill("checkout/checkout-custom-field-editor/", "checkbox-params".$invalid.".tpl.html", $template_params);
                    break;
                case 'CUSTOM_FIELD_TYPE_SELECT':
                        $template_params = array(
                            'Type'=>$type,
                            'CFldSelect_param_class'=>(isset($data['class']))?$data['class']:"Normal"
                        );
                        //$params .= $this->TemplateFiller->fill("checkout/checkout-custom-field-editor/", "select-params".$invalid.".tpl.html", $template_params);
                    break;
                case 'CUSTOM_FIELD_TYPE_TEXTAREA':
                        $template_params = array(
                            'Type'=>$type,
                            'CFldTextarea_param_class'=>(isset($data['class']))?$data['class']:"Normal",
                            'CFldTextarea_param_rows'=>(isset($data['rows']))?$data['rows']:"7",
                            'CFldTextarea_param_cols'=>(isset($data['cols']))?$data['cols']:"7"
                        );
                        $params .= $this->TemplateFiller->fill("checkout/checkout-custom-field-editor/", "textarea-params".$invalid.".tpl.html", $template_params);
                    break;
            }
            $params .= "</span>";
        }

        return $params;
    }
}
?>