<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Utils\AppUtility;
use Cake\Datasource\ConnectionManager;


ob_start();

class PostsController extends AppController{

	public function initialize(){
		parent::initialize();
		$this->already = TableRegistry::get('Favorites');
		$this->usr = TableRegistry::get('Users');
	}

	# 現在のタイムラインから定期的に最新投稿を持ってくる
	public function leadfind(){
		$this->autoRender = false;
		$created = h($_GET["created"]);
		if($this->request->is("ajax")){
			$result = $this->Posts->find()->where(["Posts.created > :created"])->bind(":created", $created, "datetime")->order(['Posts.id' => 'desc'])->toArray();
			if(!empty($result)){
				$t = "";
				for($i = 0; $i < count($result); $i++){
					$t .= '<li class="item"><div data-created="'.$result[$i]->created->format("Y-m-d H:i:s").'">'.$result[$i]->post.'</div></li>';
				}
				print $t;
			}else{
				echo "1";
			}
		}
	}

    public function add(){
	$file_name = "";
	$flug = false;
	$exp = "";
	$tmp_name = "";

        $post = $this->Posts->newEntity();
        if ($this->request->is('post')) {
		$slug = hash('md4',$this->request->session()->read('Auth.User.id').$this->request->getData('slug').mt_rand().time().mt_rand());
		$this->request->data('slug',$slug);
		$user_id = $this->request->session()->read('Auth.User.id');
		$this->request->data('user_id',$user_id);
		$username = $this->request->session()->read('Auth.User.username');
		$this->request->data('username',$username);
		$this->request->data("is_retweeted", "OFF");
		$this->request->data("retweet_slug", null);

	  if(!empty($_FILES["post_img"]["tmp_name"]) && is_uploaded_file($_FILES["post_img"]["tmp_name"])){
		$tmp_name = $_FILES["post_img"]["tmp_name"];
		$filename = basename($_FILES["post_img"]["name"]); # ファイルシステムトラバーサル対策
		$filename = hash('md5', session_id().random_int(0,99999999).$filename);

		switch($_FILES["post_img"]["type"]){
			case "image/jpeg":
				$exp = ".jpeg";
				break;
			case "image/png":
				$exp = ".png";
				break;
			case "image/gif":
				$exp = ".gif";
				break;
			default:
				break;
		}

		if($_FILES["post_img"]["size"] > 3000000){
			$exp = "";
		}

		# "/home/users/1/noor.jp-fukidashi/web/webroot/img/{$filename}{$exp}"
		if(!empty($tmp_name) && !empty($filename) && move_uploaded_file($tmp_name, "/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename}{$exp}") ){
			$file_name = "{$filename}{$exp}";
			$flug = true;
			@chmod("/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename}{$exp}",0644);
		}
          }
		if(!empty($exp) and $flug === true){
		    	$this->request->data("post_img",$file_name);
		}else{
		    	$this->request->data("post_img","");
		}

		$rst = $this->usr->get($user_id);
		$rst->fukidashi_num = $rst->fukidashi_num + 1;
		$this->usr->save($rst);

		$is_diffusion = $this->request->getData("is_diffusion");
		$diffusion_slug = $this->request->getData("diffusion_slug");

		$is_commented = $this->request->getData("is_commented");
		$comment_slug = $this->request->getData("comment_slug");

		if($is_diffusion === "ON" and $is_commented === "ON"){
			$is_diffusion = "OFF";
			$is_commented = "OFF";
			$this->request->data("is_diffusion", "OFF");
			$this->request->data("is_commented", "OFF");
		}

		if(!empty($comment_slug) and $comment_slug !== $slug){
			$check1 = $this->Posts->find()->where(["Posts.slug" => $comment_slug])->first();

			if(!empty($check1) and !empty($is_commented) and $is_commented === "ON"){
				$this->request->data("comment_slug", $comment_slug);
				$this->request->data("is_commented", "ON");

				$p = $this->Posts->find()->where(["slug" => $comment_slug])->first();
				$this->Posts->query()->update()->set(["comment_num" => $p->comment_num + 1])->where(["Posts.slug"=>$comment_slug])->execute();

				if($p->user_id !== $user_id){
					TableRegistry::get('Alerts')->query()->insert(["user_id","who","action","post_slug", "created"])
					->values(["user_id" => $p->user_id,"who" => $this->request->session()->read('Auth.User.username'),"action" => "comment", "post_slug" => $comment_slug,"created" => date("Y-m-d H:i:s")])->execute();
				}
			}else{
				$this->request->data("comment_slug", null);
			}
		}

		if(!empty($diffusion_slug) and $diffusion_slug !== $slug){
			$check1 = $this->Posts->find()->where(["Posts.slug" => $diffusion_slug])->first();
			if(!empty($check1) and isset($is_diffusion) and $is_diffusion === "ON"){
				$this->request->data("retweet_slug", $diffusion_slug);
				$this->request->data("is_retweeted", "ON");
				TableRegistry::get("Retweets")->query()->insert(["user_id","retweets_to_id", "retweetsid","retweets_username", "retweets_slug","created", "modified"])
				->values(["user_id" => $user_id, "retweets_to_id" => $check1->id, "retweetsid" => $check1->id, "retweets_username" => $username, "retweets_slug" => $slug, "created" => date("Y-m-d H:i:s"), "modified" => date("Y-m-d H:i:s")])->execute();

				$p = $this->Posts->find()->where(["slug" => $diffusion_slug])->first();
				$this->Posts->query()->update()->set(["refukidashi_num" => $p->refukidashi_num + 1])->where(["slug"=>$diffusion_slug])->execute();

				if($p->user_id !== $user_id){
					TableRegistry::get('Alerts')->query()->insert(["user_id","who","action","post_slug", "created"])
					->values(["user_id" => $p->user_id,"who" => $this->request->session()->read('Auth.User.username'),"action" => "diffusion", "post_slug" => $diffusion_slug,"created" => date("Y-m-d H:i:s")])->execute();
				}

				if($this->request->getData("post") === ""){
					$this->request->data("post", "拡散");
				}
			}
		}else{
			$this->request->data("retweet_slug", null);
		}

		TableRegistry::get("ipaddresses")->query()->insert(["ipaddress", "post_slug", "created", "modified"])->values(["ipaddress" => $_SERVER["REMOTE_ADDR"], "post_slug" => $slug, "created" => date("Y-m-d H:i:s"), "modified" => date("Y-m-d H:i:s")])->execute();

		$post = $this->Posts->patchEntity($post, $this->request->getData());

            if ($this->Posts->save($post)) {
		return $this->redirect($this->referer());
            }else{
		return $this->redirect($this->referer());
	    }
        }
    }

