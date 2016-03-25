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

define("CERTIFICATE_OK","CERTIFICATE_OK");
define('CERTIFICATE_NOT_EXISTS','CERTIFICATE_NOT_EXISTS');
define('CERTIFICATE_INVALID','CERTIFICATE_INVALID');

/**
 * License certificate management class
 *
 * An example usage:
 *<code>
 *  loadCoreFile('licensecert.php');
 *  $lc = new LicenseCertificate()
 *</code>
 *
 * @package Core
 * @author Andrei Zhuravlev
 * @access  public
 */
class LicenseCertificate
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Object constructor
     */
    function LicenseCertificate()
    {
        global $application;

        loadCoreFile('licensekey.php');
        loadCoreFile('Blowfish.php');

        $this->license_key = new LicenseKey();

        $this->cipher = new Crypt_Blowfish($this->license_key->getLicenseKey());

        $this->license_cert_file = $application->getAppIni('PATH_CERTIFICATE_KEY_FILE').".php";

        #check certificate file
        if (!is_file($this->license_cert_file))
        {
            $this->certificate_status = CERTIFICATE_NOT_EXISTS;
        }
        else
        {
            #read certificate file
            $this->license_cert = $this->_read_cert_from_file();


            #parse and check
            $this->certificate_status = $this->_validate_cert();
        }
    }


    function _read_cert_from_file()
    {
        $file = new CFile($this->license_cert_file);
        $_rows = $file->getLines();

        $this->cipher->setKey($this->license_key->license_key);
        $certificate = $this->cipher->decrypt(end($_rows)); // the last row in the cert file is the certifcate data.

        return is_string($certificate) ? trim($certificate) : '';
    }

    function _validate_cert($cert = null, $crypted = false)
    {
        if ($cert != null) // this branch will work for saving new certificates and updates.
        {
            if ($crypted)
                  $cert = $this->cipher->decrypt($cert);
            $this->license_cert = @unserialize(trim($cert));
        }
        else
        {
            $this->license_cert = @unserialize(trim($this->license_cert));
        }

        # check if certificate was decoded successfully
        if (empty($this->license_cert) || $this->license_cert == null)
        {
            $this->certificate = array();
            return CERTIFICATE_INVALID;
        }

        if (!is_array($this->license_cert))
        {
            $this->certificate = array();
            return CERTIFICATE_INVALID;
        }

        foreach ($this->license_cert as $name=>$property)
        {
            switch($name)
            {
                case "license_key":
                     if ($this->license_key->checkLicenseKey($property['value']) === false || $this->license_key->license_key !== $property['value'])
                     {
                         $this->certificate = array();
                         return CERTIFICATE_INVALID;
                     }
                break;
                case "license_url":
                     if ($this->license_key->getLicenseURL() !== $property['value'])
                     {
                         #_print($this->license_key->getLicenseURL());
                         #_print($property['value']);die;
                         $this->certificate = array();
                         return CERTIFICATE_INVALID;
                     }
                break;
            }
        }
        $this->certificate = $this->license_cert;

        return CERTIFICATE_OK;
    }


    function _save_cert_to_file($cert_code)
    {
        if ($this->_validate_cert($cert_code, true) !== CERTIFICATE_OK)
        {
            $this->certificate = array();
            $this->certificate_status = CERTIFICATE_INVALID;
            return false;
        }

        $cert = ";<?php exit();?>\n".$cert_code;

        $file = new CFile($this->license_cert_file);
        if (! $file->putContent($cert))
        {
            $this->certificate = array();
            $this->certificate_status = CERTIFICATE_NOT_EXISTS;
            return false;
        }

        $this->license_cert = $this->_read_cert_from_file();

        $this->certificate_status = $this->_validate_cert();

        return true;
    }

    function getLicenseURL()
    {
        if (empty($this->certificate) || $this->certificate_status !== CERTIFICATE_OK)
        {
            return false;
        }
        else
        {
            return $this->certificate['license_url']['value'];
        }
    }

    var $certificate = array();
    var $license_cert;
    var $license_cert_file;
    var $license_key;
    var $cipher;
    var $certificate_status;

    /**#@-*/

}

?>