<?php
$page['title'] = 'Création / Edition d\'articles';
$page['windowTitle'] = 'Article';
include_once('includes/functions.php');
secureAccess();

if ($_POST){
	if (trim($_POST['title'])){
		$fileName = $_GET['edition']?$_GET['edition']:strtolower(trim($_POST['title']));
		$originCharacters = 'àçéèêîôù';
		$destinCharacters = 'aceeeiou';
		$fileName = strtr($fileName,$originCharacters,$destinCharacters);
		$fileName = preg_replace('/[^a-z0-9-]/','-',$fileName);
		$fileName = 'posts/'.$fileName.'.md';
		$metaData['title'] = $_POST['title'];
		$fileContent = json_encode($metaData)."\n";
		$fileContent.= str_replace("\n",'',strip_tags($_POST['description']))."\n";
		$fileContent.= strip_tags($_POST['content']);

		if (file_put_contents($fileName,$fileContent)) {
			header('Location: main.php');
			exit;
		}else{
			$errMsg = '<div style="border:solid 2px red;background:pink;color:red;padding:1em;display:inline-block">Impossible d\'enregistrer le fichier '.$fileName.'</div>';
		}
	}else{
		$errMsg = '<div style="border:solid 2px red;background:pink;color:red;padding:1em;display:inline-block">Titre insuffisant</div>';
	}
}elseif ($_GET['edition']){
	$fileContent = file_get_contents('posts/'.$_GET['edition'].'.md');
	$explodedContent = explode("\n",$fileContent,3);
	$metaData = json_decode($explodedContent[0],true);
	$description = $explodedContent[1];
	$content = $explodedContent[2];
}
printHeader($page,$errMsg);
?>
		<form method="POST">
			<label for="title">Titre de l'article</label> <input id="title" name="title" <?php if ($metaData['title']) echo 'value="'.$metaData['title'].'"';?>><br>
			<label for="description">En-tête (sans retour à la ligne)</label><br>
			<textarea id="description" name="description" rows="5" cols="60"><?php if ($description) echo $description;?></textarea><br>
			<label for="content">Contenu (<a href="http://fr.wikipedia.org/wiki/Markdown" target="_blank">Markdown</a> autorisé)</label><br>
			<textarea id="content" name="content" rows="25" cols="60"><?php if ($content) echo $content;?></textarea>
			<iframe src="imgmgr.php" height="388" width="300"></iframe>
			<br>
			<input type="submit">
			<hr>
			<p>Le <a href="http://fr.wikipedia.org/wiki/Markdown" target="_blank">Markdown</a> est un langage très léger, facile à lire et à écrire, destiné à être converti en HTML. Quelques éléments de syntaxe :</p>
			<ul>
				<li>*texte en italique* ou _texte en italique_</li>
				<li>**texte en gras** ou **texte en gras**</li>
				<li>#titre de premier niveau (à ne pas utiliser)</li>
				<li>##titre de second niveau</li>
				<li>###### jusqu'à six niveaux de titre</li>
				<li> > une citation (comme pour les mails)</>
				<li> un morceau de `code` dans un texte</li>
				<li> * un point de liste non numérotée</li>
				<li> 1. un point de liste numérotée</li>
				<li> [un lien vers Linux Pratique](http://www.linux-pratique.com)</li>
				<li> ![une image](http://www.linux-pratique.com/wp-content/uploads/2014/10/blog_lp_bannier.png)</li>
			</ul>
		</form>
<?php
printFooter();
