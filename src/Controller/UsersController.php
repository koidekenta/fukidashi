<?php
namespace App\Controller;

use App\Controller\AppController;
use Imagine;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Utils\AppUtility;

ob_start();

class UsersController extends AppController
{
	public function initialize(){
    		parent::initialize();
    		$this->Auth->allow(['logout','add']);
    		$this->loadComponent('Paginator');
    		$this->already = TableRegistry::get('Follows');
    		$this->pposts = TableRegistry::get('Posts');
		$this->mutes = TableRegistry::get('Mutes');
	}

    public function index()
    {
	$user_id = $this->request->session()->read('Auth.User.id');

	$sql = <<<"EOF"
	select users.*, follows.user_id as followed_check from users left join follows on follows.user_id = $user_id and users.id = follows.follow_id order by users.id desc;
EOF;

	$connection = ConnectionManager::get('default');
	$results = $connection->execute($sql)->fetchAll('assoc');
			if(!empty($results)){
					$this->set("users",$results);
			}else{
					$this->set("users","");
			}			

	$this->set('title', 'ユーザー一覧');
    }

    public function view($username = null){
	$same_check = null;
	$username = h($username);
	$users = $this->Users->findByUsername($username)->firstOrFail();
	$user_id = $this->request->session()->read('Auth.User.id');

	if((string)$users->id === (string)$user_id){
		$same_check = "same";
	}

	$follows = $this->already->find()->where(['AND' => [['user_id' => $user_id],['follow_id' => $users->id]]])->count();

	$mutes = TableRegistry::get("Mutes")->find()->where(['user_id' => $user_id,'muteid' => $users->id])->count();

		$sql = <<<"EOT"

select tmp4.*, favorites.user_id as my_favorite_true from
(select tmp3.*, retweets.user_id as retweets_to_id from
(select tmp2.*, posts.created as retweet_posts_created, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id, posts.username as retweet_posts_username from
(select tmp.*, retweets.retweets_username as my_retweet_true from
(select posts.*, users.imageurl from posts inner join users on posts.user_id = users.id where user_id = $users->id and posts.is_commented = "OFF")
as tmp left join retweets on tmp.id = retweets.retweetsid and tmp.username = retweets.retweets_username and tmp.is_retweeted = 'ON')
as tmp2 left join posts on tmp2.is_retweeted = 'ON' and tmp2.retweet_slug = posts.slug)
as tmp3 left join retweets on tmp3.id = retweets.retweets_to_id and retweets.user_id = $user_id)
as tmp4 left join favorites on tmp4.slug = favorites.favorite_slug and favorites.user_id = $user_id
order by tmp4.id desc limit 20;

EOT;

	$connection = ConnectionManager::get('default');
	$results = $connection->execute($sql)->fetchAll('assoc');

		$this->koteistable = TableRegistry::get("Koteis");
		$koteis = $this->koteistable->find()->where(["user_id" => $users->id])->first();

		$kotei_posts = "";
		if($koteis){
			$kotei_posts = TableRegistry::get("Posts")->find()->contain(["Users"])->where(["Posts.user_id" => $users->id, "Posts.slug" => $koteis->slug])->first();
		}

	$this->set('title', $users->username."さんのページ");
        $this->set('user_info', $users);
	$this->set('results',$results);
	$this->set('mutes',$mutes);
	$this->set('follows',$follows);
	$this->set('same_check',$same_check);
	$this->set('koteis',$kotei_posts);

    }

    public function add(){
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('新規登録できました。'));

