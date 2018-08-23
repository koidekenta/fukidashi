<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class FollowsController extends AppController{

     public function initialize()
     {
         parent::initialize();
         $this->testtable = TableRegistry::get('Users');
	 $this->already = TableRegistry::get('Follows');
    }

   public function addfollow(){
		$this->autoRender = false;
		$username = h($_POST["username"]);

		if($this->request->is('ajax')){

			$check = $this->testtable->findByUsername($username)->firstOrFail();
			$user_id = $this->request->session()->read('Auth.User.id');

			if(!$user_id){ echo -4; exit; }
			if((string)$user_id === (string)$check->id){ echo -3; exit; }

			$follow_id = $check->id;

			$count = $this->already->find()->where(['AND' => [['user_id' => $user_id],['follow_id' => $check->id]]])->count();

			if($count !== 0){
				exit;
			}

			$usertbls = TableRegistry::get('Follows');
			$query = $usertbls->query();
			$query->insert(['follow_id','user_id','created','modified'])
			->values(['follow_id' => $follow_id, 'user_id' => $user_id, 'created' => date("Y-m-d H:i:s"), 'modified' => date("Y-m-d H:i:s")])
			->execute();

			$usertbls = TableRegistry::get('Users');
			$rst = $usertbls->find()->where(['Users.id' => $user_id])->firstOrFail();
			$rst->follow_num = $rst->follow_num + 1;
			$usertbls->save($rst);

			$usertbls = TableRegistry::get('Users');
			$rst = $usertbls->find()->where(['Users.id' => $follow_id])->firstOrFail();
			$rst->follower_num = $rst->follower_num + 1;
			$usertbls->save($rst);

			TableRegistry::get('Alerts')->query()->insert(["user_id","who","action","created"])
			->values(["user_id" => $check->id,"who" => $this->request->session()->read('Auth.User.username'),"action" => "follow", "created" => date("Y-m-d H:i:s")])->execute();
			echo 0;
		}else{
			echo -1;
		}
   }

	public function deletefollow(){
		$this->autoRender = false;

		$username = h($_POST["username"]);

		if($this->request->is("ajax")){
			$check = $this->testtable->findByUsername($username)->firstOrFail();
			$user_id = $this->request->session()->read('Auth.User.id');
			
			if($check && $check->id !== $user_id && $this->request->is("ajax")){
				$p = $this->already->find()->where(['AND' => [['user_id' => $user_id],['follow_id' => $check->id]]])->firstOrFail();
				$this->already->deleteOrFail($p);

			# フォローする側を1つ減らす
			$usertbls = TableRegistry::get('Users');
			$rst = $usertbls->find()->where(['id' => $user_id])->firstOrFail();
			if($rst->follow_num !== 0){
				$rst->follow_num = $rst->follow_num - 1;
			}
			$usertbls->save($rst);
			# フォローされる側を1つ減らす
			$usertbls = TableRegistry::get('Users');
			$rst = $usertbls->find()->where(['id' => $check->id])->firstOrFail();
			if($rst->follower_num !== 0){
				$rst->follower_num = $rst->follower_num - 1;
			}
			$usertbls->save($rst);
				echo 0;
			}else{
				echo -1;
			}
		}
	}


}
