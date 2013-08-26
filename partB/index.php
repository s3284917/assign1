<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html401/loose.dtd">
<?php
  require "db.php";
  //function to query sql database to populate pulldown  
  function selectDistinct($conn, $tableName, $attrName, $pulldownName, $defaultVal) {
    $defaultWithinResultSet = FALSE;
    //query string for getting data
    $distinctQuery = "SELECT DISTINCT {$attrName} FROM {$tableName}";
    //run Query and make sure it doesnt error
    if (!($resultId = @ mysql_query ($distinctQuery, $conn)))
      showerror();
 
    print "\n<select name=\"{$pulldownName}\">";

    while ($row = @ mysql_fetch_array($resultId))
    {
      $result = $row[$attrName];
    
      if (isset($defaultVal) && $result == $defaultVal)
        print "\n\t<option selected value=\"{$result}\">{$result}";
    
      else
        print "\n\t<option value=\"{$result}\">{$result}";
    
      print "</option>";
    }
  
    print "\n</select>";
  }
  
  if (!($conn = @ mysql_connect(DB_HOST, DB_USER, DB_PW))) {
    showerror();
  }
  if (!mysql_select_db(DB_NAME, $conn)) {
    showerror();
  }
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Wine Search Page</title>
  </head>
  <body bgcolor="white">
    <form action="results.php" method="GET">
    <table id='search' border='0'>
      <tr>
      <td>Enter a wine name: </td>
      <td><input type="text" name="wineName" value=""></td>
      </tr>
      <tr>
      <td>Enter a winery name: </td>
      <td><input type="text" name="wineryName" value=""></td>
      </tr>
      <tr>
      <td>Select a region: </td><td>
      <?php selectDistinct($conn, "region", "region_name", "regionName", "All"); ?>
      </td></tr>
      <tr><td>Select Grape variety: </td><td>
      <?php selectDistinct($conn, "grape_variety", "variety", "grapeVariety", "Blanc"); ?>
      <tr>
      <td><input type="submit" value="Search Wines"></td>
      </tr>
    </form>
    <br>
  </body>
</html>
