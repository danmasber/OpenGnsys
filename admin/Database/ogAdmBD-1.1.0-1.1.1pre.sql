### Fichero de actualización de la base de datos.
# OpenGnsys 1.1.0 - OpenGnsys 1.1.1
#use ogAdmBD

# Añadir campo para incluir PC de profesor de aula (ticket #816).
ALTER TABLE aulas
	ADD idordprofesor INT(11) DEFAULT 0 AFTER puestos;

