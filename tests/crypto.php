<?php
date_default_timezone_set( "Asia/Jakarta" );
require_once "../vendor/autoload.php";

$crypto = new \Pehape\Services\CryptoService();
echo $crypto->SetPassword( 'KEY_HERE' )->EncryptFile( 'SOURCE_FILE', 'DESTINATION_FILE', 'SALT' ) . PHP_EOL;
