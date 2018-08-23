<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
class AppController extends Controller
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

	$this->loadComponent('Auth', [
	    'authorize'=> 'Controller',
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'unauthorizedRedirect' => $this->referer()
        ]);

    }

public function isAuthorized($user){
    	if($this->Auth->user('id')){
		return true;
	}else{
		return $this->redirect(['controller' => 'Users', 'action' => 'login']);
	}
}


}
