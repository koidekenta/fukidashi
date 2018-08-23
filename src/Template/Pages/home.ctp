
<?php if($this->request->session()->read("Auth.User.username")){ ?>
	<br>
	<h2>こんにちは、<?= $this->request->session()->read("Auth.User.username") ?>さん　</h2>
	<ul>
		<li><b>ユーザー一覧</b> - 登録しているユーザーがリストされます</li>
		<li><b>最新の投稿</b> - ユーザーの最新の投稿が表示されます</li>
		<li><b>タイムライン</b> - 自分と自分がフォローしたユーザーの投稿が表示されます</li>
		<li><b>ブックマーク</b> - 自分がブックマークした投稿の一覧が表示されます</li>
		<li><b>プロフィール編集</b> - 自分のプロフィールを変更できます</li>
	</ul>
<?php }else{ ?>
	<center>
		<h2>吹き出し</h2>
		<?= $this->Html->image('toppage.png') ?>
	</center>
<?php } ?>

