<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html401/loose.dtd">
<?php
  //Function to display mysql errors
  function showerror() {
    die("Error " . mysql_errno() . " : " .mysql_error());
  }
  //Vaidates the input from a few fields in the input form
  function validateFormInput() {
    //Saves the min and max year from the GET
    $minYear = $_GET['minYear'];
    $maxYear = $_GET['maxYear'];
    //Saves the min and max cost from the GET
    $minCost = $_GET['minCost'];
    $maxCost = $_GET['maxCost'];
    //If the minimum year is greater than max, error
    if ($minYear > $maxYear)
    {
       $errYear = "Error: Min year is greater than max!";
       $errString .= "errYear={$errYear}&";
    }
    //If the minimum cost is greater than the max, error
    if ($minCost > $maxCost)
    {
      $errCost = "Error: min Cost is greater than max!";
      $errString .= "errCost={$errCost}&";
    }
    //If an error for either year or cost then continue
    if (isset($errYear) || isset($errCost))
    {
      /* Uses header function to go back to the index page
      and using the dynamic link, provides GET values for 
      errors to be displayed*/
      header("Location: index.php?{$errString}");
    }
  }

  //Uses the database php file 
  require 'db.php';
  require_once "MiniTemplator.class.php";
  //Function to query the database
  function displayWines($conn, $query) {
    /* Using miniTemplator to seperate presentation from functionality
    Defines a new instance of miniTemplator, reads template file
    and if no error then doesnt die with an error msg. */
    $t = new MiniTemplator;
    $ok = $t->readTemplateFromFile("results_template.htm");
    if (!$ok) die("MiniTemplator.readTemplateFromFile failed.");
    //If the query fails, shows an error
    if (!($result = @mysql_query($query, $conn))) {
      showerror();
    }
    //Gets number of rows found from query
    $rowsFound = @mysql_num_rows($result);
    //If any rows found then prints the following
    if ($rowsFound > 0) {
      
      //Loops through each row of returned results
      while ($row = @mysql_fetch_array($result)) {
        //Data printed in a row
        //Sent to the template file using variables
        $t->setVariable("wineName",$row["wine_name"]);
        $t->setVariable("variety",$row["variety"]);
        $t->setVariable("year",$row["year"]);
        $t->setVariable("wineryName",$row["winery_name"]);
        $t->setVariable("regionName",$row["region_name"]);
        $t->setVariable("cost",$row["cost"]);
        $t->setVariable("onHand",$row["on_hand"]);
        $t->setVariable("sold",$row["sold"]);
        $t->setVariable("revenue",$row["revenue"]);
        $t->addBlock("wineRow");
      }
      //once variables are set the block is called to display it
      $t->addBlock("wineTable");
    }
    //Lists number of records found
    $t->setVariable("rowsFound",$rowsFound);
    /*create the output now that all the variables on the template are 
     filled */
    $t->generateOutput(); 
  }
  //If the connection to the DBMS fails, print error
  if (!($conn = @mysql_connect(DB_HOST, DB_USER, DB_PW))) {
    die("Could not connect");
  }
  //If the connection to the database fails, print error
  if (!mysql_select_db(DB_NAME, $conn)) {
    showerror();
  }
  /*Main query with all the main colums to be displayed, as well as
  linking all the tables so that the input is displayed together*/
  $query = "SELECT wine.wine_id, wine.wine_name, variety, year, winery_name, region_name, cost, on_hand, SUM(items.price) AS revenue, SUM(items.qty) AS sold
FROM winery, region, wine, grape_variety, wine_variety, inventory, items
WHERE winery.region_id = region.region_id 
AND wine.winery_id = winery.winery_id
AND wine_variety.wine_id = wine.wine_id
AND wine_variety.variety_id = grape_variety.variety_id
AND wine.wine_id = inventory.wine_id
AND items.wine_id = wine.wine_id";
  /*If the non default region name is used
  then check that in the query*/
  if ($_GET['regionName'] != 'All')
  {
    $query .= " AND region_name = '{$_GET['regionName']}'";
  }
  /*If the length of wineName > 0 then check for a wineNAme
  in the database that is like the one entered.
   accepts partial matches*/
  if (strlen($_GET['wineName'])>0) {  
    $query .= " AND wine.wine_name LIKE '%{$_GET['wineName']}%'";
  }
  /*Same as above but for winery*/
  if (strlen($_GET['wineryName'])>0) {
    $query .= " AND winery.winery_name LIKE '%{$_GET['wineryName']}%'";
  }
  /*Grape variety is dynamically generated in a dropdown on the
  form, so we use exact matches*/
  if ($_GET['grapeVariety'] != 'All') {
    $query .= " AND grape_variety.variety = '{$_GET['grapeVariety']}'";
  }
  /*Same as above but there are 2 dropdowns for year, one for
  min and one for max */
  if (isset($_GET['minYear']) && isset($_GET['maxYear']))
  {
    $query .= " AND wine.year >= '{$_GET['minYear']}'" .
      " AND wine.year <= '{$_GET['maxYear']}'";
  }
  /*Cost is not dynamically generated. but still checks for a range*/
  if (strlen($_GET['minCost']) > 0 && strlen($_GET['maxCost'])>0)
  {
    $query .= " AND cost >= '{$_GET['minCost']}'" .
      " AND cost <= '{$_GET['maxCost']}'";
  }
  /*If a minimum stock level is defined, then check for records
  with a stock level greater than that.*/
  if (strlen($_GET['minStock']))
  {
    $query .= " AND on_hand >= '{$_GET['minStock']}'";
  }
  //Groups queries by wine_id, used for the SUM statements in SELECT
  $query .= " GROUP BY wine_id";
  /*Checks if minimum stock ordered is defined in the form,
  if so then checks the total stock ordered is greater than
  that define, if so that row is listed*/
  if (strlen($_GET['minOrdered']))
  { 
    $query .= " HAVING sold >= '{$_GET['minOrdered']}'";
  }
  /*Orderes all rows by wine_name in ASC order a-z*/
  $query .= " ORDER BY wine_name ASC";
  //Call the validation function
  validateFormInput();
  //Call the querying function
  displayWines($conn, $query);
?>
  </body>
</html>

