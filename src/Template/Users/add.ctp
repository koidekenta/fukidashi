<div style="position:relative;border:1px solid #eee;padding:10px 20px;border-radius:5px;width:90%;margin-top:70px;margin-left:auto;margin-right:auto;background-color:white;">
<?php if(!empty($user)){ ?>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('新規登録') ?></legend>
        <?php
            echo $this->Form->control('email',['label' => 'Eメール', 'style' => 'border-radius:5px;']);
            echo $this->Form->control('username',['label' => 'ユーザー名', 'style' => 'border-radius:5px;']);
            echo $this->Form->control('password',['label' => 'パスワード', 'style' => 'border-radius:5px;']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('新規登録する'),['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
<?php }else{ ?>
	エラー
<?php } ?>
</div>