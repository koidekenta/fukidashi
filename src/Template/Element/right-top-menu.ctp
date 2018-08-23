<ul class="toggle-item">
	<?php if(h($item["username"]) !== $this->request->session()->read('Auth.User.username')){ ?>
		<li class="toggle-item-list"><a class="mute"><?= h($item["username"]) ?>さんをミュートする</a></li>
	<?php }else{ ?>
		<li class="toggle-item-list"><a class="kotei">このふきだしを固定する</a></li>
		<li class="toggle-item-list"><a class="fukidashi_delete">このふきだしを削除する</a></li>
	<?php } ?>
</ul>