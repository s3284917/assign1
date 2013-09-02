<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html401/loose.dtd">
<?php

  function showerror() {
    die("Error " . mysql_errno() . " : " .mysql_error());
  }
  //use the php file with database data stored as variables
  require "db.php";
  //function to query sql database to populate pulldown  
  function selectDistinct($conn, $tableName, $attrName, $pulldownName, $defaultVal, $notInDB) {
    $defaultWithinResultSet = FALSE;
    //query string for getting data
    $distinctQuery = "SELECT DISTINCT {$attrName} FROM {$tableName} ORDER BY {$attrName}";
    //run Query and make sure it doesnt error
    if (!($resultId = @ mysql_query ($distinctQuery, $conn)))
      showerror();
 
    print "\n<select name=\"{$pulldownName}\">";
    /*Used for dropdown that doesnt have All in the DB but dont want to restrict*/
    if (isset($defaultVal) && isset($notInDB) && $notInDB == true) {
      print "\n\t<option selected value=\"{$defaultVal}\">{$defaultVal}";
    }
    //Loop through while there are still values in the array
    while ($row = @ mysql_fetch_array($resultId))
    {
      $result = $row[$attrName];
      //If the defualtVal shows up in the results from quering the DB
      //Use as default show value in dropdown
      if (isset($defaultVal) && $result == $defaultVal)
        print "\n\t<option selected value=\"{$result}\">{$result}";
      //Otherwise add row to dropdown.
      else
        print "\n\t<option value=\"{$result}\">{$result}";
      //End Options to be added
      print "</option>";
    }
    //End of Dropdown
    print "\n</select>";
  }
  //Sets $conn and if it doesnt return true, to say success in connecting
  //to the DBMS then show error
  if (!($conn = @ mysql_connect(DB_HOST, DB_USER, DB_PW))) {
    showerror();
  }
  //if can't connected to the database then show an error
  if (!mysql_select_db(DB_NAME, $conn)) {
    showerror();
  }
  //Checks GET for if any error values are set, and 
  //then if so sets a variable for them.
  if ($_GET['errYear']) { $errYear = $_GET['errYear']; }
  if ($_GET['errStock']) { $errStock = $_GET['errStock']; }
  if ($_GET['errCost']) { $errCost = $_GET['errCost']; }
?>
      <!-- selectDistinct($conn, "region", "region_name", "regionName", "All"); ?>
      <selectDistinct($conn, "grape_variety", "variety", "grapeVariety", "All", true); ?>
      selectDistinct($conn, "wine", "year", "minYear", ""); ?> To 
      selectDistinct($conn, "wine", "year", "maxYear", "1999"); ?> -->
