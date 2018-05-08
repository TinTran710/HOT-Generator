<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class NetworkController extends Controller {
	protected $token = '';

    public function makeHttpRequest($request) {
        $client = new Client();
        $response = $client->request('GET', $request, ['headers' => ['X-Auth-Token' => $this->token] ]);
        $objectResponse = json_decode($response->getBody());
        $arrayResponse = (array) $objectResponse;
        return $arrayResponse;
    }

    public function getForm() {
    	return view('network');
    }

    public function postForm(Request $request) {
    	$network = $request->network;
    	$subnet = $request->subnet;

		// Copy base files and paste to new directory		
		$source = '/var/www/html/HOTGenerator/public/scripts/networks/example'; 
		$date = date("H:i:s");
		$path = '/var/www/html/HOTGenerator/public/scripts/networks/'.$date;
		$this->recurse_copy($source, $path);

		// Edit base.yml file - the final Heat template file
		for($i=1; $i<=$network; $i++) {
			$lines = file($path.'/network.yml');
			$replacement_network = 'private_network'.$i;
			$lines = str_replace('private_network', $replacement_network, $lines);
			file_put_contents($path.'/base.yml', implode("", $lines), FILE_APPEND);
			
			for($j=1; $j<=$subnet; $j++) {
				$lines_subnet = file($path.'/subnet.yml');
				$replacement_subnet = 'private_subnet_'.$i.'.'.$j;
				$replacement_ip = '10.10.'.$j.'.0/24';
				$replacement_net = 'private_network'.$i;;
				$lines_subnet = str_replace('private_subnet', $replacement_subnet, $lines_subnet);
				$lines_subnet = str_replace('10.0.0.0/24', $replacement_ip, $lines_subnet);
				$lines_subnet = str_replace('private_network', $replacement_net, $lines_subnet);
				file_put_contents($path.'/base.yml', implode("", $lines_subnet), FILE_APPEND);
			}
		}

		// Edit run.sh file
		$write = file($path.'/run.sh');
		$randomNumber = rand(1, 1000);
		$replacement = 'MyStack_'.$randomNumber;
		$write = str_replace('MyStack', $replacement, $write);
		file_put_contents($path.'/run.sh', implode("", $write));

		// edit permission.sh to give execution permission for run.sh file
		$old_path = getcwd();
		$abs_path = '/var/www/html/HOTGenerator/public/scripts/networks';
		chdir($abs_path);
		$file = file($abs_path.'/permission.sh');
		$file[1] = 'chmod 755 /var/www/html/HOTGenerator/public/scripts/networks/'.$date.'/run.sh';
		file_put_contents($abs_path.'/permission.sh', implode("", $file));
		shell_exec('./permission.sh');
		chdir($old_path);

		// Run bash script
		$old_path = getcwd();
		$new_path = 'scripts/networks/'.$date;
		chdir($new_path);	
		shell_exec('./run.sh');
		chdir($old_path);

		return redirect('network')->with('message', 'Successfully created templates');
    }

	public function recurse_copy($src,$dst) { 
	    $dir = opendir($src); 
		$oldmask = umask(0);
	    @mkdir($dst, 0777);
	    umask($oldmask);
	    while(false !== ( $file = readdir($dir)) ) { 
	        if (( $file != '.' ) && ( $file != '..' )) { 
	            if ( is_dir($src . '/' . $file) ) { 
	                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	            else { 
	                copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	        } 
	    } 
	    closedir($dir); 
	} 
	
}