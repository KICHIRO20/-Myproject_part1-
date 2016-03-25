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
 * @copyright Copyright &copy; 2013, HBWSL
 * @package Core
 * @author ag
 */

/** Absolute path to the store directory. */
if ( !defined('ABSPATH') )
       define('ABSPATH', dirname(dirname(dirname(__FILE__))) . '/');

if ( !defined('ASC_CORE') )
       define('ASC_CORE', 'avactis-system/core');

define('TEMPLATE_FILE_SIMPLE', 1); //   a plain template file
define('TEMPLATE_FILE_PRODUCT_TYPE', 2); // a template file, depending on the product type
define('TEMPLATE_OPTION_REQUIRED', 3); // a required template option
define('TEMPLATE_OPTION_OPTIONAL', 4); // an optional template option

define('PRICE_N_A', -1000000007);

define('FILTERED_IMPLODE_RIGHT',10001);
define('FILTERED_IMPLODE_LEFT',10002);
define('FILTERED_IMPLODE_BOTH',10003);
define('FILTERED_IMPLODE_NOWHERE',10004);

define('MAX_STRDUMP_LENGTH', 1000);

/**
 * Read recursively stated directory and include all files
 * with specified extension form it.
 *
 * @ Check file errors, we have to use IO class
 * @access  private
 * @param string $directory The path to directory which will be read
 * @param string $file_extension The file extension which will be included
 * @return array List of included files
 */
function DirRead( $directory, $file_extension )
{
    $insertions_list = array();

    if ( $dir = @dir($directory) )
    {
        while ( $file = $dir->read() )
        {
            if ( !is_dir($directory . $file) )
            {
                if ( _ml_substr($file, _ml_strrpos($file, '.')) == $file_extension )
                {
                    _use($directory.$file);
                    array_push( $insertions_list, _ml_substr($file, 0, _ml_strrpos($file, '.')) );
                }
            }
            elseif ( $file != '..' && $file != '.' )
            {
                $insertions_list = array_merge( $insertions_list, DirRead($directory . $file. '/', $file_extension) );
            }
        }
        $dir->close();
    }

    return $insertions_list;
}

function __error_handler__($errno, $errmsg, $filename, $linenum)
{
    // automatically remove broken combined php file
    global $include_combined_php;
    if ($errno & (E_ERROR|E_PARSE|E_CORE_ERROR|E_COMPILE_ERROR|E_USER_ERROR) && isset($include_combined_php)) {
        if (is_readable($include_combined_php)) {
            unlink($include_combined_php);
        }
        unset($include_combined_php);
    }

	// process @ operator
	if (error_reporting() == 0) return;

	$errortype = array (
                E_ERROR              => 'Error',
                E_WARNING            => 'Warning',
                E_PARSE              => 'Parsing Error',
                E_NOTICE             => 'Notice',
                E_CORE_ERROR         => 'Core Error',
                E_CORE_WARNING       => 'Core Warning',
                E_COMPILE_ERROR      => 'Compile Error',
                E_COMPILE_WARNING    => 'Compile Warning',
                E_USER_ERROR         => 'User Error',
                E_USER_WARNING       => 'User Warning',
                E_USER_NOTICE        => 'User Notice',
                E_STRICT             => 'Runtime Notice',
                E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
    );

    if (defined('E_DEPRECATED'))
    {
    	$errortype[E_DEPRECATED] = 'Deprecated';
    }

    $e_type = $errortype[$errno];
    $msg = "PHP $e_type: $errmsg";
    /*
     *                            :                                                                                                                                         trace()                                                   
     *                 err(),                                     CTrace                                                                                                            debug_backtrace    
     *                                                                                                                                               .
     *                                                                                                                __error_handler__                                                       err(),       
     *                                                                                                                                                        .                                                                        
     *                                   ,                                                           trace().
     */
    CTrace::trace($msg, CTrace::ERR);
}


function __shutdown__()
{
    global $application, $zone;

	if(class_exists('CProfiler'))
	{
	    CProfiler::stop('API & Render');
	    if (CProfiler::isEnabled())
			CTrace::dbg(CProfiler::getProfiler());
	   	CProfiler::writeCSV();
	}
	if(class_exists('CCacheFactory'))
	    CCacheFactory::shutdown();

    if (function_exists('error_get_last'))
    {
        $error = error_get_last();
        if (is_array($error) && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR)))
        {
			_fatal($error);
//			CTrace::err($error);
//			if(class_exists('EventsManager'))
//				modApiFunc('EventsManager','throwEvent','ApplicationShutdown');
        }
    }
}

function __info_tag_output_find_tag_params($entity, $arg_list = NULL)
{
	global $application;

    switch($entity)
	{
		case 'manufacturer':
		{
			$mnf_id = PARAM_NOT_FOUND;
            if($arg_list === NULL ||
               empty($arg_list[0]))
            {
            	$params = array(TAG_PARAM_MNF_ID, TAG_PARAM_PROD_ID);
                $param = modApiFunc("tag_param_stack", "find_first_param_by_priority", $params);
                if($param !== PARAM_NOT_FOUND)
                {
                    if($param['key'] == TAG_PARAM_PROD_ID)
                    {
                        //         mnf_id
                        $prod_info = &$application->getInstance('CProductInfo', $param['value']);
                        $mnf_id = $prod_info->getProductTagValue('Manufacturer', PRODUCTINFO_NOT_LOCALIZED_DATA);
                    }
                    elseif($param['key'] == TAG_PARAM_MNF_ID)
                    {
                        $mnf_id = $param['value'];
                    }
                }
            }
            else
            {
                $mnf_id = $arg_list[0];
            }
            return $mnf_id;
		}
		default:
		{
			return PARAM_NOT_FOUND;
		}
	}
}

function __info_tag_output($tag, $arg_list)
{
    global $application, $zone;
        #                       .
        $view = $application->getLatestTag();
        list($entity, $attr) = getTagName($tag);

        /*
         *         empty($arg_list)               ,                                 msg
         *                      .
         */
        if ( $view != null && is_callable(array($view, 'getTag')) && empty($arg_list))
        {
            if(strtolower($tag) == 'viewclassname')
            {
                $alias = $application->getBlockOverride($view);
                echo strtolower(($alias==null) ? $view : $alias);
                return;
            }
            $view_obj = &$application->getInstance($view);
            $output = $view_obj->getTag($tag, $arg_list);
            if ($output !== null)
            {
                echo $output;
                return;
            }
        }
        if (empty($arg_list))
        {
            #                        .                   ,                  .
            $params = $application->getTemplateParameters();
            $product_id = $params['product_id'];
            $category_id = $params['category_id'];
            $page_id = @$params['page_id'];
        }
        else
        {
            $product_id = $arg_list[0];
            $category_id = $arg_list[0];
            $page_id = $arg_list[0];
        }

        $output = "";
        switch ($entity)
        {
            case 'attribute':
                if(empty($arg_list) || !array_key_exists(0, $arg_list) || !array_key_exists(1, $arg_list))
                    break;
                $obj = &$application->getInstance('Catalog');
                $attributeInfo = $obj->getAttributeInfo($arg_list[0], $arg_list[1]);
                $output = $attributeInfo[strtolower($attr)];
                break;

            case 'productreviews':
                if ($product_id == -1)
                    break;
                loadClass('CProductReviewInfo');
                $prcrobj = new CProductReviewInfo($product_id);
                if ($prcrobj !== null)
                    $output = $prcrobj -> getReviewTagValue($attr);
                break;

            case 'cmspage':
                if (!$page_id)
                {
                    // trying to get the page_id from the request
                    $page_id = modApiFunc('Request', 'getValueByKey', 'page_id');
                }
                loadClass('CCMSPageInfo');
                $cmspageobj = new CCMSPageInfo($page_id);
                $output = $cmspageobj -> getCMSPageTagValue($attr, $arg_list);
                break;

            case 'product':
                if ($product_id == -1)
                    break;
                $prdobj = new CProductInfo($product_id);
                if ($prdobj !== null) $output = $prdobj->getProductTagValue($attr);
                break;

            case 'manufacturer':
            	$mnf_id = __info_tag_output_find_tag_params($entity, $arg_list);
                $mnf_info = modApiFunc("Manufacturers", "getManufacturerInfo", $mnf_id);
		        if($mnf_id == PARAM_NOT_FOUND          /*       <?php ManufacturerInfo(); ?>                                    */ ||
		           $mnf_id == MANUFACTURER_NOT_DEFINED /*                     Manufacturer */ ||
		           $mnf_info === NULL                  /* <?php Manufacturer('asdf') ?> */)
                {
                	$output = NULL;
                }
                else
                {
                    $img_info = modApiFunc("Images", "getImageData", $mnf_info['manufacturer_image_id']);
	               	switch($attr)
	               	{
	               	    case 'id':
                        {
                            $output = $mnf_info['manufacturer_id'];
                            break;
                        }
	              		case 'name':
	                    {
	                       	$output = $mnf_info['manufacturer_name'];
	                        break;
	                    }
						case 'url':
	                    {
	                        $output = $mnf_info['manufacturer_site_url'];
	                        break;
	                    }
						case 'description':
	                    {
	                        $output = $mnf_info['manufacturer_descr'];
	                        break;
	                    }
	                    case 'status':
	                    {
	                        $output = $mnf_info['manufacturer_active'] == DB_TRUE ? getMsg('MNF', 'STATUS_ACTIVE') : getMsg('MNF', 'STATUS_INACTIVE');
	                        break;
	                    }
						case 'image':
	                    {
	                       	$output = ($img_info['image_data'] === NULL) ? "" : getimage_output_cz('mnf_image_'. rand(0, 32768), new image_obj($img_info['image_data']['image_id']));
	                        break;
	                    }
						case 'imagesrc':
	                    {
	                       	$output = ($img_info['image_data'] === NULL || !isset($img_info['image_data'])) ? "" : $img_info['image_data']['image_src'];
	                        break;
	                    }
						case 'imagewidth':
	                    {
	                        $output = ($img_info['image_data'] === NULL || !isset($img_info['image_data'])) ? "" : $img_info['image_data']['image_width'];
	                        break;
	                    }
						case 'imageheight':
	                    {
	                        $output = ($img_info['image_data'] === NULL || !isset($img_info['image_data'])) ? "" : $img_info['image_data']['image_height'];
	                        break;
	                    }
						case 'imagealttext':
	                    {
	                        $output = ($img_info['image_data'] === NULL || !isset($img_info['image_data'])) ? "" : $img_info['image_data']['image_alt_text'];
	                        break;
	                    }
						case 'thumbnail':
	                    {
	                        $output = ($img_info['image_thumbnail_data'] === NULL || !isset($img_info['image_thumbnail_data'])) ? "" : getimage_output_cz('mnf_image_'. rand(0, 32768), new image_obj($img_info['image_thumbnail_data']['image_id']));
	                        break;
	                    }
						case 'thumbnailsrc':
	                    {
	                        $output = ($img_info['image_thumbnail_data'] === NULL || !isset($img_info['image_thumbnail_data'])) ? "" : $img_info['image_thumbnail_data']['image_src'];
	                        break;
	                    }
						case 'thumbnailwidth':
	                    {
	                        $output = ($img_info['image_thumbnail_data'] === NULL || !isset($img_info['image_thumbnail_data'])) ? "" : $img_info['image_thumbnail_data']['image_width'];
	                        break;
	                    }
						case 'thumbnailheight':
	                    {
	                        $output = ($img_info['image_thumbnail_data'] === NULL || !isset($img_info['image_thumbnail_data'])) ? "" : $img_info['image_thumbnail_data']['image_height'];
	                        break;
	                    }
						case 'thumbnailalttext':
	                    {
	                        $output = ($img_info['image_thumbnail_data'] === NULL || !isset($img_info['image_thumbnail_data'])) ? "" : $img_info['image_thumbnail_data']['image_alt_text'];
	                        break;
	                    }
	                }
            	}
                break;

            case 'category':
                if ($category_id == -1)
                    break;
                $catobj = &$application->getInstance('CCategoryInfo', $category_id);
                if ($catobj !== null) $output = $catobj->getCategoryTagValue($attr);
                break;

            case 'msg':
                $obj = &$application->getInstance('MessageResources');
                $output = $obj->getMessage( new ActionMessage($arg_list) );
                break;

            case 'xmsg':
                $obj = &$application->getInstance('MessageResources', modApiFunc("Modules_Manager","getResFileByShortName",$arg_list[0]), 'AdminZone', $arg_list[0]);
                array_shift($arg_list);
                $output = $obj->getMessage( new ActionMessage($arg_list) );
                break;

            case 'label':
                $obj = &$application->getInstance('MessageResources', "", 'CustomerZone', "CZ");
                $output = $obj->getMessage( new ActionMessage($arg_list) );
                break;

            case 'hinttext':
                $obj = &$application->getInstance('Hint');
                $output = $obj->getHintText($arg_list);
                break;

            case 'hintlink':
                $obj = &$application->getInstance('Hint');
                $output = $obj->getHintLink($arg_list);
                break;

            case 'storeowner':
                $obj = &$application->getInstance('Configuration');
                $output = $obj->getTagValue($tag);
                break;

            case 'pagehelplink':
                loadCoreFile('page_help_tutorial_links.php');
                $obj = &$application->getInstance('HelpLinkCreator');
                $output = $obj->getPageHelpLink($arg_list[0]);
                break;

            case 'videotutoriallink':
                loadCoreFile('page_help_tutorial_links.php');
                $obj = &$application->getInstance('HelpLinkCreator');
                $output = $obj->getTutorialLink($arg_list[0]);
                break;

            case 'shoppingcart':
                $obj = &$application->getInstance('Cart');
                $output = $obj->getCartInfo($tag);
                break;

            case 'paypalproexpresscheckout':
                $output = "";
                break;

            case 'paypalproukexpresscheckout':
                $output = "";
                break;

            case 'pageurl':
                $req = new Request();
                $req->setView($arg_list[0]);
                if(isset($arg_list[1]) and is_array($arg_list[1]) and !empty($arg_list[1]))
                {
                    foreach($arg_list[1] as $k => $v)
                        $req->setKey($k,$v);
                }
                if ($arg_list[0] == 'ProductList')
                {
                    if (isset($arg_list[1]))
                        $category_id = $arg_list[1];
                    if (!$category_id)
                    {
                        $plf = $application -> getInstance('CProductListFilter');
                        $category_id = $plf -> getCurrentCatgoryId();
                        $req -> setCategoryID($category_id);
                    }
                }
                $output = $req->getURL();
                break;

            case 'customer':
                if($zone == 'CustomerZone')
                {
                    $account_name = modApiFunc('Customer_Account','getCurrentSignedCustomer');
                    if($account_name != null)
                    {
                        preg_match('/^customer(.+)/i',$tag,$m1);
                        if(preg_match('/^(billing|shipping|orders)(.+)/i',$m1[1],$m2))
                        {
                            $group = $m2[1];
                            $attr = $m2[2];
                        }
                        else
                        {
                            if(in_array(strtolower($attr),array('id','status')))
                            {
                                $group = 'base';
                            }
                            else
                            {
                                $group = 'Customer';
                            };
                            $attr = $m1[1];
                        };
                        $obj = &$application->getInstance('CCustomerInfo',$account_name);

                        if(strtolower($attr) == 'accountname')
                        {
                            $output = prepareHTMLDisplay($obj->getDisplayAccountName());
                            break;
                        };

                        if(strtolower($attr) == 'signouturl')
                        {
                            $r = new Request();
                            $r->setView(CURRENT_REQUEST_URL);
                            $r->setAction('customer_sign_out');
                            $r->setKeyValList(modApiFunc('Request',
                                                         'getGETArray'));
                            $output = $r->getURL();
                            break;
                        };

                        if(strtolower($group) != 'orders')
                        {
                            $attr_value = $obj->getPersonInfo($attr,$group);

                            switch(strtolower($attr))
                            {
                                case 'country':
                                    $output = modApiFunc('Location','getCountry',$attr_value);
                                    break;
                                case 'state':
                                    if(modApiFunc('Location','getStateCode',$attr_value) != '')
                                        $output = modApiFunc('Location','getState',$attr_value);
                                    else
                                        $output = prepareHTMLDisplay($attr_value);
                                    break;
                                default:
                                    $output = prepareHTMLDisplay($attr_value);
                                    break;
                            };
                        }
                        else
                        {
                            $qstat = ORDER_STATUS_ALL;

                            if(isset($arg_list[0]) and is_string($arg_list[0]))
                            {
                                if(defined('ORDER_STATUS_'.strtoupper($arg_list[0])))
                                    $qstat = constant('ORDER_STATUS_'.strtoupper($arg_list[0]));
                            };

                            $filter = array(
                                'type' => 'quick'
                               ,'order_status' => $qstat
                            );

                            $obj->setOrdersHistoryFilter($filter);

                            switch(strtolower($attr))
                            {
                                case 'quantity':
                                    $output = $obj->getOrdersCount();
                                    break;
                                case 'totalamount':
                                    $output = modApiFunc('Localization','currency_format',$obj->getOrdersAmount());
                                    break;
                                case 'totalfullypaidamount':
                                    $output = modApiFunc('Localization','currency_format',$obj->getOrdersFullyPaidAmount());
                                    break;
                            };
                        };
                    };
                };
                break;

            case 'subscription':
            	loadClass('Subscriptions');
            	switch ($attr) {
            		case 'active':
            			if($zone == 'CustomerZone') {
            				$signed_in = modApiFunc('Customer_Account', 'getCurrentSignedCustomer') !== null;
            				$topics = modApiFunc('Subscriptions', 'getCustomerTopics', $signed_in);
            				$output = sizeof($topics) > 0 ? 'TRUE' : 'FALSE';
            			}
            	}
            	break;

            case 'unknown':
                switch($attr) {
                    case 'currentlanguage':
                        $output = modApiFunc('MultiLang', 'getLanguage');
                        break;

                    case 'resourcelanguage':
                        $output = modApiFunc('MultiLang',
                                             'getResourceLanguage');
                        break;

                    case 'defaultlanguage':
                        $output = modApiFunc('MultiLang',
                                             'getDefaultLanguage');
                        break;
                }
                break;
        }
        echo $output;
}

