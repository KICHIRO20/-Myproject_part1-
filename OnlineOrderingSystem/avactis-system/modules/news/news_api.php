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
 * News module.
 *
 * @package News
 * @author Timur Nasibullin
 */

define('NEWS_DISPLAY_COUNT','news_display_count');
define('NEWS_MAX_COUNT','news_max_count');
define('NEWS_LAST_BUILD_DATE','news_last_build_date');
define('NEWS_TTL','news_ttl');

class News
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * News constructor.
     */
    function News()
    {
        global $application;

        $this->loadSettings();
        $this->news_server = $application->getAppIni('NEWS_SERVER');
        $this->news_gateway = $application->getAppIni('NEWS_GATEWAY');
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Configuration::getTables() instead of $this->getTables()
     */
    function install()
    {
        global $application;
        $messageResources = &$application->getInstance('MessageResources');

        $tables = News::getTables();

        $query = new DB_Table_Create($tables);

        $table_name = 'news_settings';
        $columns = $tables[$table_name]['columns'];

        $query = new DB_Insert($table_name);
        $query->addInsertValue(NEWS_DISPLAY_COUNT,$columns['key']);
        $query->addInsertValue('integer',$columns['type']);
        $query->addInsertValue('1',$columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table_name);
        $query->addInsertValue(NEWS_MAX_COUNT,$columns['key']);
        $query->addInsertValue('integer',$columns['type']);
        $query->addInsertValue('10',$columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table_name);
        $query->addInsertValue(NEWS_LAST_BUILD_DATE,$columns['key']);
        $query->addInsertValue('integer',$columns['type']);
        $query->addInsertValue(time(),$columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table_name);
        $query->addInsertValue(NEWS_TTL,$columns['key']);
        $query->addInsertValue('integer',$columns['type']);
        $query->addInsertValue('0',$columns['value']);
        $application->db->getDB_Result($query);
    }

    /**
     * Installs the specified module in the system.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Configuration::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
        global $application;

        $query = new DB_Table_Delete(News::getTables());
        $application->db->getDB_Result($query);
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of the meta description of the table:
     * <code>
     *      $tables = array ();
     *      $table_name = 'table_name';
     *      $tables[$table_name] = array();
     *      $tables[$table_name]['columns'] = array
     *      (
     *          'fn1'               => 'table_name.field_name_1'
     *         ,'fn2'               => 'table_name.field_name_2'
     *         ,'fn3'               => 'table_name.field_name_3'
     *         ,'fn4'               => 'table_name.field_name_4'
     *      );
     *      $tables[$table_name]['types'] = array
     *      (
     *          'fn1'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
     *         ,'fn2'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
     *         ,'fn3'               => DBQUERY_FIELD_TYPE_CHAR255
     *         ,'fn4'               => DBQUERY_FIELD_TYPE_TEXT
     *      );
     *      $tables[$table_name]['primary'] = array
     *      (
     *          'fn1'       # several key fields may be used, e.g. - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      # several fields can be used in one index, e.g. - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -  the meta description of module tables
     */
    function getTables()
    {
        global $application; # initialize the global object $application (the core object)
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        # the 'news' table meta discription
        $table_name = 'news'; #table name
        $tables[$table_name] = array();
        #the array of field names of the table, array keys are alias names of the table fields
        $tables[$table_name]['columns'] = array
        (
            'id'            => $table_name.'.news_id',
            'title'         => $table_name.'.news_title',
            'link'          => $table_name.'.news_link',
            'content'       => $table_name.'.news_content',
            'category'      => $table_name.'.news_category',
            'date'          => $table_name.'.news_date',
            'type'          => $table_name.'.news_type'
        );
        #the array of field types of the table, array keys are alias names of the table fields
        $tables[$table_name]['types'] = array
        (
            'id'            => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment',
            'title'         => DBQUERY_FIELD_TYPE_CHAR255,
            'link'          => DBQUERY_FIELD_TYPE_CHAR255,
            'content'       => DBQUERY_FIELD_TYPE_LONGTEXT,
            'category'      => DBQUERY_FIELD_TYPE_CHAR50,
            'date'          => DBQUERY_FIELD_TYPE_INT,
            'type'          => DBQUERY_FIELD_TYPE_CHAR10
        );
        #define the field of primary key
        $tables[$table_name]['primary'] = array
        (
            'id'
        );

        #the 'news_settings' table meta description
        $table_name = 'news_settings';
        $tables[$table_name] = array();
        # the array of field names of the table, array keys are alias names of the table fields
        $tables[$table_name]['columns'] = array
        (
            'id'        => $table_name.'.news_setting_id',
            'key'       => $table_name.'.setting_key',
            'type'      => $table_name.'.stting_type',
            'value'     => $table_name.'.setting_value'
        );
        #the array of field types of the table, array keys are alias names of the table fields
        $tables[$table_name]['types'] = array
        (
            'id'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment',
            'key'       => DBQUERY_FIELD_TYPE_CHAR50,
            'type'      => DBQUERY_FIELD_TYPE_CHAR10,
            'value'     => DBQUERY_FIELD_TYPE_CHAR50,
        );
        # define the field of primary field
        $tables[$table_name]['primary'] = array
        (
            'id'
        );

        return $application->addTablePrefix($tables); #add a prefix to table names
    }

   /**
    * Loads module settings from the database.
    */
    function loadSettings()
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['news_settings']['columns'];

        $query = new DB_Select();
        $query->addSelectField($columns['key'],'SetKey');
        $query->addSelectField($columns['type'],'SetType');
        $query->addSelectField($columns['value'],'SetValue');

        $result = $application->db->getDB_Result($query);
        $this->settings = array();
        for($i=0;$i<sizeof($result);$i++)
        {
            $this->settings[$result[$i]['SetKey']] = $result[$i]['SetValue'];
            settype($this->settings[$result[$i]['SetKey']],$result[$i]['SetType']);
        };
    }

   /**
    * Gets the parameter value of module settings.
    * @param string $key - the parameter name
    */
    function getValue($key)
    {
        if(array_key_exists($key,$this->settings))
        {
            return $this->settings[$key];
        }
        else
        {
            return NULL;
        };
    }

   /**
    * Sets the parameter value of module settings.
    * @param string $key - the parameter name
    * @param string $val - the parameter value
    */
    function setValue($key,$value)
    {
        global $application;

        if(!array_key_exists($key,$this->settings) || $this->settings[$key] == $value)
        {
            return;
        };
        $type = gettype($this->settings[$key]);
        $this->settings[$key] = $value;
        settype($this->settings[$key],$type);

        $tables = $this->getTables();
        $columns = $tables['news_settings']['columns'];

        $query = new DB_Update('news_settings');
        $query->addUpdateValue($columns['value'],$value);
        $query->WhereValue($columns['key'],DB_EQ,$key);

        $application->db->getDB_Result($query);
    }

   /**
    * Gets a list of possible number of displayed news
    * in the range from 1 to NEWS_COUNT_MAX.
    */
    function getNewsDisplayCountArray()
    {
        $retval = array();
        for($i=1;$i<=$this->settings[NEWS_MAX_COUNT];$i++)
        {
            $retval[$i] = $i;
        };
        return $retval;
    }

   /**
    * Checks whether the news update is required.
    */
    function isNewsUpdateRequired()
    {
        $ttl = '+'.$this->getValue(NEWS_TTL).' minutes';
        $lastBuildDate = $this->getValue(NEWS_LAST_BUILD_DATE);

        # expiration date of the current RSS-tape
        $newsExpireTime = strtotime($ttl,$lastBuildDate);

        return $newsExpireTime < time() ? true : false;
    }

   /**
    * Parse the RSS-tape.
    */
    function parseRSS($xml_code)
    {
        loadCoreFile('obj_xml.php');
        $last_news_date = $this->getLastNewsDate();
        $xml = new xml_doc($xml_code);
        $xml->parse();

        $channel = $xml->document->findChild('channel');    # the <channel> tag contents
        $this->channelContents = array();
        $itemContents = array();
        foreach($channel->children as $channelChild)
        {
            if(_ml_strtolower($channelChild->name) == 'item')   # parse the <item> tag contents
            {
                $pubDate = $channelChild->findChild('pubdate');
                $itemContents['NewsDate'] = strtotime($pubDate->contents,0);
                if($itemContents['NewsDate'] <= $last_news_date)
                {
                    # an old piece of news, skip it
                    continue;
                }

                foreach($channelChild->children as $itemChild)
                {
                    switch(_ml_strtolower($itemChild->name))
                    {
                        case 'title':
                            $itemContents['NewsTitle'] = $itemChild->contents;
                            break;
                        case 'link':
                            $itemContents['NewsLink'] = $itemChild->contents;
                            break;
                        case 'description':
                            $itemContents['NewsContent'] = $itemChild->contents;
                            break;
                        case 'category':
                            $itemContents['NewsCategory'] = $itemChild->contents;
                            break;
                        default:
                            break;
                    };
                };
                $itemContents['NewsType'] = 'avactis';
                $this->addNews($itemContents);  # add the latest piece of news to the database
            }
            else    # get the other tags contents into the <channel> tag
            {
                $this->channelContents[$channelChild->name] = $channelChild->contents;
            }
        };
        # update NEWS_LAST_BUILD_DATE and NEWS_TTL parameters
        $this->setValue(NEWS_LAST_BUILD_DATE,strtotime($this->channelContents['LASTBUILDDATE'],0));
        $this->setValue(NEWS_TTL,$this->channelContents['TTL']);
    }

   /**
    * Updates the news list: loading new records and removing the old ones,
    * if required.
    *
    * @return integer - the number of latest news
    */
    function updateNews()
    {
        if($this->isNewsUpdateRequired())
        {
    	    loadCoreFile('Snoopy.class.php');
    	    $snoopy = new Snoopy();
	    $domainname =  $_SERVER['HTTP_HOST'];
	    $data = array('domain_name'=>$domainname,'avactis_version'=> PRODUCT_VERSION_NUMBER,'version_type'=> PRODUCT_VERSION_TYPE);
	    $snoopy->submit($this->news_server.$this->news_gateway, $data);
	    if(_ml_strpos($snoopy->response_code, "200") != 0 && $snoopy->results != '')
            {
                $this->parseRSS($snoopy->results);
                if($this->getNewsCount() > $this->getValue(NEWS_MAX_COUNT))
                # the number of records in the news table is more than NEWS_MAX_COUNT, remove the odd ones
                {
                    $this->deleteOldNews();
                }
            };
        };
    }

   /**
    * Counts the number of news in the database.
    *
    * @return integer - the number of records in the news table
    */
    function getNewsCount()
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['news']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fCount($columns['id']),'NewsCount');
        $result = $application->db->getDB_Result($query);

        return intval($result[0]['NewsCount']);
    }

   /**
    * Get the latest piece of news date in the Avactis installation database.
    *
    * @return integer - the latest piece of news date of Unix timestamp format
    * or 0, if news doesn't exist in the database
    */
    function getLastNewsDate()
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['news']['columns'];

        $query = new DB_Select();
        $query->AddSelectField($columns['date'],'NewsDate');
        $query->WhereValue($columns['type'],DB_EQ,'avactis');
        $query->SelectOrder($columns['date'],'DESC');
        $query->SelectLimit(0,1);


        $result = $application->db->getDB_Result($query);
        if($result == NULL)
        {
                return 0;
        };
        return intval($result[0]['NewsDate']);
    }

   /**
    * Gets the news list.
    *
    * @param string $news_type - 'avactis' or 'store' - defines, if external or
    * internal news is required.
    * Added in case news is required not only for the administrator but also
    * for the Avactis users.
    * @return array -  the latest news array, the length of which is
    * NEWS_DISPLAY_COUNT parameter of the News module
    */
    function getNewsList($news_type = 'avactis')
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['news']['columns'];

        $query = new DB_Select();
        $query->AddSelectField($columns['title'],'NewsTitle');
        $query->AddSelectField($columns['link'],'NewsLink');
        $query->AddSelectField($columns['content'],'NewsContent');
        $query->AddSelectField($columns['category'],'NewsCategory');
        $query->AddSelectField($columns['date'],'NewsDate');
        $query->WhereValue($columns['type'],DB_EQ,$news_type);
        $query->SelectOrder($columns['date'],'DESC');
        $query->SelectLimit(0,$this->settings[NEWS_DISPLAY_COUNT]);

        return $application->db->getDB_Result($query);
    }

   /**
    * Adds a piece of news to the database.
    *
    * @param array $newsContents added news data
    * <code>
    * $newsContents = array(
    *    'NewsTitle'     => 'news_title',
    *    'NewsLink'      => 'news_link',
    *    'NewsContent'   => 'news_content',
    *    'NewsCategory'  => 'news_category',
    *    'NewsDate'      => 'news_date',     #!Date of the Unix timestamp format
    *    'NewsType'      => 'news_type'
    * );
    * </code>
    */
    function addNews($newsContents)
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['news']['columns'];

        if(!isset($newsContents['NewsDate']))
        {
            $newsContents['NewsDate'] = time();
        };
        $query = new DB_Insert('news');
        $query->addInsertValue($newsContents['NewsTitle'],$columns['title']);
        $query->addInsertValue($newsContents['NewsLink'],$columns['link']);
        $query->addInsertValue($newsContents['NewsContent'],$columns['content']);
        $query->addInsertValue($newsContents['NewsCategory'],$columns['category']);
        $query->addInsertValue($newsContents['NewsDate'],$columns['date']);
        $query->addInsertValue($newsContents['NewsType'],$columns['type']);

        $application->db->getDB_Result($query);
    }

   /**
    * Deletes old records in the news table.
    * After that <= NEWS_MAX_COUNT records remain.
    */
    function deleteOldNews()
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['news']['columns'];
        # select the latest date piece of news among the remain ones in the table...
        $query = new DB_Select('news');
        $query->addSelectField($columns['date'],'NewsDate');
        $query->SelectOrder($columns['date'],'DESC');
        $query->SelectLimit($this->settings[NEWS_MAX_COUNT]-1,1);
        $result = $application->db->getDB_Result($query);

        if($result == NULL)
        {
            return;
        };
        # ...delete all the latest news @ check this line
        $query = new DB_Delete('news');
        $query->WhereValue($columns['date'],DB_LT,$result[0]['NewsDate']);
        $query->WhereAND();
        $query->WhereValue($columns['type'],DB_EQ,'avactis');
        $application->db->getDB_Result($query);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

   /**
    * @var array the array of News module settings:
    * <code>
    * $settings = array(
    *         'new_display_count' => the amount of news, dislayed in AvactisHomeNews,
    *         'news_max_count' => "time-to-live" of the news in the database
    *         );
    * </code>
    */
    var $settings;

    /**
    * @var string URL of news server
    */
    var $news_server;

    /**
    * @var string the name of news gateway
    */
    var $news_gateway;

    /**#@-*/

}
?>