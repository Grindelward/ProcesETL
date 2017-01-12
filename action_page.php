<?php

require('simple_html_dom.php');
$GLOBALS['mysqli'] = new mysqli("localhost", "root", "", "etl") or die(mysql_error());

if (isset($_POST['clearDB'])) {
    clearDB($mysqli);
    return 0;
}else if (isset($_GET['CSV'])) {
    exportCsv();
    echo 'exported CSV';
    return 0;
}else if (isset($_GET['TXT'])) {
    exportTxt();
    echo 'exported TXT';
    return 0;
}else if($_GET['IDproduktu'] == null){
    echo "Zjebie podaj numer produkt";
    return 0;
}
else if (isset($_GET['ETL'])) {
    etl();
    return 0;
}

function etl() {
    $mysqli = $GLOBALS['mysqli'];
    $productId = $_GET['IDproduktu'];
    $baseUrl = 'http://www.ceneo.pl/';
    $opinonsTab = '/#tab=reviews';
    $opinionSub = '/opinie-';
    $url = $baseUrl . $productId . $opinonsTab;
    $site = 1;
    $product = Array();
    $opinions = Array();
    $info = Array();
    $prosArray = Array();
    $consArray = Array();
    $opCounter = 0;
    $prosCounter = 0;
    $consCounter = 0;
   


    $result = mysqli_query($mysqli, "SELECT * FROM products WHERE serial_number='$productId' LIMIT 1");
    if (mysqli_num_rows($result) > 0) {
        echo 'Product exixts';
        return 0;
    }


    $html = new simple_html_dom();
    $html->load_file($url);

//    $product['nazwa'] = $html->find(".product-name", 0)->innertext;
//    $product['cena'] = $html->find(".price", 0)->innertext;
//    $product['Ocena'] = explode(' ', $html->find('span [itemprop=ratingValue]', 0)->plaintext )[0];
    $product_type = $html->find("dl[data-gacategoryname]", 0)->attr['data-gacategoryname'];
    $product_brand = $html->find("dl[data-gacategoryname]", 0)->attr['data-brand'];
    $product_infos = str_replace($productId . '/', "", $html->find("dl[data-gacategoryname]", 0)->attr['data-gaproductname']);
    $product_model = $html->find("dl[data-gacategoryname]", 0)->attr['data-gaproductid'];
#var_dump($product);


    $products = mysqli_query($mysqli, "Insert Into etl.products (serial_number, type, producent, model, additional_info) VALUES ('$productId','$product_type','$product_brand','$product_model','$product_infos')");
    $id = mysqli_insert_id($mysqli);

    do {
        if ($site > 1) {
            $subPageUrl = $baseUrl . $productId . $opinionSub . $site;
            $html->load_file($subPageUrl);
        }
        foreach ($html->find('ol li[class=product-review]') as $a) {
            $info['Opiniujacy'] = $a->find(".product-reviewer", 0)->innertext;
            if ($a->find(".product-recommended", 0)) {
                $info['Rekomendacja'] = $a->find(".product-recommended", 0)->innertext;
            } else {
                $info['Rekomendacja'] = null;
            }
            $pros = $a->find(".pros-cell", 0);
            foreach ($pros->find('ul li') as $p) {
                array_push($prosArray, $p->innertext);
            }
            $info['Zalety'] = $prosArray;

            $cons = $a->find(".cons-cell", 0);
            foreach ($cons->find('ul li') as $c) {
                array_push($consArray, $c->innertext);
            }
            $info['Wady'] = $consArray;

            $info['Gwiazdki'] = str_replace(',', '.', str_replace('/5', '', $a->find(".review-score-count", 0)->innertext));
            $info['Data opinii'] = $a->find("span[time datetime]", 0)->attr['datetime'];
            $info['Na TAK'] = intval($a->find(".vote-yes", 0)->plaintext);
            $info['Na NIE'] = intval($a->find(".vote-no", 0)->plaintext);
            $info['Opis'] = $a->find("p[class=product-review-body]", 0)->innertext;
            array_push($opinions, $info);
            $consArray = Array();
            $prosArray = Array();
        }
        //var_dump($opinions[4]);
        $site++;
    } while ($html->find('.arrow-next'));

    foreach ($opinions as $opinion) {
        $op = mysqli_query($mysqli, "Insert Into etl.opinions (product_id, text, stars, author, date, recomended, useful, useless) VALUES ('$id','" . $opinion['Opis'] . "','" . $opinion['Gwiazdki'] . "','" . $opinion['Opiniujacy'] . "','" . $opinion['Data opinii'] . "','" . $opinion['Rekomendacja'] . "','" . $opinion['Na TAK'] . "','" . $opinion['Na NIE'] . "')");
        if($op){
            $opCounter++;
        }
        $op_id = mysqli_insert_id($mysqli);
        foreach ($opinion['Wady'] as $con) {
            $op_cons = mysqli_query($mysqli, "Insert Into etl.plus_minus (opinion_id, text, positive) VALUES ('$op_id', '$con', 0)");
            if($op_cons){
                $consCounter++;
            }
        }
        foreach ($opinion['Zalety'] as $pro) {
            $op_pros = mysqli_query($mysqli, "Insert Into etl.plus_minus (opinion_id, text, positive) VALUES ('$op_id', '$pro', 1)");
            if($op_pros){
                $prosCounter++;
            }
        }
    }
    
    echo "Dodanych opini: " . $opCounter .", dodanych wad: " .$consCounter. ", dodanych zalet: " .$prosCounter;
}

