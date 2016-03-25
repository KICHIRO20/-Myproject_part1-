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
 * Layout_CMS class
 *
 * Common API class for Layout CMS.
 *
 * @author Alexey Astafyev
 * @version $Id$
 * @package Layout CMS
 */
class Layout_CMS
{
    function Layout_CMS()
    {
        global $application;

        $this->_PATH_CUR_THEME = $application->appIni['PATH_THEME'];
        $this->_PATH_CUR_THEME_PAGE_LAYOUTS = $this->_PATH_CUR_THEME.'pages/yaml_layouts/';
        $this->_editor_rows = $this->getData(PATH_SYSTEM_DATA.'editor_rows.yml');
    }

    function install()
    {
    	include_once(dirname(__FILE__) . '/includes/install.inc');
    }

    function getTables()
    {
    }

    function uninstall()
    {
    }

    function getThemePath()
    {
        return $this->_PATH_CUR_THEME;
    }

    function setTheme($theme)
    {
        global $application;
        $this->_PATH_CUR_THEME = $application->appIni['PATH_THEMES'].$theme.'/';
        $this->_PATH_CUR_THEME_PAGE_LAYOUTS = $this->_PATH_CUR_THEME.'pages/yaml_layouts/';
    }

    function getYAMLLayoutsPath()
    {
        return $this->_PATH_CUR_THEME_PAGE_LAYOUTS;
    }

    /**
     *  Returns row info for specified placeholder name
     *  @param $PH placeholder name
     *  @return array($rowID, $rowName, $rowType)
     */
    function getRowByPlaceholder($PH)
    {
        $i=0;
        foreach($this->_editor_rows as $rname=>$row)
        {
            if(in_array($PH, array_keys($row['placeholders'])))
                return array($i, $rname, $row['type']);
            $i++;
        }
        return array();
    }

    /**
     *  Returns layout data for specified page name
     *  @param $page Page name
     *  @param $restore Restoring flag. If is set, then return data from <$page>.restore.yml
     *  @param $no_json JSON flag. If is set, return layout data in JSON format
     */
    function getLayoutTmpl($theme, $page, $restore, $no_json=false)
    {
        global $zone;
        $this->setTheme($theme);

        $jsonData = array(
            'page_description' => $this->getPageDescription($page),
            'settings' => array(),
            'rows' => array()
        );
        $fname = $this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.(isset($restore) ? '.restore' : '').'.yml';

        if(file_exists($fname))
        {
            if(!file_exists($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.'.restore.yml') && $zone == "AdminZone")
            {
                copy($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.'.yml',
                     $this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.'.restore.yml');
                @chmod($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.'.restore.yml', 0666);
            }

            $YAMLData = $this->getData($fname);
            foreach($YAMLData['settings'] as $name=>$val)
                $jsonData['settings'][] = array('name' => $name, 'content' => $this->_unquote($val));

            $i=0; $tmpRowName = '';
            foreach($YAMLData['placeholders'] as $PHname=>$data)
            {
                list($rowN, $rowName, $rowType) = $this->getRowByPlaceholder($PHname);
                if($rowName!=$tmpRowName) { $i=0; $tmpRowName = $rowName; }
                $jsonData['rows'][$rowN]['name'] = $rowName;
                $jsonData['rows'][$rowN]['data']['type'] = $rowType;
                $jsonData['rows'][$rowN]['data']['placeholders'][$i] = array(
                    'name' => $PHname,
                    'data' => array('visibility' => $data['visibility'])
                );

                $PHblocks = $YAMLData['placeholders'][$PHname]['blocks'];
                if(!empty($PHblocks) && is_array($PHblocks))
                {
                    foreach($PHblocks as $bid=>$block)
                    {
                        $PHblocks[$bid] = $this->getBlockInfo($block['name'], $page);
                        if(!empty($PHblocks[$bid]['template']))
                            $PHblocks[$bid]['template'] = $this->_unquote($PHblocks[$bid]['template']);
                        $PHblocks[$bid]['visibility'] = $block['visibility'];
                    }
                    $jsonData['rows'][$rowN]['data']['placeholders'][$i]['data']['blocks'] = $PHblocks;
                }
                $i++;
            }

        }

        if($no_json==true) return $jsonData;
        loadCoreFile('JSON.php');
        $json = new Services_JSON();
        return $json->encode($jsonData);
    }

