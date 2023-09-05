<?php


//--- définition environnements ---
//GMAO-R 
AdvancedOraConfig::have(Array('app'=>'GMAO-R',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAORES1.LOCAL',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));
AdvancedOraConfig::have(Array('app'=>'GMAO-R',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::DTGD,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAORES1.LOCAL',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));
ncedOraConfig::have(Array('app'=>'GMAO-R',
				'env'=>AdvancedOraConfig::VA,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAORES1.LOCAL',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));
				
AdvancedOraConfig::have(Array('app'=>'GMAO-R',
				'env'=>AdvancedOraConfig::VA,
				'role'=>AdvancedOraConfig::DTGD,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAORES1.LOCAL',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));

AdvancedOraConfig::have(Array('app'=>'GMAO-R',
				'env'=>AdvancedOraConfig::PR,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAORES1.LOCAL',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));
				
AdvancedOraConfig::have(Array('app'=>'GMAO-R',
				'env'=>AdvancedOraConfig::PR,
				'role'=>AdvancedOraConfig::DTGD,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAORES1.LOCAL',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));				
				
				
//GMAO-PS			
AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));
AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::DTGD,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));

AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'dg_iq',
				'password'=>'Soleil123!'));
AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::IQ,
				'role'=>AdvancedOraConfig::DTGD,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'dg_iq',
				'password'=>'Soleil123!'));

				
AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::VA,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));
AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::VA,
				'role'=>AdvancedOraConfig::DTGD,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));

				
AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::PR,
				'role'=>AdvancedOraConfig::PRIM,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));
AdvancedOraConfig::have(Array('app'=>'GMAO-PS',
				'env'=>AdvancedOraConfig::PR,
				'role'=>AdvancedOraConfig::DTGD,
				'server'=>'server.local',
				'port'=>1521,
				'servicename'=>'GMAOPS2',
				'user'=>'YOUR_USER',
				'password'=>base64_decode('***')));


//-------------------------------------------
// mots de passes par défaut
$conf['GMAO-R'][USER]='YOUR_USER';
$conf['GMAO-R'][PASS]='****';
$conf['GMAO-PS'][USER]='YOUR_USER';
$conf['GMAO-PS'][PASS]='****';
//-----------------------------------
