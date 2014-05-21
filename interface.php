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

  require_once('./src/crunchdb.class.php'); 
  require_once('./src/crunchroot.class.php'); 
  require_once('./src/crunchtable.class.php'); 

  $reqMethd = (!empty($_GET['method'])) ? $_GET['method'] : '';
  $reqTable = (!empty($_GET['table'])) ? $_GET['table'] : '';
  $reqArgms = array($reqTable);

  $postArgs = $_POST;
  ksort($postArgs);
  foreach($postArgs as $a) array_push($reqArgms, $a);

  $cdb = new crunchDB();
  $smooth = true;

  try { $res = call_user_func_array(array($cdb, $reqMethd), $reqArgms); }
  catch (Exception $e) {
    handleResponse(false, 'Caught exception: '.$e->getMessage());
    $smooth = false;
  }

  if($smooth){
    if(gettype($res) == 'boolean') handleResponse($res, 'performed action');
    else handleResponse(true, $res);
  }


  function handleResponse($success, $message){
    $data = ($success) ? $message : '';
    $erro = ($success) ? '' : $message;
    echo json_encode(array(
      'success' => $success,
      'error' => $erro,
      'data' => $data
    ));
  }

?>