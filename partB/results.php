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

  require 'db.php';

  function displayWines($conn, $query,) {
  
    if (!($result = @mysql_query($query, $conn))) {
      showerror();
    }
    
    $rowsFound = @mysql_num_rows($result);

    if ($rowsFound > 0) {
      print "\n<table>\n<tr>" .
        "\n\t<th>Wine ID</th>" .
        "\n\t<th>Wine Name</th>" .
        "\n\t<th>Year</th>" .
        "\n\t<th>Winery</th>" .
        "\n\t<th>Description</th>\n</tr>";
     
      while ($row = @mysql_fetch_array($result)) {
        
        print "\n<tr>\n\t<td>{$row["wine_id"]}</td>" .
          "\n\t<td>{$row["wine_name"]}</td>" .
          "\n\t<td>{$row["year"]}</td>" .
          "\n\t<td>{$row["winery_name"]}</td>" .
          "\n\t<td>{$row["description"]}</td>\n</tr>";
      }

      print "\n</table>";
    }

    print "{$rowsFOund} records found.<br>";
  
  }

  if (!($conn = @mysql_connect(DB_HOST, DB_USER, DB_PW))) {
    die("Could not connect");
  }

  



