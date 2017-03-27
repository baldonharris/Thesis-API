<?php
	header('Content-Type: application/json');
	include 'classes/controller.php';

	//echo sha1('baldonharris');

	/* START DB CREDINTIALS */
	$hostname = "localhost";
	$username = "root";
	$password = "root";
	$dbname = "ThesisDB";
	/* END DB CREDINTIALS */

	/* START CALLING NECESSARY CLASSES */
	$thesis = new Controller($hostname, $username, $password, $dbname);
	/* END CALLING NECESSARY CLASSES */

	if(isset($_GET['function'])){
		switch($_GET['function']){

			/* START AUTHENTICATION */

			case "add_auth":
				if(isset($_GET['password'])){
					echo $thesis->user_details->add($_GET['password']);
				}else{
					echo $thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
				}
			break;
			case "get_auth":
				if(isset($_GET['password'])){
					echo $thesis->user_details->get($_GET['password']);
				}else{
					echo $thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
				}
			break;
			case "del_auth":
				if(isset($_GET['password'])){
					echo $thesis->user_details->del($_GET['password']);
				}else{
					echo $thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
				}
			case "upd_auth":
				if(isset($_GET['new_pass']) && isset($_GET['cur_pass'])){
					echo $thesis->user_details->upd(array('new_pass'=>$_GET['new_pass'], 'cur_pass'=>$_GET['cur_pass']));
				}else{
					echo $thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
				}
			break;

			/* END AUTHENTICATION */

			/* START PAN ID */
			case "upd_pan_id":
				if(isset($_GET['cur_pass']) && isset($_GET['new_pan_id'])){
					echo $thesis->user_details->upd(array('pan_id'=>$_GET['new_pan_id'], 'cur_pass'=>$_GET['cur_pass']), 1);
				}else{
					echo $thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
				}
			break;
			/* END PAN ID */

			/* START FLOORS */

			case "add_floor":
				if(isset($_GET['floors_name'])){
					$name = str_replace("-", " ", $_GET['floors_name']);
					echo $thesis->floors->add(ucwords($name));
				}else{
					exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
				}
				break;
			case "del_floor":
				$where_values = array();
				if(isset($_GET['floors_name'])){
					$where_values['floors_name'] = str_replace("-", " ", $_GET['floors_name']);
				}
				if(isset($_GET['floors_id'])){
					$where_values['floors_id'] = $_GET['floors_id'];
				}
				echo $thesis->floors->del($where_values);
				break;
			case "get_floor":
				$where_values = array();
				if(isset($_GET['floors_name'])){
					$where_values['floors_name'] = $_GET['floors_name'];
				}
				if(isset($_GET['floors_id'])){
					$where_values['floors_id'] = $_GET['floors_id'];
				}
				echo $thesis->floors->get($where_values);
				break;
			case "upd_floor":
				$where_values = array();
				$new_values = array();
				if(isset($_GET['floors_name'])){
					$where_values['floors_name'] = str_replace("-", " ", $_GET['floors_name']);
				}
				if(isset($_GET['floors_id'])){
					$where_values['floors_id'] = $_GET['floors_id'];
				}
				if(isset($_GET['new_floors_name'])){
					$new_values['floors_name'] = ucwords(str_replace("-", " ", $_GET['new_floors_name']));
				}
				$curname = ucwords(str_replace("-", " ", $_GET['floors_name']));
				$newname = ucwords(str_replace("-", " ", $_GET['new_floors_name']));
				if(!strcmp($curname, $newname)){
					echo $thesis->encode_output(array('status'=>-1, 'message'=>"No changes has been made!", 'data'=>array()));
				}else{
					echo $thesis->floors->upd(array($new_values, $where_values));
				}
				
				break;

			/* END FLOORS */

			/* START ROOMS */

			case "add_rooms":
				if(isset($_GET['rooms_name'])){
					if(isset($_GET['rooms_port'])){
						if(isset($_GET['rooms_status'])){
							if(isset($_GET['rooms_address'])){
								echo $thesis->rooms->add($_GET['rooms_name'], $_GET['rooms_port'], $_GET['rooms_status'], $_GET['rooms_address']);
							}else{
								exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
							}
						}else{
							exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
						}
					}else{
						exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
					}
				}else{
					exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
				}
				break;
			case "get_rooms":
				$values = array();
				if(isset($_GET['rooms_id'])){
					$values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['rooms_name'])){
					$values['rooms_name'] = $_GET['rooms_name'];
				}
				if(isset($_GET['rooms_port'])){
					$values['rooms_port'] = $_GET['rooms_port'];
				}
				if(isset($_GET['rooms_status'])){
					$values['rooms_status'] = $_GET['rooms_status'];
				}
				if(isset($_GET['rooms_address'])){
					$values['rooms_address'] = $_GET['rooms_address'];
				}
				echo $thesis->rooms->get($values);
				break;
			case "del_rooms":
				$values = array();
				if(isset($_GET['rooms_name'])){
					$values['rooms_name'] = $_GET['rooms_name'];
				}
				if(isset($_GET['rooms_port'])){
					$values['rooms_port'] = $_GET['rooms_port'];
				}
				if(isset($_GET['rooms_id'])){
					$values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['rooms_address'])){
					$values['rooms_address'] = $_GET['rooms_address'];
				}
				echo $thesis->rooms->del($values);
				break;
			case "upd_rooms":
				$where_values = array();
				$new_values = array();
				if(isset($_GET['rooms_name'])){
					$where_values['rooms_name'] = $_GET['rooms_name'];
				}
				if(isset($_GET['rooms_port'])){
					$where_values['rooms_port'] = $_GET['rooms_port'];
				}
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['rooms_address'])){
					$where_values['rooms_address'] = $_GET['rooms_address'];
				}
				if(isset($_GET['new_rooms_name'])){
					$new_values['rooms_name'] = $_GET['new_rooms_name'];
				}
				if(isset($_GET['new_rooms_port'])){
					$new_values['rooms_port'] = $_GET['new_rooms_port'];
				}
				if(isset($_GET['new_rooms_status'])){
					$new_values['rooms_status'] = $_GET['new_rooms_status'];
				}
				if(isset($_GET['new_rooms_address'])){
					$new_values['rooms_address'] = $_GET['new_rooms_address'];
				}
				echo $thesis->rooms->upd(array($new_values, $where_values));
				break;

			/* END ROOMS */

			/* START FLOOR ROOM GROUPS */

			case "get_frg":
				$where_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['floors_id'])){
					$where_values['floors_id'] = $_GET['floors_id'];
				}
				if(isset($_GET['floor_room_groups_id'])){
					$where_values['floor_room_groups_id'] = $_GET['floor_room_groups_id'];
				}
				echo $thesis->frg->get($where_values);
				break;
			case "add_frg":
				$add_values = array();
				if(!isset($_GET['rooms_id']) || !isset($_GET['floors_id'])){
					exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
				}else{
					$add_values['rooms_id'] = $_GET['rooms_id'];
					$add_values['floors_id'] = $_GET['floors_id'];
				}
				echo $thesis->frg->add($add_values);
				break;
			case "del_frg":
				if(isset($_GET['floor_room_groups_id'])){
					echo $thesis->frg->del(array('floor_room_groups_id'=>$_GET['floor_room_groups_id']));
				}else if(isset($_GET['floors_id']) && isset($_GET['rooms_id'])){
					echo $thesis->frg->del(array('floors_id'=>$_GET['floors_id'], 'rooms_id'=>$_GET['rooms_id']));
				}else{
					exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
				}
				break;

			/* END FLOOR ROOM GROUPS */

			/* START ROOM CONSUMPTIONS */

			case "add_rc":
				$add_values = array();
				if(isset($_GET['rooms_id']) && is_numeric($_GET['rooms_id'])){
					$add_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_consumptions'])){
					if(is_double(doubleval($_GET['room_consumptions']))){
						$add_values['room_consumptions'] = $_GET['room_consumptions'];
					}else{
						exit($thesis->encode_output(array('status'=>0, 'message'=>"Parameter should be double!", 'data'=>NULL)));
					}
				}
				if(count($add_values) == 2){
					echo $thesis->rc->add($add_values);
				}else{
					exit($thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL)));
				}
				break;
			case "get_rc":
				$where_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_consumptions_id'])){
					$where_values['room_consumptions_id'] = $_GET['room_consumptions_id'];
				}
				echo $thesis->rc->get($where_values);
				break;
			case "del_rc":
				$where_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_consumptions_id'])){
					$where_values['room_consumptions_id'] = $_GET['room_consumptions_id'];
				}
				echo $thesis->rc->del($where_values);
				break;
			case "upd_rc":
				$where_values = array();
				$new_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_consumptions_id'])){
					$where_values['room_consumptions_id'] = $_GET['room_consumptions_id'];
				}
				if(isset($_GET['new_room_consumptions'])){
					$new_values['room_consumptions'] = $_GET['new_room_consumptions'];
				}
				if(isset($_GET['new_rooms_id'])){
					$new_values['rooms_id'] = $_GET['new_rooms_id'];

				}
				echo $thesis->rc->upd(array($new_values, $where_values));
				break;

			/* END ROOM CONSUMPTIONS */

			/* START ROOM DEVICES */
			
			case "add_rd":
				$add_values = array();
				if(isset($_GET['rooms_id'])){
					$add_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_devices_name'])){
					$add_values['room_devices_name'] = $_GET['room_devices_name'];
				}
				if(isset($_GET['room_devices_port'])){
					$add_values['room_devices_port'] = $_GET['room_devices_port'];
				}
				if(isset($_GET['room_devices_status'])){
					$add_values['room_devices_status'] = $_GET['room_devices_status'];
				}
				echo $thesis->rd->add($add_values);
				break;
			case "get_rd":
				$where_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_devices_id'])){
					$where_values['room_devices_id'] = $_GET['room_devices_id'];
				}
				if(isset($_GET['room_devices_port'])){
					$where_values['room_devices_port'] = $_GET['room_devices_port'];
				}
				if(isset($_GET['room_devices_status'])){
					$where_values['room_devices_status'] = $_GET['room_devices_status'];
				}
				echo $thesis->rd->get($where_values);
				break;
			case "del_rd":
				$where_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_devices_id'])){
					$where_values['room_devices_id'] = $_GET['room_devices_id'];
				}
				if(isset($_GET['room_devices_port'])){
					$where_values['room_devices_port'] = $_GET['room_devices_port'];
				}
				echo $thesis->rd->del($where_values);
				break;
			case "upd_rd":
				$where_values = array();
				$new_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_devices_id'])){
					$where_values['room_devices_id'] = $_GET['room_devices_id'];
				}
				if(isset($_GET['room_devices_port'])){
					$where_values['room_devices_port'] = $_GET['room_devices_port'];
				}
				if(isset($_GET['new_rooms_id'])){
					$new_values['rooms_id'] = $_GET['new_rooms_id'];
				}
				if(isset($_GET['new_room_devices_name'])){
					$new_values['room_devices_name'] = $_GET['new_room_devices_name'];
				}
				if(isset($_GET['new_room_devices_port'])){
					$new_values['room_devices_port'] = $_GET['new_room_devices_port'];
				}
				if(isset($_GET['new_room_devices_status'])){
					$new_values['room_devices_status'] = $_GET['new_room_devices_status'];
				}
				echo $thesis->rd->upd(array($new_values, $where_values));
				break;

			/* END ROOM DEVICES */

			/* START ROOM SCHEDULES */
			
			case "add_rs":
				$values = array();
				if(isset($_GET['rooms_id'])){
					$values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_schedules_name'])){
					$values['room_schedules_name'] = ucwords($_GET['room_schedules_name']);
				}
				if(isset($_GET['room_schedules_day'])){
					$values['room_schedules_day'] = join(',', array_map('ucfirst', explode(',', $_GET['room_schedules_day'])));
				}
				if(isset($_GET['room_schedules_date'])){
					$values['room_schedules_date'] = date("Y-m-d", strtotime($_GET['room_schedules_date']));
				}
				if(isset($_GET['room_schedules_start_time'])){
					$values['room_schedules_start_time'] = intval($_GET['room_schedules_start_time']);
				}
				if(isset($_GET['room_schedules_end_time'])){
					$values['room_schedules_end_time'] = intval($_GET['room_schedules_end_time']);
				}
				if(isset($_GET['room_schedules_duration'])){
					$values['room_schedules_duration'] = intval($_GET['room_schedules_duration']);
				}
				if(isset($_GET['room_schedules_type'])){
					$values['room_schedules_type'] = intval($_GET['room_schedules_type']);
				}else{
					$values['room_schedules_type'] = 0;
				}
				echo $thesis->rs->add($values);
				break;
			case "get_rs":
				$where_values = array();
				if(isset($_GET['room_schedules_id'])){
					$where_values['room_schedules_id'] = $_GET['room_schedules_id'];
				}
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_schedules_day'])){
					$where_values['room_schedules_day'] = ucwords($_GET['room_schedules_day']);
				}
				if(isset($_GET['room_schedules_date'])){
					$where_values['room_schedules_date'] = date("Y-m-d", strtotime($_GET['room_schedules_date']));
				}
				if(isset($_GET['room_schedules_start_time'])){
					$where_values['room_schedules_start_time'] = intval($_GET['room_schedules_start_time']);
				}
				if(isset($_GET['room_schedules_end_time'])){
					$where_values['room_schedules_end_time'] = intval($_GET['room_schedules_end_time']);
				}

				echo $thesis->rs->get($where_values);
				
				break;
			case "del_rs":
				$where_values = array();
				if(isset($_GET['room_schedules_id'])){
					$where_values['room_schedules_id'] = $_GET['room_schedules_id'];
				}
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_schedules_day'])){
					$where_values['room_schedules_day'] = ucwords($_GET['room_schedules_day']);
				}
				if(isset($_GET['room_schedules_date'])){
					$where_values['room_schedules_date'] = date("Y-m-d", strtotime($_GET['room_schedules_date']));
				}
				if(isset($_GET['room_schedules_start_time'])){
					$where_values['room_schedules_start_time'] = intval($_GET['room_schedules_start_time']);
				}
				if(isset($_GET['room_schedules_end_time'])){
					$where_values['room_schedules_end_time'] = intval($_GET['room_schedules_end_time']);
				}
				echo $thesis->rs->del($where_values, (isset($_GET['rds'])) ? $_GET['rds'] : NULL);
				break;
			case "upd_rs":
				$where_values = array();
				$new_values = array();
				if(isset($_GET['room_schedules_id'])){
					$where_values['room_schedules_id'] = $_GET['room_schedules_id'];
				}

				if(isset($_GET['new_rooms_id'])){
					$new_values['rooms_id'] = $_GET['new_rooms_id'];
				}
				if(isset($_GET['new_room_schedules_name'])){
					$new_values['room_schedules_name'] = $_GET['new_room_schedules_name'];
				}
				if(isset($_GET['new_room_schedules_day'])){
					$new_values['room_schedules_day'] = ucwords($_GET['new_room_schedules_day']);
				}
				if(isset($_GET['new_room_schedules_date'])){
					$new_values['room_schedules_date'] = date("Y-m-d", strtotime($_GET['new_room_schedules_date']));
				}
				if(isset($_GET['new_room_schedules_start_time'])){
					$new_values['room_schedules_start_time'] = intval($_GET['new_room_schedules_start_time']);
				}
				if(isset($_GET['new_room_schedules_end_time'])){
					$new_values['room_schedules_end_time'] = intval($_GET['new_room_schedules_end_time']);
				}
				if(isset($_GET['new_room_schedules_duration'])){
					$new_values['room_schedules_duration'] = intval($_GET['new_room_schedules_duration']);
				}
				if(isset($_GET['new_room_schedules_type'])){
					$new_values['room_schedules_type'] = intval($_GET['new_room_schedules_type']);
				}
				echo $thesis->rs->upd(array($new_values, $where_values));
				break;

			/* END ROOM SCHEDULES */

			/* START ROOM DEVICE SCHEDULES */
			
			case "add_rds":
				$values = array();
				if(isset($_GET['room_devices_id'])){
					$values['room_devices_id'] = $_GET['room_devices_id'];
				}
				if(isset($_GET['room_schedules_id'])){
					$values['room_schedules_id'] = $_GET['room_schedules_id'];
				}
				echo $thesis->rds->add($values);
				break;
			case "get_rds":
				$where_values = array();
				if(isset($_GET['room_device_schedules_id'])){
					$where_values['room_device_schedules_id'] = $_GET['room_device_schedules_id'];
				}
				if(isset($_GET['room_devices_id'])){
					$where_values['room_devices_id'] = $_GET['room_devices_id'];
				}
				if(isset($_GET['room_schedules_id'])){
					$where_values['room_schedules_id'] = $_GET['room_schedules_id'];
				}
				echo $thesis->rds->get($where_values);
				break;
			case "del_rds":
				$where_values = array();
				if(isset($_GET['room_device_schedules_id'])){
					$where_values['room_device_schedules_id'] = $_GET['room_device_schedules_id'];
				}
				if(isset($_GET['room_devices_id'])){
					$where_values['room_devices_id'] = $_GET['room_devices_id'];
				}
				if(isset($_GET['room_schedules_id'])){
					$where_values['room_schedules_id'] = $_GET['room_schedules_id'];
				}
				echo $thesis->rds->del($where_values);
				break;
			case "upd_rds":
				$where_values = array();
				$new_values = array();
				if(isset($_GET['room_device_schedules_id'])){
					$where_values['room_device_schedules_id'] = $_GET['room_device_schedules_id'];
				}
				if(isset($_GET['new_room_devices_id'])){
					$new_values['room_devices_id'] = $_GET['new_room_devices_id'];
				}
				if(isset($_GET['new_room_schedules_id'])){
					$new_values['room_schedules_id'] = $_GET['new_room_schedules_id'];
				}
				echo $thesis->rds->upd(array($new_values, $where_values));
				break;

			/* END ROOM DEVICE SCHEDULES */

			/* START MODIFIED QUERY */

			case "view_ras": // VIEW ROOM AND SCHEDULES
				$where_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_schedules_type'])){
					$where_values['room_schedules_type'] = $_GET['room_schedules_type'];
				}
				if(isset($_GET['room_schedules_day'])){
					$where_values['room_schedules_day'] = $_GET['room_schedules_day'];
				}
				echo $thesis->mod->get($_GET['function'], $where_values);
				break;

			case "view_room_status":
				$where_values = array();
				if(isset($_GET['floor_room_groups_id'])){
					$where_values['floor_room_groups_id'] = $_GET['floor_room_groups_id'];
				}
				if(isset($_GET['floors_id'])){
					$where_values['floors_id'] = $_GET['floors_id'];
				}
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['mode'])){
					echo $thesis->mod->get($_GET['function'], $where_values, $_GET['mode']);
				}else{
					echo $thesis->mod->get($_GET['function'], $where_values);
				}
				
				break;

			case "set_manual_switch":
				$where_values = array();
				if(isset($_GET['rooms_id'])){
					$where_values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['room_devices_id'])){
					$where_values['room_devices_id'] = $_GET['room_devices_id'];
				}
				if(isset($_GET['type'])){
					$where_values['room_schedules_type'] = $_GET['type'];
				}

				$new_values = array();
				if(isset($_GET['duration'])){
					$new_values['room_schedules_duration'] = $_GET['duration'];
				}
				if(isset($_GET['status'])){
					$new_values['room_devices_status'] = $_GET['status'];
				}
				if(isset($_GET['checked'])){
					$new_values['checked'] = $_GET['checked'];
				}
				$thesis->mod->manualSwitch($new_values, $where_values);
				break;

			case "set_trigger_manual":
				$thesis->mod->set_trigger_manual($_GET['rooms_id']);
				break;

			case "set_trigger_device":
				$thesis->mod->set_trigger_device($_GET['rooms_id']);
				break;

			case "add_device_status":
				$values = array();
				if(isset($_GET['rooms_id'])){
					$values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['status'])){
					$values['check_devices_status'] = $_GET['status'];
				}
				echo $thesis->cd->add_device_status($values);
				break;

			case "check_devices":
				$where = array();
				if(isset($_GET['rooms_id'])){
					$where['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['status'])){
					$where['check_devices_status'] = $_GET['status'];
				}
				echo $thesis->cd->check_devices($where);
				break;

			case "add_room_module":
				$where = array();
				$values = array();
				if(isset($_GET['floors_name'])){
					$where['floors_name'] = $_GET['floors_name'];
				}

				if(isset($_GET['rooms_name'])){
					$values['rooms_name'] = $_GET['rooms_name'];
				}
				if(isset($_GET['rooms_address'])){
					$values['rooms_address'] = $_GET['rooms_address'];
				}
				if(isset($_GET['rooms_port'])){
					$values['rooms_port'] = $_GET['rooms_port'];
				}
				if(isset($_GET['rooms_status'])){
					$values['rooms_status'] = $_GET['rooms_status'];	
				}else{
					$values['rooms_status'] = 0;
				}

				if(isset($_GET['room_devices'])){
					$values['room_devices'] = $_GET['room_devices'];
				}
				if(isset($_GET['room_devices_port'])){
					$values['room_devices_port'] = $_GET['room_devices_port'];
				}

				if(isset($_GET['room_schedule_day'])){
					$values['room_schedules_day'] = $_GET['room_schedule_day'];
				}
				if(isset($_GET['start_time'])){
					$values['room_schedules_start_time'] = $_GET['start_time'];
				}
				if(isset($_GET['end_time'])){
					$values['room_schedules_end_time'] = $_GET['end_time'];
				}
				if(isset($_GET['sched_name'])){
					$values['room_schedules_name'] = $_GET['sched_name'];
				}
				if(isset($_GET['sched_type'])){
					$values['room_schedules_type'] = $_GET['sched_type'];
				}
				if(isset($_GET['device_to_sched'])){
					$values['device_to_sched'] = $_GET['device_to_sched'];
				}

				echo $thesis->mod->new_module($where, $values);
				break;

			case "delete_room_module":
				if(isset($_GET['rooms_id'])){
					$where['rooms_id'] = $_GET['rooms_id'];
				}
				echo $thesis->mod->delete_room_module($where);
				break;

			case "check_rooms":
				$where = array();
				if(isset($_GET['check_rooms_for'])){
					$where['check_rooms_for'] = $_GET['check_rooms_for'];
				}
				echo $thesis->cr->check_rooms($where);
				break;

			case "add_check_rooms":
				echo $thesis->cr->add_check_rooms();
				break;

			case "get_room_details":
				if(isset($_GET['rooms_id'])){
					$where['rooms_id'] = $_GET['rooms_id'];
				}
				echo $thesis->mod->get_room_details($where);
				break;

			case "edit_room_details":
				if(isset($_GET['floor'])){
					$where['floors_name'] = str_replace("-", " ", $_GET['floor']);
				}
				if(isset($_GET['room_name'])){
					$where['rooms_name'] = str_replace("-", " ", $_GET['room_name']);
				}
				if(isset($_GET['room_address'])){
					$where['rooms_address'] = str_replace("-", " ", $_GET['room_address']);
				}
				if(isset($_GET['rooms_id'])){
					$where['rooms_id'] = $_GET['rooms_id'];
				}
				
				echo $thesis->mod->update_room($where);
				break;

			case "edit_device_ports":
				if(isset($_GET['ports'])){
					$where['ports'] = str_replace("-", " ", $_GET['ports']);
				}
				if(isset($_GET['device'])){
					$where['device'] = str_replace("-", " ", $_GET['device']);;
				}
				if(isset($_GET['rooms_id'])){
					$where['rooms_id'] = $_GET['rooms_id'];
				}

				echo $thesis->mod->update_device($where);
				break;

			case "manage_schedule":
				if(isset($_GET['days'])){
					$values['room_schedules_day'] = $_GET['days'];
				}
				if(isset($_GET['stime'])){
					$values['room_schedules_start_time'] = $_GET['stime'];
				}
				if(isset($_GET['etime'])){
					$values['room_schedules_end_time'] = $_GET['etime'];
				}
				if(isset($_GET['schedname'])){
					$values['room_schedules_name'] = $_GET['schedname'];
				}
				if(isset($_GET['sched_type'])){
					$values['room_schedules_type'] = $_GET['sched_type'];
				}else{
					$values['room_schedules_type'] = 0;
				}
				if(isset($_GET['rooms_id'])){
					$values['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['devices'])){
					$values['devices'] = $_GET['devices'];
				}
				if(isset($_GET['rs_id'])){
					$values['room_schedules_id'] = $_GET['rs_id'];
					echo $thesis->mod->update_manage_schedule($values);
				}else{
					echo $thesis->mod->manage_schedule($values);
				}
				break;

			case "get_consumption":
				$where = array();
				if(isset($_GET['rooms_name'])){
					$where['rooms_name'] = str_replace("-", " ", $_GET['rooms_name']);
				}
				if(isset($_GET['floors_name'])){
					$where['floors_name'] = str_replace("-", " ", $_GET['floors_name']);
				}

				echo $thesis->mod->get_consumption($where);
				break;

			case "get_room_schedules_plus_device":
				$where = array();
				if(isset($_GET['room_schedules_id'])){
					$where['room_schedules_id'] = $_GET['room_schedules_id'];
				}

				echo $thesis->mod->get_room_schedules_plus_device($where);
				break;

			case "get_consumption_for_graph":
				$where = array();
				if(isset($_GET['per_view'])){
					$where['per_view'] = $_GET['per_view'];
				}
				if(isset($_GET['date_me'])){
					$where['date'] = $_GET['date_me'];
				}
				if(isset($_GET['date_start'])){
					$where['date_start'] = $_GET['date_start'];
				}
				if(isset($_GET['date_end'])){
					$where['date_end'] = $_GET['date_end'];
				}
				if(isset($_GET['date_sweek'])){
					$where['date_sweek'] = $_GET['date_sweek'];
				}
				if(isset($_GET['date_eweek'])){
					$where['date_eweek'] = $_GET['date_eweek'];
				}
				echo $thesis->mod->get_consumption_for_graph($where);
				break;

			case "sync_data":
				if(isset($_GET['rooms_id'])){
					echo $thesis->mod->sync_data(array('mode'=>2, 'id'=>$_GET['rooms_id']));
				}
				break;

			case "newSched":
				$where = array();
				if(isset($_GET['stime'])){
					$where['room_schedules_start_time'] = $_GET['stime'];
				}
				if(isset($_GET['etime'])){
					$where['room_schedules_end_time'] = $_GET['etime'];
				}
				if(isset($_GET['etime'])){
					$where['room_schedules_date'] = $_GET['date'];
				}
				if(isset($_GET['schedname'])){
					$where['room_schedules_name'] = $_GET['schedname'];
				}
				if(isset($_GET['rooms_id'])){
					$where['rooms_id'] = $_GET['rooms_id'];
				}
				if(isset($_GET['device'])){
					$where['devices'] = $_GET['device'];
				}
				if(isset($_GET['rs_id'])){
					$where['room_schedules_id'] = $_GET['rs_id'];
					echo $thesis->mod->edit_schedule_day($where);
				}else{
					echo $thesis->mod->new_schedule_day($where);
				}
				break;

			case "get_the_consumption":
				echo $thesis->mod->get_the_consumption();
				break;
			/* END MODIFIED QUERY */

			default:
				echo $thesis->encode_output(array('status'=>0, 'message'=>"Invalid function!", 'data'=>NULL));
		}
	}else{
		echo $thesis->encode_output(array('status'=>0, 'message'=>"Lack of parameters!", 'data'=>NULL));
	}
?>