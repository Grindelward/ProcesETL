<?php
require('simple_html_dom.php');
$GLOBALS['mysqli'] = new mysqli("localhost", "root", "", "etl") or die(mysql_error());

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of test
 *
 * @author DamianFigurski
 */
class etl {
    var $baseUrl = 'http://www.ceneo.pl/';
    var $opinonsTab = '/#tab=reviews';
    var $opinionSub = '/opinie-';
    
 
    public function Get_Url($baseUrl, $productId, $opinionsTab)
    {
        $url = $baseUrl . $productId . $opinionsTab;
        return $url;
    }
    
    public function Get_SubUrl($baseUrl, $productId, $opinionsSub)
    {
        $subUrl = $baseUrl . $productId . $opinionsSub;
        return $subUrl;
    }
    
    public function ex($sitesTabela, $productId, $sitesValue, $subPageUrl){
        
    //    $product['nazwa'] = $html->find(".product-name", 0)->innertext;
    //    $product['cena'] = $html->find(".price", 0)->innertext;
    //    $product['Ocena'] = explode(' ', $html->find('span [itemprop=ratingValue]', 0)->plaintext )[0];
        
        $productArray['type'] = $sitesTabela[$sitesValue]->find("dl[data-gacategoryname]", 0)->attr['data-gacategoryname'];
        $productArray['brand'] = $sitesTabela[$sitesValue]->find("dl[data-gacategoryname]", 0)->attr['data-brand'];
        $productArray['infos'] = str_replace($productId . '/', "", $sitesTabela[$sitesValue]->find("dl[data-gacategoryname]", 0)->attr['data-gaproductname']);
        $productArray['model'] = $sitesTabela[$sitesValue]->find("dl[data-gacategoryname]", 0)->attr['data-gaproductid'];
#var_dump($product);
    
        if ($sitesTabela[$sitesValue]->find('.arrow-next'))
        {
            while ($sitesTabela[$sitesValue]->find('.arrow-next'))
            {
                $sitesValue++;
                $subUrl = $subPageUrl . $sitesValue;
                $sitesTabela[$sitesValue] = new simple_html_dom();
                $sitesTabela[$sitesValue]->load_file($subUrl);
            }
        }   

        return array($sitesTabela,$productArray);
    } 

    public function tr($sitesArray)
    {
        $opinions = Array();
        $info = Array();
        $prosArray = Array();
        $consArray = Array();
        
        foreach ($sitesArray as $html)
        {
            foreach ($html->find('ol li[class=product-review]') as $a) 
            {
                $info['Opiniujacy'] = $a->find(".product-reviewer", 0)->innertext;
                
                if ($a->find(".product-recommended", 0)) 
                {
                    $info['Rekomendacja'] = $a->find(".product-recommended", 0)->innertext;
                } 
                
                else 
                {
                    $info['Rekomendacja'] = null;
                }
                
                $pros = $a->find(".pros-cell", 0);
                foreach ($pros->find('ul li') as $p) 
                {
                    array_push($prosArray, $p->innertext);
                }
                $info['Zalety'] = $prosArray;

                
                $cons = $a->find(".cons-cell", 0);
                foreach ($cons->find('ul li') as $c) 
                {
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
        }
        var_dump($opinions);
        return $opinions;
    }
    
    public function lo($productsArray, $opinionsArray, $productId)
    {
        $opCounter = 0;
        $prosCounter = 0;
        $consCounter = 0;
        $mysqli = $GLOBALS['mysqli'];
        
        
        $products = mysqli_query($mysqli, "Insert Into etl.products (serial_number, type, producent, model, additional_info) VALUES ('$productId','" . $productsArray['type'] . "','" . $productsArray['brand'] . "','" . $productsArray['model'] . "','" . $productsArray['infos'] . "')");
        var_dump($products);
        foreach ($opinionsArray as $opinion) 
        {
            $op = mysqli_query($mysqli, "Insert Into etl.opinions (product_id, text, stars, author, date, recomended, useful, useless) VALUES ('$productId','" . $opinion['Opis'] . "','" . $opinion['Gwiazdki'] . "','" . $opinion['Opiniujacy'] . "','" . $opinion['Data opinii'] . "','" . $opinion['Rekomendacja'] . "','" . $opinion['Na TAK'] . "','" . $opinion['Na NIE'] . "')");
            if($op)
            {
                $opCounter++;
            }
            $op_id = mysqli_insert_id($mysqli);
            foreach ($opinion['Wady'] as $con) 
            {
                $op_cons = mysqli_query($mysqli, "Insert Into etl.plus_minus (opinion_id, text, positive) VALUES ('$op_id', '$con', 0)");
                if($op_cons)
                {
                    $consCounter++;
                }
            }
            foreach ($opinion['Zalety'] as $pro) 
            {
                $op_pros = mysqli_query($mysqli, "Insert Into etl.plus_minus (opinion_id, text, positive) VALUES ('$op_id', '$pro', 1)");
                if($op_pros)
                {
                    $prosCounter++;
                }
            }
        }
        
        echo "Dodanych opini: " . $opCounter .", dodanych wad: " .$consCounter. ", dodanych zalet: " .$prosCounter;
    }
    
    public function exportCsv() 
    {
        $mysqli = $GLOBALS['mysqli'];
        $tableArray = ["products", "opinions", "plus_minus"];
        foreach ($tableArray as $table)
        {
           $result = mysqli_query($mysqli, "SELECT * FROM ".$table);

        $row = mysqli_fetch_all($result, MYSQLI_NUM);
       
        $fp = fopen($table.'.csv', 'w');

        foreach ($row as $val) {
            fputcsv($fp, $val);
        }

        fclose($fp); 
        }
        
        echo 'exportCSV';
    }
    
    public function exportTxt() 
    {
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
        
        echo 'exportTXT';
    }
    
    public function clearDB() 
    {

        $mysqli = $GLOBALS['mysqli'];
        mysqli_query($mysqli, "TRUNCATE TABLE products");
        mysqli_query($mysqli, "TRUNCATE TABLE opinions");
        mysqli_query($mysqli, "TRUNCATE TABLE plus_minus");
        echo "Baza wyczyszczona";
        return 0;
    }
    
 /*  public function check_isOrginal($productId)
    {
        $mysqli = $GLOBALS['mysqli'];
        $result = mysqli_query($mysqli, "SELECT * FROM products WHERE serial_number='$productId' LIMIT 1");
        if (mysqli_num_rows($result) > 0) {
            echo 'Product exixts';
        return 0;
    }
    }*/
    
 public function makeEtl() {
     


$etlObj = new etl;

$productId = $_POST['idProduct'];
$baseUrl = $etlObj->baseUrl;
$opinionsTab = $etlObj->opinonsTab;
$opinionsSub = $etlObj->opinionSub;

//$etlObj->check_isOrginal($productId);

$url = $etlObj->Get_Url($baseUrl, $productId, $opinionsTab);
$subUrl = $etlObj->Get_SubUrl($baseUrl, $productId, $opinionsSub);

$firstPageArray = Array();
$sitesValue = 1;
$firstPageArray[$sitesValue] = new simple_html_dom();
$firstPageArray[$sitesValue]->load_file($url);


$product = $etlObj->ex($firstPageArray, $productId, $sitesValue, $subUrl)[1];
$sitesArray = $etlObj->ex($firstPageArray, $productId, $sitesValue, $subUrl)[0];
$opinions = $etlObj->tr($sitesArray);   
$etlObj->lo($product, $opinions, $productId);

 }
    
}
#################### MAIN ################################
