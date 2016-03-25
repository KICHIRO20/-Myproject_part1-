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
 *
 *                   ArrayAccessLayer:setAccessMask
 *
 * @access  public
 */
define('AAL_CUSTOM_PARAM','AAL_CUSTOM_PARAM');


/**
 * ArrayAccessLayer class.
 *
 * The class is used to ease the data reading from multidimensional arrays.
 * A check on index array (isset) existing is included in the class.
 * The array to read is passed to the class constructor.
 *
 * Warning! the array processing is realized through references. That means
 * the initial array in the constructuor is not copied, but only its reference
 * is saved.
 *
 * If indexes,specified in the array are not defined, then null string ('')
 * will be returned. This value can be overriden by calling
 * ArrayAccessLayer::setUndefinedValue($undefval).
 *
 * Example of usage:
 *<code>
 * $array = array(...); // a multidimensional array
 * $aal_list = new ArrayAccessLayer($array);
 *
 * // same as: $value = isset($array['key1']['key2']) ? $array['key1']['key2'] : '';
 * $value = $aal_list->get('key1','key2');
 *
 * // Set access mask (array indexes mask)
 * $aal_list->setAccessMask ("Billing", "attr", AAL_CUSTOM_PARAM, "value");
 *
 * // the reduced call can be used now
 * // same as: $value = isset($array['Billing']['attr']['Firstname']['value']) ?
 * //                         $array['Billing']['attr']['Firstname']['value']  : '';
 * $value = $aal_list->getByMask('Firstname');
 *</code>
 *
 * @package Core
 * @author Alexey Florinsky
 * @access public
 */
