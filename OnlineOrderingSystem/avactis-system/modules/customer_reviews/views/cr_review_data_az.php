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
 * Definition of CR_Review_Data viewer
 * The viewer is used to show/edit a given review
 */
class CR_Review_Data
{
    /**
     * Constructor
     */
    function CR_Review_Data()
    {
        loadCoreFile('html_form.php');
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * The main function to output the viewer
     */
    function output()
    {
        global $application;

        // getting customer review id
        // if not specified then a new review is being added
        $cr_id = modApiFunc('Request', 'getValueByKey', 'cr_id');
        if (!$cr_id)
            $cr_id = 0;

        $this -> _Review_Data = modApiFunc('Customer_Reviews',
                                           'searchCustomerReviews',
                                           array('cr_id' => $cr_id));

        // getting review data
        if (!empty($this -> _Review_Data))
        {
            // the cr_id is specified and valid
            $this -> _Review_Data = array_pop($this -> _Review_Data);
        }
        else
        {
            // the cr_id is eihter not specified or not valid
            // assuming adding a new review
            $this -> _Review_Data = array();

            // getting product data if specified
            // use case: if product filter is set on the customer reviews page
            //           we assume the new review is for the selected product
            $product_id = modApiFunc('Request', 'getValueByKey', 'product_id');

            $product_data = modApiFunc(
                                'Customer_Reviews',
                                'getBaseProductInfo',
                                array('product_id' => $product_id)
                            );

            // if a valid product_id specified pre-fill the product data
            if (@$product_data['product_id'])
            {
                $this -> _Review_Data['product_id'] = $product_id;
                $this -> _Review_Data['product_name'] =
                             $product_data['product_name'];
                $this -> _Review_Data['product_cr'] =
                             $product_data['product_cr'];
            }

            // getting the current list of rates
            $this -> _Review_Data['rating_cz'] = modApiFunc(
                'Customer_Reviews',
                'getCustomerReviewsRates',
                0
            );
        }

        // restoring data from session if any
        // use case: restoring submitted form with an error
        if (modApiFunc('Session', 'is_set', 'SavedReviewData'))
        {
            $saved_data = modApiFunc('Session', 'get', 'SavedReviewData');
            modApiFunc('Session', 'un_set', 'SavedReviewData');
            if (is_array($saved_data))
            {
                // getting product info if product_id is specified
                if ($saved_data['product_id'])
                {
                    $product_data = modApiFunc(
                        'Customer_Reviews',
                        'getBaseProductInfo',
                        array('product_id' => $saved_data['product_id'])
                    );
                    $saved_data['product_name'] = $product_data['product_name'];
                    $saved_data['product_cr'] = $product_data['product_cr'];
                }

                // restoring the data
                foreach($saved_data as $key => $value)
                {
                    // for non-array fields just copy the values
                    if (!in_array($key, array('rating', 'rating_cz', 'cr_id')))
                        $this -> _Review_Data[$key] = $value;

                    // restoring the rating
                    if ($key == 'rating' && is_array($value)
                        && is_array($this -> _Review_Data['rating_cz']))
                        foreach($this -> _Review_Data['rating_cz'] as $k => $v)
                        {
                            foreach($value as $cr_rl_id => $rate)
                                if ($v['cr_rl_id'] == $cr_rl_id)
                                {
                    // use new_rate field to keep the original rating
                    $this -> _Review_Data['rating_cz'][$k]['new_rate'] = $rate;
                    break;
                                }
                        }
                }
            }
        }

        $template_contents = array(
            'PageJSCode'    => $this -> outputJSCode(),
            'ModeField'     => $this -> outputField('mode'),
            'ActionField'   => $this -> outputField('action'),
            'ReviewIDField' => $this -> outputField('cr_id'),
            'IPField'       => $this -> outputField('ip_address'),
            'ResultMessage' => $this -> outputResultMessage(),
            'EditPageTitle' => ((@$this -> _Review_Data['cr_id'] > 0)
                               ? getMsg('CR', 'CR_EDIT_REVIEW')
                               : getMsg('CR', 'CR_ADD_REVIEW')),
            'ReviewData'    => $this -> outputReviewData()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/review_data/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the result message
     * Note: the message is taken from the session
     * Use case: it contains the result of the previous action
     */
    function outputResultMessage()
    {
        global $application;

        if (modApiFunc('Session', 'is_set', 'ResultMessage'))
        {
            $msg = modApiFunc('Session', 'get', 'ResultMessage');
            modApiFunc('Session', 'un_set', 'ResultMessage');
            $template_contents = array(
                "ResultMessage" => getMsg('CR', $msg)
            );
            $this -> _Template_Contents=$template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'customer_reviews/review_data/',
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
     * Outputs the review data
     */
    function outputReviewData()
    {
        global $application;

        $template_contents = array(
            'ReviewDate'      => $this -> outputReviewDate(),
            'ReviewAuthor'    => $this -> outputField('author'),
            'ReviewIPAddress' => ((isset($this -> _Review_Data['ip_address']))
                                     ? $this -> _Review_Data['ip_address']
                                     : $_SERVER['REMOTE_ADDR']
                                 ),
            'ReviewProduct'   => $this -> outputReviewProduct(),
            'ReviewReview'    => $this -> outputField('review'),
            'ReviewRating'    => $this -> outputReviewRating(),
            'ReviewStatus'    => $this -> outputField('status'),
            'ReviewDisabled'  => ((isset($this -> _Review_Data['product_cr'])
                                   && $this -> _Review_Data['product_cr'] != 5
                                   && $this -> _Review_Data['product_cr'] != 6)
                                 ? '<br /><span style="font-weight: normal;">('
                                   . getMsg('CR', 'CR_MSG_REVIEW_DISABLED')
                                   . ')</span>'
                                 : ''),
            'RatingDisabled'  => ((isset($this -> _Review_Data['product_cr'])
                                   && $this -> _Review_Data['product_cr'] != 5
                                   && $this -> _Review_Data['product_cr'] != 7)
                                 ? '<br /><span style="font-weight: normal;">('
                                   . getMsg('CR', 'CR_MSG_RATING_DISABLED')
                                   . ')</span>'
                                 : '')
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/review_data/',
                   'review-data.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the product data
     */
    function outputReviewProduct()
    {
        global $application;

        $template_contents = array(
            'ReviewProductID'   => '<input type="hidden" ' .
                                   HtmlForm :: genHiddenField(
                                       'review_data[product_id]',
                                       @$this -> _Review_Data['product_id']
                                   ) .
                                   ' />',
            'ReviewProductName' => '<input type="text"' .
                                   HtmlForm :: genInputTextField(
                                       '255',
                                       'review_data[product_name]',
                                       50,
                                       @$this -> _Review_Data['product_name'],
                                       'readonly="readonly" style="border: 0" class="form-control input-sm input-large"'
                                   ) .
                                   ' />',
            'ProductID'         => @$this -> _Review_Data['product_id']
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/review_data/',
                   'output-review-product.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the rating
     */
    function outputReviewRating()
    {
        global $application;

        $output = '';

        if (empty($this -> _Review_Data['rating_cz']))
            return $this -> mTmplFiller -> fill(
                       'customer_reviews/review_data/',
                       'output-review-rating-empty.tpl.html',
                       array()
                   );

        $rate_values = modApiFunc('Customer_Reviews', 'getRateValues');

        foreach($this -> _Review_Data['rating_cz'] as $rate)
        {
            $template_contents = array(
                'RateLabel'  => $rate['rate_label'],
                'RateHidden' => (($rate['visible'] == 'Y')
                                    ? ''
                                    : '(' . getMsg('CR', 'CR_RATE_HIDDEN') . ')'
                                ),
                'RateValue'  => $this -> outputRateStars($rate['rate']),
                'RateSelect' => HtmlForm :: genDropdownSingleChoice(array(
                    'select_name'    => 'review_data[rating][' .
                                        $rate['cr_rl_id'] . ']',
                    'selected_value' => ((isset($rate['new_rate']))
                                          ? $rate['new_rate']
                                          : $rate['rate']),
                    'onChange'       => '',
                    'class'       => 'form-control input-sm input-small',
                    'id'             => 'review_data[rating][' .
                                        $rate['cr_rl_id'] . ']',
                    'values'         => $rate_values
                ))
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'customer_reviews/review_data/',
                           'output-review-rating-record.tpl.html',
                           array()
                       );
        }

        return $output;
    }

    /**
     * Outputs the date/time select boxes
     */
    function outputReviewDate()
    {
        global $application;

        if (isset($this -> _Review_Data['datetime']))
        {
            $datetime = modApiFunc('Localization', 'SQL_date_format',
                                   $this -> _Review_Data['datetime'],
                                   'Y-m-d H:i:s');
        }
        else
        {
            $datetime = modApiFunc('Localization', 'SQL_date_format',
                                   date('Y-m-d H:i:s'),
                                   'Y-m-d H:i:s');
        }

        list($date, $time) = explode(' ', $datetime);
        $date = explode('-', $date);
        $time = explode(':', $time);

        $template_contents = array(
            'SelectDay'    => HtmlForm :: genDropdownDaysList(
                                              'review_data[day]',
                                              $date[2]
                                          ),
            'SelectMonth'  => HtmlForm :: genDropdownMonthsList(
                                              'review_data[month]',
                                              $date[1]
                                          ),
            'SelectYear'   => HtmlForm :: genDropdownYearsList(
                                  'review_data[year]',
                                  $date[0],
                                  CUSTOMER_REVIEWS_HIDDEN_START_YEAR,
                                  max(1, intval(date('Y'))
                                         - CUSTOMER_REVIEWS_HIDDEN_START_YEAR
                                  )
                              ),
            'SelectHour'   => $this -> outputField('hour', $time[0], 24),
            'SelectMinute' => $this -> outputField('minute', $time[1], 60),
            'SelectSecond' => $this -> outputField('second', $time[2], 60)
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/review_data/',
                   'output-review-date.tpl.html',
                   array()
               );
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
     * Outputs the field for the given params
     */
    function outputField($field_type, $def_value = '', $max_select_count = 0)
    {
        $return_value = '';

	switch($field_type)
        {
            case 'action':
               $return_value = '<input type="hidden" ' .
                               HtmlForm :: genHiddenField(
                                               'asc_action',
                                               'update_review_data'
                                           ) .
                               ' />';
               break;
            case 'mode':
               $return_value = '<input type="hidden" ' .
                               HtmlForm :: genHiddenField('mode', 'update') .
                               ' />';
               break;
            case 'cr_id':
               $return_value = '<input type="hidden" ' .
                               HtmlForm :: genHiddenField(
                                   'review_data[cr_id]',
                                   @$this -> _Review_Data['cr_id']
                               ) .
                               ' />';
               break;
            case 'ip_address':
               $return_value = '<input type="hidden" ' .
                               HtmlForm :: genHiddenField(
                                   'review_data[ip_address]',
                                   (isset($this -> _Review_Data['ip_address'])
                                     ? $this -> _Review_Data['ip_address']
                                     : $_SERVER['REMOTE_ADDR']
                                   )
                               ) .
                               ' />';
               break;
            case 'author':
               $return_value = '<input class="form-control input-sm input-large" type="text"' .
                               HtmlForm :: genInputTextField(
                                   '128',
                                   'review_data[author]',
                                   '70',
                                   prepareHTMLDisplay(
                                       @$this -> _Review_Data['author']
                                   )
                               ) .
                               ' />';
               break;
            case 'review':
                $return_value = '<textarea class="form-control input-large" name="review_data[review]" cols="53" rows="8">' .
                                prepareHTMLDisplay(
                                    @$this -> _Review_Data['review']
                                ) .
                                '</textarea>';
                break;
            case 'status':
                $values = array();
                $values[] = array(
                                'value' => 'A',
                                'contents' => getMsg('CR',
                                                     'CR_STATUS_APPROVED')
                                 );
                if (@$this -> _Review_Data['status'] == 'P')
                    $values[] = array(
                                    'value' => 'P',
                                    'contents' => getMsg('CR',
                                                         'CR_STATUS_PENDING')
                                );
                $values[] = array(
                                'value' => 'N',
                                'contents' => getMsg('CR', 'CR_STATUS_NOTAPPROVED')
                            );

                $return_value = HtmlForm :: genDropdownSingleChoice(array(
                    'select_name'    => 'review_data[status]',
                    'selected_value' => @$this -> _Review_Data['status'],
                    'onChange'       => '',
                    'id'             => 'review_data[status]',
                    'class'       => 'form-control input-sm input-small',
                    'values'         => $values
                ));
                break;
            case 'hour':
            case 'minute':
            case 'second':
                $values = array();
                for($i = 0; $i < $max_select_count; $i++)
                    $values[] = array('value'    => sprintf("%02d", $i),
                                      'contents' => sprintf("%02d", $i));
                $return_value = HtmlForm :: genDropdownSingleChoice(array(
                    'select_name'    => 'review_data[' . $field_type . ']',
                    'selected_value' => $def_value,
                    'id'             => 'review_data[' . $field_type . ']',
                    'values'         => $values
                ));
                break;
        }

        return $return_value;
    }

    /**
     * Outputs the parent window reloading javascript code if needed
     * use case: the session variable is set in the action class
     */
    function outputJSCode()
    {
        if (modApiFunc('Session', 'is_set', 'CR_ReloadParentWindow'))
        {
            modApiFunc('Session', 'un_set', 'CR_ReloadParentWindow');
            return $this -> mTmplFiller -> fill(
                                'customer_reviews/review_data/',
                                'reload-parent-js.tpl.html', array()
                            );
        }

        return '';
    }

    /**
     * Processes the tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $_Review_Data;
    var $mTmplFiller;
}

?>