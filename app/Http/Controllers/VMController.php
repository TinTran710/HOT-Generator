<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class VMController extends Controller
{
	protected $token = 'gAAAAABa6FEQJdfooh1taa9ofehs8nFSg1wmuSLd4H5JFIK6N2O4LFBFBzr-fWZvqn_Utt1M4JGzY_hpV53mmwPtU0Q3PRZvl7YXvpD_eUDS9QtT461GkqnRGeB1Pro5wkEvjtWx9GuQF1snRP7TFcdK1JqXhcIdtgUzgOX49-tuoluHdxd7YN0';
    
	public function index() {
		return view('index');
	}

	public function home() {
		return view('home');
	}

	public function getFlavorInfo() {
		$request = 'http://controller:8774/v2.1/flavors';
		$response = $this->makeHttpRequest($request);
		$flavorList = array();
		foreach($response['flavors'] as $temp) {
			array_push($flavorList, $temp->name);
		}
		return $flavorList;
	}

	public function getImageInfo() {
		$request = 'http://controller:9292/v2/images';
		$response = $this->makeHttpRequest($request);		
		$imageList = array();
		foreach($response['images'] as $temp) {
			array_push($imageList, $temp->name);
		}
		return $imageList;
	}

	public function getKeyNameInfo() {
		$request = 'http://controller:8774/v2.1/os-keypairs';
		$response = $this->makeHttpRequest($request);		
		$keyNameList = array();
		foreach($response['keypairs'] as $temp) {
			array_push($keyNameList, $temp->keypair->name);
		}
		return $keyNameList;
	}

	public function getPublicNetInfo() {
		$request = 'http://controller:9696/v2.0/networks';
		$response = $this->makeHttpRequest($request);
		$publicNetList = array();
		foreach($response['networks'] as $temp) {
			if($temp->{'router:external'} == true) {
				array_push($publicNetList, $temp->name);
			}
		}
		return $publicNetList;
	}

	public function getPrivateSubnetInfo() {
		$request = 'http://controller:9696/v2.0/subnets';
		$response = $this->makeHttpRequest($request);
		$privateSubnetList = array();
		$i = 0;
		foreach($response['subnets'] as $temp) {
			$privateSubnetList[$i]['name'] = $temp->name;
			$privateSubnetList[$i]['parent'] = $temp->network_id;
			$i++;
		}
		return $privateSubnetList;
	}

    public function makeHttpRequest($request) {
        $client = new Client();
        $response = $client->request('GET', $request, ['headers' => ['X-Auth-Token' => $this->token] ]);
        $objectResponse = json_decode($response->getBody());
        $arrayResponse = (array) $objectResponse;
        return $arrayResponse;
    }

	public function getForm() {
		$publicNetList = $this->getPublicNetInfo();
		$privateSubnetList = $this->getPrivateSubnetInfo();
		$flavorList = $this->getFlavorInfo();
		$imageList = $this->getImageInfo();
		$keyNameList = $this->getKeyNameInfo();
		return view('form')
		->with('flavorList', $flavorList)
		->with('imageList', $imageList)
		->with('publicNetList', $publicNetList)
		->with('privateSubnetList', $privateSubnetList)
		->with('keyNameList', $keyNameList);
	}

	public function postForm(Request $request) {
		$number = $request->number;
		$image = $request->image;
		$flavor = $request->flavor;
		$keyName = $request->keyName;
		$publicNet = $request->publicNet;
		$privateSubnet = explode('|', $request->privateSubnet);

		// Copy base files and paste new directory		
		$source = '/var/www/html/OpenStack/public/scripts/instances/example'; 
		$date = date("H:i:s");
		$path = '/var/www/html/OpenStack/public/scripts/instances/'.$date;
		$this->recurse_copy($source, $path);

		// Edit parameter.yml file
		$lines = file($path.'/parameter.yml');
		$lines[1] = '  image: '.$image."\r\n";
		$lines[2] = '  flavor: '.$flavor."\r\n";
		$lines[3] = '  key_name: '.$keyName."\r\n";
		$lines[4] = '  private_subnet: '.$privateSubnet[0]."\r\n";
		$lines[5] = '  private_net: '.$privateSubnet[1]."\r\n";
		$lines[6] = '  public_net: '.$publicNet."\r\n";
		file_put_contents($path.'/parameter.yml', implode("", $lines));

		// Edit base.yml file - the final Heat template file
		for($i=1; $i<=$number; $i++) {
			$lines = file($path.'/vm.yml');
			$replacement = 'instance'.$i;
			$lines = str_replace('instance', $replacement, $lines);
			file_put_contents($path.'/base.yml', implode("", $lines), FILE_APPEND);
		}

		// Edit run.sh file
		$write = file($path.'/run.sh');
		$randomNumber = rand(1, 1000);
		$replacement = 'MyStack_'.$randomNumber;
		$write = str_replace('MyStack', $replacement, $write);
		file_put_contents($path.'/run.sh', implode("", $write));

		// edit permission.sh to give execution permission for run.sh file
		$old_path = getcwd();
		$abs_path = '/var/www/html/OpenStack/public/scripts/instances';
		chdir($abs_path);
		$file = file($abs_path.'/permission.sh');
		$file[1] = 'chmod 755 /var/www/html/OpenStack/public/scripts/instances/'.$date.'/run.sh';
		file_put_contents($abs_path.'/permission.sh', implode("", $file));
		shell_exec('./permission.sh');
		chdir($old_path);

		// Run bash script
		$old_path = getcwd();
		$new_path = 'scripts/instances/'.$date;
		chdir($new_path);	
		shell_exec('./run.sh');
		chdir($old_path);

		return redirect('form')->with('message', 'Successfully created templates');
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
