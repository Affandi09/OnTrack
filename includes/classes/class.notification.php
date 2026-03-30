<?php

class Notification extends App {


    public static function ticketUser($ticketid,$reply,$templateid) { //send ticket notification
        global $database;
    	$template = getRowById("notificationtemplates",$templateid);
    	$ticket = getRowById("tickets",$ticketid);
    	$ccs = array(); if($ticket['ccs'] != "") $ccs = unserialize($ticket['ccs']);

        $client = __('Unassigned');
        $department = __('Unassigned');

        if($ticket['clientid'] != 0) $client = getSingleValue("clients","name",$ticket['clientid']);
        if($ticket['departmentid'] != 0) $department = getSingleValue("tickets_departments","name",$ticket['departmentid']);

    	if($ticket['userid'] == 0) $contact = $ticket['email']; else $contact = getSingleValue("people","name",$ticket['userid']);

        // Resolve branch and submitter names
        $branch_name = __('Unassigned');
        $submitter_name = __('Unassigned');
        if(!empty($ticket['branch_id'])) $b = getSingleValue("branches", "name", $ticket['branch_id']);
        if(!empty($b)) $branch_name = $b;
        if(!empty($ticket['submitter_id'])) $s = getSingleValue("submitters", "name", $ticket['submitter_id']);
        if(!empty($s)) $submitter_name = $s;

    	$search = array('{ticketid}', '{status}', '{subject}', '{contact}', '{message}', '{company}', '{appurl}', '{client}', '{department}');
    	$replace = array($ticket['ticket'], $ticket['status'], $ticket['subject'], $contact, $reply, getConfigValue("company_name"), getConfigValue("app_url"), $client, $department);

    	$subject = str_replace($search, $replace, $template['subject']);
    	$message = str_replace($search, $replace, $template['message']);

        // Append ticket detail block directly in code (no DB template change needed)
        $message .= self::ticketInfoBlock($ticket['subject'], $contact, $submitter_name, $branch_name, $ticket['priority'], $ticket['timestamp']);

        // attachments
        $replyid = $database->max("tickets_replies", "id", ["ticketid" => $ticketid]);
        $attachments = $database->select("files", "id", ["ticketreplyid" => $replyid]);

    	sendEmail($ticket['email'],$subject,$message,$ticket['clientid'],$ticket['userid'],$ccs,$attachments);

        sendFCM($ticket['userid'], "Ticket Notification", $subject);
    }


    public static function ticketStaff($ticketid,$reply,$templateid) { //send ticket notification
        global $database;
    	$template = getRowById("notificationtemplates",$templateid);
    	$ticket = getRowById("tickets",$ticketid);

        $client = __('Unassigned');
        $department = __('Unassigned');

        if($ticket['clientid'] != 0) $client = getSingleValue("clients","name",$ticket['clientid']);
        if($ticket['departmentid'] != 0) $department = getSingleValue("tickets_departments","name",$ticket['departmentid']);

        if($ticket['userid'] == 0) $contact = $ticket['email']; else $contact = getSingleValue("people","name",$ticket['userid']);

        // Resolve branch and submitter names
        $branch_name = __('Unassigned');
        $submitter_name = __('Unassigned');
        if(!empty($ticket['branch_id'])) $b = getSingleValue("branches", "name", $ticket['branch_id']);
        if(!empty($b)) $branch_name = $b;
        if(!empty($ticket['submitter_id'])) $s = getSingleValue("submitters", "name", $ticket['submitter_id']);
        if(!empty($s)) $submitter_name = $s;

    	$search = array('{ticketid}', '{status}', '{subject}', '{contact}', '{message}', '{company}', '{appurl}', '{client}', '{department}');
    	$replace = array($ticket['ticket'], $ticket['status'], $ticket['subject'], $contact, $reply, getConfigValue("company_name"), getConfigValue("app_url"), $client, $department);

    	$subject = str_replace($search, $replace, $template['subject']);
    	$message = str_replace($search, $replace, $template['message']);

        // Append ticket detail block directly in code (no DB template change needed)
        $message .= self::ticketInfoBlock($ticket['subject'], $contact, $submitter_name, $branch_name, $ticket['priority'], $ticket['timestamp']);

        // attachments
        $replyid = $database->max("tickets_replies", "id", ["ticketid" => $ticketid]);
        $attachments = $database->select("files", "id", ["ticketreplyid" => $replyid]);

    	$admins = getTableFiltered("people","type","admin","ticketsnotification","1");
    	foreach($admins as $admin) {
    		sendEmail($admin['email'],$subject,$message,0,$admin['id'],$ccs=array(),$attachments);

            sendFCM($admin['id'], "Ticket Notification", $subject);
    	}
    }


