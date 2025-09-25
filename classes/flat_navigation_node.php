<?php

namespace theme_klassplace;

use coding_exception;

/**
 * Subclass of navigation_node allowing different rendering for the flat navigation
 * in particular allowing dividers and indents. Resurection of deprecated core.
 *
 * @package   theme_klassplace
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class flat_navigation_node extends \flat_navigation_node {

 
    /**
     * A proxy constructor
     *
     * @param mixed $navnode A navigation_node or an array
     */
    public function __construct($navnode, $indent) {
        if (is_array($navnode)) {
            parent::__construct($navnode);
        } else if ($navnode instanceof \navigation_node) {

            // Just clone everything.
            $objvalues = get_object_vars($navnode);
            foreach ($objvalues as $key => $value) {
                 $this->$key = $value;
            }
        } else {
            throw new coding_exception('Not a valid flat_navigation_node');
        }
        $this->indent = $indent;
    }

}
