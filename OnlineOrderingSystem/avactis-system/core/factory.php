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
 * Class Factory provides interface for building a singleton object.
 *
 * @package Core
 * @access  public
 * @author af
 */
class Factory
{
    var $ObjectList;

    function Factory()
    {
        $this->ObjectList = array();
    }

    /**
     * Creates the class object and saves a reference to it.
     * If it requeries whether this object is created with the same
     * parameters, it returns the reference to the object that already exists.
     */
    function &getInstance($Classname, $_args_list = array(), $callMethodAfterCreated = '')
    {
        $Classname = strtolower($Classname); // only ascii chars here, using of _ml_strtolower() is not required

        /*
         * To identify the query of creating the object correctly, you should
         * create a unique label of constructor parameters. I.e. for one class,
         * that has different parameters should be created different objects,
         * and vice versa - create one object for the same parameters.
         */
        $Class_args_label = $Classname;
        if (count($_args_list) > 0)
        {
            $Class_args_label .= md5(implode('::', $_args_list));
        }

        if (! array_key_exists($Class_args_label,  $this->ObjectList))
        {
            if (!class_exists($Classname))
            {
//                CTrace::err('Factory, FATAL ERROR: The class '.$Classname.' does not exist!');
//                CTrace::backtrace();
//                die("Factory, FATAL ERROR: The class '$Classname' does not exist!");
				_fatal(array("CODE"=>"Factory_001","MESSAGE"=>"FATAL ERROR: The class '$Classname' does not exist!"));
            }

            $obj = &$this->createObject($Classname, $_args_list);
            $this->ObjectList[$Class_args_label] = &$obj;
            if ($callMethodAfterCreated != '' && method_exists($obj, $callMethodAfterCreated))
            {
            	$obj->$callMethodAfterCreated();
            }
        }
        return $this->ObjectList[$Class_args_label];
    }

    function callMethodToAllObjects($methodName)
    {
    	$keys = array_keys($this->ObjectList);
        foreach($keys as $k)
        {
        	$obj = &$this->ObjectList[$k];
            if (method_exists($obj, $methodName))
            {
                $obj->$methodName();
            }
        }
    }

    function &createObject($Classname, $args_array)
    {
        /*
         * This method of creation the objects is the best in performing.
         * The usage of eval or call_user_func is more universal, but much slower.
         *
         * The method limits the number of parameters, which is passed to the
         * class constructor. It may be enlarged. In fact, it isn't used
         * more than five parameters.
         */
        switch(count($args_array))
        {
            case 0:
                $obj = new $Classname();
                break;
            case 1:
                $obj = new $Classname($args_array[0]);
                break;
            case 2:
                $obj = new $Classname($args_array[0],$args_array[1]);
                break;
            case 3:
                $obj = new $Classname($args_array[0],$args_array[1],$args_array[2]);
                break;
            case 4:
                $obj = new $Classname($args_array[0],$args_array[1],$args_array[2],$args_array[3]);
                break;
            case 5:
                $obj = new $Classname($args_array[0],$args_array[1],$args_array[2],$args_array[3],$args_array[4]);
                break;
            default:
//                die('Factory, FATAL ERROR: Quantity of custructor params more the five don\'t supported.');
				_fatal(array("CODE"=>"Factory_002","MESSAGE"=>"FATAL ERROR: Quantity of custructor params more than five isn\'t supported."));
                break;
        }
        return $obj;
    }
}


?>