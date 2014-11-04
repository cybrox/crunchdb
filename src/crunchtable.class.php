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

  define("NULLTPL", "{\"data\":[]}");


  class crunchTable {

    private $tbname;
    private $tbexst;
    private $tbpath;
    private $tbdata;
    private $tbbase;


    /**
     * Class constructor, create a new instance of a table
     *
     * @param string $base The database main object
     * @param string $name The selected table's name
     */
    public function __construct($base, $name){

      $this->tbpath = $base->dbdir.$name.$base->dbext;
      $this->tbname = $name;
      $this->tbbase = $base;

      if(file_exists($this->tbpath)){
        $this->tbdata = json_decode(file_get_contents($this->tbpath), true);
        $this->tbexst = true;
      } else {
        $this->tbdata = null;
        $this->tbexst = false;
      }
    }


    /**
     * Save data to the table
     */
    public function _saveData(){
      file_put_contents($this->tbpath, json_encode($this->tbdata));
    }


    /**
     * Check if a table exists 
     * @return bool $exists Indicates if the table exists
     */
    public function exists(){
      return $this->tbexst;
    }


    /**
     * Drop the selected table
     * @return bool $success Action status indicator bool
     */
    public function drop(){
      if($this->tbexst) unlink($this->tbpath);
      return true;
    }


    /**
     * Truncate the selected table
     * @return bool $success Action status indicator bool
     */
    public function truncate(){
      if($this->tbexst){ 
        $this->tbdata = NULLTPL;
        $this->_saveData();
        return true;
      } else {
        throw new Exception('cdb table "'.$this->tbname.' does not exist.');
        return false;
      }
    }


    /**
     * Create the table if it doesn't exist yet
     * @return bool $success Action status indicator bool
     */
    public function create(){
      if(!$this->tbexst){
        $this->tbdata = NULLTPL;
        $this->_saveData();
        return true;
      } else {
        throw new Exception('cdb table "'.$this->tbname.' already exists.');
        return false;
      }
    }


    /**
     * Alter a table name
     * @param string $name The table's new name
     * @return bool $success Action status indicator bool
     */
    public function alter($name){
      if($this->tbexst){
        $newpath = $this->tbbase->dbdir.$name.$this->tbbase->dbext;
        file_put_contents($newpath, $this->tbdata);
        unlink($this->tbpath);
        $this->tbpath = $newpath;
        return true;
      } else {
        throw new Exception('cdb table "'.$this->tbname.' does not exist.');
        return false;
      }
    }


    /**
     * Select stuff from a table
     * @param string $query SQL style query to select entries
     * @return instance $crunchResource New resource instance
     */
    public function select($query){
      return new crunchResource($this, $query);
    }


    /**
     * Insert an array into the dataset of this table
     * @param array $content The new database entries
     */
    public function insert($data){
      var_dump($this);
      array_push($this->tbdata['data'], $data);
      $this->_saveData();
    }


    /**
     * Count data in this table
     * @return int $count Number of rows in the table
     */
    public function count(){
      if($this->tbexst){
        return count($this->tbdata['data']);
      } else {
        throw new Exception('cdb table "'.$this->tbname.' does not exist.');
        return 0;
      }
    }


    /**
     * Return the table data as a raw array
     * @return array $data The data array
     */
    public function raw(){
      return $this->tbdata['data'];
    }

  }

?>