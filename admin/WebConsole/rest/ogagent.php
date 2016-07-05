<?php

// OpenGnsys REST routes for OGAgent communications.
// Author: Ramón M. Gómez
// Date:   2015-09-04
// Warning: authentication/authorisation not included.


// OGAgent sessions log file.
define('LOG_FILE', '/opt/opengnsys/log/ogagent.log');

// OGAgent notifies that its service is started on client.
$app->post('/ogagent/started',
    function() use ($app) {
	try {
		// Reading POST parameters in JSON format.
		$input = json_decode($app->request()->getBody());
		$ip = htmlspecialchars($input->ip);
		$mac = htmlspecialchars($input->mac);
		// Client secret key for secure communications.
		if (isset($input->secret)) {
		    $secret = htmlspecialchars($input->secret);
		    // Store secret key in DB.
		    //...
		} else {
		    // Insecure agent exception.
		    throw new Exception("Insecure agent: ip=$ip, mac=$mac");
		}
		// May check that client is included in the server database?
		// Default processing: log activity.
		file_put_contents(LOG_FILE, date(DATE_RSS).": OGAgent started: ip=$ip, mac=$mac.\n", FILE_APPEND);
		// Response. 
		$response = "";
		jsonResponse(200, $response);
	} catch (Exception $e) {
		// Comunication error.
		$response["message"] = $e->getMessage();
		file_put_contents(LOG_FILE, date(DATE_RSS).": ERROR: ".$response["message"]."\n", FILE_APPEND);
		jsonResponse(400, $response);
	}
    }
);

// OGAgent notifies that its service is stopped on client.
$app->post('/ogagent/stopped',
    function() use ($app) {
	try {
		// Reading POST parameters in JSON format.
		$input = json_decode($app->request()->getBody());
		$ip = htmlspecialchars($input->ip);
		$mac = htmlspecialchars($input->mac);
		// May check that client is included in the server database?
		// Default processing: log activity.
		file_put_contents(LOG_FILE, date(DATE_RSS).": OGAgent stopped: ip=$ip, mac=$mac.\n", FILE_APPEND);
		// Response. 
		$response = "";
		jsonResponse(200, $response);
	} catch (Exception $e) {
		// Comunication error.
		$response["message"] = $e->getMessage();
		file_put_contents(LOG_FILE, date(DATE_RSS).": ERROR: ".$response["message"]."\n", FILE_APPEND);
		jsonResponse(400, $response);
	}
    }
);

// OGAgent notifies that an user logs in.
$app->post('/ogagent/loggedin',
    function() use ($app) {
	$osType = $osVersion = "";
	try {
		// Reading POST parameters in JSON format.
		$input = json_decode($app->request()->getBody());
		$ip = htmlspecialchars($input->ip);
		$user = htmlspecialchars($input->user);
		if (isset($input->ostype))  $osType = htmlspecialchars($input->ostype);
		if (isset($input->osversion))  $osVersion = str_replace(",", "", htmlspecialchars($input->osVersion));
		// May check that client is included in the server database?
		// Default processing: log activity.
		file_put_contents(LOG_FILE, date(DATE_RSS).": User logged in: ip=$ip, user=$user, os=$osType:$osVersion.\n", FILE_APPEND);
		// Response. 
		$response = "";
		jsonResponse(200, $response);
	} catch (Exception $e) {
		// Comunication error.
		$response["message"] = $e->getMessage();
		file_put_contents(LOG_FILE, date(DATE_RSS).": ERROR: ".$response["message"]."\n", FILE_APPEND);
		jsonResponse(400, $response);
	}
    }
);

// OGAgent notifies that an user logs out.
$app->post('/ogagent/loggedout',
    function() use ($app) {
	$osType = $osVersion = "";
	try {
		// Reading POST parameters in JSON format.
		$input = json_decode($app->request()->getBody());
		$ip = htmlspecialchars($input->ip);
		$user = htmlspecialchars($input->user);
		if (isset($input->ostype))  $osType = htmlspecialchars($input->ostype);
		if (isset($input->osversion))  $osVersion = str_replace(",", "", htmlspecialchars($input->osVersion));
		// May check that client is included in the server database?
		// Default processing: log activity.
		file_put_contents(LOG_FILE, date(DATE_RSS).": User logged in: ip=$ip, user=$user, os=$osType:$osVersion.\n", FILE_APPEND);
		// Response. 
		$response = "";
		jsonResponse(200, $response);
	} catch (Exception $e) {
		// Comunication error.
		$response["message"] = $e->getMessage();
		file_put_contents(LOG_FILE, date(DATE_RSS).": ERROR: ".$response["message"]."\n", FILE_APPEND);
		jsonResponse(400, $response);
	}
    }
);

?>

