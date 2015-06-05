<?php
function printFooter() {
	print "<hr></body></html>";
}

function printHeader($page,$errMsg=null){
	print '<!DOCTYPE html><html><head><title>Projet Reilly - Administration - ';
	print $page['windowTitle'];
	print '</title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body><h1>Administration</h1><div><a href="index.php?action=logout">Terminer la session</a></div><h2>';
	print $page['title'].'</h2>';
	echo $errMsg;
}

function secureAccess(){
	session_start();
	if (!checkAccess()){
		header('Location: index.php');
		exit;
	}
}

function checkAccess(){
	return ($_SESSION['username']=='admin');
}

function listPostFiles(){
	return glob('posts/*.md');
}

function extractMetaFromPostFile($file){
	$fh=fopen($file,'r');
	$line=fgets($fh);
	fclose($fh);
	return json_decode($line,true);
}

function getFromConfig($var){
	static $config;
	include_once(__DIR__.'/../config.php');
	return $config[$var];
}

function getExtension($filename) {
  $pos = strrpos($filename, '.');
  return substr($filename, $pos+1);
}
