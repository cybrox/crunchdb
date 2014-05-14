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

  class crunchTable {

    protected $table;
    protected $data;

    /**
     * Class constructor, will get the table file path and load its data
     * @param string $table The selected table's file path
     */
    public function __construct($table){
      $this->data  = json_decode(file_get_contents($table),true);
      $this->table = $table;
    }

    /**
     * Class destructor, will save the changes to the json file
     */
    public function __destruct(){
      file_put_contents($this->table, json_encode($this->data));
    }

    /**
     * Insert a dataset in a table
     * @param array $data An array containing the new dataset
     * @return object A new instance of crunchResponse
     */
    public function insert($data = array()){
      array_push($this->data, $data);
      return true;
    }

    /**
     * Count all entries that match $key = $value
     * @param string $key The key to search for
     * @param mixed $value The value that the key should match
     * @return int Number of entries that match the given criteria
     */
    public function count($key, $value = 0){
      $dataset = $this->findInData($key, $value);
      return count($dataset);
    }

    /**
     * Select all entries that match $key = $value
     * @param string $key The key to search for
     * @param mixed $value The value that the key should match
     * @return array All entries that match the given criteria
     */
    public function select($key, $value = 0){
      return $this->findInData($key, $value);
    }

    /**
     * Update all entries that match $key = $value
     * @param string $key The key to search for
     * @param mixed $value The value that the key should match
     * @param array $data The new data array for this entry
     */
    public function update($key, $value = 0, $data){
      $thisItem = $this->findInData($key, $value);
      foreach($thisItem as $item){
        foreach($data as $k => $v) $this->data[$item['dbindex']][$k] = $v;
      }
      return true;
    }

    /**
     * Update all entries in this table
     * @param array $data The new data array for each entry
     */
    public function updateAll($key, $value = 0, $data = array()){
      $thisItem = $this->findInData($key, $value);
      foreach($thisItem as $item){
        $this->data[$item['dbindex']] = $data;
      }
      return true;
    }

    /**
     * Delete all entries that match $key = $value
     * @param string $key The key to search for
     * @param mixed $value The value that the key should match
     */
    public function delete($key, $value = 0){
      $thisItem = $this->findInData($key, $value);
      foreach($thisItem as $item){
        array_splice($this->data, $item['dbindex'], ($item['dbindex']+1));
      }
      return true;
    }


    /**
     * Find multiple values in the table's data
     * @param string $key The key to search for
     * @param mixed $value The value that the key should match
     * @return array Contains all search matches
     */
    protected function findInData($key, $value){
      $dataset = array();
      $isIndex = 0;
      foreach($this->data as $data){
        if($key == '*' || $data[$key] == $value){
          $result = $data;
          $result['dbindex'] = $isIndex;
          array_push($dataset, $result);
        }
        $isIndex++;
      }
      return $dataset;
    }
  }

?>