    /**
     *  Saves layout data to YAML
     *  @param $page Page name
     *  @param $rows Layout rows with placeholders and blocks
     *  @param $settings Page settings
     */
    function saveLayoutTmpl($theme, $page, $rows, $settings)
    {

        global $application;

        $cache = CCacheFactory::getCache('persistent', 'page_manager');
        $cache->erase(md5($theme.$page.'.fb'));
        $cache->erase(md5($theme.$page));

        $this->setTheme($theme);
        foreach($settings as $i=>$s)
        {
            $settings[$s['name']] = "'".$this->_desanitize_tags($s['value'])."'";
            unset($settings[$i]);
        }

        $PHs = array();             // placeholders list
        $blocks2save = array();     // blocks list
        foreach($rows as $row)
        {
            if(!empty($row['placeholders']))
            foreach($row['placeholders'] as $PH)
            {
                $blocks = array();
                if(isset($PH['blocks']) && is_array($PH['blocks']))
                    $blocks = $PH['blocks'];
                foreach($blocks as $i=>$block)
                {
                    $b2save = $block;
                    $b2save['template'] = "'".$this->_desanitize_tags($block['template'])."'";
                    unset($b2save['visibility']);
                    $blocks2save[] = $b2save;
                    unset($blocks[$i]['template']);
                    unset($blocks[$i]['title']);
                    unset($blocks[$i]['description']);
                }
                $PHs[$PH['name']] = array('visibility' => $PH['visibility'], 'blocks'=>$blocks);
            }
        }

        $data = array('settings'=>$settings, 'placeholders' => $PHs);
        $this->_writeYAML($page, $data);

        // save blocks content to a separate YAML
        $this->saveBlocks($page,$blocks2save);
        return 'OK';
    }

    /**
     *  Returns an array of info tags
     */
    function getSettingsList()
    {
        return $this->getData(PATH_SYSTEM_DATA.'system_info_tags.yml');
    }

    function YAMLblocks2array($YAMLblocks=array())
    {
        $blocks = array();
        foreach($YAMLblocks as $bid=>$params){
            $blocks[] = array(
                'page'        => (isset($params['page']) ? $params['page'] : 'any'),
                'name'        => $bid,
                'title'       => $params['title'],
                'description' => $params['description'],
                'template'    => (isset($params['template']) ? $params['template'] : "<?php $bid(); ?>")
            );
        }
        return $blocks;
    }

    /**
     *  Returns an array of predefined system blocks
     */
    function getBlocks()
    {
        global $application;

        $blocks = $this->YAMLblocks2array($this->getData(PATH_SYSTEM_DATA.'system_blocks.yml'));
        $mm = &$application->getInstance('Modules_Manager');
        $mmBlocks = $mm->getYAMLBlocksList();
        if(empty($mmBlocks))
        {
            // just for trunk
            _fatal('YAML blocks are not found. Clear all cache and reinstall all modules.');
        }
        else
        {
            foreach($mmBlocks as $path)
                foreach($this->YAMLblocks2array($this->getData($path)) as $mmb)
                    $blocks[] = $mmb;
        }

        return $blocks;
    }

    /**
     *  Returns an array of layout blocks for specified page
     *  @param $page Page name
     */
    function getCustomBlocks($page)
    {
        return $this->getData($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.'.blocks.yml');
    }

    /**
     *  Returns parsed data
     *  @param $str path to YAML-file or YAML-string
     */
    function getData($str)
    {
        if (! empty($str) && (strpos($str, "\n") === false) && is_readable($str)) {
            global $application;
            $ini_cache = $application->getIniCache();
            $ini_mtime = filemtime($str);
            if ($ini_mtime == $ini_cache->read($str.'-mtime')) {
                $trunk = $ini_cache->read($str);
            }
            else {
                loadCoreFile('spyc.php');
                $trunk = Spyc::YAMLLoad($str);
                $ini_cache->write($str.'-mtime', $ini_mtime);
                $ini_cache->write($str, $trunk);
            }
            return $trunk;
        }
        loadCoreFile('spyc.php');
        return Spyc::YAMLLoad($str);
    }

    /**
     *  Saves a list of blocks for specified page into YAML-file
     *  @param $page Page name
     *  @param $blocks An array of blocks
     */
    function saveBlocks($page, $blocks=array())
    {
        loadCoreFile('spyc.php');
        $fpath = $this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.'.blocks.yml';
        $fh = fopen($fpath, 'w') or die("Could't write to file $fpath");
        fwrite($fh, Spyc::YAMLDump($blocks));
        fclose($fh);
        @chmod($fpath, 0777);
    }

