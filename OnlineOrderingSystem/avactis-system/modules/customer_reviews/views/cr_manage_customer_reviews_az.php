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
 * @package Customer_Reviews
 * @author Sergey E. Kulitsky
 *
 */

/**
 * Definition of ManageCustomerReviews viewer
 * The viewer is used to manage customer reviews in admin zone
 */
class ManageCustomerReviews
{

    /**
     * Contructor
     */
    function ManageCustomerReviews()
    {
        loadCoreFile('html_form.php');
        modApiFunc('paginator', 'setCurrentPaginatorName', 'CR_List_AZ');

        // filling search filter
        $this -> setSearchFilter();

        // filling quick navigation
        $this -> fillQuickNavigationData();

        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * Sets the search filter based on the request
     */
    function setSearchFilter()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting data from request
        $this -> _search_filter = array();
        $this -> _search_filter['asc_action'] = $request -> getValueByKey(
                                                                'asc_action'
                                                            );

        // restoring the filter if no action is provided
        // use case: changing the page or rows per page (paginator links)
        if (!in_array($this -> _search_filter['asc_action'],
                      array('ShowPendingReviews', 'ShowBadReviews',
                            'ShowGoodReviews', 'ShowAllReviews',
                            'SearchReviews')))
        {
            if (modApiFunc('Session', 'is_set', 'CR_FILTER'))
            {
                $this -> _search_filter = modApiFunc('Session', 'get',
                                                     'CR_FILTER');
                // filling up the paginator data
                $this -> _search_filter['paginator'] = null;
                $this -> _search_filter['paginator'] = modApiFunc(
                    'Customer_Reviews', 'searchPgCustomerReviews',
                    $this -> _search_filter, PAGINATOR_ENABLE
                );
                return;
            }
            else
                $this -> _search_filter['asc_action'] = 'ShowPendingReviews';
        }

        // pre-filiing end date for the date range
        $this -> _search_filter['to'] = array(
            'day'   => date('d'),
            'month' => date('m'),
            'year'  => date('Y')
        );

        // pre-filling filter for different asc_actions
        switch($this -> _search_filter['asc_action']) {
            case 'ShowAllReviews':
                break;

            case 'ShowPendingReviews':
                $this -> _search_filter['status'] = 'P';
                break;

            case 'ShowBadReviews':
                $this -> _search_filter['rating'] = array('rate' => '2',
                                                          'range' => '-');
                break;

            case 'ShowGoodReviews':
                $this -> _search_filter['rating'] = array('rate' => '4',
                                                          'range' => '+');
                break;

            case 'SearchReviews':
                $this -> _search_filter['from'] = array(
                    'day'   => $request -> getValueByKey('from_day'),
                    'month' => $request -> getValueByKey('from_month'),
                    'year'  => $request -> getValueByKey('from_year')
                );
                $this -> _search_filter['to'] = array(
                    'day'   => $request -> getValueByKey('to_day'),
                    'month' => $request -> getValueByKey('to_month'),
                    'year'  => $request -> getValueByKey('to_year')
                );
                $this -> _search_filter['author'] = array(
                    'name'    => $request -> getValueByKey('author_name'),
                    'exactly' => $request -> getValueByKey('author_exactly'),
                );
                $this -> _search_filter['ip_address'] =
                         $request -> getValueByKey('ip_address');
                $this -> _search_filter['product'] = array(
                    'name'    => $request -> getValueByKey('product_name'),
                    'id'      => $request -> getValueByKey('product_id'),
                    'exactly' => $request -> getValueByKey('product_exactly')
                );
                $this -> _search_filter['rating'] = array(
                    'rate'  => $request -> getValueByKey('rate'),
                    'range' => $request -> getValueByKey('rate_range')
                );
                $this -> _search_filter['status'] =
                         $request -> getValueByKey('status');
                break;
        }

        // validating data
        if (isset($this -> _search_filter['author']['name']) &&
            !$this -> _search_filter['author']['name'])
            unset($this -> _search_filter['author']);

        if (isset($this -> _search_filter['product']['name']) &&
            !$this -> _search_filter['product']['name'])
            unset($this -> _search_filter['product']);

        if (isset($this -> _search_filter['product']['exactly']) &&
            $this -> _search_filter['product']['exactly'] != 'Y')
            $this -> _search_filter['product']['id'] = '';

        // checking if product name belongs to product id
        // use case: customer changes product name with pre-filled product id
        // from the previous request
        if (isset($this -> _search_filter['product']['id']) &&
            $this -> _search_filter['product']['id'] != '')
        {
            $product_id = modApiFunc(
                              'Customer_Reviews',
                              'getBaseProductInfo',
                              array(
                                  'product_name' =>
                                  $this -> _search_filter['product']['name']
                              )
                          );
            $product_id = @$product_id['product_id'];
            if ($product_id != $this -> _search_filter['product']['id'])
                $this -> _search_filter['product']['id'] = '';
        }

        // clearing rate condition if it includes all reviews
        if (isset($this -> _search_filter['rating']))
            if (($this -> _search_filter['rating']['rate'] == '1'
                 && $this -> _search_filter['rating']['range'] == '+')
                ||
                ($this -> _search_filter['rating']['rate'] == '5'
                 && $this -> _search_filter['rating']['range'] == '-')
               )
                unset($this -> _search_filter['rating']);

        // clearing status condition if it includes all reviews
        if (isset($this -> _search_filter['status'])
            && $this -> _search_filter['status'] == 'All')
            unset($this -> _search_filter['status']);

        // clearing ip condition if it is empty
        if (isset($this -> _search_filter['ip_address'])
            && $this -> _search_filter['ip_address'] == '')
            unset($this -> _search_filter['ip_address']);

        // saving the filter
        modApiFunc('Session', 'set', 'CR_FILTER', $this -> _search_filter);

        // filling up the paginator data
        $this -> _search_filter['paginator'] = null;
        $this -> _search_filter['paginator'] = modApiFunc(
                                                   'Customer_Reviews',
                                                   'searchPgCustomerReviews',
                                                   $this -> _search_filter,
                                                   PAGINATOR_ENABLE
                                               );
    }

