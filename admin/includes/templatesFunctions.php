<?php
function publish(){
	// Nettoyage du répertoire de destination temporaire
	array_map('unlink',glob('newSite/*.html'));

	// Récupérer la liste des fichiers
	$postFiles = listPostFiles();

	// recueillir les informations pour chacun d'eux
	foreach($postFiles as $k => $file){
		$post=array();
		$post['orifile'] = $file;
		$post['url'] = basename($file,'.md').'.html';
		$post['id'] = basename($file,'.md');
		$post['destfile'] = 'newSite/'.$post['url'];
		$post['time'] = filemtime($file);
		$explodedContent = explode("\n",file_get_contents($file),3);
		$post['resume'] = $post['description'] = $explodedContent[1];
		$post['content'] = $explodedContent[2];
		$post['meta'] = json_decode($explodedContent[0],true);
		$post['title'] = $post['meta']['title'];
		$post['template'] = 'templates/post.html';
		$posts[$k]=$post;
	}

	// les trier par ordre inversement chronologique avec usort
	usort($posts,'comparePostsByDate');

	// faire les liens suivants/précédents
	foreach ($posts as $k => $post){
		if ($k) {
			$posts[$k]['previous'] = &$posts[$k-1];
			$posts[$k-1]['next'] = &$posts[$k];
		}
	}

	// lancer le traitement pour chaque chaque article
	foreach ($posts as $post){
		file_put_contents($post['destfile'],parseTemplate($post,'templates/page.html'));
	}

	// génération de la page d'accueil
	$index['template'] = 'templates/index.html';
	$index['items'] = $posts;
	$index['title'] = 'Accueil';
	file_put_contents('newSite/index.html',parseTemplate($index,'templates/page.html'));

	// deplacement des vieux fichiers dans un dossier de sauvegarde
	$oldFiles = glob('../*.html');
	foreach ($oldFiles as $file){
		rename($file,'oldSite/'.basename($file));
	}

	// mise en place des nouveaux fichier
	$newFiles = glob('newSite/*.html');
	foreach ($newFiles as $file){
		rename($file,'../'.basename($file));
	}

	// message de réussite
	return '<div style="border:solid 2px green;background:lightgreen;color:green;padding:1em;display:inline-block">La publication du site est terminée !</div>';
}

function searchTags($str) {
	preg_match_all('/{[a-zA-Z0-9]*}/',$str,$tags);
	return $tags[0];
}

function comparePostsByDate($a,$b){
	if ($a['time']==$b['time']) return 0;
	return ($a['time'] > $b['time']) ? -1 : 1;
}

function parseTemplate($item,$template){
	if (file_exists($template)){
		$template = file_get_contents($template);
	}
	$tags = searchTags($template);
	foreach ($tags as $tag){
		$tagName = substr($tag,1,-1);
		$tagFunction = 'tag'.ucfirst($tagName);
		if (function_exists($tagFunction)){
			$template = preg_replace('/'.$tag.'/',$tagFunction($item),$template,1);
		}
	}
	return $template;
}

function tagContent($item){
	return parseTemplate($item,$item['template']);
}

function tagPostContent($item){
	$parsedown = new Parsedown();
	return ($parsedown->text($item['content']));
}

function tagSiteTitle(){
	return getFromConfig('siteTitle');
}

function tagPostResume($item){
	return $item['resume'];
}

function tagPageTitle($item){
	return $item['title'];
}

function tagPageDescription($item){
	return $item['description'];
}

function tagPostTitle($item){
	return $item['title'];
}

function tagSiteMotto(){
	return getFromConfig('siteMotto');
}

function tagPostsList($item){
	foreach ($item['items'] as $item){
		$result.=parseTemplate($item,'templates/resumepost.html');
	}
	return $result;
}

function tagPostUrl($item){
	return $item['url'];
}

function tagNextPost($item){
	return $item['next']?'<a href="'.tagPostUrl($item['next']).'" title="Article suivant">'.tagPostTitle($item['next']).'</a>':'';
}

function tagHome(){
	return '<a href="index.html">Accueil</a>';
}

function tagPreviousPost($item){
	return $item['previous']?'<a href="'.tagPostUrl($item['previous']).'" title="Article suivant">'.tagPostTitle($item['previous']).'</a>':'';
}

function tagComments($item){
	return parseTemplate($item,'templates/comments.html');
}

function tagCommentForm(){
	return parseTemplate($item,'templates/commentform.html');
}

function tagCommentsList($item){
	$commentsFile = realpath($_SERVER['DOCUMENT_ROOT'].'/'.getFromConfig('commentsdirectory')).'/'.$item['id'];
	if (!file_exists($commentsFile)) return;
	$sComments = file_get_contents($commentsFile);
	$comments = explode("\n",$sComments);
	if (getFromConfig('lastcommentfirst'))
		$comments = array_reverse($comments);
	foreach ($comments as $comment){
		$comment = json_decode($comment,true);
		if ($comment)
			$result.=parseTemplate($comment,'templates/comment.html');
	}
	return $result;
}

function tagCommentName($item){
	return $item['name'];
}

function tagCommentWeb($item){
	return $item['web'];
}

function tagCommentComment($item){
	return nl2br($item['comment']);
}
