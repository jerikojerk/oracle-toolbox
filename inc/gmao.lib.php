<?php


Class TestGmaoDataGuard  extends TestDatabase {
	private $schema_name; 
	
	function __construct( AdvancedOraConfig $oracle , $schema_name = 'maximo'){
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
			echo '<p class="error">Database is probably only mounted (not open), witch can be a wrong dataguard setting for GMAO applications (',get_class($e),'::',$e->getMessage(),')</p>';		
		}
		catch( OracleException $e){
			echo '<p class="error">Database can not be reached (',get_class($e),'::',$e->getMessage(),')</p>';
		}
	}
	
	


	
	function scenario(){
		if (!$this->isOnline ){
			echo '<p>Tests are skipped</p>';
			return 1;
		}
		
		$this->testIdentification();
		$this->testReplication();
		$this->testArchiver();
		$this->testArchivelog();
		
			$this->testCurseurs();
		if ($this->isPrimary) $this->testPermission(array('GMAADMIN','MAXIMO','MAXIMO_VISU','EGMAOPS','AAMADMIN'));
		$this->testFastRecovery();
		$this->testActivity();
		$this->audit(AuditItem::readLock($this->schema_name));
		$this->testCapacity($this->schema_name);
		
		//if ($this->isPrimary) $this->testIndex($this->schema_name);
		$this->testParameters();
		$this->testMetier();
		
		return 0;	
	
	}


	function testMetier(){
		$this->printSection('Vérification métier');
		echo '<div class="boxed"><div>',PHP_EOL;
		
			
		$this->run('Lecture des bannieres ('.$this->schema_name.'.maxmessages)',
			OracleHelper::listHelper('maxmessagesid,msgkey,value'),
			'select MAXMESSAGESID, MSGKEY, VALUE from '.$this->schema_name.'.maxmessages where MSGKEY in (:a,:b,:c)',
			Array(':a'=>'welcome',':b'=>'welcomeusername',':c'=>'welcomemaximomessage'));
		echo '</div><div>',PHP_EOL;
	
		$this->run('Lecture des bannieres ('.$this->schema_name.'.l_maxmessages)',
			OracleHelper::listHelper('msgkey, value,m.maxmessageid, m.msgkey'),
			'select l.OWNERID, l.VALUE, m.MAXMESSAGESID, m.MSGKEY from '.$this->schema_name.'.l_maxmessages l inner join '.$this->schema_name.'.maxmessages m on (l.ownerid=m.maxmessagesid) where m.MSGKEY in (:a,:b,:c)',
			Array(':a'=>'welcome',':b'=>'welcomeusername',':c'=>'welcomemaximomessage'));
		echo '</div><div>',PHP_EOL;	
		

		$title='Lecture des proprietes ('.$this->schema_name.'.maxpropvalue)';
		$sql='select propname, propvalue from '.$this->schema_name.'.maxpropvalue where propname in (:a,:b,:c, :d, :e, :f, :g, :h, :i, :j, :k, :l, :m) order by 1';
		$binds=Array(':a'=>'mxe.doclink.path01',
			':b'=>'mxe.help.host',
			':c'=>'mxe.help.port',
			':d'=>'mxe.hostname',
			':e'=>'mxe.int.webappurl',
			':f'=>'mxe.help.protocol',
			':g'=>'Database.Oracle.ServerHostName',
			':h'=>'mxe.db.url',
			':i'=>'mxe.adminEmail',
			':j'=>'Database.Oracle.InstanceName',
			':l'=>'mxe.logging.rootfolder',
			':k'=>'mxe.doclink.doctypes.topLevelPaths',
			':m'=>'mxe.int.globaldir'
	//		':ref'=>$tmp
		);
		
		$this->run($title,
			OracleHelper::listHelper('propname, propvalue'),
			$sql,
			$binds);

		echo '</div><div>',PHP_EOL;	
		
		try {
			$tmp=$this->config->findMyDataguardServer();
			$title='Configuration BIRT ('.$this->schema_name.'.reportdsparam)';
			$sql='select datasourcename, url, CASE WHEN REGEXP_LIKE(ext_url,:ip)  THEN :wr WHEN ext_url = ext_input THEN :ok ELSE :ko END CSS_ FROM ( select datasourcename, URL, regexp_substr(lower(url),:e1,1,1,:cs,1) ext_url, regexp_substr(lower(:ref),:e2,1,1,:cs,1) ext_input from '.$this->schema_name.'.reportdsparam where datasourcename=:a) x';
			$binds=Array(
				':ip'=>'^[0-9]+$',
				':a'=>'maximoDataSource', 
				':ref' => $tmp,
				':cs'=>'c',
				':e1'=>'@([a-z0-9_-]+)([a-z0-9_.-]*):',
				':e2'=>'([a-z0-9_-]+).?',
				':ok'=> 'pass',
				':ko'=> 'fail',
				':wr'=>'warning'
				);
		}
		catch(ConfigException $e){
			$title='Configuration BIRT ('.$this->schema_name.'.reportdsparam), [dataguard configuration not found]';
			$sql='select datasourcename, URL from '.$this->schema_name.'.reportdsparam where datasourcename=:a) x';
			$binds=Array(':a'=>'maximoDataSource');
		}
		
		$this->run($title,OracleHelper::listHelper('datasourcename, url'),$sql,$binds);
			
			
		echo '</div></div>',PHP_EOL,'<div class="clear"></div>',PHP_EOL;
	}

	
	function testParameters(){
		$this->printSection('Parametrages');
		
		$this->run('Lecture Parametres d\'initialisation maximo',
		OracleHelper::listHelper('nom, valeur, valeur affichable, recommendation maximo 2017, défaut oracle, description'),
		'select p.name, p.value, p.display_value, r.recommendation, p.isdefault, p.description, CASE WHEN r.recommendation <> p.value or r.recommendation <> p.display_value THEN :wr ELSE NULL END CSS_ 
		FROM v$parameter p inner join (select :p01 as name,:v01 as recommendation from dual UNION select :p02,:v02 from dual UNION select :p03,:v03 from dual UNION select :p04,:v04 from dual UNION select :p05,:v05 from dual UNION select :p06,:v06 from dual UNION select :p07,:v07 from dual UNION select :p08,:v08 from dual UNION select :p09,:v09 from dual UNION select :p10,:v10 from dual UNION select :p11,:v11 from dual UNION select :p12,:v12 from dual UNION select :p13,:v13 from dual UNION select :p14,:v14 from dual UNION select :p15,:v15 from dual UNION select :p16,:v16 from dual) r on p.name = r.name order by 1',
		array(':wr'=>'warning',
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
	));
		
		
		$this->audit(AuditItem::readNLS());	
	}

	//':u1'=>'GMAADMIN',':u2'=>'MAXIMO',':u3'=>'MAXIMO_VISU','u4'=>'EGMAOPS'
	

	function testReplication(){
		$this->printSection('vérification du mode de réplication');

		if ($this->isPrimary){
			$title='Database open mode should be read write';
			$binds=Array(':a'=>'READ WRITE',':ok'=>'pass',':ko'=>'fail');
		}
		else {
			$title='Dataguard open mode should be read only with apply';
			$binds=Array(':a'=>'READ ONLY WITH APPLY',':ok'=>'pass',':ko'=>'fail');
		}
		
		$this->run($title,
			OracleHelper::listHelper('NAME,DATABASE_ROLE, OPEN_MODE, PROTECTION_MODE, PROTECTION_LEVEL'),
			'SELECT NAME,DATABASE_ROLE, OPEN_MODE, PROTECTION_MODE, PROTECTION_LEVEL,  CASE WHEN OPEN_MODE = :a THEN :ok ELSE :ko END CSS_ from v$database',
			$binds);


		if ( $this->isPrimary ){
			$title='Primary database should answer with 2 lines, one for local database one for remote dataguard';
			$query='SELECT db_unique_name,dest_name,status, database_mode, recovery_mode, protection_mode, destination, gap_status, CASE WHEN database_mode=:d1 AND recovery_mode=:r1 THEN :ok WHEN database_mode=:d2 AND recovery_mode=:r2 THEN :ok ELSE :ko END CSS_ FROM v$archive_dest_status WHERE status=:a';
			$binds=Array(':a'=>'VALID',':d1'=>'OPEN',':d2'=>'OPEN_READ-ONLY',':r1'=>'IDLE',':r2'=>'MANAGED REAL TIME APPLY',':ok'=>'pass',':ko'=>'fail');
		}else{
			$title='Dataguard should be OPEN_READ-ONLY and MANAGED REAL TIME APPLY';
			$query='SELECT db_unique_name,dest_name,status, database_mode, recovery_mode, protection_mode, destination, gap_status, CASE WHEN dest_name=:d OR database_mode=:u THEN :wa WHEN database_mode =:b AND recovery_mode=:c THEN :ok ELSE :ko END CSS_ FROM v$archive_dest_status WHERE status=:a';
			$binds=Array(':a'=>'VALID',':b'=>'OPEN_READ-ONLY',':c'=>'MANAGED REAL TIME APPLY',':d'=>'STANDBY_ARCHIVE_DEST',':u'=>'UNKNOWN',':ok'=>'pass',':ko'=>'fail',':wa'=>'warning');
		}
			
		$this->run($title,OracleHelper::listHelper('DB_UNIQUE_NAME,DEST_NAME,STATUS, DATABASE_MODE, RECOVERY_MODE, PROTECTION_MODE, DESTINATION, GAP_STATUS'),$query,$binds);

		if ( !$this->isPrimary ){
				$this->run('MRP should be started',
				OracleHelper::listHelper('PROCESS, STATUS'),
				'SELECT PROCESS, STATUS, CASE WHEN STATUS = :a THEN :ok  WHEN STATUS = :b THEN NULL ELSE :ko END CSS_ FROM V$MANAGED_STANDBY where PROCESS LIKE :c',
				Array(':ok'=>'pass',':ko'=>'fail',':a'=>'APPLYING_LOG',':b'=>'WAIT_FOR_LOG',':c'=>'MRP%'));
				
		
				$this->printSection('vérification du délais de réplication');
				$this->audit(AuditItem::readDataguardLag());
			}
	
	
		$this->audit(AuditItem::readArchivelogConf());
		
		$this->audit(AuditItem::readArchivelogTTL(), true);
		
		/*
		$this->run('Lecture Parametres d\'initialisation Archivelog',
		OracleHelper::listHelper('nom, valeur affichable, défaut oracle, description'),
		'SELECT p.name, p.display_value, p.isdefault, p.description FROM v$parameter p WHERE (p.name LIKE :b or p.name LIKE :a) and p.isdefault=:c order by 1',
		array( ':a'=>'log%',':b'=>'arch%',':c'=>'FALSE'));	
		*/
	}	

}


/**

Dead code

SELECT LPAD( level,2*level,'-') Lvl,grantee,granted,admin_option 
FROM (
SELECT grantee, 'Role: '||granted_role||' (default:'||default_role||')' granted, granted_role, admin_option 
FROM dba_role_privs 
union 
SELECT grantee, 'Priv: '|| privilege granted, null, admin_option 
FROM dba_sys_privs  
union 
select  grantee, 'Obj:'||p.privilege||' on '||owner||'.'||table_name granted, null, grantable  from dba_tab_privs p where p.grantee in ('GMAADMIN','MAXIMO','MAXIMO_VISU','EGMAOPS') 
)
START with (grantee in ('GMAADMIN','MAXIMO','MAXIMO_VISU','EGMAOPS') ) 
CONNECT by prior granted_role=grantee and grantee not like 'DBA'
;

*/


