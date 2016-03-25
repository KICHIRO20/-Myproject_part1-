<?php

/**
 * @package Catalog
 * @author Egor V. Derevyankin
 * 
 */

global $application;
$MR = &$application->getInstance('MessageResources');

$tables = Catalog::getTables();           
$query = new DB_Table_Create($tables);

$tag_to_id = array();

loadCoreFile('obj_xml.php');
$parser = new xml_doc(file_get_contents(CATALOG_INSTALL_DATA_XML));
$parser->parse();

foreach($parser->xml_index as $tag)
{
    if($tag->name == 'CATALOG_INSTALL_DATA')
    {
        foreach($tag->children as $id_child)
        {
            switch($id_child->name)
            {
                case 'INPUT_TYPES':
                    foreach($id_child->children as $its_child)
                    {
                        if($its_child->name == 'TYPE')
                        {
                            $type_id = null;
                            $type_name = null;
                            $type_values = array();
                            foreach($its_child->children as $t_child)
                            {
                                switch($t_child->name)
                                {
                                    case 'ID':   $type_id = $t_child->contents;     break;
                                    case 'NAME': $type_name = $t_child->contents;   break;
                                    case 'VALUES':
                                        foreach($t_child->children as $vs_child)
                                        {
                                            if($vs_child->name == 'VALUE')
                                            {
                                                $_id = $vs_child->attributes['ID'];
                                                $_val = $vs_child->attributes['VALUE'];
                                                
                                                if(strpos($_id,'const-')===0)
                                                {
                                                    $_id = constant(str_replace('const-','',$_id));
                                                };
                                                if(strpos($_val,'lang-')===0)
                                                {
                                                    $_val = str_replace('lang-','',$_val);
                                                };
                                                
                                                $type_values[$_id] = $_val;
                                            };
                                        }
                                        break;
                                    default: break;
                                };
                            };
                            if($type_id != null and $type_name != null)
                            {
                                $columns = $tables['input_types']['columns'];
                                $query = new DB_Insert('input_types');
                                $query->addInsertValue($type_id, $columns['id']);
                                $query->addInsertValue($type_name, $columns['name']);
                                $application->db->getDB_Result($query);
                                
                                if(!empty($type_values))
                                {
                                    $columns = $tables['input_type_values']['columns'];
                                    foreach($type_values as $id => $val)
                                    {
                                        $query = new DB_Insert('input_type_values');
                                        $query->addInsertValue($id, $columns['id']);
                                        $query->addInsertValue($val, $columns['value']);
                                        $query->addInsertValue($type_id, $columns['it_id']);
                                        $application->db->getDB_Result($query);
                                    };
                                };
                            };
                        };
                    };
                    break;
                case 'ATTRIBUTE_GROUPS':
                    foreach($id_child->children as $ags_child)
                    {
                        if($ags_child->name == 'GROUP')
                        {
                            $group_id = null;
                            $group_name = null;
                            $group_so = 0;
                            $group_attrs = array();
                            foreach($ags_child->children as $g_child)
                            {
                                switch($g_child->name)
                                {
                                    case 'ID':          $group_id = $g_child->contents;   break;
                                    case 'NAME':        $group_name = $g_child->contents; break;
                                    case 'SORT_ORDER':  $group_so = $g_child->contents;   break;
                                    case 'ATTRIBUTES':
                                        $attrs_type = $g_child->attributes['TYPE'];
                                        foreach($g_child->children as $atrs_child)
                                        {
                                            if($atrs_child->name == 'ATTRIBUT')
                                            {
                                                $attr_props = array(
                                                    'id' => null
                                                   ,'input_id' => null
                                                   ,'unit_type' => null
                                                   ,'view_tag' => null
                                                   ,'name' => null
                                                   ,'descr' => null
                                                   ,'allow_html' => null
                                                   ,'min' => null
                                                   ,'max' => null
                                                   ,'size' => null
                                                   ,'multilang' => null
                                                   ,'sort_order' => null
                                                   ,'type' => $attrs_type
                                                );
                                                foreach($atrs_child->children as $a_child)
                                                {
                                                    if(in_array(strtolower($a_child->name),array_keys($attr_props)))
                                                    {
                                                        $attr_props[strtolower($a_child->name)] = $a_child->contents;
                                                    };
                                                };
                                                if(!in_array(null,$attr_props,true))
                                                {
                                                    $group_attrs[] = $attr_props;
                                                };
                                            };
                                        };
                                        break;
                                    default: break;
                                };
                            };
                            if($group_id != null and $group_name != null)
                            {
                                $columns = $tables['attribute_groups']['columns'];
                                $query = new DB_Insert('attribute_groups');
                                $query->addInsertValue($group_id, $columns['id']);
                                $query->addInsertValue($group_name, $columns['name']);
                                $query->addInsertValue($group_so, $columns['sort_order']);
                                $application->db->getDB_Result($query);
                                
                                if(!empty($group_attrs))
                                {
                                    $columns = $tables['attributes']['columns'];
                                    foreach($group_attrs as $attr)
                                    {
                                        $query = new DB_Insert('attributes');
                                        $query->addInsertValue($attr['id'], $columns['id']);
                                        $query->addInsertValue($group_id, $columns['ag_id']);
                                        $query->addInsertValue($attr['input_id'], $columns['it_id']);
                                        $query->addInsertValue($attr['unit_type'], $columns['ut']);
                                        $query->addInsertValue($attr['view_tag'], $columns['view_tag']);
                                        $query->addInsertValue($attr['name'], $columns['name']);
                                        $query->addInsertValue($attr['descr'], $columns['descr']);
                                        $query->addInsertValue($attr['type'], $columns['type']);
                                        $query->addInsertValue($attr['allow_html'], $columns['allow_html']);
                                        $query->addInsertValue($attr['min'], $columns['min']);
                                        $query->addInsertValue($attr['max'], $columns['max']);
                                        $query->addInsertValue($attr['size'], $columns['size']);
                                        $query->addInsertValue($attr['multilang'], $columns['multilang']);
                                        $query->addInsertValue(($group_so*10+$attr['sort_order']), $columns['sort_order']);
                                        $application->db->getDB_Result($query);
                                        
                                        $tag_to_id[$attr['view_tag']] = $attr['id'];
                                    };
                                };
                            };
                        };
                    };
                    break;
                case 'PRODUCT_TYPES':
                    foreach($id_child->children as $pts_child)
                    {
                        if($pts_child->name == 'PRODUCT_TYPE')
                        {
                            $ptype_id = null;
                            $ptype_name = null;
                            $ptype_descr = null;
                            $ptype_attrs = array();
                            foreach($pts_child->children as $pt_child)
                            {
                                switch($pt_child->name)
                                {
                                    case 'ID':    $ptype_id = $pt_child->contents;      break;
                                    case 'NAME':  $ptype_name = $pt_child->contents;    break;
                                    case 'DESCR': $ptype_descr = $pt_child->contents;   break;
                                    case 'ATTRIBUTES':
                                        foreach($pt_child->children as $as_child)
                                        {
                                            $a_props = array(
                                                'tag' => $as_child->attributes['TAG']
                                               ,'visible' => null
                                               ,'required' => null
                                               ,'default' => null
                                            );
                                            foreach($as_child->children as $a_child)
                                            {
                                                if(in_array(strtolower($a_child->name),array_keys($a_props)))
                                                {
                                                    if(isset($a_child->attributes['TYPE']) and ($a_child->attributes['TYPE']=="const"))
                                                        $a_props[strtolower($a_child->name)] = constant($a_child->contents);
                                                    else
                                                        $a_props[strtolower($a_child->name)] = $a_child->contents;
                                                };
                                            };
                                            
                                            if(!in_array(null,$a_props,true))
                                            {
                                                $ptype_attrs[] = $a_props;
                                            };
                                        };
                                        break;
                                    default: break;
                                };
                            };
                            
                            if($ptype_id != null and $ptype_name != null and $ptype_descr != null)
                            {
                                if(strpos($ptype_name,'lang-')===0)
                                {
                                    $ptype_name = $MR->getMessage(str_replace('lang-','',$ptype_name));
                                };
                                if(strpos($ptype_descr,'lang-')===0)
                                {
                                    $ptype_descr = $MR->getMessage(str_replace('lang-','',$ptype_descr));
                                };
                                
                                $columns = $tables['product_types']['columns'];
                                $query = new DB_Insert('product_types');
                                $query->addInsertValue($ptype_id, $columns['id']);
                                $query->addInsertValue($ptype_name, $columns['name']);
                                $query->addInsertValue($ptype_descr, $columns['descr']);
                                $application->db->getDB_Result($query);
                                
                                if(!empty($ptype_attrs))
                                {
                                    $columns = $tables['product_type_attributes']['columns'];
                                    foreach($ptype_attrs as $pa)
                                    {
                                        $query = new DB_Insert('product_type_attributes');
                                        $query->addInsertValue($ptype_id, $columns['pt_id']);
                                        $query->addInsertValue($tag_to_id[$pa['tag']], $columns['a_id']);
                                        $query->addInsertValue(($pa['visible']=='Y'), $columns['type_attr_visible']);
                                        $query->addInsertValue(($pa['required']=='Y'), $columns['type_attr_required']);
                                        $query->addInsertValue($pa['default'], $columns['type_attr_def_val']);
                                        $application->db->getDB_Result($query);
                                    };
                                };
                            };
                        };
                    };
                    break;
                case 'CATEGORIES':
                    foreach($id_child->children as $cts_child)
                    {
                        if($cts_child->name == 'CATEGORY')
                        {
                            $cat_info = array(
                                'id' => null
                               ,'left' => null
                               ,'right' => null
                               ,'level' => null
                               ,'sort_order' => null
                               ,'name' => null
                               ,'descr' => null
                            );
                            
                            foreach($cts_child->children as $cat_child)
                            {
                                if(in_array(strtolower($cat_child->name),array_keys($cat_info)))
                                {
                                    $cat_info[strtolower($cat_child->name)] = $cat_child->contents;
                                };
                            };
                            
                            if(!in_array(null,$cat_info,true))
                            {
                                if(strpos($cat_info['name'],'lang-')===0)
                                {
                                    $cat_info['name'] = $MR->getMessage(str_replace('lang-','',$cat_info['name']));
                                };
                                if(strpos($cat_info['descr'],'lang-')===0)
                                {
                                    $cat_info['descr'] = $MR->getMessage(str_replace('lang-','',$cat_info['descr']));
                                };

                                $table = 'categories';           
                                $columns = $tables[$table]['columns'];  

                                $query = new DB_Insert($table);
                                $query->addInsertValue($cat_info['id'], $columns['id']);
                                $query->addInsertValue($cat_info['left'], $columns['left']);
                                $query->addInsertValue($cat_info['right'], $columns['right']);
                                $query->addInsertValue($cat_info['level'], $columns['level']);
                                $query->addInsertValue($cat_info['sort_order'], $columns['sort_order']);
                                $application->db->getDB_Result($query);

                                $table = 'categories_descr';           
                                $columns = $tables[$table]['columns'];  

                                $query = new DB_Insert($table);
                                $query->addInsertValue($cat_info['id'], $columns['id']);
                                $query->addInsertValue($cat_info['name'], $columns['name']);
                                $query->addInsertValue($cat_info['descr'], $columns['descr']);
                                $application->db->getDB_Result($query);
                            };
                        };
                    };
                    break;
                default: break;
            };
        };
    };
};

_use(dirname(__FILE__).'/install_group_edit_settings.inc');
_use(dirname(__FILE__).'/install_catalog_navigation_settings.inc');

?>
