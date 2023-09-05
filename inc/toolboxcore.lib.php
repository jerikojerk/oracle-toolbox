<?php

if ( !defined('TOOLBOX_LOCK_PATH') ){
	Define( 'TOOLBOX_LOCK_PATH',realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'locks').DIRECTORY_SEPARATOR);
}

Abstract Class OracleException extends Exception {};
 Abstract Class OracleQueryException extends OracleException{};
  Class OraNotAvailableException extends OracleConnectionException{};
  Class OraNotOpenException extends OracleConnectionException{};
  Class OraListenerException extends OracleConnectionException{};
  Class OraUnusableAccountException extends OracleConnectionException{};
  Class OraResolverException extends OracleConnectionException{};
  Class OraNoPasswordException extends  OracleConnectionException{};
 Abstract Class OracleConnectionException extends OracleException{}; 
  Class OraBadSqlException extends OracleQueryException{};
  Class OraTableViewNotFoundNoRights extends OracleQueryException{};
  Class OraStatementException extends OracleQueryException{};
  Class OraDataException extends OracleQueryException{};
 Class OraUnknownException extends OracleException{};

Class ConfigException extends Exception {}; 





set_error_handler( 'OracleHelper::error2exception' );

Class OracleHelper {

	static $ORACLE_ACCOUNTS=Array('ANONYMOUS', 'APEX_030200', 'APEX_040200', 'APEX_PUBLIC_USER', 'APPQOSSYS', 'AUDSYS', 'AWR_STAGE', 'CSMIG', 'CTXSYS', 'DBSNMP', 'DEMO', 'DIP', 'DMSYS', 'DSSYS', 'DVF', 'DVSYS', 'EXFSYS', 'FLOWS_FILES', 'GSMADMIN_INTERNAL', 'GSMCATUSER', 'GSMUSER', 'HR', 'IX', 'LBACSYS', 'MDDATA', 'MDSYS', 'OE', 'OJVMSYS', 'OLAPSYS', 'ORACLE_OCM', 'ORDDATA', 'ORDPLUGINS', 'ORDSYS', 'OUTLN', 'PERFSTAT', 'PM', 'RMAN', 'SH', 'SI_INFORMTN_SCHEMA', 'SPATIAL_CSW_ADMIN_USR', 'SPATIAL_WFS_ADMIN_USR', 'SQLTXADMIN', 'SQLTXPLAIN', 'SYS', 'SYSBACKUP', 'SYSDG', 'SYSKM', 'SYSMAN', 'SYSTEM', 'TSMSYS', 'WMSYS', 'XDB','XS$NULL');
	static $TECHNICAL_ACCOUNTS=Array('OPS$PATROL','OPS$ORACLE','PATROL','SCOTT','OEM_SEO','OEM_SNX');
	
	static $PageCode = 'UTF-8';
	static $driverCode = 'UTF-8';
	
	private static $locktypecode=Array(0=>'0-none',1=>'1-null',2=>'2-rows-S (SS)',3=>'3-row-X (SX)',4=>'4-share (S)',5=>'5-S/Row-X (SSX)',6=>'6-exclusive');
	
	// the figure here are commands identified on oracle online documentation 
	private static $commandcode= Array( 0=>'', 1=>'CREATE TABLE', 2=>'INSERT', 3=>'SELECT', 4=>'CREATE CLUSTER', 5=>'ALTER CLUSTER', 6=>'UPDATE', 7=>'DELETE', 8=>'DROP CLUSTER', 9=>'CREATE INDEX', 10=>'DROP INDEX', 11=>'ALTER INDEX', 12=>'DROP TABLE', 13=>'CREATE SEQUENCE', 14=>'ALTER SEQUENCE', 15=>'ALTER TABLE', 16=>'DROP SEQUENCE', 17=>'GRANT OBJECT', 18=>'REVOKE OBJECT', 19=>'CREATE SYNONYM', 20=>'DROP SYNONYM', 21=>'CREATE VIEW', 22=>'DROP VIEW', 23=>'VALIDATE INDEX', 24=>'CREATE PROCEDURE', 25=>'ALTER PROCEDURE', 26=>'LOCK', 27=>'NO-OP', 28=>'RENAME', 29=>'COMMENT', 30=>'AUDIT OBJECT', 31=>'NOAUDIT OBJECT', 32=>'CREATE DATABASE LINK', 33=>'DROP DATABASE LINK', 34=>'CREATE DATABASE', 35=>'ALTER DATABASE', 36=>'CREATE ROLLBACK SEG', 37=>'ALTER ROLLBACK SEG', 38=>'DROP ROLLBACK SEG', 39=>'CREATE TABLESPACE', 40=>'ALTER TABLESPACE', 41=>'DROP TABLESPACE', 42=>'ALTER SESSION', 43=>'ALTER USER', 44=>'COMMIT', 45=>'ROLLBACK', 46=>'SAVEPOINT', 47=>'PL/SQL EXECUTE', 48=>'SET TRANSACTION', 49=>'ALTER SYSTEM', 50=>'EXPLAIN', 51=>'CREATE USER', 52=>'CREATE ROLE', 53=>'DROP USER', 54=>'DROP ROLE', 55=>'SET ROLE', 56=>'CREATE SCHEMA', 57=>'CREATE CONTROL FILE', 59=>'CREATE TRIGGER', 60=>'ALTER TRIGGER', 61=>'DROP TRIGGER', 62=>'ANALYZE TABLE', 63=>'ANALYZE INDEX', 64=>'ANALYZE CLUSTER', 65=>'CREATE PROFILE', 66=>'DROP PROFILE', 67=>'ALTER PROFILE', 68=>'DROP PROCEDURE', 70=>'ALTER RESOURCE COST', 71=>'CREATE MATERIALIZED VIEW LOG', 72=>'ALTER MATERIALIZED VIEW LOG', 73=>'DROP MATERIALIZED VIEW LOG', 74=>'CREATE MATERIALIZED VIEW', 75=>'ALTER MATERIALIZED VIEW', 76=>'DROP MATERIALIZED VIEW', 77=>'CREATE TYPE', 78=>'DROP TYPE', 79=>'ALTER ROLE', 80=>'ALTER TYPE', 81=>'CREATE TYPE BODY', 82=>'ALTER TYPE BODY', 83=>'DROP TYPE BODY', 84=>'DROP LIBRARY', 85=>'TRUNCATE TABLE', 86=>'TRUNCATE CLUSTER', 91=>'CREATE FUNCTION', 92=>'ALTER FUNCTION', 93=>'DROP FUNCTION', 94=>'CREATE PACKAGE', 95=>'ALTER PACKAGE', 96=>'DROP PACKAGE', 97=>'CREATE PACKAGE BODY', 98=>'ALTER PACKAGE BODY', 99=>'DROP PACKAGE BODY', 100=>'LOGON', 101=>'LOGOFF', 102=>'LOGOFF BY CLEANUP', 103=>'SESSION REC', 104=>'SYSTEM AUDIT', 105=>'SYSTEM NOAUDIT', 106=>'AUDIT DEFAULT', 107=>'NOAUDIT DEFAULT', 108=>'SYSTEM GRANT', 109=>'SYSTEM REVOKE', 110=>'CREATE PUBLIC SYNONYM', 111=>'DROP PUBLIC SYNONYM', 112=>'CREATE PUBLIC DATABASE LINK', 113=>'DROP PUBLIC DATABASE LINK', 114=>'GRANT ROLE', 115=>'REVOKE ROLE', 116=>'EXECUTE PROCEDURE', 117=>'USER COMMENT', 118=>'ENABLE TRIGGER', 119=>'DISABLE TRIGGER', 120=>'ENABLE ALL TRIGGERS', 121=>'DISABLE ALL TRIGGERS', 122=>'NETWORK ERROR', 123=>'EXECUTE TYPE', 157=>'CREATE DIRECTORY', 158=>'DROP DIRECTORY', 159=>'CREATE LIBRARY', 160=>'CREATE JAVA', 161=>'ALTER JAVA', 162=>'DROP JAVA', 163=>'CREATE OPERATOR', 164=>'CREATE INDEXTYPE', 165=>'DROP INDEXTYPE', 167=>'DROP OPERATOR', 168=>'ASSOCIATE STATISTICS', 169=>'DISASSOCIATE STATISTICS', 170=>'CALL METHOD', 171=>'CREATE SUMMARY', 172=>'ALTER SUMMARY', 173=>'DROP SUMMARY', 174=>'CREATE DIMENSION', 175=>'ALTER DIMENSION', 176=>'DROP DIMENSION', 177=>'CREATE CONTEXT', 178=>'DROP CONTEXT', 179=>'ALTER OUTLINE', 180=>'CREATE OUTLINE', 181=>'DROP OUTLINE', 182=>'UPDATE INDEXES', 183=>'ALTER OPERATOR');
	
	
	const DRIVER_CALL_PATTERN = '/\\(ext\\\\pdo_oci\\\\oci_driver\\.c:[0-9]+\\)/';
	static function Exception(Exception &$e) {
		//Debug::printStack();
		$tmp = self::analyse($e->getMessage());
		return self::aboutORA($tmp, $e);
	}

	static function error2exception (  $errno , $errstr , $errfile , $errline , $errcontext ){
		if ( substr($errstr,0,4) === 'oci_' ){
			throw OracleHelper::generateOciException(array('code'=>OracleHelper::analyse($errstr), 'message'=>$errstr, 'sqltext'=>null ));
		}
	}
	
	static function generateOciException($oci_error_array){
		//echo __FILE__,':',__LINE__,'::';
		//($oci_error_array);
		return static::aboutORA($oci_error_array['code'], $oci_error_array['message'], $oci_error_array['sqltext']);
	}
	static function generateOracleException($errno, $message){
		return static::aboutORA($errno, $message, '' );
	}
	
	
	protected static function makeQueryDecode($array,$prefix){
		$funct=function(&$v,$k) use ($prefix){ $v=$k.',:'.$prefix.$k;};
		array_walk( $array ,  $funct );
		return implode(', ', $array); 
	}
	
	// turn a list into a array for statement bind.
	static function makeQueryBinds($array,$prefix){
		$tmp=$array;
		$funct=function(&$v,$k) use ($prefix){ $v= ':'.$prefix.$k;};
		$keys=array_walk( $tmp,  $funct );
		return array_combine($tmp,$array );
	}
		
	// turn an array into a string separated by a comma.
	static function makeQueryList($array,$prefix){
		$funct=function(&$v,$k) use ($prefix){ $v=':'.$prefix.$k;};
		array_walk( $array ,  $funct );
		return implode(', ', $array); 
	}

	static function makeQueryCommandCodeDecode($prefix){
		return static::makeQueryDecode(self::$commandcode,$prefix);
	}	
	static function makeQueryCommandCodeBinds($prefix){
		return static::makeQueryBinds(self::$commandcode,$prefix);
	}
	static function makeQueryLockTypeDecode($prefix){
		return static::makeQueryDecode(self::$locktypecode,$prefix);		
	}
	static function makeQueryLockTypeBinds($prefix){
		return static::makeQueryBinds(self::$locktypecode,$prefix);
	}
	static protected function aboutORA($code, $message, $sql_text,  Exception $e=null) {
		$tmp = preg_replace(self::DRIVER_CALL_PATTERN, '', substr($message, strpos($message, 'ORA')));
		$msg = iconv(self::$driverCode, self::$PageCode, $tmp);
		
		if (!empty($sql_text))
			$msg .= ' { '.$sql_text.' }';

		switch( $code ) {
			case 257 : //archiver error
			case 1034: //database not started 
			case 1035: // Oracle only available to users with RESTRICTED SESSION privilege
				return new OraNotAvailableException($msg, $code, $e);
			case 903 ://ORA-00903: nom de table non valide
			case 904 ://ORA-00904: invalid identifier
			case 911 ://ORA-00911: caractere non valide
			case 918 ://ORA-00918: définition de colonne ambigu
			case 923 ://ORA-00923: FROM keyword not found WHERE expected
			case 933 ://ORA-00933: la commande SQL ne se termine pas correctement
			case 936 ://00936: expression absente
			case 972 ://ORA-00972: l'identificateur est trop long
			case 1821://ORA-01821: format de date inconnu
			case 1008://ORA-01008: toutes les variables ne sont pas liées
				return new OraBadSqlException($msg, $code, $e);
			case 942 ://ORA-00942: Table ou vue inexistante (ou permission manquante)
				return new OraTableViewNotFoundNoRights($msg, $code, $e);
			case 1033:
				return new OraNotOpenException($msg, $code, $e);
			case 12154 :// could not resolve
			case 12170 ://ORA-12170: TNS : délai de connexion dépassé
				return new OraResolverException($msg, $code, $e);
			case 12505 ://SID inconnu pour listene
			case 12514 ://SERVICE NAME inconnu
				return new OraListenerException($msg, $code, $e);
			case 1017 :// bad password
			case 28000 : // account locked
			case 28001 : // account expiré
				return new OraUnusableAccountException($msg, $code);
			case 1476: //division par 0
				return new OraDataException($msg, $code,$e);
			case 1036:
				return new OraStatementException($msg, $code,$e);
			default:
				return new OraUnknownException($msg, $code);				
		}
	}
	static function analyse($str) {
		if (preg_match('/ORA-([0-9]{5}):/', $str, $match))
			return intval($match[1]);
		else
			return 0;
	}
	
	static function listHelper($str){
		$tab=explode(',',$str);
		return array_map('trim', $tab);
	}
	
}// OracleHelper