    /**
     * Pre-fills quick navigation data
     */
    function fillQuickNavigationData()
    {
        $this -> _quick_navigation_data = array(
            'pending' => modApiFunc('Customer_Reviews',
                                    'getReviewsCount', 'pending'),
            'bad'     => modApiFunc('Customer_Reviews',
                                    'getReviewsCount', 'bad'),
            'good'    => modApiFunc('Customer_Reviews',
                                    'getReviewsCount', 'good'),
            'all'     => modApiFunc('Customer_Reviews',
                                    'getReviewsCount', 'all'),
        );
    }

    /**
     * The main function to output the given view.
     */
    function output()
    {
        global $application;

        // saving request url (to restore it in action classes)
        modApiFunc('Session', 'set', 'CR_URL',
                   modApiFunc('Request', 'selfURL'));

        // getting the list of reviews
        $this -> _found_reviews = modApiFunc('Customer_Reviews',
                                             'searchCustomerReviews',
                                             $this -> _search_filter);

        $template_contents = array(
            'ResultMessageRow' => $this -> outputResultMessage(),
            'SearchReviews'    => $this -> outputSearchReviews(),
            'SearchResults'    => $this -> outputSearchResults()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/manage_customer_reviews/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the Paginator line
     * Note: it is required not to register the tag in the viewer
     *       for proper output
     * See: see the getTag function as well
     */
    function outputPaginatorLine()
    {
        global $application;

        $obj = &$application -> getInstance('PaginatorLine');
        return $obj -> output('CR_List_AZ', 'ManageCustomerReviews');
    }

    /**
     * Outputs the Paginator rows
     * Note: it is required not to register the tag in the viewer
     *       for proper output
     * See: see the getTag function as well
     */
    function outputPaginatorRows()
    {
        global $application;

        $obj = &$application -> getInstance('PaginatorRows');
        return $obj -> output('CR_List_AZ', 'ManageCustomerReviews',
                              'PGNTR_CR_ITEMS');
    }

    /**
     * Outputs the result message
     * the message should be registered in the session (var: ResultMessage)
     * Use case: useful to return the result of an action
     */
    function outputResultMessage()
    {
        global $application;

        if (modApiFunc('Session', 'is_set', 'ResultMessage'))
        {
            $msg = modApiFunc('Session', 'get', 'ResultMessage');
            modApiFunc('Session', 'un_set', 'ResultMessage');
            if ($msg == 'MSG_GNRL_SET_UPDATED') return '';
            $template_contents = array(
                "ResultMessage" => getMsg('CR', $msg)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'customer_reviews/manage_customer_reviews/',
                       'result-message.tpl.html',
                       array()
                   );
        }
        else
        {
            return '';
        }
    }

    /**
     * Outputs the search form
     */
    function outputSearchReviews()
    {
        global $application;

        $start_year = (int)(modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_START_YEAR'));
        $year_offset = (int)((int)(date("Y") - $start_year) + modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_YEAR_OFFSET'));

        $template_contents = array(
            'LabelPending'     => $this -> outputLabel('ShowPendingReviews'),
            'LabelBad'         => $this -> outputLabel('ShowBadReviews'),
            'LabelGood'        => $this -> outputLabel('ShowGoodReviews'),
            'LabelAll'         => $this -> outputLabel('ShowAllReviews'),
            'LabelDate'        => $this -> outputLabel('SearchReviews',
                                                       'from'),
            'LabelAuthor'      => $this -> outputLabel('SearchReviews',
                                                       'author'),
            'LabelIPAddress'   => $this -> outputLabel('SearchReviews',
                                                       'ip_address'),
            'LabelProduct'     => $this -> outputLabel('SearchReviews',
                                                       'product'),
            'LabelRating'      => $this -> outputLabel('SearchReviews',
                                                       'rating'),
            'LabelStatus'      => $this -> outputLabel('SearchReviews',
                                                       'status'),
            'CountPending'     => $this -> outputCount('pending'),
            'CountBad'         => $this -> outputCount('bad'),
            'CountGood'        => $this -> outputCount('good'),
            'CountAll'         => $this -> outputCount('all'),
            'ActionField'      => '<input type="hidden" ' .
                                  HtmlForm :: genHiddenField(
                                      'asc_action',
                                      'SearchReviews'
                                  ) .
                                  ' />',
            'ProductField'     => '<input type="hidden" ' .
                                  HtmlForm :: genHiddenField(
                                      'product_id',
                                      @$this -> _search_filter['product']['id']
                                  ) .
                                  ' />',
            'SelectFromDay'    => HtmlForm :: genDropdownDaysList(
                                      'from_day',
                                      @$this -> _search_filter['from']['day']
                                  ),
            'SelectFromMonth'  => HtmlForm :: genDropdownMonthsList(
                                      'from_month',
                                      @$this -> _search_filter['from']['month']
                                  ),
            'SelectFromYear'   => HtmlForm :: genDropdownYearsList(
                                      'from_year',
                                      @$this -> _search_filter['from']['year'],
                                      $start_year,
                                      $year_offset
                                  ),
            'SelectToDay'      => HtmlForm :: genDropdownDaysList(
                                      'to_day',
                                      @$this -> _search_filter['to']['day']
                                  ),
            'SelectToMonth'    => HtmlForm :: genDropdownMonthsList(
                                      'to_month',
                                      @$this -> _search_filter['to']['month']
                                  ),
            'SelectToYear'     => HtmlForm :: genDropdownYearsList(
                                      'to_year',
                                      @$this -> _search_filter['to']['year'],
                                      $start_year,
                                      $year_offset
                                  ),
            'AuthorField'      => '<input class="form-control input-sm input-large" style="float:left;" type="text"' .
                                  HtmlForm :: genInputTextField(
                                    '128',
                                    'author_name',
                                    52,
                                    prepareHTMLDisplay(@$this -> _search_filter['author']['name'])
                                  ) .
                                  ' />',
            'AuthorCheckbox'    => HtmlForm :: genCheckbox(array(
                'value' => 'Y',
                'is_checked' => (
                    (@$this -> _search_filter['author']['exactly'] == 'Y')
                    ? 'checked'
                    : ''
                ),
                'name' => 'author_exactly',
                'id' => 'author_exactly'
            )),
            'IPAddressField'   => '<input class="form-control input-sm input-large" type="text"' .
                                  HtmlForm :: genInputTextField(
                                      '15',
                                      'ip_address',
                                      52,
                                      prepareHTMLDisplay(@$this -> _search_filter['ip_address'])
                                  ) .
                                  ' />',
            'ProductNameField' => '<input class="form-control input-sm input-large" style="float:left;" type="text"' .
                                  HtmlForm :: genInputTextField(
                                    '128',
                                    'product_name',
                                    52,
                                    prepareHTMLDisplay(@$this -> _search_filter['product']['name'])
                                  ) .
                                  ' />',
            'ProductCheckbox'  => HtmlForm :: genCheckbox(array(
                'value' => 'Y',
                'is_checked' => (
                   (@$this -> _search_filter['product']['exactly'] == 'Y')
                   ? 'checked'
                   : ''
                ),
                'name' => 'product_exactly',
                'id' => 'product_exactly'
            )),
            'RatingSelect'     => HtmlForm :: genDropdownSingleChoice(array(
                'select_name' => 'rate',
                'selected_value' => @$this -> _search_filter['rating']['rate'],
                'id' => 'rate',
		'class' => 'input-small rating-margin-btm',
                'values' => modApiFunc('Customer_Reviews', 'getRateValues')
            )),
            'RatingAddSelect'  => HtmlForm :: genDropdownSingleChoice(array(
                'select_name' => 'rate_range',
                'selected_value' => @$this -> _search_filter['rating']['range'],
                'id' => 'rate_range',
		'class' => 'input-small',
                'values' => array(
                    array(
                        'value'    => '+',
                        'contents' => getMsg('CR', 'CR_ABOVE')
                    ),
                    array(
                        'value'    => '=',
                        'contents' => getMsg('CR', 'CR_EXACTLY')
                    ),
                    array(
                        'value'    => '-',
                        'contents' => getMsg('CR', 'CR_BELOW')
                    ),
                )
            )),
            'StatusSelect'     => HtmlForm :: genDropdownSingleChoice(array(
                'select_name' => 'status',
                'selected_value' => @$this -> _search_filter['status'],
                'id' => 'status',
		'class' => 'input-small',
                'values' => array(
                    array(
                        'value'    => 'All',
                        'contents' => getMsg('CR', 'CR_STATUS_ALL')
                    ),
                    array(
                        'value'    => 'A',
                        'contents' => getMsg('CR', 'CR_STATUS_APPROVED')
                    ),
                    array(
                        'value'    => 'P',
                        'contents' => getMsg('CR', 'CR_STATUS_PENDING')
                    ),
                    array(
                        'value'    => 'N',
                        'contents' => getMsg('CR', 'CR_STATUS_NOTAPPROVED')
                    ),
                )
            ))
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/manage_customer_reviews/',
                   'search-reviews.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the additional style for active elements in the search form
     */
    function outputLabel($label, $add_label = '')
    {
        $output = '';

        $condition = ($label == $this -> _search_filter['asc_action']);
        if ($label == 'SearchReviews')
            $condition = ($condition && @$this -> _search_filter[$add_label]);

        if ($condition)
            $output = ' color: blue;';

        return $output;
    }

    /**
     * Outputs the number of reviews for quick navigation links
     */
    function outputCount($type)
    {
        return intval($this -> _quick_navigation_data[$type]);
    }

    /**
     * Outputs the result of searching
     */
    function outputSearchResults()
    {
        global $application;

        $template_contents = array(
            'SearchTotal'    => $this -> outputSearchTotal(),
            'TopButtons'     => $this -> outputTopButtons(),
            'ResultForm'     => $this -> outputResultForm(),
            'BottomButtons'  => $this -> outputBottomButtons(),
            'ResActionField' => '<input type="hidden" ' .
                                HtmlForm :: genHiddenField(
                                    'asc_action',
                                    'update_customer_reviews'
                                ) .
                                ' />',
            'ResModeField'   => '<input type="hidden" ' .
                                HtmlForm :: genHiddenField(
                                    'mode',
                                    'update'
                                ) .
                                ' />',
            'AddLinkID'      => ((@$this -> _search_filter['product']['id'])
                                    ? '&product_id=' .
                                       $this -> _search_filter['product']['id']
                                    : ''
                                )
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/manage_customer_reviews/',
                   'search-results.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the total line for search results
     */
    function outputSearchTotal()
    {
        $total = modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows');

        if ($total > 0)
        {
            $output = $total . ' ' .
                      (($total == 1)
                          ? getMsg('CR', 'CR_ONE_REVIEW_FOUND')
                          : getMsg('CR', 'CR_SEVERAL_REVIEWS_FOUND')
                      ) . ' ' . getMsg('CR', 'CR_SHOWING') . ' ';
            if (isset($this -> _search_filter['paginator']))
                $output .= ($this -> _search_filter['paginator'][0] + 1) .
                           ' - ' .
                           min($this -> _search_filter['paginator'][0] +
                               $this -> _search_filter['paginator'][1], $total);
            else
                $output .= '1 - ' . $total;
        }
        else
        {
            $output = getMsg('CR', 'CR_NO_REVIEWS_FOUND');
        }

        return $output;
    }

    /**
     * Outputs the top buttons
     * Note: if the search result is empty it returns an empty string
     */
    function outputTopButtons()
    {
        global $application;

        $output = '';

        $template_contents = array(
            'AddLinkID' => ((@$this -> _search_filter['product']['id'])
                               ? '&product_id=' .
                                  $this -> _search_filter['product']['id']
                               : ''
                           )
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);

        if (count($this -> _found_reviews) > 0)
            $output = $this -> mTmplFiller -> fill(
                          'customer_reviews/manage_customer_reviews/',
                          'search-results-buttons-top.tpl.html',
                          array()
                      );
        else
            $output = $this -> mTmplFiller -> fill(
                          'customer_reviews/manage_customer_reviews/',
                          'search-results-nobuttons.tpl.html',
                          array()
                      );

        return $output;
    }

    /**
     * Outputs the bottom buttons
     * Note: if the search result is empty it returns an empty string
     * Note: separated from outputTopButtons for customizing purposes
     *       (to have ability to use different styles and id for the buttons)
     */
    function outputBottomButtons()
    {
        global $application;

        $output = '';

        $template_contents = array(
            'AddLinkID' => ((@$this -> _search_filter['product']['id'])
                               ? '&product_id=' .
                                  $this -> _search_filter['product']['id']
                               : ''
                           )
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);

        if (count($this -> _found_reviews) > 0)
            $output = $this -> mTmplFiller -> fill(
                          'customer_reviews/manage_customer_reviews/',
                          'search-results-buttons-bottom.tpl.html',
                          array()
                      );

        return $output;
    }

    /**
     * Outputs the form for the found reviews
     */
    function outputResultForm()
    {
        if (count($this -> _found_reviews) <= 0)
            return '';

        global $application;

        $template_contents = array(
            'ResCheckbox'    => HtmlForm :: genCheckbox(array(
                'value'      => 'Y',
                'name'       => '',
                'id'         => 'SelectAll',
                'is_checked' => false,
                'onclick'    => 'javascript: selectAllCheckboxes(this);'
            )),
            'ResultRecords'  => $this -> outputResultRecords()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/manage_customer_reviews/',
                   'search-results-form.tpl.html',
                   array()
               );
    }

    /**
     * Outputs a review for the search result form
     */
    function outputResultRecords()
    {
        global $application;

        $output = '';

        foreach($this -> _found_reviews as $record)
        {
            $template_contents = array(
                'DateFilter'      => $this -> outputRecordFilter('date',
                                                                 $record),
                'ProductFilter'   => $this -> outputRecordFilter('product',
                                                                 $record),
                'AuthorFilter'    => $this -> outputRecordFilter('author',
                                                                 $record),
                'IPFilter'        => $this -> outputRecordFilter('ip_address',
                                                                 $record),
                'DateValue'       => $this -> outputRecordValue('date',
                                                                $record),
                'TimeValue'       => $this -> outputRecordValue('time',
                                                                $record),
                'ProductValue'    => $this -> outputRecordValue('product_name',
                                                                $record),
                'AuthorValue'     => $this -> outputRecordValue('author',
                                                                $record),
                'IPValue'         => $this -> outputRecordValue('ip_address',
                                                                $record),
                'ReviewStyle'     => (($record['product_cr'] == 5 ||
                                       $record['product_cr'] == 6)
                                         ? ''
                                         : ' color: #BBBBBB;'
                                     ),
                'ReviewValue'     => str_replace(
                                         "\n",
                                         '<br />',
                                         $this -> outputRecordValue('review',
                                                                    $record)
                                     ),
                'ProductOpenLink' => $this -> outputProductOpenLink($record),
                'RatingValue'     => $this -> outputRating($record),
                'EditLinkID'      => 'edit_' . $record['cr_id'],
                'EditLinkCR'      => '&cr_id=' . $record['cr_id'],
                'StatusValue'     => $this -> outputStatusValue($record),
                'DeleteValue'     => HtmlForm :: genCheckbox(array(
                    'value'      => $record['cr_id'],
                    'name'       => 'review_id[]',
                    'id'         => 'select_' .
                                    $record['cr_id'],
                    'is_checked' => false,
                    'onclick'    => 'javascript: checkSelectedRows(this);'
                )),
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'customer_reviews/manage_customer_reviews/',
                           'search-results-form-record.tpl.html',
                           array()
                       );
        }

        return $output;
    }

    /**
     * Generates a link to pre-fill search filter for the given field
     */
    function outputRecordFilter($filter, $record)
    {
        $output = '';

        switch($filter)
        {
            case 'date':
                $date = explode(' ', $record['datetime']);
                $date = explode('-', $date[0]);
                $output = "fillSearchField('from_day', '" .
                          $date[2] . "'); fillSearchField('from_month', '" .
                          $date[1] . "'); fillSearchField('from_year', '" .
                          $date[0] . "'); fillSearchField('to_day', '" .
                          $date[2] . "'); fillSearchField('to_month', '" .
                          $date[1] . "'); fillSearchField('to_year', '" .
                          $date[0] . "'); submitHandler('SearchReviews');";
                break;

            case 'product':
                $output = "javascript: fillSearchField('product_name', '" .
                          str_replace("'", "\'", $record['product_name']) .
                          "'); " .
                          "fillSearchField('product_id', '" .
                          $record['product_id'] .
                          "'); " .
                          "checkSearchCheckbox('product_exactly', 'checked');" .
                          " submitHandler('SearchReviews')";
                break;

            case 'ip_address':
                $output = "javascript: fillSearchField('ip_address', '" .
                          str_replace("'", "\'", $record['ip_address']) .
                          "'); submitHandler('SearchReviews')";
                break;

            case 'author':
                $output = "javascript: fillSearchField('author_name', '" .
                          htmlspecialchars($record['author'], ENT_QUOTES) . "'); " .
                          "checkSearchCheckbox('author_exactly', 'checked');" .
                          " submitHandler('SearchReviews')";
                break;

            default:
                $output = 'javascript: void(0);';
        }

        return $output;
    }

    /**
     * Returns the value of the given field for the given record
     */

    function outputRecordValue($field, $record)
    {
        return prepareHTMLDisplay(@$record[$field]);
    }

    /**
     * Outputs the status select box for the given record
     */
    function outputStatusValue($record)
    {
        $values = array();
        $values[] = array(
                        'value' => 'A',
                        'contents' => getMsg('CR', 'CR_STATUS_APPROVED')
                    );
        if ($record['status'] == 'P')
            $values[] = array(
                            'value' => 'P',
                            'contents' => getMsg('CR', 'CR_STATUS_PENDING')
                        );
        $values[] = array(
                        'value' => 'N',
                        'contents' => getMsg('CR', 'CR_STATUS_NOTAPPROVED')
                    );

        return HtmlForm :: genDropdownSingleChoice(array(
                   'select_name'    => 'data[' . $record['cr_id'] . '][status]',
                   'selected_value' => $record['status'],
                   'onChange'       => 'javascript: onStatusChanged(' .
                                       $record['cr_id'] .
                                       ')',
                   'id'             => 'data[' . $record['cr_id'] . '][status]',
		   		   'class'	    => 'form-control input-small',
                   'values'         => $values
               ));
    }

    /**
     * Generates a link to product info page
     */
    function outputProductOpenLink($record)
    {
        global $application;

        $prod_info = &$application -> getInstance('CProductInfo',
                                                  $record['product_id']);
        return "javascript:openURLinNewWindow('" .
               $prod_info -> getProductInfoLink($record['product_id'], '') .
               "', 'ViewProduct');";
    }

    /**
     * Outputs rating for the given record
     */
    function outputRating($record)
    {
        global $application;

        $output = '';

        if (!is_array($record['rating']) || count($record['rating']) <= 0)
            return modApiFunc('TmplFiller',
                              'fill',
                              'customer_reviews/manage_customer_reviews/',
                              'search-results-form-record-norate.tpl.html',
                              array());

        foreach($record['rating'] as $rate)
        {
            $template_contents = array(
                'RateNameStyle' => (($rate['rate_label'])
                                       ? (($rate['visible'] == 'Y' &&
                                           ($record['product_cr'] == 5 ||
                                            $record['product_cr'] == 7))
                                           ? ''
                                           : ' color: #BBBBBB;')
                                       : ' color: red;'),
                'RateName'      => (($rate['rate_label'])
                                       ? $rate['rate_label']
                                       : getMsg('CR', 'CR_UNKNOWN_RATE')),
                'RateStars'     => $this -> outputRateStars($rate['rate']),
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'customer_reviews/manage_customer_reviews/',
                           'search-results-form-record-rate.tpl.html',
                           array()
                       );
        }

        return $output;
    }

    /**
     * Outputs the stars for the given rate
     */
    function outputRateStars($rate)
    {
        global $application;

        $output = '';

        for($i = 1; $i <= 5; $i++)
        {
            $template_contents = array(
                'RateImage' => (($i <= $rate) ? 'star_full.gif'
                                              : 'star_blank.gif')
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'customer_reviews/manage_customer_reviews/',
                           'search-results-form-record-rate-image.tpl.html',
                           array()
                       );
        }

        return $output;
    }

    /**
     * Returns the tag value
     * Note: since the PaginatiorLine and PaginatorRows tags cannot be
     *       registered inside the viewer the way the function processes
     *       the tags is different
     */
    function getTag($tag)
    {
        if ($tag == 'PaginatorLine')
            return $this -> outputPaginatorLine();

        if ($tag == 'PaginatorRows')
            return $this -> outputPaginatorRows();

        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $mTmplFiller;
    var $_Template_Contents;
    var $_search_filter;
    var $_quick_navigation_data;
    var $_found_reviews;
}
?>