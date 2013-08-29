<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html401/loose.dtd">
<?php
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
      <?php selectDistinct($conn, "region", "region_name", "regionName", "All"); ?>
      </td></tr>
      <tr><td>Select Grape variety: </td><td>
      <?php selectDistinct($conn, "grape_variety", "variety", "grapeVariety", "Blanc"); ?>
      </td></tr>
      <tr><td>Select Year range: </td><td>
      <?php selectDistinct($conn, "wine", "year", "minYear", ""); ?> To 
      <?php selectDistinct($conn, "wine", "year", "maxYear", "1999"); ?>
      </td>
      <td><?php if ($errYear) { echo $errYear; }?></td></tr>
      <tr><td>Wine Stock Range: </td>
      <td>Min: <input type="text" name="minStock" value="" maxlength="3" size="3">   
      Max: <input type="text" name="maxStock" value="" maxlength="3" size="3"></td>
      <td><?php if ($errStock) { echo $errStock; }?></td></tr>
      <tr><td>Select Cost Range: </td>
      <td>Min: $<input type="text" name="minCost" value="" maxlength="4" size="4"> 
      Max: $<input type="text" name="maxCost" value="" maxlength="4" size="4"></td>
      <td><?php if ($errCost) { echo $errCost; }?></td></tr>
      <tr>
      <td><input type="submit" value="Search Wines"></td>
      </tr>
    </form>
    <br>
  </body>
</html>
