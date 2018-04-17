<?php

require_once 'gridExcelGenerator.php';
require_once 'gridExcelWrapper.php';

$title_report = $_GET['report'];

$debug = false;
$error_handler = set_error_handler("PDFErrorHandler");

if (get_magic_quotes_gpc()) {
	$xmlString = stripslashes($_POST['grid_xml']);
} else {
	$xmlString = $_POST['grid_xml'];
}
$xmlString = urldecode($xmlString);

if ($debug == true) {
	error_log($xmlString, 3, 'debug_'.date("Y_m_d__H_i_s").'.xml');
}

$xml = simplexml_load_string($xmlString);
$excel = new gridExcelGenerator();
if($title_report) $excel->title = $title_report;
$excel->printGrid($xml);

function PDFErrorHandler ($errno, $errstr, $errfile, $errline) {
	global $xmlString;
	if ($errno) {
		error_log($xmlString, 3, 'error_report_'.date("Y_m_d__H_i_s").'.xml');
		error_log($errfile, 3, $errno.'_'.'_'.$errline.'_'.'error_report_'.date("Y_m_d__H_i_s").'.log');
//		exit(1);
	}

}

?>