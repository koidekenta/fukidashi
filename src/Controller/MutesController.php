<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class MutesController extends AppController{
	public function addmute(){
		$this->autoRender = false;

		if($this->request->is("ajax")){
			$muteid = TableRegistry::get('Users')->find()->select(['id'])->where(['username' => h($_POST["username"])])->first();
			$user_id = h($this->request->session()->read('Auth.User.id'));

			if(!$user_id){ echo -3; exit; }
			if($user_id === $muteid->id){ echo -4; exit; }

			$check = $this->Mutes->find()->where(['AND' => [["user_id" => $user_id], ["muteid" => $muteid->id]]])->count();

			if($check === 0){
				TableRegistry::get('Mutes')->query()->insert(['user_id','muteid','created','modified'])->values(['user_id' => $user_id, 'muteid' => $muteid->id,'created' => date("Y-m-d H:i:s"), 'modified' => date("Y-m-d H:i:s")])->execute();

				echo 0;
			}else{
				echo -1;
			}
		}
	}

	public function deletemute(){
		$this->autoRender = false;

		if($this->request->is("ajax"){
			$user_id = h($this->request->session()->read('Auth.User.id'));
			$users = TableRegistry::get("Users")->find()->where(["username" => h($_POST["username"])])->first();

			if(!$user_id){ echo -3; exit; }
			if($user_id === $users->id){ echo -4; exit; }

			$check = $this->Mutes->find()->where(["user_id" => $user_id, "muteid" => $users->id])->count();

			if($check === 1){
				TableRegistry::get("Mutes")->query()->delete()->where(["user_id" => $user_id, "muteid" => $users->id])->execute();
				echo 0;
			}else{
				echo -1;
			}
		}

	}
}
