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
 * Class Bouncer provides interface for make HTTP/S requestes to remote servers.
 *
 * @package Core
 * @access  public
 * @author Egor V. Derevyankin
 */
class Bouncer
{

//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function Bouncer()
    {
        $this->detect_OS();
        $this->bouncer_dir=dirname(__FILE__).$this->_dir_sep;

        $this->_proxy = array(
                "http" => array("host"=>null,"port"=>null,"user"=>null,"pass"=>null)
               ,"https" => array("host"=>null,"port"=>null,"user"=>null,"pass"=>null));
        $this->fillProxySets();

        #detect ssl-modules & init request
        $this->detectSSLmodules();
        $this->setCurrentSSLmodule();
        $this->InitRequest();

        global $application;

        if ($application->getAppIni('DEFAULT_HTTP_PROTOCOL_VERSION') == null)
            $this->_http_version = "1.1";
        else
            $this->_http_version = $application->getAppIni('DEFAULT_HTTP_PROTOCOL_VERSION');

        if ($application->getAppIni('DEFAULT_TIMEOUT') == null)
            $this->_timeout = 5;
        else
            $this->_timeout = $application->getAppIni('DEFAULT_TIMEOUT');

        $this->additional_headers=array();
        $this->post_type="application/x-www-form-urlencoded";
    }

    /**
     * Detects available SSL modules and puts them into $SSL_modules array.
     */
    function detectSSLmodules()
    {
        # clear Modules array
        $this->SSL_modules=array();

        # detect `libcurl`
        if (!$this->detect_libcurl())
        {
            # detect `CURL-executable`
            if (!$this->detect_CURL())
            {
                # detect `OpenSSL-executable`
                $this->detect_OpenSSL();
            }
        }

        return;
    }

    function detect_libcurl()
    {
        if(function_exists("curl_version"))
        {
            $version=curl_version();
            if(is_array($version))
                $version=$version["version"];
            $this->SSL_modules["libcurl"]["version"]=$version;
            return true;
        };
        return false;
    }

    function detect_CURL()
    {
        $curl_exec=$this->search_exec("curl");
        if($curl_exec!=false)
        {
            $this->SSL_modules["curl"]["exe_path"]=$curl_exec;
            $exec_line=$curl_exec." --version";
            $handle = @popen($exec_line,"r");
            $output=@fgets($handle,1024);
            @pclose($handle);
            $version=$output;
            $this->SSL_modules["curl"]["version"]=$version;
            return true;
        };
        return false;
    }

    function detect_OpenSSL()
    {
        # OpenSSL-cli doesn't work under windows systems
        if($this->_os_type == "win")
            return false;

        # OpenSSL-cli doesn't support proxy-connections (until...)
        if($this->_proxy["https"]["host"]!=null)
            return false;

        $ossl_exec=$this->search_exec("openssl");
        if($ossl_exec!=false)
        {
            $this->SSL_modules["openssl"]["exe_path"]=$ossl_exec;
            $exec_line=$ossl_exec." version";
            $handle = @popen($exec_line,"r");
            $output=@fgets($handle,1024);
            @pclose($handle);
            $version=$output;
            $this->SSL_modules["openssl"]["version"]=$version;
            return true;
        };
        return false;
    }

    function search_exec($app)
    {
        $_psep = ($this->_os_type=="win"?";":":");
        if($this->_os_type=="win")
            $app.=".exe";
        $paths = explode($_psep, getenv("PATH"));
        foreach($paths as $k => $path)
        {
            $fexe=$path.$this->_dir_sep.$app;
            if(@function_exists("is_executable"))
            {
                if(@is_executable($fexe))
                    return $fexe;
            }
            else
            {
                if(@is_readable($fexe))
                    return $fexe;
            }
        };
        return false;
    }

    /**
     * @return TRUE if everyone SSL module is available,
     *      or FALSE if SSL modules are not available
     */
    function isSSLavailable()
    {
        return (count($this->SSL_modules)>0?true:false);
    }

