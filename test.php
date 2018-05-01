<?php
	
	// str_replace("2013","2018",$lines[0]);
	// echo $lines[0].'<br>';
	// $lines[0] = str_replace('2013', '2018', $lines[0]);
	// echo $lines[0];

	/* Read and write from one file to another */

	// for($i=0; $i<10; $i++) {
	// 	file_put_contents('test.yml', file_get_contents('template.yml'), FILE_APPEND);
	// }
	// $lines = file('test.yml', FILE_IGNORE_NEW_LINES);
	// echo '<pre>';print_r($lines);echo '</pre>';


	/* Active text */

	$lines = file('part.yml', FILE_IGNORE_NEW_LINES);
	echo '<pre>'; print_r($lines); echo '</pre>';

	echo $lines[3]; echo '<br>';
	
	for($i=1; $i<=10; $i++) {
		$item = (string)$i;		
		$ip = '192.168.1.'.$item;
		
		if(strpos($lines[3], 'instance_type') !== false) {
			$lines[3] = str_replace('instance_type', $ip, $lines[3]);
		}

		preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $lines[3], $ip_matches);

		$lines[3] = str_replace($ip_matches[0], $ip, $lines[3]);

		echo '<pre>'; print_r($lines[3]); echo '</pre>';
	}

	$x = '192.168.1.3 asdfasdf 1e	1 sadf1 192.168.1.12';
	echo $x;
	preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $x, $ip_matches);
	echo '<br>'; var_dump($ip_matches);
	echo $ip_matches[0][1];


$x = shell_exec('/var/www/scripts/test');
print_r($x);