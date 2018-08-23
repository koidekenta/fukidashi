<?php

namespace App\Utils;

use Cake\I18n\Time;

class AppUtility{

	public static function alert_mes($action,$post_slug,$who,$to_user){
		if($action === "favorite"){
			return '<a href="/users/view/'.$who.'">'.$who.'</a>さんが以下の投稿を<a href="/users/view/'.$to_user.'/'.$post_slug.'">お気に入り</a>しました。';
		}else if($action === "diffusion"){
			return '<a href="/users/view/'.$who.'">'.$who.'</a>さんが以下の投稿を<a href="/users/view/'.$to_user.'/'.$post_slug.'">拡散</a>しました。';
		}else if($action === "follow"){
			return '<a href="/users/view/'.$who.'">'.$who.'</a>さんがあなたをフォローしました。';
		}else if($action === "comment"){
			return '<a href="/users/view/'.$who.'">'.$who.'</a>さんが以下の投稿を<a href="/users/view/'.$to_user.'/'.$post_slug.'">コメント</a>しました。';
		}
	}

	public static function r_($results,$mode = null,$username = null){
				$t = "";

		for($i = 0; $i < count($results); $i++){
				if($mode === "modal_mode"){
					$t .= '<li class="mod">';
				}else{
					$t .= '<li class="item" data-toggle="modal" data-target="#exampleModalCenter3">';
				}
				$t .= '<div data-created="'.$results[$i]["created"].'" data-slug="'.h($results[$i]["slug"]).'" data-username="'.h($results[$i]["username"]).'" data-id="'.h($results[$i]["id"]).'" data-post="'.h($results[$i]["post"]).'" data-imageurl="'.AppUtility::im_ch(h($results[$i]["imageurl"])).'">';

				if($mode !== "search_mode" and $mode !== "comment_mode" and !empty($results[$i]["my_retweet_true"]) and $results[$i]["my_retweet_true"] !== null){
					$t .= '<div style="display:flex;align-items:center;"><div><i class="fas fa-recycle my-small"></i></div><div style="margin-left:5px;font-size:12px;"><a href="/users/view/'.$results[$i]["username"].'">'.$results[$i]["username"].'</a>さんが拡散しました</div></div>';
				}

				if(!empty($results[$i]["is_commented"]) and $results[$i]["is_commented"] === "ON" and !empty($results[$i]["from_comment_username"])){
					$t .= '<div style="display:flex;align-items:center;">発信元:<a href="/users/view/'.$results[$i]["from_comment_username"].'">'.$results[$i]["from_comment_username"].'</a></div>';
				}

				$t .= '<div class="fukidashi-header"><div><a href="/users/view/'.h($results[$i]["username"]).'">'.h($results[$i]["username"]).'</a>　</div> <div style="margin-left:10px;"><a href="/users/view/'.h($results[$i]["username"]).'/'.h($results[$i]["slug"]).'">'.AppUtility::time_change($results[$i]["created"]).'</a></div><div style="margin-left:auto;">';
				$t .= '<div style="position:relative;padding:0px;"><div class="head" style="position:absolute;right:5px;margin-bottom:10px;cursor:pointer;"><i class="fas fa-angle-down fa-lg"></i></div><div class="body" style="z-index:1000;display:none;overflow:hidden;font-size:13px;width:200px;margin-top:20px;position:absolute;right:10px;background-color:white;border:solid 1px #CBCDD2;padding:5px 0px;border-radius:5px;"><ul class="toggle-item">';

				if(!empty($username) and $results[$i]["username"] !== $username){ 
					$t .= '<li><a class="mute">'.h($results[$i]["username"]).'さんをミュートする</a></li>';
				}else if(!empty($username) and $results[$i]["username"] === $username){
					$t .= '<li class="toggle-item-list"><a class="kotei">このふきだしを固定する</a></li><li class="toggle-item-list"><a class="fukidashi_delete">このふきだしを削除する</a></li>';
				}

				$t .= '</ul></div></div></div></div>';

				if($results[$i]["imageurl"] !== null and $results[$i]["imageurl"]  !== ""){
					$t .= '<img src="/img/'.$results[$i]["imageurl"].'" width="40" height="40" class="usericon">';
				}else{
					$t .= '<img src="/img/default_prof.png" width="40" height="40" class="usericon">';
				}

				$t .= '<div class="article-body">';

				if($mode === "modal_mode"){
					$t .= "<h2>".AppUtility::pc(h($results[$i]["post"]))."</h2>";
				}else{
					$t .= AppUtility::pc(h($results[$i]["post"]));
				}

				if($results[$i]["post_img"]){
					$t .= '<div class="img_container"><img src="/img/'.$results[$i]["post_img"].'" classs="header-image"></div>';
				}

				if(!empty($results[$i]["is_retweeted"]) and $results[$i]["is_retweeted"] === "ON"){
					if(!empty($results[$i]["retweet_posts_post"])){
						$t .= '<div style="width:70%;border:solid 1px #CBCDD2;padding:5px;border-radius:5px;">';
						$t .= '<div style="display:flex;">';
						$t .= '<div><a href="/users/view/'.$results[$i]["retweet_posts_username"].'">'.$results[$i]["retweet_posts_username"].'</a></div>';
						$t .= '<div style="margin-left:10px;"><a href="/users/view/'.$results[$i]["retweet_posts_username"].'/'.$results[$i]["retweet_posts_slug"].'">'.date("Y年m月d日",strtotime($results[$i]["retweet_posts_created"])).'</a></div>';
						$t .= '</div>';
						$t .= AppUtility::pc(h($results[$i]["retweet_posts_post"]));
						$t .= '</div>';
					}else{
						$t .= '<div style="width:70%;border:solid 1px #ccd4dc;text-align:center;padding:5px;border-radius:5px;background-color:#f5f8fa;">';
						$t .= 'このふきだしはありません';
						$t .= '</div>';
					}
				}

				$t .= '<div class="flex"><p class="fukidashi-footer"><label class="fukidashi-footer-comment"><i class="far fa-comment mysize"><input type="button" class="comment" style="display:none;"></i></label><span class="comment_num">'.$results[$i]["comment_num"].'</span></p><p class="fukidashi-footer"><label class="fukidashi-footer-diffusion">';
								if(!empty($results[$i]["retweets_to_id"]) and $results[$i]["retweets_to_id"] !== null){
									$t .= '<i class="fas fa-recycle recycle-clicked">';
								}else{
									$t .= '<i class="fas fa-recycle mysize">';
								}
				$t .= '<input type="button" class="diffusion" style="display:none;" data-toggle="modal" data-target="#exampleModalCenter2"></i></label><span class="diffusion_num">'.$results[$i]["refukidashi_num"].'</span></p><p class="fukidashi-footer"><label class="fukidashi-footer-favorite">';
								if(!empty($results[$i]["my_favorite_true"]) and $results[$i]["my_favorite_true"] != null){
									 $t .= '<i class="fas fa-heart heart-clicked">';
								}else{
									 $t .= '<i class="far fa-heart mysize">';
								}
				$t .= '<input type="button" class="favorite" style="display:none;"></i></label><span class="favorite_num">'.$results[$i]["favorite_num"].'</span></p><p class="fukidashi-footer"><label class="fukidashi-footer-message"><i class="far fa-envelope mysize"></i></label></p></div></div></div></li>';
		}
		return $t;
	}