function __block_tag_output($tag, $arg_list)
{
    global $application, $zone;
    if ($zone == 'AdminZone' and !class_exists($tag))
    {
        $mm = &$application->getInstance( 'Modules_Manager' );
        $mm->includeViewFileOnce($tag);
    }

    if ($zone == 'AdminZone' && ! modApiFunc('Users', 'checkCurrentUserAccess', $tag)) {
       echo file_get_contents($application->getAppIni('PATH_CORE_DIR') . '/block_no_access.tpl');
       return;
    }

    CProfiler::btStart($tag);
    if ($zone == 'CustomerZone' and !class_exists($tag))
    {
        $application->prepareStorefrontBlockTag($tag);
    }

    #          ,                                  view.
    $view = $application->getLatestTag();
    if ($view != null && is_callable(array($view, 'getTag')))
    {
        $view_obj = &$application->getInstance($view);
        $application->pushTag($tag);
        $output = $view_obj->getTag($tag, $arg_list);
        $application->popTag();
    }
    if (! isset($output)) {
        $obj = &$application->getInstance($tag);
        $application->pushTag($tag);
        $output = call_user_func_array( array( &$obj, 'output' ), $arg_list);
        $application->popTag();
    }
    echo $output;
    CProfiler::btStop($tag);
}

function __block_tag_alias($block_name, $alias_name, $args)
{
    global $application, $zone;

    if($zone == 'CustomerZone')
    {
        $application->setBlockOverride($block_name, $alias_name);
        $out = call_user_func_array($block_name, $args);
        $application->resetBlockOverride($block_name);
        return $out;
    };

    return null;
}



/**
 *                   API                                       .
 *                                                       .
 *                        API               ,                               Extended API             .
 */
function loadClass($classname)
{
	global $application;
	//                                 ,             Modules Manager
	//
	if ( !class_exists($classname) )
	{
		$mm = &$application->getInstance('Modules_Manager');
		$mm->includeAPIFileOnce($classname);
	}
}

function loadCoreFile($filename)
{
    global $_core_directory;
    _use($_core_directory.$filename);
}

function loadModuleFile($filename)
{
    global $application;
    $modules_dir = CConf::get('modules_dir');
    $add_modules_dir = CConf::get('add_modules_dir');

    if(is_file($add_modules_dir.$filename)){
    	_use($add_modules_dir.$filename);
    }else {
    	_use($modules_dir.$filename);
    }
}

function loadViewClass($classname)
{
    global $application;
    //                                 ,             Modules Manager
    //
    if ( !class_exists($classname) )
    {
        $mm = &$application->getInstance('Modules_Manager');
        $mm->includeViewFileOnce($classname);
    }
}

function loadActionClass($classname)
{
    global $application;

    if (!class_exists($classname))
    {
        $mm = $application->getInstance('Modules_Manager');
        if (!key_exists($classname, $mm->actionList))
        {
            _warning("$classname not found");
            $res = null;
            return $res;
        }
        // define the file name for given action
        $actionFile = $mm->actionList[$classname];
        // load the file
        $mm->includeFile($actionFile);
    }
}

function prepareFSPath($path)
{
    return realpath($path);
}

/**
 * This is an alias of Application::Run() method.
 * It is necessary for comfortable usage in customer's web-site pages
 * to output some view.
 *
 * @see Application::output()
 * @access  public
 * @param string $view_name View name
 * @return string Print HTML code of stated $view_name
 */
function _output($view_name)
{
    global $application;
    echo $application->output($view_name);
}

/**
 * DEPRECATED
 * Store log information in log file,
 * for debug purpose.
 *
 * This function is an alias of Logger methods. Three parameters are passed in
 * this function:
 * <ol>
 *  <li> $line - any message
 *  <li> $type - the type of log information, 'message' by default <br>
 *       The possible message types are:
 *       <ul>
 *          <li> 'message' - any message, that has several ways of using:<br>
 *               <code>_debug('Some log message');</code> adds the message
 *                      which specifies the time that has past
 *                      from the beginning of the script execution;
 *               <code>
 * _debug('Some log message', 'message', 'BEGIN');
 * .
 * .
 * .
 * _debug('Some log message', 'message', 'END');
 *               </code>
 *              if the third parameter is 'BEGIN', then the message is written
 *              to the log file. This message contains the time
 *              that has past from the beginning of the script execution. If
 *              the third parameter is 'END', then
 *              in the addition to the same information the time that has past
 *              from logging the message is written. And the label
 *              'BEGIN' is added.
 *          <li> 'class' is a list of defined classes<br>
 *               <code>_debug('', 'class');</code>
 *          <li> 'var'Logs to the log file the name and the value of variable<br>
 *               <code>
 * $a = 12;
 * $b = $a +3;
 * _debug($a, 'var', 'a');
 * _debug($b, 'var', 'b');
 *               </code>
 *              A variable name or any message is passed in as the third
 *              parameter. <br>
 *              The whole list of defined variables can be outputted:
 *               <code>_debug(get_defined_vars(), 'var');</code>
 *          <li> 'func' - a list of defined user functions<br>
 *               <code>_debug('', 'func');</code>
 *          <li> 'const' - a list of defined user and standard constants<br>
 *               <code>_debug('', 'const');</code>
 *       </ul>
 *
 *  <li> $mark - an optional parameter. There are 2 ways of using it.
 *   1. If $type = 'message' then 'BEGIN' or 'END' can be used as values.
 *   2. If $type = 'var', then the name of variable, passed in parameter
 *      $line, is used as value
 * </ol>
 *
 * @access  public
 * @param string $line any message
 * @param string $type the type of log information
 * @param string $mark an optional parameter, 'NOMARK' by default
 */
function _debug($line, $type = 'message', $mark='NOMARK')
{
    // DEPRECATED
}


/**
 * Includes once a file in the modules folder.
 *
 * @access  public
 * @param string $file
 */
function _use($file)
{
    global $bootstrap;
    $bootstrap->includeFile( $file );
}

function _print($list, $title = 'variable')
{
    echo prepareArrayDisplay($list, $title);
}

