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

loadCoreFile('urlm.php');

define("CURRENT_REQUEST_URL", "1");

/**
 * Request class.
 *
 * @package Core
 * @author Vadim Lyalikov
 * @access  public
 */
class Request
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The parameter $explicit_url is inputted to generalize the usage
     * of the Request class. Some methods, e.g. Application::
     * redirect, require an object of the Request class as an in parameter.
     * The Request class works with differnt View classes
     * of the AVACTIS system. But sometimes there is a need to redirect to
     * any URL, even out of the installed ASC. The constructor with the
     * explicit URL being as an in parameter, helps to produce a desirable
     * effect. The Request::getURL() method returns that URL unaltered.
     * This method should be used only in paticular cases not to complicate the code.
     *
     * @param string $explicit_url
     * @return Request
     */
    function Request($explicit_url = NULL)
    {
        $this->explicit_url = $explicit_url;
        $this->URLMod = new URLModifier();
        $this->encodeURLs = true;
    }

    function importHTTPRequestData()
    {
        /*
         Converts the current URL.
        */
        $this->URLMod->setURL( $this->selfURL() );

        /*
         If the URL has been converted, then new parameters append
         to the array _GET.
        */
        if ($this->URLMod->decodeURL() == true)
        {
            $url = $this->URLMod->getURL();
            $parsed_url = parse_url($url);
            $query = $parsed_url['query'];
            $query_pairs = explode('&', $query);
            foreach($query_pairs as $pair)
            {
                $key_and_value = explode('=', $pair);
                $_GET[$key_and_value[0]] = (isset($key_and_value[1])) ? urldecode($key_and_value[1]) : '';
            }
        }

    }

    /*
                                             GET   POST       .
             ,                  restoreGETPOSTData                              .
               ,                                                .

                .                                                       .
    */
    function saveCurrentGETPOSTData()
    {
        if (!isset($_GET))
        {
            $_GET = array();
        }
        if (!isset($_POST))
        {
            $_POST = array();
        }

        foreach ($_GET as $k=>$v)
        {
            $this->setKey($k, $v);
        }

        $_SESSION['__RequestSavedGETPOSTData__'] = array(/*'GET'=>$_GET,*/ 'POST'=>$_POST);
    }


    /*
                              GET   POST       ,
                saveCurrentGETPOSTData()

                .                                                       .
    */
    function restoreGETPOSTData()
    {
        if (isset($_SESSION['__RequestSavedGETPOSTData__']))
        {
            /*if (!empty($_SESSION['__RequestSavedGETPOSTData__']['GET']))
            {
                $_GET = array_merge($_GET, $_SESSION['__RequestSavedGETPOSTData__']['GET']);
            }*/
            if (!empty($_SESSION['__RequestSavedGETPOSTData__']['POST']))
            {
                if (!isset($_POST))
                {
                    $_POST = array();
                }
                $_POST = array_merge($_POST, $_SESSION['__RequestSavedGETPOSTData__']['POST']);
            }
            unset($_SESSION['__RequestSavedGETPOSTData__']);
        }
    }

    /**
     * Gets the current full URL.
     *
     * return string  The current full URL
     */
    function selfURL()
    {
        if(!isset($this->selfURL))
        {
            $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
            $protocol = $this->strleft(_ml_strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
            $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);

            if(isset($_SERVER["REDIRECT_STATUS"]) and $_SERVER["REDIRECT_STATUS"]=="200")
            {
                $a = $protocol."://".$_SERVER['SERVER_NAME'].$port.($_SERVER['PHP_SELF'].(isset($_SERVER['QUERY_STRING'])? "?".$_SERVER['QUERY_STRING']:""));
            }
            else
            {
                $a = $protocol."://".$_SERVER['SERVER_NAME'].$port.(isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF'].(isset($_SERVER['QUERY_STRING'])? "?".$_SERVER['QUERY_STRING']:"")));
            };
            $this->selfURL = $a;
        }

        return $this->selfURL;
    }

    /**
     * Gets the action from POST or GET request.
     *
     * @ finish the functions on this page
     * @return mixed  The value of the action or NULL if the action is undefined
     */
    function getCurrentAction()
    {
        if (isset($_GET['act']) && empty($_POST['act']))
        {
            return $_GET['act'];
        }
        elseif (isset($_POST['act']))
        {
            return $_POST['act'];
        }
        elseif (isset($_GET['asc_action']) && empty($_POST['asc_action']))
        {
            return $_GET['asc_action'];
        }
        elseif (isset($_POST['asc_action']))
        {
            return $_POST['asc_action'];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Returns Value of request key from GET or POST.
     *
     * @ finish the functions on this page
     * @param string $key The request key
     * @return mixed Key value or NULL if key value is undefined
     */
    function getValueByKey($key, $default = null)
    {
        if (isset($_GET[$key]))
        {
            return $_GET[$key];
        }
        elseif (isset($_POST[$key]))
        {
            return $_POST[$key];
        }
        /*elseif (isset($this->keyvalList[$key]))
        {
            return $this->keyvalList[$key];
        }*/
        else
        {
            return $default;
        }
    }

    function setView($Viewname)
    {
        if ($Viewname == 'CheckoutView'
            && isset($this -> keyvalList['step_id'])
            && $this -> keyvalList['step_id'] == 4)
            $Viewname = 'OrderPlaced';
        $this->view = $Viewname;
    }

    function setAnchor($anchor)
    {
        $this -> anchor = $anchor;
    }

    /**
     * Different caterories require different template files to
     * be displayed. Set target directory for request object.
     *
     * @param int $category_id
     */
    function setCategoryID($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * Different products require different template files to
     * be displayed. Set target product for request object.
     *
     * @param int $product_id
     */
    function setProductID($product_id)
    {
        $this->product_id = $product_id;
    }

    function setAction($asc_action)
    {
        $this->asc_action = $asc_action;
    }

    function getKey($key, $default=null)
    {
        if (isset($this->keyvalList[$key]))
            return $this->keyvalList[$key];
        else
            return $default;
    }

    function setKey($key, $val)
    {
        $this->keyvalList[$key] = $val;
        if ($key == 'step_id' && $val == 4 && $this -> view == 'CheckoutView')
            $this -> view = 'OrderPlaced';
    }

    function getGETstring()
    {
        $retval = "";
        if(!empty($this->asc_action))
        {
            $retval = 'asc_action='.$this->asc_action;
            foreach($this->keyvalList as $key => $val)
            {
                $retval .= "&$key=$val";
            }
        }
        else
        {
            $retval = "";
            foreach($this->keyvalList as $key => $val)
            {
                if ($retval == "")
                {
                    $retval .= "$key=$val";
                }
                else
                {
                    $retval .= "&$key=$val";
                }
            }
        }
        return $retval;
    }

    function getKeyValList()
    {
        return $this -> keyvalList;
    }

    function getGETArray($include_action = false)
    {
        $retval = array();
        foreach($_GET as $k => $v)
            if ($include_action || !in_array($k, array('act', 'asc_action')))
                $retval[$k] = $v;

        return $retval;
    }

    function setKeyValList($list)
    {
        if (!is_array($list))
            $list = array();

        $this -> keyvalList = $list;
    }

    function prepareGetPostData()
    {
        $this->prepareArray($_POST);
        $this->prepareArray($_GET);
    }

    function prepareArray(&$array)
    {
		global $zone;

        $search = array('|</?\s*FRAME\s*.*?>|si',
                        '|</?\s*META\s*.*?>|si',
                        '|</?\s*APPLET\s*.*?>|si',
                        '|</?\s*LINK\s*.*?>|si');
                        /*'|</?\s*OBJECT\s*.*?>|si',*/
                        /*'|STYLE\s*=\s*"[^"]*"|si');*/

		if ($zone == 'CustomerZone') {
			array_push($search, '|</?\s*SCRIPT\s*.*?>|si', '|</?\s*IFRAME.*?>|si');
		}

        $replace = array('');
        $resarray = array();
        foreach ($array as $var => $ourvar) {

            if (!isset($ourvar)) {
                continue;
            }
            if (empty($ourvar)) {
                continue;
            }

            // Clean var
            if (get_magic_quotes_gpc()) {
                $this->arrayStripslashes($ourvar);
            }
//            $ourvar = preg_replace($search, "", $ourvar);
            $ourvar = $this->arrayReplaceTags($search,$ourvar);

            $array[$var] = $ourvar;
        }
    }

    function arrayStripslashes (&$value) {
        if(!is_array($value)) {
            $value = stripslashes($value);
        } else {
            array_walk($value,array('Request', 'arrayStripslashes'));
        }
    }

    function arrayReplaceTags($search, $value)
    {
        if(!is_array($value))
            return preg_replace($search, "", $value);
        else
        {
            foreach($value as $k => $v)
                $value[$k]=$this->arrayReplaceTags($search,$v);
            return $value;
        };
    }

    function getCurrentProtocol()
    {
        global $application;
        return $application->getCurrentProtocol();
    }

    function getSiteHTTPURL()
    {
        global $application;
        return $application->getAppIni('SITE_URL');
    }

    function getSiteHTTPSURL()
    {
        global $application;
        return $application->getAppIni('SITE_HTTPS_URL');
    }

    function getZone()
    {
        return modApiFunc('Users', 'getZone');
    }

    /**
     * Forms a URL from the object Request.
     * This method only must be used to generate any URL.
     *
     * @ method isn't completed.
     */
    function getURL($force_redirect_to = "", $do_not_encode_url = false, $froogle=null)
    {
        global $application;

        if( func_num_args() < 2 )
        {
            $do_not_encode_url = !$this->encodeURLs;
        }

        if($this->explicit_url !== NULL)
        {
            return $this->explicit_url . (($this -> anchor) ? '#' . $this -> anchor : '');
        }
        else
        {
            # - read from the configuration the WEB path to the system
            # - to define a filename by the representation name
            $zone = $this->getZone();

            if ($this->view == CURRENT_REQUEST_URL)
            {
                $current_url = ($this->getCurrentProtocol() == "https"? "https://":"http://").$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
                $url = $current_url;

                $part_of_script_name = "";
                if ($zone == 'CustomerZone')
                {
                    if (isset($application->appIni['SITE_HTTPS_URL']) &&
                        _ml_strpos($current_url, $application->appIni['SITE_HTTPS_URL']) === 0)
                        $part_of_script_name = _ml_substr($current_url, _ml_strlen($application->appIni['SITE_HTTPS_URL']));
                    elseif (_ml_strpos($current_url, $application->appIni['SITE_URL']) === 0)
                        $part_of_script_name = _ml_substr($current_url, _ml_strlen($application->appIni['SITE_URL']));

                    if ($force_redirect_to == "https")
                        $url = $this->getSiteHTTPSURL().$part_of_script_name;
                    elseif ($force_redirect_to == "http")
                        $url = $this->getSiteHTTPURL().$part_of_script_name;
                }
                else
                {
                    if (isset($application->appIni['SITE_AZ_HTTPS_URL']) && _ml_strpos($current_url, $application->appIni['SITE_AZ_HTTPS_URL']) === 0)
                        $part_of_script_name = _ml_substr($current_url, _ml_strlen($application->appIni['SITE_AZ_HTTPS_URL']));
                    elseif (_ml_strpos($current_url, $application->appIni['SITE_AZ_URL']) === 0)
                        $part_of_script_name = _ml_substr($current_url, _ml_strlen($application->appIni['SITE_AZ_URL']));

                    if ($force_redirect_to == "https")
                        $url = $application->getAppIni('SITE_AZ_HTTPS_URL').$part_of_script_name;
                    elseif ($force_redirect_to == "http")
                        $url = $application->getAppIni('SITE_AZ_URL').$part_of_script_name;
                }

                if (!empty($_POST))
                {
                    $this->setKey('timestamp', time());
                }

                $request_get_string = $this->getGETstring();

                $url = $url.(!empty($request_get_string) ? '?'.$request_get_string : "");
                if ($zone == 'CustomerZone' && !$do_not_encode_url)
                {
                    $this->URLMod->setURL($url);
                    $this->URLMod->encodeURL();
                    $url = $this->URLMod->getURL();
                }
                return $url . (($this -> anchor) ? '#' . $this -> anchor : '');
            }

            if ($zone == 'CustomerZone')
            {
                if ($force_redirect_to == "https")
                {
                    $url = $this->getSiteHTTPSURL();
                }
                elseif ($force_redirect_to == "http")
                {
                    $url = $this->getSiteHTTPURL();
                }
                else
                {
                    $section = $application->getSectionByViewName($this->view);
                    if ($application->getSectionProtocol($section) == 'https')
                    {
                        $url = $this->getSiteHTTPSURL();
                    }
                    else
                    {
                        $url = $this->getSiteHTTPURL();
                    }
                }
            }
            else
            {
                if ($force_redirect_to == "https" || $this->getCurrentProtocol() == "https")
                {
                    $url = $application->getAppIni('SITE_AZ_HTTPS_URL');
                }
                else
                {
                    $url = $application->getAppIni('SITE_AZ_URL');
                }
            }
            $url .= $application->getPagenameByViewname($this->view, $this->category_id, $this->product_id, $zone);

            if (in_array($this->view, array('NavigationBar',
                                            'Subcategories',
                                            'ProductList',
                                            'ProductInfo'))
                && (modApiFunc('MultiLang', 'getLanguage') !=
                    modApiFunc('MultiLang', 'getDefaultLanguage')))
                $this -> setKey('lng', modApiFunc('MultiLang', 'getLanguage'));

            $request_get_string = $this->getGETstring();

            $url .= (!empty($request_get_string) ? '?'.$request_get_string : "");

            /*
             Convertes the URL.
            */

            $encoded_url = $url;

            if($froogle!==null)
            {
                $ii = modApiFunc('Mod_Rewrite','_getIntegrityInfoForCZLayout',$application->getAppIni('PATH_LAYOUTS_CONFIG_FILE'));
                if(!empty($ii) && is_array($ii) && isset($ii["mr_active"]) && $ii["mr_active"]==="Y")
                {
                    modApiFunc('Mod_Rewrite','_forceSetActive', true);
                }
            }

            if($zone == 'CustomerZone' and modApiFunc('Mod_Rewrite','isModRewriteAvailable'))
            {
                $encoded_url = modApiFunc('Mod_Rewrite', 'encodeURL', $url);
                if ($encoded_url === $url && !$do_not_encode_url)
                {
                    $this->URLMod->setURL($url);
                    $this->URLMod->encodeURL();
                    $encoded_url = $this->URLMod->getURL();
                }
            }
            else
            {
                if ($zone == 'AdminZone' and isset($this->keyvalList['disable_url_mod']))
                {

                }
                else if (!$do_not_encode_url)
                {
                    $this->URLMod->setURL($url);
                    $this->URLMod->encodeURL();
                    $encoded_url = $this->URLMod->getURL();
                }
            };

            return $encoded_url . (($this -> anchor) ? '#' . $this -> anchor : '');
        }
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Shifts strings.
     * It is used in the $this->selfURL() method.
     */
    function strleft($s1, $s2)
    {
        return _ml_substr($s1, 0, _ml_strpos($s1, $s2));
    }

    var $view = NULL;
    var $asc_action = NULL;
    var $category_id = -1; //legacy default value
    var $product_id = -1; //legacy default value
    /**
     * If explicit_url was specified in constructor call,
     * then IT will be outputted by getURL() call.
     *
     * @var string
     */
    var $explicit_url = NULL;
    var $keyvalList = array();

    var $URLMod = NULL;

    var $anchor;

    var $encodeURLs; # whether or not the resulting URLs will have slashes instead of ampersands in query string

    /**#@-*/

}


?>