	public static function k_($keyword){
			if(strpos($keyword, " ") or strpos($keyword, "　")){
				$keyword = str_replace("　"," ",$keyword);
				$keywords = explode(" ",$keyword);
			}else{
				$keywords[] = $keyword;
			}

			foreach($keywords as $item){
				$arr['post LIKE'] = '%'.$item.'%';
			}

			return $arr;
	}

	public static function h_plus($keyword){
			$keyword = h($keyword);
			$keyword = str_replace("'", "''", $keyword);
			return $keyword;
	}

	public static function k_sql($keyword, $flug = "o"){
			$result = [];
			$p = "";
			$keyword = AppUtility::h_plus($keyword);
			if(strpos($keyword, " ") or strpos($keyword, "　")){
				$keyword = str_replace("　"," ",$keyword);
				$keywords = explode(" ",$keyword);
			}else{
				$keywords[] = $keyword;
			}

			foreach($keywords as $item){
				$result[] = "post LIKE "."'%".$item."%'";
			}

			if($flug === "o"){
				$p = implode(" OR ", $result);
			}else if($flug === "a"){
				$p = implode(" AND ", $result);
			}

			return $p;
	}

	public static function time_change($time){
		$s = time() - strtotime($time);

		if($s < 60){
			$s = $s."秒前";
		}else if($s < 3600 and $s >= 60){
			$s = round(($s / 60))."分前";
		}else if($s < 86400 and $s >= 3600){
			$s = round(($s / 3600))."時間前";
		}else if($s >= 86400){
			$s = date("Y年m月d日", strtotime($time));
		}

		return $s;
	}

