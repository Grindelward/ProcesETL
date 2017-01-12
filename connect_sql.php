<?php 
 

 
function connect_database() 
{  
 
 $connection = @mysql_connect('localhost', 'root', '')


or die('Brak połączenia z serwerem MySQL.<br />Błąd: '.mysql_error()); 
 
echo "Udało się połączyć z serwerem!<br />"; 

$db = @mysql_select_db('etl', $connection) 

or die('Nie mogę połączyć się z bazą danych<br />Błąd: '.mysql_error()); 

echo "Udało się połączyć z bazą dancych!<br/>"; 
 
};


function close_sql_connection() 
{  
 
	$connection = @mysql_connect('localhost', 'root', '');
	mysql_close($connection);
	echo "Zamknięto połączenie z bazą danych!"; 
};

?>
