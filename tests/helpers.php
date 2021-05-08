<?php
date_default_timezone_set( "Asia/Jakarta" );
require_once "../vendor/autoload.php";

$domains = array(
	'www.example.com' => 'example.com',
	'example.com' => 'example.com',
	'example.com.br' => 'example.com.br',
	'www.example.com.br' => 'example.com.br',
	'www.example.gov.br' => 'example.gov.br',
	'localhost' => 'localhost',
	'www.localhost' => 'localhost',
	'subdomain.localhost' => 'localhost',
	'www.subdomain.example.com' => 'example.com',
	'subdomain.example.com' => 'example.com',
	'subdomain.example.com.br' => 'example.com.br',
	'www.subdomain.example.com.br' => 'example.com.br',
	'www.subdomain.example.biz.br' => 'example.biz.br',
	'subdomain.example.biz.br' => 'example.biz.br',
	'subdomain.example.net' => 'example.net',
	'www.subdomain.example.net' => 'example.net',
	'www.subdomain.example.co.kr' => 'example.co.kr',
	'subdomain.example.co.kr' => 'example.co.kr',
	'example.co.kr' => 'example.co.kr',
	'example.jobs' => 'example.jobs',
	'www.example.jobs' => 'example.jobs',
	'subdomain.example.jobs' => 'example.jobs',
	'insane.subdomain.example.jobs' => 'example.jobs',
	'insane.subdomain.example.com.br' => 'example.com.br',
	'www.doubleinsane.subdomain.example.com.br' => 'example.com.br',
	'www.subdomain.example.jobs' => 'example.jobs',
	'test' => 'test',
	'www.test' => 'test',
	'subdomain.test' => 'test',
	'www.detran.sp.gov.br' => 'sp.gov.br',
	'www.mp.sp.gov.br' => 'sp.gov.br',
	'ny.library.museum' => 'library.museum',
	'www.ny.library.museum' => 'library.museum',
	'ny.ny.library.museum' => 'library.museum',
	'www.library.museum' => 'library.museum',
	'info.abril.com.br' => 'abril.com.br',
	'127.0.0.1' => '127.0.0.1',
	'::1' => '::1',
);

$failed = 0;
$total = count($domains);

foreach ($domains as $from => $expected)
{
	$from = \Pehape\Helpers\URL::DomainName($from);
	if ($from !== $expected)
	{
		$failed++;
		echo "expected {$from} to be {$expected}" . PHP_EOL;
    continue;
	}
  echo "Done parsing: {$from} to be {$expected}" . PHP_EOL;
}

if ($failed)
{
	echo "{$failed} tests failed out of {$total}" . PHP_EOL;
}
else
{
	echo "Success" . PHP_EOL;
}
