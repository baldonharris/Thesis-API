<?php
	class Room_schedules extends Tables{

		public function __construct($hostname, $username, $password, $dbname){
			$this->mysql = new SQL_Setup($hostname, $username, $password, $dbname);
		}

		private function encode_output($data){
			return json_encode((object)$data, JSON_PRETTY_PRINT);
		}

		public function add($values){
			if(!empty($values)){
				$check_room = $this->mysql->select($this->rooms, array('rooms_id'=>$values['rooms_id']));
				$days = explode(",", $values['room_schedules_day']);
				$flag = 0;

				for($x=0; $x<count($days); $x++){
					$query = "SELECT * FROM ".$this->rs." WHERE rooms_id=".$values['rooms_id']." AND room_schedules_day LIKE '%".$days[0]."%'";
					$data = $this->mysql->personalQuery($query);

					if(!empty($data["data"])){
						for($y=0; $y<count($data["data"]); $y++){
							if($values['room_schedules_start_time'] >= $data["data"][$y]["room_schedules_start_time"] && $values['room_schedules_start_time'] < $data["data"][$y]["room_schedules_end_time"]){
								$flag = 1;
							}
							if($values['room_schedules_end_time'] > $data["data"][$y]["room_schedules_start_time"] && $values['room_schedules_end_time'] <= $data["data"][$y]["room_schedules_end_time"]){
								$flag = 1;
							}
							if($flag == 1){
								break;
							}
						}
					}
					if($flag == 1){
						break;
					}
				}

				if(!count($check_room['data'])){
					return $this->encode_output(array('status'=>0, 'message'=>"Room ID does not exist in the 'room' table!", 'data'=>NULL));
				}
				if($flag){
					return $this->encode_output(array('status'=>0, 'message'=>"The schedule is in conflict with other schedule!", 'data'=>NULL));
				}
				return $this->encode_output($this->mysql->insert($this->rs, $values));
			}else{
				return $this->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
			}
		}

		private function build_sorter($key){
			return function ($a, $b) use ($key){
				return strnatcmp($a[$key], $b[$key]);
			};
		}

		public function get($where_values){
			$data_no_date = $this->mysql->select_query($this->rs, $where_values, FALSE, 1);
			$data_with_date = $this->mysql->select_query($this->rs, $where_values, FALSE, 2);

			usort($data_no_date["data"], $this->build_sorter("room_schedules_start_time"));
			usort($data_with_date["data"], $this->build_sorter("room_schedules_start_time"));

			if( $data_no_date["status"]==1 || $data_with_date["status"]==1 ){
				$data = array('status'=>1, 'message'=>'Success!', 'data'=>array_values(array_merge($data_no_date["data"], $data_with_date["data"])));
			}else{
				$data = array('status'=>0, 'message'=>'Success!', 'data'=>NULL);
			}

			return $this->encode_output($data);
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
					}else if($values['mode'] === 2){
						$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>3));
					}else{
						$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>6));
					}
					break;
				case $this->user_details:
					$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>4));
					break;
			}
		}

		public function del($where_values, $rds_id = NULL){
			$data = $this->mysql->select($this->rds, $where_values)["data"];
			$this->mysql->delete($this->rds, array('room_schedules_id'=>$where_values['room_schedules_id']));
			if(isset($where_values['rooms_id'])){
				$data = $this->mysql->select($this->rs, array('room_schedules_id'=>$where_values['room_schedules_id']))["data"];
				if($data[0]['room_schedules_type'] == 1){
					$this->add_trigger(array('table'=>$this->rs, 'mode'=>3, 'id'=>$where_values['rooms_id']));
				}else{
					$this->add_trigger(array('table'=>$this->rs, 'mode'=>2, 'id'=>$where_values['rooms_id']));
				}
			}
			return $this->encode_output($this->mysql->delete($this->rs, $where_values));
		}

		public function upd($values){
			if(!empty($values) && is_array($values)){
				if(isset($values[0]['rooms_id'])){
					$check_room_rooms = $this->mysql->select($this->rooms, array('rooms_id'=>$values[0]['rooms_id']));
					if(!count($check_room_rooms['data'])){
						return $this->encode_output(array('status'=>0, 'message'=>"Room ID does not exist in the 'room' table!", 'data'=>NULL));
					}
				}
				if(isset($values[0]['rooms_id']) && isset($values[0]['room_schedules_time']) && isset($values[0]['room_schedules_day'])){
					$check_data = $this->mysql->select($this->rs, array('rooms_id'=>$values[0]['rooms_id'], 'room_schedules_time'=>$values[0]['room_schedules_time'], 'room_schedules_day'=>$values[0]['room_schedules_day']));
					if(count($check_data['data'])){
						return $this->encode_output(array('status'=>0, 'message'=>"The schedule is in conflict with other schedule!", 'data'=>$check_data['data']));
					}
				}
				$get_data = $this->mysql->select($this->rs, array('room_schedules_id'=>$values[1]['room_schedules_id']));
				if(isset($values[0]['room_schedules_time']) && isset($values[0]['room_schedules_day'])){
					$check_data_again = $this->mysql->select($this->rs, array('rooms_id'=>$get_data['data'][0]['rooms_id'], 'room_schedules_time'=>$values[0]['room_schedules_time'], 'room_schedules_day'=>$values[0]['room_schedules_day']));
					if(count($check_data_again['data'])){
						return $this->encode_output(array('status'=>0, 'message'=>"The schedule is in conflict with other schedule!", 'data'=>$check_data_again['data']));
					}
				}
				return $this->encode_output($this->mysql->update($this->rs, $values));
			}else{
				return $this->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
			}
		}

	}
?>