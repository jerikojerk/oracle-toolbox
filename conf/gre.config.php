<?php


//--- définition environnements ---
AdvancedOraConfig::have(Array('app'=>'PACIFIC',
				'env'=>AdvancedOraConfig::VA,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1523,
				'servicename'=>'PACIFIC',
				'user'=>'YOUR_USER',
				'password'=>'***'));


AdvancedOraConfig::have(Array('app'=>'RGF93',
				'env'=>AdvancedOraConfig::R7,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'RGF93',
				'user'=>'YOUR_USER',
				'password'=>'***'));

AdvancedOraConfig::have(Array('app'=>'PACIFIC',
				'env'=>AdvancedOraConfig::R7,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'PACIFIC ',
				'user'=>'YOUR_USER',
				'password'=>'***'));				
		

//(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=zep308e1)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=PACIFIC)))	
AdvancedOraConfig::have(Array('app'=>'ATLAS',
				'env'=>AdvancedOraConfig::PR,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1523,
				'servicename'=>'ATLAS',
				'user'=>'YOUR_USER',
				'password'=>'***'));

AdvancedOraConfig::have(Array('app'=>'ATLAS',
				'env'=>AdvancedOraConfig::PR,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1523,
				'servicename'=>'ATLAS',
				'user'=>'YOUR_USER',
				'password'=>'***'));
				
				

				AdvancedOraConfig::have(Array('app'=>'ATLAS',
				'env'=>AdvancedOraConfig::VA,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1523,
				'servicename'=>'ATLAS',
				'user'=>'YOUR_USER',
				'password'=>'***'));
				
				AdvancedOraConfig::have(Array('app'=>'ATLAS',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1523,
				'servicename'=>'ATLAS',
				'user'=>'YOUR_USER',
				'password'=>'***'));
				
				#pacific/Manager+2013
				AdvancedOraConfig::have(Array('app'=>'PACIFIC',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1523,
				'servicename'=>'PACIFIC',
				'user'=>'YOUR_USER',
				'password'=>'***'));
				
				AdvancedOraConfig::have(Array('app'=>'PACIFIC',
				'env'=>AdvancedOraConfig::PR,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1523,
				'servicename'=>'PACIFIC',
				'user'=>'YOUR_USER',
				'password'=>'***'));				
			