<?php
	class User_details extends Tables{

		public function __construct($hostname, $username, $password, $dbname){
			$this->mysql = new SQL_Setup($hostname, $username, $password, $dbname);
		}

		private function encode_output($data){
			return json_encode((object)$data, JSON_PRETTY_PRINT);
		}

		public function add($new_password){
			if(empty($new_password)){
				$result = array('status'=>0, 'message'=>"Empty field is prohibited!", 'data'=>NULL);
			}else{
				$check = json_decode($this->get($new_password));
				if(count($check->data)){
					$result = array('status'=>0, 'message'=>"Password is existing in the database!", 'data'=>NULL);
				}else{
					$result = $this->mysql->insert($this->user_details, array('user_details_password'=>sha1($new_password)));
				}
			}
			return $this->encode_output($result);
		}

		public function get($password){
			$result = $this->mysql->select($this->user_details, array('user_details_password'=>sha1($password)));
			if(count($result['data']) === 0){
				$result = array('status'=>0, 'message'=>"Wrong password!", 'data'=>NULL);
			}
			return $this->encode_output($result);
		}

		public function del($password){
			$check = json_decode($this->get($password));
			if(count($check->data)){
				$result = $this->mysql->delete($this->user_details, array('user_details_password'=>sha1($password)));
				return $this->encode_output($result);
			}else{
				return $this->encode_output(array('status'=>0, 'message'=>"Password to delete does not exist!", 'data'=>NULL));
			}
		}

		private function add_trigger($values /* = array('table'=> '', 'mode'=> '', 'id'=> '' */){
			switch($values["table"]){
				case $this->rooms:
					$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>1));
					break;
				case $this->rs:
				case $this->rds:
					if($values['mode'] === 1){
						$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>2));
					}else{
						$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>3));
					}
					break;
				case $this->user_details:
					$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>4));
					break;
			}
		}

		public function upd($password, $mode=NULL){
			if($mode==NULL){
				return $this->encode_output($this->mysql->update($this->user_details, array( array('user_details_password'=>sha1($password['new_pass'])), array('user_details_password'=>sha1($password['cur_pass'])) )));
			}else{
				$id = $this->mysql->select($this->user_details, array('user_details_password'=>sha1($password['cur_pass'])))["data"];
				for($x=0; $x<count($id); $x++){
					$this->add_trigger(array('table'=>$this->user_details, 'id'=>$id[$x]['user_details_id']));
				}
				return $this->encode_output($this->mysql->update($this->user_details, array( array('user_details_pan_id'=>$password['pan_id']), array('user_details_password'=>sha1($password['cur_pass'])) )));
			}
		}

	}
?>