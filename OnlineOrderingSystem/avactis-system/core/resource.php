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
 * Resource class is used for reading data from the resourse files
 * in the folder /templates/resources.
 *
 * Each module has its resource file, which contains text of error messages,
 * phrases, etc. The resource file syntax matches the ini-file syntax.
 *
 * @ finish the functions on this page
 * @author Alexey Florinsky
 * @access public
 * @package Core
 */
class Resource
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Constructor of the Resource class.
     *
     * @ add the process of reading and writing ini files
     * @return datatype description
     * @param type $parname description
     */
    function Resource()
    {
        $this->resources = array();
        $this->resources['CAT_001'] = 'Wrong category id {0}<br>';
        $this->resources['CAT_002'] = 'There is no subcategory: {0} in {1} category<br>';
        $this->resources['TMPL_FILLER_ERROR_TEMPLATE_WRAPPER_BEGIN_TAG_NOT_FOUND'] = "Core::TmplFiller::fill() Cannot read  the file '{0}'.";
        $this->resources['TMPL_FILLER_ERROR_TEMPLATE_WRAPPER_BEGIN_TAG_NOT_FOUND'] = "Core::TmplFiller::removeTemplateWrapper() 'Begin' wrapper tag '{0}' is absent from template file '{1}'.";
        $this->resources['TMPL_FILLER_ERROR_TEMPLATE_WRAPPER_END_TAG_NOT_FOUND'] = "Core::TmplFiller::removeTemplateWrapper() 'End' wrapper tag '{0}' is absent from template file '{1}'.";
        $this->resources['TMPL_FILLER_ERROR_TEMPLATE_WRAPPER_BEGIN_END_TAGS_ARE_MISPLACED'] = "Core::TmplFiller::removeTemplateWrapper() 'Begin' ('{0}') and 'End' ('{1}') wrapper tags are misplaced in template file '{2}'.";
    }

    /**
     * Gets a resource by its code.
     *
     * @return string The string, which matches the resource code
     * @param string $code resource code
     */
    function getRes($code)
    {
        return $this->resources[$code];
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * A resource array.
     *
     * @var array
     */
    var $resources;

    /**#@-*/
}
?>