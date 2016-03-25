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
 *                                          .
 *                                                          .
 *                                                         rotate().
 *                                                  ,  . .         ,                
 * php                                                             .
 * @author Alexey Florinsky
 * @package Avactis_Core
 */
class CTraceLogRotation
{
	protected $_file;
	protected $_size;
	protected $_rotate;

	/**
	 *
	 * @param $file                                    
	 * @param $size                          ,                               ,                     
	 * @param $rotate                          ,  . .                                            ,                          .
	 */
	function __construct($file, $size, $rotate)
	{
		$this->_file = $file;
		$this->_size = floatval($size);
		$this->_rotate = intval($rotate);
	}

	/**
	 *                                  
	 */
	function rotate()
	{
		if (file_exists($this->_file) && filesize($this->_file) >= $this->_size*1024*1024)
		{
			//                                                              ,
			//                       ,                                                         
			//              
			$LockFileHandler = new CFile($this->_file.'.lock');
			$LockFileHandler->open('w');
			if (!$LockFileHandler->isError())
			{
				$LockFileHandler->lock(LOCK_EX);
			}

			//                         ,                                  (               )
			//        $renames                          array(exists_files, new_file)
			//           new_file               ,      $renames                                
			//                     .
			//         ,        $renames                             :
			// array(
			//         array('/var/log/logfile', '/var/log/logfile.1'),
			//         array('/var/log/logfile.1', '/var/log/logfile.2'),
			//         array('/var/log/logfile.2', '/var/log/logfile.3'),
			// )
			//          :
			//    1.                ,                /var/log/logfile.3 -              ,              rename
			//    2.                                                     !
			$cnt = 1;
			$renames = array();
			$filename = $this->_file . '.' . $cnt;
			$cnt++;
			$renames[] = array($this->_file, $filename);
			while(file_exists($filename))
			{
				$new_filename = $this->_file . '.' . $cnt;
				$cnt++;
				$renames[] = array($filename, $new_filename);
				$filename = $new_filename;
			}

			//           ,                           
			$removes = array();
			if (count($renames) > $this->_rotate)
			{
				$removes = array_slice($renames, $this->_rotate);
				$renames = array_slice($renames, 0, $this->_rotate);
			}

			//              
			foreach($removes as $f)
			{
				@unlink($f[0]);
			}

			//                                   
			$renames = array_reverse($renames);
			foreach ($renames as $f)
			{
				rename($f[0], $f[1]);
			}

			//                                        
			touch($this->_file);

	        $LockFileHandler->unlock();
	        $LockFileHandler->close();
		}
	}
}

