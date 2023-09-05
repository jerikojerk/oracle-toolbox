<?php

require_once 'inc\toolboxcore.lib.php';
require_once '..\CTM\autoload\Debug.class.php';


$csv_filename="password_dbaas.csv";
$csv_separator=';';
$csv_max_len=1024;

#ordre des colonnes dans le CSV
$col_demande=0;
$col_dbuniquename=1;
$col_user=2;
$col_password=3;
$col_server=4;
$col_port=5;
$col_service=6;
$col_dg_server=7;
$col_dg_port=8;
$col_dg_service=9;


$prefered_admins=Array('GMAADMIN','PP06612S');




// lire le CSV
if (($handle = fopen($csv_filename, "r")) === FALSE) {
	
	echo 'can not open ', $csv_filename,' end';
	exit(1);
}

//ignore la premiere ligne.
$data = fgetcsv($handle, $csv_max_len, $csv_separator );

while (($data = fgetcsv($handle, $csv_max_len, $csv_separator )) !== FALSE) {
	$tmp=$data[$col_dbuniquename];
	$base[$tmp]['server']=$data[$col_server];
	$base[$tmp]['port']=$data[$col_port];
	$base[$tmp]['service']=$data[$col_service];
	$base[$tmp]['schema'][]=Array('user'=>$data[$col_user],'pass'=>$data[$col_password]);
	$base[$tmp]['dbaas']=$data[$col_demande];
	$base[$tmp]['cnx']='//'.$data[$col_server].':'.$data[$col_port].'/'.$data[$col_service];
	
	
	if (!empty( $data[$col_dg_server] ) and !empty($data[$col_dg_port]) and !empty($data[$col_dg_service])){
		$base[$tmp]['cnx_dg']='//'.$data[$col_dg_server].':'.$data[$col_dg_port].'/'.$data[$col_dg_service];		
	}
	//print_r($base[$tmp]);
}
fclose($handle);


function connectToDb($user,$pass, $oracleEasyConnect){
	if (!empty($pass)){
		$db = new OCI_DB_Wrapper(  $user, $pass, $oracleEasyConnect , 'UTF8'); 
		$db ->exec ("Alter Session Set NLS_LANGUAGE='FRENCH'");
		$db ->exec ("Alter Session Set NLS_DATE_FORMAT='yyyy/mm/dd hh24:mi:ss (day)'");
		return $db;
	}
	else {
		throw new OraNoPasswordException('no password provided');
	}
}


function loopOnAudit( AuditItem $ai, $openDatabases ){
	set_time_limit(31);
	$html = new Statement2Html( $ai->title, $ai->headers, array('N°','Database Unique Name','Connector'));
	$html->printTableBegin('auditResultTable');
	$max=count($openDatabases);
	$it;
	foreach ($openDatabases as $dbuniquename=>$mydb ){
		$it++;
		$stmt = $ai->execute($mydb['db']);
		$printed=$html->printTableContent($stmt, array( makeDbaasLink($mydb['dbaas']), $dbuniquename,$mydb['cnx']));
		if ($printed>1 and $it < $max ){
			echo '</tbody><tbody>', PHP_EOL;
		}
	}
	$html->printTableEnd();
}


function makeDbaasLink($id){
	return '<a href="https://indus.oi.enedis.fr/CEA/index.php/dbaas/view/'.htmlspecialchars($id).'">'.htmlspecialchars($id).'</a>';
}





?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $title ?></title>
<link href="favicon.ico" rel="shortcut icon" />
<link rel="stylesheet" type="text/css" media="all" href="style_all.css" />
<link rel="stylesheet" type="text/css" media="screen" href="style_screen.css" />
<link rel="stylesheet" type="text/css" media="print" href="style_print.css" />

<link href="/CTM/js/columnFilter.css" rel="stylesheet" />
<script type="text/javascript" src="/CTM/lib/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="/CTM/js/columnFilter.js"></script>

</head>
<body>

<table>
	<caption>Vérification des mots de passes </caption>
	<thead>
		<tr>
			<th>demande</th>
			<th>db unique name</th>
			<th>user</th>
			<th>status password</th>
			<th>connecstring</th>
			<th>dataguard connecstring</th>
		</tr>
	</thead>
	<tbody>
<?php


