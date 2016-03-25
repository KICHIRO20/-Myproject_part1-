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
 * @package ModRewrite
 * @author Egor V. Derevyankin
 *
 */

class MR_Settings
{
    function MR_Settings()
    {
        loadCoreFile('html_form.php');
        if(modApiFunc('Session','is_set','MR_sets'))
        {
            $this->mr_settings = modApiFunc('Session','get','MR_sets');
            modApiFunc('Session','un_set','MR_sets');
        }
        else
        {
            $this->mr_settings = modApiFunc('Mod_Rewrite','getSettings');
        };
        $this->cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
        $this->_form_disabled = true;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('MR',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("mod_rewrite/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => getMsg('MR',$eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("mod_rewrite/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function outputLayoutsSelect()
    {
        $html_code = "";

        if(sizeof($this->cz_layouts) > 0)
        {
            $i = 1;
            foreach ($this->cz_layouts as $layout_config_ini_path => $config)
            {
                if(modApiFunc('Mod_Rewrite','_isIntegrityCorrect',$layout_config_ini_path))
                {
                    $icon = 'r-green.gif';
                    $msg = getMsg('MR','MSG_INTEGRITY_OK');
                }
                else
                {
                    $icon = 'r-red.gif';
                    $msg = getMsg('MR','MSG_INTEGRITY_BROKEN');
                };

                $html_code .= '<input style="margin: 1px;" name="storefront_link" type="radio"'.
                              ' value="'.modApiFunc('Mod_Rewrite','_prepareAbsFilePath',$layout_config_ini_path).'" '.($i==1?'checked':'').'>&nbsp;'.
                              '<A HREF="'.$config['SITE_URL'].'" target="_blank" style="font-size: 10pt; color: blue;">'.$config['SITE_URL'].'</A>'.
                              '&nbsp;&nbsp;<img src="images/icons/'.$icon.'" onmouseover="return overlib(\''.$msg.'\');" onmouseout="return nd();"><br>';
                $i++;
            }
        }
        else
        {
            $html_code .= '<span style="color: red;">'.getMsg('MR','WRN_NOT_FOUND_STOREFRONTS').'</span>';
        }

        return $html_code;
    }

    function outMRStatusMessage($mr_info)
    {
        #                   -                                                           ,                    ,                                      
        #    -                                                                 mod_rewrite.

        if(preg_match("/^apache/i",$mr_info["server_soft"]))
        {
            switch($mr_info['mod_rewrite_loaded'])
            {
                case 'Y':
                    if($mr_info['tests_status'] == false)
                    {
                        return getMsg('MR','MSG_MR_STAT_05');
                    };
                    break;
                case 'N':
                    return getMsg('MR','MSG_MR_STAT_03');
                    break;
                case 'NA':
                    if($mr_info['tests_status'] == false)
                    {
                        return getMsg('MR','MSG_MR_STAT_05');
                    };
                    break;
            };
        };

        $this->_form_disabled = false;
        return '';
    }

    function out_jsLinkSmaplesArray()
    {
        $js_code = 'link_samples = new Array();'."\n";
        $js_code .= 'link_samples[1] = new Array("http://www.example.com/%cat_prefix%-2-1-3-My-Category-Name.html","http://www.example.com/%prod_prefix%-2-My-Product-Name.html","http://www.example.com/%cms_prefix%-2-My-CMS-Page.html");'."\n";
        $js_code .= 'link_samples[2] = new Array("http://www.example.com/My-Category-Name-%cat_prefix%-2-1-3.html","http://www.example.com/My-Product-Name-%prod_prefix%-2.html","http://www.example.com/My-CMS-Page-%cms_prefix%-2.html");'."\n";
        $js_code .= 'link_samples[3] = new Array("http://www.example.com/%cat_prefix%-2-1-3/My-Category-Name.html","http://www.example.com/%prod_prefix%-2/My-Product-Name.html","http://www.example.com/%cms_prefix%-2/My-CMS-Page.html");'."\n";
        $js_code .= 'link_samples[4] = new Array("http://www.example.com/My-Category-Name/%cat_prefix%-2-1-3.html","http://www.example.com/My-Product-Name/%prod_prefix%-2.html","http://www.example.com/My-CMS-Page/%cms_prefix%-2.html");'."\n";
        $js_code .= 'link_samples[5] = new Array("http://www.example.com/%cat_prefix%/2-1-3/My-Category-Name.html","http://www.example.com/%prod_prefix%/2/My-Product-Name.html","http://www.example.com/%cms_prefix%/2/My-CMS-Page.html");'."\n";
        $js_code .= 'link_samples[6] = new Array("http://www.example.com/My-Category-Name/%cat_prefix%/2-1-3.html","http://www.example.com/My-Product-Name/%prod_prefix%/2.html","http://www.example.com/My-CMS-Page/%cms_prefix%/2.html");'."\n";

        $js_code = str_replace(
            array('%cat_prefix%','%prod_prefix%','%cms_prefix%')
           ,array($this->mr_settings['CATS_PREFIX'],$this->mr_settings['PRODS_PREFIX'],$this->mr_settings['CMS_PREFIX'])
	   ,$js_code
        );

        return $js_code;
    }

    function out_LayoutsSettings()
    {
        if(count($this->cz_layouts) > 0)
        {
            global $application;
            $html_code = '';

            $i=0;
            foreach($this->cz_layouts as $layout_path => $layout_info)
            {
                $layout_path = modApiFunc('Mod_Rewrite','_prepareAbsFilePath',$layout_path);
                $integ_info = modApiFunc('Mod_Rewrite','_getIntegrityInfoForCZLayout',$layout_path);
                $integ_stat = modApiFunc('Mod_Rewrite','_isIntegrityCorrect',$layout_path);

                if($integ_info === null)
                {
                    $mr_stat = getMsg('MR','MR_STAT_OFF');
                    $int_stat = getMsg('MR','INT_STAT_NA');
                }
                elseif($integ_info['mr_active']=='Y' and $integ_stat==true)
                {
                    $mr_stat = getMsg('MR','MR_STAT_ON');
                    $int_stat = getMsg('MR','INT_STAT_OK');
                }
                elseif($integ_info['mr_active']=='N' and $integ_stat==true)
                {
                    $mr_stat = getMsg('MR','MR_STAT_OFF');
                    $int_stat = getMsg('MR','INT_STAT_OK');
                }
                elseif($integ_info['mr_active']=='Y' and $integ_stat==false)
                {
                    $mr_stat = getMsg('MR','MR_STAT_AOFF');
                    $int_stat = getMsg('MR','INT_STAT_BROKEN');
                }
                elseif($integ_info['mr_active']=='N' and $integ_stat==false)
                {
                    $mr_stat = getMsg('MR','MR_STAT_OFF');
                    $int_stat = getMsg('MR','INT_STAT_BROKEN');
                };

                $template_contents = array(
                    'sfLink' => $layout_info['SITE_URL']
                   ,'sfMRstatus' => $mr_stat
                   ,'sfINTstatus' => $int_stat
                   ,'FormID' => ++$i
                   ,'sfLayoutPath' => base64_encode($layout_path)
                   ,'OnValChacked' => ($integ_info['mr_active']=='Y' and $integ_stat==true) ? 'checked' : ''
                   ,'OffValChecked' => ($integ_info['mr_active']=='N' or $integ_stat==false) ? 'checked' : ''
                );

                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $html_code .= $this->mTmplFiller->fill("mod_rewrite/", "cz_layout.tpl.html",array());
            };

            return $html_code;
        }
        else
        {
            return getMsg('MR','WRN_NOT_FOUND_STOREFRONTS');
        };
    }

    function output()
    {
        global $application;

        $mr_info = modApiFunc('Mod_Rewrite','detectModRewrite');
        $rw_schemes = modApiFunc('Mod_Rewrite','getRewriteSchemes');

        // <begin>                                      mod_rewrite
        $tests_results = array();

        //                     "     "
        echo "<!-- Tests results:\n";
        for($i=1;$i<=2;$i++)
        {
            $tests_results[$i] = $this->_makeTest($i,"Ok");
            if($tests_results[$i])
            {
                echo "{$i}: Ok\n";
            }
            else
            {
                echo "{$i}: Error\n";
            };
        };

        echo " -->";

        if(!in_array(true,$tests_results))
        {
            $mr_info['tests_status'] = false;
        }
        else
        {
            //                                    ,
            //        Options +FollowSymLinks   .htaccess
            $mr_info['tests_status'] = true;
            $_sets = array(
                    'ADD_FSL_STRING' => array_search(true,$tests_results) == 2 ? 'Y' : 'N'
                  );
            modApiFunc('Mod_Rewrite','updateSettings',$_sets);
        };
        // <end>                                      mod_rewrite

        $schemes_select = array(
            "id"          => "sets[REWRITE_SCHEME]"
           ,"select_name" => "sets[REWRITE_SCHEME]"
           ,"selected_value" => $this->mr_settings['REWRITE_SCHEME']
           ,"onChange" => "changeSampleLinks(this.value);"
           ,"values" => array()
        );

        foreach($rw_schemes as $k => $scheme_info)
        {
            $schemes_select['values'][] = array(
                    "value" => $scheme_info['scheme_id']
                   ,"contents" => getMsg('MR','RS_'._ml_strtoupper($scheme_info['scheme_name']))
            );
        };

        $template_contents = array(
            'ResultMessage' => $this->outputResultMessage()
           ,'ServerSoft' => $mr_info['server_soft']
           ,'MRloaded' => $mr_info['mod_rewrite_loaded']
           ,'setsCatsPrefix' => HtmlForm::genInputTextField('255','sets[CATS_PREFIX]','50',$this->mr_settings['CATS_PREFIX'])
           ,'setsProdsPrefix' => HtmlForm::genInputTextField('255','sets[PRODS_PREFIX]','50',$this->mr_settings['PRODS_PREFIX'])
           ,'setsCMSPrefix' => HtmlForm::genInputTextField('255','sets[CMS_PREFIX]','50',$this->mr_settings['CMS_PREFIX'])
	   ,'MRStatusMessage' => $this->outMRStatusMessage($mr_info)
           ,'setsSchemesSelect' => HtmlForm::genDropdownSingleChoice($schemes_select)
           ,'jsLinkSmaplesArray' => $this->out_jsLinkSmaplesArray()
           ,'currentSchemeID' => $this->mr_settings['REWRITE_SCHEME']
           ,'LayoutsSettings' => ($this->_form_disabled) ? '' : $this->out_LayoutsSettings()
           ,'disableAllCondition' => ($this->_form_disabled) ? 'true' : 'false'
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("mod_rewrite/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

	function updateTestRewriteBase($test_number)
	{

		$path = str_replace(basename($_SERVER["SCRIPT_NAME"]),'',$_SERVER["SCRIPT_NAME"]);
		$mod_rewrite_test_dir = 'mod_rewrite_test_'.sprintf("%02d",$test_number);

		$search = '/RewriteBase(.*)' . $mod_rewrite_test_dir . '/';
		$replace = 'RewriteBase ' . $path . $mod_rewrite_test_dir;

		$string = file_get_contents($application->appIni['PATH_ADMIN_DIR'] . $mod_rewrite_test_dir . '/.htaccess');
		$string = preg_replace($search,$replace,$string,1);

		file_put_contents($application->appIni['PATH_ADMIN_DIR'] . $mod_rewrite_test_dir . '/.htaccess', $string);
	}

    function _makeTest($test_number, $coming_answer)
    {
		
		$this->updateTestRewriteBase($test_number);
		
        $req = new Request();
        $base_url = $req->getURL();

        $base_url = preg_replace("/\/[^\/]*$/","",$base_url);
        $test_url = $base_url.'/mod_rewrite_test_'.sprintf("%02d",$test_number).'/test.html';

        echo "{$test_url}\n";

        loadCoreFile('bouncer.php');
        $bnc = new Bouncer();
        $bnc->setMethod("GET");
        $bnc->setHTTPversion("1.0");
        $bnc->setURL($test_url);

        echo date("Y-m-d H:i:s",time())."\n";
        $res = $bnc->RunRequest();
        echo date("Y-m-d H:i:s",time())."\n";


        if($res != false)
        {
            if(trim($res["body"]) == $coming_answer)
            {
                return true;
            };
        };

        return false;
    }

    var $_Template_Contents;
    var $mr_settings;
    var $cz_layouts;
    var $_form_disabled;
};

?>