<div style="position:relative;border:1px solid #eee;padding:10px 20px;border-radius:5px;width:90%;margin-top:70px;margin-left:auto;margin-right:auto;background-color:white;">
新規登録していない場合は<a href="/users/add">こちら</a>
<h3>ログイン</h3>
<?= $this->Form->create() ?>
<fieldset>
<div class="form-group">
<?= $this->Form->control('email',['label' => 'メールアドレス','class'=>'form-control', 'required' => 'required', 'style' => 'border-radius:5px;']) ?>
</div>
<div class="form-group">
<?= $this->Form->control('password',['label' => 'パスワード','class'=>'form-control', 'required' => 'required', 'style' => 'border-radius:5px;']) ?>
</div>
</fieldset>
<?= $this->Form->button('ログイン', ['class' => 'btn btn-primary', 'style' => '']) ?>
<?= $this->Form->end() ?>
</div>