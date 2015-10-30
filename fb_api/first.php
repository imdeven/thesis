<?php

require_once __DIR__ . '/vendor/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '740839506045540',
  'app_secret' => 'c186a8910dfd8439e59e3a4e8eb4392d',
  'default_graph_version' => 'v2.2',
  ]);

$fb->setDefaultAccessToken('CAAKhyiOX6mQBAP2ITw2lvlX37OPZCBIuZAR2QYmYK2CoVRpZB3evMkI2GvtTVkmjpabB6sTaBzdvVxHy3qa0FmWUCnYdmocNXkWAA5lqGrqcKhZANrQ1KGAPDdBuiQFrpZBkBgTaWDTKqqYnoznrp75xBvhwOZA6q8GC3NnhH1H4AuFnvFOiroq0esaj9jbmDJKrwVjC5AGO9os2bZBJvbZB');

$group_list = array("48171250774");
//while(1)
foreach ($group_list as $group_id){

	$fp=fopen('../groupData/' + $group_id + '_data.txt','a+');

	$attachment_query = '/'.$group_id.'/feed?fields=message,attachments,comments,likes'; 
		
	try {

		$response = $fb->get($attachment_query);
		$feedEdge = $response->getGraphEdge();
		echo "buffer starts";
		
		ob_start(); #start buffering (all activity to the buffer)
		
		$i = 1;
		$count = 0;

		foreach ($feedEdge as $status) {

					//echo "something";
					//var_dump($status->asArray());

					$status = $status->asArray();
					if (!array_key_exists('message', $status)) continue;	//if no message/description we don't need this post
					if (!array_key_exists('attachments', $status)) continue; // if no attachments just message, no need
					if (array_key_exists('subattachments',$status['attachments'][0])) continue; //if subattachments, no need as only target on single image posts
					if (!array_key_exists('media',$status['attachments'][0])) continue; //if attachment not available, no need of post
					if (!array_key_exists('image',$status['attachments'][0]['media'])) continue; // if not image attachment, no need of post


					$post_id = $status['id'];
					$post_message = $status['message'];
					$pic_url = $status['attachments'][0]['media']['image']['src'];
					$likes = 0; $comments = 0;

					if (array_key_exists('likes',$status)) {$likes = sizeof($status['likes']);}
					if (array_key_exists('comments',$status)) {$comments = sizeof($status['comments']);}
					
					$result = array('id' => $post_id, 'message' => $post_message, 'likes' => $likes, 'comments' => $comments, 'url' => $pic_url);

					$count = $count + 1;

					echo json_encode($result,JSON_UNESCAPED_SLASHES);
					echo "\n";
		} 
		
		while(($nextFeed=$fb->next($feedEdge)))
		{	
			
			//$i = $i + 1;
			foreach ($nextFeed as $status) {
	
					$status = $status->asArray();
					if (!array_key_exists('message', $status)) continue;	//if no message/description we don't need this post
					if (!array_key_exists('attachments', $status)) continue; // if no attachments just message, no need
					if (array_key_exists('subattachments',$status['attachments'][0])) continue; //if subattachments, no need as only target on single image posts
					if (!array_key_exists('media',$status['attachments'][0])) continue; //if attachment not available, no need of post
					if (!array_key_exists('image',$status['attachments'][0]['media'])) continue; // if not image attachment, no need of post

					$post_id = $status['id'];
					$post_message = $status['message'];
					$pic_url = $status['attachments'][0]['media']['image']['src'];
					$likes = 0; $comments = 0;

					if (array_key_exists('likes',$status)) {$likes = sizeof($status['likes']);}
					if (array_key_exists('comments',$status)) {$comments = sizeof($status['comments']);}
					
					$result = array('id' => $post_id, 'message' => $post_message, 'likes' => $likes, 'comments' => $comments, 'url' => $pic_url);

					$count = $count + 1;

					echo json_encode($result,JSON_UNESCAPED_SLASHES);

					echo "\n";
			} 
			
			if($count >=1000) break;
			$feedEdge = $nextFeed;

		}
		
		$outStringVar = ob_get_contents() ; # dump buffered $classvar to $outStringVar		
		fwrite($fp, $outStringVar ); # write output to file 		
		ob_end_clean(); # clean the buffer & stop buffering output
		fclose($fp);

	}

	catch(Facebook\Exceptions\FacebookResponseException $e) {
  		// When Graph returns an error
  		echo 'Graph returned an error: ' . $e->getMessage();echo "\n";
  		continue;

	} catch(Facebook\Exceptions\FacebookSDKException $e) {
  		// When validation fails or other local issues
  		echo 'Facebook SDK returned an error: ' . $e->getMessage();echo "\n";
		$group_id = $group_id +1;
  		continue;
	}	

} 

?>
