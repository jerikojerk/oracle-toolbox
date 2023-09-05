<?php
header('Content-Type: text/html; charset=UTF-8');

if (! function_exists('oci_connect')){

	echo '<pre>';
	print_r(get_loaded_extensions(false));
	echo '</pre>';
	
	die('oci driver not loaded. Can not do anything ');
}

require_once 'inc/toolboxcore.lib.php';

$filter_env=filter_has_var(INPUT_GET,'env')?strtolower(filter_input( INPUT_GET,'env', FILTER_SANITIZE_FULL_SPECIAL_CHARS))	:null;	
$filter_app=filter_has_var(INPUT_GET,'app')?strtoupper(filter_input( INPUT_GET,'app', FILTER_SANITIZE_FULL_SPECIAL_CHARS ))	:null;



switch( $filter_app ){
	case 'GMAO-R':
	case 'GMAO-PS':
		require_once 'conf/gmao.config.php';
		require_once 'inc/gmao.lib.php';
		$title='Vérifications du status des bases '.$filter_app.' en '.$filter_env;
		$testClassName='TestGmaoDataGuard';
		break;
	case 'PACIFIC':
		require_once 'conf/gre.config.php';
		require_once 'inc/gre.lib.php';
		$title='Vérifications du status de la base Pacific en '.$filter_env;
		$testClassName='TestDatabasePacific';
		break;
	case 'ATLAS':
		require_once 'conf/gre.config.php';
		require_once 'inc/gre.lib.php';
		$title='Vérifications du status de la base Atas en '.$filter_env;
		$testClassName='TestDatabaseAtlas';
		break;		
	default: // mettre ici si pas de choix utilisateur.
		require_once 'conf/gmao.config.php';
		require_once 'conf/gre.config.php';
		$title='Page d\'accueil';
}


?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?php echo $title ?></title>
<link href="favicon.ico" rel="shortcut icon" />
<link rel="stylesheet" type="text/css" media="all" href="style_all.css" />
<link rel="stylesheet" type="text/css" media="screen" href="style_screen.css" />
<link rel="stylesheet" type="text/css" media="print" href="style_print.css" />

</head>
<body>
<h1><?php echo $title ?></h1>
<a href="?" class="noprint">revenir page d'accueil</a>
<?php

AdvancedOraConfig::makeHtmlTable($filter_app,$filter_env);

$toBeReported = AdvancedOraConfig::retrieveListOfMatching($filter_app,$filter_env);

if (count($toBeReported)===0){
	echo "<p >Merci d'utiliser le tableau dans le coin supérieur gauche pour choisir une instance pour laquelle generer le rapport.</p>",PHP_EOL;
}
else{
	foreach ( $toBeReported as $paires ){
		foreach( $paires as  $role => $configInstance ){
			//echo "================>";
			//print_r($configInstance);
			
			$cnx= $configInstance->toEasyConnect();
			$html_appli=htmlentities($configInstance->app);
			$html_envname=htmlentities(AdvancedOraConfig::toEnvName( $configInstance->env));
			$html_envcode=htmlentities($configInstance->env);
			$html_role=htmlentities($configInstance->dbrole);
			echo '<div class="scenario">',PHP_EOL,
			'<div class="postIt">',$html_appli,'<br>',$html_envname,'<br>',$html_role,'</div>
			<h2>',$html_appli,'/',$html_envname,'/',$html_role,'</h2>', PHP_EOL,
			'<p>L\'identifiant de base de donnée utilisé est ',htmlentities($cnx),'<span class="noprint">, <a title="lien avec filtre" href="?app=',$html_appli,'&amp;env=',$html_envcode,'">lien</a> pour l\'instance ',$configInstance->isPrimary()?'primaire':'répliquée (dataguard)','</span>.</p>',PHP_EOL;	
			set_time_limit(31);
			$t= new $testClassName( $configInstance );
			$t->scenario();
			echo '</div>',PHP_EOL;
			flush();
		}
	}

	TestDatabase::printTableOfContent();
}

?>
<p>Rapport créé par jerikojerk@github</p>
<div style="height:50px" class="noprint"></div>
</body>
</html>
