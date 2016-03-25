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
 * Crypto module.
 *
 * @package Crypto
 * @author Alexander Girin
 */
class Crypto
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Localization  constructor.
     */
    function Crypto()
    {
        global $application;

        $tables = $this->getTables();
        $table = 'crypto_keys';
        $k = $tables[$table]['columns'];

        $query = new DB_Delete($table);
        $query->WhereValue($k["lifetime"], DB_LT, time());
        $application->db->getDB_Result($query);

        $this->blowfish = new Crypt_Blowfish($this->blowfish_gen_blowfish_key());
    }

    function uuid() {

       // The field names refer to RFC 4122 section 4.1.2

       return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
           mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
           mt_rand(0, 65535), // 16 bits for "time_mid"
           mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
           bindec(_ml_substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
               // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
               // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
               // 8 bits for "clk_seq_low"
           mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
       );
    }

    /**
     *
     * @param
     * @return
     */
    function install()
    {
        $tables = Crypto::getTables();
        $query = new DB_Table_Create($tables);
    }

    /**
     *
     * @param
     * @return
     */
    function uninstall()
    {

    }

    /**
     *
     *
     * @param
     * @return
     */
    function encrypt($name, $string)
    {
        if (!$name && !$string)
        {
            return $string;
        }

        global $application;

        $session_id = session_id();
        $key = md5($session_id.$this->uuid());

        $tables = $this->getTables();
        $table = 'crypto_keys';
        $k = $tables[$table]['columns'];

        $query = new DB_Select();
        $query->addSelectField($k["key"], "crypto_key");
        $query->WhereValue($k["id"], DB_EQ, $session_id);
        $query->WhereAnd();
        $query->WhereValue($k["name"], DB_EQ, $name);
        $result = $application->db->getDB_Result($query);
        if (isset($result[0]['crypto_key']) && $result[0]['crypto_key'])
        {
            $query = new DB_Update($table);
            $query->addUpdateValue($k["key"], $key);
            $query->addUpdateValue($k['lifetime'], (time() + 600));
            $query->WhereValue($k["id"], DB_EQ, $session_id);
            $query->WhereAnd();
            $query->WhereValue($k["name"], DB_EQ, $name);
            $application->db->getDB_Result($query);
        }
        else
        {
            $query = new DB_Insert($table);
            $query->addInsertValue($session_id, $k['id']);
            $query->addInsertValue($name, $k['name']);
            $query->addInsertValue($key, $k['key']);
            $query->addInsertValue((time() + 600), $k['lifetime']);
            $application->db->getDB_Result($query);
        }

        $blowfish = new Crypt_Blowfish($key);
        $encrypted_string = $blowfish->encrypt($string);
        return $encrypted_string;
    }

    /**
     *
     *
     * @param
     * @return
     */
    function decrypt($name, $encrypted_string)
    {
        if (!$name && !$encrypted_string)
        {
            return $encrypted_string;
        }

        global $application;

        $session_id = session_id();

        $tables = $this->getTables();
        $table = 'crypto_keys';
        $k = $tables[$table]['columns'];

        $query = new DB_Select();
        $query->addSelectField($k["key"], "crypto_key");
        $query->WhereValue($k["id"], DB_EQ, $session_id);
        $query->WhereAnd();
        $query->WhereValue($k["name"], DB_EQ, $name);
        $result = $application->db->getDB_Result($query);
        if (isset($result[0]['crypto_key']) && $result[0]['crypto_key'])
        {
            $key = $result[0]['crypto_key'];

            $query = new DB_Delete($table);
            $query->WhereValue($k["id"], DB_EQ, $session_id);
            $query->WhereAnd();
            $query->WhereValue($k["name"], DB_EQ, $name);
            $application->db->getDB_Result($query);

            $blowfish = new Crypt_Blowfish($key);
            $blowfish->setKey($key);
            $string = $blowfish->decrypt($encrypted_string);
        }
        else
        {
            return "";
        }

        return $string;
    }

    /**
     *
     */
    function clearDB($session_id = "")
    {
        global $application;

        if (!$session_id)
        {
            $session_id = session_id();
        }

        $tables = $this->getTables();
        $table = 'crypto_keys';
        $k = $tables[$table]['columns'];

        $query = new DB_Delete('crypto_keys');
        $query->WhereValue($k["id"], DB_EQ, $session_id);
        $application->db->getDB_Result($query);
    }

    function convert_rsa_private_key_from_asc_into_cryptrsa_format($rsa_private_key_asc_format)
    {
        $key = $rsa_private_key_asc_format;
        $key_pattern = "/n\:([0-9a-f]*)\;d\:([0-9a-f]*)\;/";
        $matches = array();
        if(preg_match($key_pattern, $key, $matches))
        {
            $key_obj = new Crypt_RSA_Key(convertHex2bin($matches[1]), convertHex2bin($matches[2]), "private");
            return $key_obj;
        }
        else
        {
            //report error
            return false;
        }
    }

    function convert_rsa_public_key_from_asc_into_cryptrsa_format($rsa_public_key_asc_format)
    {
        $key = $rsa_public_key_asc_format;
        $key_pattern = "/n\:([0-9a-f]*)\;e\:([0-9a-f]*)\;/";
        $matches = array();
        if(preg_match($key_pattern, $key, $matches))
        {
            $key_obj = new Crypt_RSA_Key(convertHex2bin($matches[1]), convertHex2bin($matches[2]), "public");
            return $key_obj;
        }
        else
        {
            //report error
            return false;
        }
    }

    function blowfish_uuid()
    {
       // The field names refer to RFC 4122 section 4.1.2
       return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
               mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
               mt_rand(0, 65535), // 16 bits for "time_mid"
               mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
               bindec(_ml_substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
                   // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
                   // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
                   // 8 bits for "clk_seq_low"
               mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
           );
    }

    function blowfish_gen_blowfish_key()
    {
        $session_id = session_id();
        $key = md5($session_id . $this->blowfish_uuid());
        return $key;
    }

    function blowfish_encrypt($text, $key)
    {
        //global $blowfish;
        $this->blowfish->setKey($key);
        $encrypted_string = $this->blowfish->encrypt($this->blowfish_ab_pad_text($text));
        return $encrypted_string;
    }

    function blowfish_decrypt($text, $key)
    {
        //global $blowfish;
        //$blowfish = new Crypt_Blowfish($key);
        $this->blowfish->setKey($key);
        $decrypted_string = $this->blowfish->decrypt($text);
        $decrypted_string = $this->blowfish_ab_unpad_text($decrypted_string);
        return $decrypted_string;
    }

    function blowfish_ab_pad_text($text)
    {
        //16 byte padding.
        //If text is empty, add "aaaaaaaaaaaaaaaa".
        //    if last text bin character is "a" then pad with "b" + 16 "b"s ,
        //    else pad widh "a"

        //To unpad just remove all equal characters starting from the end of the text.
        $len = _byte_strlen($text);
        if($len == 0)
        {
            $pad_char = "a";
            $pad_len = 16;
        }
        else if($len % 16 == 0)
        {
//            $pad_char = $text[$len-1] == "a" ? "b" : "a";
            $pad_char = _byte_substr($text, -1, 1) == "a" ? "b" : "a";
            $pad_len = 16;
        }
        else
        {
//            $pad_char = $text[$len-1] == "a" ? "b" : "a";
            $pad_char = _byte_substr($text, -1, 1) == "a" ? "b" : "a";
            $pad_len = 16 - ($len % 16);
        }

        for($i=0; $i < $pad_len; $i++)
        {
            $text = $text. $pad_char;
        }

        return $text;
    }

    /**
     * see blowfish_ab_pad_text
     */
    function blowfish_ab_unpad_text($text)
    {
        if(empty($text))
        {
            //Perhaps it's a wrong situation.
            return "";
        }
//        $pad_char = $text[_byte_strlen($text)-1];
        $pad_char = _byte_substr($text, -1, 1);
        $i = _byte_strlen($text) - 1;
        for(; ($i >= 0) &&
              (_byte_substr($text, $i, 1) == $pad_char); $i--);
        $new_len = $i+1;
        $text = _byte_substr($text, 0, $new_len);
        return $text;
    }

    /**
     * Checks, if Private key matches Public key. Its important to use
     * a right key to decrypt and encrypt.
     */
    function rsa_do_public_key_match_private_key($rsa_public_key_cryptrsa_format, $rsa_private_key_cryptrsa_format)
    {
        return ($rsa_public_key_cryptrsa_format->_modulus ===
                $rsa_private_key_cryptrsa_format->_modulus);
    }

    /**
     *
     * @return array
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $table = 'crypto_keys';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.session_id'
               ,'name'              => $table.'.variable_name'
               ,'key'               => $table.'.crypto_key'
               ,'lifetime'          => $table.'.lifetime'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_CHAR50
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
               ,'key'               => DBQUERY_FIELD_TYPE_CHAR50
               ,'lifetime'          => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$table]['indexes'] = array
            (
                'UNIQUE IDX_in' => 'id, name'
            );

        global $application;
        return $application->addTablePrefix($tables);
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