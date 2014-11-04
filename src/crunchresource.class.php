<?php

/**
 * crunchDB - A simple file based database system
 * written in pure PHP. Useful for smaller applications.
 *
 * Written and copyrighted 2014+
 * by Sven Marc 'cybrox' Gehring
 *
 * Licensed under BY-NC-SA
 * Named after delicious cookies!
 */


  class crunchResource {

    private $data;


    /**
     * Create a new instance of a crunch resource
     * @param instance $base Instance of the data's table
     * @param string $query SQL like query string to select
     */
    public function __construct($base, $query){
      $this->data = $base->raw();
      $this->apply($query);
    }


    /**
     * Apply the given query to the dataset
     * @param string $query The given query
     */
    private function apply($query){
      $query = trim($query);

      if($query == '*') return;
    }


    /**
     * Sort the resource data with simple, predefined parameters
     * @param mixed $x function arguments
     * @return instance $this For chaining
     */
    public function sort(){
      $args = func_get_args();
      $args = is_array($args[0]) ? $args[0] : $args;
      usort($this->data, $this->_cmp($args));
      return $this;
    }


    /**
     * Sort the resource data with a custom function
     * @param function $func The user defined function
     * @return instance $this For chaining
     */
    public function sortfn($func){
      $this->data = usort($this->data, $func);
      return $this;
    }


    /**
     * Fetch the data in the current resource
     * @return array $data The fetched data array
     */
    public function fetch(){
      return $this->data;
    }


    /**
     * Count the data in the current resource
     * @return int $count The number of rows in the resource
     */
    public function count(){
      return count($this->data);
    }


    /**
     * Generate a multi dimensional array compare method for usort
     * Written by Jon ( http://stackoverflow.com/a/16788610 )
     */
    private function _cmp() {
      $criteria = func_get_args();
      foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion)
          ? array_pad($criterion, 3, null)
          : array($criterion, SORT_ASC, null);
      }

      return function($first, $second) use (&$criteria) {
        foreach ($criteria as $criterion) {
          list($column, $sortOrder, $projection) = $criterion;
          $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

          if ($projection) {
            $lhs = call_user_func($projection, $first[$column]);
            $rhs = call_user_func($projection, $second[$column]);
          } else {
            $lhs = $first[$column];
            $rhs = $second[$column];
          }

          if ($lhs < $rhs) { return -1 * $sortOrder; }
          else if ($lhs > $rhs) { return 1 * $sortOrder; }
        }

        return 0;
      };
    }

  }

?>