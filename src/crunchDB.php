<?php

/**
 * crunchDB - A simple file based database system
 * written in pure PHP. Useful for smaller applications.
 *
 * Written and copyrighted 2014+
 * by Sven Marc 'cybrox' Gehring
 *
 * Licensed under CC BY-NC-SA
 * Named after delicious cookies!
 */

  namespace cybrox\crunchdb;
  use \Exception;

  class CrunchDB {
    
    public $dbdir;
    public $dbmod;
    public $dbext;


    /**
     * Create a new instance of crunchdb with the given settings
     * 
     * @param string $dbdir Directory with all database files
     * @param string $dbext File extension of the database files, usually .json
     * @param string $dbmod Database access mode, can be read(r) or read-write (rw)
     */
    public function __construct($dbdir = './', $dbext = 'json', $dbmod = 'rw'){

      if(is_dir($dbdir)) {
        if(substr($dbdir, -1) != '/') $dbdir .= '/';
        $this->dbdir = $dbdir;
      }
      else throw new Exception('cdb path "'.$dbdir.'" does not exist.');

      if(in_array($dbmod, array('r', 'rw'))) $this->dbmod = $dbmod;
      else throw new Exception('cdb database mode can only be "r" or "rw".');

      if(substr($dbext, 0, 1) != '.') $dbext = '.'.$dbext;
      $this->dbext = $dbext;
    }


    /**
     * Return an instance of the selected table for further actions
     *
     * @param string $name Name of the selected table
     */
    public function table($name){
      return new crunchTable($this, $name);
    }


    /**
     * Return the current crunchDB version for testing
     */
    public function version(){
      return "2.0.0";
    }


    /**
     * Return a list of all tables
     */
    public function tables(){
      $tablelist = array();
      if($dbdir = opendir($this->dbdir)){
        while (($file = readdir($dbdir)) !== false)
          if(strstr($file, $this->dbext)) $tablelist[] = str_replace($this->dbext, '', $file);
        closedir($dbdir);
      }
      return $tablelist;
    }
  }
?>
