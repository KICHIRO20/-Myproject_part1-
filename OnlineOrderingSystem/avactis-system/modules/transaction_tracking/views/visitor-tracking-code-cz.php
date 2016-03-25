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
 * VisitorTrackingCode view.
 *
 * @package TransactionTracking
 * @author IlyaVassilevsky
 */

class VisitorTrackingCode
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  VisitorTrackingCode constructor.
     */
    function VisitorTrackingCode()
    {
    }


    function output( $part = 'footer' )
    {
        global $application;

        #Define whether to output the view or not
        if (isset($this->NoView) && $this->NoView)
        {
            $application->outputTagErrors(true, "TransactionTracking", "Errors");
            return "";
        }

        /* Output composition */
        $output = '';
        $InstalledModules = modApiStaticFunc("TransactionTracking", "getInstalledModules");

        foreach( $InstalledModules as $method_uid => $info )
        {
        	if( $info['status_active'] == DB_TRUE )
        	{
        		$output .= $this->outputMethodHtmlCode( $method_uid, $part ) . "\n";
        	}
        }
        return $output;
    }


    function outputMethodHtmlCode( $method_uid, $part )
    {
    	switch( $method_uid )
    	{
    		case MODULE_GOOGLE_ANALYTICS_UID:

    			$value = $this->outputGAsnippet( $part );
    			break;

    		default:
    			$value = '';
    	}
    	return $value;
    }


    function outputGAsnippet( $part )
    {
        if( $part == 'header' )
        {
	        $header_template = TransactionTracking::getIncludedFileContents("google_analytics_header.tpl.html");

                $settings = TransactionTracking::getModulesSettings();
	        $GA_ACCOUNT_NUMBER = $settings[MODULE_GOOGLE_ANALYTICS_UID]['GA_ACCOUNT_NUMBER'];

	        $header_snippet = strtr( $header_template, array( "UA-XXXXX-1" => $GA_ACCOUNT_NUMBER ) );

	        return $header_snippet;
        }
        else
        {
	        $footer_snippet = TransactionTracking::getIncludedFileContents("google_analytics_footer.tpl.html");
        	return $footer_snippet;
        }
    }
}
?>