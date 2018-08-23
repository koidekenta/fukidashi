<?php if(!empty($users)){ ?>
<div style="border:1px solid #E3E0DE;border-radius:5px;margin-top:70px;margin-left:5px;margin-right:5px;">
    <h3 style="text-align:center;padding:10px;">ユーザー一覧</h3>
        <ul>
            <?php foreach ($users as $user): ?>
            <li style="border-top:1px solid #E3E0DE;padding-top:10px;padding-bottom:10px;display:flex;align-items:center;">
		<?php
			if(!empty($user["imageurl"])){
				echo $this->Html->image($user["imageurl"],['width'=>'40', 'height'=>'40']);
			}else{
				echo $this->Html->image('default_prof.png',['width'=>'40', 'height'=>'40']);
			}
		?>
		<a href="/users/view/<?= h($user["username"]) ?>"><?php echo h($user["username"]); ?></a>
		<?php if(!empty($user["followed_check"]) and h($user["username"]) !== $this->request->session()->read('Auth.User.id')){ ?>
			<div class="btn_cover" style="margin-left:15px;"><input type="button" value="フォロー中" class="follow_bn" data-flug="true" data-username="<?= h($user["username"]) ?>" /></div>	
		<?php }else if(h($user["username"]) === $this->request->session()->read('Auth.User.username')){ ?>

		<?php }else{ ?>
			<div class="btn_cover" style="margin-left:15px;"><input type="button" value="フォローする" class="follow_bn" data-flug="false" data-username="<?= h($user["username"]) ?>" /></div>
		<?php } ?>
		</li>
            <?php endforeach; ?>
	</ul>
</div>
<div id="next" style="display:none;">2</div>
<?php }else{ ?>

<?php } ?>