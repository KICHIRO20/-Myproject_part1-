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
    * Option API
    *
    * @package Avactis
    */

   /**
    * Retrieve option value based on name of option.
    *
    * If the option does not exist or does not have a value, then the return value
    * will be false. This is useful to check whether you need to install an option
    * and is commonly used during installation of plugin options and to test
    * whether upgrading is required.
    *
    * If the option was serialized then it will be unserialized when it is returned.
    *
    * @since 4.7.5
    * @package Avactis
    * @subpackage Option

    * 	Any value other than false will "short-circuit" the retrieval of the option
    *	and return the returned value.
    *
    * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
    * @param mixed $default Optional. Default value to return if the option does not exist.
    * @return mixed Value set for the option.
    */

   function asc_get_option( $option, $default ) {
   	global $application;
           $default = '';

   	$option = trim( $option );
   	if ( empty( $option ) )
   	return false;

           $result = execQuery('SELECT_OPTION_VALUE', array('opt_name' => $option));
           if ( empty($result) )
           return $default;

           else {
   	$result = unserialize($result[0]['option_value']);
   	return $result;
          }
   }


   /**
    * Update the value of an option that was already added.
    *
    * You do not need to serialize values. If the value needs to be serialized, then
    * it will be serialized before it is inserted into the database. Remember,
    * resources can not be serialized or added as an option.
    *
    * If the option does not exist, then the option will be added with the option
    * value, but you will not be able to set whether it is autoloaded.
    *
    * @since 4.7.5
    * @package Avactis
    * @subpackage Option
    *
    *
    * @param string $option Option name.
    * @param mixed $newvalue Option value.
    */

   function asc_update_option( $option, $newvalue ) {
   	global $application;
   	$option = trim($option);
   	if ( empty($option) )
   	return false;
   	$result = asc_get_option($option);
        $old_value  = $result;
   	$NewserializeVal =  serialize($newvalue);
   	if($result){
   		$tables = Configuration::getTables();
   		$tr = $tables['options']['columns'];
   		$query = new DB_Update('options');
   		$query->addUpdateValue($tr['option_value'], $NewserializeVal);
   		$query->WhereValue($tr["option_name"], DB_EQ, $option);

   		$application->db->PrepareSQL($query);
   		$application->db->DB_Exec();

       /**
	 * Fires after the value of a specific option has been successfully updated.
	 *
	 * @param mixed $old_value The old option value.
	 * @param mixed $value     The new option value.
	 */
	do_action( "asc_update_option_{$option}", $old_value, $newvalue );

	/**
	 * Fires after the value of an option has been successfully updated.
	 *
	 * @param string $option    Name of the updated option.
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 */
	do_action( 'asc_updated_option', $option, $old_value, $newvalue );
        return true;
   	}
   	else {
   		asc_add_option($option, $newvalue);
   	}
   }

   /**
    * Add a new option.
    *
    * You do not need to serialize values. If the value needs to be serialized, then
    * it will be serialized before it is inserted into the database. Remember,
    * resources can not be serialized or added as an option.
    *
    * You can create options without values and then update the values later.
    * Existing options will not be updated and checks are performed
    *
    * @package Avactis
    * @subpackage Option
    * @since 4.7.5
    *
    *
    * @param string $option Name of option to add.
    * @param mixed $value Optional. Option value, can be anything.
    * @param mixed $deprecated Optional. Description. Not used anymore.
    */
   function asc_add_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {
   	global $application;

   	$option = trim($option);
   	if ( empty($option) )
   	return false;
   	$serializeVal =  serialize($value);
   	$result = asc_get_option($option);
   	if($result)
   	return '';
   	else {
   		$tables = Configuration::getTables();
   		$tr = $tables['options']['columns'];
   		$query = new DB_Insert('options');
   		$query->addInsertValue($option, $tr['option_name']);
   		$query->addInsertValue($serializeVal, $tr['option_value']);
   		$query->addInsertValue($autoload, $tr['autoload']);

   		$application->db->PrepareSQL($query);
   		$application->db->DB_Exec();

        /**
	 * Fires after a specific option has been added.
	 *
	 * @since 2.5.0 As "add_option_{$name}"
	 * @since 3.0.0
	 *
	 * @param string $option Name of the option to add.
	 * @param mixed  $value  Value of the option.
	 */
	do_action( "asc_add_option_{$option}", $option, $value );
        return true;
   	}
   }

   /**
    * Removes option by name. Prevents removal of protected Avactis options.
    *
    * @package Avactis
    * @subpackage Option
    * @since 4.7.5
    *
    *
    * @param string $option Name of option to remove.
    */
   function asc_delete_option( $option ) {
   	global $application;

   	$option = trim($option);
   	if ( empty($option) )
   	return false;
   	$result = asc_get_option($option);

   	if($result)
   	{
   		$tables = Configuration::getTables();
   		$tr = $tables['options']['columns'];
   		$db_delete = new DB_Delete('options');
   		$db_delete->WhereValue($tr['option_name'], DB_EQ, $option);
   		$application->db->PrepareSQL($db_delete);
   		$application->db->DB_Exec();

                /**
		 * Fires after a specific option has been deleted.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 * @param string $option Name of the deleted option.
		 */
		do_action( "asc_delete_option_$option", $option );
                return true;
   	}
   	else return '';
   }

   function getSQLResults($sql='')
   {
   	global $application;
   	$dbprefix = $application->getAppIni('DB_TABLE_PREFIX');
   	if ($sql)
   	{
   		$sql = str_replace('{dbprefix}',$dbprefix,$sql);
   		$m = new DB_MySQL();
   		$m->QueryResult = $m->DB_Query($sql);
   		$m->DB_Result(QUERY_RESULT_ASSOC);
   		$result = $m->ResultArray;
   		unset($m);
   		return $result;
   	}
   	else return array();
   }

   /**
    * Print option value after sanitizing for forms.
    *
    * @since 4.7.5
    *
    * @param string $option Option name.
    */
   function asc_form_option( $option ) {
     $res = asc_get_option( $option );
     echo $res[$option] ;
   }

   /**
    * Loads and caches all autoloaded options, if available or all options.
    *
    * @since 4.7.6
    *
    * @return array List of all options.
    */
   function asc_load_alloptions() {
   	global $application;
           $default = '';

           $alloptions = execQuery('SELECT_ALL_OPTIONS');
           if ( empty($alloptions) )
           return $default;
           else
           return $alloptions;

   }
   ?>