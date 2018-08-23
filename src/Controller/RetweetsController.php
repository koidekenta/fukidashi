<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class RetweetsController extends AppController{

	public function addretweet(){
		$this->autoRender = false;
		$refukidashi = h($_POST["fukidashi_num"]);
		$slug = h($_POST["slug"]);
		$user_id = h($this->request->session()->read('Auth.User.id'));

		if($this->request->is("ajax")){
		if(!$user_id){ echo -3; exit; }
		$username = h($this->request->session()->read('Auth.User.username'));

		# すで同じものがリツイートされていないかチェックする
		$check = $this->Retweets->find()->where(['AND' => [["user_id" => $user_id], ["retweetsid" => $refukidashi],["retweets_username" => $username]]])->count();

		# その記事は存在するのかチェックする
		$posts = TableRegistry::get('Posts');

		$t = $posts->get($refukidashi);

			if($check === 0 and $this->request->is("ajax") and $t !== null){
				
				$usertbls = TableRegistry::get('Retweets');
				$query = $usertbls->query();
				$query->insert(['user_id','retweetsid','retweets_username','created','modified'])
				->values(['user_id' => $user_id, 'retweetsid' => $refukidashi, 'retweets_username' => $username,'created' => date("Y-m-d H:i:s"), 'modified' => date("Y-m-d H:i:s")])
				->execute();

				# postsテーブルのrefukidashi_numを1追加
				$this->poststbls = TableRegistry::get('Posts');
				$rst = $this->poststbls->findBySlug($slug)->firstOrFail();
				$rst->refukidashi_num = $rst->refukidashi_num + 1;
				$this->poststbls->save($rst);

				echo 0;
			}else{
				echo -1;
			}
		}
	}

	public function deleteretweet(){

	        $this->request->allowMethod(['post', 'delete']);
		$this->autoRender = false;
		$refukidashi = h($_POST["fukidashi_num"]);
		$slug = h($_POST["slug"]);
		$user_id = h($this->request->session()->read('Auth.User.id'));
		$username = h($this->request->session()->read('Auth.User.username'));

		if($this->request->is("ajax")){

		if(!$user_id){ echo -3; exit; }
		# すで同じものがリツイートされていないかチェックする
		$check = $this->Retweets->find()->where(['AND' => [["user_id" => $user_id], ["retweetsid" => $refukidashi],["retweets_username" => $username]]])->count();
			
			if($check === 1 and $this->request->is("ajax")){
				$p = $this->Retweets->find()->where(['AND' => [["user_id" => $user_id], ["retweetsid" => $refukidashi],["retweets_username" => $username]]])->firstOrFail();
				$this->Retweets->deleteOrFail($p);

				# postsテーブルのrefukidashi_numを1追加
				$this->poststbls = TableRegistry::get('Posts');
				$rst = $this->poststbls->findBySlug($slug)->firstOrFail();
				if($rst->refukidashi_num !== 0){
					$rst->refukidashi_num = $rst->refukidashi_num - 1;
				}
				$this->poststbls->save($rst);
				
				echo 0;

			}else{
				echo -1;
			}

		}

	}

}
