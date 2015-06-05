<?php
$page['title'] = 'Accueil';
$page['windowTitle'] = 'Gestion des articles';
include_once('includes/functions.php');
secureAccess();
if ($_GET){
	if ($_GET['action']=='delete'){
		$filename = 'posts/'.$_GET['file'].'.md';
		if (unlink($filename)){
			$errMsg = '<div style="border:solid 2px green;background:lightgreen;color:green;padding:1em;display:inline-block">'.$filename.' a bien été effacé.</div>';
		}else{
			$errMsg = '<div style="border:solid 2px red;background:pink;color:red;padding:1em;display:inline-block">Impossible d\'effacer le fichier '.$filename.'</div>';
		}
	}
	if ($_GET['action']=='publish'){
		include_once('includes/templatesFunctions.php');
		include_once('libs/parsedown/Parsedown.php');
		$errMsg = publish();
	}
}
printHeader($page,$errMsg);
?>
		<p><a href="edit.php">Créer un nouvel article</a> - <a href="?action=publish">Publier le site</a>
		<table border="1">
			<tr><th>Titre</th><th>Actions</th></tr>
<?php
$files = listPostFiles();
foreach ($files as $file){
	$metaData = extractMetaFromPostFile($file);
	$shortFile = basename($file,'.md');
	print '<tr><td>'.$metaData['title'].'</td><td><a href="edit.php?edition='.$shortFile.'">Modifier</a> - <a href="?action=delete&file='.$shortFile.'">Supprimer</a></td></tr>';
}
?>
		</table>
<?php
printFooter();