    public function delete()
    {
        $this->request->allowMethod(['post', 'ajax']);
	$this->autoRender = false;
	$username = h($_POST["username"]);
	$slug = h($_POST["slug"]);
	$user_id = $this->request->session()->read('Auth.User.id');
	if(!$user_id){ echo -4; exit; }
	$users = TableRegistry::get("Users")->findByUsername($username)->firstOrFail();
	if((string)$user_id !== (string)$users->id){ echo -3; exit; }
	$t = $this->Posts->find()->where(["Posts.slug" => $slug])->first();

	if(!empty($t) and $this->request->is("ajax")){
        	$post = $this->Posts->get($t->id);
        	if ($this->Posts->delete($post)) {
			echo 0;
        	} else {
			echo -1;
        	}
	}else{
		echo -2;
	}
    }

	public function search(){
		if($this->request->is('get') and !empty($_GET["q"])){

			$keyword = AppUtility::h_plus($_GET["q"]);
			$arr = AppUtility::k_sql($keyword);
			$user_id = $this->request->session()->read('Auth.User.id');
			$mute_list = TableRegistry::get("Mutes")->find()->select(['muteid'])->where(['user_id' => $user_id])->extract('muteid')->toArray();
			$mute_list = AppUtility::arr($mute_list);

			TableRegistry::get("Searches")->query()->insert(["search_keyword", "created"])->values(["search_keyword" => $keyword, "created" => date('Y-m-d H:i:s')])->execute();
			//$trend = TableRegistry::get("Searches")->find()->select(['count' => $query->func()->count('search_keyword') ])->group('search_keyword');

		$sql = <<<"EOT"
select search_keyword, count(search_keyword) as kensu from searches where created > date_add(sysdate(),interval - 1 hour) group by search_keyword order by kensu desc limit 10;
EOT;
		$connection = ConnectionManager::get('default');
		$trend = $connection->execute($sql)->fetchAll('assoc');

		$sql = <<<"EOT"

select tmp3.id, tmp3.user_id, tmp3.username, tmp3.imageurl, tmp3.slug, tmp3.post, tmp3.post_img, tmp3.is_retweeted, tmp3.retweet_slug, tmp3.created, tmp3.comment_num, tmp3.refukidashi_num, tmp3.favorite_num, tmp3.retweets_to_id, tmp3.my_favorite_true, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id,posts.username as retweet_posts_username from
(select tmp2.id, tmp2.user_id, tmp2.username, tmp2.imageurl, tmp2.slug, tmp2.post, tmp2.post_img, tmp2.is_retweeted, tmp2.retweet_slug, tmp2.created, tmp2.comment_num, tmp2.refukidashi_num, tmp2.favorite_num, tmp2.retweets_to_id, favorites.user_id as my_favorite_true from
(select tmp.id, tmp.user_id, tmp.username, tmp.imageurl, tmp.slug, tmp.post, tmp.post_img, tmp.is_retweeted, tmp.retweet_slug, tmp.created, tmp.comment_num, tmp.refukidashi_num, tmp.favorite_num, retweets.user_id as retweets_to_id from
(select posts.id, posts.user_id, posts.username, users.imageurl, posts.slug, posts.post, posts.post_img, posts.is_retweeted, posts.retweet_slug, posts.created, posts.comment_num, posts.refukidashi_num, posts.favorite_num from posts inner join users on posts.user_id = users.id where $arr)
as tmp left join retweets on tmp.id = retweets.retweets_to_id and retweets.user_id = $user_id)
as tmp2 left join favorites on tmp2.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp3 left join posts on tmp3.is_retweeted = 'ON' and tmp3.retweet_slug = posts.slug where tmp3.id not in $mute_list order by tmp3.id desc limit 20;
EOT;

		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');

		$this->set('title', "検索結果");
		$this->set('trend',$trend);
		$this->set('results',$results);

		}

		$this->set('title',"検索結果");
	}

	public function comment(){
		$this->autoRender = false;
		$slug = h($_POST["slug"]);

		if($this->request->is("ajax")){

		$username = $this->request->session()->read('Auth.User.username');
		if(empty($username)){ echo -4; return; }
			$sql = <<<"EOF"

select tmp2.*, posts.username as from_comment_username from
(select tmp.*, users.imageurl from
(select posts.* from posts where is_commented = 'ON' and comment_slug = '$slug')
as tmp inner join users on tmp.username = users.username)
as tmp2 left join posts on tmp2.comment_slug = posts.slug order by tmp2.id desc limit 20;
EOF;

		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');


					if(!empty($results)){
							$t = AppUtility::r_($results,"comment_mode",$username);
						print $t;
					}else{
						print "end";
					}
		}
	}

}
