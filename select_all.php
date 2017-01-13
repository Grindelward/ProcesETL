<pre><?php


require ('connect_sql.php');
connect_database();


$dane = mysql_query("SELECT * FROM products ") ;

close_sql_connection();

       echo '<br/>';
	   echo '<br/>';
		
	echo "<table cellpadding=\"2\" border=1>"; 	
	echo "<tr>";
	echo "<td>".'ID Produktu:';
	echo "<td>".'Numer Produktu:';
	echo "<td>".'Typ produktu:';
	echo "<td>".'Producent:';
	echo "<td>".'Model:';
	echo "<td>".'Dodatkowe informacje:';
	while ($rek = mysql_fetch_array($dane))
	{
		echo "<tr>"; 
		echo "<td>".$rek['id']."<br/>";
		echo "<td>".$rek['serial_number']."<br/>";
		echo "<td>".$rek['type']."<br/>";
		echo "<td>".$rek['producent']."<br/>";
		echo "<td>".$rek['model']."<br/>";
		echo "<td>".$rek['additional_info']."<br/>";
		
	};
	echo "</table>"; 

	




?>
<!--
<html>
<form method="post" action="index.php">	
 <input type="submit" value="Powrot" />
</form>


</html>-->
