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
 *            tag_param_stack -                        -       block
 *                        , e.g. <?php ManufacturerName(); ?>, <?php ManufacturerInfo(); ?>.
 *         ,                ProductInfo              <?php ManufacturerName(); ?>
 *                                      ,                                 .
 *
 *                                                 (            ).         ,
 *                           ,           product id,      manufacturer id.
 *                   ,  . .                       ProductInfo
 * <?php ManufacturerName('2'); ?>                                       id = 2,
 *                                              .
 *                      tag_param_stack :: find_first_param_by_priority.
 *                                     -                    .
 *
 *                           ,                          push   pop.
 *             ProductInfo,                    push(product id).
 *             ProductInfo,                    pop().         ,             push-
 *          ,                        .
 *
 *                                                           __info_tag_output_find_tag_params.
 *                   __info_tag_output_find_tag_params     "manufacturer".
 *
 *                           (        ,    manufacturer_cz),          $arg_list
 * __info_tag_output_find_tag_params                                             ,       ,
 *                    ,                                   ,                    .
 *                         push-                           .                  (2008-04-04)
 *          $arg_list                                    ,                     __info_tag_output,
 *                       .         , <?php ManufacturerName('2'); ?>
 *
 */
class tag_param_stack
{
	function tag_param_stack()
	{
		$this->_stack = array();
	}

	function push($tag, $params)
	{
		array_push($this->_stack, array
			(
			    "tag"    => $tag
			   ,"params" => $params
			)
	    );
	}

	/**
	 * $tag
	 *                    .
	 */
	function pop($tag)
	{
		$last_pushed_el = array_pop($this->_stack);
		$last_pushed_tag = $last_pushed_el['tag'];
        if($tag != $last_pushed_tag)
        {
            _fatal(array( "CODE" => "CORE_060"), $tag, $last_pushed_tag);
        }
	}

	/**
	 *             ,     find_first,                               ,
	 *                      .
	 *                                      $param_key_list:
	 *       ,                                 .
	 *
	 * @param unknown_type $param_key_list
	 */
	function find_first_param_by_priority($param_key_list)
	{
		$stack_el = $this->__find_first($param_key_list);
		if($stack_el == NULL)
		{
			return PARAM_NOT_FOUND;
		}
		else
		{
			foreach($param_key_list as $param_key)
			{
                $param = $this->__get_param_value($param_key, $stack_el);
                if($param != PARAM_NOT_FOUND)
                {
                	return $param;
                }
                //                       find_first                       :
                //                                                 stack_el.
			}
		}
	}

	// Private methods ////////////////////////////////////////////////////

    /**
     *                                           .
     *              -
     *                ,                                              .
     *                             .
     *                                                               -
     *         null.
     */
    function __find_first($param_key_list)
    {
        $stack = array_reverse($this->_stack);
        foreach($stack as $el)
        {
            foreach($el['params'] as $param)
            {
                if(in_array($param['key'], $param_key_list))
                {
                    return $el;
                }
            }
        }
        return null;
    }

    /**
     *                                                      ,
     *                     .       -         PARAM_NOT_FOUND
     *
     * @param unknown_type $stack_el
     */
    function __get_param_value($param_key, $stack_el)
    {
        foreach($stack_el['params'] as $param)
        {
            if($param['key'] == $param_key)
            {
                return $param;
            }
        }
        return PARAM_NOT_FOUND;
    }

	var $_stack;
}
?>