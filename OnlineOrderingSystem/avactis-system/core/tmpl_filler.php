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
loadCoreFile('tmpl_includer.php');
/**
 * Class TmplFiller is used to manage template files: read file contents,
 * strip page footer/header, create frame around the template contents for
 * debugging or customization purposes.
 *
 * To fill a template use function fill($folder_name, $tmpl_short_filename, $vars)
 * To activate debug (red template frames) mode set $bOutputTemplateFrame to "true";
 *
 * Example of use:
 * Replacer::Replace($text, $vars)
 * $folder_name and $tmpl_short_filename are folder and file name in which template
 * is stored, $vars - associative array containing template variables values
 * <code>
 *
 *  $vars = array(
 *                {1} => 'tag1',
 *                {2} => 'tag2',
 *                {etc} => 'tag etc'
 *               );
 *
 *  echo TmplFilter::fill($folder_name, $tmpl_short_filename, $vars)
 * </code>
 * Result: template html contents with tags replaced with their values.
 *
 * @access private
 * @author Vadim Lyalikov
 * @package Core
 */
class TmplFiller extends TmplIncluder
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * TmplFiller class constructor.
     *
     *                                                                              ,
     *                                                      .
     *  . .                       ,                                            .
     */
    function TmplFiller($path_templates_dir = null)
    {
        parent::TmplIncluder();

        //                                                         .
        if ($path_templates_dir != null)
        {
        	$this->TemplateDirPrefix = $path_templates_dir;
        }
        else
        {
            switch(modApiFunc('Users', 'getZone'))
            {
                case "CustomerZone" : $this->TemplateDirPrefix = modApiFunc('application', 'getAppIni', 'PATH_USERS_TPLS_VIEWS'); break;
                case "AdminZone"    : $this->TemplateDirPrefix = modApiFunc('application', 'getAppIni', 'PATH_ADMIN_TPLS_VIEWS'); break;
                default : _fatal(array( "CODE" => "CORE_049"), __CLASS__, __FUNCTION__);
            }
        }
    }


    /**
     * Set the template path to override the above.
     *
     * @param string $folder_name Template file path: relative template folder path.
     */
    function setTemplatePath($folder_name)
    {
       $this->TemplateDirPrefix =  modApiFunc('application', 'getAppIni','PATH_ASC_ROOT').$folder_name ;
    }



    /**
     * Checks if a template file with the given name exists and is readable.
     *
     * @param string $folder_name Template file path: full folder path.
     * @param string $tmpl_short_filename Template file path: file name.
     * @return boolean TRUE if file exists and is readable, FALSE otherwise.
     */
    function isTemplateReadable($folder_name, $tmpl_short_filename)
    {
        return is_readable($this->TemplateDirPrefix . $folder_name . $tmpl_short_filename);
    }


    /**
     * Reads given template file contents.
     * It prepares it for filling.
     * It fills it with template variables values provided.
     * It does special HTML formatting if needed, e.g. draw red debug template frames.
     *
     *
     * @param string $folder_name Template file path: full folder path.
     * @param string $tmpl_short_filename Template file path: file name.
     * @param arrat $vars Template variables values.
     * @return string template html contents with tags replaced with their values.
     */
    function fill($folder_name, $tmpl_short_filename, $vars, $customer_zone = false)
    {

        //Open file, read contents, replace tags.
        $this->PreviousTemplateFilename = $this->CurrentTemplateFilename;

	if ($customer_zone)
		$this->CurrentTemplateFilename = getTemplateFileAbsolutePath($this->TemplateDirPrefix . $folder_name . $tmpl_short_filename);
	else
	{

		$this->CurrentTemplateFilename = $this->TemplateDirPrefix . $folder_name ."views/admin/templates/". $tmpl_short_filename;

		if(!is_file($this->CurrentTemplateFilename)){
			$this->CurrentTemplateFilename =  $this->TemplateDirPrefix. $folder_name . $tmpl_short_filename;
		}

		if(!is_file($this->CurrentTemplateFilename)){
			$this->CurrentTemplateFilename =  modApiFunc('application','getAppIni','PATH_ADMIN_TPLS_VIEWS'). $folder_name . $tmpl_short_filename;
		}

	}


        $code_to_include = $this->getCachedCode($this->CurrentTemplateFilename);
        if ($code_to_include === null)
        {
            $tpl_file = new CFile($this->CurrentTemplateFilename);
            $code_to_include = $tpl_file->getContent();

            //                        ,                                                (        > 128)
            $code_to_include = convertTemplate($code_to_include);

            if ($code_to_include === FALSE)
            {
                CTrace::backtrace();
                _fatal("TMPL_FILLER_ERROR_CANNOT_READ_FILE", $this->CurrentTemplateFilename);
            }

           $code_to_include = '?>'.$this->removeTemplateWrapper($code_to_include);

            $this->saveInCache($this->CurrentTemplateFilename, $code_to_include);
        }
        $text = $this->includeTmplCode($code_to_include);

        $_vars = array();
        foreach ($vars as $key => $value)
        {
        	$_vars['{'.$key.'}'] = $value;
        }
        $text = strtr($text, $_vars);

        $text = $this->addCrossSiteProtectinField($text);
        return $text;
    }

    function addCrossSiteProtectinField($text)
    {
        $text = preg_replace('/<\/form>/is', '<input type = "hidden" name="__ASC_FORM_ID__" value="'.modApiFunc('Session', 'get', '__ASC_FORM_ID__').'"/></form>', $text);
        return $text;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Removes the header and the footer from template contents.
     *
     * @param string $text Template contents.
     * @return string $text Stripped template contents.
     */
    function removeTemplateWrapper($text)
    {
        $space = "[\ \t\r\n]*";

        $begin_tpl_tag_expr =
                     "/<"  .
            $space . "!"  .
            $space . "-"  .
            $space . "-"  .
            $space . "b"  .
            $space . "e"  .
            $space . "g"  .
            $space . "i"  .
            $space . "n"  .
            $space . "t"  .
            $space . "p"  .
            $space . "l"  .
            $space . "\("  .
            $space . "d"  .
            $space . "o"  .
            $space . "n"  .
            $space . "o"  .
            $space . "t"  .
            $space . "r"  .
            $space . "e"  .
            $space . "m"  .
            $space . "o"  .
            $space . "v"  .
            $space . "e"  .
            $space . "!"  .
            $space . "\)"  .
            $space . "-"  .
            $space . "-"  .
            $space . ">/i";

        $end_tpl_tag_expr =
            "/<"  .
            $space . "!"  .
            $space . "-"  .
            $space . "-"  .
            $space . "e"  .
            $space . "n"  .
            $space . "d"  .
            $space . "t"  .
            $space . "p"  .
            $space . "l"  .
            $space . "\("  .
            $space . "d"  .
            $space . "o"  .
            $space . "n"  .
            $space . "o"  .
            $space . "t"  .
            $space . "r"  .
            $space . "e"  .
            $space . "m"  .
            $space . "o"  .
            $space . "v"  .
            $space . "e"  .
            $space . "!"  .
            $space . "\)"  .
            $space . "-"  .
            $space . "-"  .
            $space . ">/i";

        //find first non-space character position after begin-tag. If text contains such tag at all.
        $regs = array();
        $BeginTagLength = 0;
        if(! preg_match($begin_tpl_tag_expr, $text, $regs))
        {
            $pos = false;
        }
        else
        {
            $pos = _ml_strpos($text, $regs[0]);
            $BeginTagLength = _ml_strlen($regs[0]);
        }
        //end find

        if($pos === false)
        {
            _fatal(array( "CODE" => "CORE_046"), $this->CurrentTemplateFilename);
        }
        else
        {
            $begin_pos = $pos + $BeginTagLength;

            if(! preg_match($end_tpl_tag_expr, $text, $regs))
            {
                $pos = false;
            }
            else
            {
                $pos = _ml_strpos($text, $regs[0]);
                //$EndTagLength = _ml_strlen($regs[0]);
            }


            if($pos === false)
            {
            _fatal(array( "CODE" => "CORE_047"), $this->CurrentTemplateFilename);
            }
            else
            {
                $end_pos = $pos;
                if($begin_pos >= $end_pos)
                {
                    _fatal(array( "CODE" => "CORE_048"), $this->CurrentTemplateFilename);
                }
                else
                {
                    return _ml_substr($text, $begin_pos, $end_pos - $begin_pos);
                }
            }
        }
    }


    /**
     * Returns member variable value in HTML format.
     *
     * @return string HTML code snippet containing link to current
     * template file location.
     */
    function getCurrentTemplateFilenameLink()
    {
        return "<B><A STYLE=\"color: red;\" HREF=\"" . $this->CurrentTemplateFilename . "\">" .basename($this->CurrentTemplateFilename). "</B></A>";
    }

    /**
     * Checks if a sequence of identical templates (filenames) is displayed.
     *
     * @return boolean TRUE if the last filled template equals currently being
     * filled one, FALSE - otherwise.
     * template file location.
     */
    function isEqualToPreviousTemplate()
    {
        return (##$this->PreviousTemplateFilename = ""
                $this->PreviousTemplateFilename == $this->CurrentTemplateFilename);
    }

    /**
     * Template files location. E.g. "", or "system/admin/"
     */
    var $TemplateDirPrefix = "";

    /**
     * A template file contents header, which is generally removed before
     * filling.
     */
    var $TemplateWrapperBeginTag = "<hr><!-- BEGIN TPL (DO NOT REMOVE!) -->";

    /**
     * A template file contents footer, which is generally removed before filling.
     */
    var $TemplateWrapperEndTag   = "<hr><!-- END TPL (DO NOT REMOVE!) -->";

    /**
     * A template file currently being filled.
     */
    var $CurrentTemplateFilename = "";

    /**
     * The last template file being filled.
     */
    var $PreviousTemplateFilename = "";

    /**
     * Debug mode flag: switches the drawing of the frame around
     * the template contents.
     */
    var $bOutputTemplateFrame = false;

    /**
     * Debug mode parameter: the header of the frame is around the template
     * contents.
     */
    var $TemplateBorderBegin = "<DIV STYLE=\"border: thin solid red;\">";

    /**
     * Debug mode parameter: the footer of the frame is around the template
     * contents.
     */
    var $TemplateBorderEnd = "</DIV>";
    /**#@-*/
}
?>