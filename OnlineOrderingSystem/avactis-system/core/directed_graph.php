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
 * Directed graph, data structure and algorithms implementation.
 * Edges are stored as "Adjanced vertices" array.
 * "Cormen et al. 'Introduction to algorithms', 2001".
 */
class DirectedGraph
{
    //vertices
    var $V;

    //edges, as adjanced vertices
    var $ADJ;

    //vertex "colors"
    var $c;

    //vertex "parents"
    var $p;

    var $hasCycleFromGivenSource_LastVertexInCycle;

    //enumeration - vertex colors
    var $WHITE = "0";
    var $GRAY = "1";
    var $BLACK = "2";
    var $NIL = -1;

    function DirectedGraph($V, $ADJ)
    {
        $this->V = $V;
        $this->ADJ = $ADJ;

//        //: change it so that the $ADJ list contains empty sets (arrays),
//        //  if a vertex doesn't have the Adjanced vertices.
//        foreach($this->V as $v)
//        {
//            if(!isset($this->ADJ[(int)$v]))
//            {
//                $this->ADJ[(int)$v] = array();
//            }
//        }

//        //: change it so that the $V list contains all available in the system tax_name_id.
//        foreach($this->ADJ as $parent => $children)
//        {
//            foreach($children as $child)
//            {
//                if(!in_array($child, $this->V))
//                {
//                    $this->V[] = (int)$child;
//                    $this->ADJ[(int)$child] = array();
//                }
//            }
//        }
    }

    function initDFS()
    {
        $this->c = array();
        $this->p = array();

        foreach($this->V as $v)
        {
            $this->c[$v] = $this->WHITE;
            $this->p[$v] = $this->NIL;
        }
    }

    //DFS (depth first search)
    //  from given source.
    function hasCycleFromGivenSource($s, &$cycle)
    {
        $this->initDFS();
        if($this->hasCycleFromGivenSourceVisit($s))
        {
            //There is at least one cycle.
            //Return it's structure.
            if(!in_array($s, $this->ADJ[$this->hasCycleFromGivenSource_LastVertexInCycle]))
            {
                //The cycle has already existed before adding tax rules $s
                _fatal(__CLASS__ . "::" . __FUNCTION__ . "(): " . "\$i=" . $i . " != \$s =". $s);
            }
            $cycle = array();
            $cycle[] = $this->hasCycleFromGivenSource_LastVertexInCycle;
            $i = $this->p[$this->hasCycleFromGivenSource_LastVertexInCycle];
            for(;$i != $this->NIL;)//$this->hasCycleFromGivenSource_LastVertexInCycle;)
            {
                $cycle[] = $i;
                $i = $this->p[$i];
            }
//            $cycle[] = $i;
            return true;
        }
        else
        {
            return false;
        }
    }

    function hasCycleFromGivenSourceVisit($u)
    {
        $this->c[$u] = $this->GRAY;
        foreach ($this->ADJ[$u] as $v)
        {
            if($this->c[$v] == $this->WHITE)
            {
                $this->p[$v] = $u;
                if($this->hasCycleFromGivenSourceVisit($v))
                {
                    return true;
                }
            }
            else
            {
                //Should it be always $s from hasCycleFromGivenSource?
                $this->hasCycleFromGivenSource_LastVertexInCycle = $u;
                return true;
            }
        }
        return false;
    }
}
?>