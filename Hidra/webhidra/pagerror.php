<?
// *************************************************************************************************************************************************
// Aplicaci� WEB: Hidra
// Copyright 2003-2005 Jos�Manuel Alonso. Todos los derechos reservados.
// Fecha Creaci�: Diciembre-2003
// Fecha �tima modificaci�: Febrero-2005
// Nombre del fichero: controlacceso.php
// Descripci� :Este fichero implementa el control de acceso a la aplicaci� en todas las p�inas
// *************************************************************************************************************************************************
include_once("./includes/ctrlacc.php");
//________________________________________________________________________________________________________
$herror=0;
if (isset($_GET["herror"])) $herror=$_GET["herror"]; 
echo $urlacceso;
//________________________________________________________________________________________________________
?>
<HTML>
	<TITLE> Administraci� web de aulas</TITLE>
	<HEAD>
		<LINK rel="stylesheet" type="text/css" href="hidra.css">
	</HEAD>
	<BODY>
		<?
			echo '<SCRIPT LANGUAGE="JAVASCRIPT">'.chr(13);
			echo '	var o=window.top;'.chr(13);
			echo '	var ao=o.parent;'.chr(13);
			echo '	while (o!=ao){ // Busca la primera ventana del navegador'.chr(13);
			echo '	 ao=o;'.chr(13);
			echo '	 o=o.parent;';
			echo '	 };'.chr(13);
			echo '	//ao.location="'.$urlacceso.'?herror='.$herror.'";'.chr(13);
			echo '</SCRIPT>'.chr(13);
		?>
	</BODY>
</HTML>
