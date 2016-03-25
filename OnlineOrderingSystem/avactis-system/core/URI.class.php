<?php

/**
 * class that deals with URI details
 *
 * void process_uri(string url) - the method parces given URL,
 *       fills the missing parts of the URL and sets class field $url (array)
 *
 * string get_full_uri(void) - returns full URI and updates $this->url['full']
 *       use it instead of $object_name->url['full']
 *
 * function process() recieves the string containing absolute path to the URL
 * and writes the array, containing the elements of that URI,
 * to the class field $array
 * E.g. "https://urser:pass@example.com:80/path/file.ext?query=url_encoded" will be
 * presented in $this->uri as object:
 * class uri(){
 *     var $scheme = "https://";
 *     var $host = "example.com";
 *     var $user = "urser";
 *     var $pass = ":pass@";
 *     var $path = "/path/file.ext";
 *     var $query = "?query=url_encoded";
 *     var $port = 80;
 *     var $port_string = ":80";
 *     var $fragment = "";
 *     var $full = "http://urser:pass@example.com/path/file.ext?query=url_encoded";
 *              (caution: use ->get_full_uri() to get full URL)
 * }
 * while "example.com" will lead to
 * class uri(){
 *     var $scheme = "http://";
 *     var $host = "example.com";
 *     var $pass = "";
 *     var $user = "";
 *     var $port = 80;
 *     var $port_string = "";
 *     var $path = "/";
 *     var $query = "";
 *     var $fragment => "";
 *     var $full = "http://example.com/";
 *              (caution: use $object_name->get_full_uri() to get full URL)
 * }
 *
 * also you can use getPart() method for receiving a part of URI
 *
 * @package core
 * @author Egor V. Derevyankin
 *
 */

define ('QUERY_DELIMITER', '?');

class URI
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
    function URI($new_uri=null)
    {
        $new_uri and $this->process($new_uri);
    }

    /**
     * Process the URI
     *
     * @param strin $new_uri
     */
    function process($new_uri)
    {
        //log history
        $this->history[] = $new_uri;
        //init variables, results of parse_url() may redefine them
        $this->scheme =$this->host=$this->user =$this->pass =$this->path =$this->dir=$this->query=$this->fragment=$this->full='';
        $this->port = 80;
        $this->port_string = ':80';
        $this->array = array();
        if (_ml_strpos($new_uri, '://') === false)
        {
            $new_uri = 'http://'.$new_uri;
        }

        //parse current url - get parts
        $uri_array = @parse_url($new_uri);
        if (!$uri_array)
        {
            return false;
        }

        //set varables with parts of URI

        $uri_array['scheme'] = empty($uri_array['scheme'])?'http://': $uri_array['scheme'].'://';
        //user:password@
        if (!empty($uri_array['user']))
        {
            if (!empty($uri_array['pass']))
                $uri_array['pass'] = ':'.$uri_array['pass'].'@';
            else
            {
                $uri_array['user'] .= '@';
                $uri_array['pass'] = '';
            }
        }
        else
        {
            $uri_array['user'] = $uri_array['pass'] = '';
        }

        if (!empty($uri_array['port']))
        {
            $uri_array['port_string'] = ':'.$uri_array['port'];
        }
        else
        {
            $uri_array['port'] = $this->getDefaultPortByScheme($uri_array['scheme']);
            $uri_array['port_string'] = '';
        }

        if (empty($uri_array['path']) || !trim($uri_array['path']))
        {
            $uri_array['path'] = '/';
        }

        $uri_array['dir']=$this->dirname($uri_array['path']);
        $uri_array['query'] =empty($uri_array['query'])? '':'?'.$uri_array['query'];
        $uri_array['fragment'] = empty($uri_array['fragment'])?'': '#'.$uri_array['fragment'];

        //assign class fields
        foreach($uri_array as $key=>$value)
        {
            $this->$key = $value;
        }
        $this->get_full_uri();
        return true;
    }

    /**
     * function recieves the URI from the tag attribute and checks if it is relative
     * if so, function converts the URI to the absolute form
     * using data on current URI
     *
     * @param string $new_uri
     */
    function parse_http_redirect ($new_url)
    {
        //detect if URL is absolute
        if ($method_pos = _ml_strpos($new_url, '://'))
        {
            $method = _ml_substr($new_url, 0, $method_pos);
            if (!_ml_strcasecmp($method, 'http') || !_ml_strcasecmp($method,'https') || !_ml_strcasecmp($method, 'ftp'))
            {
                // absolute URL passed
                return $this->process($new_url);
            }
            else
            {
                //invalid protocol
                return false;
            }
        }

        // URL is relative
        //do we have GET data in the URL?
        $param_pos = _ml_strpos($new_url, QUERY_DELIMITER);
        if($param_pos !== false)
        {
            $new_query = _ml_substr($new_url, $param_pos);
            $new_path = $param_pos ? _ml_substr($new_url, 0, $param_pos) : '' ;
        }
        else
        {
            $new_path = $new_url;
            $new_query = '';
        }

        //is URL relative to the previous URL path?
        if ($new_url[0] != '/')
        {
            //attach relative link to the current URI's directory
            $new_path = $this->dirname($this->path).'/'. $new_path;
        }

       /*
         May be need in the future, but now it is not needed

        //replace back and repetitive slashes with a single forward one
        $new_path = preg_replace ('~((\\\\+)|/){2,}~', '/', $new_path);
        //parse links to the upper directories
        if (_ml_strpos($new_path, '/../') !== false)
        {
            $path_array = explode ('/', $new_path);
            foreach ($path_array as $key=>$value)
            {
                if ($value == '..')
                {
                    if ($key > 2)
                    {
                        unset ($path_array[$key-1]);
                    }
                    unset ($path_array[$key]);
                }
            }
            $new_path = implode ('/', $path_array);
        }
        //parse links to the 'current' directories
        $new_path = str_replace('/./', '/', $new_path);
       */

        $this->path = $new_path;
        $this->query = $new_query;
        $this->get_full_uri();

        return true;
    }

    /**
     * (re)compile the full uri and return the string
     *
     * @return full URI string
     */
    function get_full_uri()
    {
        $this->full = $this->scheme.$this->user.$this->pass.
            $this->host.$this->port_string.$this->path.$this->query;
        return $this->full;
    }

    function getPart($part_name)
    {
        $available_parts=array("scheme","host","user","pass","path","dir","query","port");
        if(!in_array($part_name,$available_parts))
            return "";
        else
            return $this->$part_name;
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**
     * Returns the directory part of the path (path may include query)
     *
     * @param string $path
     * @return string
     */
    function dirname($path)
    {
        if(!$path)
        {
            return false;
        }
        $i=_ml_strpos($path,'?');
        $dir=$i?_ml_substr($path,0,$i):$path;

        $i=_ml_strrpos($dir,'/');
        $dir=$i?_ml_substr($dir,0,$i):'/';
        $dir[0]=='/' || $dir='/'.$dir;
        return $dir;
    }

    function getDefaultPortByScheme($scheme)
    {
        $scheme = preg_replace('/[^a-z]*/i','',_ml_strtolower($scheme));
        switch($scheme)
        {
            case 'http':
                return 80;
                break;

            case 'https':
                return 443;
                break;

            default:
                return 80;
                break;
        }
    }

    var $array;
    var $scheme;
    var $host;
    var $user;
    var $pass;
    var $path;
    var $dir;
    var $query;
    var $port;
    var $port_string;
    var $fragment;
    var $full;
    var $history = array();

}
?>