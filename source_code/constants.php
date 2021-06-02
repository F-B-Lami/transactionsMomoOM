<?php
$pk = "6af6719970f0488c8b438e21a2b7949e";
$targetEnv = "sandbox";
$currency = "EUR";
$MSISDN = "MSISDN";
$date = getdate();
$transactionId = $date['year'].$date['mon'].$date['mday'].$date['hours'].$date['minutes'].$date['seconds'];
$payMessage = "Confirm Purchase";
$payeeNote = "Please enter your pin to confirm the purchase of your product";
?>