Interface Database_abstraction {
	function exec($sql_text );
	function prepare( $sql_text);
	function rollback();
}

Interface PreparedStatement_abstraction {
	function execute();
	function fetch_assoc();
	function bind_by_name($bindname,$value);
}

Final Class OCI_DB_Wrapper implements Database_abstraction{
	private $conn;
	private $isOnline;
	
	function __construct($user,$pass,$cnx, $encode, $preventSchemaLock = TRUE ){
	//	echo 'pass=', $pass, '<br>';
		
		if ( $preventSchemaLock ){
			//anyway the password was in cleartext 
			$hash=sha1("$user$pass$cnx");
			$lock_file=TOOLBOX_LOCK_PATH.'lock-'.$hash.'.txt';
			if (file_exists($lock_file) ){
				$this->conn=FALSE;
				$this->isOnline=FALSE;
				throw new ConfigException('Last authentication failed, please check provided credentials then remove:'.$lock_file );
			}
		}
		else {
			$lock_file=null;
		}
		
		//let's use a try catch in case of oci_error are handled to known exception 
		try {
			$this->conn = oci_connect($user,$pass,$cnx, $encode);
		}
		catch( OracleException $exception ){
			$this->failedConstruct($exception,$lock_file,"denied $user on $cnx");
			throw $exception;
		}
		
		//if oci_error wasn't handled
		if ($this->conn===FALSE) {			
			$exception=OracleHelper::generateOciException(oci_error());
			$this->failedConstruct($exception,$lock_file,"denied $user on $cnx");
		}
		else {
			$this->isOnline=true;
		}
	}

	private function failedConstruct(Exception $exception,$lock_file,$message){
		$this->conn===FALSE;
		$this->isOnline=FALSE;
		if ( $lock_file !== null ){
			file_put_contents( $lock_file, date('c').' '.$message.' received '.$exception->getMessage() );
		}
	}
	
	function __destruct(){
		oci_close($this->conn);
	}
	
	function exec($sql_text ){
		$statement = oci_parse( $this->conn , $sql_text );
		if ( $statement===FALSE){
			throw OracleHelper::generateOciException(oci_error());
		}
		else  {
			$res=oci_execute ( $statement );
			if ($res===FALSE){
				throw OracleHelper::generateOciException(oci_error());		
			}
			return $res;
		}
	}
			
	function prepare( $sql_text){
		$statement = oci_parse( $this->conn , $sql_text );
		if ( $statement===FALSE){
			throw OracleHelper::generateOciException(oci_error());
		}
		return new OCI_Statement_Wrapper($statement);
	}
	
	function rollback(){
		oci_rollback($this->conn );
	}


}//OCI_WRAPPER 

Final Class OCI_Statement_Wrapper implements PreparedStatement_abstraction {
	private $stmt;
	
	function __construct ( $oci_statement ){
		$this->stmt = $oci_statement;
	}
	
	function __destruct( ){
		oci_free_statement ( $this->stmt );
	}
	
	function execute(){
		$res=oci_execute ( $this->stmt );
		if ($res===FALSE){
			throw OracleHelper::generateOciException(oci_error());	
		}
		return $res;
	}
	
	function fetch_assoc(){
		return oci_fetch_assoc($this->stmt);
	}
	
	function bind_by_name($bindname,$value){	
		oci_bind_by_name($this->stmt, $bindname, $value);
	}
	
}


Class OraConfig {
	CONST PRIM='Primaire';
	CONST DTGD='Dataguard';	
	const APP='app';
	CONST ENV='env';
	CONST DBROLE='role';
	CONST SERVER='server';
	CONST PORT='port'; 
	CONST USER='user';
	CONST PASS='password';
	CONST SERVICENAME='servicename';
	CONST SID='SID';
	CONST DGSERVER='DGserver';
	CONST DGPORT='DGport';
	CONST DGSERVICENAME='DGSERVICENAME';

	
	protected $port;
	protected $server;
	protected $service;
	protected $dbrole;
	protected $user;
	protected $password;
	protected static $filters;

	protected function __construct($array){
		$this->server=$array[static::SERVER];
		$this->port=$array[static::PORT];
		$this->service=$array[static::SERVICENAME];
		$this->user=$array[static::USER];
		$this->password=$array[static::PASS];
		$this->dbrole=$array[static::DBROLE];

	}	
	
	function toEasyConnect(){
		return '//'.$this->server.':'.$this->port.'/'.$this->service;
	}
	
	function __get($name){
		if ( property_exists($this,$name)){
			//echo 'request ',$name, '=>', $this->$name, '!!!';
			return $this->$name;
		}
		throw new LogicException('not existing attribute "'.$name.'"');
	}
	
	function isPrimary(){
		return $this->dbrole === static::PRIM;
	}
	
	static protected function makeFilter(){
		
		$list_role=array(static::PRIM,static::DTGD);
		$role=function($val) use ($list_role){
			return in_array($val, $list_role);
		};
		
		return array(
			static::SERVER=>		array('filters'=> FILTER_VALIDATE_REGEXP,	'flags'=>'/^[.-[:alnum:]]+$/'),
			static::PORT=>			array('filter'=>  FILTER_VALIDATE_INT,		'options'=>array('min_range' => 1, 'max_range' => 65536)),
			static::SERVICENAME=>	array('filters'=> FILTER_VALIDATE_REGEXP,	'flags'=>'/^[.-[:alnum:]]+$/'),
			static::DBROLE=>		array('filters'=> FILTER_CALLBACK,			'options'=>$role),
			static::USER=>			array('filters'=> FILTER_UNSAFE_RAW),
			static::PASS=>			array('filters'=> FILTER_UNSAFE_RAW)
		);
	}
	
	static protected function verifyConfig($unsafe){
		//($filters);
		if ( !isset(self::$filters)){
			self::$filters=static::makeFilter();
			//echo '<pre>';
			//print_r(self::$filters);
			//echo '</pre>';
		}
		
		$safer=filter_var_array($unsafe,self::$filters,false);
		
		if (in_array(false,$safer,true) or  count($safer)!==8){
			return false;
		}
		return $safer;
	}
	
	function findMyPrimaryServer(){
		throw new ConfigException('No primary configured');
	}

	static function have($unsafe){
		$safe=static::verifyConfig($unsafe);
		if ($safe !==false ){
			return new OraConfig($safe);
		}
		else 
			throw new ConfigException('');
	}
	
}

Class ComplexOraConfig extends OraConfig {
	
	protected $dg_server;
	protected $dg_port;
	protected $dg_service;
	
	protected function __construct($array){
		parent::__construct($array);
		$this->dg_server=$array[static::DGSERVER];
		$this->dg_port=$array[static::DGPORT];
		$this->dg_service=$array[static::DGSERVICENAME];

	}
	
	static protected function makeFilter(){
		$filters=parent::makeFilter();
		return array_merge($filters, array( 
			static::DGSERVER=>		array('filters'=> FILTER_VALIDATE_REGEXP,	'flags'=>'/^[.-[:alnum:]]+$/'),
			static::DGPORT=>		array('filter'=>  FILTER_VALIDATE_INT,		'options'=>array('min_range' => 1, 'max_range' => 65536)),
			static::DGSERVICENAME=>	array('filters'=> FILTER_VALIDATE_REGEXP,	'flags'=>'/^[.-[:alnum:]]+$/'),
			));
	}
	
	static function have($unsafe){
		$safe=static::verifyConfig($unsafe);
		if ($safe !==false ){
			return new ComplexOraConfig($safe);
		}
		else 
			throw new ConfigException('');
	}

}

