<?php
	class Check_rooms extends Tables{

		public function __construct($hostname, $username, $password, $dbname){
			$this->mysql = new SQL_Setup($hostname, $username, $password, $dbname);
		}

		private function encode_output($data){
			return json_encode((object)$data, JSON_PRETTY_PRINT);
		}

		public function check_rooms($for){
			$data = $this->mysql->select($this->cr, $for);
			$this->mysql->update($this->cr, array( array('check_rooms_status'=>0), array('check_rooms_for'=>$for['check_rooms_for']) ));
			return $this->encode_output($data);
		}

		public function add_check_rooms(){
			for($x=0; $x<count($this->for_check_rooms); $x++){
				$data = $this->mysql->select($this->cr, array('check_rooms_for'=>$this->for_check_rooms[$x]));
				if(!empty($data["data"])){
					$dataret = $this->mysql->update($this->cr, array(array('check_rooms_status'=>1), array('check_rooms_for'=>$this->for_check_rooms[$x])));
				}else{
					$dataret = $this->mysql->insert($this->cr, array('check_rooms_status'=>1, 'check_rooms_for'=>$this->for_check_rooms[$x]));
				}
			}
			
			return $this->encode_output($dataret);
		}

	}
?>