    /**
     * Sets the current SSL module if it is available.
     *
     * @param $mod_name name of the SSL module
     * @return nothing
     */
    function setCurrentSSLmodule($mod_name="libcurl")
    {
        if($this->isSSLavailable())
        {
            if(isset($this->SSL_modules[$mod_name]["version"]))
                $this->currentSSLmodule=$mod_name;
            else
            {
                $ssl_names=array_keys($this->SSL_modules);
                $this->currentSSLmodule=$ssl_names[0];
            };
        };
    }

    /**
     * @return the name of the current SSL module
     */
    function getCurrentSSLmodule()
    {
        if($this->isSSLavailable())
            return $this->currentSSLmodule;
        else
            return false;
    }

    function fillProxySets()
    {
        global $application;
        foreach(array('http','https') as $proto)
        {
            $ini_host = $application->getAppIni(_ml_strtoupper($proto).'_PROXY_HOST');
            if($ini_host != null and $ini_host != "")
            {
                list($h,$p) = explode(':',$ini_host);
                if($h!="")
                    $this->_proxy[$proto]["host"] = $h;
                else
                    continue;

                if($p!="")
                    $this->_proxy[$proto]["port"] = intval($p);
                else
                    $this->_proxy[$proto]["port"] = 3128; //: default proxy port, move to config.def.php

                $ini_user = $application->getAppIni(_ml_strtoupper($proto).'_PROXY_USER');
                if($ini_user != null and $ini_user != "")
                {
                    $this->_proxy[$proto]["user"] = $application->getAppIni(_ml_strtoupper($proto).'_PROXY_USER');

                    $ini_pass = $application->getAppIni(_ml_strtoupper($proto).'_PROXY_PASS');
                    if($ini_pass != null)
                    {
                        $this->_proxy[$proto]["pass"] = $application->getAppIni(_ml_strtoupper($proto).'_PROXY_PASS');
                    };
                };
            }
        };
    }

    function forceNoProxy()
    {
        $this->_proxy = array(
                "http" => array("host"=>null,"port"=>null,"user"=>null,"pass"=>null)
               ,"https" => array("host"=>null,"port"=>null,"user"=>null,"pass"=>null));
    }

    /**
     * New request initialization.
     */
    function InitRequest()
    {
        $this->proto="HTTP";
        $this->host="";
        $this->url="";
        $this->method="GET";
        $this->cookies=array();
        $this->post_string="";
        $this->get_string="";
        $this->ssl_cert="";
        $this->ssl_key="";
        $this->_error_message="";
    }

    /**
     * experimental
     */
    function setPconnect($flag)
    {
        if(!is_bool($flag))
            return false;
        if($flag==true)
            $this->_pconnect=true;
        elseif($flag==false)
        {
            if($this->persistent_connection!=false)
            {
                $this->closeConnection($this->persistent_connection);
                $this->persistent_connection=false;
            };
            $this->_pconnect=false;
        }
    }

    function setAdditionalHeaders($headers)
    {
        $this->additional_headers=$headers;
    }

    /**
     * Sets the request protocol.
     *
     * @deprecated 1.5.4 - 02.04.2007
     * @param $proto is 'HTTP' or 'HTTPS'
     */
    function setProto($proto="HTTP")
    {
        if($proto!="HTTPS")
        {
            $this->proto="HTTP";
            $this->port=80;
        }
        else
        {
            $this->proto="HTTPS";
            $this->port=443;
        };

    }

    /*
     *                                                             .
     */
    function setSocketReadTimeout($val)
    {
        $this->_timeout = $val;
    }

    function setHTTPversion($version)
    {
        if(!in_array($version,array("1.0","1.1")))
            return;

        $this->_http_version = $version;
    }


    /**
     * Sets the request URL.
     *
     * @param $url URL
     */
    function setURL($url)
    {
        loadCoreFile('URI.class.php');
        $_uri = new URI($url);
        $this->proto = _ml_strtoupper(str_replace('://','',$_uri->getPart('scheme')));
        $this->host = $_uri->getPart("host");
        $this->port = $_uri->getPart("port");
        $this->url = $this->__urlencode($_uri->getPart("path")).$_uri->getPart("query");
    }

    /**
     * Sets the request method.
     *
     * @param $method is 'GET' or 'POST'
     */
    function setMethod($method="GET")
    {
        if($method!="POST")
            $this->method="GET";
        else
            $this->method="POST";
    }

