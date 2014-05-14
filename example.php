<?php

  /**
   * Simple chrunchdb example
   */

  require 'src/crunchdb.class.php';
  require 'src/crunchroot.class.php';
  require 'src/crunchtable.class.php';

  $cdb = new crunchDB('./db/');

  # Create two new tables called 'users' and 'ponies'
  $cdb->create('users');
  $cdb->create('ponies');

  # Rename table 'ponies' to 'cookies'
  $cdb->alter('ponies', 'cookies');

  # Drop the table 'cookies'
  $cdb->drop('cookies'); #echo "ah! hot!"

  # Insert two users to the 'users' table
  $cdb->insert('users', array('name' => 'cybrox', 'likes' => 'cookies'));
  $cdb->insert('users', array('name' => 'another', 'likes' => 'bananas'));

  # Update in 'users' where 'name' matches 'another'
  $cdb->update('users', 'name', 'another', array('name' => 'xammi', 'likes' => 'cake'));

  # Select from 'users' where 'name' equals 'xammi'
  print_r($cdb->select('users', 'name', 'xammi'));

  # Count from 'users' where * (wildcard will loop through all entries)
  echo '<br />'.$cdb->count('users', '*').' users<br />';

  # Delete the user named xammi
  $cdb->delete('users', 'name', 'xammi');

  # Select all users where * (wildcard will select all users)
  print_r($cdb->select('users', '*'));

  # Drop the users table to undo everything tested here
  $cdb->drop('users');

?>