function prepareArrayDisplay($array, $title='Title', $first_cycle = true, $func = null, $max_level = 8)
{
    if ($max_level == 0) {
        return '';
    }
    $js  = "<script>";
    $js .= " function prepareArrayDisplay_toggleBlock_{FUNC}(block_id) ";
    $js .= "{ var el = document.getElementById('list_'+block_id); ";
    $js .= "  if (el)";
    $js .= "  {";
    $js .= "    if (el.style.display == 'none')";
    $js .= "    {";
    $js .= "        el.style.display = '';";
    $js .= "    }";
    $js .= "    else";
    $js .= "    {";
    $js .= "        el.style.display = 'none';";
    $js .= "    }";
    $js .= "  }";
    $js .= "}";
    $js .= "</script>";

    $html = "<div style='font-family: Verdana; font-size: 8pt; text-align: left; color: black; line-height: 16px;'>";

    $tpl_array = "{TYPE} <A onclick=\"prepareArrayDisplay_toggleBlock_{FUNC}('{ID}',1)\" href='javascript: void(0);' style='text-decoration: none; font-weight: bold; font-family: Impact; color: green;'>&gt;&gt;&gt;</A><br><div id='list_{ID}' style='padding-left: 30px; display: {DISPLAY};'>{CONTENT}</div>";
    $tpl_key = "{KEY} => ";
    $tpl_value = "{TYPE} {VALUE}<br>";

    if ($func === null)
    {
        $func = md5(uniqid(rand(), true));
        $js = str_replace('{FUNC}', $func, $js);
    }

    if (is_object($array) or (is_array($array) and !empty($array)))
    {
        if ($first_cycle)
        {
            $html = $js.$html;
            $html .= "<b>$title:</b> ".str_replace(
                                    array('{CONTENT}', '{TYPE}', '{ID}', '{DISPLAY}', '{FUNC}'),
                                    array(prepareArrayDisplay($array, '', false, $func, $max_level-1), __formatType($array), md5(uniqid(rand(), true)), 'block', $func),
                                    $tpl_array
                                );
            $html .= '<br>';
        }
        else
        {
            foreach ($array as $key=>$item)
            {
                if (is_array($item) and !empty($item) )
                {
                    $html .= str_replace('{KEY}', __formatKey($key), $tpl_key);
                    $html .= str_replace(
                                            array('{CONTENT}', '{TYPE}', '{ID}', '{DISPLAY}', '{FUNC}'),
                                            array(prepareArrayDisplay($item, '',false, $func, $max_level-1), __formatType($item), md5(uniqid(rand(), true)), 'none', $func),
                                            $tpl_array
                                        );
                }
                else if (is_object($item))
                {
                    $html .= str_replace('{KEY}', __formatKey($key), $tpl_key);
                    if (isempty(get_object_vars($item)))
                    {
                        $html .= str_replace(
                                                array('{VALUE}', '{TYPE}'),
                                                array(__formatValue(array(),$func), __formatType($item)),
                                                $tpl_value
                                            );
                    }
                    else
                    {
                        $html .= str_replace(
                                                array('{CONTENT}', '{TYPE}', '{ID}', '{DISPLAY}', '{FUNC}'),
                                                array(prepareArrayDisplay( $item, '',false, $func, $max_level-1), __formatType($item), md5(uniqid(rand(), true)), 'none', $func),
                                                $tpl_array
                                            );
                    }
                }
                else
                {
                    $html .= str_replace('{KEY}', __formatKey($key), $tpl_key);
                    $html .= str_replace(
                                            array('{VALUE}', '{TYPE}'),
                                            array(__formatValue($item,$func), __formatType($item)),
                                            $tpl_value
                                        );
                }
            }
        }
        $html .= '</div>';
        return $html;
    }
    else
    {
        return "<b>$title:</b> ".str_replace(
                            array('{VALUE}', '{TYPE}'),
                            array(__formatValue($array), __formatType($array)),
                            $tpl_value
                          ).'<br>';
    }
}

function __formatValue($var, $func = '')
{
    $color = '#343434';
    $value = $var;
    if (is_bool($var))
    {
        $value = ($value == true) ? '&lt;true&gt;' : '&lt;false&gt;';
    }
    if (is_null($var))
    {
        $value = '&lt;null&gt;';
    }
    if (is_string($var) and empty($var) && $var !== '0')
    {
        $value = "&lt;empty value&gt;";
    }
    if (is_array($var) and empty($var))
    {
        $value = "&lt;empty value&gt;";
    }
    if (is_resource($var))
    {
        $value = "&lt;resource&gt;";
    }

    $full_value = '';
    if ($var !== $value)
    {
        $color = 'gray';
    }
    else
    {
        $value = htmlspecialchars($value);
        if (_ml_strlen($value)>100 && $func != '')
        {
            $tpl_array = "<A onclick=\"prepareArrayDisplay_toggleBlock_{FUNC}('{ID}',1)\" href='javascript: void(0);' style='text-decoration: none; font-weight: bold; font-family: Impact; color: green;'>&gt;&gt;&gt;</A><div id='list_{ID}' style='padding-left: 30px; display: none;'>{CONTENT}</div>";
            $full_value = str_replace(
                                    array('{CONTENT}', '{ID}', '{FUNC}'),
                                    array(_ml_substr($value, 0, MAX_STRDUMP_LENGTH), md5(uniqid(rand(), true)), $func),
                                    $tpl_array
                                );
            $value = _ml_substr($value, 0, 100).' ... ';
        }
    }
    $tpl = '<span style="font-family: tahoma, verdana; font-size: 11px; font-weight: bold; color: {C};">{V}</span>';
    return str_replace(array('{C}','{V}'), array($color, $value), $tpl).$full_value;
}

function __formatKey($key)
{
    return '<span style="font-family: verdana; font-size: 11px; font-weight: bold; color: navy;">['.$key.']</span>';
}

function __formatType($var)
{
    $size = null;
    $type_name = '';

    if (is_array($var))
    {
        $size = count($var);
        $type_name = 'array';
    }
    else if (is_bool($var))
    {
        $type_name = 'boolean';
    }
    else if (is_float($var))
    {
        $type_name = 'float';
    }
    else if (is_int($var))
    {
        $type_name = 'integer';
    }
    else if (is_null($var))
    {
        $type_name = 'null';
    }
    else if (is_string($var))
    {
        $size = _ml_strlen($var);
        $type_name = 'string';
    }
    else if (is_object($var))
    {
        $size = get_class($var);
        $type_name = 'object';
    }
    else if (is_resource($var))
    {
        $type_name = 'resource';
    }
    else
    {
        $type_name = 'array';
    }

    if ($size == null)
    {
        $tpl = '{TYPE}';
    }
    else
    {
        $tpl = '{TYPE}, {SIZE}';
    }
    $html = '<span style="font-family: verdana; font-size: 10px; color: navy;">('.$tpl.')</span>';
    return str_replace(array('{TYPE}', '{SIZE}'), array($type_name, $size), $html);
}

/**
 * The global function to generate the fatal error.
 *
 * <code>
 * _fatal(string $code [, mixed $var1 [, mixed $var2 [, ...]]])
 * </code>
 * Error code $code and a certain number of variables are passed in
 * the function. They are inserted to the text of the error message,
 * which matches the error code. Error codes and their texts are stored
 * in the resource files templates/resources/<module_name>.ini
 *
 * @see Resource
 * @see _warning()
 * @ function is not completed
 * @access public
 * @return
 */
function _fatal($msg)
{
    global $application, $zone;

    $numargs = func_num_args();
    $listargs = func_get_args();
    $args = array();
    if ($numargs > 1)
    {
        for ($i=1; $i<$numargs; $i++)
        {
            $args['{'.($i-1).'}'] = $listargs[$i];
        }
    }
	if(isset($application))
	{
		$lang = _ml_strtolower($application->getAppIni('LANGUAGE'));
		if ( !isset($lang) || $lang=='' )	$lang = 'eng';
	    $path = $application->getAppIni('PATH_ADMIN_RESOURCES');
    	$filename = 'system-messages-'.$lang.'.ini';
    	$messages_resources = @_parse_ini_file($path . $filename);
	}

	if(isset($listargs[0]["CODE"]))					// Code
		$err_code = $listargs[0]["CODE"];

	if(isset($listargs[0]["MODULE"]))				// Module
		$err_module = $listargs[0]["MODULE"];

	if(isset($listargs[0]["DIRECTIVE"]))			// Directive
		$err_directive = $listargs[0]["DIRECTIVE"];

	if(isset($listargs[0]["SECTION"]))				// Section
		$err_section = $listargs[0]["SECTION"];

	if(isset($listargs[0]["FILE"]))					// File
		$err_file = $listargs[0]["FILE"];
	elseif(isset($listargs[0]["file"]))
		$err_file = $listargs[0]["file"];

	if(isset($listargs[0]["LINE"]))					// Line
		$err_line = $listargs[0]["LINE"];
	elseif(isset($listargs[0]["line"]))
		$err_line = $listargs[0]["line"];

	if(isset($listargs[0]["MESSAGE"]))				// Message
		$err_message = $listargs[0]["MESSAGE"];
	elseif(isset($messages_resources[$listargs[0]["CODE"]]))
		$err_message = strtr($messages_resources[$listargs[0]["CODE"]], $args);
	elseif(isset($listargs[0]["message"]))
		$err_message = $listargs[0]["message"];

	CTrace::err(
		 'Fatal Error: '.(isset($zone)?$zone:'Unspecified Zone')."\n"
		.(isset($err_code)?"Code = $err_code\n":'')
		.(isset($err_module)?"Module = $err_module\n":'')
		."Referer = ". $_SERVER['PHP_SELF'] ."\n"
		.(isset($err_file)?"File = $err_file\n":'')
		.(isset($err_line)?"Line = $err_line\n":'')
		.(isset($err_message)?"Message = $err_message":'')
	);
	CTrace::backtrace();

	if (isset($zone) && $zone == 'CustomerZone' && isset($application))
	{
		header("location:".$application->appIni['SITE_URL']."internal-server-error.html",TRUE,307);
		die;
	}
	elseif(isset($zone) && $zone == 'CustomerZone')
	{
		header("location:internal-server-error.html",TRUE,307);
		die;
	}

    if (is_array($listargs[0]))
    {
        if (isset($listargs[0]))
		//if (isset($messages_resources[$listargs[0]["CODE"]]))
        {
            $header = "<!DOCTYPE HTML>\n";
            $header.= "<HTML><HEAD><TITLE>Tags Errors and Warnings</TITLE>";
            $header.= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\">\n";
            $header.= "<LINK HREF=\"../../includes/bootstrap/css/bootstrap.min.css\" TYPE=\"text/css\" REL=\"stylesheet\">\n";
            $header.= "</HEAD><BODY><DIV class='container'><br /><br /><br /><br />\n";
            $subtitle = "<div class='alert alert-danger'><h1>%s</h1>\n";
            $subtitle.= "<strong>Error Code:</strong> %s</div>\n";

            $tablecontent = "%s<br /><br />\n";
            $tablecontent.= "%s\n";

            $source_line = "<strong>%s</strong>\n";
            $source_line.= "%s<br />\n";

            $output = $header;
            $source = "";
            if (isset($err_file))
            {
                $source.= sprintf($source_line, "File:", $err_file);
            }
            if (isset($err_line))
            {
                $source.= sprintf($source_line, "Line:", $err_line);
            }
            if (isset($err_section))
            {
                $source.= sprintf($source_line, "Section:", "[".$err_section."]");
            }
            if (isset($err_directive))
            {
                $source.= sprintf($source_line, "Directive:", $err_directive);
            }
            if (isset($err_module))
            {
                $source.= sprintf($source_line, "Module:", $err_module);
            }
	    	if(isset($err_message))
			{
		    	$source.= sprintf($source_line, "Message:", $err_message);
	    	}

	    	$content = sprintf($tablecontent, $listargs[0]["CODE"], $source);
            $output.= sprintf($subtitle, "FATAL Core Error", $content);
        	$footer = "</DIV></BODY></HTML>";
	        $output.= $footer;
            die($output);
        }
        else
        {
            die(print_r($listargs[0],true));
        }
    }
    else
    {
        $msg = '';
        foreach($listargs as $a)
        {
            $msg .= $a ."<br>\n";
        }
        die('<B><FONT COLOR="red">FATAL ERROR</FONT></B><br> '.$msg);
    }
}

/**
 * Function is used for calling all the API methods
 * of main classes of modules.
 *
 * The first parameter is the class name,
 * the second one is the name of the called method.
 * Then an unlimited number of parameters for called method can follow.
 * An example:
 * <code>
 * // Sets a current category in catalog
 * modApiFunc('Catalog', 'setCurrentCategoryID', $curr_cat_id);
 *
 * // Gets a current category in catalog
 * $cid = modApiFunc('Catalog', 'getCurrentCategoryID');
 *
 * // A general example of using the function
 * $retval = modApiFunc('Class_Name', 'methodName', $par1, $par2, $par3);
 * </code>
 *
 * This function always calles the object of specified class by reference.
 *
 * @ Use resources for error reporting and add an error handling mechanism.
 * @author Alexey Florinsky
 * @access  public
 * @return mixed The result of API method execution
 * @param string $className Class name
 * @param string $methodName Method name in the class
 */
function modApiFunc($className, $methodName)
{
    global $application;
    # Get a list of passed parameters
    $arg_list = func_get_args();

    # Remove first two parameters from the list
    array_shift($arg_list);
    array_shift($arg_list);

        if ( $className == 'application' )
        {
            $classObj = &$application;
        }
        else
        {
            $classObj = &$application->getInstance( $className );
        }

        if ( method_exists( $classObj, $methodName ) )
        {
            return call_user_func_array( array( &$classObj, $methodName ), $arg_list);
        }
        else
        {
            _fatal(array( "CODE" => "CORE_044"), $className, $methodName);
        }

    return NULL;
}

/**
 *                                      modApiFunc,                    ,
 *                                               .
 *
 *      ,                                       ,                        .
 */
function modApiStaticFunc($className, $methodName)
{
    global $application;
    # Get a list of passed parameters
    $arg_list = func_get_args();

    # Remove first two parameters from the list
    array_shift($arg_list);
    array_shift($arg_list);

    $mmObj = &$application->getInstance('Modules_Manager');
    $mmObj->includeAPIFileOnce($className);
	return call_user_func_array(array($className, $methodName),$arg_list);
}


/**
 * The global function to generate the warnings.
 *
 * <code>
 * _warning(string $code [, mixed $var1 [, mixed $var2 [, ...]]])
 * </code>
 * Warning code $code and a certain number of variables are passed in
 * the function. They are inserted to the text of the error message,
 * which matches the warning code. Error codes and their texts are stored
 * in the resource files templates/resources/<module_name>.ini
 *
 * @see Resource
 * @see _error()
 *
 * @access public
 * @return
 */