    /**
     *  Returns an array of predefined system blocks in JSON format
     */
    function getAvailableBlocks()
    {
        $blocks = $this->getBlocks();
        foreach($blocks as $i=>$b) $blocks[$i]['template'] = $b['template'];
        loadCoreFile('JSON.php');
        $json = new Services_JSON();
        return $json->encode($blocks);
    }

    /**
     *  Returns an array of params for specified block name
     *  @param $bName Block name
     */
    function getBlockInfo($bName, $page=null)
    {
        $sys_blocks = $this->getBlocks();                   // array of system blocks
        $custom_blocks = $this->getCustomBlocks($page);     // array of custom (user-defined) blocks

        foreach($sys_blocks as $block)
            if($block['name']==$bName)
            {
                foreach($custom_blocks as $cblock)
                {
                    if($cblock['name']==$bName)
                    {
                        if(isset($cblock['title'])) $block['title']=$cblock['title'];
                        if(isset($cblock['description'])) $block['description']=$cblock['description'];
                        if(isset($cblock['template'])) $block['template']=$cblock['template'];
                        return $block;
                    }
                }
                return $block;
            }

        foreach($custom_blocks as $block)
            if($block['name']==$bName) return $block;

        return array();
    }

    /**
     *  Returns a page description
     *  @param $page Page name
     */
    function getPageDescription($page)
    {
        $pages = $this->getData(PATH_SYSTEM_DATA.'pages.yml');
        return (empty($pages['pages'][$page]) ? '' : $pages['pages'][$page]);
    }

    /**
     *  Returns a list of existing pages
     */
    function getPagesList()
    {
        $files = $this->_listLayoutsDir();
        foreach($files as $i=>$file)
            if(preg_match('/(.*blocks|.*restore|default)$/',$file)) unset($files[$i]);
        //array_unshift($files, 'default');
        return $files;
    }

    /*
     *  Makes a clone of existing YAML page.
     *  @param $params Array of overriding params
     *  Ex.
        modApiFunc('Layout_CMS', 'clonePage', array(
            'page_name' => 'test.php',
            'cloned_page' => 'index.php',
            'settings' => array(
                'template' => 'page.my.tpl.html'    // override page template
            ),
            'placeholders' => array(
                'left_column' => array(
                    'visibility' => 'hidden',       // hide left column
                    'blocks' => array()             // empty left column
                ),
                'center_column' => array(
                    'overwrite' => true,            // following blocks will OVERWRITE existing blocks
                    'blocks' => array(
                        array('name'=>'welcome_text'),
                        array('name'=>'ProductList', 'visibility' => 'hidden')
                    )
                ),
                'right_column' => array(
                    'blocks' => array(              // these blocks will be ADDED into right column
                        array('name'=>'RightBanner')
                    )
                )
            )
        ));
    */
    function clonePage($params=array())
    {
        if(!is_writable($this->_PATH_CUR_THEME_PAGE_LAYOUTS))
        {
            CTrace::err("ClonePage: ".$this->_PATH_CUR_THEME_PAGE_LAYOUTS ." is not writable.");
            return;
        }

        $cur_pages = $this->getPagesList();
        if(empty($params['page_name'])) $params['page_name'] = 'test.php';
        if(empty($params['cloned_page'])) $params['cloned_page'] = 'index.php';
        if(empty($params['settings'])) $params['settings'] = null;
        if(empty($params['placeholders'])) $params['placeholders'] = null;

        if(in_array($params['page_name'], $cur_pages))
        {
            CTrace::err("ClonePage: page {$params['page_name']} already exists");
            return;
        }

        if(in_array($params['cloned_page'], $cur_pages))
        {
            $clone = $this->getData($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$params['cloned_page'].'.yml');
            if(!empty($params['settings']) && is_array($params['settings']))
                foreach($params['settings'] as $sname=>$sval)
                    if(!empty($sname) && in_array($sname, array_keys($clone['settings'])))
                        $clone['settings'][$sname] = $sval;

            if(!empty($params['placeholders']) && is_array($params['placeholders']))
                foreach($params['placeholders'] as $phName=>$phVal)
                    if(!empty($phName) && in_array($phName, array_keys($clone['placeholders'])))
                    {
                        if(!empty($params['placeholders'][$phName]['visibility']))
                            $clone['placeholders'][$phName]['visibility'] =
                                $params['placeholders'][$phName]['visibility'];
                        if(isset($params['placeholders'][$phName]['blocks'])
                            && is_array($params['placeholders'][$phName]['blocks']))
                        {
                            if($params['placeholders'][$phName]['blocks'] == array())
                                $clone['placeholders'][$phName]['blocks'] = array();
                            else {
                                if(!empty($params['placeholders'][$phName]['overwrite']))
                                    $clone['placeholders'][$phName]['blocks'] = array();

                                foreach($params['placeholders'][$phName]['blocks'] as $block)
                                    if(!empty($block['name']))
                                    {
                                        if(empty($block['visibility'])) $block['visibility']='visible';
                                        array_push($clone['placeholders'][$phName]['blocks'], $block);
                                    }
                            }
                        }
                    }
            $this->_writeYAML($params['page_name'], $clone);        // save 'new_page.php.yml'
            $this->_writeYAML($params['page_name'], $clone, true);  // save 'new_page.php.restore.yml'
        }
        else
        {
            CTrace::err("ClonePage: cloning page {$params['cloned_page']} doesn't exist");
        }
    }