                return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
            }
            $this->Flash->error(__('新規登録できました。'));
        }

	$this->set('title', "新規登録");
        $this->set(compact('user'));
    }

    public function edit()
    {
	$flug = false;
	$flug2 = false;
	$file_name = "";
	$file_name2 = "";
	$exp = "";
	$exp2 = "";

	$id  = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($id,[
	]);

        if ($this->request->is(['patch', 'post', 'put'])) {
	  if((!empty($_FILES["header_imageurl"]["tmp_name"]) and is_uploaded_file($_FILES["header_imageurl"]["tmp_name"]) ) or (!empty($_FILES["imageurl"]["tmp_name"]) && is_uploaded_file($_FILES["imageurl"]["tmp_name"])) ){
		$tmp_name = $_FILES["imageurl"]["tmp_name"];
		$filename = basename($_FILES["imageurl"]["name"]);
		$filename = hash('md5', session_id().random_int(0,99999999).$filename);
		$exp = "";

		$tmp_name2 = $_FILES["header_imageurl"]["tmp_name"];
		$filename2 = basename($_FILES["header_imageurl"]["name"]);
		$filename2 = hash('md5', session_id().random_int(0,99999999).$filename2);
		$exp2 = "";

		switch($_FILES["imageurl"]["type"]){
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

		switch($_FILES["header_imageurl"]["type"]){
			case "image/jpeg":
				$exp2 = ".jpeg";
				break;
			case "image/png":
				$exp2 = ".png";
				break;
			case "image/gif":
				$exp2 = ".gif";
				break;
			default:
				break;
		}

		if($_FILES["imageurl"]["size"] > 3000000){
			$exp = "";
		}

		if($_FILES["header_imageurl"]["size"] > 3000000){
			$exp2 = "";
		}

		if(!empty($tmp_name) && !empty($filename) && @move_uploaded_file($tmp_name, "/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename}{$exp}") ){
			$file_name = "{$filename}{$exp}";
			$flug = true;
			@chmod("/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename}{$exp}",0644);
			$imagine = new Imagine\Imagick\Imagine();
        		$size = new Imagine\Image\Box(70,70);
        		$imagine->open("/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename}{$exp}")
            		->thumbnail($size)
            		->save("/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename}{$exp}");
			@chmod("/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename}{$exp}",0644);
		}

		if(!empty($tmp_name2) && !empty($filename2) && @move_uploaded_file($tmp_name2, "/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename2}{$exp2}")){
			$file_name2 = "{$filename2}{$exp2}";
			$flug2 = true;
			@chmod("/home/users/0/pupu.jp-fukidashi/web".DS."webroot".DS."img".DS."{$filename2}{$exp2}",0644);
		}
          }

		if(!empty($file_name)){
		    	$this->request->data("imageurl",$file_name);
		}else if($this->request->getData("icon-image-none") === "true"){
		    	$this->request->data("imageurl",null);
		}else{
		    	$this->request->data("imageurl",$user->imageurl);
		}

		if(!empty($file_name2)){
		    	$this->request->data("header_imageurl",$file_name2);
		}else if($this->request->getData("header-image-none") === "true"){
		    	$this->request->data("header_imageurl",null);
		}else{
		    	$this->request->data("header_imageurl",$user->header_imageurl);
		}

            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                return $this->redirect(['action' => 'timeline']);
            }
        }

	$this->set('title', "プロフィール編集");
        $this->set(compact('user'));
    }

	public function timeline(){
		
		$timeline = "";
		$follower_id = $this->request->session()->read('Auth.User.id');
		$li = $this->already->find()->select(['follow_id'])->where(['user_id' => $follower_id])->extract('follow_id')->toArray();
		array_push($li,$follower_id);
		$follower_list = implode(",",$li);

		$retweet_list = $follower_list;

		$mute_list = $this->mutes->find()->select(['muteid'])->where(['user_id' => $follower_id])->extract('muteid')->toArray();
		$mute_list = AppUtility::arr($mute_list);
		$sql = <<<"EOT"

		select tmp5.*,retweets.user_id as retweets_to_id from
		(select tmp4.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id, users.username as retweet_posts_username,posts.created as retweet_posts_created from
		(select tmp3.*,favorites.user_id as my_favorite_true from
		(select tmp2.*, retweets.user_id as my_retweet_true from
		(select tmp.*, users.username, users.imageurl from
		((select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, retweets_username, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts
		inner join retweets on posts.id = retweets.retweetsid where retweets.user_id in ($follower_list))
		union (select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, posts.dummy_column, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts where user_id in ($follower_list) and posts.is_commented = 'OFF')) as tmp inner join users on tmp.user_id = users.id
		group by tmp.id) as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id =$follower_id) as tmp3
		left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $follower_id) as tmp4 left join posts on tmp4.is_retweeted = 'ON' and tmp4.retweet_slug = posts.slug left join users on posts.user_id = users.id where tmp4.user_id not in $mute_list) as tmp5 left join retweets on tmp5.id = retweets.retweets_to_id and retweets.user_id =$follower_id where tmp5.user_id not in $mute_list order by tmp5.id desc limit 20;
EOT;

		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');

		$this->koteistable = TableRegistry::get("Koteis");
		$koteis = $this->koteistable->find()->where(["user_id" => $follower_id])->first();

		$kotei_posts = "";
		if($koteis){
			$kotei_posts = TableRegistry::get("Posts")->find()->contain(["Users"])->where(["Posts.user_id" => $follower_id, "Posts.slug" => $koteis->slug])->first();
		}

		$user_info = $this->Users->get($follower_id);

		$this->set('results',$results);
		$this->set('title', 'タイムライン');
		$this->set('koteis',$kotei_posts);
		$this->set('user_info',$user_info);

	}

	public function leadfind(){
		$this->autoRender = false;
		$created = AppUtility::h_plus($_POST["created"]);
		$created = "'".$created."'";
		$mode = AppUtility::h_plus($_POST["mode"]);
		$username = AppUtility::h_plus($_POST["username"]);
		$keyword = AppUtility::h_plus($_POST["keyword"]);
		$user_id = $this->request->session()->read('Auth.User.id');
		if($this->request->is("ajax")){
			$sql = "";

			# フォロワーリスト
			$li = $this->already->find()->select(['follow_id'])->where(['user_id' => $user_id])->extract('follow_id')->toArray();
			array_push($li,$user_id);
			$follower_list = implode(",",$li);

			# ミュートリスト
			$mute_list = $this->mutes->find()->select(['muteid'])->where(['user_id' => $user_id])->extract('muteid')->toArray();
			$mute_list = AppUtility::arr($mute_list);

			if($mode === "search_mode"){


				$keywords = AppUtility::k_sql($keyword);

				$sql = <<<"EOT"

select tmp3.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id,posts.username as retweet_posts_username,posts.created as retweet_posts_created from
(select tmp2.*, favorites.user_id as my_favorite_true from
(select tmp.*, retweets.user_id as retweets_to_id from
(select posts.id, posts.user_id, posts.username, users.imageurl, posts.slug, posts.post, posts.post_img, posts.is_retweeted, posts.retweet_slug, posts.created, posts.comment_num, posts.refukidashi_num, posts.favorite_num from posts inner join users on posts.user_id = users.id where $keywords)
as tmp left join retweets on tmp.id = retweets.retweets_to_id and retweets.user_id = $user_id)
as tmp2 left join favorites on tmp2.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp3 left join posts on tmp3.is_retweeted = 'ON' and tmp3.retweet_slug = posts.slug where tmp3.created > $created order by tmp3.id desc;

EOT;
			}else if($mode === "timeline_mode"){

				$sql = <<<"EOT"
select tmp5.*, retweets.user_id as retweets_to_id from
(select tmp4.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id, users.username as retweet_posts_username,posts.created as retweet_posts_created from
(select tmp3.*,favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as my_retweet_true from
(select tmp.*, users.username, users.imageurl from
((select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, retweets_username, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts
inner join retweets on posts.id = retweets.retweetsid where retweets.user_id in ($follower_list))
union (select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, posts.dummy_column, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts where user_id in ($follower_list) and posts.is_commented = 'OFF')) as tmp inner join users on tmp.user_id = users.id
group by tmp.id) as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id =$user_id) as tmp3
left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id) as tmp4 left join posts on tmp4.is_retweeted = 'ON' and tmp4.retweet_slug = posts.slug left join users on posts.user_id = users.id) as tmp5 left join retweets on tmp5.id = retweets.retweets_to_id and retweets.user_id =$user_id where tmp5.user_id not in $mute_list and tmp5.created > $created order by tmp5.id desc;

EOT;
			}else if($mode === "user_mode"){

			$users = $this->Users->findByUsername($username)->firstOrFail();

				$sql = <<<"EOT"

select tmp4.*, favorites.user_id as my_favorite_true from
(select tmp3.*, retweets.user_id as retweets_to_id from
(select tmp2.*, posts.created as retweet_posts_created, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id, posts.username as retweet_posts_username from
(select tmp.*, retweets.retweets_username as my_retweet_true from
(select posts.*, users.imageurl from posts inner join users on posts.user_id = users.id where user_id = $users->id and posts.is_commented = "OFF")
as tmp left join retweets on tmp.id = retweets.retweetsid and tmp.username = retweets.retweets_username and tmp.is_retweeted = 'ON')
as tmp2 left join posts on tmp2.is_retweeted = 'ON' and tmp2.retweet_slug = posts.slug)
as tmp3 left join retweets on tmp3.id = retweets.retweets_to_id and retweets.user_id = $user_id)
as tmp4 left join favorites on tmp4.slug = favorites.favorite_slug and favorites.user_id = $user_id
where tmp4.created > $created order by tmp4.id desc limit 20;
EOT;

			}else{
				$sql = "";
			}

			$connection = ConnectionManager::get('default');
			$results = $connection->execute($sql)->fetchAll('assoc');

			if(!empty($results)){
				$t = AppUtility::r_($results,$mode);
				echo $t;
			}else{
				echo -1;
			}
		}
	}

	public function endtimeline(){
		$this->autoRender = false;

		$p = h($_GET["page"]);
		$mode = h($_GET["mode"]);
		$username = h($_GET["username"]);
		$keyword = h($_GET["keyword"]);

		if($this->request->is("ajax")){

				if($p === "データはここでおしまいです"){
					print "end"; exit;
				}
				if(!empty($p) and is_numeric($p)){
					$offset = AppUtility::offr($p);

					$result = "";
					$follower_id = $this->request->session()->read('Auth.User.id');
					if(empty($follower_id)){ echo -4; exit; }
					$authuserusername = $this->request->session()->read('Auth.User.username');
					if(empty($authuserusername)){ echo -4; exit; }
					$user_id = $follower_id;
					$users = "";
					if( !empty($username) ){
						$users = $this->Users->findByUsername($username)->firstOrFail();
					}
					$li = $this->already->find()->select(['follow_id'])->where(['user_id' => $follower_id])->extract('follow_id')->toArray();
					array_push($li,$follower_id);

		$follower_list = implode(",",$li);

		$retweet_list = $follower_list;

		$mute_list = $this->mutes->find()->select(['muteid'])->where(['user_id' => $follower_id])->extract('muteid')->toArray();
		$mute_list = AppUtility::arr($mute_list);

		$sql = "";
if($mode === "timeline_mode"){
					$sql = <<<"EOT"

select tmp5.*,retweets.user_id as retweets_to_id from
(select tmp4.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id, users.username as retweet_posts_username,posts.created as retweet_posts_created from
(select tmp3.*,favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as my_retweet_true from
(select tmp.id, tmp.user_id, tmp.post, tmp.slug, tmp.post_img, tmp.retweets_username, tmp.is_retweeted, tmp.retweet_slug, tmp.comment_num, tmp.refukidashi_num, tmp.favorite_num, tmp.created, users.username, users.imageurl from
((select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, retweets_username, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts
inner join retweets on posts.id = retweets.retweetsid where retweets.user_id in ($follower_list))
union (select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, posts.dummy_column, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts where user_id in ($follower_list) and posts.is_commented = 'OFF')) as tmp inner join users on tmp.user_id = users.id
group by tmp.id) as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id =$follower_id) as tmp3
left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $follower_id) as tmp4 left join posts on tmp4.is_retweeted = 'ON' and tmp4.retweet_slug = posts.slug left join users on posts.user_id = users.id) as tmp5 left join retweets on tmp5.id = retweets.retweets_to_id and retweets.user_id = $follower_id where tmp5.user_id not in $mute_list order by tmp5.id desc limit 20 offset $offset;

EOT;
}else if($mode === "user_mode"){

		$sql = <<<"EOT"

select tmp5.*,retweets.user_id as retweets_to_id from
(select tmp4.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id,posts.username as retweet_posts_username,posts.created as retweet_posts_created from
(select tmp3.*,favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as my_retweet_true from
(select tmp.*, users.username, users.imageurl from
((select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, retweets_username, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts
inner join retweets on posts.id = retweets.retweetsid where retweets.user_id in ($users->id))
union (select posts.id, posts.user_id, posts.post, posts.slug, posts.post_img, posts.dummy_column, posts.is_retweeted, posts.retweet_slug, posts.comment_num, posts.refukidashi_num, posts.favorite_num, posts.created from posts where user_id in ($users->id))) as tmp inner join users on tmp.user_id = users.id
group by tmp.id) as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id = $user_id) as tmp3
left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id) as tmp4 left join posts on tmp4.is_retweeted = 'ON' and tmp4.retweet_slug = posts.slug) as tmp5 left join retweets on tmp5.id = retweets.retweets_to_id and retweets.user_id =$user_id order by tmp5.id desc limit 20 offset $offset;
EOT;

}else if($mode === "search_mode"){

		$keywords = AppUtility::k_sql($keyword);
		$sql = <<<"EOT"

select tmp3.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id,posts.username as retweet_posts_username,posts.created as retweet_posts_created from
(select tmp2.*, favorites.user_id as my_favorite_true from
(select tmp.*, retweets.user_id as retweets_to_id from
(select posts.id, posts.user_id, posts.username, users.imageurl, posts.slug, posts.post, posts.post_img, posts.is_retweeted, posts.retweet_slug, posts.created, posts.comment_num, posts.refukidashi_num, posts.favorite_num from posts inner join users on posts.user_id = users.id where $keywords)
as tmp left join retweets on tmp.id = retweets.retweets_to_id and retweets.user_id = $user_id)
as tmp2 left join favorites on tmp2.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp3 left join posts on tmp3.is_retweeted = 'ON' and tmp3.retweet_slug = posts.slug order by tmp3.id desc limit 20 offset $offset;


EOT;

}else if($mode === "reply_mode"){

		$sql = <<<"EOT"

select tmp4.*, posts.username as from_comment_username from
(select tmp3.*, favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as retweets_to_id from
(select tmp.*, users.imageurl from
(select * from posts where is_commented = 'ON' and user_id = $users->id) as tmp inner join users on tmp.user_id = users.id)
as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp3 left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp4 left join posts on tmp4.comment_slug = posts.slug order by tmp4.id desc limit 20 offset $offset;

EOT;

}else if($mode === "favorite_mode"){

		$sql = <<<"EOT"
select tmp6.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id,posts.username as retweet_posts_username,posts.created as retweet_posts_created from
(select tmp5.*, retweets.user_id as my_retweet_true from
(select tmp4.*, favorites.user_id as my_favorite_true from
(select tmp3.*, retweets.user_id as retweets_to_id from
(select tmp2.*,users.imageurl from
(select posts.id, tmp.user_id, posts.username, tmp.slug, posts.post, posts.post_img, posts.is_retweeted, posts.retweet_slug, posts.created, posts.comment_num, posts.refukidashi_num, posts.favorite_num from
(select favorites.user_id, favorites.favorite_slug as slug from favorites where favorites.user_id = $users->id) as tmp inner join posts on tmp.slug = posts.slug) 
as tmp2 inner join users on tmp2.user_id = users.id) 
as tmp3 left join retweets on tmp3.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp4 left join favorites on tmp4.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp5 left join retweets on tmp5.user_id = retweets.retweetsid and retweets.user_id = $users->id)
as tmp6 left join posts on tmp6.is_retweeted = 'ON' and tmp6.retweet_slug = posts.slug order by tmp6.id desc limit 20 offset $offset;
EOT;

}else if($mode === "follow_mode"){
		$sql = <<<"EOT"
		select * from follows where follows.user_id = $users->id order by follows.id desc limit 20 offset $offset;
EOT;

}else if($mode === "follower_mode"){
		$sql = <<<"EOT"
		select * from follows where follows.follow_id = $users->id order by follows.id desc limit 20 offset $offset;
EOT;

}else if($mode === "media_mode"){

		$sql = <<<"EOT"
select tmp3.*, favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as retweets_to_id from
(select tmp.id, tmp.user_id, tmp.username, users.imageurl, tmp.slug, tmp.post, tmp.post_img, tmp.is_retweeted, tmp.retweet_slug, tmp.created, tmp.comment_num, tmp.refukidashi_num, tmp.favorite_num from 
(select posts.id, posts.user_id, posts.username, posts.slug, posts.post, posts.post_img, posts.is_retweeted, posts.retweet_slug, posts.created, posts.comment_num, posts.refukidashi_num, posts.favorite_num from posts where post_img <> '' and user_id = $users->id) as tmp inner join users on tmp.user_id = users.id)
as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp3 left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id order by tmp3.id desc limit 20 offset $offset;
EOT;

}else if($mode === "reply_mode"){

		$sql = <<<"EOT"
select tmp4.*, posts.username as from_comment_username from
(select tmp3.*, favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as retweets_to_id from
(select tmp.*, users.imageurl from
(select * from posts where is_commented = 'ON' and user_id = $users->id) as tmp inner join users on tmp.user_id = users.id)
as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp3 left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp4 left join posts on tmp4.comment_slug = posts.slug order by tmp4.id desc limit 20 offset $offset;

EOT;

}else{
	$sql = "";

}

		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');

					if(!empty($results)){

						if($mode !== "follower_mode" and $mode !== "follow_mode"){
							$t = AppUtility::r_($results,$mode,$authuserusername);
						}else{
						}
						print $t;
					}else{
						print "end";
					}
				}
		}

	}

public function login()
{
    if ($this->request->is('post')) {
        $user = $this->Auth->identify();
        if ($user) {
            	$this->Auth->setUser($user);
            	return $this->redirect($this->Auth->redirectUrl(['controller' => 'Users','action' => 'timeline']));
		$this->Flash->success('ログインしました。');
        }
        $this->Flash->error('あなたのユーザー名またはパスワードが不正です。');
    }

	$this->set('title', "ログイン");
}

public function logout()
{
    $this->Flash->success('ログアウトしました。');
    $this->Auth->logout();
    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
}

	public function favorite($username = null){
		$username = h($username);
		if(!empty($username)){
			$same_check = null;
			$users = $this->Users->findByUsername($username)->firstOrFail();
			$user_id = $this->request->session()->read('Auth.User.id');

			$follows = $this->already->find()->where(['AND' => [['user_id' => $user_id],['follow_id' => $users->id]]])->count();

			$mutes = TableRegistry::get("Mutes")->find()->where(['user_id' => $user_id,'muteid' => $users->id])->count();

			if((string)$users->id === (string)$user_id){
				$same_check = "same";
			}

			$sql = <<<"EOT"

select tmp6.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id,posts.username as retweet_posts_username,posts.created as retweet_posts_created from
(select tmp5.*, retweets.user_id as my_retweet_true from
(select tmp4.*, favorites.user_id as my_favorite_true from
(select tmp3.*, retweets.user_id as retweets_to_id from
(select tmp2.*, users.imageurl from
(select posts.id, tmp.user_id, posts.username, tmp.slug, posts.post, posts.post_img, posts.is_retweeted, posts.retweet_slug, posts.created, posts.comment_num, posts.refukidashi_num, posts.favorite_num from
(select favorites.user_id, favorites.favorite_slug as slug from favorites where favorites.user_id = $users->id) as tmp inner join posts on tmp.slug = posts.slug) 
as tmp2 inner join users on tmp2.user_id = users.id) 
as tmp3 left join retweets on tmp3.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp4 left join favorites on tmp4.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp5 left join retweets on tmp5.user_id = retweets.retweetsid and retweets.user_id = $users->id)
as tmp6 left join posts on tmp6.is_retweeted = 'ON' and tmp6.retweet_slug = posts.slug order by tmp6.id desc limit 20;

EOT;

			$connection = ConnectionManager::get('default');
			$results = $connection->execute($sql)->fetchAll('assoc');

			$this->set("results",$results);
		        $this->set('user_info', $users);
			$this->set('follows',$follows);
			$this->set('mutes',$mutes);
			$this->set('same_check',$same_check);
			$this->set("title","お気に入り");
		}
	}

	public function follower($username = null){
		$username = h($username);
		if(!empty($username)){
			$same_check = null;
			$users = $this->Users->findByUsername($username)->firstOrFail();
			$user_id = $this->request->session()->read('Auth.User.id');

			$follows = $this->already->find()->where(['AND' => [['Follows.user_id' => $user_id],['Follows.follow_id' => $users->id]]])->count();

			$mutes = TableRegistry::get("Mutes")->find()->where(['Mutes.user_id' => $user_id,'Mutes.muteid' => $users->id])->count();

			if((string)$users->id === (string)$user_id){
				$same_check = "same";
			}

			$sql = <<<"EOT"
select tmp.user_id, users.username, users.imageurl, users.header_imageurl, users.prof from
(select follows.user_id from follows where follows.follow_id = $users->id)
as tmp inner join users on tmp.user_id = users.id order by tmp.user_id desc limit 20;
EOT;

		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');

		$this->set("title",$username."さんのフォロワー");
		$this->set("user_info",$users);
		$this->set("results",$results);
		$this->set("follows",$follows);
		$this->set("mutes",$mutes);
		$this->set("same_check",$same_check);
		}
	}

	public function follow($username = null){
		$username = h($username);
		if(!empty($username)){
			$same_check = null;
			$users = $this->Users->findByUsername($username)->firstOrFail();
			$user_id = $this->request->session()->read('Auth.User.id');

			$follows = $this->already->find()->where(['AND' => [['Follows.user_id' => $user_id],['Follows.follow_id' => $users->id]]])->count();

			$mutes = TableRegistry::get("Mutes")->find()->where(['Mutes.user_id' => $user_id,'Mutes.muteid' => $users->id])->count();

			if((string)$users->id === (string)$user_id){
				$same_check = "same";
			}

			$sql = <<<"EOT"

select tmp2.*, follows.follow_id as my_follow_true from
(select tmp.user_id,tmp.follow_id, tmp.created,users.username, users.imageurl, users.header_imageurl, users.prof from
(select follows.user_id, follows.follow_id, follows.created from follows where follows.user_id = $users->id)
as tmp inner join users on tmp.follow_id = users.id)
as tmp2 left join follows on follows.user_id = $user_id and tmp2.follow_id = follows.follow_id where tmp2.follow_id order by tmp2.created desc limit 20;
EOT;

		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');

		$this->set("title",$username."さんが、フォローしている人");
		$this->set("user_info",$users);
		$this->set("results",$results);
		$this->set("follows",$follows);
		$this->set("mutes",$mutes);
		$this->set("same_check",$same_check);
		}
	}

	public function media($username = null){
		$username = h($username);
		if(!empty($username)){

			$same_check = null;
			$users = $this->Users->findByUsername($username)->firstOrFail();
			$user_id = $this->request->session()->read('Auth.User.id');

			$follows = $this->already->find()->where(['AND' => [['user_id' => $user_id],['follow_id' => $users->id]]])->count();

			$mutes = TableRegistry::get("Mutes")->find()->where(['user_id' => $user_id,'muteid' => $users->id])->count();

			if((string)$users->id === (string)$user_id){
				$same_check = "same";
			}

			$sql = <<<"EOT"

select tmp3.*, favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as retweets_to_id from
(select tmp.*, users.imageurl from
(select posts.id, posts.user_id, posts.username, posts.slug, posts.post, posts.post_img, posts.is_retweeted, posts.retweet_slug, posts.created, posts.comment_num, posts.refukidashi_num, posts.favorite_num from posts where post_img <> '' and user_id = $users->id) as tmp inner join users on tmp.user_id = users.id)
as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp3 left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id order by tmp3.id desc limit 20;

EOT;

			$connection = ConnectionManager::get('default');
			$results = $connection->execute($sql)->fetchAll('assoc');

			$this->set("title","メディア");
			$this->set("user_info",$users);
			$this->set("results",$results);
			$this->set("follows",$follows);
			$this->set("mutes",$mutes);
			$this->set("same_check",$same_check);
		}

	}

	public function reply($username = null){
		$username = h($username);
		if(!empty($username)){

			$same_check = null;
			$users = $this->Users->findByUsername($username)->firstOrFail();
			$user_id = $this->request->session()->read('Auth.User.id');

			$follows = $this->already->find()->where(['AND' => [['user_id' => $user_id],['follow_id' => $users->id]]])->count();

			$mutes = TableRegistry::get("Mutes")->find()->where(['user_id' => $user_id,'muteid' => $users->id])->count();

			if((string)$users->id === (string)$user_id){
				$same_check = "same";
			}

			$sql = <<<"EOT"

select tmp4.*, posts.username as from_comment_username from
(select tmp3.*, favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as retweets_to_id from
(select tmp.*, users.imageurl from
(select * from posts where is_commented = 'ON' and user_id = $users->id) as tmp inner join users on tmp.user_id = users.id)
as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp3 left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp4 left join posts on tmp4.comment_slug = posts.slug order by tmp4.id desc limit 20;

EOT;

			$connection = ConnectionManager::get('default');
			$results = $connection->execute($sql)->fetchAll('assoc');

			$this->set("title","返信");
			$this->set("user_info",$users);
			$this->set("results",$results);
			$this->set("follows",$follows);
			$this->set("mutes",$mutes);
			$this->set("same_check",$same_check);
		}
	}

	public function getcontent(){
		$this->autoRender = false;
		if($this->request->is("ajax")){
			$slug = h($_POST["slug"]);
			$user_id = $this->request->session()->read('Auth.User.id');
			$username = $this->request->session()->read('Auth.User.username');
			if(empty($user_id)){ echo -4; return; }

			$sql = <<<"EOF"
select tmp4.*, posts.post as retweet_posts_post, posts.slug as retweet_posts_slug, posts.id as retweet_posts_id,posts.username as retweet_posts_username, posts.created as retweet_posts_created from
(select tmp3.*, favorites.user_id as my_favorite_true from
(select tmp2.*, retweets.user_id as retweets_to_id from
(select tmp.*, users.imageurl from
(select posts.* from posts where posts.slug = '$slug')
as tmp inner join users on tmp.username = users.username)
as tmp2 left join retweets on tmp2.id = retweets.retweetsid and retweets.user_id = $user_id)
as tmp3 left join favorites on tmp3.slug = favorites.favorite_slug and favorites.user_id = $user_id)
as tmp4 left join posts on tmp4.is_retweeted = "ON" and tmp4.retweet_slug = posts.slug order by tmp4.id desc;

EOF;

			$connection = ConnectionManager::get('default');
			$results = $connection->execute($sql)->fetchAll('assoc');

			$t = "";

			if(!empty($results)){
					$t = AppUtility::r_($results,"modal_mode",$username);
					$t = "<ul>".$t."</ul>";
					print $t;
			}else{
					print "end";
			}			
		}
	}

}