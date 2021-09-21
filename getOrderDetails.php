<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('dbconnect.php');
session_start();

date_default_timezone_set("Asia/Kolkata");
$json = file_get_contents('php://input');
$json_d = json_decode($json, true);
$packageIdsarray = array("31","32","33","34","35","36","37","38","39","40","41","42","43","44");
$package_id = $json_d['pack_id'];
$emp_mobile = $json_d['mobile'];
$coupon_no = $json_d['coupon'];
$contact_required = $json_d['contactrequired'];
$validity_required = $json_d['validityrequired'];
$buy_id = $json_d['buy_id'];
$parameter_name = $json_d['parameter_name'];
$parameter_value = $json_d['parameter_value'];
$usertype = $json_d['usertype'];
$requiredseekers_count = $json_d['requiredseekers_count'];
$personalpostJobId = $json_d['personalpostJobId'];
$additionalPackages=$json_d['additionalPackages'];
$personalpostJobIdstring = implode(",",$personalpostJobId);
if($usertype == "Job Giver"){
  $usertype1 = "JobGiver";
}
else if($usertype == "Job Seeker"){
  $usertype1 = "JobSeeker";
}
$benificiary_count = $json_d['benificiary_count'];
$redeem_flag = $json_d['redeem_flag'];
$redeem_points = $json_d['redeem_points'];
$redeem_points_used = 0;
//$doconcall = $_SESSION['user_mobile'];
$resume_id = $_COOKIE['resumeId'];
$notifyUrl ="https://helper4u.in/api/v1/react/payment/notification.php";
$additionalPackagesId=implode(",", $additionalPackages);

if($redeem_flag != null && $redeem_flag != "" && $redeem_flag != false){
  $returnUrl = "https://helper4u.in/api/v1/react/payment/payment-status.php?redeem_flag=".$redeem_flag."&&redeem_points=".$redeem_points."&&usertype=".$usertype1;
}elseif ($redeem_flag != null && $redeem_flag != "" && $redeem_flag != false && count($additionalPackages)) {
  $returnUrl = "https://helper4u.in/api/v1/react/payment/payment-status.php?redeem_flag=".$redeem_flag."&&redeem_points=".$redeem_points."&&usertype=".$usertype1."&additionalPackagesId=$additionalPackagesId";
}elseif(count($additionalPackages)){
   $returnUrl = "https://helper4u.in/api/v1/react/payment/payment-status.php?additionalPackagesId=$additionalPackagesId";
}
else{
  $returnUrl = "https://helper4u.in/api/v1/react/payment/payment-status.php";
}
/*echo "-".$emp_mobile."-";*/
/*$emp_name = "Bhagyesh";
$emp_email = "bhagyesh@sarvaswa.com";*/
$emp_name = $db_read->get("employer","employer_fullname",["employer_mobile"=>$emp_mobile]);
$emp_email = $db_read->get("employer","employer_email",["employer_mobile"=>$emp_mobile]);
$emp_id = $db_read->get("employer","employer_id",["employer_mobile"=>$emp_mobile]);
if($package_id == 18)
{
  $emp_name = $db_read->get("resume_new_data","name",["id"=>$resume_id]);
  $emp_email = $db_read->get("resume_new_data","email_id",["id"=>$resume_id]);
}
if($emp_name == "")
{

  $emp_name = "User_".$emp_mobile;
}
if($emp_email =="")
{
  $emp_email = "admin@helper4u.in";
}
$currentdate = date('Y_m_d');
$orderId = "ORDS"._.$currentdate._.rand(10000,99999999);
$select = $db_read->select("package_offers","*",["package_id"=>$package_id]);
$coupdata = $db_read->select("coupons","*",["coupon_code"=>$coupon_no]);
//$finalprice =0;
//package details
$package_name = $select[0]['package_name'];
$duration = ucwords($select[0]['duration']);
$price = $select[0]['price'];
$contact = $select[0]['contact'];
$gst_percentage = $select[0]['gst_percentage'];
$order_note = "".$orderId." ".$package_name;
//coupon details
$discount_type = $coupdata[0]['discount_type'];
$discount_amt = $coupdata[0]['discount_amt'];
if($emp_mobile == 8169458686 || $emp_mobile == 9664912123)
{
    $price = 1;
}
//coupon part started
if($discount_type != "" && $discount_type != null && $discount_type != "null")
{
  if($discount_type == "percentage")
  {
    $price1 = $price*($discount_amt/100);
    $price1 = round($price1,2);
    $finalprice = $price-$price1;
    $finalprice = round($finalprice,2);
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
    $GST2 = $price*($gst_percentage/100);
    $total = $price+$GST2;
    $savedmoney = $total-$order_amount;
    $savedmoney = round($savedmoney,2);
  }
  else if($discount_type == "amount")
  {
    $finalprice = $price-$discount_amt;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
    $GST2 = $price*($gst_percentage/100);
    $total = $price+$GST2;
    $savedmoney = $total-$order_amount;
    $savedmoney = round($savedmoney,2);
  }
}
else if($package_id == 23)
{
  if($requiredseekers_count > 1)
  {
    $finalprice = $price*$requiredseekers_count;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
  }
  else
  {
    $finalprice = $price;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
  }
}
else if($package_id == 29 || $package_id == 46|| $package_id == 45){
  if($contact_required == "" || $contact_required < 2 || $contact_required == null)
  {
    $finalprice = $price;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
  }
  else{
    $finalprice = $price*$contact_required;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
  }
}
else if($package_id == 30){
  $expiry_date1 = $db_read->get("buy_package", "package_expiry", ["buy_package_id" => $buy_id]);
  $expiry_date2 = date('d-m-Y', strtotime($expiry_date1));
  if($validity_required == "" || $validity_required < 2 || $validity_required == null)
  {
    $finalprice = $price;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
    $expiry_date3 = date('d-m-Y', strtotime("$expiry_date2".'+'."1".' days'));
  }
  else{
    $finalprice = $price*$validity_required;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
    $expiry_date3 = date('d-m-Y', strtotime("$expiry_date2".'+'."$validity_required".' days'));
  }
}
else if($package_id == 31 || $package_id == 32 || $package_id == 33 || $package_id == 34){
$finalprice = $price;
$order_amount = $price;
}
else if($package_id > 34 && $package_id < 45){

  $finalprice = $price*$benificiary_count;
  $order_amount = $price*$benificiary_count;
  }
