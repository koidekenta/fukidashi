<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
    <?= $this->Html->css('timeline.css') ?>
    <?= $this->Html->css('fa-brands.css') ?>
    <?= $this->Html->css('fa-regular.css') ?>
    <?= $this->Html->css('fa-solid.css') ?>
    <?= $this->Html->css('fontawesome.css') ?>
    <?= $this->Html->css('bootstrap.min.css') ?>
    <?= $this->Html->script('jquery-3.2.1.min.js') ?>
    <?= $this->Html->script('popper.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('bootstrap.bundle.min.js') ?>
    <?php //$this->Html->script('dropdown.js'); ?>
    <?= $this->Html->script('timeline.js') ?>
    <?= $this->Html->script('ofi.min.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
<script>
</script>
</head>
<body>

<?php if($current_user !== null){ ?>
<div id="header">
	<div style="margin-left:auto;"><label style="margin-left:15px;"><a href="/users/timeline"><i class="fas fa-home mysize-big"></i></a></label></div>
	<div style="margin-left:auto;"><label style="margin-left:15px;"><i class="fas fa-search mysize-big"><input type="button" id="search_btn" style="display:none;"></i></label></div>
	<div style="margin-left:auto;position:relative;"><span style="margin-left:20px;"><a href="/alerts"><i class="far fa-bell mysize-big"></i></a><span id="alert_count"></span></span></div>
	<div style="margin-left:auto;position:relative;"><label style="margin-left:15px;"><i class="far fa-envelope mysize-big"><input type="button" style="display:none;" id="header_dm_btn"></i><span id="dm_count"></span></label></div>
	<div style="margin-left:auto;"><label style="margin-left:15px;"><i class="far fa-edit mysize-big"><input type="button" style="display:none;" id="fukidashi_btn" data-toggle="modal" data-target="#exampleModalCenter"></i></label></div>
	<div style="margin-left:auto;">
		<div style="position:relative;padding:0px;">
			<div class="head" style="margin-right:10px;cursor:pointer;"><i class="fas fa-bars mysize-big"></i></div>
			<div class="body" style="z-index:1000;display:none;overflow:hidden;font-size:13px;width:200px;margin-top:20px;position:absolute;right:10px;background-color:white;border:solid 1px #CBCDD2;padding:5px 0px;border-radius:5px;">
				<ul class="toggle-item">
					<li class="toggle-item-list"><a href="/users/edit" id="edit" >プロフィール編集</a></li>
					<li class="toggle-item-list"><a href="/users/" id="user_list" >ユーザー一覧</a></li>
					<li class="toggle-item-list"><a href="/users/logout" id="logout" >ログアウト</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php } ?>
    <?= $this->Flash->render() ?>

<div style="margin-bottom:60px;"></div>

        <?= $this->fetch('content') ?>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">吹き出す</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

    <form method="post" accept-charset="utf-8" action="/posts/add" enctype="multipart/form-data"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>    <fieldset>
        <div class="input textarea required"><label for="post">吹き出す</label><textarea name="post" id="fukidashi_main" class="form_textarea" rows="2" placeholder="今、吹き出したいことは？" style="border-radius:10px;" required="required" maxlength="255" id="post"></textarea></div><input type="hidden" name="slug" id="slug" value="<?php echo hash("md4",$this->request->session()->read("Auth.User.username").mt_rand().time()); ?>"/>    </fieldset>
<label class="fukidashi-menu"><i class="far fa-smile mysize-big-big"><input type="button" class="form_emoji" data-status="hide" style="display:none;"></i></label>
<label class="fukidashi-menu">
<i class="fas fa-camera mysize-big-big"><input type="file" name="post_img" class="form_file" style="display:none;"></i>
</label>    <label class="fukidashi-menu"><i class="far fa-edit mysize-big-big"><input type="submit" data-flug="true" data-type="fukidashi" class="form_btn" style="cursor:pointer;display:none;"></i></label>
<div style="position:relative;width:55%;">
<div class="emoji_list" data-index="1" style="z-index:3000;display:none;position:absolute;background-color:white;border:1px solid #eee;width:100%;height:auto;border-radius:5px;padding-bottom:3px;"><?= $this->element('emoji') ?></div>
</div>
  </form>
<div class="img_container"><img class="preview"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">フォロワーに拡散する</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	<form method="post" action="/posts/add" accept-charset="utf-8" id="diffusion_form">
        	<textarea rows="2" name="post" class="form_textarea" style="border-radius:6px;" placeholder="引用吹き出し(コメントを追加)"></textarea>
		<input type="hidden" name="_method" value="POST"/>
		<input type="hidden" name="diffusion_slug" id="diffusion_slug" value="">
		<input type="hidden" name="is_diffusion" id="is_diffusion" value="ON">
		<input type="hidden" name="slug" id="slug" value="<?php echo hash("md4",$this->request->session()->read("Auth.User.username").mt_rand().time()); ?>"/>
		<div style="text-align:right;margin-top:5px;margin-bottom:5px;">
		<input type="submit" class="form_btn" style="margin-left:15px;border-radius:15px;font-size:13px;padding:5px 10px;color:white;font-weight:bold;border:1px #04C1FB solid;background-color:#04C1FB;" data-flug="true" data-type="diffusion" value="拡散">
		</div>
	</form>
      </div>
      <div class="modal-body" style="border-top:solid #e9ecef 1px;">
        <ol>
		<div id="modal-extend-data"></div>
	</ol>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          		<span aria-hidden="true">&times;</span>
        	</button>
     </div>
	<div class="modal-body" id="modal-fukidashi-data">
	</div>
	<div class="modal-footer">
	</div>
    </div>
  </div>
</div>

<div class="modal fade" id="ModalTemplate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div id="modal-template-data"></div>
    </div>
  </div>
</div>

<div class="search_box_block" style="display:none;left:3%;right:5%;top:60px;position:fixed;margin:10px;">
	<form method="get" action="/posts/search">
		<input type="text" placeholder="検索" name="q" id="search_window" data-status="hidden" style="border-radius:20px;">
	</form>
</div>


    <footer>
    </footer>
	<script>
      		objectFitImages('img.header-image');
    	</script>
</body>
</html>
