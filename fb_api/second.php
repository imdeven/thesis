<?php

require_once __DIR__ . '/vendor/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '740839506045540',
  'app_secret' => 'c186a8910dfd8439e59e3a4e8eb4392d',
  'default_graph_version' => 'v2.2',
  ]);

$fb->setDefaultAccessToken('CAAOTshScBIABABD5QBdpfu4qPmjLjhZA4nZBVGupZAsXhZAe3awRjUHMSNhVV0BtSqzHmHpYCr5lsfAslqj7Ru7az3y6hpzCPA68jgA8n2lKTrmCxTZBCTvE1ixSdXJt5WKZAp7IbXTE03Willn0TeiZBrzMsr5kRl8uZCLGu7NDxlooRl2e9sKJHCMZCYhuWJkzy6ZB9sTVflF1fySghkIv9R');

$group_id = "229797827079378";//"88096246863033"; //229797827079378

# open your file with append, read, write perms
# (be sure to check your file perms)
$fp=fopen('seconddata.txt','a+');
//$success_count = 0;$last_success = 0;$a=array("1");
$member_array = array();
while(1)

{
	if($group_id > "229797827079378" )break;	

	$query = '/'.$group_id.'/?fields=id,members.limit(5000)';
	
	try {
		
		$response = $fb->get($query);
		//var_dump($response);
		//$feedEdge = $response->getGraphNode();
		//var_dump($feedEdge);
		$memberList = $response->getDecodedBody();
		#start buffering (all activity to the buffer)
		ob_start() ;

		foreach ($memberList["members"]["data"] as $member) {
					array_push($member_array, $member["id"]);
					
		} 
		foreach ($member_array as $member_id) {
			
			$query = '/'.$member_id.'/?fields=groups';
			$response = $fb->get($query);
	
			$resArray = $response->getDecodedBody();
			if(sizeof($resArray)>1) var_dump($resArray);
			else var_dump($query);
		}
		/*while(($nextEdge=$fb->next($feedEdge)))
		{	
			
			$i = $i + 1;

			$memberList = $response->getDecodedBody();
			foreach ($memberList["members"]["data"] as $member) {
					array_push($member_array, $member["id"]);
					
			}
			 
			if($i==10) break;
			$feedEdge = $nextEdge;

		}*/
		//var_dump($member_array);

		# dump buffered $classvar to $outStringVar
		$outStringVar = ob_get_contents() ;
 
		# write output to file 
		fwrite($fp, $outStringVar );	
		# clean the buffer & stop buffering output
		ob_end_clean() ;


		$group_id = $group_id +1;
		//$success_count = $success_count + 1;$last_success=$group_id;array_push($a,$group_id);
	}
	catch(Facebook\Exceptions\FacebookResponseException $e) {
  		// When Graph returns an error
  		echo 'Graph returned an error: ' . $e->getMessage();echo "\n";
		//echo "ERROR 1\n";
		$group_id = $group_id +1;
  		continue;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
  		// When validation fails or other local issues

  		echo 'Facebook SDK returned an error: ' . $e->getMessage();echo "\n";
		//echo "ERROR 2\n";
		$group_id = $group_id +1;
  		continue;
	}	

}
//var_dump($a);
fclose($fp);
 

?>
