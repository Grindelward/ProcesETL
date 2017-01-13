<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<form action="class_etl.php" method="GET">
  Product ID:<br>
  <input id="productId" type="text" name="IDproduktu" value="">
  <br>
  <br>
  <input id="ETL" type="submit" value="ETL" name="ETL">
  <input id="EXTRACT" type="submit" value="E" name="Extract"> 
  <input id="TRANSFORM" type="submit" value="T" name="Transform"> 
  <input id="LOAD" type="submit" value="L" name="Load"> 
  <input id="CSV" type="submit" value="CSV" name="CSV">
  <input id="TXT" type="submit" value="TXT" name="TXT">
</form> 
 
    <input id="clearDB" type="submit" value="clearDB" name="clearDB"> 


    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script>
    $(document).ready(function(){
      $("#ETL").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "actionHandler.php",
                data: { idProduct: $("#productId").val(), jobType: "ETL" },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
      $("#clearDB").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "actionHandler.php",
                data: {
                jobType: "CLEARDB"
            },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
      $("#CSV").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "actionHandler.php",
                data: {
                jobType: "CSV"
            },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
      $("#TXT").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "actionHandler.php",
                data: {
                jobType: "TXT"
            },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
      $("#EXTRACT").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "actionHandler.php",
                data: {
                jobType: "EXTRACT"
            },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
      $("#TRANSFORM").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "actionHandler.php",
                data: {
                jobType: "TRANSFORM"
            },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
      $("#LOAD").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "actionHandler.php",
                data: {
                jobType: "LOAD"
            },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
    });
    </script>

    <div id="ETL_result">
        
        
    </div>



</body>
</html>