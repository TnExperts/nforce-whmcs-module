<?php

/*
 * (C) Ashish Saxena <ashish@reak.in>
 * (C) 2016 REAK INFOTECH LLP
 *
 * The LICENSE file included with the project would govern the use policy for this code,
 * In case of missing LICENSE file the code will be treated as an Intellectual Property of the creator mentioned above,
 * All rights related to distribution, modifcation, reselling, use for commercial or private use of this code is terminated.
 *
 */



function nforce_CreateAccount($params) {
	// Set Vars
	$message = "An order needs to be provisioned for ServiceID ".$params['serviceid'];
	$command = 'OpenTicket';
	$values = array( 'clientid' => $params['userid'], 'deptid' => '3', 'subject' => 'Server needs to be provisioned', 'message' => $message );
	$adminuser = 'API';
 
	// Call API
	$results = localAPI($command, $values, $adminuser);
	if ($results['result']!="success") echo "An Error Occurred: ".$results['result'];
}

function nforce_SuspendAccount($params) {
	// Set Vars
	$message = "An order needs to be suspended for ServiceID :".$params['serviceid']."   | IP :".$params['serverip']."   | ServerID :".$params['customfields']['serverid'];
	$command = 'OpenTicket';
	$values = array( 'clientid' => $params['userid'], 'deptid' => '3', 'subject' => 'Server needs to be suspended', 'message' => $message );
	$adminuser = 'API';
 
	// Call API
	$results = localAPI($command, $values, $adminuser);
	if ($results['result']!="success") echo "An Error Occurred: ".$results['result'];
}

function nforce_UnsuspendAccount($params) {
	// Set Vars
	$message = "An order needs to be Unsuspended for ServiceID :".$params['serviceid']."   | IP :".$params['serverip']."   | ServerID :".$params['customfields']['serverid'];
	$command = 'OpenTicket';
	$values = array( 'clientid' => $params['userid'], 'deptid' => '3', 'subject' => 'Server needs to be Unsuspended', 'message' => $message );
	$adminuser = 'API';
 
	// Call API
	$results = localAPI($command, $values, $adminuser);
	if ($results['result']!="success") echo "An Error Occurred: ".$results['result'];
}


function nforce_TerminateAccount($params) {
	// Set Vars
	$message = "An order needs to be terminated for ServiceID :".$params['serviceid']."   | IP :".$params['serverip']."   | ServerID :".$params['customfields']['serverid'];
	$command = 'OpenTicket';
	$values = array( 'clientid' => $params['userid'], 'deptid' => '3', 'subject' => 'Server needs to be terminated', 'message' => $message );
	$adminuser = 'API';
 
	// Call API
	$results = localAPI($command, $values, $adminuser);
	if ($results['result']!="success") echo "An Error Occurred: ".$results['result'];
}

function fetchbwimage($deliveryday, $serverid) {
	
	$daynow = date('j');
    $monthnow = date('m');
    $yearnow = date('Y');
    //Assume delivery date of 25th
    //$deliveryday = 2;
    if($deliveryday >= $daynow) {
        // Delivery day is in previous month
        if($monthnow == "1") {
            //Checking if month is January, if yes then year of delivery date will be set to -1 from current
            $monthdelivery = "12";
            $yeardelivery = $yearnow-1;
            }
        $monthdelivery = $monthnow-1;
        $yeardelivery = $yearnow;
        }
    else
        {
        $monthdelivery = $monthnow;
        $yeardelivery = $yearnow;
        }


    $epochnow = strtotime($daynow.".".$monthnow.".".$yearnow);
    $epochdelivery = strtotime($deliveryday.".".$monthdelivery.".".$yeardelivery);


    $timediff = $epochnow - $epochdelivery;
	$timediff += 172800;
	
	$url = 'http://snmp-web.stats.nforce.com/SwitchportUsage.php?auth=1221,077ffd557f83546358ce448fd30d48efd8bba6e1&method=dedi-srv&id='.$serverid.'&swap=1&timetype=lastx&lastx='.$timediff;
$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);
$myfile = fopen("dummy.png", "w");
fwrite($myfile, $data);
fclose($myfile);
$inFile = "dummy.png";
$outFile = "test-cropped.jpg";
$image = new Imagick($inFile);
$image->cropImage(600,300, 0,40);
//$image->writeImage($outFile);
//header('Content-type:image/png');
return $image->getImageBlob();
	
}


function getnforcebw($serverid, $deliveryday) {
    // Get data used since the delivery date of this month
    
    $daynow = date('j');
    $monthnow = date('m');
    $yearnow = date('Y');
    //Assume delivery date of 25th
    //$deliveryday = 2;
    if($deliveryday >= $daynow) {
        // Delivery day is in previous month
        if($monthnow == "1") {
            //Checking if month is January, if yes then year of delivery date will be set to -1 from current
            $monthdelivery = "12";
            $yeardelivery = $yearnow-1;
            }
        $monthdelivery = $monthnow-1;
        $yeardelivery = $yearnow;
        }
    else
        {
        $monthdelivery = $monthnow;
        $yeardelivery = $yearnow;
        }

    $epochnow = strtotime($daynow.".".$monthnow.".".$yearnow);
    $epochdelivery = strtotime($deliveryday.".".$monthdelivery.".".$yeardelivery);

    $timediff = $epochnow - $epochdelivery;
    $timediff += 172800;
    if($timediff>=2592000) {
        //Reset BW
        return 0;
    }
	else {
    
    $url = 'http://snmp-web.stats.nforce.com/SwitchportUsage.php?auth=1221,077ffd557f83546358ce448fd30d48efd8bba6e1&method=dedi-srv&id='.$serverid.'&swap=1&timetype=lastx&lastx='.$timediff.'&output=table';
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);

	$lines = split('<td colspan="5"><hr></td>', $data);
	$lines1 = split('<td align="right">',$lines[1]);
	$totalbw = $lines1[3]+$lines1[4];
	return $totalbw;
	}

}

