<?php
session_start();
if ($_GET['action']=='logout'){
	$_SESSION = array();
	$errMsg = '<div style="border:solid 2px green;background:lightgreen;color:green;padding:1em;display:inline-block">Votre session est terminée.</div>';
}
if ($_SESSION['username']=='admin'){
	header('Location: main.php');
	exit;
}
if ($_POST){
	if ($_POST['username']=='admin' && $_POST['password']=='monpass'){
		$_SESSION['username']=$_POST['username'];
		header('Location: main.php');
		exit;
	}else{
		$errMsg = '<div style="border:solid 2px red;background:pink;color:red;padding:1em;display:inline-block">Nom d\'utilisateur ou mot de passe invalide.</div>';
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Projet Reilly - Administration - Authentification</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<h1>Accès contrôlé</h1>
		<?php print $errMsg;?>
		<p>Veuillez vous authentifier ci-dessous.</p>
		<form method="POST">
			<input name="username" placeholder="Nom d'utilisateur">
			<input name="password" placeholder="Mot de passe" type="password">
			<input type="submit">
		</form>
	</body>
</html>
