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
 * class MultiLang_Core - using for initial multilang support
 *
 * @access  public
 * @author Sergey Kulitsky
 * @package Core
 */
class MultiLang_Core
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Constructor
     */
    function MultiLang_Core()
    {
        // getting if mb_string is installed
        $this -> _mb_enabled = in_array('mbstring', get_loaded_extensions());

        if ($this -> _mb_enabled)
			CTrace::inf('mb_string is enabled.');
		else
			CTrace::inf('mb_string is NOT enabled, it disabled multilang support.');

        // analyzing mb_string settings
        if ($this -> _mb_enabled)
        {
            // getting if mb_output_handler is set
            $this -> _mb_output = (strpos(ini_get('output_handler'), 'mb_output_handler') !== false);
        }
    }

    /**
     * Returns if multilang support is enabled
     */
    function getMultiLangSupport()
    {
        return $this -> _mb_enabled;
    }

    /**
     * Configures mbstring settings
     */
    function configureMBSettings($charset)
    {
        $charset = _ml_strtolower($charset);

        $this -> _charset = $charset;
        $this -> _internal_charset = $charset;

        if (!$this -> _mb_enabled)
        {
            // setting the codepage for mysql connection
            $this -> _codepage = $this -> getSQLCharacterSet();
            return;
        }

        // getting the encoding for post/get data
        if (_ml_strtolower(ini_get('mbstring.http_input')) == 'pass'
            || !ini_get('mbstring.encoding_translation'))
            $data_charset = $charset;
        else
            $data_charset = ini_get('mbstring.internal_encoding');

        $this -> _internal_charset = 'UTF-8';
        mb_internal_encoding('UTF-8');
        mb_http_output($charset);

        if (!$this -> _mb_output)
            ob_start("mb_output_handler", 8096);

        // setting the codepage for mysql connection
        $this -> _codepage = $this -> getSQLCharacterSet();

        // checking if get/post data is properly encoded
        if ($data_charset != $this -> _internal_charset)
            convertInputData($this -> _internal_charset, $data_charset);
    }

    /**
     * Returns SQL Character Set by page charset
     */
    function getSQLCharacterSet($charset = false)
    {
        if (!$charset)
            $charset = _ml_strtolower($this -> _internal_charset);
        else
            $charset = _ml_strtolower($charset);

        // if $charset is within 1 byte charsets -> return it
        if (isset($this -> _cp_map_1b[$charset]))
            return $this -> _cp_map_1b[$charset];

        // if mb_string is enabled and $charset is within mb_charsets -> return it
        if ($this -> _mb_enabled && isset($this -> _cp_map_mb[$charset]))
            return $this -> _cp_map_mb[$charset];

        // here comes some errors...
        return '';
    }

    /**
     * Returns detailed error for charset
     */
    function getSQLCharacterSetError($charset = false)
    {
        if (!$charset)
            $charset = _ml_strtolower($this -> _internal_charset);
        else
            $charset = _ml_strtolower($charset);

        // codepage cannot be used without mb_string
        if (!$this -> _mb_enabled && isset($this -> _cp_map_mb[$charset]))
            return -1;

        // codepage is unknown
        if (!isset($this -> _cp_map_1b[$charset])
            && !isset($this -> _cp_map_mb[$charset]))
            return -2;

        return 0;
    }

    # if mb_string is installed
    var $_mb_enabled;

    # if mb_output_handler is set
    var $_mb_output;

    # charset of the page
    var $_charset;

    # internal charset
    var $_internal_charset;

    # codepage for bd queries
    var $_codepage;

    var $_cp_map_1b = array(
        'cp-866'       => 'cp866',
        'iso-8859-1'   => 'latin1',
        'iso-8859-2'   => 'latin2',
        'iso-8859-7'   => 'greek',
        'iso-8859-8'   => 'hebrew',
        'iso-8859-8-i' => 'hebrew',
        'iso-8859-9'   => 'latin5',
        'iso-8859-13'  => 'latin7',
        'iso-8859-15'  => 'latin1',
        'koi8-r'       => 'koi8r',
        'tis-620'      => 'tis620',
        'windows-1250' => 'cp1250',
        'windows-1251' => 'cp1251',
        'windows-1252' => 'latin1',
        'windows-1256' => 'cp1256',
        'windows-1257' => 'cp1257',
    );
    var $_cp_map_mb = array(
        'big5'         => 'big5',
        'euc-jp'       => 'ujis',
        'euc-kr'       => 'euckr',
        'gb2312'       => 'gb2312',
        'gbk'          => 'gbk',
        'shift_jis'    => 'sjis',
        'utf-8'        => 'utf8',
    );
}
?>