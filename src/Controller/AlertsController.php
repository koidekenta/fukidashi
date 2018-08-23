<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class AlertsController extends AppController{
    public function index(){

	$user_id = $this->request->session()->read('Auth.User.id');
	$time = null;

	$check = TableRegistry::get("Times")->find()->where(["user_id" => $user_id, "mode" => "alert"])->count();
	if($check === 0){
		$time = date("Y-m-d H:i:s");
		TableRegistry::get("Times")->query()->insert(["user_id", "mode", "created"])
		->values(["user_id" => $user_id, "mode" => "alert", "created" => $time])->execute();
	}else{
		$dummy_var = TableRegistry::get("Times")->find()->where(["user_id" => $user_id, "mode" => "alert"])->first();
		$time = $dummy_var->created->format("Y-m-d H:i:s");
		TableRegistry::get("Times")->query()->update()
		->set(["created" => date("Y-m-d H:i:s")])->where(["user_id" => $user_id, "mode" => "alert"])->execute();
	}

	$check = TableRegistry::get("Alerts")->find()->count();

	if($check !== 0){
		$this->Alerts->query()->update()->set(["flug" => "OFF"])->where(["flug" => "ON", "user_id" => $user_id])->execute();
	}
	$entity = $this->Alerts->find()->where(["created >" => $time,  "user_id" => $user_id])->order(["id" => "asc"])->first();
	if($check !== 0 and $entity === null){
		$entity = $this->Alerts->find()->where(["user_id" => $user_id])->order(["id" => "desc"])->first();
		$entity->flug = "ON";
		$this->Alerts->save($entity);
	}else if($check !== 0){
		$entity = $this->Alerts->find()->where(["created >" => $time,"user_id" => $user_id])->order(["id" => "asc"])->first();
		$entity->flug = "ON";
		$this->Alerts->save($entity);
	}

	$sql = <<<"EOF"
		select alerts.id,alerts.user_id,alerts.who,alerts.action,alerts.post_slug,alerts.flug,alerts.created,users.imageurl, posts.post from alerts inner join users on alerts.who = users.username
		left join posts on alerts.post_slug = posts.slug where alerts.user_id = $user_id order by alerts.id desc;
EOF;

		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');

	$this->set("title","アラート");
	$this->set("time",$time);
	$this->set("results", $results);
    }

	public function alertcount(){
		$this->autoRender = false;
		$mode = h($_POST["mode"]);
		if($this->request->is("ajax")){
			$user_id = $this->request->session()->read('Auth.User.id');
			$username = $this->request->session()->read('Auth.User.username');
			if($mode !== "alert" and $mode !== "dm"){ echo -4; exit; }
			if(!$user_id){ echo -4; exit; }
			if(!$username){ echo -4; exit; }
			$dummy_var = TableRegistry::get("Times")->find()->where(["user_id" => $user_id, "mode" => $mode])->first();
			$time = "";
			if(!empty($dummy_var)){
				$time = $dummy_var->created->format("Y-m-d H:i:s");
			}else{
				if($mode === "alert"){
					TableRegistry::get("Times")->query()->insert(['user_id','mode','created'])->values(['user_id' => $user_id, 'mode' => "alert", 'created' => date("Y-m-d H:i:s")])->execute();
				}else if($mode === "dm"){
					TableRegistry::get("Times")->query()->insert(['user_id','mode','created'])->values(['user_id' => $user_id, 'mode' => "dm", 'created' => date("Y-m-d H:i:s")])->execute();
				}
				echo ""; exit;
			}

			$results = 0;
			if($mode === "alert"){
				$results = $this->Alerts->find()->where(["created >" => $time,  "user_id" => $user_id])->count();
			}else if($mode === "dm"){
				$results = TableRegistry::get("Directmessages")->find()->where(["created >" => $time,  "to_user" => $username])->count();
			}

			if($results !== 0){
				echo $results;
			}else{
				echo "";
			}

		}
	}
}
