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
  <input id="productId" type="text" name="IDproduktu" value="">
  <br>
  <br>
  <input type="submit" value="ETL" name="ETL">
  <input type="submit" value="CSV" name="CSV">
  <input type="submit" value="TXT" name="TXT">
</form> 
    <form method="post" action="action_page.php">
    <input type="submit" value="clearDB" name="clearDB"> 
   <input id="showAll" type="submit" value="All" name="ShowAll">
   <input id="showId" type="submit" value="ShowById" name="ShowId">
</form>

    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script>
    $(document).ready(function(){
      
      $("#showAll").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "select_all.php",
                data: {
                jobType: "LOAD"
            },
                success:function(result){
          $("#ETL_result").html(result);
        }});
      });
      
      $("#showId").click(function(e){
          e.preventDefault();
        $.ajax({type: "POST",
                url: "select_id.php",
                data: { idProduct: $("#productId").val(), jobType: "ETL" },
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