	public static function arr($array_list){
		if(is_array($array_list) and count($array_list) !== 0){
			$array_list = implode(",",$array_list);
			return "(".$array_list.")";
		}else{
			return "('')";
		}
	}

	public static function im_ch($url){
		if(!empty($url)){
			return $url;
		}else{
			return "default_prof.png";
		}
	}

	public static function h_im_ch($url){
		if(!empty($url)){
			return $url;
		}else{
			return "default_header_image.png";
		}
	}

	public static function offr($p){
		$amari = $p - 2;
		$amari = $amari * 20;
		$offset = $amari + 20;
		return $offset;
	}

	public static function pc($str){
		$str = preg_replace("/(?<!#|@)@([0-9a-zA-Z]+)(\r\n|\r|\n| |　|$)/u", "<a href=\"/posts/search?q=$1\">@$1</a>$2", $str);
		$str = preg_replace('/(?<!#|@|href=")#([0-9a-zA-Zぁ-んァ-ヶー一-龠。？！、]+)(\r\n|\r|\n| |　|$)/u', "<a href=\"/posts/search?q=$1\">#$1</a>$2", $str);
		$str = preg_replace('/(?<!href=")(https?):\/\/([-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)(\r\n|\r|\n| |　|$)/u', "<a href=\"$1://$2\">$2</a>$3", $str);
		$short_str = [":normal:",":laugh:",":cry:", ":sweat:",":-w-:",":kiss:",":anger:",":shock:",":vertigo:",":upface:",":cracker:",":cat:",":dog:",":rabbit:",":flog:",":horse:",":eye:",":ear:",":mask:",":dollar:"];
		$imagename_list = ['<img src="/img/1f642.png" width="16" height="16">','<img src="/img/1f603.png" width="16" height="16">','<img src="/img/1f602.png" width="16" height="16">','<img src="/img/1f613.png" width="16" height="16">','<img src="/img/1f616.png" width="16" height="16">','<img src="/img/1f618.png" width="16" height="16">','<img src="/img/1f621.png" width="16" height="16">','<img src="/img/1f631.png" width="16" height="16">','<img src="/img/1f635.png" width="16" height="16">','<img src="/img/1f644.png" width="16" height="16">','<img src="/img/1f389.png" width="16" height="16">','<img src="/img/1f431.png" width="16" height="16">','<img src="/img/1f436.png" width="16" height="16">','<img src="/img/1f430.png" width="16" height="16">','<img src="/img/1f438.png" width="16" height="16">','<img src="/img/1f434.png" width="16" height="16">','<img src="/img/1f441.png" width="16" height="16">','<img src="/img/1f442-1f3fb.png" width="16" height="16">','<img src="/img/1f637.png" width="16" height="16">','<img src="/img/1f911.png" width="16" height="16">'];
		$str = str_replace($short_str, $imagename_list, $str);

		return $str;
	}
}

?>