class ArrayAccessLayer
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Constructor.
     *
     * @param array $array Array to access
     */
    function ArrayAccessLayer(&$array)
    {
        $this->_fArray = &$array;
    }

    /**
     * Gets the initial array value by indexes.
     * It takes an unlimitted number of parameters.
     *
     *@param string
     *@return mixed Returns the value in the initial array by specified
     *              indexes, If the value isn't defined, then
     *              the value ArrayAccessLayer::_fUndefinedValue is returned
     */
    function get()
    {
        return $this->_get(func_get_args());
    }


    /**
     * Checks if indexes exist in the initial array.
     * It takes an unlimitted number of parameters.
     *
     *@return bool TRUE if the specified index list exist, FALSE otherwise
     */
    function isDefined()
    {
        return $this->_isDefined(func_get_args());
    }

    function isDefinedByMask()
    {
        return $this->_isDefined($this->_getFullIndexesList(func_get_args()));
    }


    /**
     * Gets a value from the initial array by indexes using index mask.
     * It takes an unlimitted number of parameters.
     *
     * Example:
     *<code>
     * // the mask
     * $aal_list->setAccessMask(AAL_CUSTOM_PARAM, "attr", AAL_CUSTOM_PARAM, "value");
     *
     * // the array access
     * $value = $aal_list->getByMask('Billing', 'Firstname','key1','key2');
     * // the indexes wil be the following: ['Billing']['attr']['Firstname']
     * ['value']['key1']['key2']
     *</code>
     *
     *@param string
     *@return mixed Returns the value in the initial array by specified
     *              indexes, if the value isn't defined, then
     *              the value ArrayAccessLayer::_fUndefinedValue is returned
     */
    function getByMask()
    {
        return $this->_get($this->_getFullIndexesList(func_get_args()));
    }

    /**
     * Sets up index mask to access to the initial array.
     * It takes an unlimitted number of parameters.
     * As index mask, which will be changed during the following calls,
     * should be used the AAL_CUSTOM_PARAM constant.
     *
     * Example:
     *<code>
     * $aal_list->setAccessMask("Billing", "attr", AAL_CUSTOM_PARAM, "value");
     *</code>
     *
     * This example means the following:
     * - the index mask: [Billing"][attr"][AAL_CUSTOM_PARAM]["value"]
     * - the third index will be defined during the folowing array references
     * - the array reference: $value = $aal_list->getByMask('Firstname');
     * - parameter 'Firstname' will be used as the third index
     *
     * Unlimitted number of constants AAL_CUSTOM_PARAM can be defined
     * in the ndex mask, for example:
     *<code>
     * // the mask
     * $aal_list->setAccessMask(AAL_CUSTOM_PARAM, "attr", AAL_CUSTOM_PARAM, "value");
     *
     * // the array reference
     * $value = $aal_list->getByMask('Billing', 'Firstname');
     *</code>
     *
     * Note: when referring to the array, all additional indexes will be
     * appended to the end of index list, for example:
     *<code>
     * // the mask
     * $aal_list->setAccessMask(AAL_CUSTOM_PARAM, "attr", AAL_CUSTOM_PARAM, "value");
     *
     * // the array access
     * $value = $aal_list->getByMask('Billing', 'Firstname','key1','key2');
     * // the indexes will be the following: ['Billing']['attr']['Firstname']['value']['key1']['key2']
     *</code>
     *
     *@param string
     *@return void
     */
    function setAccessMask()
    {
        # Get a list of passed parameters
        $this->_fAccessMask = func_get_args();
    }


    /**
     * Sets up a value, which will be returned, if the specified indexes
     * do not exist in the initial array.
     *
     *@param mixed $undefval Return value if array value is undefined
     *@return void
     */
    function setUndefinedValue($undefval)
    {
        $this->_fUndefinedValue = $undefval;
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**
     * Gets a value from the initial array by arrays,
     * specified in the $index_list list.
     *
     *@param array $index_list index list
     *@return mixed Returns the value in the initial array by specified
     *              indexes, if the value isn't defined, then
     *              the value ArrayAccessLayer::_fUndefinedValue is returned
     */
    function _get($index_list)
    {
        $list = &$this->_fArray;
        foreach($index_list as $index)
        {
            if (is_array($list) && isset($list[$index]))
            {
                $list = &$list[$index];
            }
            else
            {
                return $this->_fUndefinedValue;
            }
        }
        return $list;
    }

    function _isDefined(&$arg_list)
    {
        $c = $this->_fUndefinedValue;
        $this->_fUndefinedValue = '__fUndefinedValue_UNREAL_VALUE__';
        $result = $this->_get($arg_list);
        if ($result == $this->_fUndefinedValue)
        {
            $this->_fUndefinedValue = $c;
            return FALSE;
        }
        else
        {
            $this->_fUndefinedValue = $c;
            return TRUE;
        }
    }

    function _getFullIndexesList(&$args)
    {
        /*
            Explanation.

            Example 1:
            The mask: array('mkey1','mkey2',AAL_CUSTOM_PARAM,'mkey3')
            The args: array('key1','key2','key3','key4')
            Result args must be: array('mkey1','mkey2','key1','mkey3','key2','key3','key4')

            Example 2:
            The mask: array(AAL_CUSTOM_PARAM,'mkey2',AAL_CUSTOM_PARAM,'mkey3')
            The args: array('key1','key2','key3','key4')
            Result args must be: array('key1','mkey2','key2','mkey3','key3','key4')

            Example 3:
            The mask: array(AAL_CUSTOM_PARAM, AAL_CUSTOM_PARAM)
            The args: array('key1','key2','key3')
            Result args must be: array('key1','key2','key3')
        */

        $args_index = 0;
        $result_args = array();
        foreach($this->_fAccessMask as $mvalue)
        {
            if ($mvalue == AAL_CUSTOM_PARAM)
            {
                if (!isset($args[$args_index]))
                {
                    die('ERROR: '.__CLASS__.':'.__FUNCTION__.' indefined index in arguments array, every custom param in access mask must be defined!');
                }
                $result_args[] = $args[$args_index];
                $args_index++;
            }
            else
            {
                $result_args[] = $mvalue;
            }
        }

        for ($i=$args_index; $i<count($args); $i++)
        {
            $result_args[] = $args[$i];
        }

        return $result_args;
    }


    /**
     * @var array a reference to the initial array (the array to read)
     */
    var $_fArray = array();


    /**
     * @var mixed a value, which will be returned, if no value exists on
     *            specified indexes in the initial array.
     */
    var $_fUndefinedValue = '';


    /**
     * @var array the array to store the index mask for
     *            the reduced access to the initial array
     */
    var $_fAccessMask = array();

    /**#@-*/
}
?>