<?php
$page['title'] = 'Gestionnaire d\'images';
$page['windowTitle'] = 'Gestion des images';
include_once('includes/functions.php');
include_once('includes/imagesFunctions.php');
secureAccess();
$imagesRoot = getFromConfig('imgdirectory');
if (!array_key_exists('imgmgr',$_SESSION)) $_SESSION['imgmgr'] = array();
// Réglage du répertoire courant
if ($_GET['chdir']) {
	$newDir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$_GET['chdir']);
	$absoluteImagesRoot = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$imagesRoot);
	if (substr($newDir,0,strlen($absoluteImagesRoot)) == $absoluteImagesRoot){
		$curDir = $_SESSION['imgmgr']['currentdir'] = $_GET['chdir'];
	}else{
		// il s'agit d'un chemin interdit !
		$curDir = $_SESSION['imgmgr']['currentdir'] = $imagesRoot;
	}
}elseif($_SESSION['imgmgr']['currentdir']){
	$curDir = $_SESSION['imgmgr']['currentdir'];
}else{
	$curDir = $_SESSION['imgmgr']['currentdir'] = $imagesRoot;
}
$absoluteCurDir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$curDir).'/';

if ($_GET['action']=='upload' && $_POST && $_FILES['imagefile']) {
	if (strpos($_FILES['imagefile']['type'],'image')!==0){
		$errMsg = '<div style="border:solid 2px red;background:pink;color:red;padding:1em;display:inline-block">Le fichier téléchargé n\'est pas reconnu comme une image.</div>';
	}else{
		move_uploaded_file($_FILES['imagefile']['tmp_name'], $absoluteCurDir.basename($_FILES['imagefile']['name']));
	}
}

if ($_GET['action']=='createdir' && $crdir = basename($_POST['directoryname'])) {
	if (mkdir($absoluteCurDir.$crdir)) {
		$curDir.=$crdir.'/';
		$absoluteCurDir = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$curDir).'/';
	}else{
		$errMsg = '<div style="border:solid 2px red;background:pink;color:red;padding:1em;display:inline-block">Impossible de créer le dossier '.$cddir.'</div>';
	}
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body>
	<?php echo $errMsg;?>
<h2>Dossiers</h2>
Emplacement actuel :
<?php
echo '/'.substr($curDir,strlen($imagesRoot),-1);
// remonter d'un niveau ?
if ($curDir != $imagesRoot) {
	print '<br /><a href="?chdir='.dirname($curDir).'/">Remonter d\'un niveau</a>';
}

//affichage des dossiers
$dirs = glob($absoluteCurDir.'*', GLOB_ONLYDIR);
if ($dirs){
	print '<ul>';
	foreach ($dirs as $dir)
	{
		$dir=basename($dir);
		print '<li>';
		print '<a href="?chdir='.$curDir.$dir.'/">'.$dir.'</a>';
		print '</li>';
	}
	print '</ul>';
}
?>
<h2>Images</h2>
<?php
$imageFiles = array();
$finfo = finfo_open(FILEINFO_MIME_TYPE);
foreach (glob($absoluteCurDir.'*') as $filename) {
	if (strpos(finfo_file($finfo, $filename),'image')===0){
		$imageFiles[]=$filename;
	}
}
finfo_close($finfo);
if ($imageFiles){
	$thumbWidth = getFromConfig('thumbwidth');
	$thumbHeight = getFromConfig('thumbheight');
	foreach ($imageFiles as $imageFile){
		$url = substr(realpath($imageFile),strlen($_SERVER['DOCUMENT_ROOT']));
		print '<img src="'.getResized($imageFile,$thumbWidth,$thumbHeight).'" onclick="javascript:c=parent.document.getElementById(\'content\');c.value+=\'![texte]('.$url.')\';c.focus();">';
		print '<button onclick="javascript:c=parent.document.getElementById(\'titlepic\');c.value=\''.$url.'\';">Utiliser comme image de titre</button>';
	}
}

?>
<hr>
<form enctype="multipart/form-data" method="POST" action="?action=upload">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo getFromConfig('maxuploadedfilesize');?>" >
	<fieldset><legend>Envoyer une image</legend>
		<label for="imagefile">Choisissez une image à télécharger :</label><br>
		<input type="file" name="imagefile"><br>
		<input type="submit">
	</fieldset>
</form>
<form method="POST" action="?action=createdir">
	<fieldset><legend>Créer un dossier</legend>
		<label for="directoryname">Nom du dossier à créer :</label><br>
		<input name="directoryname"><br>
		<input type="submit">
	</fieldset>
</form>
</body></html>