    /**
     * Converts the array to the cookies string.
     *
     * @param $cookies_array array for converting
     */
    function prepareCookiesString($cookies_array)
    {
        $cookies_string="";
        if(is_array($cookies_array) and !empty($cookies_array))
        {
            $this->cookies=$cookies_array;
            $pre_array=array();
            foreach($cookies_array as $variable => $value)
                $pre_array[]="$variable=".urlencode($value);
            $cookies_string=implode("; ",$pre_array);
        }
        return $cookies_string;
    }

    /**
     * Sets request cookies.
     *
     * @param $cookies_array array of cookies for set in the request
     */
    function setCookies($cookies_array)
    {
        if(is_array($cookies_array) and !empty($cookies_array))
            $this->cookies=array_merge($this->cookies,$cookies_array);
    }

    /**
     * Unsets request cookies.
     *
     * @param $unset_array array of cookies for unset
     */
    function unsetCookies($unset_array)
    {
        if(is_array($unset_array) and !empty($unset_array))
        {
            foreach(array_keys($unset_array) as $key => $varname)
                unset($this->cookies[$varname]);

        };
    }

    /**
     * Converts the array to the url-encoded string.
     *
     * @param $data_array array for converting
     */
    function prepareDATAstring($data_array)
    {
        $data_string="";
        if(is_array($data_array) and !empty($data_array))
        {
            $data_strings=array();

            foreach($data_array as $variable => $value)
                $data_strings[]="$variable=".urlencode($value);

            $data_string=implode("&",$data_strings);
        }
        return $data_string;
    }

    /**
     * Sets the POST-data string for request.
     *
     * @param $post_string POST-data string
     */
    function setPOSTstring($post_string)
    {
        if($post_string!="")
            $this->post_string=$post_string;
    }

    function setPOSTtype($type)
    {
        $this->post_type=$type;
    }

    /**
     * Sets the GET-data string for request.
     *
     * @param $get_string GET-data string
     */
    function setGETstring($get_string)
    {
        if($get_string!="")
            $this->get_string=$get_string;
    }

    /**
     * Sets the SSL-certificate.
     *
     * @param $cert the name of a file containing a PEM formatted certificate
     */
    function setSSLcert($cert)
    {
        $cert=realpath($cert);
        if($cert==false or !is_file($cert) or !is_readable($cert))
            return false;

        $this->ssl_cert=$cert;
        return true;
    }

    /**
     * Sets the SSL-key.
     *
     * @param $key the name of a file containing a private SSL key
     */
    function setSSLkey($key)
    {
        $key=realpath($key);
        if($key==false or !is_file($key) or !is_readable($key))
            return false;

        $this->ssl_key=$key;
        return true;
    }

    /**
     * Makes the HTTP-request text.
     *
     * @return HTTP-request text
     */
    function prepareHTTPRequest()
    {
        $request=array();

        # first string
        $request[]=$this->method." ".$this->url.($this->get_string!=""?"?".$this->get_string:"")." HTTP/".$this->_http_version;

        #headers
        $request[]="Host: ".$this->host.":".$this->port;

        $request[]="User-Agent: Mozilla/4.5 [en]";

        #proxy-auth
        if($this->_proxy[_ml_strtolower($this->proto)]["user"]!=null)
        {
            $u = $this->_proxy[_ml_strtolower($this->proto)]["user"];
            $p = $this->_proxy[_ml_strtolower($this->proto)]["pass"];
            if($p === null)
                $p = "";

            $request[] = "Proxy-Authorization: Basic ".base64_encode($u.':',$p);
        }

        #cookies
        if(!empty($this->cookies))
            $request[]="Cookie: ".$this->prepareCookiesString($this->cookies);

        #post data headers
        if($this->method=="POST")
        {
            $request[]="Content-Type: ".$this->post_type;
            $request[]="Content-Length: "._byte_strlen($this->post_string);
        };

        if(!empty($this->additional_headers))
            foreach($this->additional_headers as $header => $value)
                $request[]="$header: $value";

        $request[]="";
        if($this->method=="POST" and $this->post_string!="")
        {
            $request[]=$this->post_string;
            $request[]="";
        };

        $request[]="";

        return implode("\r\n",$request);

    }

