<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Timezone extends REST_Controller
{
	function isAuth(){
		$id = $this->session->userdata('id');
		return isset($id) && $id > 0;
	}

	function get_get(){
		if(!$this->isAuth()){
			$this->response(array('error' => 'no login'), 200);
		}else{
			$id = $this->session->userdata('id');
			$query = $this->db->query("SELECT * FROM timezone WHERE user_id={$id}");
			$result = $query->result();
			$data = array();
			foreach($result as $item){
				// $data->title = $item.title;
				// $data->desc = $item.desc;
				// $data->id = $item.id;
				// $data->amount = $item.amount;
				// $item->comments = $this->getComments($item->id);
			}
			$this->response(array('data' => $result), 200);
		}
	}

	function getComments($id){
		$query = $this->db->query("SELECT * from comment WHERE expenseid='{$id}' ");
		$result = $query->result();
		return $result;
	}

	// function update_post(){
	function update_put(){
		if (!$this->isAuth()) {
			$this->response(array('error' => 'no login'), 200);
		} else {
			$id = $this->put('id');
			$user_id = $this->session->userdata('id');
			$data = array(
				'name' => $this->put('name'),
				'city' => $this->put('city'),
				'timezone' => $this->put('timezone')
			);

			$this->db->where('id', $id);
			$this->db->update('timezone', $data);

			$query = $this->db->query("SELECT * from timezone WHERE id = {$id} ");
			if($query->num_rows == 1){
				$query2 = $this->db->query("SELECT * from timezone WHERE id = {$id} AND user_id = {$user_id}");
				if ($query2->num_rows == 0) {
					$this->response(array('status' => 'no auth', 'error' => 'no auth'), 200);
				}
				$result = $query->result();
				$data = $result[0];
				$this->response(array('status' => 'success', 'data' => $data), 200);
			}else{
				$this->response(array('status' => 'not exists', 'error' => 'not exists'), 200);
			}
		}
	}

	function add_post() {
		if (!$this->isAuth()) {
			$this->response(array('error' => 'no login'), 200);
		} else {
			$data = array(
				'name' => $this->post('name'),
				'city' => $this->post('city'),
				'timezone' => $this->post('timezone'),
				'user_id' => $this->session->userdata('id')
			);

			$this->db->insert('timezone', $data);
			$id = $this->db->insert_id();
			$query = $this->db->query("SELECT * from timezone WHERE id={$id} ");
			if ($query->num_rows == 1) {
				$result = $query->result();
				$data = $result[0];
				$this->response(array('status' => 'success', 'data' => $data), 200);
			}

			$this->response(array('status' => 'fail'), 200);
		}
	}

	function addComment_post(){
		if(!$this->isAuth()){
			$this->response(array('error' => 'no login'), 200);
		}else{
			$data = array(
				'expenseid' => $this->post('id'),
				'comment' => $this->post('comment'),
			);

			$this->db->insert('comment', $data);
			$id = $this->db->insert_id();
			$query = $this->db->query("SELECT * from comment WHERE id={$id} ");
			if($query->num_rows == 1){
				$result = $query->result();
				$data = $result[0];
				$this->response(array('status' => 'success', 'data' => $data), 200);
			}

			$this->response(array('status' => 'fail'), 200);
		}
	}

	// function delete_post(){
	function delete_delete(){
		if (!$this->isAuth()) {
			$this->response(array('error' => 'no login'), 200);
		} else {
			$id = $this->get('id');
			$query = $this->db->query("SELECT * from timezone WHERE id={$id} ");

			if ($query->num_rows === 0) {		//nothing to delete in the DB
				$this->response(array('status' => 'not exists'), 200);
				return;
			} else {
				$this->db->query("DELETE from timezone where id='{$id}' ");
				$this->response(array('status' => 'success'), 200);
			}
		}
	}

	function login_post(){
		$username = $this->post('username');
		$password = $this->post('password');
		$password = md5($password);

		$query = $this->db->query("SELECT username, password FROM user WHERE username='{$username}' and password='{$password}' ");
		if($query->num_rows > 0){
			$this->response(array('status' => 'successs'), 200);
		}else{
			$this->response(array('status' => 'fail'), 200);
		}
		// echo 'Total Results: ' . $query->num_rows();
	}

	function user_get()
    {
        if(!$this->get('id'))
        {
			$this->response(NULL, 400);
        }

        // $user = $this->some_model->getSomething( $this->get('id') );
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);
		
    	$user = @$users[$this->get('id')];
    	
        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }
    
    function user_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->get('limit') );
        $users = array(
			array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
			array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
		);
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}