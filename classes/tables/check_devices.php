<?php
	class Check_devices extends Tables{

		public function __construct($hostname, $username, $password, $dbname){
			$this->mysql = new SQL_Setup($hostname, $username, $password, $dbname);
		}

		private function encode_output($data){
			return json_encode((object)$data, JSON_PRETTY_PRINT);
		}

		public function check_devices($where){
			$data = $this->mysql->select($this->cd, $where);
			$this->mysql->delete($this->cd, $where);
			$string = "python2 /home/pi/Documents/Thesis_Python/change_room_devices_status.py ".$where['rooms_id'];
			$result = exec($string);
			return $this->encode_output($data);
		}

		public function add_device_status($values){
			return $this->encode_output($this->mysql->insert($this->cd, $values));
		}

	}
?>