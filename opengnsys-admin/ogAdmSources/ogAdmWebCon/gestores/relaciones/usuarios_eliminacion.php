<?
// *************************************************************************************************************************************************
// Aplicaci�n WEB: Hidra
// Copyright 2003-2005  Jos� Manuel Alonso. Todos los derechos reservados.
// Fecha Creaci�n: A�o 2003-2004
// Fecha �ltima modificaci�n: Marzo-2005
// Nombre del fichero: usuarios_eliminacion.php
// Descripci�n :
//	Elimina en cascada registros de la tabla usuarios 
//		Parametros: 
//		-	cmd:Una comando ya operativo (con conexi�n abierta)  
//		-	identificador: El identificador por el que se eliminar� el usuario
//		-	nombreid: Nombre del campo identificador del registro 
//		-	swid: Indica 0= El identificador es tipo alfanum�rico	1= EI identificador es tipo num�rico ( valor por defecto) *************************************************************************************************************************************************
function	EliminaUsuarios($cmd,$identificador,$nombreid,$swid=1){
	if (empty($identificador)) return(true);
	if($swid==0)
		$cmd->texto="SELECT  idusuario  FROM  usuarios WHERE ".$nombreid."='".$identificador."'";
	else
		$cmd->texto='SELECT  idusuario  FROM usuarios WHERE '.$nombreid.'='.$identificador;
	$rs=new Recordset; 
	$rs->Comando=&$cmd; 
	if (!$rs->Abrir()) return(false); // Error al abrir recordset
	if ($rs->numeroderegistros==0) return(true);
	$rs->Primero(); 
	while (!$rs->EOF){	
		/*
		$cmd->texto="DELETE  FROM usuario_imagen WHERE idusuario=".$rs->campos["idusuario"];
		$resul=$cmd->Ejecutar();
		if (!$resul){
			$rs->Cerrar();
			return(false);
		}
		*/
		$rs->Siguiente();
	}
	if($swid==0)
		$cmd->texto="DELETE  FROM usuarios WHERE ".$nombreid."='".$identificador."'";
	else
		$cmd->texto='DELETE  FROM usuarios  WHERE '.$nombreid.'='.$identificador;
	$resul=$cmd->Ejecutar();
	return($resul);
}
?>