    /**
     * Makes a connection to remote host.
     *
     * @return connection handle on succes or FALSE on failure
     */
    function prepareConnection()
    {
        if($this->proto=="HTTPS")
            return $this->prepareSSLconnection();
        else
            return $this->prepareHTTPconnection();
    }

    /**
     * Makes an unsecure connection to remote host.
     *
     * @return connection handle on succes or FALSE on failure
     */
    function prepareHTTPconnection()
    {
        if($this->_proxy["http"]["host"]==null)
            $connection=@fsockopen($this->host,$this->port,$errno,$errstr,$this->_timeout);
        else
            $connection=@fsockopen($this->_proxy["http"]["host"],$this->_proxy["http"]["port"],$errno,$errstr,$this->_timeout);

        if($connection == false)
        {
            $this->_error_message="Error of open HTTP connection. #$errno: $errstr";
            return false;
        }
        else
            return $connection;
    }

    /**
     * Makes a secure connection to remote host.
     *
     * @return connection handle on succes or FALSE on failure
     */
    function prepareSSLconnection()
    {
        if(!$this->isSSLavailable())
            return false;

        $func_name=$this->currentSSLmodule."_prepare";
        $connection=$this->$func_name();

        if($connection==false)
            $this->_error_message="Error of open HTTPS connection. #".$this->errno.": ".$this->errstr;

        return $connection;
    }

    /**
     * Sends a request to the remote host.
     *
     * @return answer from the remote host
     */
    function processRequest($connection)
    {
        if($this->proto=="HTTPS")
            return $this->processSSLrequest($connection);
        else
            return $this->processHTTPrequest($connection);

    }

    /**
     * Sends a request to the remote host by unsecure HTTP protocol.
     *
     * @return answer from the remote host
     */
    function processHTTPrequest($connection)
    {
        $request_text=$this->prepareHTTPRequest();

        fwrite($connection,$request_text);
        stream_set_timeout($connection, $this->_timeout);
        $result="";

        while(!feof($connection))
        {
            $result.=fread($connection,65536);
            $cni=stream_get_meta_data($connection);
            if($cni["timed_out"])
            {
                $this->_error_message="Error of process HTTP request. #0: Connection timed out";
                $result="";
                break;
            };
        };

        return $result;
    }

    /**
     * Sends a request to the remote host by secure HTTP protocol.
     *
     * @return answer from the remote host
     */
    function processSSLrequest($connection)
    {
        $func_name=$this->currentSSLmodule."_process";
        $result=$this->$func_name($connection);

        if($this->errno)
            $this->_error_message="Error of process SSL request. #".$this->errno.": ".$this->errstr;

        return $result;
    }

    /**
     * Closes the connection to the remote host.
     */
    function closeConnection($connection)
    {
        if($this->proto=="HTTPS")
            $this->closeSSLconnection($connection);
        else
            $this->closeHTTPconnection($connection);

        return;
    }

    /**
     * Closes the unsecure connection to the remote host.
     */
    function closeHTTPconnection($connection)
    {
        fclose($connection);

        return;
    }

    /**
     * Closes the secure connection to the remote host.
     */
    function closeSSLconnection($connection)
    {
        $func_name=$this->currentSSLmodule."_close_connection";
        $this->$func_name($connection);

        return;
    }

    /**
     * Executes the request.
     *
     * @return FALSE on failure or answer from the remote host
     *  as array of headers, cookies and body
     */
    function RunRequest()
    {
        CTrace::dbg('Bouncer:', $this);
    	$bouncer_connection=$this->prepareConnection();

        if($bouncer_connection == false)
        {
        	CTrace::wrn('Failed to make connection.');
            return false;
        }

        $request_result=$this->processRequest($bouncer_connection);
		CTrace::dbg('Raw response:', $request_result);

        $this->closeConnection($bouncer_connection);

        if($request_result=="")
        {
        	CTrace::wrn('Responce is empty.');
            return false;
        }

        $result = $this->parseRequestResult($request_result);
        CTrace::dbg('Parsed response:', $result);
        return $result;
    }

