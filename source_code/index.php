<?php
include 'constants.php';
?>
<!DOCTYPE html>  
 <html>  
      <head>  
           <title>Transaction MTN MOMO and OM</title>  
           <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">
           </script>  
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
           <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
      </head>  
      <body>  
           <br />  
           <div class="container" style="width:500px;">  
                <h3>Buy Books using MTN Momo or OM</h3><br />  
             
                <div class="table-responsive">  
                    <form  id="payment" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                     <table class="table table-bordered">  
                          <tr>  
                               <th>Name</th>   <th> Cost (<?php echo $currency ?>)</th>  <th>#</th>    
                          </tr>  
                          <?php   
                          $data = file_get_contents("articles.json");  
                          $data = json_decode($data, true);  
                          foreach($data as $row)  
                          {  
                               echo '<tr><td>'.$row["name"].'</td> 
                                         <td style="text-align:center">'.$row["cost"].'</td> 
                                         <td>  <input name='.$row["cost"];
                                         //ensuring that checked boxes remain checked
                                        if(isset($_POST[$row['cost']]) && $_POST[$row['cost']] == 'on')
                                             echo ' checked';
                                       echo ' type="checkbox" > </input></td></tr>';  
                          }  
                          
                         
                          ?>  
                           <tr><td colspan="3" style="text-align:right">
                              <label>Phone number<input type = "number" name="phone_num" 
                              <?php 
                                    if(isset($_POST['phone_num']))
                                        echo ' value = "'.$_POST['phone_num'].'" ';
                              ?>  pattern ="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" > </label>
                              <input  type="submit"  name="mtn_pay"  class="btn btn-warning" id="mtn_pay" data-toggle="modal" data-target="#waitClient"  value="MTN Momo">                          
                              <input type="submit" name="orange_pay" class="btn btn-primary" value="Orange OM">
                        
                 </td></tr> 
                    </table>  
                 
              <!-- Modal -->
              <div class="modal fade" id="waitClient" tabindex="-1" role="dialog" aria-labelledby="waitClient" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered" role="document">
               <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="waitClient">WAIT FOR VALIDATION</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                    Wait for the client to validate this transaction (You can inform the client to enter his/her PIN) to confirm
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
               </div>
               </div>
               </div>
                  
                    <?php      
                         include 'tokenrequestpay.php'; //php file that gets $access_token

                    ?>
                     
                    </form>
                
             
                </div>  
           </div>  
           <br />  
      </body>  
 </html>  