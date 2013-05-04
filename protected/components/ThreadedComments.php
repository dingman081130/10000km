<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ThreadedComments
 *
 * @author yemin
 */
class ThreadedComments {
    //put your code here
    
    
    private $parents = array();
    private $children = array();
    private $count;
    
    function __construct($comments) {
        $this->count = count($comments);
        
        foreach ($comments as $comment) {
            if ($comment['parent_id'] === null) {
                $this->parents[$comment['id']] = $comment;
            } else {
                $this->children[$comment['parent_id']][] = $comment;
            }
        }
    }
    
    private function format_comment($comment, $depth) {
        for ($depth; $depth > 0; $depth--) {
            echo "\t";
        }
        
        echo $comment['content'];
        echo "\n";
    }
    
    private function print_parent($comment, $depth = 0) {
        foreach ($comment as $c) {
            $this->format_comment($c, $depth);
            
            if (isset($this->children[$c['id']])) {
                $this->print_parent($this->children[$c['id']], $depth + 1);
            }
        }
    }
    
    public function print_comments() {
        $this->print_parent($this->parents);
    }
    
    public function getParents() {
        return $this->parents;
    }
    
    public function getChildren($parent) {
        if (isset($this->children[$parent['id']]))
            return $this->children[$parent['id']];
        return array();
    }
    
    public function getCount() {
        return $this->count;
    }
}

?>