    function removePage($pageName)
    {
        if(file_exists($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$pageName.'.yml'))
            @unlink($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$pageName.'.yml');
        if(file_exists($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$pageName.'.restore.yml'))
            @unlink($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$pageName.'.restore.yml');
        if(file_exists($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$pageName.'.blocks.yml'))
            @unlink($this->_PATH_CUR_THEME_PAGE_LAYOUTS.$pageName.'.blocks.yml');
    }

    /**
     *  Flatten layout data of specified page into 2D KV data
     *  @param $page Page name
     */
    function getPageMap($page)
    {
        global $application;

        $fb = modApiFunc('Look_Feel','isFacebook') ? '.fb' : '';
        $theme = modApiFunc('Look_Feel', 'getCurrentSkin');
        $cache = CCacheFactory::getCache('persistent', 'page_manager');
       	$cached_map = $cache->read(md5($theme.$page.$fb));
       	if ($cached_map !== null)
       	{
        	return $cached_map;
		}

        $map = array();
        $layout = $this->getLayoutTmpl($theme, $page, null, true);
        foreach($layout['settings'] as $set)
            $map[$set['name']] = $this->_desanitize_tags($set['content']);
        foreach($layout['rows'] as $row)
        {
            switch($row['name'])
            {
                case 'header':
                case 'main_menu':
                case 'footer':
                case 'topslideshow':
                    $PHdata = $row['data']['placeholders'][0]['data'];
                    if(empty($PHdata['blocks']) || !is_array($PHdata['blocks'])) continue;
                    foreach($PHdata['blocks'] as $block)
                    {
                        if(empty($block['name'])) continue;
                        $map[$block['name']] = '';
                        if($block['visibility']=='visible' && $PHdata['visibility']=='visible')
                        {
                                $InfoBlock = $this->getBlockInfo($block['name'], $page);
                                $map[$block['name']] = $this->_unquote($InfoBlock['template']);
                        }
                    }
                break;
                case 'main_content':
                default:
                    foreach($row['data']['placeholders'] as $PH)
                    {
                        $map[$PH['name']] = '';
                        if(empty($PH['data']['visibility'])
                            || $PH['data']['visibility'] != 'visible'
                            || empty($PH['data']['blocks'])
                            || !is_array($PH['data']['blocks']))
                                continue;
                        foreach($PH['data']['blocks'] as $block)
                            if($block['visibility']=='visible')
                            {
                                $InfoBlock = $this->getBlockInfo($block['name'], $page);
                                $map[$PH['name']] .= $this->_unquote($InfoBlock['template']);
                            }
                    }
                break;
            }
        }
        $cache->write(md5($theme.$page.$fb), $map);
        return $map;
    }

    /**
     *  Returns 2 generated maps (for 'default' and specified one pages)
     *  @param $page Page name
     */
    function generateMap($page)
    {
        if(file_exists($this->_PATH_CUR_THEME.'map.ini'))
            if($this->parseMapIni()==null) return array();
        $defaultMap = $this->getPageMap('default');
        $pageMap = $this->getPageMap($page);

        return array('default'=>$defaultMap, $page=>$pageMap);
    }

