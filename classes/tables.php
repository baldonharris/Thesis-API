<?php
	class Tables{

		public $user_details = "user_details";
		public $rooms = "rooms";
		public $floors = "floors";
		public $frg = "floor_room_groups";
		public $rc = "room_consumptions";
		public $rd = "room_devices";
		public $rs = "room_schedules";
		public $rds = "room_device_schedules";
		public $cd = "check_devices";
		public $cr = "check_rooms";
		public $rrt = "rooms_routing_table";
		public $pan_id = "system_pan";
		public $triggers = "triggers";

		public $manual = "manual";

		public $for_check_rooms = array('view', 'manual', 'update', 'manage');
		
	}
?>