<?php
	class Modified_queries extends Tables{

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

		private function sort_up_by_floors($data){
			usort($data["data"], $this->build_sorter("floors_name"));

			for($x=0; $x<count($data["data"]); $x++){
				if(!isset($new_data[$data["data"][$x]["floors_id"] - 1])){
					$new_data[$data["data"][$x]["floors_id"] - 1] = array();
				}
				array_push($new_data[$data["data"][$x]["floors_id"] - 1], $data["data"][$x]);
			}

			$new_data = array_values($new_data);

			for($x=0; $x<count($new_data); $x++){
				usort($new_data[$x], $this->build_sorter("rooms_name"));
			}

			$data["data"] = array_values($new_data);

			return $data;
		}

		private function sort_up_by_day_hours($data){
			$new_data = array(
					array(), // Mon
					array(), // Tue
					array(), // Wed
					array(), // Thu
					array(), // Fri
					array(), // Sat
					array()  // Sun
				);

			for($x=0; $x<count($data["data"]); $x++){
				$days = explode(",", $data["data"][$x]["room_schedules_day"]);
				for($y=0; $y<count($days); $y++){
					$room_schedules_day = $days[$y];
					$data["data"][$x]["room_schedules_day"] = $days[$y];
					switch($room_schedules_day){
						case "Mon":
							array_push($new_data[0], $data["data"][$x]);
							break;
						case "Tue":
							array_push($new_data[1], $data["data"][$x]);
							break;
						case "Wed":
							array_push($new_data[2], $data["data"][$x]);
							break;
						case "Thu":
							array_push($new_data[3], $data["data"][$x]);
							break;
						case "Fri":
							array_push($new_data[4], $data["data"][$x]);
							break;
						case "Sat":
							array_push($new_data[5], $data["data"][$x]);
							break;
						case "Sun":
							array_push($new_data[6], $data["data"][$x]);
							break;
					}
				}
			}

			$new_data = array_values($new_data);

			$counter = 0;
			for($x=0; $x<count($new_data); $x++){
				if(!empty($new_data[$x])){
					for($y=0; $y<count($new_data[$x]); $y++){
						$new_data[$x][$y] = array_merge(
												$new_data[$x][$y],
												array(
													"room_schedules_time"=>
														$this->parse_time(
															$new_data[$x][$y]["room_schedules_start_time"],
															$new_data[$x][$y]["room_schedules_end_time"])));
					}
					usort($new_data[$x], $this->build_sorter("room_schedules_time"));
				}
			}

			$new_data = array_filter($new_data);	// delete empty arrays
			$data["data"] = array_values($new_data);

			return $data;
		}

		private function parse_time($start_time, $end_time){
			$start_time_hour = intval($start_time/60);
			$start_time_minutes = intval($start_time%60);
			$end_time_hour = intval($end_time/60);
			$end_time_minutes = intval($end_time%60);

			if($start_time_hour < 10){
				$start_time_hour = "0".$start_time_hour;
			}
			if($start_time_minutes < 10){
				$start_time_minutes = "0".$start_time_minutes;
			}
			if($end_time_hour < 10){
				$end_time_hour = "0".$end_time_hour;
			}
			if($end_time_minutes < 10){
				$end_time_minutes = "0".$end_time_minutes;
			}

			return $start_time_hour.":".$start_time_minutes." - ".$end_time_hour.":".$end_time_minutes;
		}

		public function get_the_consumption(){
			$query = "SELECT room_consumptions.rooms_id, rooms.rooms_name, room_consumptions.room_consumptions, room_consumptions.room_consumptions_datetime FROM room_consumptions LEFT JOIN rooms ON room_consumptions.rooms_id=rooms.rooms_id";
			$data = $this->mysql->personalQuery($query);
			print_r($data);
		}

		public function get($condition, $where_values, $mode=NULL){

			$where_query = "";

			switch($condition){
				case "view_ras":	// ROOM AND SCHEDULES
					$query = "SELECT room_devices.room_devices_id, room_devices.rooms_id, rooms.rooms_name, room_devices.room_devices_name, room_devices.room_devices_status, room_device_schedules.room_devices_id, room_device_schedules.room_schedules_id, room_schedules.room_schedules_name, room_schedules.room_schedules_day, room_schedules.room_schedules_date, room_schedules.room_schedules_start_time, room_schedules.room_schedules_end_time, room_schedules.room_schedules_duration, room_schedules.room_schedules_type FROM room_device_schedules LEFT JOIN room_devices ON room_device_schedules.room_devices_id=room_devices.room_devices_id LEFT JOIN room_schedules ON room_schedules.room_schedules_id=room_device_schedules.room_schedules_id LEFT JOIN rooms ON room_devices.rooms_id=rooms.rooms_id";
					if(!empty($where_values)){
						$where_query = " WHERE room_schedules.rooms_id=".$where_values['rooms_id'];
						if(isset($where_values['room_schedules_day'])){
							$where_query = $where_query." AND room_schedules.room_schedules_day='".$where_values['room_schedules_day']."'";
						}
						if(isset($where_values['room_schedules_type'])){
							$where_query = $where_query." AND room_schedules.room_schedules_type='".$where_values['room_schedules_type']."'";
						}
						unset($where_values);
						$where_values = array();
					}

					$table = "room_device_schedules";
					break;
				case "view_room_status":
					
					if($mode == 2){
						$query = "SELECT floor_room_groups.floor_room_groups_id, floor_room_groups.floors_id, floor_room_groups.rooms_id, floors.floors_name, rooms.rooms_name, rooms.rooms_status FROM floor_room_groups LEFT JOIN floors ON floor_room_groups.floors_id=floors.floors_id LEFT JOIN rooms ON floor_room_groups.rooms_id=rooms.rooms_id WHERE rooms.rooms_status=1";
					}else{
						$query = "SELECT floor_room_groups.floor_room_groups_id, floor_room_groups.floors_id, floor_room_groups.rooms_id, floors.floors_name, rooms.rooms_name, rooms.rooms_status FROM floor_room_groups LEFT JOIN floors ON floor_room_groups.floors_id=floors.floors_id LEFT JOIN rooms ON floor_room_groups.rooms_id=rooms.rooms_id";
					}
					$table = "floor_room_groups";
					break;
				default:
					break;
			}

			if(!empty($where_values)){
				$x=0;
				$where_query = " WHERE ";
				foreach($where_values as $key=>$value){
					if(($x+1) != count($where_values)){
						$where_query = $where_query.$table.".".$key."='".$value."' AND ";
					}else{
						$where_query = $where_query.$table.".".$key."='".$value."'";
					}
					$x++;
				}
			}

			$query = $query.$where_query;

			switch($condition){
				case "view_room_status":
					$data = $this->mysql->select_query(NULL, $where_values, $query);
					if(!empty($data["data"])){
						$data = $this->sort_up_by_floors($data);
					}
					break;
				case "view_ras":
					$data = $this->sort_up_by_day_hours($this->mysql->select_query(NULL, $where_values, $query));
					break;
				default:
					$data = $this->mysql->select_query(NULL, $where_values, $query);
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

		public function manualSwitch($new_values, $where_values){
			if($new_values['checked'] == 0){
				$data = $this->mysql->select($this->rs, array('rooms_id'=>$where_values['rooms_id'], 'room_schedules_type'=>1));
				$this->mysql->delete($this->rds, array('room_devices_id'=>$where_values['room_devices_id'], 'room_schedules_id'=>$data["data"][0]["room_schedules_id"]));
				$this->mysql->update($this->rd, array( array('room_devices_status'=>0), array('rooms_id'=>$where_values['rooms_id'], 'room_devices_id'=>$where_values['room_devices_id']) ));
			}else{
				date_default_timezone_set("Asia/Manila");
				$system_hour = date("H");
				$system_minute = date("i");
				$system_month = date("m");
				$system_date = date("d");
				$system_year = date("Y");
				$number_month_days = date("t");

				$duration_hour = $new_values['room_schedules_duration']/60;
				
				if(($duration_hour+$system_hour) >= 24){
					if(($system_date+1) > $number_month_days){
						$system_date = sprintf("%02d", 1);
						$system_month = sprintf("%02d", ($system_month+1));
					}
					if($system_month == 12){
						$system_month = sprintf("%02d", 1);
						$system_year += 1;
					}
					$system_hour = sprintf("%02d", (($duration_hour+$system_hour)-24));
				}

				$final_date = $system_year."-".$system_month."-".$system_date;
				$final_start_time = ($system_hour*60)+$system_minute;
				$final_end_time = $final_start_time+$new_values['room_schedules_duration'];

				$data = $this->mysql->select('room_schedules', array('rooms_id'=>$where_values['rooms_id'], 'room_schedules_type'=>$where_values['room_schedules_type']));

				if(empty($data["data"])){
					$in_id = $this->mysql->insert($this->rs, array(
						'room_schedules_name'=>'Class',
						'rooms_id'=>$where_values['rooms_id'],
						'room_schedules_date'=>$final_date,
						'room_schedules_start_time'=>$final_start_time,
						'room_schedules_end_time'=>$final_end_time,
						'room_schedules_duration'=>$new_values['room_schedules_duration'],
						'room_schedules_type'=>1));
					
					$this->mysql->update($this->rd, array(
							array('room_devices_status'=>$new_values['room_devices_status']),
							array('rooms_id'=>$where_values['rooms_id'])
						));
					$data = $this->mysql->insert($this->rds, array('room_devices_id'=>$where_values['room_devices_id'], 'room_schedules_id'=>$in_id['data']));		
				}else{
					$this->mysql->update($this->rd, array(
							array(
								'room_devices_status'=>$new_values['room_devices_status']
								),
							array(
								'rooms_id'=>$where_values['rooms_id'],
								'room_devices_id'=>$where_values['room_devices_id']
								)
						));
					$this->mysql->update($this->rs, array(
							array(
								'room_schedules_date'=>$final_date,
								'room_schedules_start_time'=>$final_start_time,
								'room_schedules_end_time'=>$final_end_time,
								'room_schedules_duration'=>$new_values['room_schedules_duration'],
								),
							array(
								'rooms_id'=>$where_values['rooms_id'],
								'room_schedules_type'=>1
								)
						));
					$data = $this->mysql->select($this->rs, array('rooms_id'=>$where_values['rooms_id'], 'room_schedules_type'=>1));
					$data1 = $this->mysql->select($this->rds, array('room_schedules_id'=>$data["data"][0]["room_schedules_id"], 'room_devices_id'=>$where_values['room_devices_id']));
					if(empty($data1["data"])){
						$this->mysql->insert($this->rds, array('room_schedules_id'=>$data["data"][0]["room_schedules_id"], 'room_devices_id'=>$where_values['room_devices_id']));
					}
				}
			}
		}

		public function set_trigger_manual($rooms_id){
			$this->add_trigger(array('table'=>$this->manual, 'id'=>$rooms_id));
		}

		public function set_trigger_device($rooms_id){
			$this->add_trigger(array('table'=>$this->rd, 'id'=>$rooms_id));
		}

		private function add_check_rooms(){
			for($x=0; $x<count($this->for_check_rooms); $x++){
				$data = $this->mysql->select($this->cr, array('check_rooms_for'=>$this->for_check_rooms[$x]));
				if(!empty($data["data"])){
					$dataret = $this->mysql->update($this->cr, array(array('check_rooms_status'=>1), array('check_rooms_for'=>$this->for_check_rooms[$x])));
				}else{
					$dataret = $this->mysql->insert($this->cr, array('check_rooms_status'=>1, 'check_rooms_for'=>$this->for_check_rooms[$x]));
				}
			}
			return $dataret;
		}

		private function add_trigger($values /* = array('table'=> '', 'mode'=> '', 'id'=> '' */){
			switch($values["table"]){
				case $this->rd:
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
				case $this->manual:
					$data = $this->mysql->insert($this->triggers, array('triggers_table_id'=>$values['id'], 'triggers_table'=>5));
					break;
			}
		}

		public function sync_data($values){
			$values['table'] = $this->rs;
			$this->add_trigger($values);
		}

		public function new_module($where, $values){
			if(!is_array($where) || !is_array($values)){
				return $this->encode_output(array("status"=>0, "message"=>"One of the parameters is not an array!", "data"=>array()));
			}else{
				foreach($where as $key=>$value){
					$where[$key] = str_replace("-", " ", $value);
				}

				foreach($values as $key=>$value){
					$values[$key] = str_replace("-", " ", $value);
				}

				$room_devices = array_values(explode(",", $values['room_devices']));
				$room_devices_port = array_values(explode(",", $values['room_devices_port']));
				if(isset($values['device_to_sched'])){
					$device_to_sched = array_map('ucfirst', array_values(explode(",", $values['device_to_sched'])));
				}

				unset($values['room_devices']);
				unset($values['room_devices_port']);

				foreach($room_devices_port as $key=>$value){
					switch($value){
						case "Port A":
							$room_devices_port[$key] = 5;
							break;
						case "Port B":
							$room_devices_port[$key] = 6;
							break;
						case "Port C":
							$room_devices_port[$key] = 7;
							break;
						case "Port D":
							$room_devices_port[$key] = 8;
							break;
					}
				}
				
				$data_floor = $this->mysql->select($this->floors, $where);
				$check_room = $this->mysql->select($this->rooms, array('rooms_name'=>$values['rooms_name']));
				
				if(!empty($check_room["data"])){
					return $this->encode_output(array('status'=>(-1), 'message'=>'Room name is existing in the database!', 'data'=>$check_room["data"]));
				}else{
					$data = $this->mysql->insert($this->rooms, array(
						'rooms_name'=>$values['rooms_name'],
						'rooms_address'=>$values['rooms_address'],
						'rooms_port'=>$values['rooms_port'],
						'rooms_status'=>$values['rooms_status']));
					$rooms_id = $data["data"];
					$this->mysql->insert($this->frg, array('floors_id'=>$data_floor["data"][0]['floors_id'], 'rooms_id'=>$rooms_id));

					if(!( count($room_devices) == 1 && empty($room_devices[0]) ) || !( count($room_devices_port) == 1 && empty($room_devices_port[0]) )){
						for($x=0; $x<count($room_devices); $x++){
							$device_id = $this->mysql->insert($this->rd, array(
								'rooms_id'=>$rooms_id, // $rooms_id
								'room_devices_name'=>ucfirst($room_devices[$x]),
								'room_devices_port'=>$room_devices_port[$x],
								'room_devices_status'=>0))["data"];
						}
		
						if(isset($values['room_schedules_name'])){
							$sched_days = array_values(explode(",", $values['room_schedules_day']));
							$sched_start_time = array_values(explode(",", $values['room_schedules_start_time']));
							$sched_end_time = array_values(explode(",", $values['room_schedules_end_time']));
							$sched_name = array_values(array_map( 'ucwords', array_map('strtolower', array_values( explode(",", $values['room_schedules_name']) ) ) ));

							$temp_sched = array();

							for($x=0; $x<count($sched_days); $x++){
								$schedule = array(
										'room_schedules_name'=>$sched_name[$x],
										'rooms_id'=>$rooms_id, // $rooms_id
										'room_schedules_day'=>$sched_days[$x],
										'room_schedules_start_time'=>$sched_start_time[$x],
										'room_schedules_end_time'=>$sched_end_time[$x],
										'room_schedules_type'=>0
									);
								array_push($temp_sched, $schedule);
							}

							$temp_sched = array_values($temp_sched);

							for($x=0; $x<count($temp_sched); $x++){
								$temporary = $temp_sched[$x];
								$day = $temporary['room_schedules_day'];
								unset($temporary['room_schedules_day']);
								$query = $this->mysql->select($this->rs, $temporary);
								if(!empty($query["data"])){
									$new_day = $query["data"][0]["room_schedules_day"].",".$day;
									$this->mysql->update($this->rs, array( array('room_schedules_day'=>$new_day), $temporary ));
								}else{
									$schedule_id = $this->mysql->insert($this->rs, $temp_sched[$x]);
								}
							}

							$data_rs = $this->mysql->select($this->rs, array('rooms_id'=>$rooms_id));
							$data_rd = $this->mysql->select($this->rd, array('rooms_id'=>$rooms_id));

							for($x=0; $x<count($data_rs["data"]); $x++){
								for($y=0; $y<count($data_rd["data"]); $y++){
									$rds_id = $this->mysql->insert($this->rds, array('room_devices_id'=>$data_rd["data"][$y]["room_devices_id"], 'room_schedules_id'=>$data_rs["data"][$x]["room_schedules_id"]));
								}
							}
						}
					}

					$this->add_check_rooms();

					return $this->encode_output($data);
				}
			}
		}

		public function delete_room_module($where){
			$room_schedules_data = array();
			$toDelete = array('rooms_id'=>$where['rooms_id']);
			$room_address = $this->mysql->select( $this->rooms, $toDelete )["data"][0]["rooms_address"];
			$query = "SELECT room_device_schedules.room_device_schedules_id FROM room_device_schedules INNER JOIN room_devices ON room_device_schedules.room_devices_id=room_devices.room_devices_id WHERE room_devices.rooms_id=".$where['rooms_id'];
			$room_schedules_data = $this->mysql->personalQuery($query)["data"];

			$this->mysql->delete($this->frg, $toDelete);
			$this->mysql->delete($this->rc, $toDelete);

			if(!empty($room_schedules_data)){
				for($x=0; $x<count($room_schedules_data); $x++){
					$this->mysql->delete($this->rds, array('room_device_schedules_id'=>$room_schedules_data[$x]["room_device_schedules_id"]));
				}
			}

			$room_sched_id = $this->mysql->select($this->rs, $toDelete)["data"];
			$data = $this->mysql->delete($this->rd, $toDelete);
			$data = $this->mysql->delete($this->rs, $toDelete);
			$data = $this->mysql->delete($this->rrt, array('rooms_address'=>$room_address));
			$data = $this->mysql->delete($this->rooms, $toDelete);

			$this->add_check_rooms();

			return $this->encode_output(array('status'=>1, 'message'=>'Success!', 'data'=>array()));
		}

		public function get_room_details($where){
			$toData = array();
			$query = "SELECT floors.floors_id, floors.floors_name, rooms.rooms_name, rooms.rooms_address, rooms.rooms_port, rooms.rooms_status FROM floor_room_groups LEFT JOIN floors ON floor_room_groups.floors_id=floors.floors_id LEFT JOIN rooms ON floor_room_groups.rooms_id=rooms.rooms_id WHERE floor_room_groups.rooms_id=".$where['rooms_id'];
			$toData["room_details"] = $this->mysql->personalQuery($query)["data"];
			$toData["room_details"][0]["rooms_address"] = array_values(explode(" ", $toData["room_details"][0]["rooms_address"]));

			$query = "SELECT room_devices.room_devices_id, room_devices.room_devices_name, room_devices.room_devices_port, room_devices.room_devices_status FROM floor_room_groups LEFT JOIN floors ON floor_room_groups.floors_id=floors.floors_id LEFT JOIN rooms ON floor_room_groups.rooms_id=rooms.rooms_id INNER JOIN room_devices ON floor_room_groups.rooms_id=room_devices.rooms_id WHERE floor_room_groups.rooms_id=".$where['rooms_id'];
			$toData["room_devices"] = $this->mysql->personalQuery($query)["data"];
			usort($toData["room_devices"], $this->build_sorter("room_devices_port"));

			$query = "SELECT room_device_schedules.room_devices_id, room_device_schedules.room_schedules_id, room_devices.room_devices_name, room_schedules.room_schedules_name, room_schedules.room_schedules_day, room_schedules.room_schedules_start_time, room_schedules.room_schedules_end_time FROM room_device_schedules LEFT JOIN room_devices ON room_device_schedules.room_devices_id=room_devices.room_devices_id LEFT JOIN room_schedules ON room_device_schedules.room_schedules_id=room_schedules.room_schedules_id WHERE room_devices.rooms_id=".$where['rooms_id'];
			$toData["room_schedules"] = $this->mysql->personalQuery($query)["data"];

			return $this->encode_output(array("status"=>1, "message"=>"Success!", "data"=>$toData));
		}

		public function update_room($where){
			$data_curr_frg = $this->mysql->select($this->frg, array('rooms_id'=>$where['rooms_id']))["data"][0];
			$data_curr_floors = $this->mysql->select($this->floors, array('floors_id'=>$data_curr_frg["floors_id"]))["data"][0];
			$data_new_floors = $this->mysql->select($this->floors, array('floors_name'=>$where['floors_name']))["data"][0];
			
			$this->mysql->update($this->rooms, array(array('rooms_name'=>$where['rooms_name'], 'rooms_address'=>$where['rooms_address']), array('rooms_id'=>$where['rooms_id'])));
			$this->mysql->update($this->frg, array(array('floors_id'=>$data_new_floors['floors_id']), array('floor_room_groups_id'=>$data_curr_frg['floor_room_groups_id'])));

			$this->add_check_rooms();
			return $this->encode_output(array('status'=>1, 'message'=>"Success!", 'data'=>array()));
		}

		public function update_device($value){
			if(empty($value['ports']) && empty($value['device'])){
				$room_devices_data = $this->mysql->select($this->rd, array('rooms_id'=>$value['rooms_id']))["data"];

				for($x=0; $x<count($room_devices_data); $x++){
					$this->mysql->delete($this->rds, array('room_devices_id'=>$room_devices_data[$x]['room_devices_id']));
				}

				$data = $this->mysql->delete($this->rd, array('rooms_id'=>$value['rooms_id']));
			}else{
				$ports = array_values(explode(",", $value['ports']));
				$device = array_values(explode(",", $value['device']));

				for($x=0; $x<count($ports); $x++){
					switch($ports[$x]){
						case "Port A":
							$ports[$x] = 5;
							break;
						case "Port B":
							$ports[$x] = 6;
							break;
						case "Port C":
							$ports[$x] = 7;
							break;
						case "Port D":
							$ports[$x] = 8;
							break;
					}
				}
				$ports = array_values($ports);

				$data = $this->mysql->select($this->rd, array('rooms_id'=>$value['rooms_id']));
				for($x=0; $x<count($data["data"]); $x++){
					$flag=0;
					for($y=0; $y<count($ports); $y++){
						if($data["data"][$x]["room_devices_port"] == $ports[$y]){
							$flag=1;
						}
					}
					if(!$flag){
						$this->mysql->delete($this->rds, array('room_devices_id'=>$data["data"][$x]["room_devices_id"]));
						$this->mysql->delete($this->rd, array('rooms_id'=>$value['rooms_id'], 'room_devices_port'=>$data["data"][$x]["room_devices_port"]));
					}
				}
	
				for($y=0; $y<count($ports); $y++){
					$flag = 0;
					for($x=0; $x<count($data["data"]); $x++){
						if($data["data"][$x]["room_devices_port"] == $ports[$y]){
							$this->mysql->update($this->rd, array( array('room_devices_name'=>$device[$y]), array('rooms_id'=>$value['rooms_id'], 'room_devices_port'=>$data["data"][$x]["room_devices_port"]) ));
							$flag=1;
						}
					}
					if(!$flag){
						$this->mysql->insert($this->rd, array('rooms_id'=>$value['rooms_id'], 'room_devices_name'=>$device[$y], 'room_devices_port'=>$ports[$y], 'room_devices_status'=>0));
					}
				}
			}

			$this->add_trigger(array('table'=>$this->rd, 'mode'=>2, 'id'=>$value['rooms_id']));
			return $this->encode_output(array('status'=>1, 'message'=>"Success", 'data'=>NULL));
		}

		public function manage_schedule($values){
			$sched_days_not_exploded = $values['room_schedules_day'];
			$sched_days = array_values( explode( ",", $values['room_schedules_day'] ) );
			$sched_stime = $values['room_schedules_start_time'];
			$sched_etime = $values['room_schedules_end_time'];
			$sched_name = ucwords( strtolower( $values['room_schedules_name'] ) );
			$sched_name = strtr($sched_name, array("-"=>" "));

			$flag_device = 0;
			if(!strcmp($values['devices'], "NULL")){
				$flag_device = 1;
			}else{
				$sched_devices = array_values( explode( ",", $values['devices'] ) );
			}

			$rooms_id = $values['rooms_id'];

			if(!$flag_device){
				$room_devices_id = array();
				for($x=0; $x<count($sched_devices); $x++){
					$data = $this->mysql->select($this->rd, array('rooms_id'=>$rooms_id, 'room_devices_name'=>$sched_devices[$x]));
					array_push($room_devices_id, $data["data"][0]["room_devices_id"]);
				}
				$room_devices_id = array_values($room_devices_id);
			}

			$notOkay = 0;
			for($x=0; $x<count($sched_days); $x++){
				$query = "SELECT * FROM room_schedules WHERE room_schedules_day LIKE '%".$sched_days[$x]."%' AND ".($sched_stime+1)." BETWEEN room_schedules_start_time AND room_schedules_end_time AND rooms_id=".$rooms_id;
				$data = $this->mysql->personalQuery($query);

				if(!empty($data["data"])){
					$notOkay++;
				}
			}

			if($notOkay){
				$data = array('status'=>-1, 'message'=>"A schedule is in conflict!", 'data'=>array());
			}else{
				$data = $this->mysql->insert($this->rs, array('rooms_id'=>$rooms_id, 'room_schedules_day'=>$sched_days_not_exploded, 'room_schedules_start_time'=>$sched_stime, 'room_schedules_end_time'=>$sched_etime, 'room_schedules_type'=>0, 'room_schedules_name'=>$sched_name) );
				$this->add_trigger(array('table'=>$this->rs, 'mode'=>1, 'id'=>$data["data"]));
				if(!$flag_device){
					for($x=0; $x<count($room_devices_id); $x++){
						$data_device_sched = $this->mysql->select($this->rds, array('room_schedules_id'=>$data["data"], 'room_devices_id'=>$room_devices_id[$x]) );
						if(empty($data_device_sched["data"])){
							$return = $this->mysql->insert($this->rds, array('room_schedules_id'=>$data["data"], 'room_devices_id'=>$room_devices_id[$x]));
						}
					}
				}
			}

			return $this->encode_output($data);
		}

		public function update_manage_schedule($values){
			$sched_days_not_exploded = $values['room_schedules_day'];
			$sched_days = array_values( explode( ",", $values['room_schedules_day'] ) );
			$sched_stime = $values['room_schedules_start_time'];
			$sched_etime = $values['room_schedules_end_time'];
			$sched_name = ucwords( strtolower( $values['room_schedules_name'] ) );
			$sched_name = strtr($sched_name, array("-"=>" "));

			$flag_device = 0;
			if(!strcmp($values['devices'], "NULL")){
				$flag_device = 1;
			}else{
				$sched_devices = array_values( explode( ",", $values['devices'] ) );
			}

			for($x=0; $x<count($sched_devices); $x++){
				$sched_devices[$x] = strtr($sched_devices[$x], array("-"=>" "));
			}

			$rooms_id = $values['rooms_id'];

			if(!$flag_device){
				$room_devices_id = array();
				for($x=0; $x<count($sched_devices); $x++){
					$data = $this->mysql->select($this->rd, array('rooms_id'=>$rooms_id, 'room_devices_name'=>$sched_devices[$x]));
					array_push($room_devices_id, $data["data"][0]["room_devices_id"]);
				}
				$room_devices_id = array_values($room_devices_id);
			}

			$notOkay = 0;
			for($x=0; $x<count($sched_days); $x++){
				$query = "SELECT * FROM room_schedules WHERE room_schedules_day LIKE '%".$sched_days[$x]."%' AND ".($sched_stime+1)." BETWEEN room_schedules_start_time AND room_schedules_end_time AND rooms_id=".$rooms_id;
				$data = $this->mysql->personalQuery($query);

				for($y=0; $y<count($data["data"]); $y++){
					if($data["data"][$y]["room_schedules_id"] != $values["room_schedules_id"]){
						$notOkay++;
					}
				}
			}

			if($notOkay){
				$data = array('status'=>-1, 'message'=>"A schedule is in conflict!", 'data'=>array());
			}else{
				$data = $this->mysql->select($this->rs, array('room_schedules_id'=>$values['room_schedules_id']));
				for($x=0; $x<count($sched_days); $x++){
					if(strpos($data["data"][0]["room_schedules_day"], $sched_days[$x]) === false){	// if day does not exist in the current days
						$new_day = $data["data"][0]["room_schedules_day"].",".$sched_days[$x];	// append the new day, then update
						$this->mysql->update($this->rs, array( array('room_schedules_day'=>$new_day, 'room_schedules_start_time'=>$sched_stime, 'room_schedules_end_time'=>$sched_etime), array('room_schedules_id'=>$values['room_schedules_id']) ));
					}
				}
				$this->mysql->update($this->rs, array( array('room_schedules_name'=> $sched_name, 'room_schedules_start_time'=>$sched_stime, 'room_schedules_end_time'=>$sched_etime), array('room_schedules_id'=>$values['room_schedules_id']) ));

				$rds_id = $this->mysql->select($this->rds, array('room_schedules_id'=>$values['room_schedules_id']))["data"];
				$this->mysql->delete($this->rds, array('room_schedules_id'=>$values['room_schedules_id']));
				if(!$flag_device){
					for($x=0; $x<count($room_devices_id); $x++){
						$data_device_sched = $this->mysql->select($this->rds, array('room_schedules_id'=>$data["data"][0]["room_schedules_id"], 'room_devices_id'=>$room_devices_id[$x]) );
						if(empty($data_device_sched["data"])){
							$return = $this->mysql->insert($this->rds, array('room_schedules_id'=>$values['room_schedules_id'], 'room_devices_id'=>$room_devices_id[$x]));
						}
					}
				}
				$this->add_trigger(array('table'=>$this->rs, 'mode'=>2, 'id'=>$rooms_id));
			}

			return $this->encode_output($data);
		}

		private function filter_consumption($data){
			$new_data = $data["data"];

			for($x=0; $x<count($new_data); $x++){
				for($y=0; $y<count($new_data[$x]); $y++){
					$new_data[$x][$y]["room_consumptions_datetime"] = date("m-d-Y", strtotime($new_data[$x][$y]["room_consumptions_datetime"]));
				}
			}

			for($x=0; $x<count($new_data); $x++){
				for($y=0; $y<count($new_data[$x]); $y++){
					for($z=0; $z<count($new_data[$x]); $z++){
						if($y != $z){
							$result = array_intersect($new_data[$x][$z], $new_data[$x][$y]);
							if(isset($result['room_consumptions_datetime']) && isset($result['rooms_name'])){
								$new_data[$x][$y]['room_consumptions'] += $new_data[$x][$z]['room_consumptions'];
								$new_data[$x][$y]['room_consumptions'] = "".$new_data[$x][$y]['room_consumptions']."";
								unset($new_data[$x][$z]);
								$new_data[$x] = array_values($new_data[$x]);
							}
						}
					}
				}
			}

			for($x=0; $x<count($new_data); $x++){
				for($y=0; $y<count($new_data[$x]); $y++){
					if(empty($new_data[$x][$y]['room_consumptions'])){
						$new_data[$x][$y]['room_consumptions'] = "NULL";
						$new_data[$x][$y]['room_consumptions_datetime'] = "NULL";
					}
				}
			}

			for($x=0; $x<count($new_data); $x++){
				usort($new_data[$x], $this->build_sorter('rooms_name'));
			}

			$data["data"] = array_values($new_data);
			return $data;
		}

		public function get_consumption($where){

			$where_values = array();

			if(isset($where['floors_name'])){
				$where_values['floors_id'] = $this->mysql->select($this->floors, array('floors_name'=>$where['floors_name']))["data"][0]["floors_id"];
			}
			if(isset($where['rooms_name'])){
				$where_values['rooms_id'] = $this->mysql->select($this->rooms, array('rooms_name'=>$where['rooms_name']))["data"][0]["rooms_id"];
			}

			$query = "SELECT floor_room_groups.floor_room_groups_id, floor_room_groups.floors_id, floor_room_groups.rooms_id, floors.floors_name, rooms.rooms_name, rooms.rooms_status, room_consumptions.room_consumptions_datetime, room_consumptions.room_consumptions FROM floor_room_groups LEFT JOIN floors ON floor_room_groups.floors_id=floors.floors_id LEFT JOIN rooms ON floor_room_groups.rooms_id=rooms.rooms_id LEFT JOIN room_consumptions ON floor_room_groups.rooms_id=room_consumptions.rooms_id";
			$table = "floor_room_groups";

			if(!empty($where_values)){
				$x=0;
				$where_query = " WHERE ";
				foreach($where_values as $key=>$value){
					if(($x+1) != count($where_values)){
						$where_query = $where_query.$table.".".$key."='".$value."' AND ";
					}else{
						$where_query = $where_query.$table.".".$key."='".$value."'";
					}
					$x++;
				}
				$query = $query.$where_query;
			}

			$data = $this->mysql->select_query(NULL, $where_values, $query);
			//print_r($data);
			if(!empty($data["data"])){
				$data = $this->sort_up_by_floors($data);
				$data = $this->filter_consumption($data);
			}

			return $this->encode_output($data);

		}

		public function get_room_schedules_plus_device($where){
			$query = "SELECT room_devices.room_devices_name FROM room_device_schedules LEFT JOIN room_devices ON room_device_schedules.room_devices_id=room_devices.room_devices_id WHERE room_device_schedules.room_schedules_id=".$where['room_schedules_id'];
			$data = $this->mysql->personalQuery($query);
			$data_sched = $this->mysql->select($this->rs, array('room_schedules_id'=>$where['room_schedules_id']))["data"][0];
			
			if(!empty($data["data"])){
				$final_data = array();
				$dummy_array = $data["data"][0];
				for($x=1; $x<count($data["data"]); $x++){
					$dummy_array["room_devices_name"] .= ",".$data["data"][$x]["room_devices_name"];
				}
				$dummy_array = array_merge($dummy_array, $data_sched);
				array_push($final_data, $dummy_array);
				$data["data"] = array_values($final_data);
			}else{
				$final_data = array();
				$dummy_array = array();
				$dummy_array["room_devices_name"] = "";
				$dummy_array = array_merge($dummy_array, $data_sched);
				array_push($final_data, $dummy_array);
				$data["data"] = array_values($final_data);
			}

			return $this->encode_output($data);
		}

		public function get_consumption_for_graph($where){
			switch($where['per_view']){
				case "1":
					$date = new DateTime($where['date']);
					$query = "SELECT HOUR(room_consumptions_datetime) AS hour, room_consumptions AS cons FROM room_consumptions WHERE DATE(room_consumptions_datetime) = '".$date->format('Y-m-d')."' ORDER BY HOUR(room_consumptions_datetime) ASC";
					$data = $this->mysql->personalQuery($query);

					$hourArray = array();
					foreach($data['data'] as $key => $value){
						if(in_array($value['hour'], $hourArray)){
							$newArray[$value['hour']]['cons'] = $value['cons'] + $newArray[$value['hour']]['cons'];
						}
						else{
							$hourArray[] = $value['hour'];
							$newArray[$value['hour']] = $value;
						}
					}

					$hour = array();
					for($x=0; $x<count($data['data']); $x++){
						array_push($hour, $data['data'][$x]['hour']);
					}

					$hour = array_values($hour);
					$countHour = array_count_values($hour);
					$newArray = array_values($newArray);

					for($x=0; $x<count($data['data']); $x++){
						if($data['data'][$x]['cons'] == 0){
							$countHour[$data['data'][$x]['hour']] -= 1;
						}
					}

					for($x=0; $x<count($newArray); $x++){
						if(isset($countHour[$newArray[$x]['hour']]) && $countHour[$newArray[$x]['hour']] != 0){
							$newArray[$x]['cons'] /= $countHour[$newArray[$x]['hour']];
						}
						$newArray[$x]['hour'] .= ":00";
					}

					$data['data'] = $newArray;

					break;
				case "2":
					$date_start = new DateTime($where['date_start']);
					$date_end = new DateTime($where['date_end']);

					$query = "SELECT DATE(room_consumptions_datetime) AS date, room_consumptions AS cons FROM room_consumptions WHERE DATE(room_consumptions_datetime) BETWEEN '".$date_start->format('Y-m-d')."' AND '".$date_end->format('Y-m-d')."' ORDER BY DATE(room_consumptions_datetime) ASC";
					$data = $this->mysql->personalQuery($query);

					$hourArray = array();
					foreach($data['data'] as $key => $value){
						if(in_array($value['date'], $hourArray)){
							$newArray[$value['date']]['cons'] = $value['cons'] + $newArray[$value['date']]['cons'];
						}
						else{
							$hourArray[] = $value['date'];
							$newArray[$value['date']] = $value;
						}
					}

					$data['data'] = array_values($newArray);
					break;
				case "3":
					print_r($where);
					$start_week = array_values(explode("-", $where['date_sweek']));
					$end_week = array_values(explode("-", $where['date_eweek']));

					$dateEnd = new DateTime($end_week[0]);
					$dateStart = new DateTime($start_week[1]);
					
					$get_interval = strtotime($end_week[0]) - strtotime($end_week[1]);
					$days_interval = $get_interval/86400;

					$intervalDays = $dateEnd->diff($dateStart)->format("%d")-1;

					$start_days = array();
					$end_days = array();

					array_push($start_days, $start_week[0]);
					array_push($end_days, $start_week[1]);
					$date_started = $start_week[0];
					$date_ended = $start_week[1];
					$date_started = strtotime($date_started);
					$date_ended = strtotime($date_ended);
					for($x=0; $x<($intervalDays/7); $x++){
						$date_started = strtotime("+7 day", $date_started);
						array_push($start_days, date('m/d/Y', $date_started));

						$date_ended = strtotime("+7 day", $date_ended);
						array_push($end_days, date('m/d/Y', $date_ended));
					}
					array_push($start_days, $end_week[0]);
					array_push($end_days, $end_week[1]);

					for($x=0; $x<count($start_days); $x++){
						$start_days[$x] = date("Y-m-d", strtotime($start_days[$x]));
						$end_days[$x] = date("Y-m-d", strtotime($end_days[$x]));
					}

					$data = array();
					
					for($x=0; $x<count($start_days); $x++){
						$query = "SELECT DATE(room_consumptions_datetime) AS date, room_consumptions AS cons FROM room_consumptions WHERE DATE(room_consumptions_datetime) BETWEEN '".$start_days[$x]."' AND '".$end_days[$x]."' ORDER BY DATE(room_consumptions_datetime) ASC";

						$data = $this->mysql->personalQuery($query);
						print_r($data);

						$hourArray = array();
						foreach($data['data'] as $key => $value){
							if(in_array($value['date'], $hourArray)){
								$newArray[$value['date']]['cons'] = $value['cons'] + $newArray[$value['date']]['cons'];
							}
							else{
								$hourArray[] = $value['date'];
								$newArray[$value['date']] = $value;
							}
						}

						array_push($data, $newArray);
					}

					break;
				default:;
			}
			return $this->encode_output($data);
		}

		public function new_schedule_day($values){
			if(isset($values['function'])){
				unset($values['function']);
			}
			$values['room_schedules_name'] = strtr($values['room_schedules_name'], array("-"=>" "));

			$flag_device = 0;
			if(!strcmp($values['devices'], "NULL")){
				$flag_device = 1;
			}else{
				$values['devices'] = strtr($values['devices'], array("-"=>" "));
				$sched_devices = array_values( explode( ",", $values['devices'] ) );
			}

			$rooms_id = $values['rooms_id'];

			if(!$flag_device){
				$room_devices_id = array();
				for($x=0; $x<count($sched_devices); $x++){
					$data = $this->mysql->select($this->rd, array('rooms_id'=>$rooms_id, 'room_devices_name'=>$sched_devices[$x]));
					array_push($room_devices_id, $data["data"][0]["room_devices_id"]);
				}
				$room_devices_id = array_values($room_devices_id);
			}

			$notOkay = 0;
			$query = "SELECT * FROM room_schedules WHERE room_schedules_date = '".$values['room_schedules_date']."' AND ".($values['room_schedules_start_time']+1)." BETWEEN room_schedules_start_time AND room_schedules_end_time AND rooms_id=".$rooms_id;
			$data = $this->mysql->personalQuery($query);

			$sched_name = $values['room_schedules_name'];
			$sched_stime = $values['room_schedules_start_time'];
			$sched_etime = $values['room_schedules_end_time'];

			if(!empty($data["data"])){
				$data = array('status'=>-1, 'message'=>"A schedule is in conflict!", 'data'=>array());
			}else{
				$data = $this->mysql->select($this->rs, array('room_schedules_name'=>$values['room_schedules_name'], 'room_schedules_date'=>$values['room_schedules_date'], 'rooms_id'=>$values['rooms_id']));	// select data
				unset($values['devices']);
				$data = $this->mysql->insert($this->rs, $values);
				if(!$flag_device){
					for($x=0; $x<count($room_devices_id); $x++){
						$data_device_sched = $this->mysql->select($this->rds, array('room_schedules_id'=>$data["data"], 'room_devices_id'=>$room_devices_id[$x]) );
						if(empty($data_device_sched["data"])){
							$return = $this->mysql->insert($this->rds, array('room_schedules_id'=>$data["data"], 'room_devices_id'=>$room_devices_id[$x]));
						}
					}
				}
			}
			
			return $this->encode_output($data);
		}

		public function edit_schedule_day($values){
			if(isset($values['function'])){
				unset($values['function']);
			}
			$values['room_schedules_name'] = strtr($values['room_schedules_name'], array("-"=>" "));

			$flag_device = 0;
			if(!strcmp($values['devices'], "NULL")){
				$flag_device = 1;
			}else{
				$values['devices'] = strtr($values['devices'], array("-"=>" "));
				$sched_devices = array_values( explode( ",", $values['devices'] ) );
			}

			$rooms_id = $values['rooms_id'];

			print_r($sched_devices);

			if(!$flag_device){
				$room_devices_id = array();
				for($x=0; $x<count($sched_devices); $x++){
					$data = $this->mysql->select($this->rd, array('rooms_id'=>$rooms_id, 'room_devices_name'=>$sched_devices[$x]));
					array_push($room_devices_id, $data["data"][0]["room_devices_id"]);
				}
				$room_devices_id = array_values($room_devices_id);
			}

			print_r($room_devices_id);

			$notOkay = 0;
			$query = "SELECT * FROM room_schedules WHERE room_schedules_name='".$values['room_schedules_name']."' AND room_schedules_date = '".$values['room_schedules_date']."' AND ".($values['room_schedules_start_time']+1)." BETWEEN room_schedules_start_time AND room_schedules_end_time AND rooms_id=".$rooms_id;
			$data = $this->mysql->personalQuery($query);

			$sched_name = $values['room_schedules_name'];
			$sched_stime = $values['room_schedules_start_time'];
			$sched_etime = $values['room_schedules_end_time'];

			if($data["data"][0]["room_schedules_id"] == $values["room_schedules_id"]) $notOkay=0;

			if($notOkay){
				$data = array('status'=>-1, 'message'=>"A schedule is in conflict!", 'data'=>array());
			}else{
				$data = $this->mysql->select($this->rs, array('room_schedules_id'=>$values['room_schedules_id']));
				$this->mysql->update($this->rs, array( array('room_schedules_name'=>$values['room_schedules_name'], 'room_schedules_date'=>$values['room_schedules_date'], 'room_schedules_start_time'=>$values['room_schedules_start_time'], 'room_schedules_end_time'=>$values['room_schedules_end_time']), array('room_schedules_id'=>$values['room_schedules_id']) ));
				$this->mysql->delete($this->rds, array('room_schedules_id'=>$values['room_schedules_id']));
				if(!$flag_device){
					for($x=0; $x<count($room_devices_id); $x++){
						$data_device_sched = $this->mysql->select($this->rds, array('room_schedules_id'=>$values['room_schedules_id'], 'room_devices_id'=>$room_devices_id[$x]) );
						if(empty($data_device_sched["data"])){
							$return = $this->mysql->insert($this->rds, array('room_schedules_id'=>$values['room_schedules_id'], 'room_devices_id'=>$room_devices_id[$x]));
						}
					}
				}
			}
			
			return $this->encode_output($data);
		}

	}
?>