Class AdvancedOraConfig extends OraConfig {
	CONST PR='prd';
	CONST VA='val';
	CONST IQ='int';
	CONST R7='rec';
	CONST DV='dev';
	CONST IS='iso';
	CONST BX='oob';
	CONST TS='tst';
	CONST OT='oth';
	CONST Production='Production';
	CONST Validation='Validation';
	CONST Preproduction='Préproduction';
	CONST Integration='Intégration';
	CONST Qualification='Qualification';
	CONST Recette='Recette';
	CONST Developpement='Développement';
	CONST IsoProduction='IsoProduction';
	CONST OutOfTheBox='OutOfTheBox';
	CONST Test='Test';
	CONST Other='Autre';
	
	private $env;	
	private $app;

	private static $register=array();
	private static $known_app=array();
	private static $known_env=array(); 
	
	private static $default_user;
	private static $default_pass;
	
	protected function __construct($array){
		//print_r($array);
		parent::__construct($array);
				

		$this->env=$array[static::ENV];
		$this->app=$array[static::APP];
		$this->selfRegister();
	}
	
	
	function __get($name){
		if ( property_exists($this,$name)){
			//echo 'request ',$name, '=>', $this->$name, '!!!';
			return $this->$name;
		}
		throw new LogicException('not existing attribute "'.$name.'"');
	}
	
	private function selfRegister(){
		$app=strtoupper($this->app);
		$env=$this->env;
		static::$register[$app][$env][$this->dbrole]=&$this;
		if ( !in_array($app, static::$known_app)){
			static::$known_app[]=$app;
		}
		if ( !in_array($env, static::$known_env)){
			static::$known_env[]=$env;
		}
	}
	
	static protected function makeFilter(){
		
		$filters=parent::makeFilter();
		$list_env=array(static::PR,static::VA,static::IQ,static::R7, static::DV, static::IS, static::BX,static::TS);
		$env=function($val) use ($list_env) {
			//echo 'hello world';
			if ( preg_match('[a-z]{3}[0-9]*', $val)){
				//echo 'it is a match';
				return in_array(static::toEnvCode(substr($val,0,3)),$list_env);				
			}
			else {
				//echo 'no match ',$val; 
				return false;
			  }
		};
		
		return array_merge($filters, array( 
			static::APP=>	array('filters'=> FILTER_VALIDATE_REGEXP,	'flags'=>'/^[.-[:alnum:]]+$/'),
			static::ENV=>	array('filters'=> FILTER_CALLBACK,			'options'=>$env)
			));
	}
	
	function findMyDataguardServer(){
		if ( isset(static::$register[$this->app][$this->env][static::DTGD]) ){
			return static::$register[$this->app][$this->env][static::DTGD]->server;
		}
		else {
			throw new ConfigException('No Dataguard configured');
		}
	}
	
	
	function findMyPrimaryServer(){
		if ( isset(static::$register[$this->app][$this->env][static::PRIM]) ){
			return static::$register[$this->app][$this->env][static::PRIM]->server;
		}
		else {
			throw new ConfigException('No primary configured');
		}
	}
	
	static function toEnvCode($x){	
		switch($x){
			case static::PR:
			case static::Production:
				return static::PR;
			case static::VA:
			case static::Validation:
			case static::Preproduction:
				return static::VA;
			case static::IQ:
			case static::Integration:
			case static::Qualification:
				return static::IQ;
			case static::R7:
			case static::Recette:
				return static::R7;
			case static::DV:
			case static::Developpement:
				return static::DV;
			case static::IS:
			case static::IsoProduction;
				return static::IS;
			case static::BX:
			case static::OutOfTheBox;
				return static::BX;
			case static::TS:
			case static::Test;
				return static::Ts;
			default:
				return static::OT;
		}
	}
	
	static function toEnvName($x){	
		switch( substr($x, 0,3)){
			case static::PR:
			case static::Production:
				return static::Production;
			case static::VA:
			case static::Validation:
			case static::Preproduction:
				return static::Validation;
			case static::IQ:
			case static::Integration:
			case static::Qualification:
				return static::Integration;
			case static::R7:
			case static::Recette:
				return static::Recette;
			case static::DV:
			case static::Developpement:
				return static::Developpement;
			case static::IS:
			case static::IsoProduction;
				return static::IsoProduction;				
			case static::BX:
			case static::OutOfTheBox;
				return static::OutOfTheBox;
			case static::TS:
			case static::Test;
				return static::Test;
			default:
				return static::Other.'-'.$x;
		}
	}



	
	static function have($unsafe){
		$safe=static::verifyConfig($unsafe);
		if ($safe !==false ){
			new AdvancedOraConfig($safe);
		}
	}
	
	
	static function makeHtmlTable($refapp,$refenv){
		echo '
<div class="menu">
<table>
	<thead>
		<tr>
			<th>Application</th>';
			foreach(static::$known_env as $env){
				echo '<th>',htmlentities(static::toEnvName($env)),'</th>';
			}
		echo '
		</tr>
	</thead>
	<tbody>';
		foreach( static::$known_app as $app ){
			echo '<tr><th>', htmlentities($app),'</th>';
			foreach( static::$known_env as $env ){
				if ( $env === $refenv and $app===$refapp){
					echo '<td class="currentdisplay">';
				}
				else{
					echo '<td>';
				}
					
				if ( isset(static::$register[$app][$env]) ){
					echo '<a href="?app=',htmlentities($app),'&amp;env=',htmlentities($env),'">',
						implode(' + ', array_keys(static::$register[$app][$env])),
						'</a>';
				}
				echo '</td>';
			}
			echo '</tr>', PHP_EOL;
		}
		echo '
	</tbody>
</table>
</div>';
	
	}
	
	static function retrieveListOfMatching($app,$env){
		if ( $app !== null and $env !== null and isset(static::$register[$app][$env])){
			
			return array(static::$register[$app][$env]);
		}
		else {
			return array();
		}
	}
}


Class AuditItem {
	protected $title;
	protected $headers;
	protected $query;
	protected $matchs;
	protected $binds;
	
	function __construct($title, $headers, $query, $matchs, $binds){
		$this->title=$title;
		$this->headers=$headers;
		$this->query=$query;
		$this->matchs=$matchs;
		$this->binds=$binds;
	}
	
	function __get($name){
		if ( property_exists($this,$name)){
			return $this->$name;
		}
		throw new LogicException('not existing attribute "'.$name.'"');
	}
	
	function run(OCI_DB_Wrapper $db,$debug=false){
		
		echo '<div class="query">';
		$stmt=$db->prepare($this->query);
		//print_r($binds);
		$tmp1=htmlentities($this->query);
		$tmp0=$tmp1;
		foreach( $this->binds as $name => $value ){
			$stmt->bind_by_name($name,$value);
			if ($debug){
				$tmp1=str_replace($name,'<b>'.$name.'</b>',$tmp1);
				$tmp0=str_replace($name,'<b>\''.$value.'\'</b>',$tmp0);
			}
		}
		if ($debug){
			echo '<div class="showSQL">
	<pre class="sql">',$tmp0,'</pre>
	<pre class="sql">',$tmp1,'</pre>
</div>';
		}
		
		try {
			$stmt->execute();
		}
		catch( OracleException $e ){
			//debug_print_backtrace();
			echo '<p> can not complete the test:<em>',htmlentities($this->headers),'</em>(',htmlentities($e->getMessage()),')</p></div>',PHP_EOL;
			return ;
		}
		
		$html=new Statement2Html( $this->title, $this->headers );
		$html->printTable($stmt);
		echo '</div><!--query-->';
	}
		
	function execute(OCI_DB_Wrapper $db){
		$stmt=$db->prepare($this->query);
		//print_r($binds);*
		//Debug::var_dump($this->query);
		//$tmp=htmlentities($this->query);
		foreach( $this->binds as $name => $value ){
			$stmt->bind_by_name($name,$value);
			//$tmp=str_replace($name,'<b>'.$name.'</b>',$tmp);
		}
		$stmt->execute();
		return $stmt;
	}

	static function readIdentite(){
		return new AuditItem('identification de la base ',
		OracleHelper::listHelper('INSTANCE_NAME, HOST_NAME, SERVICE NAME, VERSION, STARTUP_TIME, STATUS, LOGINS, INSTANCE_ROLE, CURRENT_DATE'),
		'SELECT instance_name, host_name, display_value servicename, version, startup_time, status, logins, instance_role, current_date FROM v$instance, v$parameter WHERE name like :sn', 
		Array(),Array(':sn'=>'service_name%'));
	}

	static function readArchiver(){
		return new AuditItem('Database\'s archiver should be started',
		OracleHelper::listHelper( 'ARCHIVER,LOG_SWITCH_WAIT'),
		'SELECT archiver,log_switch_wait, CASE WHEN archiver = :a THEN :ok ELSE :ko END CSS_ FROM v$instance',
		Array(1),Array(':a'=>'STARTED',':ok'=>'pass',':ko'=>'fail'));
	}

	static function readArchivelogManagementPrimary( $days=9 ){
		return new AuditItem('gestion des archivelogs pour les '.$days.' derniers jours (Primaire)',
			OracleHelper::listHelper('jour, archivelog generes, archivelog archived, archivelog applied on standby, archivelog deleted' ),
			'SELECT ct , generated/copies generated,archived,applied_on_standby,deleted, case when generated/copies<=deleted then :ok else case when current_date-ct>2.1 then :ko else null end end css_ FROM (
			SELECT trunc(completion_time) ct, count(*) as generated, sum(case when archived=:y and standby_dest=:n then 1 else 0 end) as archived, sum(case when applied=:y and standby_dest=:y then 1 else 0 end) as applied_on_standby, sum(case when deleted=:y and standby_dest=:n then 1 else 0 end)as deleted
			FROM v$archived_log where completion_time > trunc(current_date)-:days group by  trunc(completion_time) ), (SELECT count(distinct dest_id) copies FROM v$archived_log )   ORDER BY ct desc',
			Array(0),Array(':y'=>'YES',':n'=>'NO',':ok'=>'pass',':ko'=>'fail',':days'=>$days) );
	}
	
	static function readArchivelogManagementDataguard($days=9){
		return new AuditItem('gestion des archivelogs pour les '.$days.' derniers jours (Dataguard)',
			OracleHelper::listHelper('JOUR, ARCHIVELOG GENERES, ARCHIVELOG APPLIED ON STANDBY, ARCHIVELOG DELETED' ),
			'SELECT trunc(completion_time), count(*) as generated, sum(decode(applied,:y,1,0)) as applied_on_standby, sum(decode(deleted,:y,1,0))as deleted, case when count(*) <= sum(decode(deleted,:y,1,0)) then :ok else case when current_date-trunc(current_date)>2.1 then :ko else null end end css_
			FROM v$archived_log WHERE completion_time > trunc(current_date)-:days and standby_dest=:n GROUP BY  trunc(completion_time) ORDER BY trunc(completion_time) desc',
			Array(0),Array(':y'=>'YES',':n'=>'NO',':ok'=>'pass',':ko'=>'fail', ':days'=>$days ));
	}
	
	
	static function readArchivelogConf(){
		return new AuditItem('Lecture Parametres d\'initialisation Archivelog',
		OracleHelper::listHelper('nom, valeur affichable, défaut oracle, description'),
		'SELECT p.name, p.display_value, p.isdefault, p.description FROM v$parameter p WHERE (p.name LIKE :b or p.name LIKE :a) and p.isdefault=:c order by 1',
		array(0),array( ':a'=>'log%',':b'=>'arch%',':c'=>'FALSE'));
	}
	
	static function readArchivelogTTL($days=15){
		return new AuditItem('Estimation nombre switch  '.$days.' jours',
		OracleHelper::listHelper('DATE, bascule, comptage arch complete, durée vie arch complete, taille arch complete, nombre arch/h, duree de vie (moyenne), duree de vie (ecart-type), Volume écrit (Mo)'),
		'select a.ft , a.cnt, b.big_cnt,  b.big_ttl, round(b.big_size,2), round(a.frq,2), a.avg, a.stddev, round(a.mo,3) 
		FROM (select trunc(first_time) ft, count(*) cnt,  count(*)/(24 * ( max(completion_time)- min(first_time))) frq,
			NUMTODSINTERVAL(AVG(COMPLETION_TIME-FIRST_TIME),:unit) avg, 
			NUMTODSINTERVAL(STDDEV(COMPLETION_TIME-FIRST_TIME),:unit) STDDEV  , sum(BLOCKS*BLOCK_SIZE)/1024/1024 mo 
		from V$ARCHIVED_LOG
		Where first_time>current_date-:days and STANDBY_DEST=:transport and IS_RECOVERY_DEST_FILE=:fra
		group by rollup(trunc(first_time))
		) a  LEFT JOIN 
		(select trunc(first_time) ft, NUMTODSINTERVAL(MIN(COMPLETION_TIME-FIRST_TIME),:unit) big_ttl, count(first_time) big_cnt, avg(BLOCKS*BLOCK_SIZE)/1024/1024 big_size
		from V$ARCHIVED_LOG 
		Where first_time>current_date-:days and STANDBY_DEST=:transport and IS_RECOVERY_DEST_FILE=:fra
		and blocks > (select 0.8*avg(blocks) avg_blocks from V$ARCHIVED_LOG where first_time>current_date-:days )
		group by rollup(trunc(first_time)) ) b ON a.ft = b.ft 
		order by a.ft ',
		array(0),array(':days'=>$days , ':unit'=>'DAY', ':transport'=>'YES', ':fra'=>'NO'));//  ));//
	
		
		/*
		'select trunc(first_time) "DATE", count(*) "BASCULES", round(count(*)/24,4) "MOYENNE / H", avg(resetlogs_time-first_time) avg, count(*) * max(taille) "Volume Mo"
		from v$log_history lh, (select max(bytes)/1024/1024 taille from v$log) l
		where first_time>current_date-:days
		group by rollup(trunc(first_time))
		order by trunc(first_time)'
		
		*/
		
	}
	
	
	static function readDataguardLag( $max_lag='0 00:00:10'){
		return new AuditItem('Dataguard lag should is better very low',
			OracleHelper::listHelper('name, value, time_computed, current_date'),
			'select name, value, time_computed, current_date, CASE WHEN name like :b THEN Null WHEN value is null THEN :ko WHEN TO_DSINTERVAL(SUBSTR(value, 2,11))> TO_DSINTERVAL(:a) THEN :ko ELSE :ok END CSS_  FROM v$dataguard_stats',
			Array(0),
			Array(':ok'=>'pass',':ko'=>'fail',':a'=>$max_lag,':b'=>'estimated startup time'));
	}
	
	
	
	
	static function readOpenCursorConfiguration(){
		return new AuditItem( 'configuration open_cursors',
		OracleHelper::listHelper('name,value'),
		'SELECT name,value FROM v$parameter WHERE name=:a',
		Array(1),Array(':a'=>'open_cursors'));
	}

	static function readCursorConfiguration(){
		return new AuditItem('cursor configuration',
		OracleHelper::listHelper('max value, name, CURRENT_DATE '),
		'SELECT  max(a.value) value, b.name, CURRENT_DATE, CASE WHEN max(a.value) = (SELECT value FROM v$parameter WHERE name = :b ) THEN :ko ELSE NULL END CSS_ FROM v$sesstat a INNER JOIN  v$statname b on  a.statistic# = b.statistic# WHERE  b.name like :a group by b.name',
		Array(1),Array(':a'=>'%cursor%',':b'=>'open_cursors',':ko'=>'fail'));
	}
		
		
	static function readCursor2(){
		return new AuditItem('cursor configuration using V$SESSTAT A, V$STATNAME B, V$PARAMETER',
		OracleHelper::listHelper('HIGHEST_OPEN_CUR, MAX_OPEN_CUR,CURRENT_DATE '),
		'SELECT MAX(A.VALUE) AS HIGHEST_OPEN_CUR, P.VALUE AS MAX_OPEN_CUR, CURRENT_DATE FROM V$SESSTAT A, V$STATNAME B, V$PARAMETER P 
		WHERE A.STATISTIC# = B.STATISTIC# AND B.NAME = :a AND P.NAME= :b GROUP BY P.VALUE',
		Array(1),Array(':a'=>'opened cursors current',':b'=>'open_cursors'));
		
		
	}
