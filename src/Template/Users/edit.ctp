<div style="position:relative;border:1px solid #eee;padding:10px 20px;border-radius:5px;width:90%;margin-top:70px;margin-left:auto;margin-right:auto;background-color:white;">
    <?= $this->Form->create($user,['enctype' => 'multipart/form-data']) ?>
    <fieldset>
        <legend><?= __('プロフィール編集') ?></legend>
        <?php
            echo $this->Form->control('email',['label'=>'Eメール','style' => 'border-radius:5px;']);
            echo $this->Form->control('username',['label'=>'ユーザー名','style' => 'border-radius:5px;']);
            echo $this->Form->control('prof',['label'=>'一言プロフィール','rows' => 2,'style' => 'border-radius:5px;']);
            echo $this->Form->control('imageurl',['label'=>'プロフィール画像','type'=>'file']);
	    echo "現在のプロフィール画像　(投稿すると置き換わります)<br>";
	if(!empty($user->imageurl)){
		echo $this->Html->image($user->imageurl,['width' => 40, 'height' => 40]);
		echo '<button id="icon-image-none-btn" class="btn btn-primary">現在のアイコン画像を消す</button>';
		echo '<input type="hidden" value="false" name="icon-image-none" id="icon-image-none">';
	}else{
	    echo "プロフィール画像はありません";
	}
	echo '<div style="margin-top:15px;">';
	echo $this->Form->control('header_imageurl',['label' => 'ヘッダー画像','type'=>'file']);
	echo "<p>現在のヘッダー画像　(投稿すると置き換わります)</p>";
	if(!empty($user->header_imageurl)){
		echo $this->Html->image($user->header_imageurl,['width' => 250]);
		echo '<button id="header-image-none-btn" class="btn btn-primary">現在のヘッダー画像を消す</button>';
		echo '<input type="hidden" value="false" name="header-image-none" id="header-image-none">';
	}else{
		echo "ヘッダー画像はありません。";
	}
	echo '</div>';
        ?>
    </fieldset>
    	<div style="text-align:right;margin-top:10px;margin-bottom:10px;">
		<?= $this->Form->button(__('更新する'), ['class' => 'btn btn-primary']) ?>
	</div>
    <?= $this->Form->end() ?>
</div>