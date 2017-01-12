<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<form action="action_page.php" method="GET">
  Product ID:<br>
  <input type="text" name="IDproduktu" value="">
  <br>
  <br>
  <input type="submit" value="ETL" name="ETL">
  <input type="submit" value="E" name="Extract"> 
  <input type="submit" value="T" name="Transform"> 
  <input type="submit" value="L" name="Load"> 
  <input type="submit" value="CSV" name="CSV">
  <input type="submit" value="TXT" name="TXT">
</form> 
 
    <form method="post" action="action_page.php">
    <input type="submit" value="clearDB" name="clearDB"> 
</form>






</body>
</html>