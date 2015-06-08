<?php
include_once('../admin/includes/functions.php');
include_once('../admin/includes/templatesFunctions.php');

function tagNewCommentArea($item){
	return '<!--{newCommentArea}-->'.parseTemplate($item,'../admin/templates/comment.html');
}
$expectedInputs = array('name','mail','web','comment');

$page = realpath('../'.basename($_SERVER['HTTP_REFERER']));
$postId = basename($_SERVER['HTTP_REFERER'],'.html');

$pageContent = file_get_contents($page);
$pageContent = str_replace('<!--{newCommentArea}-->','{newCommentArea}',$pageContent);
foreach ($expectedInputs as $input){
	$data[$input] = strip_tags($_POST[$input]);
}
file_put_contents($page,parseTemplate($data,$pageContent));

if ($_GET['ajaxRequest']){
	echo parseTemplate($data,'../admin/templates/comment.html');
}else{
	header('Location: '.$_SERVER["HTTP_REFERER"]);
}

$commentsFile = realpath($_SERVER['DOCUMENT_ROOT'].'/'.getFromConfig('commentsdirectory')).'/'.$postId;
file_put_contents($commentsFile,json_encode($data)."\n",FILE_APPEND);

