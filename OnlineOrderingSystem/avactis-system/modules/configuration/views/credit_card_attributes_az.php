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
 * Configuration Module, Mail Settings.
 *
 * @package Configuration
 * @author Ravil Garafutdinov
 */
class CreditCardAttributes
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * MailParamList constructor.
     */
    function CreditCardAttributes()
    {
        global $application;
        $request = $application->getInstance('Request');
        $cc_id = $request->getValueByKey('cc_id');
        if ($cc_id == null)
            $cc_id = 1;

        $this->cc_id = $cc_id;

        $this->card = null;
        $cc_types = modApiFunc("Configuration", "getCreditCardSettings", false);
        foreach ($cc_types as $type)
        {
            if ($type['id'] == $cc_id)
            {
                $this->card = $type;
            }
        }

        $this->attr = modApiFunc('Configuration', 'getAttributesForCardType', $this->cc_id);

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
     *                         POST
     */
    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
        $this->POST = $SessionPost;
    }

    /**
     *                         DB
     */
    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript"  => "false"
               ,"FormSubmitValue" => "save"
                 );
    }



    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }


    function outputItem($id)
    {
        global $application;

        $attr_hidden = "<input type=hidden name='attrs[$id]' value='$id'/>";

        $vis_name = "visible[$id]";
        $req_name = "required[$id]";
        $vis_hidden = '';
        $req_hidden = '';

        $visible = ($this->attr[$id]['visible']) ? 'checked' : '';
        if ($id == 12 || $id == 13)
        {
            $visible .= ' disabled';
            $vis_hidden = "<input type=hidden name='$vis_name' value='on'/>";
        }

        $required = ($this->attr[$id]['required']) ? 'checked' : '';
        if ($id == 13)
        {
            $required .= ' disabled';
            $req_hidden = "<input type=hidden name='$req_name' value='on'/>";
        }

        $this->_Template_Contents = array(
                 'AttrName'           => $this->attr[$id]['name']
                ,'AttrId'             => $id
                ,'AttrHidden'		  => $attr_hidden
                ,'VisibilityCheckbox' => $visible
                ,'VisibilityName'	  => $vis_name
                ,'VisibilityHidden'   => $vis_hidden
                ,'RequiredCheckbox'   => $required
                ,'RequiredName'	      => $req_name
                ,'RequiredHidden'     => $req_hidden
            );

        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "configuration/credit_card_attributes/","credit_card_attr_item.tpl.html", array());
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(
            array(
                 'CreditCardAttributesAction'
				,'HiddenArrayViewState'
				,'CardName'
				,'ModuleName'
				,'Subtitle'
				,'Errors'
				,'ModuleStatusFieldName'
				,'ModuleStatusFieldHint'
				,'ModuleStatusField'
				,"Items"
            ));

        $request = $application->getInstance('Request');

        return modApiFunc('TmplFiller', 'fill', "configuration/credit_card_attributes/","container.tpl.html", array());
    }

    function getMessageBox()
    {
        $html = '';
        if (modApiFunc('Session','is_set','ResultMessage'))
        {
            $messages = modApiFunc('Session','get','ResultMessage');
            modApiFunc('Session','un_set','ResultMessage');

            if (isset($messages['ERRORS']))
            {
                $html .= $this->renderMessages($messages['ERRORS'], "errors.tpl.html");
            }

            if (isset($messages['MESSAGES']))
            {
                $html .= $this->renderMessages($messages['MESSAGES'], "messages.tpl.html");
            }
        }
        return $html;
    }

    function renderMessages($messages, $tpl)
    {
        $this->__msg = '';
        foreach ($messages as $msg)
        {
            $this->__msg .= getMsg("SYS", $msg)."<br>";
        }
        $html = modApiFunc('TmplFiller', 'fill', "configuration/setting-param-list/",$tpl, array());
        $this->__msg = '';
        return $html;
    }

    function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
		    case 'HiddenArrayViewState':
		        $value = $this->outputViewState();
		        break;

		    case 'MessageBox':
		        $value = $this->getMessageBox();
		        break;

            case 'Messages':
                $value = $this->__msg;
                break;

            case 'Errors':
                $value = '';//$this->__msg;
                break;

            case 'CreditCardAttributesAction':
                loadCoreFile('html_form.php');
                $HtmlForm1 = new HtmlForm();

                $request = new Request();
                $request->setView('CreditCardAttributes');
                $request->setAction("UpdateCreditCardAttributes");
                $request->setKey('cc_id', $this->cc_id);
                $form_action = $request->getURL();

                $value = $HtmlForm1->genForm($form_action, "POST", "CreditCardAttributesForm");
                break;

            case "CardName":
                $value = '';
                if (isset($this->card['name']))
                    $value = $this->card['name'];
                break;

            case 'Items':
                $value = '';
                foreach ($this->attr as $id => $a)
                {
                    $value .= $this->outputItem($id);
                }
                break;
            default:
                $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
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

	var $cc_id;
	var $card;
	var $attr;
	var $_Template_Contents;
	var $ViewState;
	var $ErrorsArray;

    /**#@-*/

}
?>