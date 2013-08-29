<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html401/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Wine Search Results PAge</title>
  </head>
  <body bgcolor="white">

<?php

  function showerror() {
    die("Error " . mysql_errno() . " : " .mysql_error());
  }
  function validateFormInput() {

    $minYear = $_GET['minYear'];
    $maxYear = $_GET['maxYear'];

    $minStock = $_GET['minStock'];
    $maxStock = $_GET['maxStock'];

    $minCost = $_GET['minCost'];
    $maxCost = $_GET['maxCost'];

    if ($minYear > $maxYear)
    {
       $errYear = "Error: Min year is greater than max!";
       $errString .= "errYear={$errYear}&";
    }
    if ($minStock > $maxStock)
    {
      $errStock ="Error: Min stock is greater than max!";
      $errString .= "errStock={$errStock}&";
    }
    if ($minCost > $maxCost)
    {
      $errCost = "Error: min Cost is greater than max!";
      $errString .= "errCost={$errCost}&";
    }

    if (isset($errYear) || isset($errStock) || isset($errCost))
    {
      header("Location: index.php?{$errString}");
    }
  }


  require 'db.php';

  function displayWines($conn, $query) {
  
    if (!($result = @mysql_query($query, $conn))) {
      showerror();
    }
    
    $rowsFound = @mysql_num_rows($result);

    if ($rowsFound > 0) {
      print "\n<table border=1>\n<tr>" .
        "\n\t<th>Wine ID</th>" .
        "\n\t<th>Wine Name</th>" .
        "\n\t<th>Year</th>" .
        "\n\t<th>Winery</th>" .
        "\n\t<th>Variety</th>\n</tr>";
     
      while ($row = @mysql_fetch_array($result)) {
        
        print "\n<tr>\n\t<td>{$row["wine_id"]}</td>" .
          "\n\t<td>{$row["wine_name"]}</td>" .
          "\n\t<td>{$row["year"]}</td>" .
          "\n\t<td>{$row["winery_name"]}</td>" .
          "\n\t<td>{$row["variety"]}</td>\n</tr>";
      }

      print "\n</table>";
    }

    print "{$rowsFound} records found.<br>";
  
  }

  if (!($conn = @mysql_connect(DB_HOST, DB_USER, DB_PW))) {
    die("Could not connect");
  }

  if (!mysql_select_db(DB_NAME, $conn)) {
    showerror();
  }

  $query = "SELECT wine.wine_id, wine_name, year, winery_name, variety
FROM winery, region, wine, grape_variety, wine_variety 
WHERE winery.region_id = region.region_id 
AND wine.winery_id = winery.winery_id
AND wine_variety.wine_id = wine.wine_id
AND wine_variety.variety_id = grape_variety.variety_id"; 

  if (isset($_GET['regionName']) && $_GET['regionName'] != 'All')
  {
    $query .= " AND region_name = '{$_GET['regionName']}'";
  }
  if (isset($_GET['wineName'])) {  
    $query .= " AND wine.wine_name LIKE '%{$_GET['wineName']}%'";
  }
  if (isset($_GET['wineryName'])) {
    $query .= " AND winery.winery_name LIKE '%{$_GET['wineryName']}%'";
  }
  if (isset($_GET['grapeVariety'])) {
    $query .= " AND grape_variety.variety = '{$_GET['grapeVariety']}'";
  }

  validateFormInput();

  //$query .= ";"

  displayWines($conn, $query);
?>

  </body>
</html>