function _warning()
{
    $numargs = func_num_args();
    $listargs = func_get_args();
    $args = array();
    if ($numargs > 1)
    {
        for ($i=1; $i<$numargs; $i++)
        {
            $args['{'.($i-1).'}'] = $listargs[$i];
        }
    }
    global $application;
    $error = &$application->getInstance('Error');
    $error->setErrorCode(func_get_arg(0), $args, 'warning');
}

/**
 * Searches some value in the multidimensional array.
 *
 * @return Array  The array, which describes a path to the found element.
 * FALSE if the element wasn't found.
 */
function multi_array_search($search_value, $the_array)
{
   if (is_array($the_array))
   {
       foreach ($the_array as $key => $value)
       {
           $result = multi_array_search($search_value, $value);
           if (is_array($result))
           {
               $return = $result;
               array_unshift($return, $key);
               return $return;
           }
           elseif ($result == true)
           {
               $return[] = $key;
               return $return;
           }
       }
       return false;
   }
   else
   {
       if ($search_value == $the_array)
       {
           return true;
       }
       else return false;
   }
}

/**
 * Simple function to replicate PHP 5 behaviour.
 */
function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

/**
 * Checks if the specified key in this array is an empty string.
 *
 * @param $key String the key
 * @param $array Array the array of values
 * @return boolean
 */
function isEmptyKey($key, $array)
{
	if (!array_key_exists($key, $array) || trim($array[$key]) == '')
		return true;
	return false;
}

/**
 * Returns the value from the associative array by the case-insensitive key.
 */
function getKeyIgnoreCase($key, $array)
{
    if (!is_array($array))
    {
    	return null;
    }

	$key = _ml_strtolower($key);
	foreach ($array as $_key => $_value)
	{
		if ($key === _ml_strtolower($_key))
		{
			return $_value;
		}
	}
	return null;
}

/**
 * ready HTML output
 * <br>
 * Gets a variable, cleaning it up such that the text is
 * shown exactly as expected, except for allowed HTML tags which
 * are allowed through
 *
 * @param var variable to prepare
 * @param ...
 * @returns string/array
 * @return prepared variable if only one variable passed
 * in, otherwise an array of prepared variables
 */
function prepareHTMLDisplay()
{
    // This search and replace finds the text 'x@y' and replaces
    // it with HTML entities, this provides protection against
    // email harvesters
    //
    // Note that the use of \024 and \022 are needed to ensure that
    // this does not break HTML tags that might be around either
    // the username or the domain name
//    static $search = array('/([^\024])@([^\022])/se');
//
//    static $replace = array('"&#" .
//                            sprintf("%03d", ord("\\1")) .
//                            ";&#064;&#" .
//                            sprintf("%03d", ord("\\2")) . ";";');

    $resarray = array();
    foreach (func_get_args() as $ourvar) {
        // Preparse var to mark the HTML that we want
//            $ourvar = preg_replace($allowedhtml, "\022\\1\024", $ourvar);

        // Prepare var
        $ourvar = htmlspecialchars($ourvar);
//        $ourvar = preg_replace($search, $replace, $ourvar);
//
//        // Fix the HTML that we want
//        $ourvar = preg_replace('/\022([^\024]*)\024/e',
//                               "'<' . strtr('\\1', array('&gt;' => '>',
//                                                         '&lt;' => '<',
//                                                         '&quot;' => '\"',
//                                                         '&amp;' => '&'))
//                               . '>';", $ourvar);
//
//        // Fix entities if required
//            if (pnConfigGetVar('htmlentities')) {
//            $ourvar = preg_replace('/&amp;#/', '&#', $ourvar);
//            }

        // Add to array
        array_push($resarray, $ourvar);
    }

    // Return vars
    if (func_num_args() == 1) {
        return $resarray[0];
    } else {
        return $resarray;
    }
}

/**
 * Extracts a tag name from the full name used in the system.
 * Examples: <br/>
 * - input: ProductName output: array('product' => 'name') <br/>
 * - input: CategoryDescr output: array('category' => 'descr') <br/>
 * An input case-insesitive parameter. The result is output in lower case.
 *
 * @return array (entity, tag). entity: 'product', 'category', 'attribute',
 * 'msg', 'group', 'order', 'customer'. tag: the tag name.
 */
function getTagName($full_tag)
{
    $full_tag = _ml_strtolower($full_tag);
    $entity = '';
    $tag = '';
    if (preg_match("/^(product)(.+)(custom)$/", $full_tag, $matches))
    {
        $full_tag = _ml_substr($full_tag, 0, (_ml_strlen($full_tag)-6));
    }
    if (strncmp($full_tag, 'productreviews', 14) === 0)
    {
        $entity = 'productreviews';
        $tag = substr($full_tag, 14);
    }
    elseif (strncmp($full_tag, 'product', 7) === 0)
    {
        $entity = 'product';
        $tag = substr($full_tag, 7);
    }
    elseif (strncmp($full_tag, 'cmspage', 7) === 0)
    {
        $entity = 'cmspage';
        $tag = _ml_substr($full_tag, 7);
    }
    elseif (strncmp($full_tag, 'manufacturer', 12) === 0)
    {
        $entity = 'manufacturer';
        $tag = _ml_substr($full_tag, 12);
    }
    elseif (strncmp($full_tag, 'category', 8) === 0)
    {
        $entity = 'category';
        $tag = _ml_substr($full_tag, 8);
    }
    elseif (strncmp($full_tag, 'attribute', 9) === 0)
    {
        $entity = 'attribute';
        $tag = _ml_substr($full_tag, 9);
    }
    elseif (strncmp($full_tag, 'msg', 3) === 0)
    {
        $entity = 'msg';
        $tag = _ml_substr($full_tag, 3);
    }
    elseif (strncmp($full_tag, 'xmsg', 4) === 0)
    {
        $entity = 'xmsg';
        $tag = _ml_substr($full_tag, 4);
    }
    elseif (strncmp($full_tag, 'label', 5) === 0)
    {
        $entity = 'label';
        $tag = _ml_substr($full_tag, 5);
    }
    elseif (strncmp($full_tag, 'hinttext', 8) === 0)
    {
        $entity = 'hinttext';
        $tag = _ml_substr($full_tag, 8);
    }
    elseif (strncmp($full_tag, 'hintlink', 8) === 0)
    {
        $entity = 'hintlink';
        $tag = _ml_substr($full_tag, 8);
    }
    elseif (strncmp($full_tag, 'pagehelplink', 12) === 0)
    {
        $entity = 'pagehelplink';
        $tag = _ml_substr($full_tag, 12);
    }
    elseif (strncmp($full_tag, 'videotutoriallink', 17) === 0)
    {
        $entity = 'videotutoriallink';
        $tag = _ml_substr($full_tag, 17);
    }
    elseif (strncmp($full_tag, 'group', 5) === 0)
    {
        $entity = 'group';
        $tag = _ml_substr($full_tag, 5);
    }
    elseif (strncmp($full_tag, 'order', 5) === 0)
    {
        $entity = 'order';
        $tag = _ml_substr($full_tag, 5);
    }
    elseif (strncmp($full_tag, 'customer', 8) === 0)
    {
        $entity = 'customer';
        $tag = _ml_substr($full_tag, 8);
    }
    elseif (strncmp($full_tag, 'step', 4) === 0)
    {
        $entity = 'step';
        $tag = _ml_substr($full_tag, 4);
    }
    elseif (strncmp($full_tag, 'storeowner', 10) === 0)
    {
        $entity = 'storeowner';
        $tag = _ml_substr($full_tag, 10);
    }
    elseif (strncmp($full_tag, 'shoppingcart', 12) === 0)
    {
        $entity = 'shoppingcart';
        $tag = _ml_substr($full_tag, 12);
    }
    elseif (strncmp($full_tag, 'pageurl', 7) === 0)
    {
        $entity = 'pageurl';
        $tag = _ml_substr($full_tag, 7);
    }
    elseif (strncmp($full_tag, 'subscription', 12) === 0)
    {
        $entity = 'subscription';
        $tag = _ml_substr($full_tag, 12);
    }
    else
    {
        $entity = 'unknown';
        $tag = $full_tag;
    }

    return array($entity, $tag);
}

/**
 * Returns the server's current time, which is updated by the system settings.
 *
 * @return integer timestamp
 */
function getServerTime()
{
	$current_time = time();
	$time_shift = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT);
	$current_time += ($time_shift * 60 * 60);
	return $current_time;
}

function _parse_cz_layout_ini_file($filename, $process_sections = false)
{
    global $application;
    $ini_cache = $application->getIniCache();
    $ini_mtime = @filemtime($filename);
    if ($ini_mtime == $ini_cache->read($filename.'-mtime')) {
        $layout_array = $ini_cache->read($filename);
    }
    else {
        $layout_array = _parse_ini_file($filename, $process_sections);
        $ini_cache->write($filename.'-mtime', $ini_mtime);
        $ini_cache->write($filename, $layout_array);
    }
    $layout_array = LayoutConfigurationManager::add_https_settings($layout_array, $filename);
    return $layout_array;
}

function _parse_ini_file($filename, $process_sections = false)
{
    if (phpversion()<5)
    {
        CProfiler::ioStart($filename, 'parse');
        $retval = @parse_ini_file($filename, $process_sections);
        CProfiler::ioStop();
        $retval = convertResourceArray($retval);

        if (isset($retval["Site"]["SiteURL"]))
        {
            $retval["Site"]["SiteURL"] = _ini_file_work_slashes($retval["Site"]["SiteURL"]);
        }
        if (isset($retval["Site"]["SitePath"]))
        {
            $retval["Site"]["SitePath"] = _ini_file_work_slashes($retval["Site"]["SitePath"]);
        }
            if (isset($retval["Site"]["SiteHTTPSURL"]))
        {
            $retval["Site"]["SiteHTTPSURL"] = _ini_file_work_slashes($retval["Site"]["SiteHTTPSURL"]);
        }

        return $retval;
    }
    $retval = array();
    if (!file_exists($filename))
    {
        return $retval;
    }
    $section = "";
    CProfiler::ioStart($filename, 'parse');
    $fp = fopen($filename, "r");
    while (!feof($fp))
    {
        $line = trim(fgets($fp));
        $line = convertResource($line);
        if (_ml_strlen($line) == 0 || $line[0] == ";")
        {
            continue;
        }
        if ($line[0] == "[")
        {
            if (!$process_sections)
            {
                continue;
            }
            else
            {
                $section = strtr($line, array("["=>"", "]"=>""));
                $retval[$section] = array();
            }
        }
        else
        {
            list($key, $val) = parse_ini_line($line);
            if ($key)
            {
                if (!$process_sections)
                {
                    $retval[$key] = $val;
                }
                else
                {
                    $retval[$section][$key] = $val;
                }
            }
        }
    }
    CProfiler::ioStop();


    if (isset($retval["Site"]["SiteURL"]))
    {
        $retval["Site"]["SiteURL"] = _ini_file_work_slashes($retval["Site"]["SiteURL"]);
    }
    if (isset($retval["Site"]["SitePath"]))
    {
        $retval["Site"]["SitePath"] = _ini_file_work_slashes($retval["Site"]["SitePath"]);
    }
        if (isset($retval["Site"]["SiteHTTPSURL"]))
    {
        $retval["Site"]["SiteHTTPSURL"] = _ini_file_work_slashes($retval["Site"]["SiteHTTPSURL"]);
    }

    return $retval;
}

function parse_ini_line($line)
{
	$str_pos_eq = _ml_strpos($line, "=");
    if ($str_pos_eq === false)
    {
        return array(null, null);
    }
    $str_pos_quote = _ml_strpos($line, '"');
    if ($str_pos_quote && $str_pos_eq > $str_pos_quote)
    {
        return array(null, null);
    }
    $key = trim(_ml_substr($line, 0, $str_pos_eq));
    $val = trim(_ml_substr($line, ($str_pos_eq+1)));
    if (_ml_strlen($val) && $val[0] == '"')
    {
    	$str_pos_quote_val = _ml_strpos($val, '"');
        $val = _ml_substr($val, 1, $str_pos_quote_val - 1);
    }
    return array($key, $val);
}

/**
 * Function changes '\' into '/' and adds closing slash if not present
 *
 * @param char[] $str
 */

function _ini_file_work_slashes($str)
{
    $str = str_replace("\\", "/", $str);
    if ($str[_byte_strlen($str)-1] != "/")
        $str .= "/";

    return $str;
}

/**
 * It equals the php function array_merge_recursive and merges two arrays
 * recursively. It differs from the standard function in that numeral keys of
 * the resulting array are not overidden.
 *
 * @param array $array1 - first array
 * @param array $array2 - second array
 * @return array - merged array
 */
function _array_merge_recursive($array1, $array2)
{
    foreach ($array2 as $key => $val)
    {
        if (is_array($val))
        {
            if (isset($array1[$key]))
            {
                $array1[$key] = _array_merge_recursive($array1[$key], $val);
            }
            else
            {
                $array1[$key] = $val;
            }
        }
        else
        {
            $array1[$key] = $val;
        }
    }
    return $array1;
}

/**
 * bin2hex
 */
