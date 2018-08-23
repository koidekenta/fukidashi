$(function(){
	var stock = "";
	var count = 0;
	var toggle_flug = false;
	var modal_flug = false;
	var current_url = "";
	$('#dm_create_btn').prop('disabled',true);

	if(location.pathname.match(/^\/users\/view\/[a-zA-Z0-9@\.]+?\/[a-fA-F0-9]{32}$/)){
		if(location.pathname.match(/^\/users\/view\/[a-zA-Z0-9@\.]+?\/[a-fA-F0-9]{32}$/)){
			$('#modal-fukidashi-data').html('<div style="text-align:center;"><img src="/img/loading02_r2_c1.gif"></div>');
			var m = location.pathname.lastIndexOf("/");
			m = m + 1;
			var slug = location.pathname.slice(m);
			getcontent(slug);
			$("#exampleModalCenter3").modal("show");
		}
	}

	$('#header-image-none-btn').on('click',function(){
		if($('#header-image-none').val() === "false"){
			$('#header-image-none').val("true");
			$('#header-image-none-btn').html("このまま投稿してください");
		}
		return false;
	});

	$('#icon-image-none-btn').on('click',function(){
		if($('#icon-image-none').val() === "false"){
			$('#icon-image-none').val("true");
			$('#icon-image-none-btn').html("このまま投稿してください");
		}
		return false;
	});

	$(document).on('keypress', '#search_window',function(e){
		if(e.keyCode == '13' && $('#search_window').val() === ""){
			return false;
		}
	});

	$(window).on('load resize',function(){
		if($(window).width() <= 560){
			if($('#user_info .user_info_item:nth-child(5)').html() !== ""){
				var p = $('#user_info .user_info_item:nth-child(5)').html();
				$('#user_info .user_info_item:nth-child(5)').html("");
				$('#retm .user_info_item2:nth-child(1)').html(p);
			}

			if($('#user_info .user_info_item:nth-child(6)').html() !== ""){
				var p = $('#user_info .user_info_item:nth-child(6)').html();
				$('#user_info .user_info_item:nth-child(6)').html("");
				$('#retm .user_info_item2:nth-child(2)').html(p);
			}
		}else{

			if($('#retm .user_info_item2:nth-child(1)').html() !== ""){
				var p = $('#retm .user_info_item2:nth-child(1)').html();
				$('#retm .user_info_item2:nth-child(1)').html("");
				$('#user_info .user_info_item:nth-child(5)').html(p);
			}

			if($('#retm .user_info_item2:nth-child(2)').html() !== ""){
				var p = $('#retm .user_info_item2:nth-child(2)').html();
				$('#retm .user_info_item2:nth-child(2)').html("");
				$('#user_info .user_info_item:nth-child(6)').html(p);
			}
		}
	});


	$(document).on('click','.dm-back-btn',function(){
		var result = '<div class="modal-header">ダイレクトメッセージ<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> </div>';
		result += '<div class="modal-body" style="height:350px;overflow:scroll;overflow-x:hidden;"><div id="cont"></div></div><div class="modal-footer"></div>';

		$('#modal-template-data').html(result);
		$('#cont').html('<div style="text-align:center;"><img src="/img/loading02_r2_c1.gif"></div>');
		$('#ModalTemplate').modal('show');

		$.ajax({
			url: "/directmessages/userlist",
			type: "post",
			data: "q=test",
			success: function(msg){
				$('#cont').html('<div style="text-align:center;">' + msg + '</div>');
			},
			error: function(){
				$('#cont').html("error");
			}

		});
	});


	$(document).on('click','.dm_user_item',function(){
		var i = $('.dm_user_item').index(this);
		var username = $('.dm_user_item').eq(i).data('username');
		var result = '<div class="modal-header" style="flex:display;align-items:center;"><label style="cursor:pointer;"><i class="fas fa-angle-left fa-lg"><input type="button" class="dm-back-btn" style="display:none;"></i></label><div style="margin-left:auto;"><h5 class="modal-title"><b>相手:</b>' + username + '</h5></div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
		result += '<div class="modal-body" style="overflow:scroll;overflow-x:hidden;height:350px;"><div id="dm_list"></div></div>';
		result += '<div class="modal-footer"><form method="post" action="/directmessages/add" style="width:100%;font-size:14px;"><textarea style="border-radius:5px;" rows="3" class="form_textarea" name="message"></textarea><input type="hidden" name="to_user" value="' + username + '"><div style="margin:5px;text-align:right;"><input type="submit" value="送信" class="form_btn" data-type="dm" data-flug="true" style="margin-left:15px;border-radius:15px;font-size:13px;padding:5px 10px;color:white;font-weight:bold;border:1px #04C1FB solid;background-color:#04C1FB;"></div></form></div>';
		$("#modal-template-data").html(result);
		$("#ModalTemplate").modal("show");

		$.ajax({
			url: "/directmessages/dmlist",
			type: "post",
			data: "username=" + username
		}).done(function(data){
				$("#dm_list").html(data);
		}).fail( function(data){
				$("#dm_list").html("データを取得できませんでした。");
		});
	});

	$('li.item a:not(.fukidashi_delete,.kotei,.mute)').on('click',function(e){
		e.stopPropagation();
	});

	$(document).on('click','.form_btn',function(){
		var i = $('.form_btn').index(this);
		var flug = $('.form_btn').eq(i).data('flug');
		var type = $('.form_btn').eq(i).data('type');
		if(type !== 'diffusion' && $('.form_textarea').eq(i).val() === ""){
			return false;
		}

			$('.form_btn').eq(i).prop('disabled',true);
			$(this).closest('form').submit();
	});

	$(window).on('popstate',function(e){
		if(location.pathname.match(/(\/users\/timeline\/?$)|(\/users\/view\/[a-zA-Z0-9@\.]+?\/?$)/) ){
			$('#exampleModalCenter3').modal('hide');
		}

		if(location.pathname.match(/^\/users\/view\/[a-zA-Z0-9@\.]+?\/[a-fA-F0-9]{32}$/)){
			$('#modal-fukidashi-data').html('<div style="text-align:center;"><img src="/img/loading02_r2_c1.gif"></div>');
			var m = location.pathname.lastIndexOf("/");
			m = m + 1;
			var slug = location.pathname.slice(m);
			getcontent(slug);
			$("#exampleModalCenter3").modal("show");
		}
	});


	$(document).on('change','.form_file',function(){
  		var i = $('.form_file').index(this);
  		var file = $('.form_file').eq(i).prop('files')[0];

  		if(!file){
    			return;
  		}

		var fr = new FileReader();
  		fr.onload = function(e){
	  		$('.preview').eq(i).attr('src', e.target.result);
			$('.preview').eq(i).addClass('header-image');
  		}
  		fr.readAsDataURL(file);
	});


	$('#exampleModalCenter3').on('hide.bs.modal',function(e){

		if(modal_flug === false){
			if(current_url !== "" && location.pathname.indexOf("/alerts") == -1 && window.history && window.history.go){
				history.pushState(null,null,current_url);
			}else if(current_url === "" && location.pathname != "/users/timeline"){
				var a = location.pathname.lastIndexOf("/");
				history.pushState(null,null,location.pathname.slice(0,a));
			}
		}
	});

	$('#exampleModalCenter3').on('hidden.bs.modal',function(e){
		if(modal_flug === true && location.pathname.match(/^\/users\/view\/[0-9a-zA-Z@\.]+?\/[0-9a-fA-F]{32}$/)){
			var a = location.pathname.lastIndexOf("/");
			a = a + 1;
			$('#exampleModalCenter3').modal('show');
			var slug = location.pathname.slice(a);
			getcontent(slug);
		}else{
			modal_flug = false;
		}
	});

	function getcontent(slug){
		var result = "";
		$.ajax({
			url: "/users/getcontent",
			data: "slug=" + slug,
			type: "post",
			success: function(msg){
				result = msg;
				result += '<div style="margin-top:5px;margin-bottom:5px;"><form method="post" action="/posts/add" enctype="multipart/form-data" style="width:100%;"><textarea id="fukidashi_comment_modal" name="post" rows="3" placeholder="コメントする" style="border-radius:5px;font-size:13px;" required="required" class="form_textarea"></textarea><input type="hidden" name="comment_slug" value="' + slug + '"><input type="hidden" name="is_commented" value="ON"><label class="fukidashi-menu"><i class="far fa-smile mysize-big-big"><input type="button" class="form_emoji" data-status="hide" style="display:none;"></i></label><label class="fukidashi-menu"><i class="fas fa-camera mysize-big-big"><input type="file" name="post_img" class="form_file" style="display:none;"></i></label><input type="submit" value="送信" data-flug="true" class="form_btn" data-type="comment" style="margin-left:15px;border-radius:15px;font-size:13px;padding:5px 10px;color:white;font-weight:bold;border:1px #04C1FB solid;background-color:#04C1FB;"><div class="img_container"><img class="preview"></div>';
				result += '<div style="position:relative;width:55%;">';
				result += '<div class="emoji_list" data-index="3" style="z-index:3000;display:none;position:absolute;background-color:white;border:1px solid #eee;width:100%;height:auto;border-radius:5px;padding-bottom:3px;"><img src="/img/1f642.png" width="20" height="20" title=":normal:" class="emoji"><img src="/img/1f603.png" width="20" height="20" title=":laugh:" class="emoji"><img src="/img/1f602.png" width="20" height="20" title=":cry:" class="emoji"><img src="/img/1f613.png" width="20" height="20" title=":sweat:" class="emoji"><img src="/img/1f616.png" width="20" height="20" title=":-w-:" class="emoji"><img src="/img/1f618.png" width="20" height="20" title=":kiss:" class="emoji"><img src="/img/1f621.png" width="20" height="20" title=":anger:" class="emoji"><img src="/img/1f631.png" width="20" height="20" title=":shock:" class="emoji"><img src="/img/1f635.png" width="20" height="20" title=":vertigo:" class="emoji"><img src="/img/1f644.png" width="20" height="20" title=":upface:" class="emoji"><img src="/img/1f389.png" width="20" height="20" title=":cracker:" class="emoji"><img src="/img/1f431.png" width="20" height="20" title=":cat:" class="emoji"><img src="/img/1f436.png" width="20" height="20" title=":dog:" class="emoji"><img src="/img/1f430.png" width="20" height="20" title=":rabbit:" class="emoji"><img src="/img/1f438.png" width="20" height="20" title=":flog:" class="emoji"><img src="/img/1f434.png" width="20" height="20" title=":horse:" class="emoji"><img src="/img/1f441.png" width="20" height="20" title=":eye:" class="emoji"><img src="/img/1f442-1f3fb.png" width="20" height="20" title=":ear:" class="emoji"><img src="/img/1f637.png" width="20" height="20" title=":mask:" class="emoji"><img src="/img/1f911.png" width="20" height="20" title=":dollar:" class="emoji"></div>';
				result += '</div>';
				result += '</form></div>';
			},
			error: function(){
				$('#modal-fukidashi-data').html("error");
			},

			complete: function(){

		result += '<ul>';

		$.ajax({
			url: "/posts/comment",
			type: "post",
			data: "slug=" + slug,
			success: function(msg){
				if(msg != "end"){
					result += msg;
				}
			},
			error: function(e){
			},
			complete: function(){
				result += '</ul>';
				$('#modal-fukidashi-data').html(result);
				modal_flug = false;
			}
		});

}
		});

	}

	$(document).on('click','li.item',function(){
		modal_flug = true;
		var i = $('li.item').index(this);
		var data = $('li.item > div').eq(i).data("id");
		var slug = $('li.item > div').eq(i).data("slug");
		var username = $('li.item > div').eq(i).data("username");
		var post = $('li.item > div').eq(i).data("post");
		var imageurl = $('li.item > div').eq(i).data("imageurl");
		var created = $('li.item > div').eq(i).data("created");

		$('#modal-fukidashi-data').html('<div style="text-align:center;"><img src="/img/loading02_r2_c1.gif"></div>');

		if(location.pathname.indexOf("/alerts") == -1 && window.history && window.history.pushState){
				if(location.pathname.match(/^\/users\/timeline\/?$/) ){
					current_url = location.pathname;
				}else if(location.pathname.match(/^\/posts\/search/)){
					current_url = location.pathname + location.search;
				}else if(location.pathname.match(/^\/users\/reply\/[a-zA-Z0-9@\.]+?/)){
					current_url = location.pathname;
				}else if(location.pathname.match(/^\/users\/media\/[a-zA-Z0-9@\.]+?/)){
					current_url = location.pathname;
				}else if(location.pathname.match(/^\/$/)){
					current_url = location.pathname;
				}
				history.pushState(null,null,"/users/view/" + username + "/" + slug);
		}
		getcontent(slug);
	});

	$(document).on('click','#header_dm_btn',function(e){
		var result = '<div class="modal-header">ダイレクトメッセージ<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> </div>';
		result += '<div class="modal-body" style="height:350px;overflow:scroll;overflow-x:hidden;"><div id="cont"></div></div><div class="modal-footer"></div>';

		$('#modal-template-data').html(result);
		$('#cont').html('<div style="text-align:center;"><img src="/img/loading02_r2_c1.gif"></div>');
		$('#ModalTemplate').modal('show');

		$.ajax({
			url: "/directmessages/userlist",
			type: "post",
			data: "q=test",
			success: function(msg){
				$('#cont').html('<div style="text-align:center;">' + msg + '</div>');
			},
			error: function(){
				$('#cont').html("error");
			}

		});
	});

	$(document).on('click','.fukidashi-footer-message',function(e){
		e.stopPropagation();
		var result = '<div class="modal-header"><div style="font-weight:bold;">メッセージの作成</div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
		result += '<div class="modal-body" style="padding-bottom:5px;"><p style="font-weight:bold;font-size:14px;">メッセージの送信元:</p><input type="text" id="dm_create_text" style="border-radius:5px;"></div>';
		result += '<div class="modal-body" style="height:280px;overflow:scroll;overflow-x:hidden;"></div>';
		result += '<div class="modal-footer"><input type="button" id="dm_create_btn" value="次へ" style="border-radius:15px;font-size:13px;padding:5px 10px;color:white;font-weight:bold;border:1px #04C1FB solid;background-color:#04C1FB;"></div>';
		$('#modal-template-data').html(result);
		$('#ModalTemplate').modal('show');
	});

	$(document).on('click','#dm_create_btn',function(e){
		e.stopPropagation();
		if($('#dm_create_text').val() !== ""){
		var username = $('#dm_create_text').val();
		$('#modal-template-data').html();
		var result = '<div class="modal-header"><b>送信元:</b>' + username + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
		result += '<div class="modal-body"></div>';
		result += '<div class="modal-footer"><form style="width:100%;" method="post" action="/directmessages/add"><textarea name="message" id="ogmbte" class="form_textarea" required="required" style="border-radius:5px;font-size:13px;"></textarea><input type="hidden" name="to_user" value="' + username + '"><div style="margin-top:10px;text-align:right;"><input type="submit" class="form_btn" data-type="dm" data-flug="true" value="送信" style="padding:5px 10px;border-radius:15px;font-size:13px;color:white;font-weight:bold;border:1px #04C1FB solid;background-color:#04C1FB;"></div></form></div>';
		$('#modal-template-data').html(result);
		$('#ModalTemplate').modal('show');
		}
	});

	$(document).on('click','.fukidashi_delete', function(){
		var i = $('.fukidashi_delete').index(this);
		var username = $('li.item > div').eq(i).data('username');
		var slug = $('li.item > div').eq(i).data('slug');
		$.ajax({
			url: "/posts/delete",
			type: "post",
			data: "username=" + username + "&slug=" + slug,
			success: function(msg){
				if(msg == 0){
					alert("削除できました");
				}else{
					alert("削除できませんでした");
				}
			},
			error: function(){
				alert("削除できませんでした");
			}
		});
	});


	$(document).on('click', '.mute', function(){
		alert("ok");
		var i = $('.mute').index(this);
		var username = $('li.item > div').eq(i).data('username');
		$.ajax({
			url: "/mutes/addmute",
			type: "post",
			data: "username=" + username,
			success: function(msg){
				if(msg != 0){
					alert("ミュートできませんでした。");
				}
			},
			error: function(msg){
				alert("error");
			}
		});
	});

	$(document).on('click',':not(.body)',function(e){

		if(toggle_flug){
			$(".body").hide("slow");
			toggle_flug = false;
			e.stopPropagation();
		}
	});

	$(document).on('click','#search_btn', function(){
		var status = $('#search_window').data("status");
		if(status == "hidden"){
			$(".search_box_block").css({"display":"block"});
			$('#search_window').data("status","show");
		}else if(status == "show"){
			$(".search_box_block").css({"display":"none"});
			$('#search_window').data("status","hidden");
		}
	});

	$(document).on('click', '.head',function(){
		var i = $(".head").index(this);
		$('.body').eq(i).css('display','block');

		if(toggle_flug){
			$(".body").eq(i).hide("slow");
			toggle_flug = false;
			return false;
		}else{
			$(".body").eq(i).show("slow");
			toggle_flug = true;
			return false;
		}
	});

	$(document).on('click','.form_emoji',function(){
		var i = $('.form_emoji').index(this);
		var status = $('.form_emoji').eq(i).data('status');
		if(status === "hide"){
			$('.emoji_list').eq(i).css('display','block');
			$('.form_emoji').eq(i).data('status','show');
		}else if(status === "show"){
			$('.emoji_list').eq(i).css('display','none');
			$('.form_emoji').eq(i).data('status','hide');
		}
	});

	$(document).on('click','.emoji',function(){
		var i = $('.emoji').index(this);
		var l = $(this).parent().data("index");
		if(l === 1){
			$('#fukidashi_main').val( $('#fukidashi_main').val() + $('.emoji').eq(i).attr('title') );
		}else if(l === 2){
			 $('#fukidashi_comment').val( $('#fukidashi_comment').val() + $('.emoji').eq(i).attr('title') );
		}else if(l === 3){
			 $('#fukidashi_comment_modal').val( $('#fukidashi_comment_modal').val() + $('.emoji').eq(i).attr('title') );
		}
	});

	$('ol#content,ol#kotei-fukidashi').on({
		'mouseenter': function(){
			$(this).css('background-color','#F5F8FF');
		},

		'mouseleave': function(){
			$(this).css('background-color','white');
		}
	},'li.item');

	$(document).on('click', '.kotei-kaijyo',function(){
		var i = $('.kotei-kaijyo').index(this);
		var slug = $('li.item > div').eq(i).data("slug");
		var username = $('li.item > div').eq(i).data("username");

		$.ajax({
			url: "/koteis/delete",
			type: "post",
			data: "slug=" + slug + "&username=" + username,
			success: function(msg){
				alert("固定解除できました");
				$("#kotei-fukidashi").remove();
			},
			error: function(msg){
				alert("固定解除できませんでした");
			}
		});
	});

	$(document).on('click', '.kotei',function(){
		var i = $('.kotei').index(this);
		if($("#kotei-fukidashi").length){
			i = i + 1;
		}
		var slug = $('li.item > div').eq(i).data("slug");
		var username = $('li.item > div').eq(i).data("username");

		$.ajax({
			url: "/koteis/add",
			type: "post",
			data: "slug=" + slug + "&username=" + username,
			success: function(msg){
				alert("固定できました");
			},
			error: function(msg){
				alert("固定できませんでした。");
			}
		});

	});

	$(document).on('click','.fukidashi-footer-comment', function(e){
		e.stopPropagation();
		e.preventDefault();
		var i = $('.fukidashi-footer-comment').index(this);
		var slug = $('li.item > div').eq(i).data('slug');
		var username = $('li.item > div').eq(i).data('username');
		var result = '<div class="modal-header"><h5 class="modal-title"><b>返信先:</b> ' + username +  '</h5>';
		result += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		result += '</div>';
		result += '<div class="modal-body">';
		result += '</div>';
		result += '<div class="modal-footer comment_form_modal_footer">';
		result += '<form method="post" class="comment_form" action="/posts/add" enctype="multipart/form-data" accept-charset="utf-8" style="width:100%;">';
		result += '<textarea rows="2" id="fukidashi_comment" class="form_textarea" style="border-radius:5px;" name="post" required="required"></textarea>';
		result += '<input type="hidden" name="comment_slug" value="' + slug + '">';
		result += '<input type="hidden" name="is_commented" value="ON">';
		result += '<div style="display:flex;align-items:center;">';
		result += '<label class="fukidashi-menu"><i class="far fa-smile mysize-big-big"><input type="button" class="form_emoji" data-status="hide" style="display:none;"></i></label><label class="fukidashi-menu"><i class="fas fa-camera mysize-big-big"><input type="file" name="post_img" class="form_file" style="display:none;"></i></label><input type="submit" value="返信" class="form_btn" data-type="comment" data-flug="true" style="margin-left:10px;border-radius:15px;font-size:13px;padding:5px 10px;color:white;font-weight:bold;border:1px #04C1FB solid;background-color:#04C1FB;">';
		result += '</div>';
		result += '<div class="img_container"><img class="preview"></div>';
		result += '<div style="position:relative;width:55%;">';
		result += '<div class="emoji_list" data-index="2" style="z-index:3000;display:none;position:absolute;background-color:white;border:1px solid #eee;width:100%;height:auto;border-radius:5px;padding-bottom:3px;"><img src="/img/1f642.png" width="20" height="20" title=":normal:" class="emoji"><img src="/img/1f603.png" width="20" height="20" title=":laugh:" class="emoji"><img src="/img/1f602.png" width="20" height="20" title=":cry:" class="emoji"><img src="/img/1f613.png" width="20" height="20" title=":sweat:" class="emoji"><img src="/img/1f616.png" width="20" height="20" title=":-w-:" class="emoji"><img src="/img/1f618.png" width="20" height="20" title=":kiss:" class="emoji"><img src="/img/1f621.png" width="20" height="20" title=":anger:" class="emoji"><img src="/img/1f631.png" width="20" height="20" title=":shock:" class="emoji"><img src="/img/1f635.png" width="20" height="20" title=":vertigo:" class="emoji"><img src="/img/1f644.png" width="20" height="20" title=":upface:" class="emoji"><img src="/img/1f389.png" width="20" height="20" title=":cracker:" class="emoji"><img src="/img/1f431.png" width="20" height="20" title=":cat:" class="emoji"><img src="/img/1f436.png" width="20" height="20" title=":dog:" class="emoji"><img src="/img/1f430.png" width="20" height="20" title=":rabbit:" class="emoji"><img src="/img/1f438.png" width="20" height="20" title=":flog:" class="emoji"><img src="/img/1f434.png" width="20" height="20" title=":horse:" class="emoji"><img src="/img/1f441.png" width="20" height="20" title=":eye:" class="emoji"><img src="/img/1f442-1f3fb.png" width="20" height="20" title=":ear:" class="emoji"><img src="/img/1f637.png" width="20" height="20" title=":mask:" class="emoji"><img src="/img/1f911.png" width="20" height="20" title=":dollar:" class="emoji"></div>';
		result += '</div>';
		result += '</form>';
		result += '</div>';
		$("#modal-template-data").html(result);
		$("#ModalTemplate").modal('show');
	});

	$(document).on('click','.fukidashi-footer-diffusion', function(e){
		e.stopPropagation();
		var i = $('.fukidashi-footer-diffusion').index(this);
		var data = $('li.item > div').eq(i).data("id");
		var slug = $('li.item > div').eq(i).data("slug");
		var created = $('li.item > div').eq(i).data("created");
		var username = $('li.item > div').eq(i).data("username");
		var post = $('li.item > div').eq(i).data("post");
		var imageurl = $('li.item > div').eq(i).data("imageurl");
		$('#diffusion-slug').val(slug);
		var result = '<ul><li class="item" style="border:none;"></div><div class="fukidashi-header"><div><a href="/users/view/' + username + '">' + username + '</a>　</div><div><a href="/users/view/' + username + '/' + slug + '">' + created + '</a></div></div>';
		result += '<img src="/img/' + imageurl + '" width="40" height="40" class="usericon"><div class="article-body">' + post + "</div></div></li></ul>";
		$('#modal-extend-data').html(result);
		$('#diffusion_slug').val(slug);

	});

	$(document).on('click','.fukidashi-footer-favorite', function(e){
		e.stopPropagation();
		var i = $('.fukidashi-footer-favorite').index(this);
		var str = $('.fukidashi-footer-favorite').eq(i).html();
		var slug = $('li.item > div').eq(i).data('slug');
		$('.favorite').eq(i).prop('disabled', true);
		var flug = "";
		var url  = "";

		if(str.indexOf('fas fa-heart heart-clicked') != -1){
			url = "/favorites/deletefavorite";
			flug = false;
		}else if(str.indexOf('far fa-heart mysize') != -1){
			url = "/favorites/addfavorite";
			flug = true;
		}

		$.ajax({
			url: url,
			type: "post",
			data: "slug=" + slug,
			success: function(msg){
				if(msg == 0 && flug == true){
					$('.fukidashi-footer-favorite').eq(i).html('<i class="fas fa-heart heart-clicked"><input type="button" class="favorite" style="display:none;"></i>');
					$('.favorite_num').eq(i).html(parseInt($('.favorite_num').eq(i).html(),10) + 1);
					$('.favorite').eq(i).prop('disabled', false);
				}else if(msg == 0 && flug == false){
					$('.fukidashi-footer-favorite').eq(i).html('<i class="far fa-heart mysize"><input type="button" class="favorite" style="display:none;"></i>');
					$('.favorite_num').eq(i).html(parseInt($('.favorite_num').eq(i).html(),10) - 1);
					$('.favorite').eq(i).prop('disabled', false);
				}
			},
			error: function(msg){
				alert("お気に入りできませんでした。もう一度、やってみてください。");
			}
		});
	});

	$(document).on('click','.right-dropdown', function(e){
		e.stopPropagation();
		var i = $('.right-dropdown').index(this);
		alert('right-dropdown' + i);
		$('.right-dropdown-body').eq(i).css('display','block');
		if(toggle_flug){
			
			toggle_flug = false;
		}else{
			
			toggle_flug = true;
		}
	});

	setInterval(function(){
		var created = "";
		var mode = "";
		var username = "";
		var keyword = "";
		var flug = false;

		if(location.pathname.indexOf("/posts/search") != -1){
			flug = true;
			mode = "search_mode";
			last = location.href.lastIndexOf("?q=");
			last = last + 3;
			keyword = location.href.slice(last);
		}else if(location.pathname.indexOf("/users/timeline") != -1 || location.pathname.match(/^\/$/)){
			flug = true;
			mode = "timeline_mode";
		}else if(location.pathname.match(/\/users\/view\/[0-9a-zA-Z@\.]+?$/)){
			flug = true;
			mode = "user_mode";
			last = location.pathname.lastIndexOf("/");
			last = last + 1;
			username = location.pathname.slice(last);
		}else if(location.pathname.match(/^\/users\/reply\/[0-9a-zA-Z@\.]+?$/)){
			flug = true;
			mode = "reply_mode";
			last = location.pathname.lastIndexOf("/");
			last = last + 1;
			username = location.pathname.slice(last);
		}else if(location.pathname.match(/^\/users\/media\/[0-9a-zA-Z@\.]+?$/)){
			flug = true;
			mode = "media_mode";
			last = location.pathname.lastIndexOf("/");
			last = last + 1;
			username = location.pathname.slice(last);
		}else if(location.pathname.match(/^\/users\/favorite\/[0-9a-zA-Z@\.]+?$/)){
			flug = true;
			mode = "favorite_mode";
			last = location.pathname.lastIndexOf("/");
			last = last + 1;
			username = location.pathname.slice(last);
		}

		if(stock != ""){
			var a = [];
			var r = /data\-created="(.*?)"/g;
			while((mtt = r.exec(stock)) != null){
         			a.push(mtt[1]);
			}
			created = a[0];

		}else{
			if($("#kotei-fukidashi").length){
				created = $('li.item > div:eq(1)').data('created');
			}else{
				created = $('li.item > div:first').data('created');
			}
		}

		$.ajax({
			type: "post",
			url: "/alerts/alertcount",
			data: "mode=alert",
			success: function(msg){
				if(msg != ""){
					$("#alert_count").addClass("alert_num");
					$("#alert_count").html(msg);
				}else{
					$("#alert_count").removeClass("alert_num");
					$("#alert_count").html("");
				}
			},

			error: function(msg){
				
			}
		});

		$.ajax({
			type: "post",
			url: "/alerts/alertcount",
			data: "mode=dm",
			success: function(msg){
				if(msg != ""){

					$("#dm_count").addClass("dm_num");
					$("#dm_count").html(msg);
				}else{
					$("#dm_count").removeClass("dm_num");
					$("#dm_count").html("");
				}
			},

			error: function(msg){
				
			}
		});

		if(flug != false){
		$.ajax({
			type: "post",
			url: "/users/leadfind",
			data: "created=" + created + "&mode=" + mode + "&username=" + username + "&keyword=" + keyword,
			dataType: "text",
			success: function(msg){
					if(msg != -1){
						stock = msg + stock;
						count = stock.split('<li class="item"').length - 1;
						if(count != 0){
							$("#latest_message").html("<li><center>" + count + '件のメッセージがあります。</center></li>');
							document.title = "(" + count + ")" + " タイムライン";
						}
					}
					flug = false;
			},
			error: function(){
				flug = false;
			}
		});
		}

	},30000);

	$(document).on('click','#latest_message', function(){
		$('#latest_message').html("");
		$('#content').prepend(stock);
		document.title = "タイムライン";
		count = 0;
		stock = "";
	});

	$(window).on('scroll', function(){
		"use strict";
		var docHeight = $(document).innerHeight();
		var windowHeight = $(window).innerHeight();
		var pageBottom = docHeight - windowHeight;

		var url = "";
		var slug = "";
		var mode = "";
		var username = "";
		var keyword = "";

		if(location.pathname == "/posts" || location.pathname == "/posts/"){
			url = "/posts/endfind";
		}else if(location.pathname === "/users/timeline" || location.pathname === "/"){
			url = "/users/endtimeline";
			mode  = "timeline_mode";
		}else if(location.pathname.indexOf("/users/favorite/") != -1){
			url = "/users/endtimeline";
			mode = "favorite_mode";
			var last = location.pathname.lastIndexOf("/");
			last = last + 1;
			username = location.pathname.slice(last);
		}else if(location.pathname == "/users"){
			url = "/users/endusers";
		}else if(location.pathname.indexOf("/posts/view/") != -1){
			url = "/comments/endcomments";
		}else if(location.pathname.indexOf("/users/view/") != -1){
			url = "/users/endtimeline";
			mode = "user_mode";
			last = location.pathname.lastIndexOf("/");
			last = last + 1;
			username = location.pathname.slice(last);
		}else if(location.pathname.indexOf("/posts/search") != -1){
			url = "/users/endtimeline";
			mode = "search_mode";
			last = location.href.lastIndexOf("?q=");
			last = last + 3;
			keyword = location.href.slice(last);
		}else if(location.pathname.match(/^\/users\/reply\/[0-9a-zA-Z@\.]+?$/)){
			mode = "reply_mode";
			last = location.pathname.lastIndexOf("/");
			last = last + 1;
			url = "/users/endtimeline";
			username = location.pathname.slice(last);
		}else if(location.pathname.match(/^\/users\/media\/[0-9a-zA-Z@\.]+?$/)){
			mode = "media_mode";
			last = location.pathname.lastIndexOf("/");
			last = last + 1;
			url = "/users/endtimeline";
			username = location.pathname.slice(last);
		}

		if(pageBottom <= $(window).scrollTop()){
			next = $('#next').text();
			$.ajax({
				type: "get",
				url: url,
				data: "page=" + next + "&slug=" + slug + "&mode=" + mode + "&username=" + username + "&keyword=" + keyword,
				success: function(msg){
					if(msg == "end"){
					$('#next').css('display','inline');
					$('#next').html('<center><b>データはここでおしまいです</b></center>');
					}else{
					$('#next').text(parseInt(next,10) + 1);
					$('ol#content').append(msg);
					}

				},
				error:	function(msg){
					$('ol#content').html($('ol#content').html() + "<center><br>申し訳ありませんエラーのようです<br></center>");
				}
			});
		}
	});

$(document).on('click','.mute_btn',function(){
		$('.mute_btn').prop("disabled",true);
		var i = $('.mute_btn').index(this);
		var username = $('.mute_btn').eq(i).data("username");

	$.ajax({
		type: "post",
		url: "/mutes/addmute",
		data: "username=" + username,
		success: function(msg){
			if(msg == 0){
				$('.mute_btn').prop("disabled",false);
				$('#mute_btn_cover').html('<i class="fas fa-microphone-slash mysize-big" style="color:red;"><input type="button" class="unmute_btn" style="display:none;" data-username="' + username + '" />');
			}else{
				$('.mute_btn').prop("disabled",false);
				alert("error");
			}
		},
		error: function(msg){
			alert("error");
		}
	});

});

$(document).on('click','.unmute_btn',function(){
		$('.unmute_btn').prop("disabled",true);
		var i = $('.unmute_btn').index(this);
		var username = $('.unmute_btn').eq(i).data("username");
	$.ajax({
		type: "post",
		url: "/mutes/deletemute",
		data: "username=" + username,
		success: function(msg){
			if(msg == 0){
				$('.unmute_btn').prop("disabled",false);
				$('#mute_btn_cover').html('<i class="fas fa-microphone-slash mysize-big"><input type="button" class="mute_btn" style="display:none;" data-username="' + username + '" />');
			}else{
				$('.unmute_btn').prop("disabled",false);
				alert("error");
			}
		},
		error: function(msg){
			alert("error");
		}
	});
});

$(document).on('click','.follow_bn',function(){
		var i = $('.follow_bn').index(this);
		$('.follow_bn').eq(i).prop("disabled",true);
		var flug = $('.follow_bn').eq(i).data("flug");
		var username = $('.follow_bn').eq(i).data("username");

		var url = "";
				
		if(flug == true){
			url = "/follows/deletefollow";
		}else{
			url = "/follows/addfollow";
		}

	$.ajax({
		type: "post",
		url: url,
		data: "username=" + username,
		success: function(msg){
			$('.follow_bn').eq(i).prop("disabled",false);
			if(flug == true){
				$('.btn_cover').eq(i).html('<input type="button" value="フォローする" class="follow_bn" data-flug="false" data-username="' + username + '" />');
				$('.follow_bn').eq(i).css({"color":"#04c1fb","background-color":"#ffffff"});
			}else{
				$('.btn_cover').eq(i).html('<input type="button" value="フォロー中" class="follow_bn" data-flug="true" data-username="' + username + '" />');
				$('.follow_bn').eq(i).css({"color":"#ffffff","background-color":"#04c1fb"});
			}
		},
		error: function(msg){
			alert("error");
			$('.follow_bn').prop("disabled",false);
		}
	});
});

$(document).on('click','.unfavorite_favorite_bn',function(e){
		e.stopPropagation();
		$('.unfavorite_favorite_bn').prop("disabled",true);
		var i = $('.unfavorite_favorite_bn').index(this);
		var slug = $('.hidden_slug').eq(i).val();
		
	$.ajax({
		type: "post",
		url: "/favorites/deletefavorite",
		data: "slug=" + slug,
		success: function(msg){
			if(msg != -1){
				$('#favorite_btn_cover').html('<input type="button" value="ブックマークする" class="favorite_bn" />');
			}else{
				alert("ブックマークできませんでした。");
			}
				$('.unfavorite_favorite_bn').prop("disabled",false);
		},
		error: function(msg){
			alert("error");
			$('.unfavorite_favorite_bn').prop("disabled",false);
		}
	});
});


$(document).on('click','.favorite_bn',function(){
		$('.favorite_bn').prop("disabled",true);
		var i = $('.favorite_bn').index(this);
		var slug = $('.hidden_slug').eq(i).val();
		
	$.ajax({
		type: "post",
		url: "/favorites/addfavorite",
		data: "slug=" + slug,
		success: function(msg){
			if(msg != -1){
				$('#favorite_btn_cover').html('<input type="button" value="ブックマーク中" class="unfavorite_favorite_bn" />');
			}else{
				alert("ブックマークできませんでした。");
			}
				$('.favorite_bn').prop("disabled",false);
		},
		error: function(msg){
			alert("error");
			$('.favorite_bn').prop("disabled",false);
		}
	});
});


	$('.favorite_bn').hover(
		function(){
				var index = $('.favorite_bn').index(this);
				$('.favorite_bn').eq(index).css("background-color","#F2F3F2");
		},
		function(){
				var index = $('.follow_bn').index(this);
				$('.favorite_bn').eq(index).css("background-color","#FFFFFF");
		}
	);

	$('.unfavorite_favorite_bn').hover(
		function(){
				var index = $('.unfavorite_favorite_bn').index(this);
				$('.unfavorite_favorite_bn').eq(index).val("解除");
				$('.unfavorite_favorite_bn').eq(index).css("background-color","#FF0000");
				$('.unfavorite_favorite_bn').eq(index).css("color","white");
		},
		function(){
				var index = $('.unfavorite_favorite_bn').index(this);
				$('.unfavorite_favorite_bn').eq(index).val("ブックマーク中");
				$('.unfavorite_favorite_bn').eq(index).css("background-color","#04C1FB");
				$('.unfavorite_favorite_bn').eq(index).css("color","white");
		}
	);

	$('.follow_bn').hover(
		function(){
				var i = $('.follow_bn').index(this);
				if($('.follow_bn').eq(i).data("flug") === true){
					$('.follow_bn').eq(i).val("解除");
					$('.follow_bn').eq(i).css("background-color","#FF0000");
					$('.follow_bn').eq(i).css("border","1px solid #FF0000");
					$('.follow_bn').eq(i).css("color","white");
				}else{
					$('.follow_bn').eq(i).css("background-color","#F2F3F2");
				}
		},
		function(){
				var i = $('.follow_bn').index(this);
				if($('.follow_bn').eq(i).data("flug") === true){
					$('.follow_bn').eq(i).val("フォロー中");
					$('.follow_bn').eq(i).css("background-color","#04C1FB");
					$('.follow_bn').eq(i).css("border","1px solid #04C1FB");
					$('.follow_bn').eq(i).css("color","white");
				}else{
					$('.follow_bn').eq(i).css("background-color","");
				}
		}
	);
});