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
 * Output Gift Certificates list
 *
 * @package GiftCertificate
 * @author Alexey Florinsky
 */
class GiftCertificateListView
{
    function GiftCertificateListView()
    {
        $this->filler = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/gc_list/');
    }

    function output()
    {
        global $application;

        loadClass('GiftCertificateCreator');
        loadClass('GiftCertificateApi');

        $application->registerAttributes(array(
            'Local_Items',
            'Local_GC_id',
            'Local_GC_Code',
            'Local_GC_From',
            'Local_GC_To',
            'Local_GC_Amount',
            'Local_GC_Remainder',
            'Local_GC_Sendtype',
            'Local_GC_Status',
            'Local_GC_date_Created',
            'AddGiftCertificateHref',
            'Local_Dell_GC_Href',
        ));
        $res = $this->filler->fill("", "container.tpl.html",array());
        return $res;
    }

    function getItemList()
    {
        $this -> paginator_name = 'GiftCertificateList_AZ';
        $paginator = modAPIFunc('paginator', 'setCurrentPaginatorName', $this->paginator_name);

        $f = new GiftCertificateFilter();
        $l = new GiftCertificateList($f);
        $html = '';

        $counter = 0;
        $l->reset();
        while( $gc = $l->next() )
        {
            $this->__gc_item = $gc;
            $html .= $this->filler->fill("", "items.tpl.html",array());
            $counter++;
        }

        if ($counter < 10)
        {
            for ($i=0; $i<(10-$counter); $i++)
                $html .= $this->filler->fill("", "items_empty.tpl.html",array());
        }

        return $html;
    }

    function getTag($tag)
    {
        global $application;

        $value = null;

        switch($tag)
        {
            case 'Local_Items':
                    $value = $this->getItemList();
                break;

                # paginator
                case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output($this->paginator_name, "GiftCertificateListView", 'PGNTR_GC_ITEMS');
                break;

                # override the PaginatorRows tag behavior
                case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output($this->paginator_name, "GiftCertificateListView", 'PGNTR_GC_ITEMS');
                break;

        	case 'Local_Dell_GC_Href':
                $request = new Request();
                $request->setView(CURRENT_REQUEST_URL);
                $request->setAction('GiftCertificateDellAction');
                $request->setKey('gc_code', '');
                $value = $request->getURL();
        	    break;

            default:
                    if (strpos($tag, 'Local_') === 0)
                    {
                        $gc_field = substr(strtolower($tag), strlen('local_'));
                        if (isset($this->__gc_item[$gc_field]))
                        {
                            switch ($gc_field)
                            {
                                case 'gc_amount':
                                case 'gc_remainder':
                                        $value = modApiFunc('Localization', 'currency_format', $this->__gc_item[$gc_field]);
                                    break;
                                case 'gc_date_created':
                                        $value = modApiFunc('Localization', 'date_format', intval($this->__gc_item[$gc_field]));
                                    break;
                                case 'gc_sendtype':
                                        if ($this->__gc_item[$gc_field] == GC_SENDTYPE_EMAIL) $value = getMsg('GCT', 'GC_SENDTYPE_EMAIL');
                                        if ($this->__gc_item[$gc_field] == GC_SENDTYPE_POST) $value = getMsg('GCT', 'GC_SENDTYPE_POST');
                                    break;
                                case 'gc_status':
                                        if ($this->__gc_item[$gc_field] == GC_STATUS_ACTIVE) $value = getMsg('GCT', 'GC_STATUS_ACTIVE');
                                        if ($this->__gc_item[$gc_field] == GC_STATUS_BLOCKED) $value = getMsg('GCT', 'GC_STATUS_BLOCKED');
                                        if ($this->__gc_item[$gc_field] == GC_STATUS_PENDING) $value = getMsg('GCT', 'GC_STATUS_PENDING');
                                    break;
                                default :
                                        $value = prepareHTMLDisplay($this->__gc_item[$gc_field]);
                                    break;
                            }
                        }
                    }
                break;
        }

        return $value;
    }

    var $filler;
    var $__gc_item;
    var $paginator_name;
}

?>