<?php if(!empty($user_info)){ ?>
	<div style="position:relative;">
	<img src="/img/<?= App\Utils\AppUtility::h_im_ch($user_info["header_imageurl"]) ?>" class="header-image" style="width:100%;height:350px;">
	<img src="/img/<?= App\Utils\AppUtility::im_ch($user_info["imageurl"]) ?>" class="header-icon">
	</div>
<div id="user_info">
	<div class="user_info_item"><span>ツイート</span><a href="/users/view/<?= $user_info->username ?>" class="br"><?= $user_info->fukidashi_num ?></a></div>
	<div class="user_info_item"><span>フォロー</span><a href="/users/follow/<?= $user_info->username ?>" class="br"><?= $user_info->follow_num ?></a></div>
	<div class="user_info_item" style="border-bottom: #04C1FB solid 3px;"><span>フォロワー</span><a href="/users/follower/<?= $user_info->username ?>" class="br"><?= $user_info->follower_num ?></a></div>
	<div class="user_info_item"><span>お気に入り</span><a href="/users/favorite/<?= $user_info->username ?>" class="br"><?= $user_info->favorite_num ?></a></div>
	<?php if($same_check !== "same"){ ?>
		<div class="user_info_item"><label class="cursor:pointer;" id="mute_btn_cover">
			<?php if($mutes !== 0){ ?>
				<i class="fas fa-microphone-slash mysize-big" style="color:red;"><input type="button" class="unmute_btn" style="display:none;" data-username="<?= $user_info->username ?>"></i>
			<?php }else{ ?>
				<i class="fas fa-microphone-slash mysize-big"><input type="button" class="mute_btn" style="display:none;" data-username="<?= $user_info->username ?>"></i>
			<?php } ?>
		</label></div>
		<div class="user_info_item"><div style="padding-left:60px;"><div id="btn_cover">
			<?php if($follows !== 0){ ?>
			<input type="button" value="フォロー中" class="unfollow_follow_bn" data-username="<?= $user_info->username ?>" /></div>
			<?php }else{ ?>
			<input type="button" value="フォローする" class="follow_bn" data-username="<?= $user_info->username ?>" /></div>
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

<?php if(!empty($results)){ ?>

<?php $count = 1; ?>
<?php foreach($results as $item){ ?>
<?php if($count % 2 !== 0){ ?>
	<div class="card-block">
<?php } ?>

<div class="card card-custom">
  <img class="card-img-top header-image" src="/img/<?= App\Utils\AppUtility::h_im_ch($item["header_imageurl"]) ?>" style="height:200px;">
  <div class="card-body">
   <?php echo $this->Html->image(App\Utils\AppUtility::im_ch($item["imageurl"]),["width" => 40, "height" => 40, "class" => "usericon"]); ?>
   <div class="card-title" style="margin-left:40px;"><a href="/users/view/<?= $item["username"] ?>"><?= $item["username"] ?></a></div>
    <p class="card-text" style="margin-top:20px;"><?= $item["prof"] ?></p>
  </div>
</div>

<?php if($count % 2 === 0 or count($results) === $count){ ?>
	</div>
<?php } ?>
<?php $count++; ?>
<?php } ?>


<?php }else{ ?>
	まだ、誰にもフォローされていません。
<?php } ?>
