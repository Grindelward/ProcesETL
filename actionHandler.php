<?php
require_once 'class_etl.php';
$etl = new etl();
$result = 0;
if($_POST['jobType'] == "ETL"){
    $result = $etl->makeEtl();
}
if( $_POST['jobType'] == "CLEARDB" ) {
     
     $result = $etl->clearDB();
}
if( $_POST['jobType'] == "EXTRACT" ) {
     
    $result = $etl->ex();
}
if( $_POST['jobType'] == "TRANSFORM" ) {
     
     $result = $etl->makeEtl();
}
if( $_POST['jobType'] == "LOAD" ) {
     
     $result = $etl->makeEtl();
}
if( $_POST['jobType'] == "CSV" ) {
     
     $result = $etl->exportCsv();
}

if( $_POST['jobType'] == "TXT" ) {
     
     $result = $etl->exportTxt();
}


echo $result;