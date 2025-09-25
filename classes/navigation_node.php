<?php

namespace theme_klassplace;

class navigation_node extends \navigation_node {
    /**
     * Walk the tree building up a list of all the flat navigation nodes.
     *
     * @param flat_navigation $nodes List of the found flat navigation nodes.
     * @param boolean $showdivider Show a divider before the first node.
     * @param string $label A label for the collection of navigation links.
     */
    public static function static_build_flat_navigation_list(\navigation_node $item, flat_navigation $nodes, $showdivider = false, $label = '') {
        if ($item->showinflatnavigation) {
            $indent = 0;
            if ($item->type == self::TYPE_COURSE || $item->key === self::COURSE_INDEX_PAGE) {
                $indent = 1;
            }
            $flat = new flat_navigation_node($item, $indent);
            $flat->set_showdivider($showdivider, $label);
            $nodes->add($flat);
        }
        foreach ($item->children as $child) {
            self::static_build_flat_navigation_list($child, $nodes, false);
        }
    }
}