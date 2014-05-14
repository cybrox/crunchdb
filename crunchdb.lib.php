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
      $handleInRoot = array('create', 'drop', 'alter', 'truncate', 'version');
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
      foreach($this->data as $d) if(!empty($d['dbindex'])) unset($d['dbindex']);
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