    /**
     *  Initial parsing if theme map.ini file and generating of YAML files
     */
    function parseMapIni()
    {
        $map_file = $this->_PATH_CUR_THEME.'map.ini';
        if (!file_exists($map_file))
        {
            return null;
        }

        $pages = $blocks_yml = array();
        foreach($this->getBlocks() as $bl)
            $blocks_yml[trim($bl['template'])] = $bl['name'];

        // Hoare sort blocks by order
        function hoare($arr)
        {
            $less = $greater = array();
            if(count($arr)<=1) return $arr;
            $x = array_shift($arr);
            foreach($arr as $y)
            {
                if($y[1]<=$x[1]) array_push($less, $y);
                else array_push($greater, $y);
            }
            return array_merge(hoare($less), array($x), hoare($greater));
        }

        CProfiler::ioStart($map_file, 'parse');
        $map = parse_ini_file($map_file, true);
        CProfiler::ioStop();
        foreach($map as $page => $sections)
        {
            $pages[$page] = array('settings' => array(
                                    'template'          => '#DEFAULT#',
                                    'page_title'        => '#DEFAULT#',
                                    'page_description'  => '#DEFAULT#',
                                    'page_keywords'     => '#DEFAULT#'
                                ),
                                'placeholders' => array(
                                    'header' => array(),
                                    'main_menu' => array(),
                                	'topslideshow' => array(),
                                    'left_column' => array(),
                                    'center_column' => array(),
                                    'right_column' => array(),
                                    'footer' => array()
                                ));
            foreach($sections as $section_name=>$section_content)
            {
                switch($section_name)
                {
                    case 'template': case 'page_title': case 'page_description': case 'page_keywords':
                        $pages[$page]['settings'][$section_name] = "'$section_content'";
                    break;

                    default:
                        $PH = 'custom';
                        switch($section_name)
                        {
                            case 'top_menu':
                            case 'top_phone':
                            case 'user1':
                            case 'user3':
                                $PH = 'header';
                            break;
                            case 'main_menu': case 'topslideshow':
                            case 'left_column': case 'center_column': case 'right_column':
                                $PH = $section_name;
                            break;
                            case 'copyright': case 'footer_menu':
                                $PH = 'footer';
                            break;
                            default: break;
                        }

                        // regexp pattern for 'if()...endif' blocks
                        $if_endif_pat = '/(\<\?php\s+if\s*\(.+?\)\s*\:\s*\?\>(.*?)(?!endif)(.*?)\<\?php\s+endif\s*;\s*\?\>)/';
                        // common regexp pattern for standard PHP tags
                        $common_pat = '/(\<\?php\s+(.*?)(?!\<\?php)(.*?)\s*\?\>)/';

                        $obtained_blocks = array();     // array of obtained blocks from placeholder
                        if(!empty($section_content))
                        {
                            $scont = $section_content;
                            if(preg_match_all($if_endif_pat, $scont, $matches, PREG_OFFSET_CAPTURE))
                            {
                                $obtained_blocks = $matches[0];
                            }
                            foreach($obtained_blocks as $ob)
                            {
                                $scont = str_replace($ob[0], str_repeat("#",strlen($ob[0])), $scont);
                            }
                            if(preg_match_all($common_pat, $scont, $matches, PREG_OFFSET_CAPTURE))
                            {
                                $obtained_blocks = array_merge($obtained_blocks, $matches[0]);
                            }

                            // preserve blocks sequence in placeholder
                            $obtained_blocks = hoare($obtained_blocks);
                            foreach($obtained_blocks as $i=>$ob)
                            {
                                $bcont = trim($obtained_blocks[$i][0]);
                                $bname = @$blocks_yml[$bcont];
                                if(isset($bname))
                                {
                                    $pages[$page]['placeholders'][$PH]['blocks'][] =
                                        array('name' => $bname, 'visibility' => 'visible');
                                }
                                // if block content is not in a list of system blocks
                                else
                                {
                                    $pages[$page]['placeholders'][$PH]['blocks'][] =
                                        array('name' => "undef_".$PH."_$i", 'visibility' => 'visible');
                                    $pages[$page]['placeholders'][$PH]['undef_blocks'][] = array(
                                                                    'name' => "undef_".$PH."_$i",
                                                                    'title' => 'Undefined block',
                                                                    'description' => 'Undefined block',
                                                                    'template' => "'$bcont'"
                                                                );

                                }
                                $pages[$page]['placeholders'][$PH]['visibility'] = 'visible';
                            }
                        }
                        // if placeholder is empty we make it 'hidden'
                        else
                        {
                            $pages[$page]['placeholders'][$PH]['blocks'] = array();
                            $pages[$page]['placeholders'][$PH]['visibility'] = 'hidden';
                        }
                    break;
                }
            }
        }

        // merge pages with 'default'
        $def_page = $pages['default'];
        foreach($pages as $page=>$layout)
        {
            $undef_blocks = array();
            foreach($layout['settings'] as $sname=>$sval)
                if($sval==='#DEFAULT#')
                    $pages[$page]['settings'][$sname] = $def_page['settings'][$sname];

            foreach($layout['placeholders'] as $PHname=>$PHparams)
            {
                if($PHparams===array())
                    $pages[$page]['placeholders'][$PHname] = $def_page['placeholders'][$PHname];

                if(!empty($pages[$page]['placeholders'][$PHname]['undef_blocks']))
                {
                    $undef_blocks = array_merge($undef_blocks, $pages[$page]['placeholders'][$PHname]['undef_blocks']);
                    unset($pages[$page]['placeholders'][$PHname]['undef_blocks']);
                }
            }
            // save unparsed blocks
            $this->_saveCustomBlocks($page, $undef_blocks);
        }

        // save parsed pages
        $this->_saveMapIniParsedPages($pages);
        // rename theme map.ini
        if(file_exists($this->_PATH_CUR_THEME.'map.ini'))
        {
            copy($this->_PATH_CUR_THEME.'map.ini', $this->_PATH_CUR_THEME.'map.ini.old');
            @chmod($this->_PATH_CUR_THEME.'map.ini.old', 0777);
            unlink($this->_PATH_CUR_THEME.'map.ini');
        }
    }

