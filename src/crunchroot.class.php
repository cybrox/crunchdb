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

  class crunchRoot {

    protected $dbdir;
    protected $dbext;

    /**
     * Class constructor, will receive needed information
     * about file location and extension to handle file actions.
     * @param string $dbdir Directory with all database files
     * @param string $dbext File extension of the database files, usually .json
     */
    public function __construct($dbdir, $dbext){
      $this->dbdir = $dbdir;
      $this->dbext = $dbext;
    }

    /**
     * Create a chrunchDB table
     * @param string $tablename The respective table's name
     */
    public function create($tablename){
      $table = $this->getFile($tablename);
      if(strlen($tablename) < 2) throw new Exception('crunchDB: Table name is too short');
      if(file_exists($table)) throw new Exception('crunchDB: Table "'.$tablename.'" already exists');
      else file_put_contents($table, '[]');
    }

    /**
     * Drop a chrunchDB table
     * @param string $tablename The respective table's name
     */
    public function drop($tablename){
      $table = $this->getFile($tablename);
      if(!file_exists($table)) throw new Exception('crunchDB: Table "'.$tablename.'" doesn\'t exists');
      else unlink($table);
    }

    /**
     * Alter a chrunchDB table (change its name)
     * @param string $tablename The respective table's name
     * @param string $newname The table's new name
     */
    public function alter($tablename, $newname){
      $otable = $this->getFile($tablename);
      $ntable = $this->getFile($newname);
      if(!file_exists($otable)) throw new Exception('crunchDB: Table "'.$tablename.'" doesn\'t exists');
      if(file_exists($ntable)) throw new Exception('crunchDB: Table "'.$tablename.'" already exists');
      rename($otable, $ntable);
    }

    /**
     * Truncate a chrunchDB table
     * @param string $tablename The respective table's name
     */
    public function truncate($tablename){
      $table = $this->getFile($tablename);
      if(!file_exists($table)) throw new Exception('crunchDB: Table "'.$tablename.'" doesn\'t exists');
      else file_put_contents($table, '[]');
    }

    /**
     * Return the path to a table's file
     * @param string $tablename The respective table's name
     * @return string The respective table's file location
     */
    protected function getFile($tablename){
      return $this->dbdir.$tablename.'.'.$this->dbext;
    }

    /**
     * Misc, return the current version
     */
    public function version(){ return 'Running crunchDB 0.1a'; }
  }

?>