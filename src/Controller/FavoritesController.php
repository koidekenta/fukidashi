<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class FavoritesController extends AppController{

	public function addfavorite(){
		$this->autoRender = false;
		$slug = h($_POST["slug"]);
		$user_id = $this->request->session()->read('Auth.User.id');

		if(!$user_id){ return -4; exit; }

		$check = $this->Favorites->find()->where(['AND' => [["favorite_slug" => $slug], ["user_id" => $user_id]]])->count();

			if($check === 0 && $this->request->is("ajax")){
				
				$usertbls = TableRegistry::get('Favorites');
				$query = $usertbls->query();
				$query->insert(['favorite_slug','user_id','created','modified'])
				->values(['favorite_slug' => $slug, 'user_id' => $user_id, 'created' => date("Y-m-d H:i:s"), 'modified' => date("Y-m-d H:i:s")])
				->execute();

				$this->poststbls = TableRegistry::get('Posts');
				$rst = $this->poststbls->findBySlug($slug)->firstOrFail();
				$rst->favorite_num = $rst->favorite_num + 1;
				$this->poststbls->save($rst);

				$this->users_table = TableRegistry::get('Users');
				$bb = $this->users_table->get($user_id);
				$bb->favorite_num = $bb->favorite_num + 1;
				$this->users_table->save($bb);

				if($user_id !== $rst->user_id){
					TableRegistry::get('Alerts')->query()->insert(["user_id","who","action","post_slug","created"])
					->values(["user_id" => $rst->user_id,"who" => $this->request->session()->read('Auth.User.username'),"action" => "favorite", "post_slug" => $slug,"created" => date("Y-m-d H:i:s")])->execute();
				}
				echo 0;
			}else{
				echo -1;
			}
	}

	public function deletefavorite(){

	        $this->request->allowMethod(['post', 'delete']);
		$this->autoRender = false;
		$slug = h($_POST["slug"]);
		$user_id = $this->request->session()->read('Auth.User.id');

		$check = $this->Favorites->find()->where(['AND' => [["favorite_slug" => $slug], ["user_id" => $user_id]]])->count();

			if($check === 1 && $this->request->is("ajax")){
				$p = $this->Favorites->find()->where(['AND' => [['user_id' => $user_id],['favorite_slug' => $slug]]])->firstOrFail();
				$this->Favorites->deleteOrFail($p);

				$this->poststbls = TableRegistry::get('Posts');
				$rst = $this->poststbls->findBySlug($slug)->firstOrFail();
				if($rst->favorite_num !== 0){
					$rst->favorite_num = $rst->favorite_num - 1;
				}
				$this->poststbls->save($rst);

				$this->users_table = TableRegistry::get('Users');
				$rst = $this->users_table->get($user_id);
				if($rst->favorite_num !== 0){
					$rst->favorite_num = $rst->favorite_num - 1;
				}
				$this->users_table->save($rst);

				echo 0;
			}else{
					echo -1;
			}
	}


}
