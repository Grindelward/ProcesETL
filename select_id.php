<pre><?php
$productId = $_POST['idProduct'];
require ('connect_sql.php');
//connect_database();
$GLOBALS['mysqli'] = new mysqli("localhost", "root", "", "etl") or die(mysql_error());
$mysqli = $GLOBALS['mysqli'];
$dane = mysqli_query($mysqli,"SELECT * FROM products WHERE serial_number='$productId'") ;
$dane2 = mysqli_query($mysqli,"SELECT * FROM opinions  WHERE product_id='$productId'") ;
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
	while ($rek = mysqli_fetch_array($dane))
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
	
	while ($rek2 = mysqli_fetch_array($dane2))
	{
		echo "<tr>"; 
		echo "<td>".$lp."<br/>";
		echo "<td>".$rek2['product_id']."<br/>";
		echo "<td>".$rek2['stars']."<br/>";
		echo "<td>".$rek2['text']."<br/>";
		echo "<td>".$rek2['author']."<br/>";
		echo "<td>".$rek2['date']."<br/>";
		echo "<td>".$rek2['recomended']."<br/>";
		echo "<td>".$rek2['useful']."<br/>";
		echo "<td>".$rek2['useless']."<br/>";
	
$lp = $lp+1;	
	};
	 
	

	
?>