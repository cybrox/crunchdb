<?php

  // Require source files
  require 'src/crunchdb.class.php';
  require 'src/crunchtable.class.php';
  require 'src/crunchresource.class.php';

  // Creating new cdb instance
  $cdb = new crunchDB('./db/');

  echo '<pre>';


//=============================================================================
//= CrunchDB Examples. e() is a comment, f() contains the respective function =
//=============================================================================

    e( 'Requesting database version' );
    f( $cdb->version() );

    e( 'Check if a table called "cookies" exists ' );
    f( $cdb->table('cookies')->exists() );

    e( 'Creating a table called "cookies" ' );
    f( $cdb->table('cookies')->create() );

    e( 'Creating a table called "cakes" ' );
    f( $cdb->table('cakes')->create() );

    e( 'Rename table "cakes" to "cheese" ' );
    f( $cdb->table('cakes')->alter('cheese') );

    e( 'Requesting a list of tables in the database');
    f( $cdb->tables() );

    e( 'Count all rows in the cookies table ' );
    f( $cdb->table('cookies')->count() );

    e( 'Insert a chocolate cookie to the cookies table' );
    f( $cdb->table('cookies')->insert(array("type" => "chocolate", "is" => "nice")) );

    e( 'Insert a banana cookie to the cookies table' );
    f( $cdb->table('cookies')->insert(array("type" => "banana", "is" => "nice")) );

    e( 'Insert a strawberry cookie to the cookies table' );
    f( $cdb->table('cookies')->insert(array("type" => "strawberry", "is" => "ok")) );

    e( 'Get the raw dataset from the cookies table' );
    f( $cdb->table('cookies')->raw() );

    e( 'Select all the cookies from the cookie table' );
    f( $cdb->table('cookies')->select('*')->fetch() );

    e( 'Count all the cookies from the cookie table' );
    f( $cdb->table('cookies')->select('*')->count() );

    e( 'Select and fetch all cookies where type is chocolate' );
    f( $cdb->table('cookies')->select(['type', '==', 'chocolate'])->fetch() );

    e( 'Select and fetch all cookies where type is chocolate or banana' );
    f( $cdb->table('cookies')->select(['type', '==', 'chocolate'],['type', '==', 'banana', 'or'])->fetch() );

    e( 'Select and count all cookies where type is chocolate and banana' );
    f( $cdb->table('cookies')->select(['type', '==', 'chocolate'],['type', '==', 'banana', 'and'])->count() );

    e( 'Sort the selected cookies (all) alphabetically and fetch the result' );
    f( $cdb->table('cookies')->select('*')->sort(['type'])->fetch() );

    e( 'Delete the cookie with the type strawberry' );
    f( $cdb->table('cookies')->select(['type', '==', 'strawberry'])->delete() );

    e( 'Rename the banana cookie to chocolate as well' );
    f( $cdb->table('cookies')->select(['type', '==', 'banana'])->update(['type', 'chocolate']) );

    e( 'Fetch all cookies from the cookies table' );
    f( $cdb->table('cookies')->select('*')->fetch() );

    e( 'Dropping all tables to end this test ' );
    $cdb->table('cookies')->drop();
    $cdb->table('cheese')->drop();

//=============================================================================
//=============================================================================
//=============================================================================

  echo '</pre>';


  // Functions to display stuff
  function e($in){ echo $in; }
  function f($in){
    echo '
';
    var_dump($in);
    echo '

';
  }

?>