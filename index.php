<?php

	session_start();

	require_once('src/option.php');

	if(!empty($_POST['email']) && !empty($_POST['password'])){

		// Connexion à la bdd
		require_once('src/connection.php');

		// Variables
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);

		// L'adresse email est-elle correcte ?
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

			header('location: index.php?error=1&message=Votre adresse email est invalide.');
			exit();
		}

		// Chiffrement du mot de passe
		$password = "aq1".sha1($password."123")."25";

		//L'adresse email est-elle bien utiliser?
		$req = $bdd->prepare('SELECT COUNT(*) as numberEmail FROM users WHERE email= ?');
		$req->execute([$email]);

		while($emailVerification = $req->fetch()){

			if($emailVerification['numberEmail'] != 1){
				header('location: index.php?error=1&message=Impossible de vous authentifier.');
				exit();
			}
		}

		// Connexion de l'utilisateur
		$req = $bdd->prepare('SELECT * FROM users WHERE email = ?');
		$req->execute([$email]);

		while($user = $req->fetch()){

			if($password == $user['password']){

				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $user['email'];

				if(isset($_POST['auto'])){

					setcookie('auth', $user['secret'], time() + 365*24*3600, '/', null, false, true);

				}

				header('location: index.php?success=1');
				exit();
			}
			else{

				header('location: index.php?error=1&message=Impossible de vous authentifier correctement');
				exit();
			}
		}

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

				<?php if(isset($_SESSION['connect'])) { ?>

					<h1>Bonjour !</h1>
					<?php
					if(isset($_GET['success'])){
						echo'<div class="alert success">Vous êtes maintenant connecté.</div>';
					} ?>
					<p>Qu'allez-vous regarder aujourd'hui ?</p>
					<small><a href="logout.php">Déconnexion</a></small>

				<?php } else { ?>
					<h1>S'identifier</h1>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					} ?>

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="password" name="password" placeholder="Mot de passe" required />
						<button type="submit">S'identifier</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
					</form>
				

					<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php require_once('src/footer.php'); ?>
</body>
</html>