function clearDB() {

    $mysqli = $GLOBALS['mysqli'];
    mysqli_query($mysqli, "TRUNCATE TABLE products");
    mysqli_query($mysqli, "TRUNCATE TABLE opinions");
    mysqli_query($mysqli, "TRUNCATE TABLE plus_minus");
    echo "Baza wyczyszczona";
    return 0;
}

function exportCsv() {
    $mysqli = $GLOBALS['mysqli'];
    $tableArray = ["products", "opinions", "plus_minus"];
    foreach ($tableArray as $table){
       $result = mysqli_query($mysqli, "SELECT * FROM ".$table);
       
    $row = mysqli_fetch_all($result, MYSQLI_NUM);
    var_dump($row);
    $fp = fopen($table.'.csv', 'w');

    foreach ($row as $val) {
        fputcsv($fp, $val);
    }

    fclose($fp); 
    }
}

function exportTxt() {
    $mysqli = $GLOBALS['mysqli'];
    $result = mysqli_query($mysqli, "SELECT * FROM opinions");
    while($row = mysqli_fetch_array($result, MYSQLI_NUM)){  
         $fp = fopen('opinion_'.$row[0].'.txt', 'w');
        
        $num = mysqli_num_fields($result) ;    
        $last = $num - 1;
        for($i = 0; $i < $num; $i++) {            
            fwrite($fp, $row[$i]);                       
            if ($i != $last) {
                fwrite($fp, ",");
            }
        }   
        fwrite($fp, "\n");
        fclose($fp);
    }
    
}

# var_dump($op);
//$info['Opiniujacy']    = $html->find(".product-reviewer",0)->innertext;
//$info['Rekomendacja']   = trim( $html->find(".product-recommended",0)->innertext );
//$info['Zalety']   = trim( $html->find(".pros-cell",0)->innertext );
//$info['Wady']    = $html->find(".cons-cell",0)->innertext;
//$info['Gwiazdki']    = $html->find(".review-score-count",0)->innertext;
//$info['Data opinii']    = $html->find(".review-time",0)->innertext;
//$info['Na TAK']    = $html->find(".vote-yes",0)->innertext;
//$info['Na NIE']    = $html->find(".vote-no",0)->innertext;
//print_r($info);

/*
  $html2 = file_get_html("http://www.ceneo.pl/$adresik/#tab=reviews");
  $info['Opinie'] = $html2->find(".product-review",0)->innertext;
  $info['Opinie'] = $html2->find(".product-review",1)->innertext;
 */
//$html2 = new simple_html_dom();
// 
//$html2->load_file("http://www.ceneo.pl/$adresik/#tab=reviews");
//$count = 1;
//
//while ($count <= 10){
//echo '#####################################################################################';
//$info2['Opiniujacy']    = $html2->find(".product-reviewer",$count)->innertext;
//$info2['Opinia']   = trim( $html->find(".product-review-body",$count)->innertext );
//$info2['Rekomendacja']   = trim( $html->find(".product-recommended",$count)->innertext );
//$info2['Zalety']   = trim( $html2->find(".pros-cell",$count)->innertext );
//$info2['Wady']    = $html2->find(".cons-cell",$count)->innertext;
//$info2['Gwiazdki']    = $html2->find(".review-score-count",$count)->innertext;
//$info2['Data opinii']    = $html2->find(".review-time",$count)->innertext;
//$info2['Na TAK']    = $html2->find(".vote-yes",$count)->innertext;
//$info2['Na NIE']    = $html2->find(".vote-no",$count)->innertext;
//
//echo '#####################################################################################';
//
//print_r($info2);
//$count = $count + 1;
//}
//
///* ########################################################################################## */
//
//$site = 2;
//$count3 = 0;
//$count2 = 1;
//while ($site <= 4){
//
//
//$html3 = new simple_html_dom();
// 
//$html3->load_file("http://www.ceneo.pl/$adresik/opinie-$site");
//
//
//while ($count2 <= 10 ){
//echo '#####################################################################################';
//$info3['Opiniujacy']    = $html3->find(".product-reviewer",$count2)->innertext;
//$info3['Opinia']   = trim( $html3->find(".product-review-body",$count2)->innertext );
//$info3['Rekomendacja']   = trim( $html3->find(".product-recommended",$count2)->innertext );
//$info3['Zalety']   = trim( $html3->find(".pros-cell",$count2)->innertext );
//$info3['Wady']    = $html3->find(".cons-cell",$count2)->innertext;
//$info3['Gwiazdki']    = $html3->find(".review-score-count",$count2)->innertext;
//$info3['Data opinii']    = $html3->find(".review-time",$count2)->innertext;
//$info3['Na TAK']    = $html3->find(".vote-yes",$count2)->innertext;
//$info3['Na NIE']    = $html3->find(".vote-no",$count2)->innertext;
//
//echo '#####################################################################################';
//
//print_r($info3);
//$count2 = $count2 + 1;
//
//}
//$site = $site + 1;
//echo $count2;
//$count3 = $count3 + $count2;
//}
//echo 'Ilosc opini:';echo $count-1;
//echo 'Ilosc opini:';echo $count+$count3;
?>