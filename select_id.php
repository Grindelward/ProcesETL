<pre><?php

$productId = $_GET['IDproduktu'];
require ('connect_sql.php');
connect_database();

$dane = mysql_query("SELECT * FROM products WHERE serial_number='$productId'") ;


   echo '<br/>';
	   echo '<br/>';
		
	echo "<table cellpadding=\"2\" border=1>"; 	
	echo "<tr>";
	echo "<td>".'ID Produktu:';
	echo "<td>".'Numer Produktu:';
	echo "<td>".'Typ produktu:';
	echo "<td>".'Producent:';
	echo "<td>".'Model:';
	echo "<td>".'Nazwa:';
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


$dane2 = mysql_query("SELECT * FROM opinions  WHERE serial_number='$productId'") ;

$lp = 1;

       echo '<br/>';
	    echo '<br/>';
		
	echo "<table cellpadding=\"2\" border=1>"; 	
	echo "<tr>";
	echo "<td>".'id:';
	echo "<td>".'Numer produktu:';
	echo "<td>".'Ocena:';
	echo "<td>".'Tresc:';
	echo "<td>".'Autor';
	echo "<td>".'Data dodania:';
	echo "<td>".'Rekomendacja:';
	echo "<td>".'Uzyteczna:';
	echo "<td>".'Nieuzyteczna:';
	
	while ($rek2 = mysql_fetch_array($dane2))
	{
		echo "<tr>"; 
		echo "<td>".$lp."<br/>";
		echo "<td>".$rek2['serial_number']."<br/>";
		echo "<td>".$rek2['stars']."<br/>";
		echo "<td>".$rek2['text']."<br/>";
		echo "<td>".$rek2['author']."<br/>";
		echo "<td>".$rek2['date']."<br/>";
		echo "<td>".$rek2['recomended']."<br/>";
		echo "<td>".$rek2['useful']."<br/>";
		echo "<td>".$rek2['useless']."<br/>";
	
$lp = $lp+1;	
	};
	 
	
close_sql_connection();	
	
?>









<html>

 
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  
<form method="post" action="index.php">	
 <input type="submit" value="Powrot" />
</form>


</html>
