function convertHex2bin($data)
{
    $len = _byte_strlen($data);
    return pack("H" . $len, $data);
}

/**
 * Sends a chunk of data, which can be put in one query without loading,
 * as a file.
 *
 */
function _send_data_as_file_in_one_chunk($file_name, $file_content)
{
    if(!empty($file_name))
    {
        header ("HTTP/1.1 200 OK\n");
        header ("Content-Length: "._byte_strlen($file_content));
        header ("Pragma: public");
        header ("Expires: 0");
        header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header ("Cache-Control: private",false);
        header ("Content-Type: text/plain; charset=ISO 8859-1");
        header ("Content-Disposition: attachment; filename=\"".$file_name."\"");
        header ("Content-Transfer-Encoding: binary");
        print $file_content;
    }
}

function clone_db_table_info($APIClassName, $TableName, $NewTableName="")
{
    global $application;

    if (!$NewTableName)
    {
        $NewTableName = $TableName."_copy";
    }
    $tables = modApiFunc($APIClassName, "getTables");
    $table_info_old = $tables[$TableName];
    $table_info_new = $table_info_old;
    foreach($table_info_old['columns'] as $key => $value)
    {
        $table_info_new['columns'][$key] = str_replace
        (
             $TableName . ".",
             $NewTableName . ".",
             $value
        );
    }
    return $table_info_new;
}

/**
 * Appends a new key with the value to the end of the array.
 * That means the key must be the last thing when viewing
 * the array with the foreach.
 */
function asc_assoc_array_push_back(&$array, $key, $value)
{
    $array[$key] = $value;
}

/**
 * Moves the element (if it exists) to the end of the array.
 * That means the key must be the last thing when viewing
 * the array with the foreach.
 */
function asc_assoc_array_move_back(&$array, $key)
{
	if(isset($array[$key]))
	{
		$value = $array[$key];
		unset($array[$key]);
		asc_assoc_array_push_back($array, $key, $value);
	}
}

function asc_file_put_contents($filepath, $contents)
{
    $f = new CFile($filepath);
    $f->putContent($contents);
}

/**
 * wrapper for array_combine function.
 */
function asc_array_combine($keys, $values)
{
    if(version_compare(phpversion(),"5.0.0")!=-1)
    {
        return array_combine($keys, $values);
    }
    else
    {
        $ck = reset( $keys );
        $cv = reset( $values );
        $c = array();
        while ( $ck && $cv )
        {
            $c[$ck] = $cv;
            $ck = next( $keys );
            $cv = next( $values );
        }
        return $c;
     };
}

function asc_detect_eol($fpath)
{
    $_eol = "\n";

    $flag = false;
    CProfiler::ioStart($fpath);
    $fh = fopen($fpath,'rb');
    while(!$flag and !feof($fh))
    {
        $c = fread($fh,1);
        if($c=="\n")
        {
            $_eol = "\n";
            $flag = true;
        }
        elseif($c=="\r")
        {
            $nc = fread($fh,1);
            if($nc == "\n")
                $_eol = "\r\n";
            else
                $_eol = "\r";
            $flag = true;
        }
    };
    CProfiler::ioStop();

    return $_eol;
};

function asc_mac2nix($src)
{
    CProfiler::ioStart($src, 'read-write');
    $fh = fopen($src,'r+b');
    while(!feof($fh))
    {
        $c = fread($fh,1);
        if($c=="\r")
        {
            fseek($fh,-1,SEEK_CUR);
            fwrite($fh,"\n");
        };
    };
    fclose($fh);
    CProfiler::ioStop();
}

function printNavCell($shortModuleName, $msgKeyHeader,$msgKeyDescription,$onClick)
{
    $tpl =  '<div class="InactiveNavCellBorder" id="NavCell%UID%" '
           .'     onMouseOver="NavCellMouseOver(this.id);" onMouseOut="NavCellMouseOut(this.id);">'
           .'     <div class="InactiveNavCellContent" id="NavCell%UID%_content" '
           .'          onclick="%HREF%"> '
           .'               <B><span id="NavCell%UID%_header" class="InactiveNavCellHeader">%HEADER%</span></B> '
           .'               <BR><BR>%DESCR% '
           .'    </div> '
           .'</div> ';

    static $_uid = 1000;
    $_uid++;

    $_href = $onClick;

    global $application;
    if ($shortModuleName == null)
        $obj = &$application->getInstance('MessageResources');
    else
        $obj = &$application->getInstance('MessageResources', modApiFunc("Modules_Manager","getResFileByShortName",$shortModuleName), 'AdminZone');
    $_header = $obj->getMessage( new ActionMessage(array($msgKeyHeader)) );
    $_descr = $obj->getMessage( new ActionMessage(array($msgKeyDescription)) );

    echo strtr($tpl, array('%UID%'=>$_uid, '%HREF%'=>$_href, '%HEADER%'=>$_header, '%DESCR%'=>$_descr ));
}

function NB_0071() {echo "";}

function str_rev_pad($str,$length,$symbol="...")
{
    if(_ml_strlen($str) <= $length)
        return $str;

    $pl = ceil($length / 2) - ceil(_ml_strlen($symbol) / 2);

    return _ml_substr($str,0,$pl).$symbol._ml_substr($str,(-1)*$pl);
};

function getip()
{
    static $ip = false;
    if ($ip !== false) return $ip;

    foreach ( array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                    'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $aah)
    {
        if (!isset($_SERVER[$aah])) continue;
        $curip = $_SERVER[$aah];
        $curip = explode('.', $curip);
        if (count($curip) !== 4) break; // If they've sent at least one invalid IP, break out

        foreach ($curip as $sup) if (($sup = intval($sup)) < 0 or $sup > 255) break 2;

        $curip_bin = $curip[0] << 24 | $curip[1] << 16 | $curip[2] << 8 | $curip[3];
        foreach (array(
            //    hexadecimal ip  ip mask
            array(0x7F000001,     0xFFFF0000), // 127.0.*.*
            array(0x0A000000,     0xFFFF0000), // 10.0.*.*
            array(0xC0A80000,     0xFFFF0000), // 192.168.*.*
            ) as $ipmask)
        {
            if (($curip_bin & $ipmask[1]) === ($ipmask[0] & $ipmask[1])) break 2;
        }
        return $ip = $curip;
    }
    return $ip = $_SERVER['REMOTE_ADDR'];
}

function cz_getMsg()
{
    global $application;
    $obj = &$application->getInstance('MessageResources', 'messages', 'CustomerZone', 'CZ');
    $args = func_get_args();
    return $obj->getMessage(new ActionMessage($args));
}

function getCharset($zone = "CZ")
{
    global $application;
    $charset = "STOREFRONT_CHARSET";
    switch ($zone)
    {
        case "AZ":
            $charset = "ADMIN_ZONE_CHARSET";
            break;
        case "NTFC":
            $charset = "NOTIFICATIONS_CHARSET";
            break;
        default:
            $charset = "STOREFRONT_CHARSET";
            break;
    }
    return $application->getAppIni($charset);
}

function Charset($zone = "CZ")
{
    echo getCharset($zone);
}

/**
 *                                            $list                        $glue.
 *                              implode                    ,
 *                         $list
 *         $glue.
 *
 *                               $add_glue_to_end,
 *         $glue                              .                   :
 * FILTERED_IMPLODE_NOWHERE -                     $glue         (  -         ),
 * FILTERED_IMPLODE_BOTH -           $glue                   ,
 * FILTERED_IMPLODE_LEFT -           $glue      ,
 * FILTERED_IMPLODE_RIGHT -           $glue       .
 *
 *                                                    isempty($var).
 *
 *       :
 * <code>
 * $list = array(1,2,'',4,null,6);
 * $glue = "|";
 * echo filtered_implode($glue, $list); // 1|2|4|6
 * echo filtered_implode($glue, $list, FILTERED_IMPLODE_BOTH); // |1|2|4|6|
 * </code>
 *
 * @param string $glue
 * @param arrau $list                 :       ,      .
 * @param const $add_glue_to_end                  FILTERED_IMPLODE_NOWHERE,
 * FILTERED_IMPLODE_BOTH, FILTERED_IMPLODE_LEFT, FILTERED_IMPLODE_RIGHT.
 */
function filtered_implode($glue, $list, $add_glue_to_end = FILTERED_IMPLODE_NOWHERE)
{
    $str = implode($glue, array_filter($list));
    if (empty($str))
    {
        return $str;
    }
    else
    {
        $_left = '';
        $_right = '';
        switch($add_glue_to_end)
        {
            case FILTERED_IMPLODE_BOTH:
                $_left = $_right = $glue;
                break;
            case FILTERED_IMPLODE_LEFT:
                $_left = $glue;
                break;
            case FILTERED_IMPLODE_RIGHT:
                $_right = $glue;
                break;
        }
        $func = create_function('$var','return !isempty($var);');
        return $_left . implode($glue, array_filter($list, $func)) . $_right;
    }
}

/**
 * From http://www.php.net/manual/en/function.empty.php#74093
 *
 * The following things are considered to be empty:
 * unset variable ($unset)
 * empty string ($var = "")
 * null string ($var = null)
 * single space ($var = " ")
 * several space ($var = "    ")
 * empty array ($var = array())
 */
function isempty($var)
{
    if (  ( is_array($var) && empty($var) )
         or
         ( !is_array($var) && (is_null($var) || rtrim($var) == "") && $var !== false )
       )
    {
        return true;
    }
    else
    {
        return false;
    }
}

function ife($expression, $if_true_value, $if_false_value)
{
    return ($expression ? $if_true_value : $if_false_value);
}

function execQuery($query_name, $params=null, $do_not_use_memory_cache = CCACHE_USE_MEMORY_CACHE)
{

    global $application;
    $q = &$application->getInstance('CQueryExecuter');
    return $q->exec($query_name, $params, '-direct-', false, $do_not_use_memory_cache);
}

function printQuery($query_name, $params)
{
    global $application;
    $query_obj = new $query_name();
    $query_obj->initQuery($params);
    _print($application->db->_getSQL($query_obj), $query_name);
}

function execQueryPaginator($query_name, $params)
{
    $lines_number = execQueryCount($query_name, $params);
    return modApiFunc('paginator', 'getQueryLimits', $lines_number);
}

function execQueryCount($query_name, $params)
{
    global $application;
    $q = &$application->getInstance('CQueryExecuter');
    return $q->getQueryCount($query_name, $params);
}

function clearQueriesCache($table_list)
{
    global $application;
    $cache = &$application->getInstance('CQueryExecuter');
    $cache->clearCache($table_list);
}

/**
 *                  ,            $object                  $class.
 *                                                  .
 *
 * @return bool             $object                  $class,
 *                           TRUE,       FALSE
 * @param object $object
 * @param string $class
 */
function _is_a($object, $class)
{
    if (!is_object($object))
    {
        return false;
    }

    if (!class_exists($class))
    {
        die("FATAL ERROR: Class $class has not been defined.");
    }

    if (_ml_strtolower(get_class($object)) == _ml_strtolower(trim($class)))
    {
        return true;
    }
    else
    {
        return is_subclass_of($object, $class);
    }
}

function toMySQLDatetime($timestamp)
{
    return date("Y-m-d H:i:s",$timestamp);
}



/**
 *            ,     xmsg,                                  ,
 *         .
 */
function getMsg()
{
    global $application;
/*    if($application === NULL)
    {
    	//                    . $application                   .
        _fatal(array( "CODE" => "CORE_060"), __FUNCTION__);
    }
*/

    $arg_list = func_get_args();
    if (_ml_strtoupper($arg_list[0]) == 'SYS')
    {
        array_shift($arg_list);
        $obj = &$application->getInstance('MessageResources');
        $output = $obj->getMessage( new ActionMessage($arg_list) );
    }
    else
    {
        $obj = &$application->getInstance('MessageResources', modApiFunc("Modules_Manager","getResFileByShortName",$arg_list[0]), 'AdminZone', $arg_list[0]);
        array_shift($arg_list);
        $output = $obj->getMessage( new ActionMessage($arg_list) );
    }
    return $output;
}

//                      .
//                                              .
//                   ,        UNIX.
//                                           .
//                                       .
function file_path_cmp($path1, $path2)
{
	$path1 = str_replace('\\', '/', $path1);
    $path1 = str_replace('//', '/', $path1);

    $path2 = str_replace('\\', '/', $path2);
    $path2 = str_replace('//', '/', $path2);

    loadCoreFile('bouncer.php');
    $bnc = new Bouncer();
	$bnc->detect_OS();
	if($bnc->_os_type == "win")
	{
		return _ml_strcasecmp($path1, $path2);
	}
    else
    {
        return strcmp($path1, $path2);
    }
}

function getVisitorIP()
{
    $address_list = array();
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        foreach( array_reverse( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) as $item )
        {
            $item = trim($item);
            if ( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $item ) )
            {
                $address_list[] = $item;
            }
        }
    }
    if (isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $address_list[] = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR']))
    {
        $address_list[] = $_SERVER['REMOTE_ADDR'];
    }
    if (isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER']))
    {
        $address_list[] = $_SERVER['HTTP_PROXY_USER'];
    }

    $address = '';
    if (count($address_list) >= 1)
    {
        $address = $address_list[0];
    }
    else
    {
        return $address;
    }

    return preg_replace( "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "$1.$2.$3.$4", $address );
}

function getRemoteHostByIP($ip)
{
    if (!empty($ip))
    {
        return @gethostbyaddr($ip);
    }
    else
    {
        return '';
    }
}

function getCurrentURL()
{
	static $current_url = null;
	if ($current_url == null)
	{
	    $URL = $_SERVER["HTTP_HOST"];
	    if (isset($_SERVER['QUERY_STRING']) and !empty($_SERVER['QUERY_STRING']))
	    {
	        $URL.= $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
	    }
	    elseif (isset($_SERVER['REQUEST_URI']) and !empty($_SERVER['REQUEST_URI']))
	    {
	        $URL.= $_SERVER['REQUEST_URI'];
	    }
	    else
	    {
	        $URL.= $_SERVER['PHP_SELF'];
	    }
	    # remove security-critical parameters from URL
	    if (preg_match("/[CAZPH]*SESSID=([a-z0-9]*)/i", $URL, $m))
	    {
	        $URL = preg_replace("/".$m[1]."/i","***",$URL);
	    }
	    if (preg_match("/CHECKOUT_CZ_BLOWFISH_KEY=([a-z0-9]*)/i",$URL, $m))
	    {
	        $URL = preg_replace("/".$m[1]."/i","***",$URL);
	    }
	    $protocol = (isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER["HTTPS"] === true))? "https":"http";
	    $current_url = $protocol.'://'.$URL;
	}
    return $current_url;
}


function getIndexWordsFromText($text, $max_text_len_to_scan = 1024, $min_len_index_word = 1, $max_len_index_word = 50)
{
    loadCoreFile('cstring.php');
    $cstring = new CString();

    $text = _ml_substr($text, 0, $max_text_len_to_scan);
    $text = $cstring->stripHTML($text);

    // strip punctuation
    $chars = array("$",".","!","?","@",",","#","%","^","&","*","(",")","_","+","=","(",")","{","}","[","]","\\","|",";",":","\"","<",">","/","~","-","'");
    $search = array();
    $replace = array();
    $search[] = "/([" . implode("\\", $chars) . "]+)/";
    $replace[]= " ";
    $text = preg_replace($search, $replace, $text);

    $text = $cstring->mergeWhiteSpace($text);
    $words = explode(" ",$text);

    // one more, remove wrong-length words
    foreach($words as $key => $word)
    {
        if(0==_ml_strlen($word) ||
           _ml_strlen($word)<$min_len_index_word ||
           _ml_strlen($word)>$max_len_index_word)
        {
            unset($words[$key]);
        }
    }

    return $words;
}


if (version_compare(phpversion(), '5.0') < 0)
{
    eval('function clone($object) { return $object;}');
}

function convertTemplate($text)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        $text = mb_convert_encoding($text, $application -> multilang_core -> _internal_charset, $application -> getAppIni('TEMPLATE_CHARSET'));

    return $text;
}