    /**
     * Converts the answer from the remote host to the array of headers, cookies
     * and body.
     *
     * @param $result answer from the remote host
     * @return array of headers, cookies and body
     */
    function parseRequestResult($result)
    {
        $headers=array();
        $cookies=array();
        $body="";

        $dp = 4096;
        $delimiter = false;
        $dtrs = array("\n", "\r\n", "\n\r");
        foreach ($dtrs as $v)
        {
            // trying to find a delimiter pair - headers/body border
            $tdp = _byte_strpos($result, $v.$v);
            if ($tdp != false && $dp > $tdp)
            {
                $dp = $tdp;
                $delimiter = $v;
            }
        }

        if ($delimiter == false)
        {
        	CTrace::wrn('Failed to parse response, I cannot guess headers/body delimiter.');
        	return;
        }

        $headers = _byte_substr($result, 0, $dp);

        $hstr = explode($delimiter, $headers);

        foreach($hstr as $key => $string)
        {
            if(preg_match("/^HTTP/", $string))
                continue;
            if(trim($string) == "")
                break;

            $header_array = explode(": ", trim($string), 2);
            $header_array[0] = _ml_strtoupper($header_array[0]);
            $headers[$header_array[0]] = chop($header_array[1]);

            if($header_array[0]=="SET-COOKIE")
                array_push($cookies, $header_array[1]);
        }

        $cookies = $this->parseCookies($cookies);
        $body = _byte_substr($result, $dp + _byte_strlen($delimiter)*2);

        return array("headers" => $headers, "cookies" => $cookies, "body" => $body);
    }

    /**
     * Converts the array of the cookies strings to the array with the type
     * 'cookie-name' => 'cookie-value'.
     *
     * @param $cookies_array array of the cookies strings
     * @return array of the two arrays, first with the deleted cookies, second with the new cookies
     */
    function parseCookies($cookies_array)
    {
        $deleted = array();
        $valid = array();
        foreach ($cookies_array as $line)
        {
            if (preg_match_all('!^\s*([^\n\r=]+)=([^\r\n; ]+)?!S', $line, $m))
            {
                if (!empty($m[1]) && is_array($m[1]))
                {
                    foreach ($m[1] as $k=>$v)
                    {
                        if ($m[2][$k] == 'deleted')
                        {
                            $deleted[$v] = true;
                            if (isset($valid[$v])) unset($valid[$v]);
                        }
                        else
                        {
                            $valid[$v] = $m[2][$k];
                            if (isset($deleted[$v])) unset($deleted[$v]);
                        }
                    }
                }
            }
        }

        return array("deleted" => $deleted, "valid"=>$valid);

    }

    /**
     * @return the message of the last error
     */
    function getLastErrorMessage()
    {
           return $this->_error_message;
    }

