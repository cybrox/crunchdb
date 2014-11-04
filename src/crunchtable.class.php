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
        $this->tbdata = json_decode(file_get_contents($this->tbpath));
        $this->tbexst = true;
      } else {
        $this->tbdata = null;
        $this->tbexst = false;
      }
    }


    /**
     * Check if a table exists 
     *
     * @return bool $exists Indicates if the table exists
     */
    public function exists(){
      return $this->tbexst;
    }


    /**
     * Drop the selected table
     */
    public function drop(){
      if($this->tbexst) unlink($this->tbpath);
      return true;
    }


    /**
     * Truncate the selected table
     */
    public function truncate(){
      if($this->tbexst){ 
        file_put_contents($this->tbpath, "{}");
        return true;
      } else {
        throw new Exception('cdb table "'.$this->tbname.' does not exist.');
        return false;
      }
    }


    /**
     * Create the table if it doesn't exist yet
     */
    public function create(){
      if(!$this->tbexst){
        file_put_contents($this->tbpath, "{}");
        return true;
      } else {
        throw new Exception('cdb table "'.$this->tbname.' already exists.');
        return false;
      }
    }


    /**
     * Alter a table name
     * @param string $name The table's new name
     */
    public function alter($name){
      if($this->tbexst){
        $newpath = $tbbase->dbdir.$name.$tbbase->dbext;
        file_put_contents($newpath, $this->tbdata);
        unlink($this->tbpath);
        $this->tbpath = $newpath;
        return true;
      } else {
        throw new Exception('cdb table "'.$this->tbname.' does not exist.');
        return false;
      }
    }

  }

?>