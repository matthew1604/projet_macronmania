<?php

	require_once (File::build_path(array('model', 'ModelClients.php'))); // chargement du modèle

	class ControllerClients {
		protected static $object = 'Client';

		/************************************************************************************/

	    public static function Compte() {

	    	$pagetitle = 'MacronMania | Compte';
	    	$controller = 'Client';
	    	$view = 'Compte';
	        require(File::build_path(array('view', 'view.php')));
	    }

	    /************************************************************************************/

	    public static function Contact() {
	    	if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['mail']) || 
	    		empty($_POST['sujet']) || empty($_POST['msg'])) {
		    	$pagetitle = 'MacronMania | Contact';
		    	$controller = 'Client';
		    	$view = 'Contact';
		        require(File::build_path(array('view', 'view.php')));
		    }
		    else {
		    	$headers ='From: "' . $_POST["nom"] . ' ' . $_POST["prenom"] . '"<' . $_POST["mail"] . '>' . "\n";
			    $headers .='Content-Type: text/plain; charset="iso-8859-1"'."\n";
			    $headers .='Content-Transfer-Encoding: 8bit';

		    	$envoye = mail('vergely.matt@gmail.com', $_POST["sujet"], $_POST["msg"], $headers);
			    $pagetitle = 'MacronMania | Contact';
		    	$controller = 'Client';
		    	$view = 'Contact';
		        require(File::build_path(array('view', 'view.php')));
		    }
	    }

	    /************************************************************************************/

	    public static function Inscription() {
	    	if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['pseudo']) || 
	    		empty($_POST['mdp']) || empty($_POST['mdpVerif']) || empty($_POST['mail'])) {
		    	$pagetitle = 'MacronMania | Inscription';
		    	$controller = 'Client';
		    	$view = 'Inscription';
		    	require(File::build_path(array('view', 'view.php')));
		    }
		    else {
		    	if ($_POST['mdp'] == $_POST['mdpVerif']) {
		    		$pseudo = htmlspecialchars($_POST['pseudo']);
		    		$nom = htmlspecialchars($_POST['nom']);
		    		$prenom = htmlspecialchars($_POST['prenom']);
		    		$mail = htmlspecialchars($_POST['mail']);
		    		$mdp = htmlspecialchars($_POST['mdp']);

					$newClient = new ModelClients($pseudo, $nom, $prenom, $mail, $mdp);
					$create = $newClient->create();
					if ($create) {
						$pagetitle = 'MacronMania | Inscription terminée';
				    	$controller = 'Client';
				    	$view = 'FinishInscription';
				        require(File::build_path(array('view', 'view.php')));
				    }
				    else {
				    	$inscrip = false;
						$pagetitle = 'MacronMania | Inscription';
				    	$controller = 'Client';
				    	$view = 'Inscription';
						require (File::build_path(array('view', "view.php")));
				    }
				} else {
					$inscrip = false;
					$pagetitle = 'MacronMania | Inscription';
			    	$controller = 'Client';
			    	$view = 'Inscription';
					require (File::build_path(array('view', "view.php")));
				}
		    }
	    }

	    /************************************************************************************/

	    public static function Connexion() {
    		if (empty($_POST['pseudo']) || empty($_POST['mdp'])) {
	    		$pagetitle = 'MacronMania | Connexion';
	    		$controller = 'Client';
	    		$view = 'Connexion';
	    	    require(File::build_path(array('view', 'view.php')));
	    	}
	    	else {
	    		$user = ModelClients::connect($_POST['pseudo']);
	    		if ($user == false) {
	    			$connect = false;
	    		} else {
	    			$user = $user[0];
	    			if ($user->getMdp() == hash('sha256', $_POST['mdp'])) {
	    				/**** TODO TD7 SESSIONS ****/
	    				
	    				session_start();
	    				$_SESSION['id'] = $user->getId();
	    				if ($_SESSION['id'] == 1) $_SESSION['isAdmin'] = true;
	    				else $_SESSION['isAdmin'] = false;
	    				$_SESSION['pseudo'] = $user->getPseudo();
	    				$_SESSION['nom'] = $user->getNom();
	    				$_SESSION['prenom'] = $user->getPrenom();
	    				$_SESSION['mail'] = $user->getMail();
	    				$_SESSION['mdp'] = $user->getMdp();
	    				$connect = true;

	    				require_once(File::build_path(array('model', 'ModelJeux.php')));
	    				$allJeux = ModelJeux::selectAll();
	    				$pagetitle = 'MacronMania | Accueil';
				        $controller = 'Client';
				        $view = 'Connected';
				        require (file::build_path(array('view', 'view.php')));
	    			}
	    			else {
	    				$connect = false;
	    			}
	    		}
	    		if ($connect == false) {
		    		$pagetitle = 'MacronMania | Connexion';
				    $controller = 'Client';
				    $view = 'Connexion';
					require (File::build_path(array('view', "view.php")));
				}
	    	}
	    }

	    /************************************************************************************/

	    public static function Deconnexion() {
	    	session_start(); 
	    	session_unset();
			session_destroy();

	    	require_once(File::build_path(array('model', 'ModelJeux.php')));
	    	$allJeux = ModelJeux::selectAll();
	    	$pagetitle = 'MacronMania | Accueil';
			$controller = 'Client';
			$view = 'Deconnected';
			require (file::build_path(array('view', 'view.php')));
	    }

		/************************************************************************************/

		public static function update() {
			session_start();
			$Client = ModelClients::select($_SESSION['id']);
			$id = $Client->getId();
			$pseudo = $Client->getPseudo();
			$nom = $Client->getNom();
			$prenom = $Client->getPrenom();
			$mail = $Client->getMail();
			$mdp = $Client->getMdp();

			$action = 'updated';
			$pagetitle = 'MacronMania | Modifier mes informations';
	        $controller = 'Client';
	        $view = 'Update';
	    	require_once(file::build_path(array('view', 'view.php')));
	    }

		/************************************************************************************/

		public static function updated() {
			session_start();
			$maj = ModelClients::update(array('idClient' => $_SESSION['id'],
											  'nomClient' => htmlspecialchars($_GET['nom']),
											  'prenomClient' => htmlspecialchars($_GET['prenom']),
											  'mailClient' => htmlspecialchars($_GET['mail']) ));

			$client = ModelClients::select($_SESSION['id']);
			$_SESSION['nom'] = $client->getNom();
			$_SESSION['prenom'] = $client->getPrenom();
			$_SESSION['mail'] = $client->getMail();

			if ($maj) {
				$msg = "Vos informations ont bien été modifiés";
			} else {
				$msg = "Erreur, vos informations n'ont pas pu être modifiée";
			}

			$pagetitle = 'MacronMania | Modifié';
	        $controller = 'Client';
	        $view = 'Updated';
	    	require_once(file::build_path(array('view', 'view.php')));

			/*$maj = ModelJeux::update(array('idJeu' => $_GET['id'],
										   'nomJeu' => $_GET['nom'],
										   'plateforme' => $_GET['plateforme'],
										   'genre' => $_GET['genre'],
										   'image' => $_GET['image'],
										   'noteSur5' => $_GET['note'],
										   'prix' => $_GET['prix']));

			if ($maj) {
				$msg = "Le jeu à bien été modifié.";
			} else {
				$msg = "Erreur, les modifications n'ont pas été prise en compte";
			}

			$pagetitle = 'MacronMania | Modifié';
	        $controller = 'Jeux';
	        $view = 'Updated';
	    	require_once(file::build_path(array('view', 'view.php')));*/
		}   

	}

?>