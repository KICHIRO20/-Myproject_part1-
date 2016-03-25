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

// ---------------------------
// Select queries
// ---------------------------

class NLT_SELECT_LIST_OF_MESSAGES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Newsletter::getTables();
        $nl = $tables['newsletter_letters']['columns'];

        $this -> addSelectField($nl['letter_id']);
        $this -> setMultiLangAlias('_ml_subject', 'newsletter_letters',
                                   $nl['letter_subject'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_subject'),
                                'letter_subject');
        $this -> addSelectField($nl['letter_creation_date']);
        $this -> addSelectField($nl['letter_sent_date']);
        $this -> setMultiLangAlias('_ml_from_name', 'newsletter_letters',
                                   $nl['letter_from_name'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_from_name'),
                                'letter_from_name');
        $this -> setMultiLangAlias('_ml_from_email', 'newsletter_letters',
                                   $nl['letter_from_email'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_from_email'),
                                'letter_from_email');
        $this -> SelectOrder($nl['letter_creation_date'], 'DESC');
    }
}

class NLT_SELECT_MESSAGE_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Newsletter::getTables();
        $nl = $tables['newsletter_letters']['columns'];

        $this -> addSelectField($nl['letter_id']);
        $this -> setMultiLangAlias('_ml_subject', 'newsletter_letters',
                                   $nl['letter_subject'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_subject'),
                                'letter_subject');
        $this -> addSelectField($nl['letter_creation_date']);
        $this -> addSelectField($nl['letter_sent_date']);
        $this -> setMultiLangAlias('_ml_from_name', 'newsletter_letters',
                                   $nl['letter_from_name'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_from_name'),
                                'letter_from_name');
        $this -> setMultiLangAlias('_ml_from_email', 'newsletter_letters',
                                   $nl['letter_from_email'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_from_email'),
                                'letter_from_email');
        $this -> setMultiLangAlias('_ml_html', 'newsletter_letters',
                                   $nl['letter_html'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_html'),
                                'letter_html');
/*      $this -> setMultiLangAlias('_ml_text', 'newsletter_letters',
                                   $nl['letter_text'],
                                   $nl['letter_id'], 'Newsletter');
        $this -> addSelectValue($this -> getMultiLangAlias('_ml_text'),
                                'letter_text');*/

        $this -> Where($nl['letter_id'], DB_EQ, $params['id_message']);
    }
}

// ---------------------------
// Update queries
// ---------------------------

class NLT_UPDATE_MESSAGE extends DB_Update
{
    function NLT_UPDATE_MESSAGE()
    {
        parent :: DB_Update('newsletter_letters');
    }

    function initQuery($params)
    {
        $tables = Newsletter::getTables();
        $nl = $tables['newsletter_letters']['columns'];

        if (isset($params['letter_subject']))
            $this -> addMultiLangUpdateValue($nl['letter_subject'], $params['letter_subject'],
                                             $nl['letter_id'], $params['id_message'], 'Newsletter');

        if (isset($params['letter_from_name']))
            $this -> addMultiLangUpdateValue($nl['letter_from_name'], $params['letter_from_name'],
                                             $nl['letter_id'], $params['id_message'], 'Newsletter');

        if (isset($params['letter_from_email']))
            $this -> addMultiLangUpdateValue($nl['letter_from_email'], $params['letter_from_email'],
                                             $nl['letter_id'], $params['id_message'], 'Newsletter');

        if (isset($params['letter_html']))
            $this -> addMultiLangUpdateValue($nl['letter_html'], $params['letter_html'],
                                             $nl['letter_id'], $params['id_message'], 'Newsletter');

//      if (isset($params['letter_text']))
//          $this -> addMultiLangUpdateValue($nl['letter_text'], $params['letter_text'],
//                                           $nl['letter_id'], $params['id_message'], 'Newsletter');

        if (isset($params['letter_sent_date']))
            $this -> addUpdateValue($nl['letter_sent_date'], $params['letter_sent_date']);

        $this -> Where($nl['letter_id'], DB_EQ, $params['id_message']);
    }
}

// ---------------------------
// Insert queries
// ---------------------------

class NLT_INSERT_MESSAGE extends DB_Insert
{
    function NLT_INSERT_MESSAGE()
    {
        parent :: DB_Insert('newsletter_letters');
    }

    function initQuery($params)
    {
        $tables = Newsletter::getTables();
        $nl = $tables['newsletter_letters']['columns'];

        $this -> addMultiLangInsertValue($params['letter_subject'], $nl['letter_subject'],
                                         $nl['letter_id'], 'Newsletter');
        $this -> addMultiLangInsertValue($params['letter_from_name'], $nl['letter_from_name'],
                                         $nl['letter_id'], 'Newsletter');
        $this -> addMultiLangInsertValue($params['letter_from_email'], $nl['letter_from_email'],
                                         $nl['letter_id'], 'Newsletter');
        $this -> addMultiLangInsertValue($params['letter_html'], $nl['letter_html'],
                                         $nl['letter_id'], 'Newsletter');
/*      $this -> addMultiLangInsertValue($params['letter_text'], $nl['letter_text'],
                                         $nl['letter_id'], 'Newsletter');*/
        $this -> addInsertValue($params['date'], $nl['letter_creation_date']);
    }
}

// ---------------------------
// Delete queries
// ---------------------------

class NLT_DELETE_MESSAGE extends DB_Delete
{
    function NLT_DELETE_MESSAGE()
    {
        parent :: DB_Delete('newsletter_letters');
    }

    function initQuery($params)
    {
        $tables = Newsletter::getTables();
        $nl = $tables['newsletter_letters']['columns'];

        $this -> deleteMultiLangField($nl['letter_subject'], $nl['letter_id'], 'Newsletter');
        $this -> deleteMultiLangField($nl['letter_from_name'], $nl['letter_id'], 'Newsletter');
        $this -> deleteMultiLangField($nl['letter_from_email'], $nl['letter_id'], 'Newsletter');
        $this -> deleteMultiLangField($nl['letter_html'], $nl['letter_id'], 'Newsletter');
//      $this -> deleteMultiLangField($nl['letter_text'], $nl['letter_id'], 'Newsletter');
        $this -> Where($nl['letter_id'], DB_IN, $this -> arrayToIn($params['ids']));
    }
}
?>