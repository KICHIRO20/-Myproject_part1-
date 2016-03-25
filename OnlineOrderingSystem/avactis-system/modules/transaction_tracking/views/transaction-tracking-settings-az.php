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
 * TransactionTracking Settings view.
 *
 * @package TransactionTracking
 * @author VadimLyalikov
 */

class TransactionTrackingSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  TransactionTrackingSettings constructor.
     */
    function TransactionTrackingSettings()
    {
        global $application;
        modApiFunc("TransactionTracking", "TransactionTracking");
        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("TransactionTrackingSettings"))
        {
            $this->NoView = true;
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
                "ResultMessage" => getMsg('TT', $msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
            $res = $this->mTmplFiller->fill("settings/", "result-message.tpl.html",array());
            return $res;
        }
        else
        {
            return "";
        }
    }

    function getModulesList()
    {
        global $application;
        $res = "";
        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
        $modules = TransactionTracking::getInstalledModules();

        //ClixGalore
        $this->_Current_Module = array('ModuleUID' => MODULE_CLIXGALORE_UID
                                      ,'ModuleName' => getMsg('TT', 'CLIXGALORE_LABEL')
                                      ,"ModuleChecked" => ($modules[MODULE_CLIXGALORE_UID]['status_active'] == DB_TRUE) ? 'CHECKED' : ""
                                      ,'ModuleSettingsControls' => $this->outputClixGaloreSettings());
        $application->registerAttributes($this->_Current_Module);
    	$res .= $this->mTmplFiller->fill("settings/", "item.tpl.html",array());

    	//Google Analytics
        $this->_Current_Module = array('ModuleUID' => MODULE_GOOGLE_ANALYTICS_UID
                                      ,"ModuleName" => getMsg('TT', 'GA_LABEL')
                                      ,"ModuleChecked" => ($modules[MODULE_GOOGLE_ANALYTICS_UID]['status_active'] == DB_TRUE) ? 'CHECKED' : ""
                                      ,'ModuleSettingsControls' => $this->outputGASettings());
        $application->registerAttributes($this->_Current_Module);
        $res .= $this->mTmplFiller->fill("settings/", "item.tpl.html",array());

        return $res;
    }

    /**
     * Otputs the view.
     *
     * @ $request->setView  ( '' ) - define the view name
     */
    function outputGASettings()
    {
        global $application;
        $settings = TransactionTracking::getModulesSettings();
        $this->_Current_Module_Settings = array('GA_ACCOUNT_NUMBER' => $settings[MODULE_GOOGLE_ANALYTICS_UID]['GA_ACCOUNT_NUMBER']
                                               ,'GAUID' => MODULE_GOOGLE_ANALYTICS_UID);
        $application->registerAttributes($this->_Current_Module_Settings);

        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
        return $this->mTmplFiller->fill("settings/", "settings_ga.tpl.html",array());
    }

    function outputClixGaloreSettings()
    {
        global $application;
        $settings = TransactionTracking::getModulesSettings();
        $this->_Current_Module_Settings = array('CLIXGALORE_AD_ID' => $settings[MODULE_CLIXGALORE_UID]['CLIXGALORE_AD_ID']
                                               ,'ClixGaloreUID' => MODULE_CLIXGALORE_UID);
        $application->registerAttributes($this->_Current_Module_Settings);

        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
        return $this->mTmplFiller->fill("settings/", "settings_clixgalore.tpl.html",array());
    }

    function output()
    {
        global $application;

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "TransactionTrackingSettings", "Errors");
            return "";
        }

        $application->registerAttributes(array('Items' => ""
                                               ,"EditTransactionTrackingSettingsForm" => ""
                                               ,"ResultMessageRow" => ""));

        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');

        $this->MessageResources = &$application->getInstance('MessageResources');
        return $this->mTmplFiller->fill("settings/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Items':
                $value = $this->getModulesList();
                break;
            case 'EditTransactionTrackingSettingsForm':
                loadCoreFile('html_form.php');
		        $HtmlForm1 = new HtmlForm();
		        $request = new Request();
		        $request->setView('TransactionTrackingSettings');
		        $form_action = $request->getURL();
		        $value = $HtmlForm1->genForm($form_action, "POST", "EditTransactionTrackingSettingsForm");
		        break;
            case 'ResultMessageRow':
                $value = $this->outputResultMessage();
                break;
        break;
            default:
                //Current Module (Modules List Item) details
                $value = getKeyIgnoreCase($tag, $this->_Current_Module);
                if($value === NULL)
                {
                    $value = getKeyIgnoreCase($tag, $this->_Current_Module_Settings);
                }
                if($value === NULL)
                {
                    $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
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

    /**
     * A reference to the object TemplateFiller.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current selected template.
     *
     * @var array
     */
    var $template;

    /**
     * Current selected module info. It is used for the internal processing.
     *
     * @var array
     */
    var $_Current_Module;
    var $_Current_Module_Settings;

    /**#@-*/
}
?>