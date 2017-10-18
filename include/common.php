<?php

  // Establish connection to the database
  $db = mysqli_connect('localhost:3306', 'tucker', 'starburst', 'settlerswarlords');
  
  function reporterror($errordesc) {
  //Generates an error report for the database.
    $db->query("INSERT INTO error (happens, content) VALUES (NOW(), '". danescape($errordesc) ."');");
  }
  
  function danescape($rawstring) {
  // A shortened version of mysqli_real_escape_string, that wraps the 
    global $db;
    
    return mysqli_real_escape_string($db, $rawstring);
  }

  
  function danquery($content, $codesection) {
  //Executes a sql query and returns the resulting data structure.  A basic wrapper for the mysql_query function, that includes error reporting.  If you are after a
  //specific field, use getfield.
  //  $content     - specific sql content to send to the database
  //  $codesection - which code section made this call.  Used to determine where errors happened.
    global $db;
    
    $query = $db->query($content);
    if(!$query) {
      reporterror($codesection .": query returned an error in danquery(), query = {". $content ."} mysql says ". mysql_error());
      return null;
    }
    return $query; 
  }
  
  function aboutEqual($sample, $target, $variance) {
  //Returns true if the sample value is close to its target value.
  //  $sample - what value to check for being on or near the correct value.
  //  $target - the value that $sample should be close to
  //  $variance - how far off the sample value can be from its target to still be in range.
    if($sample+$variance>=$target) {
      if($sample-$variance<=$target) {
        return true;
      }
    }
    return false;
  }
?>