    private static function ticketInfoBlock($subject, $contact, $submitter, $branch, $priority, $timestamp) {
        $date = date('d M Y H:i', strtotime($timestamp));
        return '
<br>
<table style="border-collapse:collapse; width:100%; max-width:600px; font-family:Arial,sans-serif; font-size:14px; margin-top:12px; border:1px solid #dee2e6; border-radius:6px; overflow:hidden;">
  <thead>
    <tr style="background-color:#0d6efd; color:#ffffff;">
      <td colspan="2" style="padding:10px 14px; font-weight:bold; font-size:15px;">&#128203; Detail Tiket</td>
    </tr>
  </thead>
  <tbody>
    <tr style="background-color:#f8f9fa;">
      <td style="padding:8px 14px; font-weight:bold; width:35%; color:#495057;">Subject</td>
      <td style="padding:8px 14px; color:#212529;">'.htmlspecialchars($subject).'</td>
    </tr>
    <tr>
      <td style="padding:8px 14px; font-weight:bold; color:#495057;">Pengirim (Contact)</td>
      <td style="padding:8px 14px; color:#212529;">'.htmlspecialchars($contact).'</td>
    </tr>
    <tr style="background-color:#f8f9fa;">
      <td style="padding:8px 14px; font-weight:bold; color:#495057;">Submitter</td>
      <td style="padding:8px 14px; color:#212529;">'.htmlspecialchars($submitter).'</td>
    </tr>
    <tr>
      <td style="padding:8px 14px; font-weight:bold; color:#495057;">Cabang</td>
      <td style="padding:8px 14px; color:#212529;">'.htmlspecialchars($branch).'</td>
    </tr>
    <tr style="background-color:#f8f9fa;">
      <td style="padding:8px 14px; font-weight:bold; color:#495057;">Prioritas</td>
      <td style="padding:8px 14px; color:#212529;">'.htmlspecialchars($priority).'</td>
    </tr>
    <tr>
      <td style="padding:8px 14px; font-weight:bold; color:#495057;">Tanggal Submit</td>
      <td style="padding:8px 14px; color:#212529;">'.$date.'</td>
    </tr>
  </tbody>
</table>
';
    }


    public static function newUser($peopleid,$password) { //send new user/admin notification
    	global $database;
    	$template = getRowById("notificationtemplates",3);
    	$people = getRowById("people",$peopleid);

    	$search = array('{contact}', '{email}', '{password}', '{company}', '{appurl}');
    	$replace = array($people['name'], $people['email'], $password, getConfigValue("company_name"), getConfigValue("app_url"));

    	$subject = str_replace($search, $replace, $template['subject']);
    	$message = str_replace($search, $replace, $template['message']);

    	sendEmail($people['email'],$subject,$message,$people['clientid'],$people['id']);
    }


    public static function passwordReset($peopleid,$resetlink) { //send password reset link
    	global $database;
    	$template = getRowById("notificationtemplates",5);
    	$people = getRowById("people",$peopleid);

    	$search = array('{contact}', '{resetlink}', '{company}', '{appurl}');
    	$replace = array($people['name'], $resetlink, getConfigValue("company_name"), getConfigValue("app_url"));

    	$subject = str_replace($search, $replace, $template['subject']);
    	$message = str_replace($search, $replace, $template['message']);

    	sendEmail($people['email'],$subject,$message,$people['clientid'],$people['id']);
    }


    public static function monitoringEmail($peopleid,$hostid,$hostinfo,$status) { //send monitoting email alert
    	global $database;
    	$template = getRowById("notificationtemplates",6);
    	$people = getRowById("people",$peopleid);
    	$host = getRowById("hosts",$hostid);

    	$search = array('{hostinfo}', '{status}', '{contact}', '{company}', '{appurl}');
    	$replace = array($hostinfo, $status, $people['name'], getConfigValue("company_name"), getConfigValue("app_url"));

    	$subject = str_replace($search, $replace, $template['subject']);
    	$message = str_replace($search, $replace, $template['message']);

    	sendEmail($people['email'],$subject,$message,$host['clientid'],$people['id']);
    }


    public static function monitoringSMS($peopleid,$hostid,$hostinfo,$status) { //send monitoring SMS alert
    	global $database;
    	$template = getRowById("notificationtemplates",6);
    	$people = getRowById("people",$peopleid);
    	$host = getRowById("hosts",$hostid);

    	$search = array('{hostinfo}', '{status}', '{contact}', '{company}', '{appurl}');
    	$replace = array($hostinfo, $status, $people['name'], getConfigValue("company_name"), getConfigValue("app_url"));

    	$sms = str_replace($search, $replace, $template['sms']);

    	sendSMS($people['mobile'],$sms,$host['clientid'],$people['id']);
    }


}


?>
