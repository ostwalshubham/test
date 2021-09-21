<?php

include 'medoo.php';

$db_write= new medoo([
	'database_type' => 'mysql',
	'database_name' => 'helper4unwdco_helper',
	'server' => 'database-hforu-instance-ap-south-1a.c9pkufqri1dl.ap-south-1.rds.amazonaws.com',
	'password' => 'VxPv53QdPTZDgQAS',//   UXU8a[.Hq6HA // VxPv53QdPTZDgQAS -Changed on 20/12/1018
	'username' => 'helper4u_helper',
	'charset' => 'utf8'
]);
$db_read= new medoo([
	'database_type' => 'mysql',
	'database_name' => 'helper4unwdco_helper',
	'server' => 'database-hforu-instance.c9pkufqri1dl.ap-south-1.rds.amazonaws.com',
	'password' => 'VxPv53QdPTZDgQAS',//   UXU8a[.Hq6HA // VxPv53QdPTZDgQAS -Changed on 20/12/1018
	'username' => 'helper4u_helper',
	'charset' => 'utf8'
]);

$db= new medoo([
	'database_type' => 'mysql',
	'database_name' => 'helper4unwdco_helper',
	'server' => 'database-hforu.cluster-c9pkufqri1dl.ap-south-1.rds.amazonaws.com',
	'password' => 'VxPv53QdPTZDgQAS',//   UXU8a[.Hq6HA // VxPv53QdPTZDgQAS -Changed on 20/12/1018
	'username' => 'helper4u_helper',
	'charset' => 'utf8'
]);
/*
$db= new medoo([
	'database_type' => 'mysql',
	'database_name' => 'helper4unwdco_helper',
	'server' => 'localhost',
	'password' => 'VitjVdvobn55P7aX',//   UXU8a[.Hq6HA // VxPv53QdPTZDgQAS -Changed on 20/12/1018
	'username' => 'helper4unwdco_he',
	'charset' => 'utf8'
]);*/

?>
