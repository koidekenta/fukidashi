<?php 
	echo '<div style="font-weight:bold;font-size:24px;">'.$user_info["username"].'</div>';

	if(!empty($user_info["prof"])){
		echo h($user_info["prof"]);
	}
	// ここにフォローボタンやミュートボタンを入れる

?>
