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
 * The Replacer class is used to replace the tags specified in the template
 * with variables. Variables in the template are defined as {tag_name}.
 *
 * An example of use:
 * To replace the tags with the values in the text you need to invoke
 * the Replacer::Replace($text, $vars) method, where $text - the text with tags,
 * $vars - an associative array of tags and values.
 * <code>
 *  $text = 'Example of text with tags: {1}, {2}, and {etc}';
 *  $vars = array(
 *                {1} => 'tag1',
 *                {2} => 'tag2',
 *                {etc} => 'tag etc'
 *               );
 *  echo Replacer::Replace($text, $vars);
 * </code>
 * The result: Example of text with tags: tag1, tag2, and tag etc.
 *
 * @ to complete the identification of methods of this class
 * @author Alexey Florinsky
 * @access private
 * @package Core
 */
class Replacer
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The Replacer class constructor.
     *
     * @ to complete the constructor
     * @return
     */
    function Replacer()
    {

    }

    /**
     * Replaces the tags in the template with values.
     *
     * @ to complete the method
     * @return string the string with replaced tags
     * @param string $text the string with tags
     * @param array $vars the array of compatabilities of tags and its values
     */
    function replace($text, $vars)
    {
        global $application;
        $text = strtr($text, $vars);


        $prepared_tpl_file = $application->getAppIni('PATH_CACHE_DIR').'_tpl_'.md5($text);
        asc_file_put_contents($prepared_tpl_file, $text);

        ob_start();
        include($prepared_tpl_file);
        $out = ob_get_clean();
        return $out;
    }

    /**
     * @ describes the Replacer-> function.
     */
    function my_eval($m)
    {
    	return eval("return $m[1]");
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}
?>