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

define('UPDATESERVER_PROTO','HTTP');
define('UPDATESERVER_METHOD','POST');
define('UPDATESERVER_HOST_PATH','shopcartupdate.local/xml.gateway.php');
define('UPDATESERVER_URL_WITH_PROTO', _ml_strtolower(UPDATESERVER_PROTO).'://'.UPDATESERVER_HOST_PATH);

class UpdateClient
{
    function UpdateClient()
    {
    }

    function requestGetLatestVersion($installation_uuid, $license_key, $license_url, $from_version_type, $from_version_number)
    {
        $xml_request=   '<?xml version="1.0" encoding="ISO-8859-1"?>'.
                        '<UPD_REQUEST>'.
                        '    <UPD_ACTION>GET_LATEST_VERSION</UPD_ACTION>'.
                        '    <INSTALLATION_UUID>' . $installation_uuid . '</INSTALLATION_UUID>'.
                        '    <LK_KEY>' . $license_key . '</LK_KEY>'.
                        '    <LK_URL>' . $license_url . '</LK_URL>'.
                        '    <UPD_FROM_VERSION_TYPE>' . $from_version_type . '</UPD_FROM_VERSION_TYPE>'.
                        '    <UPD_FROM_VERSION_NUMBER>' . $from_version_number . '</UPD_FROM_VERSION_NUMBER>'.
                        '</UPD_REQUEST>';

        /* Response scheme, LK_PRODUCT_TYPE value may be different:
           <LK_RESPONSE>
            <LK_STATUS>(OK | INVALID)</LK_STATUS>
            <LK_PRODUCT_TYPE>(BASIC | PRO | INVALID)</LK_PRODUCT_TYPE>
            <LK_UPDATE>(OK | UPDATE_EXPIRED | INVALID)</LK_UPDATE>
            <LK_SUPPORT>(OK | SUPPORT_EXPIRED | INVALID)</LK_SUPPORT>
           </LK_RESPONSE>
        */
        $response = array( 'UPD_LATEST_VERSION_TYPE'   => 'INVALID',
                           'UPD_LATEST_VERSION_NUMBER' => 'INVALID',
                           'UPD_ERROR_CODE'            => '',
                           'UPD_ERROR_PARAMS'          => serialize(array()));
        return $this->_process_request($xml_request, $response);
    }

    function _process_request(&$xml_request, &$default_response)
    {
        $xml_request = urlencode($xml_request);
        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();

        $bnc->setMethod(UPDATESERVER_METHOD);
        $bnc->setURL(UPDATESERVER_PROTO.'://'.UPDATESERVER_HOST_PATH.'?xml='.$xml_request);
        switch(UPDATESERVER_METHOD)
        {
            case "GET":
            {
                $bnc->setURL(UPDATESERVER_PROTO.'://'.UPDATESERVER_HOST_PATH.'?xml='.$xml_request);
                break;
            }
            case "POST":
            {
                $bnc->setURL(UPDATESERVER_PROTO.'://'.UPDATESERVER_HOST_PATH);
                $bnc->setPOSTstring($bnc->prepareDATAstring(array("xml" => $xml_request)));
                break;
            }
        }
        $result = $bnc->RunRequest();

        if ($result == false)
        {
            return false;
        }
//die(print_r($result['body'], true));
        loadCoreFile('obj_xml.php');
        $xml = new xml_doc($result['body']);
//die(print_r($result['body'], true));
        $xml->parse();

        if (!is_object($xml->document))
        {
            return false;
        }
//die(print_r($xml->document,true));
        foreach ($default_response as $key=>$val)
        {
            $xml_tag = $xml->document->findChild($key);
            if ($xml_tag != FALSE)
            {
                $default_response[$key] = $xml_tag->contents;
            }
        }
//die(print_r($default_response,true));
        return $default_response;
    }

}

?>