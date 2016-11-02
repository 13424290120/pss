<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="gbk">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="jackson li">
    <title>Purchase Requisition</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/order.js"></script>
    
    <script type = "text/javascript" language = "javascript">
        // To get the invoice address from database by ajax
       $(document).ready(function() {
            $("#mySelect").change(function(event){
               var invoiceId = $(this).val();
               $("#address").load('ajax.php', {"id":invoiceId} );
            });
           
            // Click the printButton action
            $("#printButton").click(function()
            {
                    window.print();
            });
            
            // Click the saveButton action
            $("#saveButton").click(function()
            {
                    // To submit ajaxform data by ajax.
                    $("#ajaxform").submit(function(e)
                    {
                            $("#simple-msg").html("");
                            var postData = $(this).serializeArray();
                            var formURL = $(this).attr("action");
                            $.ajax(
                            {
                                    url : formURL,
                                    type: "POST",
                                    data : postData,
                                    success:function(data, textStatus, jqXHR) 
                                    {
                                            $("#simple-msg").html('<pre><code class="prettyprint">'+data+'</code></pre>');
                                            $("#simple-msg").fadeOut(600);

                                    },
                                    error: function(jqXHR, textStatus, errorThrown) 
                                    {
                                            $("#simple-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus='+textStatus+', errorThrown='+errorThrown+'</code></pre>');
                                    }
                            });
                        e.preventDefault();	//STOP default action
                        //e.unbind();
                    });

                    $("#ajaxform").submit(); //SUBMIT FORM
                    
                    //To submit gridform data by ajax.
                    
                    $("#gridForm").submit(function(e)
                    {
                            $("#simple-msg").html("");
                            var postData = $(this).serializeArray();
                            var formURL = $(this).attr("action");
                            $.ajax(
                            {
                                    url : formURL,
                                    type: "POST",
                                    data : postData,
                                    success:function(data, textStatus, jqXHR) 
                                    {
                                            $("#simple-msg").html('<pre><code class="prettyprint">'+data+'</code></pre>');
                                            $("#simple-msg").fadeOut(600);

                                    },
                                    error: function(jqXHR, textStatus, errorThrown) 
                                    {
                                            $("#simple-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus='+textStatus+', errorThrown='+errorThrown+'</code></pre>');
                                    }
                            });
                        e.preventDefault();	//STOP default action
                        //e.unbind();
                    });

                    $("#gridForm").submit(); //SUBMIT FORM
            });           
            
          
       }); 
    </script>
    
  </head>

  <body>
      
<?php
//初始化数据库
include_once 'db.php';

//判断用户是否登录
if(isset($_SESSION["username"]) && $_SESSION["username"]){
    $requestor=$_SESSION["username"];
}else{
    echo '<div class="error"> Sorry, please login first!<br><a href="index.php">Go Back</a></div>';  
    return false;    
}      

//从数据库取出字典数据，生成表单下拉清单

$currentDate = date("Y-m-d");

$sqlAccount = "SELECT `id`,`accountNumber`,`description` FROM `account`";
$sqlCostCode = "SELECT `id`,`code`,`codeName` FROM `costcode`";
$sqlCategory = "SELECT `id`,`name` FROM `category`";
$sqlInvoice = "SELECT `id`,`name`,`address` FROM `invoice`";

$stmtAccount = $db->prepare($sqlAccount);
$stmtCostCode = $db->prepare($sqlCostCode);
$stmtCategory = $db->prepare($sqlCategory);
$stmtInvoice = $db->prepare($sqlInvoice);

$stmtAccount->execute();
$stmtCostCode->execute();
$stmtCategory->execute();
$stmtInvoice->execute();

//

$prNumber=$_GET['id'];

$sqlPrNumber = "SELECT * FROM `request` WHERE prNumber='$prNumber'";
$stmtPrNumber = $db->query($sqlPrNumber);
$row = $stmtPrNumber->fetch(PDO::FETCH_ASSOC);
//print_r($row);
    $prDate=$row['prDate'];
    $supplierName=$row['supplierName'];
    $supplierContact = $row['supplierContact'];
    $supplierPhone= $row['supplierPhone'];
    $withinBudget=$row['withinBudget'];
    $recoverable=$row['recoverable'];
    $currency=$row['currency'];
    $purpose=$row['purpose'];
    $deliveryDate=$row['deliveryDate'];
    $gridContents=$row['gridContents'];
    
    
    $arrayGridContents = unserialize($gridContents); //将表格内容由文本序列转换成数组
    //print_r($arrayGridContents);

?>

    <!-- Begin page content -->
    <div id="content" class="container" style="width:1000px;">
        <form id="ajaxform" name="ajaxform" action="ajax-form-submit.php" method="post">
      <div class="page-header">
          <h1>PremiumSoundSolutions<small> Purchase Requisition</small></h1>          
          PR Number: <input type="text" class="prinput" name="prNumber" value="<?php echo $_GET['id'] ?>">
          PR Date: <input type="text" class="prinput" name="prDate" value="<?php echo $prDate ?>">
      </div>  
            
        <div class="row">
          <div class="col-xs-6">
              <p><span class="badge">Supplier Info</span></p>
              Order to:<input name="supplierName" class="form-control" value="<?php echo $supplierName ?>">
              Contact:<input name="supplierContact" class="form-control" value="<?php echo $supplierContact ?>">
              Telephone:<input name="supplierPhone" class="form-control" value="<?php echo $supplierPhone ?>">
          </div>
          <div class="col-xs-6">
              <p><span class="badge">Invoice Info</span></p>
              Invoice to:              
              <select id="mySelect" class="form-control" name="invoiceTo" >
                  <option value="0">------------------------------</option>
                  <?php
                  while($rowInvoice = $stmtInvoice->fetch(PDO::FETCH_ASSOC)){
                      if ($row['invoiceTo']===$rowInvoice['id']){
                          echo "<option value=".$rowInvoice['id']." selected='selected'>".$rowInvoice['name']."</option>";
                      }else{
                          echo "<option value=".$rowInvoice['id'].">".$rowInvoice['name']."</option>";    
                      }
                  }
                  ?> 
              </select>              
              Address:<textarea id="address" name="invoiceAddress" class="form-control" rows="3"><?php echo $row['invoiceAddress'] ?></textarea>

          </div>
        </div>   
        <hr style="margin:5px; margin-bottom: 2px;"></hr>
        <div class="row">
          <div class="col-xs-4">
              Purchase Category:
              <select class="form-control" name="categoryName">
                  <option value="0">------------------------------</option>
                  <?php
                  while($rowCategory = $stmtCategory->fetch(PDO::FETCH_ASSOC)){
                      if ($rowCategory['id']===$row['categoryName']){
                          echo "<option value=".$rowCategory['id']." selected='selected'>".$rowCategory['name']."</option>";
                      }else{
                          echo "<option value=".$rowCategory['id'].">".$rowCategory['name']."</option>"; 
                      }
                                           
                  }
                  ?> 
              </select>
          </div>
          <div class="col-xs-4">
              Cost Code:
              <select class="form-control" name="costCode">
                  <option value="0">------------------------------</option>                  
                  <?php
                  while($rowCostCode = $stmtCostCode->fetch(PDO::FETCH_ASSOC)){
                      if($rowCostCode['id']===$row['costCode']){
                          echo "<option value=".$rowCostCode['id']." selected='selected'>".$rowCostCode['code']." - ".$rowCostCode['codeName']."</option>";                          
                      }else{
                          echo "<option value=".$rowCostCode['id'].">".$rowCostCode['code']." - ".$rowCostCode['codeName']."</option>"; 
                      }
                                           
                  }
                  ?> 
              </select>
          </div>
          <div class="col-xs-4">
              Account No.:
              <select class="form-control" name="accountNumber">
                  <option value="0">------------------------------</option>
                  <?php
                  while($rowAccount = $stmtAccount->fetch(PDO::FETCH_ASSOC)){
                      if($rowAccount['id']===$row['accountNumber']){
                          echo "<option value=".$rowAccount['id']." selected='selected'>".$rowAccount['accountNumber']." - ".$rowAccount['description']."</option>";
                      }else{
                          echo "<option value=".$rowAccount['id'].">".$rowAccount['accountNumber']." - ".$rowAccount['description']."</option>";
                      }
                                            
                  }
                  ?>
              </select>
          </div>            
        </div>
        
        <div class="row">
          <div class="col-xs-4">
              Delivery Date Required:<input type="date" name="deliveryDate" class="form-control" value="<?php echo $deliveryDate ?>"></input>
              <span class="badge" style="margin:20px;">With In Budget: <input  type="radio" name="withInBudget" value="1" <?php if ($withinBudget==="1"){ echo "checked"; } ?>>Yes</input>
              <input type="radio" name="withInBudget" value="0" <?php if ($withinBudget==="0"){ echo "checked"; } ?>>No</input></span>              
          </div>
          <div class="col-xs-4">
              Currency:
              <select name="currency" class="form-control">                  
                  <option></option>
                  <option <?php if($currency==="RMB"){ echo "selected=selected"; }?> value="RMB">RMB</option>
                  <option <?php if($currency==="HKD"){ echo "selected=selected"; }?> value="HKD">HKD</option>
                  <option <?php if($currency==="USD"){ echo "selected=selected"; }?> value="USD">USD</option>
                  <option <?php if($currency==="EURO"){ echo "selected=selected"; }?> value="EURO">EURO</option>
              </select>
              <span class="badge" style="margin:20px;"> Recoverable: <input  type="radio" name="Recoverable" value="1" <?php if ($recoverable==="1"){ echo "checked"; } ?>>Yes</input>
              <input  type="radio" name="Recoverable" value="0" <?php if ($recoverable==="0"){ echo "checked"; } ?>>No</input></span>
          </div>

          <div class="col-xs-4">
              Charge Back To:<textarea class="form-control" rows="3">Customer Code/Name:
                                                                     Charge Amount:
                             </textarea>              

          </div>            
        </div>
        
        
        <div class="row">
          <div class="col-xs-12">
              Purpose:<textarea class="form-control" rows="3" name="purpose"><?php echo $purpose ?></textarea>
          </div>         
        </div>
    </form>

        <hr></hr>
        
        
        <div class="row">
          <div class="col-xs-12">
          <form id="gridForm" action="ajax-gridform-save.php" method="post">
              <input type="hidden" name="prNumber" value="<?php echo $prNumber ?>">  
            <table id="order-table" class="table-hover">
                    <tr>
                            <th style="width:55%;">Item</th>
                            <th style="width:20%;">Project No.</th>               
                            <th style="width:8%;">UnitPrice</th>		
                            <th style="width:5%;">Quantity</th>
                            <th style="width:10%;">Subtotal</th>
                    </tr>
                    
                    <?php
                    $i = 1; //定义行号                    
                    foreach($arrayGridContents as $gridRow){ //遍历表格内容数组 
                        
                        if(is_array($gridRow)){ 
                            $inputName = "row".$i."[]"; // 定义输入框名称，以数组的形式存储数据
                            echo "<tr>";
                            echo "        <td class='product-title'><input name=\"$inputName\" type='text' value=\"$gridRow[0]\" class='form-control' ></td>";
                            echo "        <td class='product-title'><input name=\"$inputName\" type='text' value=\"$gridRow[1]\" class='form-control'></td>";
                            echo "        <td><input name=\"$inputName\" type='text'  value=\"$gridRow[2]\" class='price-per-pallet form-control'></td>";
                            echo "        <td class='num-pallets'>";
                            echo "                <input name=\"$inputName\" type='text' value=\"$gridRow[3]\" class='num-pallets-input form-control' id='turface-pro-league-num-pallets'>";
                            echo "        </td>";             
                            echo "        <td class='row-total'>";
                            echo "                <input name=\"$inputName\" type='text' value=\"$gridRow[4]\" class='row-total-input form-control' id='turface-pro-league-row-total' readonly='readonly'>";
                            echo "        </td>";
                            echo "</tr>"; 
                            $i++;
                        }
                    }
                    ?>
                    <tr>
                            <td>Total:</td>                            
                            <td></td>
                            <td></td>
                            <td colspan="6" style="text-align: right;">
                                    <input type="text" name="total" value="<?php echo $arrayGridContents['total'] ?>" class="total-box form-control" id="product-subtotal" readonly="readonly"></input>
                            </td>
                    </tr>
            </table>
          </form>
          </div>         
        </div>

        <hr></hr>
        
        <div class="row">
          <div class="col-xs-12">
              <table style="width:100%;">
                  <tr>
                      <th style="width:25%;">Requstor</th>
                      <th style="width:25%;">Department Manager</th>
                      <th style="width:25%;">Finance</th>
                      <th style="width:25%;">General Manager</th>
                  </tr>                  
                  <tr>
                      <td><input type="text" class="prinput" value="<?php echo $requestor ?>"></td>
                      <td><input type="text" class="prinput"></td>
                      <td><input type="text" class="prinput"></td>
                      <td><input type="text" class="prinput"></td>
                  </tr>
              </table>
          </div>         
        </div>
        <br></br>
        <div id="simple-msg"></div>
        <div class="row noprint">
            <center>
                <input id="saveButton" type="button" value=" Save " class="btn btn-success">
                <input id="printButton" type="button" value=" Print " class="btn btn-success">
            </center>
            <hr>
        </div>        
    </div>
  </body>
</html>