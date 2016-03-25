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
 * HtmlForm class.
 * Provides generation of HTML <FORM> elements.
 *
 * @package Core
 * @author Vadim Lyalikov
 * @access  public
 */
class HtmlForm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function HtmlForm()
    {
    }


    /**
     * Generates the HTLM code for <SELECT> control.
     *
     * It inputs data structure
     * array( "onChange" => "[onchange value]"
     *        "select_name" => "name",
     *        "select_name" => "name",
     *        "id" => "id",
     *        "values" => array(array("value" => "1", "contents" => "option_1_1_text"),
     *                          array("value" => "2", "contents" => "option_1_2_text")
     *
     *
     * @param mixed Array $data <SELECT> data: name, values list.
     * @return $string HTML code of generated <SELECT> control.
     */
    function genDropdownSingleChoice($data, $param="")
    {
        if(isset($data["onChange"]))
        {
            $on_change=" onchange=\"".$data["onChange"]."\"";
        }
        else
        {
            $on_change="";
        }

        if(isset($data["id"]))
            $id=" id=\"".$data["id"]."\"";
        else
            $id="";

	if(isset($data["class"]))
            $classvalue=" class=\"".$data["class"]."\"";
        else
            $classvalue="";

        $out = "<select class='form-control " .$data["class"] . "' id='".$data["id"]."' name='" . $data["select_name"] . "' " . $on_change . $id . " ".$param . " >";
        foreach ($data["values"] as $key => $value)
        {
            if(isset($data["selected_value"]) && $value["value"]==$data["selected_value"])
            {
                $is_selected=' selected="selected"';
            }
            else
            {
                $is_selected="";
            }
            $out .= "<option value=\"" .$value["value"]."\"$is_selected>" .$value["contents"]. "</option>";
        }
        $out .= "</select>";
        return $out;
    }

    function getRadio($data, $param = "", $newline = true)
    {
        $out = '';
        foreach ($data["values"] as $key => $value) {
            $out .= '<input type="radio" name="'.htmlspecialchars($data["select_name"]).
                    '" value="'.htmlspecialchars($value['value']).
                    '" id="'.htmlspecialchars($data["select_name"].'_'.$value['value']).'"'.
                    (isset($data["selected_value"]) && $value["value"] == $data["selected_value"] ? ' checked="checked"' : '').
                    ' /><label for="'.htmlspecialchars($data["select_name"].'_'.$value['value']).
                    '">'.htmlspecialchars($value['contents']).'</label>';
            if ($newline) {
                $out .= '<br />';
            }
            $out .= "\n";
        }
        return $out;
    }

    function genCheckbox($data, $param="")
    {
    	if(isset($data["onclick"]))
        {
            $on_change=" onclick=\"".$data["onclick"]."\"";
        }
        else
        {
            $on_change="";
        }

        if(isset($data["value"]))
        {
            $value=" value='".$data["value"]."'";
        }
        else
        {
            $value="";
        }

        if($data["is_checked"] == "checked")
        {
            $checked=" checked='checked'";
        }
        else
        {
            $checked="";
        }

    	$out = "<input type='checkbox' name='".$data['name']."' id='".$data['id']."' ".$checked." ".$value." ".$on_change." ".$param." />";
    	return $out;
    }

    /**
     * Generates the HTLM code for <SELECT> control.
     *
     * It inputs data structure
     * array( "name" => "option_1_name",
     *        "values" => array(array("value" => "1", "contents" => "option_1_1_text"),
     *                          array("value" => "2", "contents" => "option_1_2_text")
     *
     *
     * @param mixed Array $data <SELECT> data: name, values list.
     * @return $string HTML code of generated <SELECT> control.
     *
     *  @param $param is not used.
     */
    function genHiddenField( $html_name, $html_value, $param="")
    {
        $out = " name=\"" . $html_name . "\"" .
               " value=\"" . $html_value . "\"";

        return $out;
    }

    /**
     * Generates the HTLM code for <input type="text"> control.
     *
     *
     *  @param $param is not used.
     */
    function genInputTextField( $html_maxlength, $html_name, $html_size, $html_value, $param="")
    {
        $out = " maxlength=\"" . $html_maxlength . "\"" .
               " name=\"" . $html_name . "\"" .
               " size=\"" . $html_size . "\"" .
               " value=\"" . $html_value . "\" ". $param;

        return $out;
    }

    /**
     * Generates the HTLM code for <input type="file"> control.
     *
     *
     *  @param $param is not used.
     */
    function genInputFileName( $html_name, $html_size, $param="")
    {
        $out = " name=\"" . $html_name . "\"" .
               " size=\"" . $html_size . "\"";

        return $out;
    }

    /**
     * Generates the HTLM code for <input type="file"> control.
     *
     *
     *  @param $param is not used.
     */
    function genInputSubmit( $html_name, $html_value, $param="")
    {
        $out = " name=\"" . $html_name . "\"" .
               " value=\"" . $html_value . "\"";

        return $out;
    }



    /**
     * Generates the HTLM code for <textarea> control.
     *
     * It inputs data structure
     * array( "name" => "option_1_name",
     *        "values" => array(array("value" => "1", "contents" => "option_1_1_text"),
     *                          array("value" => "2", "contents" => "option_1_2_text")
     *
     *
     * @param mixed Array $data <SELECT> data: name, values list.
     * @return $string HTML code of generated <SELECT> control.
     *
     *  @param $param is not used.
     */
    function genInputTextAreaField( $html_cols, $html_name, $html_rows, $html_readonly = false, $param="")
    {
        $out = " cols=\"" . $html_cols .  "\"" .
               " name=\"" . $html_name . "\"" .
               ( $html_readonly ? " readonly" : "") .
               " rows=\"" . $html_rows . "\"";

        return $out;
    }

    /**
     * Generates the HTLM code for <SELECT> control.
     *
     * It inputs data structure
     * array( "name" => "option_1_name",
     *        "values" => array(array("value" => "1", "contents" => "option_1_1_text"),
     *                          array("value" => "2", "contents" => "option_1_2_text")
     *
     *
     * @param mixed Array $data <SELECT> data: name, values list.
     * @return $string HTML code of generated <SELECT> control.
     *
     *  @param $param is not used.
     */
    function genSubmitButton( $html_alt, $html_src, $param="")
    {
        $out = " alt=\"" . $html_name . "\"" .
               " src=\"" . $html_size . "\"";

        return $out;
    }

    /**
     * Generates the HTLM code for <SELECT> control.
     *
     * It inputs data  structure
     * array( "name" => "option_1_name",
     *        "values" => array(array("value" => "1", "contents" => "option_1_1_text"),
     *                          array("value" => "2", "contents" => "option_1_2_text")
     *
     *
     * @param mixed Array $data <SELECT> data: name, values list.
     * @return $string HTML code of generated <SELECT> control.
     *
     *  @param $param is not used.
     */
    function genSubmitScript( $html_form_name, $param="")
    {
//        $out = " onClick = \"" .  " window.opener.focus(); " . $html_form_name . ".submit();\"";
        $out = " onclick = \"" . $html_form_name . ".submit();\"";

        return $out;
    }


    /**
     * Generates the HTLM code for <SELECT> control.
     *
     * It inputs data structure
     * array( "name" => "option_1_name",
     *        "values" => array(array("value" => "1", "contents" => "option_1_1_text"),
     *                          array("value" => "2", "contents" => "option_1_2_text")
     *
     *
     * @param mixed Array $data <SELECT> data: name, values list.
     * @return $string HTML code of generated <SELECT> control.
     *
     *  @param $param is not used.
     */
    function genForm($html_action, $html_method, $html_name, $param="")
    {
        $out = " action=\"" . $html_action . "\"" .
               " method=\"" . $html_method . "\"" .
               " name=\"" . $html_name . "\"" ;

        return $out;
    }

    /**
     * Generates the HTLM code for <SELECT> control for days.
     *
     * @param $name - name of the list element
     * @param $selected_value - value of the selected day. Values 1-31.
     *
     * @return $string HTML code of generated <SELECT> control.
     */
    function genDropdownDaysList($name="", $selected_value="", $param="")
    {
        $out = "";
        for ($i=1; $i<=31; $i++)
        {
            if(is_int(intval($selected_value)) && $i==$selected_value)
            {
                $is_selected=' selected="selected"';
            }
            else
            {
                $is_selected="";
            }
            $out .= '<option value="' . sprintf("%02d", $i) . '" ' . $is_selected . '>' . $i . '</option>';
        }
        if ($name != '')
        {
            if ($param && _ml_substr($param, 0, 1) != ' ')
                $param = ' ' . $param;
            $out = '<select class="date-from" name="' . $name . '"' . $param . '>' . $out . '</select>';
        }
        return $out;
    }

    /**
     * Generates the HTLM code for <SELECT> control for months.
     *
     * @param $name - name of the list element
     * @param $selected_value - value of the selected month. Values 1-12.
     *
     * @return $string HTML code of generated <SELECT> control.
     */
    function genDropdownMonthsList($name="", $selected_value="", $param="")
    {
        global $application;
        $msg = &$application->getInstance('MessageResources');

        $out = "";
        for ($i=1; $i<=12; $i++)
        {
            if(is_int(intval($selected_value)) && $i==$selected_value)
            {
                $is_selected=' selected="selected"';
            }
            else
            {
                $is_selected="";
            }
            $out .= "<option value=\"" .sprintf("%02d", $i)."\" ".$is_selected.">".$msg->getMessage(sprintf("MONTH_%03d", $i)). "</option>";
        }
        if ($name != "")
        {
            $out = "<select class='date-from' name=" . $name . ">".$out."</select>";
        }
        return $out;
    }

    /**
     * Generates the HTLM code for <SELECT> control for a year.
     *
     * @param $name - name of the list element
     * @param $selected_value - value of the selected month. Values 1-12.
     *
     * @return $string HTML code of generated <SELECT> control.
     */
    function genDropdownYearsList($name="", $selected_value="", $start_year="", $end_year=10, $param="")
    {
        if ($start_year == "" || !is_int($start_year))
        {
            $start_year = date("Y", time());
        }

        $out = "";
        for ($i=0; $i<=$end_year; $i++)
        {
            if(is_int(intval($selected_value)) && ($start_year+$i)==$selected_value)
            {
                $is_selected=' selected="selected"';
            }
            else
            {
                $is_selected="";
            }
            $out .= "<option value=\"" .($start_year+$i)."\" ".$is_selected.">".($start_year+$i). "</option>";
        }
        if ($name != "")
        {
            $out = "<select class='date-from' name=" . $name . ">".$out."</select>";
        }
        return $out;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    #var $view = NULL;
    #var $action = NULL;
    #var $keyvalList = array();

    /**#@-*/

}


?>