else{
  if(($package_id == 25 || $package_id == 48 || $package_id == 49) && count($additionalPackages)){

   $calPrice=0;
    foreach ($additionalPackages as $packageId) {
      $sql=$db_read->query("SELECT * FROM package_offers where package_id=$packageId");

      foreach ($sql as $data) {
        if(!$calPrice){
          $calPrice=$data['price'];
          continue;
        }
        $calPrice=$calPrice+$data['price'];
      }
    }
    $finalprice = $calPrice+$price;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
  }else{
    $finalprice = $price;
    $GST_AMOUNT = $finalprice*($gst_percentage/100);
    $GST_AMOUNT = round($GST_AMOUNT,2);
    $order_amount = $GST_AMOUNT+$finalprice;
  }
}

if($package_id == 30 || $package_id == 47){
  $payment_info =  $db_write->insert("payment_initiate",[
    "package_id" => $package_id,
    "employer_mobile" => $emp_mobile,
    "order_id" => $orderId,
    "parameter" => "validityId",
    "value" => $buy_id
  ]);
}
else if($package_id > 30){
  $payment_info =  $db_write->insert("payment_initiate",[
    "package_id" => $package_id,
    "employer_mobile" => $emp_mobile,
    "order_id" => $orderId,
    "parameter" => $parameter_name,
    "value" => $parameter_value
  ]);
}
else if($package_id == 23){
  $payment_info =  $db_write->insert("payment_initiate",[
    "package_id" => $package_id,
    "employer_mobile" => $emp_mobile,
    "order_id" => $orderId,
    "parameter" => "personalpostJobId",
    "value" => $personalpostJobIdstring
  ]);
}
else if($package_id == 8 || $package_id == 9 || $package_id == 10){
  $payment_info =  $db_write->insert("payment_initiate",[
    "package_id" => $package_id,
    "employer_mobile" => $emp_mobile,
    "order_id" => $orderId,
    "parameter" => "usertype",
    "value" => $usertype
  ]);
}
else{
  $payment_info =  $db_write->insert("payment_initiate",[
    "package_id" => $package_id,
    "employer_mobile" => $emp_mobile,
    "order_id" => $orderId,
  ]);
}
if($redeem_flag == true || $redeem_flag == "true"){
  if($order_amount > $redeem_points){
    $redeem_points_used = $redeem_points;
    $order_amount = $order_amount-$redeem_points;
    $order_amount = round($order_amount,2);
  }
  else{
    $redeem_points_used = $order_amount;
    $order_amount = 0;
  }
}


$secretKey = "66d4dddb34da5a9ab3611e2bb6118c9f4c87b34e";

$additionalPackagesId=implode(",", $additionalPackages);

  $postData = array(
  "appId" => "26820ac211d9183b73ef81762862",
  "orderId" => $orderId,
  "orderAmount" => $order_amount,
  "orderCurrency" => "INR",
  "orderNote" => $order_note,
  "customerName" => $emp_name,
  "customerPhone" => $emp_mobile,
  "customerEmail" => $emp_email,
  "returnUrl" => $returnUrl,
  "notifyUrl" => $notifyUrl,
);

 ksort($postData);
 $signatureData = "";
 foreach ($postData as $key => $value){
      $signatureData .= $key.$value;
 }
 $signature = hash_hmac('sha256', $signatureData, $secretKey,true);
 $signature = base64_encode($signature);



$order_details = array("appId" => "26820ac211d9183b73ef81762862" ,"orderId" => $orderId,"orderAmount" => $order_amount, "orderCurrency" => "INR", "orderNote" => $order_note,"customerName" => $emp_name,"customerPhone" => $emp_mobile,"customerEmail" => $emp_email,"returnUrl" => $returnUrl, "notifyUrl" => $notifyUrl,"signature" => $signature, "packageName" => $package_name,"price"=>$finalprice,"gst"=>$GST_AMOUNT,"finalPrice" => $order_amount,"duration"=>$duration, "savedmoney"=>$savedmoney, "expiry_date1"=>$expiry_date3, "doconcall" => $doconcall, "redeem_points_used"=>$redeem_points_used, "packageIdsarray"=>$packageIdsarray, "redeem_flag"=>$redeem_flag, "additionalPackagesId"=>$additionalPackagesId);

echo json_encode($order_details);
?>
