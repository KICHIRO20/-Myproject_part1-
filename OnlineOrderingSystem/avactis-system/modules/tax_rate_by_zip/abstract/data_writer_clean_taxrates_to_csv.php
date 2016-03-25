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
loadClass('DataWriterDefault');
loadCoreFile('csv_parser.php');

class DataWriterCleanTaxRatesToCSV extends DataWriterDefault
{

//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

	function DataWriterCleanTaxRatesToCSV()
	{
	}

	/**
	 *               -
	 *
	 * @param array $settings -        settings
	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
	 */
	function initWork($settings)
	{
        $this->clearWork();

        $this->_settings = array(
            'out_file' => $settings['out_file']
           ,'headers'  => $settings['headers']
           ,'csv_delimiter' => isset($settings['csv_delimiter']) ? $settings['csv_delimiter'] : ','
           ,"total_string_number" => 0
           ,"valid_string_number" => 0
        );

        $this->_csv_worker = new CSV_Writer();
        $this->_csv_worker->setOutFile($this->_settings['out_file'],'w');
        $this->_csv_worker->setLayout($this->_settings['headers']);
        $this->_csv_worker->setDelimetr($this->_settings['csv_delimiter']);
        $this->_csv_worker->writeLayout();
        $this->_csv_worker->closeOutFile();

        $this->_process_info['status'] = 'INITED';
        $this->_process_info['global']['valid_lines'] = 0;
	}

 	/**
 	 *                            .
     *
     * @param array $data -                      '<tag name>' => '<tag value>'
 	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
 	 */
	function doWork($data)
	{
	    $this->_settings["total_string_number"]++;
        if (!isset($this->_process_info['global']['valid_lines']))
        {
            if (modApiFunc('Session','is_set','DataWriterCleanTaxRatesToCSV_validLines'))
                $this->_process_info['global']['valid_lines'] = modApiFunc('Session','get','DataWriterCleanTaxRatesToCSV_validLines');
            else
                $this->_process_info['global']['valid_lines'] = 0;
        }

	    if ($data["item_data"]["isValid"] == true)
	    {
            if($this->_csv_worker != null)
                $this->_csv_worker->writeArray($data["item_data"]);

            $this->_settings["valid_string_number"]++;
            $this->_process_info['global']['valid_lines']++;
            $this->_warnings = '';
	    }
	    else
	    {
	        $warning = getMsg("TAX_ZIP", "IMPORT_SET_LINE_NOT_VALID");
	        $this->_warnings = str_replace('%1%', $this->_settings["total_string_number"]+1, $warning);
	    }
	}

    function finishWork()
    {
        $this->_csv_worker->closeOutFile();
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataWriterCleanTaxRatesToCSV',$this->_settings);
        modApiFunc('Session','set','DataWriterCleanTaxRatesToCSV_validLines', $this->_process_info['global']["valid_lines"]);
       // $this->_process_info["valid_lines"] = $this->_settings["valid_string_number"];
        $this->_csv_worker->closeOutFile();
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataWriterCleanTaxRatesToCSV'))
        {
            $this->_settings = modApiFunc('Session','get','DataWriterCleanTaxRatesToCSV');
            $this->_csv_worker = new CSV_Writer();
            $this->_csv_worker->setOutFile($this->_settings['out_file'],'a');
            $this->_csv_worker->setLayout($this->_settings['headers']);
            $this->_csv_worker->setDelimetr($this->_settings['csv_delimiter']);
            return;
        };

        $this->_settings = null;
        $this->_csv_worker = null;
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataWriterCleanTaxRatesToCSV');
        $this->_settings = null;
        $this->_csv_worker = null;
    }
}



?>