function convertResource($text)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        $text = mb_convert_encoding($text, $application -> multilang_core -> _internal_charset, $application -> getAppIni('TEMPLATE_CHARSET'));

    return $text;
}

function convertResourceArray($text_array)
{
    if (!is_array($text_array))
        return convertResource($text_array);

    foreach($text_array as $k => $v)
        if (is_array($v))
            $text_array[$k] = convertResourceArray($v);
        else
            $text_array[$k] = convertResource($v);

    return $text_array;
}

function convertImportData($text)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        $text = mb_convert_encoding($text, $application -> multilang_core -> _internal_charset, $application -> getAppIni('IO_CHARSET'));

    return $text;
}

function convertImportDataArray($text_array)
{
    if (!is_array($text_array))
        return convertImportData($text_array);

    foreach($text_array as $k => $v)
        if (is_array($v))
            $text_array[$k] = convertImportDataArray($v);
        else
            $text_array[$k] = convertImportData($v);

    return $text_array;
}

function convertExportData($text)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        $text = mb_convert_encoding($text, $application -> getAppIni('IO_CHARSET'), $application -> multilang_core -> _internal_charset);

    return $text;
}

function convertExportDataArray($text_array)
{
    if (!is_array($text_array))
        return convertExportData($text_array);

    foreach($text_array as $k => $v)
        if (is_array($v))
            $text_array[$k] = convertExportDataArray($v);
        else
            $text_array[$k] = convertExportData($v);

    return $text_array;
}

function convertData($data, $_to, $_from)
{
    global $application;

    if (!$application -> multilang_core -> _mb_enabled)
        return $data;

    if (!is_array($data))
        return mb_convert_encoding($data, $_to, $_from);

    foreach($data as $k => $v)
    {
        if (is_array($v))
            $data[$k] = convertData($v, $_to, $_from);
        else
            $data[$k] = mb_convert_encoding($v, $_to, $_from);
    }

    return $data;
}

function convertInputData($_to, $_from)
{
    $_POST = convertData($_POST, $_to, $_from);
    $_GET = convertData($_GET, $_to, $_from);
    $_COOKIE = convertData($_COOKIE, $_to, $_from);
    $_FILES = convertData($_FILES, $_to, $_from);
}

/**
 * Overloading base mb_string functions to be used instead of native ones
 * Begin
 */
function _ml_mail($to, $subject, $message, $additional_headers = NULL, $additional_parameters = NULL)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_send_mail($to, $subject, $message, $additional_headers, $additional_parameters);

    if ($additional_parameters !== NULL)
        return mail($to, $subject, $message, $additional_headers, $additional_parameters);

    if ($additional_headers !== NULL)
        return mail($to, $subject, $message, $additional_headers);

    return mail($to, $subject, $message);
}

function _ml_strlen($string)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_strlen($string);

    return strlen($string);
}

function _ml_strpos($haystack, $needle, $offset = 0)
{
    if ($needle == '') return '';

    global $application;
    if ($application -> multilang_core && $application -> multilang_core -> _mb_enabled)
        return mb_strpos($haystack, $needle, $offset);

    return strpos($haystack, $needle, $offset);
}

/**
 * mb_strrpos has offset from 5.2.0
 * while strrpos has offset from 5.0.0
 * we need to check version...
 */
function _ml_strrpos($haystack, $needle, $offset = 0)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
    {
        if (version_compare(PHP_VERSION, '5.2.0', '>='))
            return mb_strrpos($haystack, $needle, $offset);

        return mb_strrpos($haystack, $needle);
    }

    if (version_compare(PHP_VERSION, '5.0.0', '>='))
        return strrpos($haystack, $needle, $offset);

    return strrpos($haystack, $needle);
}

function _ml_substr($string, $start, $length = NULL)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
    {
        if ($length !== NULL)
            return mb_substr($string, $start, $length);

        return mb_substr($string, $start);
    }

    if ($length !== NULL)
        return substr($string, $start, $length);

    return substr($string, $start);
}

function _ml_strtolower($string)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_strtolower($string);

    return strtolower($string);
}

function _ml_strtoupper($string)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_strtoupper($string);

    return strtoupper($string);
}

function _ml_substr_count($haystack, $needle)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_substr_count($haystack, $needle);

    return substr_count($haystack, $needle);
}


function _byte_strlen($string)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_strlen($string, 'ISO-8859-1');

    return strlen($string);
}

function _byte_substr($string, $start, $length = NULL)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
    {
        if ($length !== NULL)
            return mb_substr($string, $start, $length, 'ISO-8859-1');

        return mb_substr($string, $start, _byte_strlen($string), 'ISO-8859-1');
    }

    if ($length !== NULL)
        return substr($string, $start, $length);

    return substr($string, $start);
}

function _byte_strpos($haystack, $needle, $offset = 0)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_strpos($haystack, $needle, $offset, 'ISO-8859-1');

    return strpos($haystack, $needle, $offset);
}

/**
 * mb_strrpos has offset from 5.2.0
 * while strrpos has offset from 5.0.0
 * we need to check version...
 */
function _byte_strrpos($haystack, $needle, $offset = 0)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
    {
        if (version_compare(PHP_VERSION, '5.2.0', '>='))
            return mb_strrpos($haystack, $needle, $offset, 'ISO-8859-1');

        return mb_strrpos($haystack, $needle, 'ISO-8859-1');
    }

    if (version_compare(PHP_VERSION, '5.0.0', '>='))
        return strrpos($haystack, $needle, $offset);

    return strrpos($haystack, $needle);
}

/**
 * End
 */

/**
 * Overloading text functions
 */

/**
 * Chr is replaced with 3 functions
 * 1. bytechr - for binary operations
 * 2. unichr - returns unicode symbol by its code
 * 3. chr1251 - returns utf-8 symbol by its code in win-1251
 */
function _byte_chr($code)
{
    return chr($code);
}

function unichr($code)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_convert_encoding('&#' . intval($code) . ';', 'UTF-8',
                                                               'HTML-ENTITIES');

    return chr($code);
}

function chr1251($code)
{
    global $application;
    if ($application -> multilang_core -> _mb_enabled)
        return mb_convert_encoding(chr($code), 'UTF-8', 'windows-1251');

    return chr($code);
}

/**
 * Ord is replaced with 3 functions
 * 1. byteord - for binary operations
 * 2. uniord - returns unicode for first symbol
 * 3. ord1251 - returns win-1251 code for utf-8 char (no checking!)
 */
function _byte_ord($c)
{
    return ord($c);
}

function uniord($c) {
    global $application;
    if (!$application -> multilang_core -> _mb_enabled)
        return ord($c);

    $h = ord($c{0});
    if ($h <= 0x7F)
    {
        return $h;
    }
    elseif ($h < 0xC2)
    {
        return false;
    }
    elseif ($h <= 0xDF)
    {
        return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
    }
    elseif ($h <= 0xEF)
    {
        return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                                 | (ord($c{2}) & 0x3F);
    }
    elseif ($h <= 0xF4)
    {
        return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                                 | (ord($c{2}) & 0x3F) << 6
                                 | (ord($c{3}) & 0x3F);
    }
    else
    {
        return false;
    }
}

function ord1251($c)
{
    global $application;
    if (!$application -> multilang_core -> _mb_enabled)
        return ord($c);

    return ord(mb_convert_encoding($c, 'windows-1251', 'UTF-8'));
}

/**
 * htmlentities
 */
function _ml_htmlentities($string, $quote_style = ENT_COMPAT, $charset = NULL)
{
    global $application;

    if ($charset !== NULL)
        return htmlentities($string, $quote_style, $charset);

    return htmlentities($string, $quote_style, $application -> multilang_core -> _internal_charset);
}

/**
 * html_entity_decode
 */
function _ml_html_entity_decode($string, $quote_style = ENT_COMPAT, $charset = NULL)
{
    global $application;

    if ($charset !== NULL)
        return html_entity_decode($string, $quote_style, $charset);

    return html_entity_decode($string, $quote_style, $application -> multilang_core -> _internal_charset);
}

/**
 * stristr (mb_stristr is available only from php 5.2.0)
 */

function _ml_stristr($haystack, $needle)
{
    global $application;

    if (!$application -> multilang_core -> _mb_enabled)
        return stristr($haystack, $needle);

    $result = strstr(_ml_strtolower($haystack), _ml_strtolower($needle));
    if (!$result)
        return $result;

    return _ml_substr($haystack, _ml_strlen($haystack) - _ml_strlen($result));
}

/**
 * ucfirst (mb_convert_case is available from php 4.3.0)
 */
function _ml_ucfirst($string)
{
    global $application;

    if ($application -> multilang_core -> _mb_enabled)
        return mb_convert_case($string, MB_CASE_TITLE);

    return ucfirst($string);
}

/**
 * strcasecmp
 */
function _ml_strcasecmp($str1, $str2)
{
    return strcmp(_ml_strtolower($str1), _ml_strtolower($str2));
}

/**
 * strncasecmp
 */
function _ml_strncasecmp($str1, $str2, $len)
{
    return strncmp(_ml_strtolower($str1), _ml_strtolower($str2), $len);
}

/**
 * strnatcasecmp
 */
function _ml_strnatcasecmp($str1, $str2)
{
    return strnatcmp(_ml_strtolower($str1), _ml_strtolower($str2));
}

/**
 * strrev is replaced with 2 functions
 * bytestrrev - reverse byte by byte
 * _ml_strrev - reverse symbol by symbol
 */
function _byte_strrev($string)
{
    return strrev($string);
}

function _ml_strrev($string)
{
    $result = '';
    for($i = _ml_strlen($string) - 1; $i >= 0; $i--)
        $result .= _ml_substr($string, $i, 1);

    return $result;
}

/**
 * substr_replace
 */
function _ml_substr_replace($string, $replacement, $start, $length = NULL)
{
    global $application;

    if ($length === NULL)
        $length = _ml_strlen($string);

    if (!$application -> multilang_core -> _mb_enabled)
        return substr_replace($string, $replacement, $start, $length);

    $before = _ml_substr($string, 0, $start);
    $after = '';
    if ($start + $length < _ml_strlen($string))
        $after = _ml_substr($string, $start + $length);

    return $before . $replacement . $after;
}

