
<hr>
<?php echo '<div style="width:100%;color:#ffffff;background-color:#04c1fb;"><h1>'.h($_GET["q"]).'</h1></div>'; ?>
<?php if(!empty($trend)){ ?>
	<div style="border-radius:5px;border:1px solid #eee;margin:10px;padding:10px;">
		<h3>最近のトレンド</h3>
		<?php foreach($trend as $item){ ?>
				<a href="/posts/search?q=<?= $item["search_keyword"] ?>">#<?= $item["search_keyword"] ?></a> 
		<?php } ?>
	</div>
<?php } ?>
<?php	if(!empty($results)){	?>
<div>

	<ol id="latest_message">

	</ol>
	<ol id="content">
	<?php foreach($results as $item){ ?>
            <li class="item" data-toggle="modal" data-target="#exampleModalCenter3">
		<div data-created="<?php echo $item["created"]; ?>" data-slug="<?= h($item["slug"]) ?>" data-username="<?= h($item["username"]) ?>" data-id="<?= h($item["id"]) ?>" data-post="<?= h($item["post"]) ?>" data-imageurl="<?php echo App\Utils\AppUtility::im_ch(h($item["imageurl"])); ?>" data-type="kotei">

			<div class="fukidashi-header">
				<div><a href="/users/view/<?= h($item["username"]) ?>"><?= h($item["username"]) ?></a>　</div>
				<div><a href="/users/view/<?= h($item["username"]) ?>/<?= h($item["slug"]) ?>"><?php echo App\Utils\AppUtility::time_change($item["created"]); ?></a></div>
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
				if($item["post_img"]){
					echo '<div class="img_container">';
					echo $this->Html->image($item["post_img"], ['class' => 'header-image']);
					echo '</div>';
				}
			?>
			
				<div class="flex">
					<p class="fukidashi-footer"><label class="fukidashi-footer-comment"><i class="far fa-comment mysize"><input type="button" class="comment" style="display:none;"></i></label><span class="comment_num"><?= $item["comment_num"] ?></span></p>
					<p class="fukidashi-footer"><label class="fukidashi-footer-diffusion"><?php
								if($item["retweets_to_id"] !== null){
									echo '<i class="fas fa-recycle recycle-clicked">';
								}else{
									echo '<i class="fas fa-recycle mysize">';
								}
							?><input type="button" class="diffusion" style="display:none;"  data-toggle="modal" data-target="#exampleModalCenter2"></i></label><span class="diffusion_num"><?= $item["refukidashi_num"] ?></span></p>
					<p class="fukidashi-footer"><label class="fukidashi-footer-favorite"><?php
								if($item["my_favorite_true"] != null){
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
		echo "検索結果はありません。<br>";
}
?>

