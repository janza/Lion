<?php

	    echo '<div class="roundedBox" id="type1">';
	    //when we have a round frame, we can print what we wanted
	    echo "Notification information: <b>ID ".$data['notification_id'].", XML name: ".$data["xml_name"].'</b><br/>';
	    echo "Notification type: ".$data['type'].'<br/>';
	    echo "User information: <b>".$data['full_name'].", recipients: ".$data['recipient'].'</b><br/>';
	    echo "Sending criteria: <b>Send when ".($data['criteria_type']==1?"below":"above")." ".$data["critical_value"].$data["unit_mark"]."</b><br/>";
	    echo "Sensor information: <b>Sensor ".$data['sensor_user_name'].", on GSN ".$data['gsn_name'].'</b><br/>';
	    echo "Resending interval: ".(int)($data["resending_interval"]/1000/60/60)."h ".(int)(($data["resending_interval"] - ((int)($data["resending_interval"]/1000/60/60))*60*60*1000)/1000/60)."m ".(($data["resending_interval"] - ((int)($data["resending_interval"]/1000/60))*60*1000)/1000)."s".'<br/>';
	    //echo "Time of approval: ".$data['time_notification_approved'].'<br/>';
	    echo "Activity: ".(($data["is_active"]=="1")?"Active":"Inactive").'<br/>';
	    echo "Activation: ".CHtml::link(($data["is_active"] == 1) ? "Revoke" : "Activate",array('admin/adminNotificationRequests', "notification_id"=>$data["notification_id"], "type"=>$data["type"])).'<br/>';
	    echo "Delete: ".CHtml::link('Delete',array('admin/adminNotificationRequests', "notification_id"=>$data["notification_id"],"type"=>$data["type"],"action"=>"delete")).'<br/>';
	    echo '<div class="corner topLeft"></div><div class="corner topRight"></div><div class="corner bottomLeft"></div><div class="corner bottomRight"></div>';
	    echo '</div>';
?>