    /**
     *  Saves custom and undefined blocks
     *  @param $page Page name
     *  @param $blocks A list of blocks
     */
    function _saveCustomBlocks($page, $blocks){
        loadCoreFile('spyc.php');
        $fpath = $this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.'.blocks.yml';
        $fh = fopen($fpath, 'w') or die("Could't write to file $fpath");
        fwrite($fh, Spyc::YAMLDump($blocks));
        fclose($fh);
        @chmod($fpath, 0777);
    }

    function _desanitize_tags($str="")
    {
        return str_replace(')gt;','>',
                str_replace('(lt;','<',
                    str_replace('(?php','<?php',
                        str_replace('?)','?>', $str))));
    }

    /**
     *  Returns a list of .yml files
     */
    function _listLayoutsDir()
    {
        $files = array();
        if($files=scandir($this->_PATH_CUR_THEME_PAGE_LAYOUTS))
        {
            foreach($files as $i => $file)
            {
                if(!preg_match('/^([0-9a-zA-Z])+(.)*(\.yml)$/',$file))
                {
                    unset($files[$i]);
                }
                else $files[$i] = str_replace('.yml','',$files[$i]);
            }
        }
        return $files;
    }

    /**
     *  Saves parsed pages
     *  @param $pages Layout pages data
     */
    function _saveMapIniParsedPages($pages=array())
    {
        foreach($pages as $pname=>$data)
            $this->_writeYAML($pname, $data);
    }

    /**
     *  Converts layout data to YAML format and saves it
     *  @param $page Page name
     *  @param $data Page data
     *  @param $restore Restore flag. If is set, save data to <$page>.restore.yml file
     */
    function _writeYAML($page, $data, $restore=null)
    {
        loadCoreFile('spyc.php');
        $fpath = $this->_PATH_CUR_THEME_PAGE_LAYOUTS.$page.(isset($restore) ? '.restore' : '').'.yml';
        $fh = fopen($fpath, 'w') or die("Could't write to file $fpath");
        fwrite($fh, Spyc::YAMLDump($data));
        fclose($fh);
        @chmod($fpath, 0777);
    }

    /**
     *  Removes quotes at the beginning and at the end of specified string
     */
    function _unquote($s)
    {
        return preg_replace('/(\'+)$/','',preg_replace('/^(\'+)/', '', $s));
    }


    /**
     *  Array of system rows
     */
    var $_editor_rows;
}
?>