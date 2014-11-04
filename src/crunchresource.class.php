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

  }

?>