$openDatabases=Array();
foreach ( $base as $dbuniquename => $config ){
	foreach( $config['schema'] as $identity ){
		echo '<tr><td>', makeDbaasLink($config['dbaas']),	
			'</td><td>',htmlspecialchars($dbuniquename),
			'</td><td>',htmlspecialchars($identity['user']);
			
			if ( substr($identity['user'],1,1)==='!'){
				echo '</td><td class="warning">desactivé</td>';
			}
			else {
	try{
		
		
		$db=connectToDb($identity['user'], $identity['pass'], $config['cnx']);
		echo '</td><td class="pass">password ok</td>';
		if ( in_array($identity['user'], $prefered_admins ) ){
			$openDatabases[$dbuniquename]=array('cnx'=>$config['cnx'], 'user'=>$identity['user'], 'db'=>$db, 'dbaas'=> $config['dbaas'], 'primary'=>true)  ;
		}
		
//			if (isset( $config['cnx_dg'] )) {
//				$openDatabases[$dbuniquename]=array('cnx'=>$config['cnx_dg'], 'user'=>$identity['user'], 'db'=>$db, 'dbaas'=> $config['dbaas'], 'primary'=>false)  ;
//			}
	}
	catch(OracleException $e){
		echo '</td><td class="fail">password ko:',$e->getMessage(),'</td>';
	}
	catch (ConfigException $e){
		echo '</td><td class="fail">skipped:',$e->getMessage(),'</td>';			
	}
			}
		echo '<td>', $config['cnx'],'</td><td>', isset($config['cnx_dg'])?$config['cnx_dg']:'','</td></tr>', PHP_EOL;
	}//for


	flush();
}
?>
	</tbody>
</table>
<?php
unset($base);




//print_r($openDatabases);


loopOnAudit(AuditItem::readIdentite(), $openDatabases );
loopOnAudit(AuditItem::readRoleDB(), 	$openDatabases );
loopOnAudit(AuditItem::readArchiver(), $openDatabases );
loopOnAudit(AuditItem::readArchivelogConf(), $openDatabases );
loopOnAudit(AuditItem::readCursorConfiguration(), $openDatabases );
loopOnAudit(AuditItem::readDatabasePrimary(), $openDatabases );
loopOnAudit(AuditItem::readNLS(), $openDatabases );

loopOnAudit(new AuditItem('Lecture Parametres d\'initialisation maximo',
		OracleHelper::listHelper('nom, valeur, valeur affichable, recommendation maximo 2017, défaut oracle, description'),
		'select p.name, p.value, p.display_value, r.recommendation, p.isdefault, p.description, CASE WHEN r.recommendation <> p.value or r.recommendation <> p.display_value THEN :wr ELSE NULL END CSS_ 
		FROM v$parameter p inner join (select :p01 as name,:v01 as recommendation from dual UNION select :p02,:v02 from dual UNION select :p03,:v03 from dual UNION select :p04,:v04 from dual UNION select :p05,:v05 from dual UNION select :p06,:v06 from dual UNION select :p07,:v07 from dual UNION select :p08,:v08 from dual UNION select :p09,:v09 from dual UNION select :p10,:v10 from dual UNION select :p11,:v11 from dual UNION select :p12,:v12 from dual UNION select :p13,:v13 from dual UNION select :p14,:v14 from dual UNION select :p15,:v15 from dual UNION select :p16,:v16 from dual) r on p.name = r.name order by 1',
		array(1),array(':wr'=>'warning',
			':p01'=>'optimizer_mode',
			':v01'=>'FIRST_ROWS_100',
			':p02'=>'pga_aggregate_target',
			':v02'=>'0',
			':p03'=>'sga_target',
			':v03'=>'0',
			':p04'=>'sga_max_size',
			':v04'=>'0',
			':p05'=>'memory_target',
			':v05'=>'6G',
			':p06'=>'memory_max_target',
			':v06'=>'6G',
			':p07'=>'processes',
			':v07'=>'4000',
			':p08'=>'sessions',
			':v08'=>'4000',
			':p09'=>'nls_length_semantics',
			':v09'=>'BYTE',
			':p10'=>'transactions',
			':v10'=>'2425',
			':p11'=>'session_cached_cursors',
			':v11'=>'400',
			':p12'=>'cursor_sharing',
			':v12'=>'FORCE',
			':p13'=>'session_max_open_files',
			':v13'=>'300',
			':p14'=>'cursor_sharing',
			':v14'=>'FORCE',
			':p15'=>'open_cursors',
			':v15'=>'3000',
			':p16'=>'workarea_size_policy',
			':v16'=>'AUTO'
	)), $openDatabases );



loopOnAudit(AuditItem::readPermissions(array('GMAADMIN','MAXIMO','MAXIMO_VISU','EGMAOPS','AAMADMIN') ), $openDatabases);


?>

<script type="text/javascript">
 $( document ).ready(function() {
	$table=$('table.auditResultTable');
	$table.find('th:nth-child(1),th:nth-child(2),th:nth-child(3)').addClass('filterSelect')
	$table.columnFilter({forceInputRegex:/^date|^max|^CURRENT_DATE|valeur/i,importFilters:{}.filters,exportFilters:{}});
	 
 });
</script>

</body>
</html>

