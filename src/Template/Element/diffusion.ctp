<?php if(!empty($item["is_retweeted"]) and $item["is_retweeted"] === "ON"){

	if(!empty($item["retweet_posts_post"])){
		echo '<div style="width:70%;border:solid 1px #CBCDD2;padding:5px;border-radius:5px;">';
		echo '<div style="display:flex;">';
		echo '<div><a href="/users/view/'.$item["retweet_posts_username"].'">'.$item["retweet_posts_username"].'</a></div>';
		echo '<div style="margin-left:10px;"><a href="/users/view/'.$item["retweet_posts_username"].'/'.$item["retweet_posts_slug"].'">'.App\Utils\AppUtility::time_change($item["retweet_posts_created"]).'</a></div>';
		echo '</div>';
		echo App\Utils\AppUtility::pc(h($item["retweet_posts_post"]));
		echo '</div>';
	}else{
		echo '<div style="width:70%;border:solid 1px #ccd4dc;text-align:center;padding:5px;border-radius:5px;background-color:#f5f8fa;">';
		echo 'このふきだしはありません';
		echo '</div>';
	}
} ?>
