<?php
//require_once 'core.lib.php';

Abstract Class TestDatabaseGRE extends TestDatabase {
	
	function testLock($schema){	
		$this->printSection('Vérification verroux et session blockantes');
		
		$this->run('Arbre des sessions bloquantes',
		OracleHelper::listHelper('level,session id,locked objects, acquiring object,event,session_lock_mode,command,ferme citrix,machine, osuser, module, s.state, s.status, idle_time_blockant, logon_time'),
'		WITH base_sessions AS(SELECT sid, username, command, machine,module, osuser, logon_time, event,state,status, prev_exec_start, blocking_session, row_wait_obj#, sql_id FROM v$session),
		possessions AS (SELECT  session_id, LISTAGG(o2.owner||:dot||o2.object_name, :coma ) WITHIN GROUP (ORDER BY o2.owner,o2.object_name) possession from dba_objects o2 inner join V$LOCKED_OBJECT los2 on los2.object_id=o2.object_id  WHERE locked_mode > 0 GROUP BY session_id)
		SELECT lpad(:dot, LEVEL, :dot) ||  level,sid sid, ps.possession, o.owner||:dot||o.object_name object_name, s.event,  decode(los.locked_mode,'.OracleHelper::makeQueryLockTypeDecode('lk').',los.locked_mode) session_lock_mode, decode(s.command,'.OracleHelper::makeQueryCommandCodeDecode('cmd').' , s.command) command, case when upper(s.machine) like :e8 or upper(machine) like :rh then :ps4 WHEN upper(machine)  like :pg or upper(machine) like :bb then :xenapp else upper(s.machine) end ferme_citrix_bloquante, s.machine, s.osuser,  s.module,s.state,s.status, NUMTODSINTERVAL(current_date-s.prev_exec_start, :unit) idle_time_blockant, logon_time,
		case when s.command=:fap_command and o.object_name=:fap_table0 and o.owner=:fap_schema then :ko end css_   
		FROM base_sessions s LEFT JOIN dba_objects o ON (o.object_id = s.row_wait_obj#) LEFT JOIN v$locked_object los on (los.session_id=s.sid and los.object_id=o.object_id) LEFT JOIN possessions ps ON ps.session_id = s.sid  
		WHERE sid IN (SELECT distinct blocking_session FROM base_sessions )  OR blocking_session IS NOT NULL 
		CONNECT BY PRIOR sid = blocking_session START WITH blocking_session IS NULL',
		array_merge(Array(':unit'=>'DAY', ':dot'=>'.', ':coma' => ', ' ),// requete de base 
			Array(':e8'=>'ZE%8E%', ':rh'=>'ZE%RH%', ':pg'=>'ZE%PG%', ':bb'=>'ZE%8B%',  ':ps4'=>'FC Dédiée PS4', ':xenapp'=>'FC Mutualisée XenApp 6.5', ':ko'=>'fail', ':fap_command'=>26, ':fap_table0'=>'EMPRISE', ':fap_schema'=>'PACIFIC'), //surcouche pacific/atlas
			OracleHelper::makeQueryCommandCodeBinds('cmd'),
			OracleHelper::makeQueryLockTypeBinds('lk')),true);
	//WHERE sid IN (SELECT distinct blocking_session FROM tmp_sessions WHERE username = upper(:owner) ) OR blocking_session IS NOT NULL
		
		/*
		$this->run('tmp_sessions ',
		OracleHelper::listHelper('sid, username, command, machine,module, osuser, logon_time, event,state,status, prev_exec_start, blocking_session, row_wait_obj#, sql_id'),
		'SELECT sid, username, command, machine,module, osuser, logon_time, event,state,status, prev_exec_start, blocking_session, row_wait_obj#, sql_id FROM v$session where blocking_session IS NULL and username is not null',
		array(), true );
		
		
		$this->run('v$lock',
		OracleHelper::listHelper('ADDR,KADDR,SID,TYPE,ID1,ID2,LMODE,REQUEST,CTIME,BLOCK'),
		'select ADDR,KADDR,SID,TYPE,ID1,ID2,LMODE,REQUEST,CTIME,BLOCK from v$lock where block > 0',
		array(), true );	
		
		$this->run('V$LOCKED_OBJECT',
		OracleHelper::listHelper(' XIDUSN,XIDSLOT,XIDSQN,OBJECT_ID,SESSION_ID,ORACLE_USERNAME,OS_USER_NAME,PROCESS,LOCKED_MODE'),
		'select XIDUSN,XIDSLOT,XIDSQN,OBJECT_ID,SESSION_ID,ORACLE_USERNAME,OS_USER_NAME,PROCESS,LOCKED_MODE from V$LOCKED_OBJECT',
		array(), true );	
		*/

	}
		
	
	
	
	
}

Class TestDatabasePacific  extends TestDatabaseGRE {
	protected $schema_name;
	
	function __construct( OraConfig $oracle, $schema_name = 'WINCARTO' ){
		parent::__construct($oracle,$schema_name);
	}

	
	
	function scenario(){

		if (!$this->isOnline ){
			echo '<p>Tests are skipped</p>';
			return 1;
		}
		
		$this->testIdentification();
		$this->testArchiver();
			
			/*
			$stmt=$this->db->prepare('SELECT MAX(SEQUENCE#),CURRENT_DATE FROM V$ARCHIVED_LOG WHERE APPLIED=:a');
			$stmt->bind_by_name(':a'=>'YES');
			$r = new TestRunAndResults( 'last sequence', $stmt, array( 'MAX SEQUENCE#', 'CURRENT DATE' )); 
			$r->draw();
			*/
		$this->testArchivelog();
			
		$this->testCurseurs();
		if ($this->isPrimary) $this->testPermission(OracleHelper::listHelper('PACIFIC,WINCARTO,WINUSER'));
		$this->testFastRecovery();
		$this->testActivity();
		$this->testLock($this->schema_name);
		$this->testParameters();
		echo '<p>end of scenario</p>';
		return 0;	
	
	}

		
	function testParameters(){
		$this->printSection('Parametrages');
		$this->audit(AuditItem::readNLS());
		$this->audit(AuditItem::readOpenCursorConfiguration());
		$this->audit(AuditItem::readRessourceLimit());
		$this->audit(AuditItem::readCursorConfiguration());		
	}



}

Class TestDatabaseAtlas  extends TestDatabaseGRE {
	protected $schema_name;
	
	function __construct( AdvancedOraConfig $oracle, $schema_name = 'WINCARTO' ){
		parent::__construct($oracle,$schema_name);
	}
	
	
	function scenario(){

		if (!$this->isOnline ){
			echo '<p>Tests are skipped</p>';
			return 1;
		}
		
		$this->testIdentification();
		$this->testArchiver();
			
			/*
			$stmt=$this->db->prepare('SELECT MAX(SEQUENCE#),CURRENT_DATE FROM V$ARCHIVED_LOG WHERE APPLIED=:a');
			$stmt->bind_by_name(':a'=>'YES');
			$r = new TestRunAndResults( 'last sequence', $stmt, array( 'MAX SEQUENCE#', 'CURRENT DATE' )); 
			$r->draw();
			*/
		$this->testArchivelog();
			
		$this->testCurseurs();
		if ($this->isPrimary) $this->testPermission(OracleHelper::listHelper('PACIFIC,WINCARTO,WINUSER'));
		$this->testFastRecovery();
		$this->testActivity();
		$this->testLock($this->schema_name);
		$this->testParameters();
		echo '<p>end of scenario</p>';
		return 0;	
	
	}

		
	function testParameters(){
		$this->printSection('Parametrages');
		$this->audit(AuditItem::readNLS());
		$this->audit(AuditItem::readCursorConfiguration());
		$this->audit(AuditItem::readRessourceLimit());
		$this->audit(AuditItem::readCursorConfiguration());		
	}



}


