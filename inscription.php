<?php

	session_start();

	//Vérification du formulaire
	if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])){

		// Connexion à la base de donnée
		require_once('src/connection.php');

		// Variable
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);
		$passwordTwo = htmlspecialchars($_POST['password_two']);

		// Les mots de passe sont-ils different ?
		if($password != $passwordTwo){

			header('location: inscription.php?error=1&message=Vos mots de passe ne sont pas identiques');
			exit();
		}

		// L'adresse email est-elle correcte ?
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

			header('location: inscription.php?error=1&message=Votre adresse email est invalide.');
			exit();
		}

		//L'adresse email est-elle en doublon ?
		$req = $bdd->prepare('SELECT COUNT(*) as numberEmail FROM users WHERE email= ?');
		$req->execute([$email]);

		while($emailVerification = $req->fetch()){

			if($emailVerification['numberEmail'] != 0){
				header('location: inscription.php?error=1&mesage=Votre adresse email est déjà utitlisée par un autre utilisateurs');
				exit();
			}
		}

		// Chiffrement du mot de passe
		$password = "aq1".sha1($password."123")."25";

		// Secret
		$secret = sha1($email).time();
		$secret = sha1($secret).time();

		// Ajouter un utitlisateur
		$req = $bdd->prepare('INSERT INTO users(email, password, secret) VALUES(?, ?, ?)');
		$req->execute([$email, $password, $secret]);

		header('location: inscription.php?success=1');
		exit();

	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="./assets/favicon.png">
</head>
<body>

	<?php require_once('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php
				if(isset($_GET['error']) && isset($_GET['message'])){

					echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';

				}elseif(isset($_GET['success'])){

					echo '<div class="alert success">Vous êtes desormais inscrit. <a href="index.php">Connectez-vous</a>.</div>';
				}
			?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php require_once('src/footer.php'); ?>
</body>
</html>