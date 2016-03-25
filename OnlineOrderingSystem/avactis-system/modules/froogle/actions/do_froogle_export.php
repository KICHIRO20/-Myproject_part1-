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
 * @package Froogle
 * @author Egor Makarov
 */
class do_froogle_export extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * do_froogle_export constructor
     */
    function do_froogle_export()
    {
    }

    function getExportSettings()
    {
    }

    /**
     *                                     .                                      .
     * @param array $newSettings                          ,              DataWriterFroogle
     */
    function saveSettings($newSettings)
    {
        $sets = array();
        $sets['LOCATION'] = $newSettings['froogle_location'];
        $sets['PAYMENT_NOTES'] = $newSettings['froogle_payment_notes'];
        $sets['PAYMENT_ACCEPTED'] = $newSettings['froogle_payment_accepted'];
        modApiFunc('Froogle', 'updateSettings', $sets);
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $frg_target = $request->getValueByKey('frg_target');

        global $_RESULT;

        switch($frg_target)
        {
            case 'init':

                $settings['froogle_expires_date'] = $request->getValueByKey('expiration_date');
                $settings['froogle_location'] = $request->getValueByKey('location');
                $settings['froogle_payment_accepted'] = $request->getValueByKey('payment_accepted');
                $settings['froogle_export_file'] = $application->getAppIni('PATH_CACHE_DIR') . 'products.txt';
                $settings['froogle_storefront_link'] = $request->getValueByKey('storefront_link');

                $notes = $request->getValueByKey('payment_notes');
                $lines = explode("\n", $notes);
                $notes = implode(' ', $lines);
                $settings['froogle_payment_notes'] = $notes;

                $this->saveSettings($settings);

                $settings = array_merge($settings, array(
                    'script_code' => 'Products_DB_Froogle'
                   ,'script_step' => 1
                   ,'product_type_id' => 0      //
                   ,'product_category_id' => $request->getValueByKey('src_category')
                   ,'categories_export_recursively' => $request->getValueByKey('export_recurs')=='Y' ? 'RECURSIVELY' : ''
                ));

                modApiFunc('Data_Converter', 'initDataConvert', $settings);

                $_RESULT["errors"] = modApiFunc('Data_Converter','getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter','getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter','getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter','getProcessInfo');

                break;

            case 'do':
                modApiFunc('Data_Converter','doDataConvert');
                $_RESULT["errors"] = modApiFunc('Data_Converter','getErrors');
                $_RESULT["warnings"] = modApiFunc('Data_Converter','getWarnings');
                $_RESULT["messages"] = modApiFunc('Data_Converter','getMessages');
                $_RESULT["process_info"] = modApiFunc('Data_Converter','getProcessInfo');
                break;

            case 'get':
                header ("Pragma: no-cache");
                header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header ("Content-Type: text/plain");
                header ("Content-Length: " . filesize($application->getAppIni('PATH_CACHE_DIR') . 'products.txt'));
                header ("Content-Disposition: attachment; filename=\"products.txt\"");
                $csv_file = fopen($application->getAppIni('PATH_CACHE_DIR').'products.txt','r');
                while(!feof($csv_file))
                    echo fread($csv_file,4096);
                fclose($csv_file);
                unlink($application->getAppIni('PATH_CACHE_DIR').'products.txt');
                die();
                break;
        };
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