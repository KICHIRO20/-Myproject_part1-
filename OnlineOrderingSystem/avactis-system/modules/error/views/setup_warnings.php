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
 * @copyright Copyright &copy; 2005, HBWSL
 * @package Error
 * @author ag
 */
_use(dirname(__FILE__) . '/error_view.php');
/**
 * SetupWarnings is a view of errors or warnings, which is concerned with the
 * general configuration. For example, check if folders are writable
 * and if distribution files exist or not.
 *
 * @package Error
 * @access public
 */
class SetupWarnings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function SetupWarnings()
    {
//        parent::Abstract_Error_View();
    }

    function getWarnings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
///        $replacer = &$application->getInstance('Replacer');
        $HTMLCode = '';

        //Not to double the code, info on warnings
        //  is in the table.
        $warnings_data = array
                    (
                        array("class" => "Catalog",
                              "func" => "isImageFolderNotWritable",
                              "warn_code" => "SETUP_WARNING_IMAGE_FOLDER_IS_NOT_WRITABLE",
                              "replacer_params" => array("0" => modApiFunc("Catalog", "getImagesDir")))
                       ,array("class" => "Application",
                              "func" => "isCacheFolderNotWritable",
                              "warn_code" => "SETUP_WARNING_CACHE_FOLDER_IS_NOT_WRITABLE",
                              "replacer_params" => array("0" => modApiFunc("Application", "getAppIni", 'PATH_CACHE_DIR')))
                       ,array("class" => "Application",
                              "func" => "areInstallerFilesNotRemoved",
                              "warn_code" => "SETUP_WARNING_INSTALLER_FILES_ARE_NOT_REMOVED",
                              "replacer_params" => array())
                       ,array("class" => "Tools",
                              "func" => "isBackupFolderNotWritable",
                              "warn_code" => "SETUP_WARNING_BACKUP_FOLDER_IS_NOT_WRITABLE",
                              "replacer_params" => array("0" => modApiFunc("Tools", "getBackupDir")))
                       ,array("class" => "Product_Files",
                              "func" => "isDownloadsDirNotWritable",
                              "warn_code" => "SETUP_WARNING_DOWNLOADS_FOLDER_IS_NOT_WRITABLE",
                              "replacer_params" => array("0" => $application->getAppIni('PRODUCT_FILES_DIR')))
                       ,array("class" => "Product_Options",
                              "func" => "isUploadsDirNotWritable",
                              "warn_code" => "SETUP_WARNING_UPLOADS_FOLDER_IS_NOT_WRITABLE",
                              "replacer_params" => array("0" => $application->getAppIni('UPLOAD_FILES_DIR')))
                    );

        foreach($warnings_data as $warning_data)
        {
            //: as for now, warnings can be generated either by the module,
            // or the application class. Other variants are prohibited.
            $check_res = ($warning_data['class'] == "Application") ?
                call_user_func(array(&$application, $warning_data['func'])) :
                modApiFunc($warning_data['class'], $warning_data['func']);
            if($check_res == true)
            {
                $text = $this->MessageResources->getMessage($warning_data['warn_code'], $warning_data['replacer_params']);
                //: fix the passing of optional parameters from the checked
                //  function. For example, folder names are checked if they are writable.
///                $text = $replacer->Replace($text, $warning_data['replacer_params']);

                $this->_warning = array("text" => $text);
                $template = "item.tpl.html";
    		    $HTMLCode .= $this->mTmplFiller->fill("error/setup_warnings/", $template, array());
            }
        }
        return $HTMLCode;
    }

    /**
     * Returns the HTML code of Error view.
     *
     * @ finish the functions on this page
     * @return string HTML code
     */
    function output()
    {
        global $application;
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        $application->registerAttributes(array("Items" => "",
                                               "WarningText" => ""));

        $this->_items = $this->getTag("Items");
        if($this->_items == "")
        {
            $template = "container-empty.tpl.html";
        }
        else
        {
            $template = "container.tpl.html";
        }
        $HTMLCode = $this->mTmplFiller->fill("error/setup_warnings/", $template, array());

        return $HTMLCode;
    }

    /**
     * @ describe the function ProductInfo->getTag.
     */
    function getTag($tag)
    {
        global $application;
    	$value = null;
    	switch ($tag)
    	{
    	    case 'Items':
    	        $value = $this->getWarnings();
    	        break;

    	    case 'WarningText':
    	        $value = $this->_warning['text'];
    	        break;

    		default:
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
     * Gets the template name of the view.
     *
     * @ finish the functions on this page
     * @return string template name
     */
    function getTemplate()
    {
//        return 'Admin View Template';
    }

    /**#@-*/

}
?>