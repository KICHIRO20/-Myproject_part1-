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
 * Error_Document module.
 *
 * @package Error_Document
 * @author HBWSL
 */
class Error_Document
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Error_Document constructor.
     */
    function Error_Document()
    {

    }

    function install()
    {
        global $application;
	    $tables = Configuration :: getTables();
	    $columns = $tables['store_settings']['columns'];

		$query = new DB_Insert('store_settings');
        $query->addInsertValue('enable_error_document', $columns['name']);
        $query->addInsertValue('boolean', $columns['type']);
        $query->addInsertValue('0', $columns['value']);
        $application->db->getDB_Result($query);
	}

	function isActive()
	{
        global $application;
        $tables = Configuration :: getTables();
	    $columns = $tables['store_settings']['columns'];
        $result_rows = array();
        $query = new DB_Select();
        $query->addSelectField($columns['value'], 'value');

        $query->WhereValue($columns['name'], DB_EQ, 'enable_error_document');
        $result_rows = $application->db->getDB_Result($query);
        return $result_rows[0]['value'];
	}

	function updateStatus($errdoc_status)
	{
        global $application;
        $tables = Configuration :: getTables();
	    $columns = $tables['store_settings']['columns'];

        $query = new DB_Update('store_settings');
        $query->addUpdateValue($columns['value'], (int) $errdoc_status);
        $query->WhereValue($columns['name'], DB_EQ, 'enable_error_document');
        $application->db->getDB_Result($query);
	}

	function prepareHtaccessCode($arr_err_code)
	{
		global $application;
        loadCoreFile('URI.class.php');
        $uriObj = new URI($application->getAppIni('HTTP_URL'));
		$url_dir = $uriObj->getPart('dir') . ((_ml_substr($uriObj->getPart('dir'), -1) != '/') ? '/' : '');

		$hta_content = ERRDOC_BLOCK_IDENT_BEGIN."\n";
		/* ignore list htaccess code - starts */
		$hta_content .= str_replace(
				'%files_to_ignore%',FILES_TO_IGNORE
				,file_get_contents(dirname(__FILE__).'/includes/errdoc_ignore_list_block_first_strings')
			);
		foreach($arr_err_code as $error_code => $error_page)
		{
			$hta_content .= str_replace('%error_code%', $error_code, file_get_contents(dirname(__FILE__).'/includes/errdoc_block_for_ignore_list'));
		}
		$hta_content .= file_get_contents(dirname(__FILE__).'/includes/errdoc_ignore_list_block_last_strings');
		/* ignore list htaccess code - ends */

		foreach($arr_err_code as $error_code => $error_page)
		{
			$hta_content .= str_replace(
				 array('%error_code%','%url_dir%','%error_code_file%')
				,array($error_code,$url_dir,$error_page)
				,file_get_contents(dirname(__FILE__).'/includes/errdoc_block_for_all_files'));
		}
			$hta_content .= ERRDOC_BLOCK_IDENT_END;
			return $hta_content;
	}

	function writeHtaccessCode($arr_err_code)
	{
		global $application;
		$hta_file_path = $application->appIni["PATH_ASC_ROOT"].'.htaccess';
		$this->removeHtaccessCode();
		$hta_content = $this->prepareHtaccessCode($arr_err_code);
		$file = new CFile($hta_file_path);
		$file->appendContent($hta_content);
	}

	function getErrDocCode()
	{
		global $application;
		$hta_file_path = $application->appIni["PATH_ASC_ROOT"].'.htaccess';
        $lines = file($hta_file_path);
        $hta_content = '';
        $i = 0;
        while($i < count($lines) and $lines[$i] != ERRDOC_BLOCK_IDENT_BEGIN."\n")
        {
            $i++;
        };
        while($i < count($lines) and $lines[$i] != ERRDOC_BLOCK_IDENT_END."\n")
        {
            $hta_content .= $lines[$i];
            $i++;
        };
        $hta_content .= $lines[$i];
		return $hta_content;
	}

	function removeHtaccessCode()
	{
		global $application;
		$hta_file_path = $application->appIni["PATH_ASC_ROOT"].'.htaccess';
		$code = $this->getErrDocCode();
		$hta_content = file_get_contents($hta_file_path);
		asc_file_put_contents($hta_file_path,str_replace($code,'',$hta_content));
	}
}