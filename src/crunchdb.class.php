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

  class crunchDB {

    protected $dbdir;
    protected $dbext;
    protected $dbmode;

    /**
     * Create new CrunchDB instance with needed parameters
     * @param string $dbdir Directory with all database files
     * @param string $dbext File extension of the database files, usually .json
     * @param string $dbmode Database access mode, can be read(r), write(w) or (rw)
     */
    public function __construct($dbdir='/', $dbext='json', $dbmode='rw'){
      if(in_array($dbmode, array('r', 'w', 'rw'))) $this->dbmode = $dbmode;
      else throw new Exception('crunchDB: Invalid mode, only accetps [r,w,rw]');
      if(preg_match("#[a-z0-9]{1,4}#", $dbext)) $this->dbext  = $dbext;
      else throw new Exception('crunchDB: Invalid file extension');
      if(is_dir($dbdir)) $this->dbdir = $dbdir;
      else throw new Exception('crunchDB: Invalid directory');
      if(substr($this->dbdir, -1) != '/') $this->dbdir.='/';
    }

    /**
     * Handle any calls to the crunchDB instance and trigger the respective function
     * @param string $func The name of the function to trigger
     * @param array $args An array with all arguments that will be passed to the function
     */
    public function __call($func, $args){
      $handleInRoot = array('create', 'drop', 'alter', 'truncate', 'tables', 'version');
      if($func != 'select' && $this->dbmode == 'r') throw new Exception('CrunchDB is running in read mode');
      if(in_array($func, $handleInRoot)) return call_user_func_array(array($this->_crunchRoot(), $func), $args);
      if(method_exists('crunchTable', $func)){
        $target = $args[0]; array_splice($args, 0, 1);
        return call_user_func_array(array($this->_crunchTable($target), $func), $args);
      }
      throw new Exception('crunchDB: Can\'t handle the action "'.$func.'"');
    }

    /**
     * Return a new instance of a crunchDB table
     * @param string $tablename The respective table's name
     * @return object The new crunchTable instance
     */
    protected function _crunchTable($tablename){
      if(!$this->hasTable($tablename)) throw new Exception('crunchDB: Invalid table name "'.$tablename.'"');
      else return new crunchTable($this->dbdir.$tablename.'.'.$this->dbext);
    }

    /**
     * Return a new instance of the crunchDB root controller
     * @return object The new crunchRoot instance
     */
    protected function _crunchRoot(){
      return new crunchRoot($this->dbdir, $this->dbext);
    }

    /**
     * Check if a table exists by searching for the respective file
     * @param string $tablename The respective table's name
     * @return boolean Indicates wether the table exists or not
     */
    protected function hasTable($tablename){
      return file_exists($this->dbdir.$tablename.'.'.$this->dbext);
    }
  }

?>