function nforce_reboot() {
	//Fail = <div class='alert-message error'><p>Your server has been rebooted.<a href='#' class='close'>&times;</a></p></div>
	$url = "https://ssc.nforce.com/api/servers/reboot/id/".$params['customfields']['serverid']."/type/dedicated";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:36affc32785d8f92704449abc3a4eab6845253e3', 'Accept: xml'));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);
	$xmlobj = simplexml_load_string($data);
	if ($xmlobj->boot == "success") {
		return "<div class='alert-message success'><p>Your server has been rebooted.<a href='#' class='close'>&times;</a></p></div>";
	}
	else {
		return "<div class='alert-message error'><p>Sorry something went wrong, Please contact support.<a href='#' class='close'>&times;</a></p></div>";
	}
	
	
}

function nforce_shutdown() {
	
	$url = "https://ssc.nforce.com/api/servers/shutdown/id/".$params['customfields']['serverid']."/type/dedicated";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:36affc32785d8f92704449abc3a4eab6845253e3', 'Accept: xml'));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);
	$xmlobj = simplexml_load_string($data);
	if ($xmlobj->boot == "success") {
		return "<div class='alert-message success'><p>Your server has been shutdown.<a href='#' class='close'>&times;</a></p></div>";
	}
	else {
		return "<div class='alert-message error'><p>Sorry something went wrong, Please contact support.<a href='#' class='close'>&times;</a></p></div>";
	}
}

function nforce_boot() {
	
	$url = "https://ssc.nforce.com/api/servers/boot/id/".$params['customfields']['serverid']."/type/dedicated";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:36affc32785d8f92704449abc3a4eab6845253e3', 'Accept: xml'));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);
	$xmlobj = simplexml_load_string($data);
	if ($xmlobj->boot == "success") {
		return "<div class='alert-message success'><p>Your server has been Booted.<a href='#' class='close'>&times;</a></p></div>";
	}
	else {
		return "<div class='alert-message error'><p>Sorry something went wrong, Please contact support.<a href='#' class='close'>&times;</a></p></div>";
	}
	
}


function nforce_ClientArea($params) {
$message = "";
$bwimage = "";
$bwnumber = "";
	if ($_GET['c'] == "reboot") {
		// Execute Reboot
		$message = nforce_reboot();
	}
	
	if ($_GET['c'] == "shutdown") {
		// Execute Shutdown
		$message = nforce_shutdown();
	}
	
	if ($_GET['c'] == "boot") {
		// Execute Boot
		$message = nforce_boot();
	}
	
	if ($_GET['b'] == "network" || empty($_GET['b'])) {
		$info = "<h3>Network Information</h3><br /><br />";
		$bwimage = fetchbwimage($params['customfields']['deliveryday'], $params['customfields']['serverid']);
		$bwimage = base64_encode($bwimage);
		$bwimage = '<img alt="Embedded Image" src="data:image/png;base64,'.$bwimage.'" />';
		$usedbw = getnforcebw($params['customfields']['serverid'], $params['customfields']['deliveryday']);
		$allottedbw = $params['customfields']['allottedbw'];
		$bwpercent = ($usedbw/$allottedbw)*100;
		if ($bwpercent <= 40)
		{
			$bwnumber = '
			<h5>Bandwidth Utilization (Billing Month)</h5>
			<div class="progress">
			<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="'.$bwpercent.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$bwpercent.'%">
			</div>
			'.$bwpercent.'% Bandwidth Used
			</div>
			';
		}
		if ($bwpercent > 40 && $bwpercent <= 60)
		{
			$bwnumber = '
			<h5>Bandwidth Utilization (Billing Month)</h5>
			<div class="progress">
			<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="'.$bwpercent.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$bwpercent.'%">
			</div>
			'.$bwpercent.'% Bandwidth Used
			</div>
			';
		}
		if ($bwpercent > 60 && $bwpercent <= 80)
		{
			$bwnumber = '
			<h5>Bandwidth Utilization (Billing Month)</h5>
			<div class="progress">
			<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="'.$bwpercent.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$bwpercent.'%">
			</div>
			'.$bwpercent.'% Bandwidth Used
			</div>
			';
		}
		if ($bwpercent > 80)
		{
			$bwnumber = '
			<h5>Bandwidth Utilization (Billing Month)</h5>
			<div class="progress">
			<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="'.$bwpercent.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$bwpercent.'%">
			</div>
			'.$bwpercent.'% Bandwidth Used
			</div>
			';
		}
	}
	else {
		$info = "<h3>Power Options</h3><br /><br />";
		$info .= '<a href="clientarea.php?action=productdetails&id='.$params["serviceid"].'&b=power&c=reboot"><button class="btn btn-info" name="reboot" type="submit" value="reboot">Reboot</button></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$info .= '<a href="clientarea.php?action=productdetails&id='.$params["serviceid"].'&b=power&c=shutdown"><button class="btn btn-danger" name="shutdown" type="submit" value="shutdown">Shutdown</button></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$info .= '<a href="clientarea.php?action=productdetails&id='.$params["serviceid"].'&b=power&c=reboot"><button class="btn btn-success" name="boot" type="submit" value="boot">Boot</button></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	return array(
	'vars' => array(
		'get' => $_GET,
		'message' => $message,
		'srvid' => $params['serviceid'],
		'info' => $info,
		'bwimage' => $bwimage,
		'bwnumber' => $bwnumber
	),
	);
}

?>