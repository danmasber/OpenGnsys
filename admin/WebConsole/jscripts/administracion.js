﻿// *************************************************************************************************************************************************
// Libreria de scripts de Javascript
// Autor: José Manuel Alonso (E.T.S.I.I.) Universidad de Sevilla
// Fecha Creación: 2009-2010
// Fecha Última modificación: Noviembre-2005
// Nombre del fichero: administracion.js
// Descripción : 
//		Este fichero implementa las funciones javascript del fichero administracion.php
// *************************************************************************************************************************************************
	function Asignar(){
		reset_contextual(-1,-1); // Oculta menu contextual
		var identificador=currentNodo.toma_identificador();
		var literal=currentNodo.toma_infonodo();
		var wurl="../varios/administradores_centros.php?idcentro="+identificador+"&nombrecentro="+literal;
		window.open(wurl,"frame_contenidos");
	}
	//__________________________________________________________________________________________
	function Asignar_Usuario(){
		reset_contextual(-1,-1); // Oculta menu contextual
		var identificador=currentNodo.toma_identificador();
		var literal=currentNodo.toma_infonodo();
		var wurl="../varios/administradores_usuarios.php?idusuario="+identificador+"&nombre="+literal;
		window.open(wurl,"frame_contenidos");
	}
