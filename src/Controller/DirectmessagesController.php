<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use App\Utils\AppUtility;
use Cake\ORM\TableRegistry;


ob_start();

class DirectmessagesController extends AppController{

	public function add(){
		$directmessage = $this->Directmessages->newEntity();
		if($this->request->is("post")){
			$username = $this->request->session()->read('Auth.User.username');
			$this->request->data("from_user", $username);
			$directmessage = $this->Directmessages->patchEntity($directmessage, $this->request->getData());
			if($this->Directmessages->save($directmessage)){
				$this->redirect($this->referer());
			}
		}
	}

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $directmessage = $this->Directmessages->get($id);
        if ($this->Directmessages->delete($directmessage)) {
            $this->Flash->success(__('The directmessage has been deleted.'));
        } else {
            $this->Flash->error(__('The directmessage could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

	public function dmlist(){
		$this->autoRender = false;
		if($this->request->is("ajax")){
			$username = h($_POST["username"]);
			if(empty($username)){ echo -1; return; }
			$user_name = $this->request->session()->read('Auth.User.username');
			if(empty($username)){ echo -1; return; }

			$sql = <<<"EOF"
			select directmessages.*,users.imageurl from directmessages inner join users on from_user = users.username where from_user = '$username' and to_user = '$user_name' or from_user = '$user_name' and to_user = '$username';
EOF;
			$connection = ConnectionManager::get('default');
			$results = $connection->execute($sql)->fetchAll('assoc');
				
			if(!empty($results)){
				$t = "";
				for($i = 0; $i < count($results); $i++){
					if($user_name === $results[$i]["from_user"]){
						$t .= '<div class="comment-right">'.$results[$i]["message"].'<img src="/img/'.AppUtility::im_ch($results[$i]["imageurl"]).'" width="40" height="40"></div>';
					}else{
						$t .= '<div class="comment-left"><img src="/img/'.AppUtility::im_ch($results[$i]["imageurl"]).'" width="40" height="40">'.$results[$i]["message"].'</div>';
					}
				}
				echo $t;
				return;
			}else{
				echo "ダイレクトメッセージはありません。";
				return;
			}
			echo -3;
			return;
		}
		echo -4;
		return;
	}

	public function userlist(){
		$this->autoRender = false;
		$q = h($_POST["q"]);

		if($this->request->is("ajax")){
			$username = $this->request->session()->read('Auth.User.username');
			$user_id = $this->request->session()->read('Auth.User.id');
			
			if(empty($user_id) or empty($username)){ echo -4; return; }
			$sql = <<<"EOT"

select tmp.id, tmp.to_user, tmp.from_user, tmp.message, tmp.created, users.username, users.imageurl from
(select directmessages.id, directmessages.to_user, directmessages.from_user, directmessages.message, directmessages.created from directmessages where created in (select max(created) from directmessages where from_user != '$username' group by from_user))
as tmp inner join users on tmp.from_user = users.username order by tmp.id limit 20;
EOT;
			$connection = ConnectionManager::get('default');
			$results = $connection->execute($sql)->fetchAll('assoc');
			$k = "";

			TableRegistry::get("Times")->query()->update()->set(["created" => date("Y-m-d H:i:s")])->where(["user_id" => $user_id,"mode" => "dm"])->execute();

			for($i = 0; $i < count($results); $i++){
				$k .= '<div class="dm_user_item" style="cursor:pointer;" data-username="'.h($results[$i]["username"]).'"><img src="/img/'.AppUtility::im_ch($results[$i]["imageurl"]).'" width="40" height="40">'.$results[$i]["message"]."</div><hr>";
			}
			echo $k;
		}
	}
}
