<pre><?php

require('simple_html_dom.php');
 
/*
        Klasa           .nazwaklasy
        ID              #nazwaid
        tag             img
        klasa + name    .nazwaklasy[name='nazwa']
*/




$html = file_get_html("http://www.ceneo.pl/45007093");

echo $html;



?>