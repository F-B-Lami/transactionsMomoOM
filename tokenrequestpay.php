<?php

include 'authgen.php';
include 'uuid.php';



// This sample uses the Apache HTTP client from HTTP Components (http://hc.apache.org/httpcomponents-client-ga/)
require_once 'HTTP/Request2.php';

$amountToPay = array_sum(array_keys($_POST, 'on'));
if(isset($_POST['phone_num'])){
$phoneNum = $_POST['phone_num'];
if($amountToPay > 0 ){
    //echo "<div class='alert alert-info text-center'>Wait for the client (<strong>".$amountToPay ."</strong>) to confirm this MTN Momo payment of <strong>".$phoneNum." </strong> FCFA...</div>";

if(isset($_POST['mtn_pay']))
    {
      $dataRequestPayment = paramsRequestPay($amountToPay, $currency, $transactionId, $MSISDN, $phoneNum, $payMessage, $payeeNote);
      //I have access_token
      $access_token = getTokenPay($credentials, $pk);
      paymentMTN($amountToPay, $phoneNum, $credentials, $pk, $targetEnv, $uuidvalue, $dataRequestPayment, $access_token);
    }
elseif(isset($_POST['orange_pay']))
    {
      paymentOrange($amountToPay, $phoneNum);
    }
}
else
     echo "Debit ".$phoneNum." an amount of ".$amountToPay." FCFA";
}
function paymentMTN($amt, $phoneNumber, $credentials, $pk, $targetEnv, $uuidvalue, $dataRequestPayment, $access_token)
{
    
    //I have made a request to pay
    requestPay($access_token, $targetEnv, $pk, $uuidvalue, $dataRequestPayment);
    
    responsePay($access_token, $targetEnv, $pk, $uuidvalue);

}
function paymentOrange($amt, $phoneNumber)
{
     //insert code for payment here with Orange
     echo "let us <strong>OM ".$amt." FCFA</strong> for this transaction<br>Debit ".$phoneNumber;
}

function paramsRequestPay($amountToPay, $currency, $id, $payerIdType, $payerId, $payerMessage, $payeeNote)
{
   
    $data->amount = strval($amountToPay);
    $data->currency = $currency;
    $data->externalId = $id;
    $payer->partyIdType = $payerIdType;
    $payer->partyId = $payerId;
    $data->payer = $payer;
    $data->payerMessage = $payerMessage;
    $data->payeeNote = $payeeNote;

    return json_encode($data, JSON_FORCE_OBJECT);
}


function responsePay($access_token, $targetEnv, $pk, $uuidvalue)
{
while(TRUE){
$waitDuration = 5;
time_sleep_until(time()+$waitDuration);
$request = new Http_Request2('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/'.$uuidvalue);
$url = $request->getUrl();

$headers = array(
    // Request headers
    'Authorization' => $access_token,
    'X-Target-Environment' => $targetEnv,
    'Ocp-Apim-Subscription-Key' => $pk,
);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_GET);

// Request body
$request->setBody("{body}");
try
{

    $response = $request->send();
  
   // echo $response->getBody();

    $decode = json_decode($response->getBody(), true);
  

    if($decode['reason'] == "EXPIRED")
    {
     echo "<div class='alert alert-danger text-center'>Time out <strong>REINITIATE DEBIT of ".$decode['amount']." ".$decode['currency']."  for ".$decode['payer']['partyId']." </strong></div>";
     break;
    }
    elseif($decode['reason'] == "APPROVAL_REJECTED")
    {
     echo "<div class='alert alert-warning text-center'>Approval of ".$decode['amount']." ".$decode['currency']."  was <strong>REJECTED by ".$decode['payer']['partyId']."</strong></div>";
     break;
    }
    elseif($decode['reason'] == "INTERNAL_PROCESSING_ERROR")
    {
     echo "<div class='alert alert-warning text-center'>An INTERNAL ERROR OCCURRED for debit of<br> <strong>".$decode['amount']." ".$decode['currency']."  from  ".$decode['payer']['partyId']."<br> PLEASE TRY AGAIN</strong></div>";
     break;
    }
    
    elseif($decode['status'] == "SUCCESSFUL")
    {
        echo "<div class='alert alert-success text-center'><strong>SUCCESSFULLY debited ".$decode['amount']." ".$decode['currency']." from ".$decode['payer']['partyId']."</strong></div>";
        break;
    }
    elseif($decode['status'] == "PENDING")
    {

    }
    else
     {
         
         badRequest();
         break;
     }
}
catch (HttpException $ex)
{
    echo $ex;
}
}

}



function requestPay($access_token, $targetEnv, $pk, $uuidvalue, $dataRequestPayment)
{

$request = new Http_Request2('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay');
$url = $request->getUrl();

$headers = array(
    // Request headers
    'Authorization' => $access_token,
//    'X-Callback-Url' => '',
    'X-Reference-Id' => $uuidvalue,
    'X-Target-Environment' => $targetEnv,
    'Content-Type' => 'application/json',
    'Ocp-Apim-Subscription-Key' => $pk,
);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_POST);
// Request body
$request->setBody($dataRequestPayment);

try
{
    
    $response = $request->send();
    if($response->getReasonPhrase() == "Accepted")
       {
        $decode = json_decode($dataRequestPayment, true);
       // echo "<div class='alert alert-info text-center'>Wait for the client (<strong>".$decode['payer']['partyId'] ."</strong>) to confirm this MTN Momo payment of <strong>".$decode['amount']." </strong> FCFA...</div>";
         }
    else
       {
      
        badRequest();  
       } 
    
    
}
catch (HttpException $ex)
{
    echo $ex;
}

}

function badRequest()
{
    echo "<div class='alert alert-danger text-center'>SOME INVALID PARAMETERS <strong>VERIFY AND TRY AGAIN</strong></div>";
}
function getTokenPay($credentials, $pk){

$request = new Http_Request2('https://sandbox.momodeveloper.mtn.com/collection/token/');
$url = $request->getUrl();

$headers = array(
    // Request headers
    'Authorization' => 'Basic '.$credentials,
    'Ocp-Apim-Subscription-Key' => $pk,
);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_POST);

// Request body
$request->setBody("{body}");

try
{
    
    $response = $request->send();
    $access_token ='Bearer '.json_decode($response->getBody(), true)['access_token'];
    //I have obtained the access token
    return $access_token;
    
}
catch (HttpException $ex)
{
    echo $ex;
}
}
?>