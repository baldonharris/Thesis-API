<?php
	class Floor_room_groups extends Tables{

		public function __construct($hostname, $username, $password, $dbname){
			$this->mysql = new SQL_Setup($hostname, $username, $password, $dbname);
		}

		private function encode_output($data){
			return json_encode((object)$data, JSON_PRETTY_PRINT);
		}

		private function build_sorter($key){
			return function ($a, $b) use ($key){
				return strnatcmp($a[$key], $b[$key]);
			};
		}

		public function get($where_values){
			$data = $this->mysql->select_query($this->frg, $where_values);
			$new_data = array();

			if(!empty($data["data"])){
				usort($data["data"], $this->build_sorter("floors_name"));

				for($x=0; $x<count($data["data"]); $x++){
					if(!isset($new_data[$data["data"][$x]["floors_id"] - 1])){
						$new_data[$data["data"][$x]["floors_id"] - 1] = array();
					}
					array_push($new_data[$data["data"][$x]["floors_id"] - 1], $data["data"][$x]);
				}

				for($x=0; $x<count($new_data); $x++){
					usort($new_data[$x], $this->build_sorter("rooms_name"));
				}

				$data["data"] = array_values($new_data);
			}

			return $this->encode_output($data);
		}

		public function add($add_values){
			if(empty($add_values) || count($add_values) == 1){
				return $this->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
			}else{
				$check_room = $this->mysql->select($this->rooms, array('rooms_id'=>$add_values['rooms_id']));
				$check_floor = $this->mysql->select($this->floors, array('floors_id'=>$add_values['floors_id']));
				$check_data = $this->mysql->select($this->frg, $add_values);
				$check_room_exist_in_frg = $this->mysql->select($this->frg, array('rooms_id'=>$add_values['rooms_id']));
				if(!count($check_room['data']) || !count($check_floor['data'])){
					return $this->encode_output(array('status'=>0, 'message'=>'One of the parameters does not exist in the database!', 'data'=>NULL));
				}else if(count($check_data['data']) || count($check_room_exist_in_frg['data'])){
					return $this->encode_output(array('status'=>0, 'message'=>'Parameters already exist in the table!', 'data'=>NULL));
				}else{
					return $this->encode_output($this->mysql->insert($this->frg, $add_values));
				}
			}
		}

		public function del($where_values){
			return $this->encode_output($this->mysql->delete($this->frg, $where_values));
		}

	}
?>