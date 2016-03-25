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
 * @copyright Copyright &copy; 2008, HBWSL
 * @package Core
 * @author Vadim Lyalikov
 */

/**
 *       image_obj -                         ,                           .
 *
 * @access  public
 * @author Vadim Lyalikov
 * @package Core
 */
class image_obj
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *
     *
     * @return
     */
    function image_obj($image_id = NULL, $error_code = NULL, $error_msg = "")
    {
        $this->image_id = $image_id;
        $this->error_code = $error_code;
        $this->error_msg = $error_msg;
    }

    function is_empty()
    {
    	return $this->image_id === NULL;
    }

    function get_id()
    {
        return $this->image_id;
    }

    function set_id($image_id)
    {
    	$this->image_id = $image_id;
    }

    /**
     *       0                     .
     *
     * @param unknown_type $error_code
     */
    function set_error($error_code)
    {
    	$this->error_code = $error_code;
    	$args = func_get_args();
    	array_unshift($args, 'IMG');
    	$this->error_msg = call_user_func_array('getMsg', $args);
    }

    function get_error()
    {
    	if($this->error_code === NULL)
    	{
    		return NULL;
    	}
    	else
    	{
            return array("error_code" => $this->error_code, "error_msg" => $this->error_msg);
    	}
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $image_id;
    var $error_code;
    var $error_msg;
    /**#@-*/
}
?>