function _byte_substr_replace($string, $replacement, $start, $length = NULL)
{
    if ($length === NULL)
        $length = _byte_strlen($string);

    return substr_replace($string, $replacement, $start, $length);
}

/**
 * strrchr
 */
function _ml_strrchr($haystack, $needle)
{
    global $application;
    if (!$application -> multilang_core -> _mb_enabled)
        return strrchr($haystack, $needle);

    if (!is_string($needle))
        $needle = unichr(intval($needle));

    $needle = _ml_substr($needle, 0, 1);

    if (_ml_strpos($haystack, $needle) === false)
        return false;

    $result = explode($needle, $haystack);
    $result = array_pop($result);

    return $needle . $result;
}

/**
 * stripos
 */
function _ml_stripos($haystack, $needle, $offset = 0)
{
    global $application;
    if (!$application -> multilang_core -> _mb_enabled)
        return stripos($haystack, $needle, $offset);

    return _ml_strpos(_ml_strtolower($haystack), _ml_strtolower($needle), $offset);
}

/**
 * str_split
 */
function _ml_str_split($string, $split_length = 1)
{
    $result = array();
    for ($i = 0; $i < _ml_strlen($string); $i += $split_length)
        $result[] = _ml_substr($string, $i, $split_length);

    return $result;
}

/**
 * wordwrap
 */
function _ml_wordwrap($str, $width = 75, $break = "\n", $cut = false)
{
    $words = explode(' ', $str);
    $pieces = array();
    $cur_piece = '';

    foreach($words as $word)
    {
        if (_ml_strlen($cur_piece . ' ' . $word) > $width)
        {
            if ($cur_piece)
                $pieces[] = $cur_piece;
            $cur_piece = $word;
        }
        elseif ($cur_piece)
        {
            $cur_piece .= ' ' . $word;
        }
        else
        {
            $cur_piece = $word;
        }
        while ($cut && _ml_strlen($cur_piece) > $width) {
            $pieces[] = _ml_substr($cur_piece, 0, $width);
            $cur_piece = _ml_substr($cur_piece, $width);
        }
    }
    if ($cur_piece)
        $pieces[] = $cur_piece;

    return join($break, $pieces);
}

/*
//                                        debug_backtrace.
function setCurrentStepID($step_id)
{
   $debug_backtrace = debug_backtrace();
   asc_file_put_contents('c:/var/debug_backtrace.txt', print_r( array_map("__foo", $debug_backtrace) ,true ) );
   $this->currentStepID = $step_id;
}

Alexey_Florinsky (16:15:57 12/10/2007)
function __foo($bar)
{
   unset($bar['object']);
//    unset($bar['args']);
   return $bar;
}
*/

function push_js_css()
{
    global $application;
    if ($application->getAppIni('PUSH_JS_CSS') == 'yes') {
        @ob_flush();
        @flush();
    }
}

function get_extfiles_versions($files, $sets)
{
    $root = dirname(dirname(__FILE__)).'/admin/';
    $versions = array();
    if (is_array($files)) {
        foreach ($sets as $s) {
            if (is_array($files[$s])) {
                foreach ($files[$s] as $k => $f) {
                    if (is_numeric($k)) {
                        $versions[$f] = @ filemtime($root.$f);
                    }
                }
            }
        }
    }
    return $versions;
}

function get_sets_versions($files, $sets, $default_path, $prefix, $postfix)
{
    $root = dirname(dirname(__FILE__)).'/admin/';
    $versions = array();
    if (is_array($sets)) {
        foreach ($sets as $s) {
            $path = $default_path;
            if (isset($files[$s]) && isset($files[$s]['compressed_path'])) {
                $path = $files[$s]['compressed_path'];
            }
            $f = $path.$prefix.$s.$postfix;
            $versions[$f] = filemtime($root.$f);
        }
    }
    return $versions;
}

/**
 * Returns absolute path to template file
 * @relative_path - path to template file from the template root
 * if the file exists in the current template dir -> returns it
 * otherwise if the file exists in the system template dir -> returns it
 * otherwise returns the path in the current template dir for error reporting
 */
function getTemplateFileAbsolutePath($relative_path,$current_template_path=null)
{
    global $__TPL_DIR__;
    global $__SYSTEM_TPL_DIR__;

    if (is_file($__TPL_DIR__ . $relative_path)
        && is_readable($__TPL_DIR__ . $relative_path))
        return $__TPL_DIR__ . $relative_path;

    if (is_file($__SYSTEM_TPL_DIR__ . $relative_path)
        && is_readable($__SYSTEM_TPL_DIR__ . $relative_path))
        return $__SYSTEM_TPL_DIR__ . $relative_path;
    /**
     * Below condition check the file is present in the extension
     * path given in the block ini files of the extensions
     */
    if(isset($current_template_path))
	{
		if(is_file($current_template_path.$relative_path)
				&& is_readable($current_template_path.$relative_path))
			return $current_template_path.$relative_path;

		if(is_file($current_template_path."/templates/".$relative_path)
				&& is_readable($current_template_path."/templates/".$relative_path))
			return $current_template_path."/templates/".$relative_path;
	}

    return $__TPL_DIR__ . $relative_path;
}

function getTemplateFileExactAbsolutePath($relative_path)
{
    global $__TPL_DIR__;
    return $__TPL_DIR__ . $relative_path;
}

function getTemplateFileByPattern($pattern)
{
    global $__TPL_DIR__;
    global $__SYSTEM_TPL_DIR__;
	CTrace::dbg('Pattern: ' . $pattern);

	// files from $__TPL_DIR__ (priority files)
	$files_user_skin = glob($__TPL_DIR__ . $pattern);
	CTrace::dbg('User\'s skin files: ', $files_user_skin);

	// files from $__SYSTEM_TPL_DIR__
	$files_system_skin = glob($__SYSTEM_TPL_DIR__ . $pattern);
	CTrace::dbg('System\'s skin files: ', $files_system_skin);

	$result = array();
	foreach($files_user_skin as $f)
	{
		$result[] = str_replace($__TPL_DIR__, '', $f);
	}
	foreach($files_system_skin as $f)
	{
		$result[] = str_replace($__SYSTEM_TPL_DIR__, '', $f);
	}
	$result = array_unique($result);
	CTrace::dbg('Relative pathes by pattern (merge of user skin files and system skin files): ', $result);

	return $result;
}

/**
 * The same for directories
 */
function getTemplateDirAbsolutePath($relative_path)
{
    global $__TPL_DIR__;
    global $__SYSTEM_TPL_DIR__;

    if (is_dir($__TPL_DIR__ . $relative_path)
        && is_readable($__TPL_DIR__ . $relative_path))
        return $__TPL_DIR__ . $relative_path;

    if (is_dir($__SYSTEM_TPL_DIR__ . $relative_path)
        && is_readable($__SYSTEM_TPL_DIR__ . $relative_path))
        return $__SYSTEM_TPL_DIR__ . $relative_path;

    return $__TPL_DIR__ . $relative_path;
}

/**
 * Returns url to template file
 * @relative_path - path to template file from the template root
 * if the file exists in the current template dir -> returns it
 * otherwise if the file exists in the system template dir -> returns it
 * otherwise returns the path in the current template dir for error reporting
 */
function getTemplateFileURL($relative_path)
{
    global $__TPL_DIR__;
    global $__SYSTEM_TPL_DIR__;

    global $__TPL_URL__;
    global $__SYSTEM_TPL_URL__;

    if (is_file($__TPL_DIR__ . $relative_path)
        && is_readable($__TPL_DIR__ . $relative_path))
        return $__TPL_URL__ . $relative_path;

    if (is_file($__SYSTEM_TPL_DIR__ . $relative_path)
        && is_readable($__SYSTEM_TPL_DIR__ . $relative_path))
        return $__SYSTEM_TPL_URL__ . $relative_path;

    return $__TPL_URL__ . $relative_path;
}

function getTemplateFileExactURL($relative_path)
{
    global $__TPL_URL__;
    return $__TPL_URL__ . $relative_path;
}

/**
 * The same for directories
 */
function getTemplateDirURL($relative_path)
{
    global $__TPL_DIR__;
    global $__SYSTEM_TPL_DIR__;

    global $__TPL_URL__;
    global $__SYSTEM_TPL_URL__;

    if (is_dir($__TPL_DIR__ . $relative_path)
        && is_readable($__TPL_DIR__ . $relative_path))
        return $__TPL_URL__ . $relative_path;

    if (is_dir($__SYSTEM_TPL_DIR__ . $relative_path)
        && is_readable($__SYSTEM_TPL_DIR__ . $relative_path))
        return $__SYSTEM_TPL_URL__ . $relative_path;

    return $__TPL_URL__ . $relative_path;
}

function escapeJSScript($str)
{
	return strtr($str, array('\\' => '\\\\', '\'' => '\\\'', '"' => '\\"'));
}

function unescapeJSScript($str)
{
	return strtr($str, array('\\\\' => '\\', '\\\'' => '\'', '\\"' => '"'));
}