    function detect_OS()
    {
        if(preg_match("/^win/i",php_uname('s')))
        {
            $this->_os_type="win";
            $this->_dir_sep="\\";
        }
        else
        {
            $this->_os_type="nix";
            $this->_dir_sep="/";
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
     * Prepares the SSL-connection by `libcurl`.
     *
     * @return SSL-connection handler
     */
    function libcurl_prepare()
    {
        $connection=curl_init();

        $this->errno = curl_errno($connection);
        $this->errstr = curl_error($connection);

        if($this->errno!=0)
            $connection=false;

        return $connection;

    }

    /**
     * Processes the SSL-request by `libcurl`.
     *
     * @param $connection SSL-connection handler, which was opened by `libcurl`
     * @return response from the remote server
     */
    function libcurl_process($connection)
    {
        $full_url="https://".$this->host.":".$this->port.$this->url.($this->get_string!=""?"?".$this->get_string:"");

        $headers=array();

        if($this->method=="POST")
        {
            $headers[]="Content-Type: ".$this->post_type;
        }

        $version = $this->SSL_modules["libcurl"]["version"];

        $supports_insecure = false;
        # insecure key is supported by curl since version 7.10
        if ($pos = _ml_strpos($version, "libcurl/") === 0)
        {
            $version = _ml_substr($version, _ml_strlen("libcurl/"));
        }
        $version = trim(strtr($version, array("libcurl" => "")));
        if( preg_match("/([^ $]+)/", $version, $m) )
        {
            $parts = explode(".",$m[1]);
            if( $parts[0] > 7 || ($parts[0] = 7 && $parts[1] >= 10) )
                $supports_insecure = true;
        }

        curl_setopt($connection, CURLOPT_URL, $full_url);
        curl_setopt($connection, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($connection, CURLOPT_HEADER, 1);

        #proxy
        if($this->_proxy["https"]["host"]!=null)
        {
            curl_setopt($connection, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($connection, CURLOPT_PROXY, $this->_proxy["https"]["host"].':'.$this->_proxy["https"]["port"]);

            $u = $this->_proxy["https"]["user"];
            $p = $this->_proxy["https"]["pass"];
            if($u!=null)
            {
                if($p===null) $p = "";
                curl_setopt($connection, CURLOPT_PROXYUSERPWD, $u.':'.$p);
            }
        }

         if(!empty($this->additional_headers))
            foreach($this->additional_headers as $header => $value)
                $headers[]="$header: $value";

        if(!empty($headers))
            curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

        if($this->ssl_cert!="")
        {
            curl_setopt($connection, CURLOPT_SSLCERT, $this->ssl_cert);
            if($this->ssl_key!="")
                curl_setopt($connection, CURLOPT_SSLKEY, $this->ssl_key);
        }

        if (!empty($this->cookies))
        {
            curl_setopt($connection, CURLOPT_COOKIE, $this->prepareCookiesString($this->cookies));
        }

        curl_setopt($connection, CURLOPT_TIMEOUT, $this->_timeout);

        if ($supports_insecure)
        {
            curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 1);
        }

        if( $this->method == "POST" )
        {
            curl_setopt($connection, CURLOPT_POST, 1);
            curl_setopt($connection, CURLOPT_POSTFIELDS, $this->post_string);
        }
        else
        {
            curl_setopt($connection, CURLOPT_POST, 0);
        }

        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($connection);
        $this->errno = curl_errno($connection);
        $this->errstr = curl_error($connection);

	$this->responseCode=curl_getinfo($connection,CURLINFO_HTTP_CODE);
        return $result;
    }

    /**
     * Closes the SSL-connection, which was opened by `libcurl`.
     *
     * @param $connection SSL-connection handler
     */
    function libcurl_close_connection($connection)
    {
        curl_close($connection);
    }

    /**
     * Prepares the SSL-connection by `curl-executable`.
     *
     * @return SSL-connection handler (always true, because a connection will
     * open during process request)
     */
    function curl_prepare()
    {
        return true;
    }

    /**
     * Processes the SSL-request by `curl-executable`.
     *
     * @param $connection SSL-connection handler, which was opened by `curl-executable`
     * @return response from the remote server
     */
    function curl_process($connection)
    {
        $full_url="https://".$this->host.":".$this->port.$this->url.($this->get_string!=""?"?".$this->get_string:"");

        $curl_exec = $this->SSL_modules["curl"]["exe_path"];

        $version = $this->SSL_modules["curl"]["version"];

        $supports_insecure = false;
        # insecure key is supported by curl since version 7.10
        if (_ml_strpos($version, "curl") === 0)
        {
            $version = trim(_ml_substr($version, _ml_strlen("curl")));
        }
        if ($version[0] == "/")
        {
            $version = trim(_ml_substr($version, 1));
        }
        if( preg_match("/([^ $]+)/", $version, $m) )
        {
            $parts = explode(".",$m[1]);
            if( $parts[0] > 7 || ($parts[0] = 7 && $parts[1] >= 10) )
                $supports_insecure = true;
        }

        $exec_line=" --http1.0 -D-";

        if($this->method=="GET")
            $exec_line.=" --get";

        $exec_line.=" --connect-timeout ".$this->_timeout." --max-time ".$this->_timeout;

        #proxy
        if($this->_proxy["https"]["host"]!=null)
        {
            $exec_line.=" --proxytunnel --proxy ".$this->_proxy["https"]["host"].':'.$this->_proxy["https"]["port"];

            $u = $this->_proxy["https"]["user"];
            $p = $this->_proxy["https"]["pass"];
            if($u!==null)
            {
                if($p===null) $p = "";
                $exec_line.=" --proxy-basic --proxy-user ".$u.':'.$p;
            };
        }

        if($this->method=="POST")
            $exec_line.=" --data ".urlencode($this->post_string);

        if($this->ssl_cert!="")
        {
            $exec_line.=" --cert ".$this->ssl_cert;
            if($this->ssl_key!="")
                $exec_line.=" --key ".$this->ssl_key;
        }

        if($supports_insecure)
            $exec_line.=" --insecure";

        if(!empty($this->cookies))
            $exec_line.=" --cookie ".$this->prepareCookiesString($this->cookies);

        $cmd_line=$curl_exec.$exec_line." ".$full_url;

        $fp=@popen($cmd_line,"r");

        if($fp==false)
        {
            $this->errno=1;
            $this->errstr="Can't execute CURL";
        }
        else
        {
            $result="";
            while(!feof($fp))
                $result.=fread($fp,65536);
            pclose($fp);
        };

        return $result;
    }

    /**
     * Closes hte SSL-connection, which was opened by `curl-executable`.
     *
     * @param $connection SSL-connection handler
     */
    function curl_close_connection($connection)
    {
        return;
    }

    /**
     * Prepares the SSL-connection by `openssl-executable`.
     *
     * @return SSL-connection handler (always true, because a connection will
     * open during process request)
     */
    function openssl_prepare()
    {
        return true;
    }

    /**
     * Processes the SSL-request by `openssl-executable`
     *
     * @param $connection SSL-connection handler, which was opened by
     * `openssl-executable`.
     *
     * @return response from the remote server
     */
    function openssl_process($connection)
    {
        $openssl_exec = $this->SSL_modules["openssl"]["exe_path"];
        $exec_args = "-connect ".$this->host.":".$this->port;
        if($this->ssl_cert!="")
        {
            $exec_args.=" -cert ".$this->ssl_cert;
            if($this->ssl_key!="")
                $exec_args.=" -key ".$this->ssl_key;
        }
        $request=$this->prepareHTTPrequest();

        $tmp_fname=_ml_strrev(md5(time().uniqid(mt_rand(),true)));
        $ign_fp=$this->bouncer_dir.$tmp_fname;
        $req_fp=$this->bouncer_dir._ml_strrev($tmp_fname);

        if(($th=@fopen($req_fp,"w"))==false)
        {
            $this->errno=4;
            $this->errstr="OpenSSL: Can't create temporary file";
        }
        else
        {
            fwrite($th,$request);
            fclose($th);

            $cmd_line = $openssl_exec." s_client ".$exec_args." -quiet < ".$req_fp." 2> ".$ign_fp;

            $fp=@popen($cmd_line,"r");

            if($fp==false)
            {
                $this->errno=2;
                $this->errstr="OpenSSL: can't execute application";
            }
            else
            {
                stream_set_timeout($fp,$this->_timeout);
                $result="";

                while(!feof($fp))
                {
                    $result.=fread($fp,65536);
                    $fpi=stream_get_meta_data($fp);

                    if($fpi["timed_out"])
                    {
                        $this->errno=3;
                        $this->errstr="OpenSSL: response receiving timed out";
                        $result="";
                        break;
                    };
                };

                pclose($fp);

                @unlink($req_fp);
                @unlink($ign_fp);
            };
        };
        return $result;
    }

    /**
     * Closes the SSL-connection, which was opened by `openssl-executable`.
     *
     * @param $connection SSL-connection handler
     */
    function openssl_close_connection($connection)
    {
        return;
    }

    function __urlencode($url_path)
    {
        return implode('/',array_map('rawurlencode', explode('/',$url_path)));
    }

    var $bouncer_dir;

    var $SSL_modules;
    var $currentSSLmodule;

    var $proto;
    var $host;
    var $port;
    var $url;
    var $method;
    var $cookies;
    var $post_string;
    var $get_string;

    var $additional_headers;
    var $post_type;

    var $ssl_cert;
    var $ssl_key;

    var $_timeout;

    var $errno;
    var $errstr;
    var $_error_message;

    var $_os_type;
    var $_dir_sep;

    var $_http_version;

    var $_proxy;
   /**#@-*/

};

?>