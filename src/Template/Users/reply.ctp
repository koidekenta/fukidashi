<?php if(!empty($user_info)){ ?>
	<div style="position:relative;">
	<img src="/img/<?= App\Utils\AppUtility::h_im_ch($user_info["header_imageurl"]) ?>" class="header-image">
	<img src="/img/<?= App\Utils\AppUtility::im_ch($user_info["imageurl"]) ?>" class="header-icon">
	</div>
<div id="user_info">
	<div class="user_info_item" style="border-bottom: #04C1FB solid 3px;"><span>投稿</span><a href="/users/view/<?= $user_info->username ?>" class="br"><?= $user_info->fukidashi_num ?></a></div>
	<div class="user_info_item"><span>フォロー</span><a href="/users/follow/<?= $user_info->username ?>" class="br"><?= $user_info->follow_num ?></a></div>
	<div class="user_info_item"><span>フォロワー</span><a href="/users/follower/<?= $user_info->username ?>" class="br"><?= $user_info->follower_num ?></a></div>
	<div class="user_info_item"><span>お気に入り</span><a href="/users/favorite/<?= $user_info->username ?>" class="br"><?= $user_info->favorite_num ?></a></div>
	<?php if($same_check !== "same"){ ?>
		<div class="user_info_item"><label class="cursor:pointer;" id="mute_btn_cover">
			<?php if($mutes !== 0){ ?>
				<i class="fas fa-microphone-slash mysize-big" style="color:red;"><input type="button" class="unmute_btn" style="display:none;" data-username="<?= $user_info->username ?>"></i>
			<?php }else{ ?>
				<i class="fas fa-microphone-slash mysize-big"><input type="button" class="mute_btn" style="display:none;" data-username="<?= $user_info->username ?>"></i>
			<?php } ?>
		</label></div>
		<div class="user_info_item"><div style="padding-left:60px;">
			<?php if($follows !== 0){ ?>
			<div class="btn_cover"><input type="button" value="フォロー中" class="unfollow_follow_bn" data-username="<?= $user_info->username ?>" /></div>
			<?php }else{ ?>
			<div class="btn_cover"><input type="button" value="フォローする" class="follow_bn" data-username="<?= $user_info->username ?>" /></div>
			<?php } ?>
		</div></div>
	<?php } ?>

</div>
<div id="user_prof">
	<?= $this->element('partial_template', ['user_info' => $user_info]) ?>
<div id="retm" style="display:flex;align-items:center;">
	<div class="user_info_item2"></div>
	<div class="user_info_item2"></div>
</div>
</div>
<?php } ?>

<hr>
	<ol class="fukidashi-header-menu">
		<span class="fukidashi-header-menu-item"><a href="/users/view/<?= h($user_info["username"]) ?>">ふきだし</a></span><span class="fukidashi-header-menu-item">返信</span><span class="fukidashi-header-menu-item"><a href="/users/media/<?= h($user_info["username"]) ?>">メディア</a></span>
	</ol>
<?php	if(!empty($results)){	?>
<div>

	<ol id="latest_message">

	</ol>
	<ol id="content">
	<?php foreach($results as $item){ ?>
            <li class="item" data-toggle="modal" data-target="#exampleModalCenter3">
		<div data-created="<?php echo $item["created"]; ?>" data-slug="<?= h($item["slug"]) ?>" data-username="<?= h($item["username"]) ?>" data-id="<?= h($item["id"]) ?>" data-post="<?= h($item["post"]) ?>" data-imageurl="<?php echo App\Utils\AppUtility::im_ch(h($item["imageurl"])); ?>" data-type="kotei">

		<?php if(!empty($item["is_commented"]) and $item["is_commented"] === "ON" and !empty($item["from_comment_username"])){ ?>
			<div style="display:flex;align-items:center;">発信元:<a href="/users/view/<?= $item["from_comment_username"] ?>'"><?= $item["from_comment_username"] ?></a></div>
		<?php } ?>

			<div class="fukidashi-header">
				<div><a href="/users/view/<?= h($item["username"]) ?>"><?= h($item["username"]) ?></a>　</div>
				<div style="margin-left:10px;"><a href="/users/view/<?= h($item["username"]) ?>/<?= h($item["slug"]) ?>"><?php echo App\Utils\AppUtility::time_change($item["created"]); ?></a></div>
				<div style="margin-left:auto;">

	<div style="position:relative;padding:0px;">
	<div class="head" style="position:absolute;right:5px;margin-bottom:10px;cursor:pointer;"><i class="fas fa-angle-down fa-lg"></i></div>
	<div class="body" style="z-index:1000;display:none;overflow:hidden;font-size:13px;width:200px;margin-top:20px;position:absolute;right:10px;background-color:white;border:solid 1px #CBCDD2;padding:5px 0px;border-radius:5px;">
		<?= $this->element('right-top-menu',["item" => $item]) ?>
	</div>
	</div>

</div>
			</div>


			<?php echo $this->Html->image(App\Utils\AppUtility::im_ch($item["imageurl"]),["width" => 40, "height" => 40, "class" => "usericon"]); ?>
			<div class="article-body">
			<?php echo App\Utils\AppUtility::pc(h($item["post"])); ?>

			<?php echo $this->element('diffusion',["item" => $item]); ?>

			<?php
				if(!empty($item["post_img"])){
					echo '<div class="img_container">';
					echo $this->Html->image($item["post_img"], ['class' => 'header-image']);
					echo '</div>';
				}
			?>
			
				<div class="flex">
					<p class="fukidashi-footer"><label class="fukidashi-footer-comment"><i class="far fa-comment mysize"><input type="button" class="comment" style="display:none;"></i></label><span class="comment_num"><?= $item["comment_num"] ?></span></p>
					<p class="fukidashi-footer"><label class="fukidashi-footer-diffusion"><?php
								if(!empty($item["retweets_to_id"])){
									echo '<i class="fas fa-recycle recycle-clicked">';
								}else{
									echo '<i class="fas fa-recycle mysize">';
								}
							?><input type="button" class="diffusion" style="display:none;"  data-toggle="modal" data-target="#exampleModalCenter2"></i></label><span class="diffusion_num"><?= $item["refukidashi_num"] ?></span></p>
					<p class="fukidashi-footer"><label class="fukidashi-footer-favorite"><?php
								if(!empty($item["my_favorite_true"])){
									 echo '<i class="fas fa-heart heart-clicked">';
								}else{
									 echo '<i class="far fa-heart mysize">';
								}
							?><input type="button" class="favorite" style="display:none;"></i></label><span class="favorite_num"><?= $item["favorite_num"] ?></span></p>
					<p class="fukidashi-footer"><label class="fukidashi-footer-message"><i class="far fa-envelope mysize"></i></label></p>
				</div>
			</div>
		</div>
            </li>
	<?php } ?>
	</ol>
</div>
<div id="next" style="display:none;">2</div>
<?php	}else{
		echo "まだ、投稿していません。<br>";
}
?>