function escapeHTML($str)
{
    return strtr($str, array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;'));
}

function unescapeHTML($str)
{
    return strtr($str, array('&amp;' => '&', '&lt;' => '<', '&gt;' => '>'));
}

function escapeAttr($str)
{
    return strtr($str, array('\'' => '&#039;', '"' => '&quot;'));
}

function escapeAttrAny($val)
{
	if (is_scalar($val)) {
		return escapeAttr($val);
	}
	return array_map('escapeAttrAny', $val);
}

function unescapeAttr($str)
{
    return strtr($str, array('&amp;' => '&', '&#039;' => '\'', '&quot;' => '"'));
}

function escapeAttrHTML($str)
{
    return escapeAttr(escapeHTML($str));
}

function escapeJSString($str)
{
	return strtr($str, array('\\' => '\\\\', '\'' => '\\\'', '"' => '\\"', "\n" => '\\n', "\r" => '\\r'));
}

function unescapeJSString($str)
{
	return strtr($str, array('\\\\' => '\\', '\\\'' => '\'', '\\"' => '"', '\\n' => "\n", '\\r' => "\r"));
}

function escapeAttrJS($str)
{
	return escapeAttr(escapeJSString($str));
}

function escapeUrlParam($str)
{
    return urlencode($str);
}

function escapeAttrUrlParam($str)
{
    return escapeAttr(escapeUrlParam($str));
}

function escapeEmailName($str)
{
    return strpbrk($str, '()<>[];:,.@\\"') !== false ? '"'.strtr($str, array('"' => '\\"', '\\' => '\\\\')).'"' : $str;
}

function asArray($param)
{
	return isset($param) && is_array($param) ? $param : array();
}

function prepareRE($str)
{
    return strtr($str, array (
        '.' => '\\.',
        '(' => '\\(',
        ')' => '\\)',
        '{' => '\\{',
        '}' => '\\}',
        '[' => '\\[',
        ']' => '\\]',
        '/' => '\\/',
	'-' => '\\-',
	' ' => '\\s',
    ));
}

function formatDate($date)
{
    return modApiFunc('Localization', 'date_format', $date);
}

function formatCurrency($value)
{
    return modApiFunc('Localization', 'currency_format', $value);
}

function intercept( $var, $include_backtrace = false )
{
	global $application;

    $message = "\n".str_repeat( '=', 100 )."\n";

    if( $include_backtrace )
    {
        $prefix = '';
        $btr = debug_backtrace();
        krsort( $btr );
        $inst_dir = $application->getAppIni('PATH_ASC_ROOT');

        foreach( $btr as $p )
        {
            $message .= $prefix.str_replace( $inst_dir, '', $p['file'] ).', line '.$p['line'].', function '
                       .$p['object'].$p['type'].$p['function'].'( '.implode(', ',$p['args'])." )\n";
            $prefix .= ' ';
        }
    }

	$message .= print_r( $var, true );

	$debugLogFile = $application->getAppIni('PATH_CACHE_DIR') . 'intercepted.txt';

	$file = new CFile($debugLogFile);
	$file->appendContent($message);
}

function image_manager_path()
{
	if(isset($_GET['catalog']) && $_GET['catalog']=='yes')
		return 'avactis-images/';
	else
		return 'avactis-images/u/';
}

function admin_storefront_url()
{
	global $application;
	$admin_url = $application->getAppIni('HTTP_URL');
        if ($application->getCurrentProtocol() == 'https')
            $admin_url = $application->appIni['HTTPS_URL'];
	return $admin_url;
}

function site_url()
{
	 return urlStorefrontBase();
}
function is_admin()
{
 //    : write a function to check correct admin permissions
	return modApiFunc('Users','isUserSignedIn');
}
function urlStorefrontBase()
{
	global $application;
    $storefront_list = array();
    $config_array = LayoutConfigurationManager::static_get_cz_layouts_list();
    foreach($config_array as $k => $v) {
    	if (preg_match('/^.*\.ini$/', $v['PATH_LAYOUTS_CONFIG_FILE'])) {
    		if($application->getCurrentProtocol() == "https")
	    		$storefront_list[] = $application->appIni['HTTPS_URL'];
		else
		        $storefront_list[] = $v['SITE_URL'];
    	}
    }
    return sizeof($storefront_list) ? reset($storefront_list) : '';
}

function combineFiles($pattern)
{
    $files = glob($pattern);
    $str = '';
    if (is_array($files)) {
        foreach ($files as $file) {
            $str .= file_get_contents($file);
            $str .= ' ';
        }
    }
    return trim($str);
}

function lang()
{
    echo strtolower(modApiFunc("MultiLang", "getLanguage"));
}

function pagelang()
{
    $lang = strtolower(modApiFunc("MultiLang", "getLanguage"));
    if (!$lang)
        echo 'en';

    echo $lang;
}

function getClientIPs()
{
    $fields = array('REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED');
    $ips = array();
    foreach ($fields as $field) {
        if (isset($_SERVER[$field])) {
            $ips = array_merge($ips, explode(',', $_SERVER[$field]));
        }
    }
    $ips = array_unique(array_filter(array_map('trim', $ips)));
    //CTrace::dbg('Client IPs:', $ips);
    return $ips;
}

function FreeVersionRestriction($addCloseButton=true)
{

    $text = <<<EOD
<table class="form" cellspacing="1" cellpadding="0" width="100%">  <tr>     <td width="100%" style="padding-left: 4px; padding-right: 4px;">You are using the Free version of Avactis Shopping Cart. This feature is available only in the commercial version. You may see how this feature works by viewing a demo at <a target='_blank' href='http://www.avactis.com/demo.php
EOD
    . '?source=FreeVersion_'.PRODUCT_DISTRIBUTOR.'&utm_campaign=FreeVersion&utm_source='.PRODUCT_DISTRIBUTOR.'&utm_medium=TopBanner'
    . <<<EOD
'>http://www.avactis.com/demo.php</a><br><br><b>The commercial version offers the following benefits:</b><ul><li>Regular free updates<li>Prompt technical support<li>Marketing support for your online store<li>Supports online shipping services, including UPS, USPS, FedEx andmore. <li>Supports payments systems such as Authorize.net, 2Checkout, Google Checkout, etc.<li>Expanded functionality of Avactis Shopping Cart<li>All source codes provided (commercial open source)</ul><b>To buy the commercial version, visit</b> <a target='_blank' href='https://www.avactis.com/avactis-downloadable/'>http://www.avactis.com/avactis-downloadable/</a>
     </td>
  </tr>
  <tr>
     <td width="100%" style="padding: 4px; " align="center">
EOD;

  if($addCloseButton)
    $text .= '<div class="button button_small" onClick="closeAndFocusParent();">Close</div>';

  echo $text . '</td></tr></table>';
}

function getProductAttributeId($view_tag_name, $default_value=null)
{
    global $application;

    if(!is_object($application))
        return $default_value;

    $attr_ids_cache = $application->getAttrIdsCache();
    $attr_id = $attr_ids_cache->read($view_tag_name);
    if($attr_id == null)
    {
        if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX')."attributes") != null)
        {
            $s = new DB_Select();
            $s->addSelectTable("attributes");
            $s->addSelectField('attribute_id','attribute_id');
            $s->WhereValue('attribute_view_tag', DB_EQ, $view_tag_name);
            $m = new DB_MySQL();
            $m->PrepareSQL($s);
            $result = $m->getDB_Result($s);
            $attr_id = !empty($result) ? $result[0]['attribute_id'] : $default_value;
        }
        else
        {
            $attr_id = $default_value;
        }
        $attr_ids_cache->write($view_tag_name, $attr_id);
    }

    return $attr_id;
}

function cutTemplatesPathes($content)
{
    static $ADD_TEMPLATE_PATHES = null;
    if ($ADD_TEMPLATE_PATHES === null)
    {
        $ADD_TEMPLATE_PATHES = modApiFunc('Settings','getParamValue', 'DEBUG_STORE_BLOCK', 'ADD_TEMPLATE_PATHES');
    }
    if ($ADD_TEMPLATE_PATHES === 'Yes')
    {
        return str_replace("\n###ASC_ADD_TEMPLATE_PATHES_TOKEN###\n",'',preg_replace('/(\<\!\-\-(.*?)(?!\!\<\-\-)(.*?)-->)/','###ASC_ADD_TEMPLATE_PATHES_TOKEN###',$content));
    }
    return $content;
}


/**Functions for zipping **/
function recursive_zip($src,&$zip,$path,$exclude){
	$dir = opendir($src);
	while(false !== ( $file = readdir($dir)) )
	{
		if (!(array_search($file,$exclude) > -1))/**************************multiple directory excluding****************************/
		{
			if (( $file != '.' ) && ( $file != '..' ))
			{
				if ( is_dir($src . '/' . $file) )
				{
					recursive_zip($src . '/' . $file,$zip,$path,$exclude);
				}
				else
				{
					$zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path));
				}

			}
		}

	}
	closedir($dir);
}
function createZip($src,$dst,$filename,$exclude){
	if(substr($src,-1)==='/'){
		$src=substr($src,0,-1);
	}
	if(substr($dst,-1)==='/'){
		$dst=substr($dst,0,-1);
	}

	$path=strlen(dirname($src).'/');
	$dst=empty($dst)? $filename : $dst.'/'.$filename;

	@unlink($dst);
	//Additional permission check as ziparchive->open doesnt detect 644
	$fp = fopen($dst,"w+");
	if(!$fp){
		return $fp;
	}else{
		fclose($fp);
	}

	$zip = new ZipArchive;
	$res = $zip->open($dst, ZipArchive::CREATE);
	if($res !== TRUE )
	{
		return false;
	}
	if(is_file($src))
	{

		$zip->addFile($src,substr($src,$path));
	}
	else
	{
		if(!is_dir($src))
		{
			$zip->close();
			@unlink($dst);
			return false;
		}
		recursive_zip($src,$zip,$path,$exclude);
	}
	$zip->close();
	return true;
}
/**
 * This function is used to extract extension
 * It will unzip the zip file in the target folder
 */
function extractFiles($zip_path, $target)
{
	global $application;
	$success = false;
	$zip = new ZipArchive();

	if ($zip->open($zip_path) === TRUE)
	{
		if( $zip->extractTo($target)){
			$success = true;
		}
	}
	$zip->close();
	return $success;
}

/**Function for DB Backup **/
function backupDB_tables($dest,$backup_filename,$tables)
{
	global $application;
	//get all of the tables
	if((is_null($tables)) or ($tables == '') ){
		return true;
	}else if(($tables == '*')){
		$db_name=$application->getAppIni('DB_NAME');
		$tables = array();
		$i =0;
		$tables_list = $application->db->DB_Query("SHOW TABLES FROM `$db_name`");
		while ($table = $application->db->DB_Fetch_Array($tables_list, QUERY_RESULT_NUM))
		{
			$tables[$i++] = str_replace($application->getAppIni('DB_TABLE_PREFIX'),"",$table[0]);
		}

	}else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);

	}						$handle = fopen($dest."/".$backup_filename,'w+');
	if(!$handle){
		return false;
	}else{
		//cycle through
		foreach($tables as $table)
		{
			$result = "SELECT * FROM ".$application->getAppIni('DB_TABLE_PREFIX').$table;

			$result = $application->db->DB_Query($result);

			$num_fields = $application->db->DB_Num_Rows($result);
			$querycreate = "SHOW CREATE TABLE ".$application->getAppIni('DB_TABLE_PREFIX').$table;
			$dbresult = $application->db->DB_Query($querycreate);

			$createresult = $application->db->DB_Fetch_Array($dbresult, QUERY_RESULT_NUM);

			$return.= $createresult[1].";\n\n";

			for ($i = 0; $i < $num_fields; $i++)
			{
				while ($row = $application->db->DB_Fetch_Array($result, QUERY_RESULT_NUM))
				{
					$return.= 'INSERT INTO '.$application->getAppIni('DB_TABLE_PREFIX').$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++){
						if($row[$j] != ""){
							$row[$j] = addslashes($row[$j]);
							$row[$j] = str_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
							if ($j<($num_fields-1)) { $return.= ','; }
						}
					}
				$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		fwrite($handle,$return);
		fclose($handle);
		return true;
	}
}

/**
 * Retrieve a modified URL query string.
 *
 * You can rebuild the URL and append a new query variable to the URL query by
 * using this function. You can also retrieve the full URL with query data.
 *
 * Adding a single key & value or an associative array. Setting a key value to
 * an empty string removes the key. Omitting oldquery_or_uri uses the $_SERVER
 * value. Additional values provided are expected to be encoded appropriately
 * with urlencode() or rawurlencode().
 *
 * @since 4.7.0
 *
 * @param string|array $param1 Either newkey or an associative_array.
 * @param string       $param2 Either newvalue or oldquery or URI.
 * @param string       $param3 Optional. Old query or URI.
 * @return string New URL query string.
 */
function add_query_arg() {
	$args = func_get_args();
	if ( is_array( $args[0] ) ) {
		if ( count( $args ) < 2 || false === $args[1] )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = $args[1];
	} else {
		if ( count( $args ) < 3 || false === $args[2] )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = $args[2];
	}

	if ( $frag = strstr( $uri, '#' ) )
		$uri = substr( $uri, 0, -strlen( $frag ) );
	else
		$frag = '';

	if ( 0 === stripos( $uri, 'http://' ) ) {
		$protocol = 'http://';
		$uri = substr( $uri, 7 );
	} elseif ( 0 === stripos( $uri, 'https://' ) ) {
		$protocol = 'https://';
		$uri = substr( $uri, 8 );
	} else {
		$protocol = '';
	}

	if ( strpos( $uri, '?' ) !== false ) {
		list( $base, $query ) = explode( '?', $uri, 2 );
		$base .= '?';
	} elseif ( $protocol || strpos( $uri, '=' ) === false ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}

	asc_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
	if ( is_array( $args[0] ) ) {
		$kayvees = $args[0];
		$qs = array_merge( $qs, $kayvees );
	} else {
		$qs[ $args[0] ] = $args[1];
	}

	foreach ( $qs as $k => $v ) {
		if ( $v === false )
			unset( $qs[$k] );
	}

	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	return $ret;
}

/**
 * Build URL query based on an associative and, or indexed array.
 *
 * This is a convenient function for easily building url queries. It sets the
 * separator to '&' and uses _http_build_query() function.
 *
 * @since 2.3.0
 *
 * @see _http_build_query() Used to build the query
 * @see http://us2.php.net/manual/en/function.http-build-query.php for more on what
 *		http_build_query() does.
 *
 * @param array $data URL-encode key/value pairs.
 * @return string URL-encoded string.
 */
function build_query( $data ) {
	return _http_build_query( $data, null, '&', '', false );
}

/**
 * From php.net (modified by Mark Jaquith to behave like the native PHP5 function).
 *
 * @since 4.7.0
 * @access private
 *
 * @see http://us1.php.net/manual/en/function.http-build-query.php
 *
 * @param array|object  $data       An array or object of data. Converted to array.
 * @param string        $prefix     Optional. Numeric index. If set, start parameter numbering with it.
 *                                  Default null.
 * @param string        $sep        Optional. Argument separator; defaults to 'arg_separator.output'.
 *                                  Default null.
 * @param string        $key        Optional. Used to prefix key name. Default empty.
 * @param bool          $urlencode  Optional. Whether to use urlencode() in the result. Default true.
 *
 * @return string The query string.
 */
function _http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode)
			$k = urlencode($k);
		if ( is_int($k) && $prefix != null )
			$k = $prefix.$k;
		if ( !empty($key) )
			$k = $key . '%5B' . $k . '%5D';
		if ( $v === null )
			continue;
		elseif ( $v === FALSE )
		$v = '0';

		if ( is_array($v) || is_object($v) )
			array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
		elseif ( $urlencode )
		array_push($ret, $k.'='.urlencode($v));
		else
			array_push($ret, $k.'='.$v);
	}

	if ( null === $sep )
		$sep = ini_get('arg_separator.output');

	return implode($sep, $ret);
}

/**
 * Retrieve a list of protocols to allow in HTML attributes.
 *
 * @since 3.3.0
 *
 * @see asc_kses()
 * @see esc_url()
 *
 * @return array Array of allowed protocols.
 */
function asc_allowed_protocols() {
	static $protocols;

	if ( empty( $protocols ) ) {
		$protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp' );

		/**
		 * Filter the list of protocols allowed in HTML attributes.
		 *
		 * @since 3.0.0
		 *
		 * @param array $protocols Array of allowed protocols e.g. 'http', 'ftp', 'tel', and more.
		*/
		$protocols = apply_filters( 'kses_allowed_protocols', $protocols );
	}

	return $protocols;
}

/**
 * Convert a value to non-negative integer.
 *
 * @since 2.5.0
 *
 * @param mixed $maybeint Data you wish to have converted to a non-negative integer.
 * @return int A non-negative integer.
 */
function absint( $maybeint ) {
	return abs( intval( $maybeint ) );
}
?>