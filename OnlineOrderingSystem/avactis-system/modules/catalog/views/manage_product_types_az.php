<?php

?><?php

class ManageProductTypes
{
	/**
	 * @ display the product types
	 */
    function outputProductTypes(){
    	global $application;

    	$this->TemplateFiller = $application->getInstance('TmplFiller');
    	$application->registerAttributes(array(
    			 'EditLink'
    			,'TypeName'
    			,'TypeDescription'
    			//'ProductTypes'
    	));
    	$result = '';
    	$types = modApiFunc('Catalog', 'getProductTypes');
    	foreach ($types as $id => $type)
    	{
    		$this->_type = $type;
    		$result .= $this->TemplateFiller->fill("catalog/product_type_edit/", "manage.tpl.html", array());
    	}
    	return $result;
    }
    /**
     * @ describe the function ->.
     */
    function output()
    {
        global $application;

        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $application->registerAttributes(array(
           // 'EditLink'
           //,'TypeName'
           //,'TypeDescription'
           'ProductTypes'
        ));
        $result = '';
        $result .= $this->TemplateFiller->fill("catalog/product_type_edit/", "product-type-container.tpl.html", array());
        /* $types = modApiFunc('Catalog', 'getProductTypes');
        foreach ($types as $id => $type)
        {
            $this->_type = $type;
            $result .= $this->TemplateFiller->fill("catalog/product_type_edit/", "manage.tpl.html", array());
        } */

        return $result;
    }

    /**
     * @ describe the function ->getTag.
     */
    function getTag($tag)
    {
    	global $application;
    	$value = null;
    	switch ($tag)
    	{
    		case 'EditLink':
            	$request = new Request();
            	$request->setAction('SetCurrentProductType');
            	$request->setView('EditProductType');
            	$request->setKey('type_id', $this->_type['id']);
            	$value = $request->getURL();
    			break;

    		case 'TypeName':
    		    $value = $this->_type['name'];
    			break;

            case 'TypeDescription':
                $value = '';
                if ($this->_type['description']!='')
                {
                    $value = '&nbsp;-&nbsp;'.$this->_type['description'];
                }
                break;
            case 'ProductTypes' :
            	$value = $this->outputProductTypes();
            	break;

    		default:
    			break;
    	}
    	return $value;
    }

    var $TemplateFiller;
    var $_type;
}

?>