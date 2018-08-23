<hr>
<?php if($results){ ?>
<ul>
	<?php foreach($results as $item){ ?>
		<?php if($item["flug"] === "ON" and $results[0]["flug"] !== "ON"){ ?>
			<li class="item" style="border-bottom:1px solid yellow;">
		<?php }else{ ?>
			<li class="item">
		<?php } ?>
			<?php echo '<img src="/img/'.App\Utils\AppUtility::im_ch($item["imageurl"]).'" width="40" height="40">'.App\Utils\AppUtility::alert_mes($item["action"],$item["post_slug"],$item["who"],$this->request->session()->read('Auth.User.username')).":<b>".$item["created"]."</b>"; ?>
			<?php if(!empty($item["post"])){ ?>
			<div style="border:solid 1px #eee;border-radius:5px;padding:5px;width:50%;">
				<?php echo App\Utils\AppUtility::pc(h($item["post"])); ?>
			</div>
			<?php } ?>
		</li>
	<?php } ?>
</ul>
<?php }else{ ?>
	まだ、アラートはありません。
<?php } ?>