/*
		$this->on['DbSize']=<<<'EOSTRING'
select round(sum(BYTES)/1024/1024/1024,1)||' Go' message  from DBA_DATA_FILES
EOSTRING;
		$this->on['!tablespaces permanent']=<<<'EOSTRING'
SELECT a.TABLESPACE_NAME, file_count "files count", ROUND (files_size / 1024 / 1024 / 1024, 3) "size Go", ROUND (100 * (b.available_blocks - c.free_blocks) / b.available_blocks,2) "Usage %", CASE WHEN autoext > 0 THEN 'YES' ELSE 'NON' END AUTOEXTEND, a.STATUS
FROM DBA_TABLESPACES a
LEFT JOIN (  SELECT TABLESPACE_NAME, SUM (USER_BLOCKS) available_blocks, COUNT (file_id) file_count, NVL (SUM (bytes), 0) files_size, SUM (CASE WHEN AUTOEXTENSIBLE = 'YES' THEN 1 ELSE 0 END) autoext FROM DBA_DATA_FILES GROUP BY TABLESPACE_NAME) b ON (a.TABLESPACE_NAME = b.TABLESPACE_NAME)
LEFT JOIN (  SELECT f.TABLESPACE_NAME, SUM (f.BLOCKS) free_blocks FROM DBA_FREE_SPACE f GROUP BY f.TABLESPACE_NAME) c ON (a.TABLESPACE_NAME = c.TABLESPACE_NAME)
   WHERE a.contents = 'PERMANENT'
ORDER BY a.TABLESPACE_NAME
EOSTRING;
		$this->on['!tablespaces undo']=<<<'EOSTRING'
SELECT a.TABLESPACE_NAME, file_count "files count", ROUND (files_size / 1024 / 1024 / 1024, 3) "size Go", ROUND (100 * (b.available_blocks - c.free_blocks) / b.available_blocks,2) "Usage %", CASE WHEN autoext > 0 THEN 'YES' ELSE 'NON' END AUTOEXTEND, a.STATUS
from DBA_TABLESPACES a left join (SELECT TABLESPACE_NAME, SUM (USER_BLOCKS) available_blocks, COUNT (file_id) file_count, NVL (SUM (bytes), 0) files_size, SUM (CASE WHEN AUTOEXTENSIBLE = 'YES' THEN 1 ELSE 0 END) autoext  from DBA_DATA_FILES group by TABLESPACE_NAME) b on (a.TABLESPACE_NAME = b.TABLESPACE_NAME) left join (select  f.TABLESPACE_NAME, sum(f.BLOCKS) free_blocks from DBA_FREE_SPACE f group by f.TABLESPACE_NAME ) c on ( a.TABLESPACE_NAME = c.TABLESPACE_NAME ) where a.contents = 'UNDO' order by a.TABLESPACE_NAME
EOSTRING;
		$this->on['!tablespaces temporary']=<<<'EOSTRING'
select a.TABLESPACE_NAME,count(b.file_id) "files count", round( nvl(sum(b.bytes),0)/1024/1024/1024, 3) "Size Go", round( 100*sum(c.blocks_used)/sum(c.blocks_used+c.blocks_free),2) "Usage %",case when max(b.AUTOEXTENSIBLE)='YES' then '	AUTOEXTENSIBLE' end AUTOEXTENSIBLE,a.STATUS
from DBA_TABLESPACES a 
left join DBA_TEMP_FILES b on (a.TABLESPACE_NAME = b.TABLESPACE_NAME)
left join V$TEMP_SPACE_HEADER c  on (b.file_id = c.file_id ) 
where a.TABLESPACE_NAME = c.TABLESPACE_NAME and a.contents = 'TEMPORARY' 
group by a.TABLESPACE_NAME, a.STATUS order by a.TABLESPACE_NAME
EOSTRING;
*/	
	
	static function readTablespacePermanent(){
		return new AuditItem('Tablespaces permanent', 
		OracleHelper::listHelper('tablespace name, file count, total size Go, Usage %, Autoextend, STATUS'),
		'SELECT a.TABLESPACE_NAME, file_count "files count", ROUND(files_size/1024/1024/1024,3) "size Go", ROUND(100*(b.available_blocks-c.free_blocks)/b.available_blocks,2) "Usage %", CASE WHEN autoext > 0 THEN :oui ELSE :non END AUTOEXTEND, a.STATUS
FROM DBA_TABLESPACES a 
LEFT JOIN (  SELECT TABLESPACE_NAME, SUM (USER_BLOCKS) available_blocks, COUNT (file_id) file_count, NVL (SUM (bytes), 0) files_size, SUM (CASE WHEN AUTOEXTENSIBLE = :yes THEN 1 ELSE 0 END) autoext FROM DBA_DATA_FILES GROUP BY TABLESPACE_NAME) b ON (a.TABLESPACE_NAME = b.TABLESPACE_NAME)
LEFT JOIN (  SELECT f.TABLESPACE_NAME, SUM (f.BLOCKS) free_blocks FROM DBA_FREE_SPACE f GROUP BY f.TABLESPACE_NAME) c ON (a.TABLESPACE_NAME = c.TABLESPACE_NAME)
WHERE a.contents = :typetbsp ORDER BY a.TABLESPACE_NAME',
		Array(1,3),
		Array(':typetbsp'=>'PERMANENT',':yes'=>'YES',':oui'=>'YES',':non'=>'NO'));
	}
	
	
	static function readTablespaceTemporary(){
		return new AuditItem('Tablespaces temporaires', 
		OracleHelper::listHelper('tablespace name, file count, total size Go, Usage %, Autoextend, STATUS'),
		'select a.TABLESPACE_NAME, count(b.file_id) "files count", round(nvl(sum(b.bytes),0)/1024/1024/1024,3) "Size Go", round(100*sum(c.blocks_used)/sum(c.blocks_used+c.blocks_free),2) "Usage %",case when SUM (CASE WHEN AUTOEXTENSIBLE = :yes THEN 1 ELSE 0 END)>0 then :autoext else :non end AUTOEXTENSIBLE,a.STATUS
from DBA_TABLESPACES a  left join DBA_TEMP_FILES b on (a.TABLESPACE_NAME = b.TABLESPACE_NAME) left join V$TEMP_SPACE_HEADER c  on (b.file_id = c.file_id ) 
where a.TABLESPACE_NAME = c.TABLESPACE_NAME and a.contents = :typetbsp
group by a.TABLESPACE_NAME, a.STATUS order by a.TABLESPACE_NAME',
		Array(1,3),
		Array(':typetbsp'=>'TEMPORARY',':yes'=>'YES',':autoext'=>'AUTOEXTENSIBLE',':non'=>'NO'));	
	}
	
	
	static function readTablespaceUndo(){
		return new AuditItem ('Tablespace undo',
		OracleHelper::listHelper('tablespace name, file count, total size Go, Usage %, Autoextend, STATUS'),
		'SELECT a.TABLESPACE_NAME, file_count "files count", ROUND(files_size/1024/1024/1024,3) "size Go", ROUND(100*(b.available_blocks-c.free_blocks)/b.available_blocks,2) "Usage %", CASE WHEN autoext > 0 THEN :oui ELSE :non END AUTOEXTEND, a.STATUS
		from DBA_TABLESPACES a left join (SELECT TABLESPACE_NAME, SUM (USER_BLOCKS) available_blocks, COUNT (file_id) file_count, NVL (SUM (bytes), 0) files_size, SUM (CASE WHEN AUTOEXTENSIBLE = :yes THEN 1 ELSE 0 END) autoext  from DBA_DATA_FILES group by TABLESPACE_NAME) b on (a.TABLESPACE_NAME = b.TABLESPACE_NAME) left join (select  f.TABLESPACE_NAME, sum(f.BLOCKS) free_blocks from DBA_FREE_SPACE f group by f.TABLESPACE_NAME ) c on ( a.TABLESPACE_NAME = c.TABLESPACE_NAME ) where a.contents = :typetbsp order by a.TABLESPACE_NAME',
		Array(1,3),
		Array(':typetbsp'=>'PERMANENT',':yes'=>'YES',':oui'=>'YES',':non'=>'NO'));	
	}
	
	
	static function readDataSize($schema){
		return new AuditItem('Taille des tables',
		OracleHelper::listHelper('owner, table_name, tablespace name, total_size_Mo, table_size_Mo, Index_Size_Mo, index_segment, index_tblsp'),
		'SELECT a.owner schema, a.segment_name table_name, a.tablespace_name table_tablespace, round((a.table_bytes+nvl(b.index_bytes,0))/1024/1024,3) total_size_Mo, round(a.table_bytes/1024/1024,3) table_size_Mo, round(nvl(b.index_bytes,0)/1024/1024,3) Index_Size_Mo, b.index_segment, B.index_tblsp, CASE WHEN table_bytes > 2*nvl(index_bytes,0) THEN :ok WHEN index_bytes > table_bytes THEN :wr END CSS_ 
		FROM (SELECT s.owner, s.segment_name, s.segment_type,s.tablespace_name,sum(s.bytes) table_bytes FROM dba_segments s WHERE s.segment_type=:tbl GROUP BY s.tablespace_name,s.owner,s.segment_type, s.segment_name) a LEFT JOIN ( SELECT i.table_owner, i.table_name, sum(s.bytes) index_bytes, count(s.segment_name) index_segment, count(distinct i.tablespace_name) index_tblsp FROM dba_indexes i LEFT JOIN dba_segments s ON (i.index_name = s.segment_name) GROUP BY i.table_owner, i.table_name) b ON (b.table_owner=a.owner and a.segment_name=b.table_name) WHERE a.owner=upper(:owner) ORDER BY 1,4 desc',
		Array(1,4),
		Array(':tbl'=>'TABLE',':wr'=>'warning',':owner'=>strtoupper($schema),':ok'=>'pass'));
	}
	
	static function readDoubleIndex($schema){
		return new AuditItem('Doubon d\'index ',
		OracleHelper::listHelper('table_name, good index name, good index properties, good index columns,  sub-index name, sub-index properties , sub-index columns'),
		'WITH tmp_indexColumn as (SELECT table_owner, table_name,index_owner, index_name, listagg(column_name, :glue) within group (order by column_position)||:glue index_columns, count(column_name) cpt, sum(expression) as expression
		FROM (SELECT table_owner, table_name,index_owner, index_name, column_name,column_position,0 as expression FROM dba_ind_columns UNION SELECT table_owner, table_name,index_owner, index_name, :fonct, column_position,1  FROM dba_ind_expressions )GROUP BY table_owner, table_name, index_owner, index_name)
		SELECT x.table_owner||:dot||x.table_name tablename, x.index_owner||:dot||x.index_name good_index, ix.index_type||:space||ix.uniqueness good_index_properties, x.index_columns good_index_column, y.index_owner||:dot||y.index_name poor_index, iy.index_type||:space||iy.uniqueness sub_index_properties, y.index_columns sub_index_column, 
		CASE WHEN  ix.index_type<>iy.index_type THEN null WHEN ix.uniqueness=:onl1 and iy.uniqueness=:onl1 THEN :ok WHEN iy.uniqueness=:onl1 or x.expression > 0 or y.expression> 0 THEN :wr ELSE :ko END CSS_
		FROM tmp_indexColumn x INNER JOIN tmp_indexColumn Y ON (x.table_owner=y.table_owner and x.table_name=y.table_name and (x.index_owner<>y.index_owner or  x.index_name<>y.index_name) ) 
		INNER JOIN dba_indexes ix on (ix.owner=x.index_owner and ix.index_name = x.index_name) INNER JOIN dba_indexes iy on (iy.owner=y.index_owner and iy.index_name = y.index_name)
		WHERE x.table_owner=upper(:owner)  and x.index_columns like y.index_columns||:pct and y.cpt<=x.cpt order by 1,2,3',
		Array(1,2,3),Array(':glue'=>', ',':owner'=>strtoupper($schema),':pct'=>'%',':dot'=>'.',':space'=>' ',':fonct'=>'function()', ':wr'=>'warning',':ko'=>'fail',':ok'=>'pass',':onl1'=>'UNIQUE'));
	}
	
	static function readIndexTop($schema,$top=48){
		return new AuditItem('Top '.$top.' - Usage d\'index',
		OracleHelper::listHelper('indexed_table, index_name, OBJECT_TYPE, OPTION, COUNT_SQL_ID, EXECUTIONS_TOTAL'),
		'SELECT i.table_owner||:dot||i.table_name indexed_table, a.object_owner||:dot||a.object_name index_name,a.object_type,options,a.count_sql_id, executions_sum 
		FROM (SELECT object_owner,object_name,object_type,options, COUNT(sql_id) count_sql_id, SUM(executions_total) executions_sum FROM (
		SELECT s.sql_id, object_owner, object_name, object_type, options, executions_total FROM dba_hist_sql_plan p, dba_hist_snapshot n, dba_hist_sqlstat s 
		WHERE n.snap_id=s.snap_id and s.sql_id=p.sql_id and operation=:oper and object_owner=upper(:owner) and begin_interval_time>sysdate-:days) 
		GROUP BY object_owner,object_name,object_type,options) a LEFT JOIN dba_indexes i ON (a.object_owner=i.owner and a.object_name=i.index_name) 
		WHERE rownum <:top ORDER BY count_sql_id DESC',
		Array(1),
		Array(':oper'=>'INDEX',':days'=>8,':owner'=>$schema,':dot'=>'.', ':top'=>$top ));				
		}	
	static function readDataFileHeader(){
		return new AuditItem( 'lecture des entetes des fichiers de données',
		OracleHelper::listHelper('file,name,status, fuzzy, recovery,tablespace'),	
		'select dh.file#,name, dh.status,dh.fuzzy, dh.RECOVER,TABLESPACE_NAME from v$datafile_header dh' ,
		Array(0),
		Array());
		
	}
	
	static function readFastRecoveryAreaPointCount(){
		return new AuditItem('Comptage des points de restauration',
			OracleHelper::listHelper('nombre de point de restoration'),
			'SELECT c, CASE WHEN c > 0 THEN :wr ELSE :ok END CSS_ FROM (SELECT count(*) c FROM V$RESTORE_POINT)',
			Array(0),Array(':wr'=>'warning',':ok'=>'pass'));
	}

	static function readFastRecoveryAreaConfiguration(){
		return new AuditItem('Lecture Parametres d\'initialisation FRA',
		OracleHelper::listHelper('nom, valeur, valeur affichable, défaut oracle, description'),
		'SELECT p.name, p.value, p.display_value, p.isdefault, p.description FROM v$parameter p WHERE (p.name like :b1 and p.value is not null ) or p.name in (:c) order by 1',
		Array(1),
		array(':b1'=>'db_recovery_file_dest%',':c'=>'db_unique_name'));
	}
	
	static function readFastRecoveryAreaUsage(){
		return new AuditItem('Usage FRA',
		OracleHelper::listHelper('file type, espace utilisé %, espace récupérable %, nombre de fichiers'),
		'SELECT FILE_TYPE,PERCENT_SPACE_USED,PERCENT_SPACE_RECLAIMABLE,NUMBER_OF_FILES,
		case file_type WHEN :ft THEN CASE 
			WHEN PERCENT_SPACE_USED < 75 THEN :ok
			WHEN PERCENT_SPACE_USED < 90 THEN :wr ELSE :ko END END CSS_
		FROM v$flash_recovery_area_usage order by 1',
		Array(1),Array(':ft'=>'FLASHBACK LOG',':ok'=>'pass',':ko'=>'fail',':wr'=>'warning'));
	}
	
	
	static function readFastRecoveryAreaPointList() {
		return new AuditItem('Listing des points de restauration',
		OracleHelper::listHelper('NAME, SCN, TIME, DATABASE_INCARNATION#,GUARANTEE_FLASHBACK_DATABASE, STORAGE_SIZE'),
		'SELECT NAME, SCN, TIME, DATABASE_INCARNATION#,GUARANTEE_FLASHBACK_DATABASE, STORAGE_SIZE FROM V$RESTORE_POINT
		WHERE GUARANTEE_FLASHBACK_DATABASE=:a',
		Array(1),array(':a'=>'YES'));
	}
	
	static function readForeignKeyIndex($schema){
		return new AuditItem('Contrainte de clés etrangère sans index',
		OracleHelper::listHelper('owner, table_name, fk_name, fk_columns, existing index on table count'),
		'SELECT x.owner,x.table_name as table_name, x.constraint_name as fk_name,x.fk_columns as fk_columns, (SELECT count(*) FROM dba_ind_columns i WHERE i.table_owner=x.owner and x.table_name=i.table_name) count_existing_index, :ko CSS_ FROM (SELECT a.owner, a.table_name,a.constraint_name, listagg(a.column_name, :glue ) within group ( order by a.position) fk_columns FROM dba_cons_columns a INNER JOIN dba_constraints b on (a.constraint_name = b.constraint_name and a.owner = b.owner) WHERE  b.constraint_type = :ct GROUP BY a.owner, a.table_name, a.constraint_name ) x LEFT JOIN (SELECT table_owner, table_name, index_name, listagg(column_name, :glue) within group (order by column_position) index_columns FROM dba_ind_columns GROUP BY table_owner, table_name, index_name) y ON (x.owner=y.table_owner and x.table_name = y.table_name and y.index_columns like x.fk_columns || :ending ) WHERE y.table_name is null and x.owner=upper(:owner) ORDER by  1, 2 desc, 3',
		Array(1,2,3),
		Array(':ko'=>'fail',':glue'=>',',':ct'=>'R',':owner'=>$schema,':ending'=>'%'));
	}
	
	static function readLock(){
		return new AuditItem('Recherche des locks transactionnels par objects',
		OracleHelper::listHelper('object_name,object_type,locked_mode,locking_sessions,oldest,newest,users'),
		'SELECT o.owner||:dot||o.object_name object_name, o.object_type, decode(lo.locked_mode,'.OracleHelper::makeQueryLockTypeDecode('lk').',lo.locked_mode) locked_mode, count(lo.session_id) locking_session, min (t.start_date) oldest, max(t.start_date)newest, listagg(s.osuser||:at||s.machine, :glue) within group (order by t.start_date) users 
		FROM v$locked_object lo LEFT JOIN dba_objects o on lo.object_id=o.object_id LEFT JOIN v$session s on lo.session_id=s.sid INNER JOIN v$transaction t  on t.addr = s.taddr
		WHERE s.type=:onlyuser GROUP BY o.owner, o.object_name, o.object_type, locked_mode ORDER BY 1,2',
		Array(1, 2),
		array_merge(
			Array(':onlyuser'=>'USER',':dot'=>'.',':at'=>'@',':glue'=>', '),
			OracleHelper::makeQueryLockTypeBinds('lk')), false);
	}
	
	static function readLockingSession(){
		return new AuditItem('Arbre des sessions bloquantes',
		OracleHelper::listHelper('level,session id,locked objects, acquiring object,event,session_lock_mode, command, machine, osuser, module, s.state, s.status, idle_time_blockant, logon_time'),
'		WITH base_sessions AS(SELECT sid, username, command, machine,module, osuser, logon_time, event,state,status, prev_exec_start, blocking_session, row_wait_obj#, sql_id FROM v$session),
		possessions AS (SELECT  session_id, LISTAGG(o2.owner||:dot||o2.object_name, :coma ) WITHIN GROUP (ORDER BY o2.owner,o2.object_name) possession from dba_objects o2 inner join V$LOCKED_OBJECT los2 on los2.object_id=o2.object_id  WHERE locked_mode > 0 GROUP BY session_id)
		SELECT lpad(:dot, LEVEL, :dot) ||  level,sid sid, ps.possession, o.owner||:dot||o.object_name object_name, s.event,  decode(los.locked_mode,'.OracleHelper::makeQueryLockTypeDecode('lk').',los.locked_mode) session_lock_mode, decode(s.command,'.OracleHelper::makeQueryCommandCodeDecode('cmd').' , s.command) command, s.machine, s.osuser,  s.module,s.state,s.status, NUMTODSINTERVAL(current_date-s.prev_exec_start, :unit) idle_time_blockant, logon_time 
		FROM base_sessions s LEFT JOIN dba_objects o ON (o.object_id = s.row_wait_obj#) LEFT JOIN v$locked_object los on (los.session_id=s.sid and los.object_id=o.object_id) LEFT JOIN possessions ps ON ps.session_id = s.sid  
		WHERE sid IN (SELECT distinct blocking_session FROM base_sessions )  OR blocking_session IS NOT NULL 
		CONNECT BY PRIOR sid = blocking_session START WITH blocking_session IS NULL',
		Array(),
		array_merge(Array(':unit'=>'DAY', ':dot'=>'.', ':coma' => ', ' ),// ,':owner'=>strtoupper($schema)), 
			OracleHelper::makeQueryCommandCodeBinds('cmd'),
			OracleHelper::makeQueryLockTypeBinds('lk')));
		
	}
	
	
	
	static function readNLS(){
		return new AuditItem( 'Vérification NLS',
		OracleHelper::listHelper('parameter,session,database,instance'),
		'SELECT * FROM(SELECT \'SESSION\' SCOPE,nsp.* FROM nls_session_parameters nsp UNION
 SELECT \'DATABASE\' SCOPE,ndp.* FROM nls_database_parameters ndp union
 SELECT \'INSTANCE\' SCOPE,nip.* FROM nls_instance_parameters nip
) a pivot  (listagg(value) within group (order by scope)
FOR SCOPE in (\'SESSION\' as "SESSION",\'DATABASE\' as "DATABASE",\'INSTANCE\' as "INSTANCE"))
order by 1',
			Array(1),
			Array());	
	}	
	
	static function readDatabasePrimary(){
		return  new AuditItem('Primary database should answer with 2 lines, one for local database one for remote dataguard',
			OracleHelper::listHelper('DB_UNIQUE_NAME,DEST_NAME,STATUS, DATABASE_MODE, RECOVERY_MODE, PROTECTION_MODE, DESTINATION, GAP_STATUS'),
			'SELECT db_unique_name,dest_name,status, database_mode, recovery_mode, protection_mode, destination, gap_status, CASE WHEN database_mode=:d1 AND recovery_mode=:r1 THEN :ok WHEN database_mode=:d2 AND recovery_mode=:r2 THEN :ok ELSE :ko END CSS_ FROM v$archive_dest_status WHERE status=:a',
			Array(1),
			Array(':a'=>'VALID',':d1'=>'OPEN',':d2'=>'OPEN_READ-ONLY',':r1'=>'IDLE',':r2'=>'MANAGED REAL TIME APPLY',':ok'=>'pass',':ko'=>'fail'));
	}
	
	
	static function readPermissions($listOfUsers){
		//$param=func_get_args();
		$list= OracleHelper::makeQueryList($listOfUsers,'u');
		$bind= OracleHelper::makeQueryBinds($listOfUsers,'u');
		
		return new AuditItem('Droits utilisateurs applicatifs',
			OracleHelper::listHelper('Level, Grantee, Privilege, Admin'),
			'SELECT LPAD( level,2*level,:tiret) Lvl,grantee,granted,admin_option 
FROM ( SELECT grantee, :roletext||granted_role||:defaulttext||default_role||:pf granted, granted_role, admin_option 
FROM dba_role_privs union 
SELECT grantee, :privtxt|| privilege granted, null, admin_option 
FROM dba_sys_privs union 
select  grantee, :objtxt||p.privilege||:ontxt||owner||:dot||table_name granted, null, grantable  from dba_tab_privs p 
where p.grantee in ('.$list.') )
START with (grantee in ('.$list.') ) 
CONNECT by prior granted_role=grantee and grantee not like :dba',
			Array(2,3),
			array_merge(Array(':tiret'=>'-', ':roletext'=>'Role: ',':defaulttext'=>' (default: ',':pf'=>')',':privtxt'=>'Priv: ',':ontxt'=>' on ',':objtxt'=>'Obj:',':dot'=>'.',':dba'=>'DBA'),$bind));
	
	}
		
	static function readRessourceLimit(){
		return new AuditItem('restrictions instances',
		OracleHelper::listHelper('resource_name,current_utilization,max_utilization,limit_value'),
		'SELECT resource_name, current_utilization, max_utilization, limit_value, case when current_utilization=max_utilization then :wr END CSS_ FROM v$resource_limit WHERE RESOURCE_NAME in (:a,:b)',
		Array(1),Array(':a'=>'processes',':b'=>'sessions',':wr'=>'warning'));
	}
		
	static function readRmanBackup(){
		return new AuditItem('Création du backup RMAN',
			OracleHelper::listHelper('Startup,duration, Type backup, Status,output_device_type,Mo read,Mo written  '),
			'SELECT start_time, time_taken_display, rmantype, status, output_device_type, Mo_Read, Mo_written, CSS_
			FROM ((SELECT current_date start_time, null time_taken_display, null rmantype,:m status, null output_device_type, null Mo_read, null Mo_written,:ko CSS_ FROM dual WHERE not exists (SELECT * FROM v$rman_backup_job_details d WHERE d.input_type<>:l and (current_date-2)<d.start_time  and d.status in (:c,:r)) 
			UNION  ALL (SELECT j.start_time,j.time_taken_display, j.input_type||(case when count>0 then :a else :b end) rmantype,j.status, j.output_device_type, round(j.input_bytes/1024/1024,3) mo_read, round(j.output_bytes/1024/1024,3) mo_written, decode( status, :rman_runwarning,:wa, :rman_runerror,:wa, :rman_complete,:ok, :rman_complwarn,:wa, :rman_complerror,:ko, :rman_fail,:ko, null) css_ FROM v$rman_backup_job_details j LEFT JOIN (SELECT bs.session_recid,bs.session_stamp, count(*) count FROM v$backup_set_details bs where bs.incremental_level=0 and bs.backup_type=:d group by bs.session_recid,bs.session_stamp) x on x.session_stamp=j.session_stamp and x.session_recid=j.session_recid WHERE j.input_type<>:l and (current_date-10)<j.start_time ))) order by start_time desc ',
			null,
			Array(':a'=>' [Full]',':b'=>'',':c'=>'COMPLETED',':r'=>'RUNNING',':d'=>'D',':l'=>'ARCHIVELOG',':m'=>'missing',':ok'=>'pass',':ko'=>'fail',':wa'=>'warning',':rman_runwarning'=>'RUNNING WITH WARNINGS', ':rman_runerror'=>'RUNNING WITH ERRORS', ':rman_complete'=>'COMPLETED', ':rman_complwarn'=>'COMPLETED WITH WARNINGS', ':rman_complerror'=>'COMPLETED WITH ERRORS', ':rman_fail'=>'FAILED' ));
	}
	
	static function readRoles(){
		return new AuditItem('liste des roles',
		OracleHelper::listHelper('role'),
		'SELECT role FROM dba_roles order by 1',
		Array(1),Array());
	}

	static function readSchemaPrivilege($name){
		return new AuditItem('Droits utilisateurs sur les objects du schéma '.$name.' (les objets homonymes introduisent des erreurs)',
		OracleHelper::listHelper('Type, Grantee, privilege,Object type, Grant count, Existing count'),
		'SELECT case when role is null THEN :u ELSE :r END TYPE_GRANTEE, x.grantee, x.privilege, x.object_type, x.cnt X,y.cnt Y, CASE WHEN x.cnt=y.cnt THEN :ok END CSS_ FROM ( SELECT p.grantee, p.privilege, a.object_type, count(distinct p.table_name) cnt, p.owner FROM dba_tab_privs p INNER JOIN dba_objects a on p.owner=a.owner and p.table_name=a.object_name WHERE a.owner=:a and a.object_type<>:b group by p.grantee,p.privilege, p.owner, a.object_type ) x INNER JOIN ( SELECT owner, object_type, count(object_id) cnt FROM dba_objects WHERE owner=:a group by owner, object_type ) y on y.object_type=x.object_type and x.owner=y.owner LEFT JOIN dba_roles r on x.grantee=r.role order by 2,3',
		Array(1,2,3),Array(':ok'=>'pass',':u'=>'user',':r'=>'role',':a'=>strtoupper($name),':b'=>'INDEX'));
	}
	
	static function readUsers(){
		$tmp=array_merge(OracleHelper::$TECHNICAL_ACCOUNTS, OracleHelper::$ORACLE_ACCOUNTS);
		return new AuditItem('utilisateurs applicatifs ',
		OracleHelper::listHelper('shema,default_tablespace,ACCOUNT_STATUS,profile,expire'),
		'SELECT u.username schema, u.default_tablespace, u.ACCOUNT_STATUS, u.profile,nvl(TO_CHAR(u.EXPIRY_DATE,:ftd),:never) expire FROM dba_users u WHERE username not in ('.OracleHelper::makeQueryList($tmp,'user').') ORDER BY u.username',
		Array(1), 
		array_merge(Array(':ftd'=>'YYYY-MM-DD HH24:MI',':never'=>'never'),OracleHelper::makeQueryBinds($tmp,'user')));
	}

	static function readUserSession(){
		return new AuditItem('Sessions utilisateurs',
		OracleHelper::listHelper('sid, username, status, schemaname, osuser, machine, program, module,logon_time,command, idle_time, event'),
		'SELECT sid, username, status, schemaname, osuser, machine, program, module,logon_time,
		decode(command, '.OracleHelper::makeQueryCommandCodeDecode('cmd').',command) CMD, NUMTODSINTERVAL ( current_date -prev_exec_start, :unit ) idle_time , event 
		FROM v$session WHERE type=:t ORDER BY username, schemaname, program, module, osuser, machine',
		Array(2,4,7, 5, 8),
		array_merge(
			array(':t'=>'USER', ':unit'=> 'DAY'),
			OracleHelper::makeQueryCommandCodeBinds('cmd')));

	
	}
	
	static function readUserPrivileges(){
		$tmp=array_merge(OracleHelper::$TECHNICAL_ACCOUNTS,OracleHelper::$ORACLE_ACCOUNTS);
		return new AuditItem('privileges utilisateurs',
		OracleHelper::listHelper('schema, privilege'),
		'SELECT grantee schema, privilege FROM dba_sys_privs p INNER JOIN dba_users u on (p.grantee=u.username) WHERE u.username not in ('.OracleHelper::makeQueryList($tmp,'user').') order by grantee, privilege', 
		Array(1,2),
		OracleHelper::makeQueryBinds($tmp,'user'));
	}
	
	static function readUserRoles(){
		$tmp=array_merge(OracleHelper::$TECHNICAL_ACCOUNTS,OracleHelper::$ORACLE_ACCOUNTS);
		return new AuditItem('roles utilisateurs',
		OracleHelper::listHelper('schema, granted_role'),
		'SELECT grantee schema, granted_role FROM dba_role_privs r INNER JOIN dba_users u on (r.grantee=u.username) WHERE u.username not in ('.OracleHelper::makeQueryList($tmp,'user').') order by grantee, granted_role',
		Array(1,2),OracleHelper::makeQueryBinds($tmp,'user'));
	}

	
	static function readRoleDB($isPrimary=true){
		if ($isPrimary){
			$title='Database open mode should be read write';
			$binds=Array(':a'=>'READ WRITE',':ok'=>'pass',':ko'=>'fail');
		}
		else {
			$title='Dataguard open mode should be read only with apply';
			$binds=Array(':a'=>'READ ONLY WITH APPLY',':ok'=>'pass',':ko'=>'fail');
		}
		return new AuditItem($title,
			OracleHelper::listHelper('NAME,DATABASE_ROLE, OPEN_MODE, PROTECTION_MODE, PROTECTION_LEVEL'),
			'SELECT NAME,DATABASE_ROLE, OPEN_MODE, PROTECTION_MODE, PROTECTION_LEVEL,  CASE WHEN OPEN_MODE = :a THEN :ok ELSE :ko END CSS_ from v$database',
			array(2),$binds);
	}
	
	static function readSessionSummary(){
		return new AuditItem('comptage sessions',
		OracleHelper::listHelper('type,Status,type'),
		'SELECT type,status,count(sid) cnt FROM v$session group by type,status',
		Array(),Array());
		
		
	}
	
	static function readOpenSessionSummary(){
		return new AuditItem( 'sessions ouvertes',
		OracleHelper::listHelper('Programme,Machine,User/schema,Session count,Status'),
		"SELECT program,machine,username||'/'||schemaname us,count(SID) SC, status|| CASE WHEN sum(CASE WHEN audsid=sys_context(:ue,:si) THEN 1 ELSE 0 END)>0 THEN :cur ELSE '' END STATUS, CASE WHEN sum(CASE WHEN audsid=sys_context(:ue,:si) THEN 1 ELSE 0 END)>0 THEN :ok ELSE '' END CSS_ FROM v\$session WHERE type<>:a GROUP BY machine, program, username, schemaname, status ORDER BY schemaname, machine, program",
		array(),Array(':ue'=>'USERENV',':si'=>'SESSIONID',':ok'=>'pass',':cur'=>' (*)',':a'=>'BACKGROUND'));
	}
	
	static function readPasswordHash(){
		return new AuditItem('liste des mots de passes',
		OracleHelper::listHelper('User,SQL password'),//,
		"SELECT name, password, spare4 FROM sys.user$ WHERE type# = 1 and password is not null order by name",
//		"SELECT name,'alter user '||name||' identified by  values '''||spare4||':'||password||''';' FROM sys.user$ WHERE type# = 1 and password is not null order by name",
//			"SELECT u.username,dbms_metadata.get_ddl('USER', 'PP06612S' ) as ddl FROM dba_users u", //require SELECT_CATALOG_ROLE 
		Array(1),Array() );		
		
	}
	

	
	
}

Class Statement2Html{

	private $caption;
	private $headers;
	private $supCol;
	
	function __construct( $caption, $headers, $leftColNames = null ){
		$this->caption=$caption;
		if (is_array($leftColNames) ){
			$this->supCol=count($leftColNames);
			$this->headers=array_merge($leftColNames,$headers);
		}
		else {
			$this->supCol=0;
			$this->headers=$headers;
		}
	}


	function printTableContent(PreparedStatement_abstraction $stmt, $leftColValues=null){
		$cpt=0;
		if ( is_array($leftColValues) and count($leftColValues) === $this->supCol ){
			ob_start();
			foreach( $leftColValues as $cell ){
				echo '<td>',$cell,'</td>';
			}
			$htmlchunk=ob_get_clean();
			//echo htmlentities($htmlchunk);
			while ($row = $stmt->fetch_assoc()) {
				self::printTableRowWithUnsafeLeft($row, $htmlchunk );
				$cpt++;
			}
		}
		else{
			while ($row = $stmt->fetch_assoc()) {
				self::printTableRow($row  );
				$cpt++;
			}
		}
		if ( $cpt === 0 ){
			echo '<tr class="noresult"><td colspan="',count($this->headers),'">Pas de résultats</td></tr>',PHP_EOL;		
		}
		return $cpt;
	}

	function printTable(PreparedStatement_abstraction $stmt, $classnames=''){
		$this->printTableBegin($classnames);
		$this->printTableContent($stmt);
		$this->printTableEnd();
		
	}

	function printTableEnd(){
		echo '</tbody></table>',PHP_EOL,'<br/>',PHP_EOL;	
	}
	
	function printTableBegin($classnames=''){
		if (empty($classnames)){
			echo '<table><caption>',htmlentities($this->caption),'</caption><thead><tr>',PHP_EOL;
		}
		else {
			echo '<table class="',$classnames,'"><caption>',htmlentities($this->caption),'</caption><thead><tr>',PHP_EOL;			
		}
		foreach ($this->headers as $head ){
			echo '<th>',htmlentities($head),'</th>';
		}
		echo '</tr></thead><tbody>',PHP_EOL;		
	}
	

	static protected function printTableRow( $row ){
		if (isset($row['CSS_'])){
			echo '<tr class="',htmlentities($row['CSS_']),'">';
		}
		else{
			echo '<tr>';
		}
		foreach ( $row as $val => $col ){
			if ( $val === 'CSS_' )continue;
//			if (strlen($col)>48){
//				echo '<td><div class="cell">',htmlentities($col),'</div></td>';			
//			}
//			else {
				echo '<td>',htmlentities($col),'</td>';
//			}
		}
		echo '</tr>',PHP_EOL;
	}

	
	
	
	static protected function printTableRowWithUnsafeLeft($row, $tdchunk ){
		if (isset($row['CSS_'])){
			echo '<tr class="',htmlentities($row['CSS_']),'">';
		}
		else{
			echo '<tr>';
		}
		echo $tdchunk, PHP_EOL;
		foreach ( $row as $val => $col ){
			if ( $val === 'CSS_' )continue;
//			if (strlen($col)>48){
//				echo '<td><div class="cell">',htmlentities($col),'</div></td>';			
//			}
//			else {
				echo '<td>',htmlentities($col),'</td>';
//			}
		}
		echo '</tr>',PHP_EOL;
	}	
	
}



Abstract Class TestDatabase {
	protected $db;
	protected $config;
	protected $isOnline;
	protected $isPrimary;
	private static $sequence=0;
	private static $sections=array();
	public $verbose=false;
	
	
	function __construct( AdvancedOraConfig $oracle, $schema_name = '' ){
		$this->isOnline=false;
		$this->config=$oracle;
		$this->isPrimary=$oracle->isPrimary();
		$this->schema_name=$schema_name;
		try{
			$this->initConnexion(  );			
		}
		catch ( OraUnusableAccountException $e ){
			echo '<p class="error">Script can not connect due to account issue  (',get_class($e),'::',$e->getMessage(),')</p>';
		}
		catch (OraNotOpenException $e){
			echo '<p class="error">Database is probably only mounted (not open) (',get_class($e),'::',$e->getMessage(),')</p>';		
		}
		catch( OracleException $e){
			echo '<p class="error">Database can not be reached (',get_class($e),'::',$e->getMessage(),')</p>';
		}
		catch (ConfigException $e){
			echo '<p class="error">Connection on database is not allowed (',get_class($e),'::',$e->getMessage(),')</p>';
		}
			
			
	}
	
	protected function initConnexion(){
		$this->db = new OCI_DB_Wrapper(  $this->config->user, $this->config->password, $this->config->toEasyConnect() , 'UTF8'); 
		$this->db ->exec ("Alter Session Set NLS_LANGUAGE='FRENCH'");
		$this->db ->exec ("Alter Session Set NLS_DATE_FORMAT='yyyy/mm/dd hh24:mi:ss (day)'");
		//$this->db ->exec ("SET NLS_LANGUAGE=FRENCH_FRANCE.WE8MSWIN1252");
		// only without exception.		
		$this->isOnline=true;
	}	
	
	protected function printSection($text){
		$seq = self::$sequence++;
		self::$sections[$this->config->app][$this->config->env][$this->config->dbrole][]=Array($text,$seq);
		echo '<h4 id="sec',$seq,'">',$text,'</h4>', PHP_EOL;
	}
	
	protected function audit(AuditItem $x,$debug=false){
		$x->run( $this->db, $debug or $this->verbose );
	}
	
	function run($title, $headers, $query, $binds, $debug=false){
		
		$x= new AuditItem($title,$headers,$query,null,$binds);
		$x->run($this->db,$debug);	
	}
	
	abstract function scenario();

	function testArchiver(){
		$this->printSection('Archiver process status');	
		$this->audit(AuditItem::readArchiver());
	}

	function testActivity(){
		$this->printSection('Activité base de donnée');
		
		echo '<div class="boxed">',PHP_EOL,'<div>',PHP_EOL;
		$this->audit(AuditItem::readRessourceLimit());

		echo '</div><div>',PHP_EOL;
		
		$this->audit(AuditItem::readSessionSummary());
		
		echo '</div><div>',PHP_EOL;
		
		$this->audit(AuditItem::readOpenSessionSummary());
		
		echo '</div></div>',PHP_EOL,'<div class="clear"></div>',PHP_EOL;
	}	
	
	function testCapacity($schema){
		$this->printSection('Evaluation capacité de la base');
		echo '<div class="boxed"><div>',PHP_EOL;
		$this->audit( AuditItem::readTablespacePermanent());
		echo '</div><div>',PHP_EOL;
		$this->audit( AuditItem::readTablespaceTemporary());
		echo '</div><div>',PHP_EOL;
		$this->audit( AuditItem::readTablespaceUndo());
		echo '</div><div>',PHP_EOL;		
		$this->audit( AuditItem::readDataFileHeader());
		echo '</div></div><div class="clear"></div>',PHP_EOL;
		echo '<div class="boxed"><div>',PHP_EOL;
		$this->audit( auditItem::readDataSize($schema));
		echo '</div></div>',PHP_EOL,'<div class="clear"></div>',PHP_EOL;
	}
	
	function testCurseurs(){
		$this->printSection('vérification des curseurs');
		
		$this->audit(AuditItem::readOpenCursorConfiguration());		
		$this->audit(AuditItem::readCursorConfiguration());
		$this->audit(AuditItem::readCursor2());
	}

	function testIdentification(){
		$this->printSection('Identification de la base');
		$this->audit(AuditItem::readIdentite());
	}
	
	function testArchivelog(){
		$this->printSection('vérification gestion des archivelogs');	

		if ( $this->isPrimary ){
			$this->audit(AuditItem::readArchivelogManagementPrimary());
			$this->audit(AuditItem::readRmanBackup());
			$this->audit(AuditItem::readArchivelogTTL());
		}
		else {
			$this->audit(AuditItem::readArchivelogManagementDataguard());
		}

	}

	function testIndex($schema){
		$this->printSection('Controle des indexes pour '.$schema);
		$this->audit(AuditItem::readForeignKeyIndex($schema));
		$this->audit(AuditItem::readDoubleIndex($schema));
		$this->audit(AuditItem::readIndexTop($schema));	
	}
	
	function testPermission($users){	
		$this->printSection('Permissions utilisateurs');
		echo '<div class="boxed"><div>',PHP_EOL;
		$this->audit(AuditItem::readUsers());
		
		echo '</div><div>',PHP_EOL;
		$this->audit(AuditItem::readUserPrivileges());
		
		echo '</div><div>',PHP_EOL;
		$this->audit(AuditItem::readUserRoles());
		
		echo '</div><div>',PHP_EOL;
		$this->audit(AuditItem::readRoles());
		
		echo '</div><div>',PHP_EOL;
		$this->audit(AuditItem::readPermissions($users),false);
		
		echo '</div><div>',PHP_EOL;
		$this->audit(AuditItem::readPasswordHash());
		
		echo '</div></div>',PHP_EOL,'<div class="clear"></div>',PHP_EOL;
	}

	function testFastRecovery(){
		$this->printSection('Fast Recovery Area');
		$this->audit(AuditItem::readFastRecoveryAreaPointCount());
		$this->audit(AuditItem::readFastRecoveryAreaConfiguration());
		$this->audit(AuditItem::readFastRecoveryAreaUsage() );
		$this->audit(AuditItem::readFastRecoveryAreaPointList() );
	}
	

	static function printTableOfContent(){
		echo '<div class="toc"> table of content <ol>';
		foreach ( self::$sections as $appli => $envs ){
			foreach ( $envs as $env => $roles ){
				foreach ($roles as $role => $sections ){
					echo '<li>',htmlentities( $appli ),'/',AdvancedOraConfig::toEnvName($env), '/', htmlentities( $role ),'</li><div><ol>',PHP_EOL;  
					foreach ( $sections as $titre ){
						echo '<li>aller à <a href="#sec',$titre[1],'">',$titre[0],'</a></li>',PHP_EOL;
					}
					echo '</ol></div>',PHP_EOL;
				}
			}
			echo '</ol></div>',PHP_EOL;	
		}
	}
}
/*
dead code follows.
;

SELECT x.owner,x.table_name as table_name, x.constraint_name as fk_name,x.fk_columns as fk_columns, 
(SELECT count(*) FROM dba_ind_columns i WHERE i.table_owner=x.owner and x.table_name=i.table_name) count_existing_index,
index_name,y.index_columns,
:ko CSS_
FROM (SELECT a.owner, a.table_name,a.constraint_name, listagg(a.column_name, :glue ) within group ( order by a.position) fk_columns 
FROM dba_cons_columns a INNER JOIN dba_constraints b on (a.constraint_name = b.constraint_name and a.owner = b.owner)
WHERE  b.constraint_type = :ct GROUP BY a.owner, a.table_name, a.constraint_name )  x 
LEFT JOIN (SELECT table_owner, table_name, index_name, listagg(column_name, :glue) within group (order by column_position) index_columns 
FROM dba_ind_columns GROUP BY table_owner, table_name, index_name) y
ON (x.owner=y.table_owner and x.table_name = y.table_name and y.index_columns like x.fk_columns || :ending )
WHERE x.owner=:owner
ORDER by  1, 2 desc, 3;

SELECT i.table_owner||:dt||i.table_name indexed_table, A.OBJECT_OWNER||:dt||A.OBJECT_NAME index_name,A.OBJECT_TYPE,OPTIONS,A.count_Sql_id
FROM (
  SELECT OBJECT_OWNER,OBJECT_NAME,OBJECT_TYPE,OPTIONS, count (sql_id) count_Sql_id
  FROM (
    SELECT s.SQL_ID,OBJECT_OWNER,OBJECT_NAME,OBJECT_TYPE,OPTIONS
    FROM dba_hist_sql_plan p, DBA_HIST_SNAPSHOT n, DBA_HIST_SQLSTAT s
    WHERE n.SNAP_ID=s.SNAP_ID and s.sql_id=p.sql_id and OPERATION='INDEX' 
    and OBJECT_OWNER=:owner  and BEGIN_INTERVAL_TIME>sysdate-8
  ) 
  group by OBJECT_OWNER,OBJECT_NAME,OBJECT_TYPE,OPTIONS
) a
LEFT JOIN dba_indexes i on (a.OBJECT_OWNER=i.owner and a.OBJECT_NAME=i.INDEX_NAME)
order by count_sql_id desc;

*//*
 SELECT x.table_owner||:dot||x.table_name, x.index_owner||:dot||x.index_name good_index, ix.index_type||:space||ix.uniqueness||:space||x.cpt good_index_properties, x.index_columns good_index_column, y.index_owner||:dot||y.index_name poor_index, iy.index_type||:space||iy.uniqueness||:space||y.cpt sub_index_properties, y.index_columns sub_index_column, 
			CASE WHEN  ix.index_type<>iy.index_type THEN null WHEN ix.uniqueness=:onl1 and iy.uniqueness=:onl1 THEN :ok WHEN iy.uniqueness=:onl1 or x.expression > 0 or y.expression> 0 THEN :wr ELSE :ko END CSS_
FROM ( SELECT table_owner, table_name,index_owner, index_name, listagg(column_name, :glue) within group (order by column_position)||:glue index_columns, count(column_name) cpt, sum(expression) as expression
  FROM (SELECT table_owner, table_name,index_owner, index_name, column_name,column_position,0 as expression FROM dba_ind_columns UNION SELECT table_owner, table_name,index_owner, index_name, :fonct, column_position,1  FROM dba_ind_expressions) GROUP BY table_owner, table_name, index_owner, index_name) x 
INNER JOIN ( SELECT table_owner, table_name,index_owner, index_name, listagg(column_name, :glue) within group (order by column_position)||:glue index_columns, count( column_name) cpt, sum(expression) as expression
  FROM (SELECT table_owner, table_name,index_owner, index_name, column_name ,column_position,0 as expression FROM dba_ind_columns UNION SELECT table_owner, table_name,index_owner, index_name, :fonct, column_position,1  FROM dba_ind_expressions) GROUP BY table_owner, table_name, index_owner, index_name) Y 
ON (x.table_owner=y.table_owner and x.table_name=y.table_name and ( x.index_owner<>y.index_owner or  x.index_name<>y.index_name) ) 
INNER JOIN dba_indexes ix on ( ix.owner=x.index_owner  and ix.index_name = x.index_name )
INNER JOIN dba_indexes iy on ( iy.owner=y.index_owner  and iy.index_name = y.index_name )
WHERE x.table_owner=upper(:owner)  and x.index_columns like y.index_columns||:pct and y.cpt<=x.cpt order by 1,2,3;
	
		$this->run('Liste des index ',
		OracleHelper::listHelper('table_name, index name, columns, type, uniqueness, tablespace'),
		'SELECT c.table_owner||:dot||c.table_name tablename, i.owner||:dot||c.index_name indexname, listagg(c.column_name, :glue) within group (order by c.column_position) index_columns, i.index_type, i.uniqueness, i.tablespace_name
FROM dba_ind_columns c  INNER JOIN dba_indexes i on (i.table_name=c.table_name and i.table_owner=c.table_owner and c.index_name=i.index_name) WHERE c.table_owner=upper(:owner) GROUP BY c.table_owner,i.owner, c.table_name, c.index_name, i.index_type, i.uniqueness, i.tablespace_name order by 1,2,3 ',
		Array(':glue'=>',',':owner'=>$schema,':dot'=>'.'));



-	select GROUP#,SEQUENCE#,BYTES/1024/1024,MEMBERS,STATUS from v$log;
-	select GROUP#, STATUS,MEMBER from v$logfile;
-	select l.GROUP#,l.SEQUENCE#,l.BYTES/1024/1024,l.MEMBERS,l.STATUS,lf.GROUP#,lf.STATUS,lf.MEMBER from v$log l, v$logfile lf group by l.GROUP#;
-	select action_time, version, bundle_series, comments from dba_registry_history where bundle_series = 'SBP' or bundle_series = 'SAP' order by action_time desc;
-	SELECT CLIENT_NAME, STATUS FROM DBA_AUTOTASK_CLIENT;
-	SELECT WINDOW_NAME, AUTOTASK_STATUS, OPTIMIZER_STATS, SEGMENT_ADVISOR, SQL_TUNE_ADVISOR    FROM DBA_AUTOTASK_WINDOW_CLIENTS;
-	SELECT PNAME, PVAL1 FROM SYS.AUX_STATS$ WHERE SNAME = 'SYSSTATS_MAIN';
-	SELECT   PNAME,   SUBSTR(PVAL2, 1, 40) "DATE" FROM SYS.AUX_STATS$ WHERE SNAME = 'SYSSTATS_INFO' AND PNAME IN ('DSTART', 'DSTOP');
-	show parameter CPU_COUNT
		
		
*/
