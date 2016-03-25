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
define('LICENSESERVER_PROTO','HTTP');
define('LICENSESERVER_METHOD','GET');

define('ACCOUNTSERVER_URL', 'licenses.avactis.com/xml.public.gateway2.php');


/**
 * Forms a request to Account Server
 */
class AccountServerRequest
{
    /**
     * XML body of the request
     * @var string
     */
    var $request; // the request body (well-formed XML)
    /**
     * Operation code
     * @var string
     */
    var $op; // operation code
    /**
     * The list of properties to be sent
     * @var array
     */
    var $props; //the list of properties. 1-level array

    function AccountServerRequest ($op, $properties = array())
    {
        $this->op = $op;
        $this->props = $properties;

        loadCoreFile('obj_xml.php');

        $xml = new xml_doc();
        $req = $xml->createTag('request');
        $ope = $xml->createTag('operation', array(), $this->op, $req);
        $tim = $xml->createTag('timestamp', array(), time(), $req);
        $data = $xml->createTag('properties', array(), "", $req);
        foreach ($this->props as $p_name => $p_cont)
        {
            $property = $xml->createTag('property', array("name" => $p_name), $p_cont, $data);
        }

        $this->request = $xml->generate();
    }
}

class LicenseAccountClient
{
    function LicenseAccountClient()
    {
    }

    /**
     * Forms a license registration request
     * @param array $data 1-level assoc array like ("Param1"=>$p1, "Param2"=>$p2)
     * @return array Accoutn Server response
     */
    function requestRegisterLicense($data)
    {
        $req = new AccountServerRequest("001",$data);

        $response = array();

        return $this->_process_request($req->request, true);
    }

    /**
     * Forms a license update request
     * @param array $data 1-level assoc array like ("Param1"=>$p1, "Param2"=>$p2)
     * @return array Accoutn Server response
     */
    function requestUpdateLicense($data)
    {
        $req = new AccountServerRequest("002",$data);

        return $this->_process_request($req->request, true);
    }

    function requestCheckLicense($license_key, $license_url)
    {
        $properties = array("LicenseKey"=>$license_key, "LicenseDomain"=>$license_url, "LicenseType"=>PRODUCT_VERSION_INTERNAL_TYPE);

        $req = new AccountServerRequest("000",$properties);

        return $this->_process_request($req->request, true);
    }

    function _process_request(&$data,$response_required = false)
    {
        $xml_request = urlencode($data);
        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();
        $bnc->setHTTPversion("1.0");
        $bnc->setMethod(LICENSESERVER_METHOD);
        $bnc->setURL(LICENSESERVER_PROTO.'://'.ACCOUNTSERVER_URL.'?xml='.$xml_request);
        $result = $bnc->RunRequest();

        if ($result == false)
        {
            return false;
        }

        if ($response_required)
        {
            loadCoreFile('obj_xml.php');
            $xml = new xml_doc($result['body']);
            $res = $xml->parse();

            if ($res !== true)
            {
                return false;
            }

            if (!is_object($xml->document))
            {
                return false;
            }

            // general check

            // response data
            $response = array(
                "CODE" => "not_set",
                "MESSAGE" => "not_set",
                "CERT" => "not_set",
            );

            foreach ($response as $key=>$val)
            {
                $xml_tag = $xml->findTag($key);
                if ($xml_tag != FALSE) $response[$key] = $xml_tag->contents;
            }

            return $response;
        }
        else
            return true;
    }

}

?>