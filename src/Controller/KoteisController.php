<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class KoteisController extends AppController
{
    public function add(){
	$this->autoRender = false;

	$username = h($_POST["username"]);
	$slug = h($_POST["slug"]);


	$this->userstable = TableRegistry::get("Users");

	$rst = $this->userstable->find()->where(["username" => $username])->firstOrFail();
	$user_id = h($this->request->session()->read('Auth.User.id'));

	$rst2 = $this->Koteis->find()->where(["slug" => $slug,"user_id" => $user_id])->count();

	$this->poststable = TableRegistry::get("Posts");
	$rst3 = $this->poststable->find()->select(["user_id"])->where(["slug" => $slug])->first();

	$posted_check = $this->Koteis->find()->where(["user_id" => $user_id])->count();

	if(!$user_id){ echo -4; exit;}
	
	if($user_id !== $rst->id){ echo -5; exit;}

	if($rst3->user_id !== $user_id){ echo -6; exit; }

	try{
		if($posted_check === 1 and $this->request->is("ajax")){
			$this->koteistable = TableRegistry::get("Koteis");
			$this->koteistable->query()->update()->set(["slug" => $slug])->where(['user_id' => $rst->id])->execute();
			echo 0;
		}else if($posted_check === 0 and $this->request->is("ajax")){
			$this->koteistable = TableRegistry::get("Koteis");
			$this->koteistable->query()->insert(['user_id','slug'])->values(['user_id' => $rst->id, 'slug' => $slug])->execute();			
			echo 0;
		}
		echo -1;
	}catch(Exception $e){
		echo -2;
	}

    }

    public function delete(){
	$this->autoRender = false;

	$username = h($_POST["username"]);
	$slug = h($_POST["slug"]);

	$rst = TableRegistry::get("Users")->find()->where(["username" => $username])->firstOrFail();
	$user_id = $this->request->session()->read('Auth.User.id');
	if($rst->id !== $user_id){ echo -3; exit;}

	$posted_check = $this->Koteis->find()->where(["user_id" => $user_id])->count();

		if($posted_check === 1 and $this->request->is("ajax")){

			$p = $this->Koteis->find()->where(["user_id" => $user_id, "slug" => $slug])->first();
			$entity = $this->Koteis->get($p->id);
			$r = $this->Koteis->delete($entity);
			
			echo 0;
		}
    }
}
