<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html401/loose.dtd">
<?php
  //use the php file with database data stored as variables
  require "db.php";
  //function to query sql database to populate pulldown  
  function selectDistinct($conn, $tableName, $attrName, $pulldownName, $defaultVal) {
    $defaultWithinResultSet = FALSE;
    //query string for getting data
    $distinctQuery = "SELECT DISTINCT {$attrName} FROM {$tableName} ORDER BY {$attrName}";
    //run Query and make sure it doesnt error
    if (!($resultId = @ mysql_query ($distinctQuery, $conn)))
      showerror();
 
    print "\n<select name=\"{$pulldownName}\">";
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
<script defer="defer" type="text/javascript"><!--
/* Start of form validation: */

function validateForm(formElement) {
  
  if (formElement.minYear.value > formElement.maxYear.value)
  {
    return focusElement(formElement.minYear,'Please enter a min Year less than max year');
  }
  return true;
}
function focusElement(element, errorMsg) {
  alert((errorMsg.length > 0) ? errorMsg :
    'You did not enter valid data; please try again');
  if (element.select) element.select();
  if (element.focus) element.focus();

  return false;
}
//--></script>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Wine Search Page</title>
  </head>
  <body bgcolor="white">
    <!-- Input form to be sent to query DB -->
    <form action="results.php" method="GET" onsubmit="return validateForm(this);">
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
      <!-- Dynamically generate dropdown for region names -->
      <?php selectDistinct($conn, "region", "region_name", "regionName", "All"); ?>
      </td></tr>
      <tr><td>Select Grape variety: </td><td>
      <!-- Dynamically generate dropdown for grape variety -->
      <?php selectDistinct($conn, "grape_variety", "variety", "grapeVariety", "Blanc"); ?>
      </td></tr>
      <tr><td>Select Year range: </td><td>
      <!-- Dynamically generate 2 drop downs for wine years, to select
      min and max for a year range -->
      <?php selectDistinct($conn, "wine", "year", "minYear", ""); ?> To 
      <?php selectDistinct($conn, "wine", "year", "maxYear", "1999"); ?>
      </td>
      <!-- If an error was returned from the results page
      then display next to fields -->
      <td><?php if ($errYear) { echo $errYear; }?></td></tr>
      <tr><td>Wine Stock Range: </td>
      <!-- Text boxes to input a range of stock, max is 999 -->
      <td>Min: <input type="text" name="minStock" value="" maxlength="3" size="3">   
      Max: <input type="text" name="maxStock" value="" maxlength="3" size="3"></td>
      <!-- If an error was returned from the results page
      then display next to fields -->
      <td><?php if ($errStock) { echo $errStock; }?></td></tr>
      <tr><td>Select Cost Range: </td>
      <!-- Text boxes to input a range of price, max is a length of 4 -->
      <td>Min: $<input type="text" name="minCost" value="" maxlength="4" size="4"> 
      Max: $<input type="text" name="maxCost" value="" maxlength="4" size="4"></td>
      <!-- If an error was returned from the results page
      then display next to fields -->
      <td><?php if ($errCost) { echo $errCost; }?></td></tr>
      <tr>
      <td><input type="submit" value="Search Wines"></td>
      </tr>
    </form>
    <br>
  </body>
</html>
