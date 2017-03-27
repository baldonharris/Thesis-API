<?php
	class SQL_Setup{

		private $hostname;
		private $username;
		private $password;
		private $dbname;

		private $con;

		public $mysql;

		public function __construct($hostname, $username, $password, $dbname){
			$this->hostname = $hostname;
			$this->username = $username;
			$this->password = $password;
			$this->dbname = $dbname;
			$this->connect();
		}

		public function connect(){
			$this->con = mysqli_connect($this->hostname, $this->username, $this->password, $this->dbname) or exit(json_encode(array('status'=>0, 'message'=>"Unable to connect to database!", 'data'=>NULL), JSON_PRETTY_PRINT));
		}

		public function personalQuery($query){
			$result = mysqli_query($this->con, $query);
			$status = 0;
			if($result){
				$message="Success!";
				$status=1;
			}else{
				$message = array('MYSQL Error'=>mysqli_error($this->con), 'MYSQL Query'=>$query);
			}
			$data = array();
			while($row = mysqli_fetch_assoc($result)){
				array_push($data, $row);
			}
			return array('status'=>$status, 'message'=>$message, 'data'=>array_values($data));
		}

		public function insert_batch($table, $columns, $values){
			$query = "INSERT INTO ".$table." ";
			$sub_query_column = "(";
			for($x=0; $x<count($columns); $x++){
				if(($x+1) != count($columns)){
					$sub_query_column .= $columns[$x].",";
				}else{
					$sub_query_column .= $columns[$x].") VALUES ";
				}
			}
			$query .= $sub_query_column;

			for($x=0; $x<count($values); $x++){
				$sub_query_values = "(";
				for($y=0; $y<count($values[$x]); $y++){
					if(($y+1) != count($values[$x])){
						$sub_query_values .= "'".$values[$x][$y]."',";
					}else{
						$sub_query_values .= "'".$values[$x][$y]."'";
					}
				}
				if(($x+1) != count($values)){
					$sub_query_values .= "),";
				}else{
					$sub_query_values .= ")";
				}
				$query .= $sub_query_values;
			}

			$result = mysqli_query($this->con, $query);
			if($result){
				return array('status'=>1, 'message'=>'Success!', 'data'=>array());
			}else{
				return array('status'=>0, 'message'=>array('MYSQL Error'=>mysqli_error($this->con), 'MYSQL Query'=>$query), 'data'=>array());
			}
		}

		public function insert($table, $values){
			if(is_array($values)){
				$query = "INSERT INTO ".$table." (".$table."_id, ";
				$sub_query = "('', ";
				$x=0;
				$to_return=0;
				$message = "Error in Query = ";
				$status=0;
				foreach($values as $key=>$value){
					if(($x+1) != count($values)){
						$query = $query." ".$key.", ";
						$sub_query = $sub_query." '".$value."', ";
					}else{
						$query = $query." ".$key.") VALUES ";
						$sub_query = $sub_query." '".$value."')";
					}
					$x++;
				}
				$result = mysqli_query($this->con, $query.$sub_query);
				if($result){
					$to_return = mysqli_insert_id($this->con);
					$message = "Success!";
					$status = 1;
				}else{
					$message = array('MYSQL Error'=>mysqli_error($this->con), 'MYSQL Query'=>$query.$sub_query);
				}
				return array('status'=>$status, 'message'=>$message, 'data'=>$to_return);
			}else{
				return array('status'=>0, 'message'=>"Value passed is not an array!", 'data'=>NULL);
			}
		}

		public function select($table, $values = NULL){
			if(is_array($values) || $values == NULL){
				$query = "SELECT * FROM ".$table;
				if(!empty($values)){
					$query = $query." WHERE ";
					$x=0;
					foreach($values as $key=>$value){
						if(($x+1) != count($values)){
							$query = $query." ".$key." = '".$value."' AND ";
						}else{
							$query = $query." ".$key." = '".$value."'";
						}
						$x++;
					}
				}
				$result = mysqli_query($this->con, $query);
				if($result){
					if(!mysqli_affected_rows($this->con)){
						return array('status'=>0, 'message'=>"Success but no row is affected! Query = ".$query, 'data'=>array());
					}else{
						$data = array();
						while($row = mysqli_fetch_assoc($result)){
							array_push($data, $row);
						}
						return array('status'=>1, 'message'=>"Success!", 'data'=>array_values($data));
					}
				}else{
					return array('status'=>0, 'message'=>array('MYSQL Error'=>mysqli_error($this->con), 'MYSQL Query'=>$query), 'data'=>NULL);
				}
			}else{
				return array('status'=>0, 'message'=>"Value passed is not an array!", 'data'=>NULL);
			}
		}

		public function delete($table, $values){
			if(is_array($values)){
				$query = "DELETE FROM ".$table." WHERE ";
				$x=0;
				foreach($values as $key=>$value){
					if(($x+1) != count($values)){
						$query = $query." ".$key." = '".$value."' AND ";
					}else{
						$query = $query." ".$key." = '".$value."'";
					}
					$x++;
				}
				$result = mysqli_query($this->con, $query);
				if($result){
					if(!mysqli_affected_rows($this->con)){
						return array('status'=>1, 'message'=>"Success but no row is affected! Query = ".$query, 'data'=>array());
					}else{
						return array('status'=>1, 'message'=>"Delete success!", 'data'=>NULL);
					}
				}else{
					return array('status'=>0, 'message'=>array('MYSQL Error'=>mysqli_error($this->con), 'MYSQL Query'=>$query), 'data'=>NULL);
				}
			}else{
				return array('status'=>0, 'message'=>"Value passed is not an array!", 'data'=>NULL);
			}
		}

		public function update($table, $values){
			if(is_array($values)){
				if(!is_array($values[0]) || !is_array($values[1])){
					return array('status'=>0, 'message'=>"Value passed should be a 2D array!", 'data'=>NULL);
				}else{
					$query = "UPDATE ".$table." SET ";
					$sub_query = " WHERE ";
					$x=0;
					foreach($values[0] as $key=>$value){
						if(($x+1) != count($values[0])){
							$query = $query.$key."='".$value."', ";
						}else{
							$query = $query.$key."='".$value."' WHERE ";
						}
						$x++;
					}
					$x=0;
					foreach($values[1] as $key=>$value){
						if(($x+1) != count($values[1])){
							$query = $query.$key."='".$value."' AND ";
						}else{
							$query = $query.$key."='".$value."'";
						}
						$x++;
					}
				}
				$result = mysqli_query($this->con, $query);
				if($result){
					if(!mysqli_affected_rows($this->con)){
						return array('status'=>1, 'message'=>"Success but no row is affected! Query = ".$query, 'data'=>array());
					}else{
						return array('status'=>1, 'message'=>"Update success!", 'data'=>NULl);
					}
				}else{
					return array('status'=>0, 'message'=>array('MYSQL Error'=>mysqli_error($this->con), 'MYSQL Query'=>$query), 'data'=>NULL);
				}
			}else{
				return array('status'=>0, 'message'=>"Value passed is not an array!", 'data'=>NULL);
			}
		}

		public function select_query($function, $where_values, $custom_query = FALSE, $mode = NULL){
			$where_query = "";
			$order_by_frg = "";
			//"ORDER BY floor_room_groups.floors_id ASC"
			$x=0;
			$frg = 0;
			if(!empty($where_values)){
				$where_query = " WHERE ";
				foreach($where_values as $key=>$value){
					if(($x+1) != count($where_values)){
						$where_query = $where_query.$function.".".$key."='".$value."' AND ";
					}else{
						$where_query = $where_query.$function.".".$key."='".$value."'";
					}
					$x++;
				}
			}

			if($mode == 1){
				$where_query = $where_query." AND room_schedules.room_schedules_day IS NOT NULL";
			}else if($mode == 2){
				$where_query = $where_query." AND room_schedules.room_schedules_day IS NULL";
			}
			switch($function){
				case "floor_room_groups":
					$query = "SELECT floor_room_groups.floor_room_groups_id, floors.floors_id, rooms.rooms_id,floors.floors_name, rooms.rooms_name, rooms.rooms_port, rooms.rooms_status, rooms.rooms_address FROM floor_room_groups INNER JOIN floors ON floor_room_groups.floors_id=floors.floors_id INNER JOIN rooms ON floor_room_groups.rooms_id=rooms.rooms_id";
						$frg = 1;
					break;
				case "room_consumptions":
					$query = "SELECT room_consumptions.room_consumptions_id, rooms.rooms_id, room_consumptions.room_consumptions, room_consumptions.room_consumptions_datetime, rooms.rooms_name, rooms.rooms_port, rooms.rooms_status, rooms.rooms_address FROM room_consumptions INNER JOIN rooms ON room_consumptions.rooms_id=rooms.rooms_id";
					break;
				case "room_devices":
					$query = "SELECT room_devices.room_devices_id, room_devices.room_devices_name, room_devices.room_devices_port, room_devices.room_devices_status, rooms.rooms_id, rooms.rooms_name, rooms.rooms_port, rooms.rooms_status, rooms.rooms_address FROM room_devices INNER JOIN rooms ON room_devices.rooms_id=rooms.rooms_id";
					break;
				case "room_schedules":
					$query = "SELECT room_schedules.room_schedules_id, room_schedules.room_schedules_name, room_schedules.room_schedules_day, room_schedules.room_schedules_date, room_schedules.room_schedules_start_time, room_schedules.room_schedules_end_time, room_schedules.room_schedules_duration, room_schedules.room_schedules_type, rooms.rooms_id, rooms.rooms_name, rooms.rooms_port, rooms.rooms_status, rooms.rooms_address FROM room_schedules INNER JOIN rooms ON room_schedules.rooms_id=rooms.rooms_id";
					break;
				case "room_device_schedules":
				 	$query = "SELECT room_device_schedules.room_device_schedules_id, room_devices.room_devices_id, room_schedules.room_schedules_id, room_devices.rooms_id, room_devices.room_devices_name, room_devices.room_devices_port, room_devices.room_devices_status, room_schedules.room_schedules_day, room_schedules.room_schedules_date, room_schedules.room_schedules_start_time, room_schedules.room_schedules_end_time, rooms.rooms_name, rooms.rooms_port, rooms.rooms_status, rooms.rooms_address FROM room_device_schedules INNER JOIN room_devices ON room_device_schedules.room_devices_id=room_devices.room_devices_id INNER JOIN room_schedules ON room_device_schedules.room_schedules_id=room_schedules.room_schedules_id INNER JOIN rooms ON rooms.rooms_id=room_devices.rooms_id";
				 	break;
				default:
					$query = NULL;
			}
			// $query = (!$custom_query && ) ? $query.$where_query : $custom_query;
			if(!$custom_query){
				if(!$frg){
					$query = $query.$where_query;
				}else{
					$query = $query.$where_query." ".$order_by_frg;
					//echo $query;
				}
			}else{
				$query = $custom_query;
			}
			// echo $query;
			// $query = $query.$where_query;
			// echo $query;
			if(!$query){
				return array('status'=>0, 'message'=>"Error in function parameter!", 'data'=>NULL);
			}else{
				$result = mysqli_query($this->con, $query);
				if($result){
					if(!mysqli_affected_rows($this->con)){
						return array('status'=>0, 'message'=>"No data for this.", 'data'=>array());
					}else{
						$data = array();
						while($row = mysqli_fetch_assoc($result)){
							array_push($data, $row);
						}
						if(empty($data)) return array('status'=>0, 'message'=>"No data for this.", 'data'=>NULL);
						else return array('status'=>1, 'message'=>"Success!", 'data'=>array_values($data));						
					}
				}else{
					return array('status'=>0, 'message'=>array('MYSQL Error'=>mysqli_error($this->con), 'MYSQL Query'=>$query), 'data'=>NULL);
				}
			}
		}
	}
?>