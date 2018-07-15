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

  namespace cybrox\crunchdb;

  class CrunchResource {

    private $base;
    private $data;
    private $rawd;
    private $filt = false;
    private $uids = array();


    /**
     * Create a new instance of a crunch resource
     * @param instance $base Instance of the data's table
     * @param arrays $query Multiple arrays for filtering
     */
    public function __construct($base, $query){
      $this->tweak($base->raw());
      $this->apply($query);
      $this->base = $base;
    }


    /**
     * Append internal id's to the rows to prevent overlaps
     * when filtering with or connectors
     */
    private function tweak($input){
      $counter = 0;
      foreach($input as $inp){
        array_push($this->uids, $counter);
        $inp['__dbid'] = $counter;
        $this->data[] = $inp;
        $counter++;
      }
      $this->rawd = $this->data;
    }


    /**
     * Apply the given query to the dataset
     * @param string $queries The given query
     */
    private function apply($queries){
      foreach($queries as $query){
        if(count($query) < 3) continue;

        $key = (!empty($query[0])) ? $query[0] : '';
        $con = (!empty($query[3])) ? $query[3] : 'or';

        if($key == '*') return;
        if($con == 'or') $this->check($this->rawd, $query);
        else $this->check($this->data, $query);
      }
    }


    /**
     * Compare datasets. - Used for select queries.
     */
    private function check($dataset, $query){
      $resd = array();
      $uids = array();

      $key = (!empty($query[0])) ? trim($query[0]) : '';
      $com = (!empty($query[1])) ? trim($query[1]) : '==';
      $val = (!empty($query[2])) ? trim($query[2]) : '';
      $con = (!empty($query[3])) ? trim($query[3]) : 'or';

      foreach($dataset as $data){
        if(!empty($data[$key])){
          if(
            ($data[$key] <= $val && $com == '<=') ||
            ($data[$key] <  $val && $com == '<' ) ||
            ($data[$key] == $val && $com == '==') ||
            ($data[$key] <= $val && $com == '>=') ||
            ($data[$key] >  $val && $com == '>' )) {
            array_push($resd, $data);
          }
        }
      }


      if($con == 'and' || !$this->filt) $this->data = $resd;
      else {
        foreach($resd as $r){
          if(!in_array($r['__dbid'], $uids))
            array_push($this->data, $r);
        }
      }

      $this->filt = true;
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
     * @param bool $withid Don't filter dbid if this is active
     * @return array $data The fetched data array
     */
    public function fetch($withid = false){
      if($withid) return $this->data;

      $res = array();

      foreach($this->data as $data){
        unset($data['__dbid']);
        array_push($res, $data);
      }

      return $res;
    }


    /**
     * Count the data in the current resource
     * @return int $count The number of rows in the resource
     */
    public function count(){
      return count($this->data);
    }


    /**
     * Delete the selected resources from the database
     */
    public function delete(){
      foreach($this->data as $d) unset($this->base->tbdata['data'][$d['__dbid']]);
      $this->base->tbdata['data'] = array_values($this->base->tbdata['data']);
      $this->base->_saveData();
      return true;
    }


    /**
     * Update keys with values
     */
    public function update(){
      foreach($this->data as $data){
        foreach(func_get_args() as $arg){
          if(array_key_exists($arg['0'], $this->base->tbdata['data'][$data['__dbid']])) {
            $this->base->tbdata['data'][$data['__dbid']][$arg[0]] = $arg[1];
          }
        }
      }
      $this->base->_saveData();
      return true;
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
