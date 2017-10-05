#!/bin/bash
#         importclient str_backupfile
#@file    importclient
#@brief   Importa los datos de OpenGnsys de un archivo de backup: dhcp, pxe, páginas de inicio y configuración de la consola.
#@param 1 str_backupfile fichero de backup (creado con exportclient)
#@exception 1 Error de formato
#@exception 2 Sólo ejecutable por usuario root
#@exception 3 Sin acceso al fichero de backup
#@exception 4 Sin acceso a la configuración de OpenGnsys
#@version 1.1.0 - Versión inicial.
#@author  Irina Gómez - ETSII Univ. Sevilla
#@date    2016-10-18
#*/ ##

# Variables globales.
PROG="$(basename $0)"

DATE=$(date +%Y%m%d)
BACKUPFILE=$1
TMPDIR=/tmp/opengnsys_export
OPENGNSYS="/opt/opengnsys"
MYSQLFILE="$TMPDIR/ogAdmBD.sql"
MYSQLFILE2="$TMPDIR/usuarios.sql"
MYSQLBCK="$OPENGNSYS/doc/ogAdmBD.sql-$DATE"
CATALOG="ogAdmBD"

DEFAULT_MYSQL_ROOT_PASSWORD="passwordroot"      # Clave por defecto root de MySQL

# Si se solicita, mostrar ayuda.
if [ "$*" == "help" ]; then
    echo -e "$PROG: Importa los datos de OpenGnsys desde un archivo de backup:" \
           " dhcp, pxe, páginas de inicio y configuración de la consola.\n" \
           "    Formato: $PROG backup_file\n" \
           "    Ejemplo: $PROG backup.tgz"
    exit
fi

# Comprobamos número de parámetros
if [ $# -ne 1 ]; then
    echo "$PROG: ERROR: Error de formato: $PROG backup_file"
    exit 1
fi

# Comprobar parámetros.
if [ "$USER" != "root" ]; then
        echo "$PROG: Error: solo ejecutable por root." >&2
        exit 2
fi

# Comprobamos acceso al fichero de backup
if ! [ -r $BACKUPFILE ]; then
    echo "$PROG: ERROR: Sin acceso al fichero de backup." | tee -a $FILESAL
    exit 3
fi

# Comprobamos  acceso a ficheros de configuración
if ! [ -r $OPENGNSYS/etc/ogAdmServer.cfg ]; then
    echo "$PROG: ERROR: Sin acceso a la configuración de OpenGnsys." | tee -a $FILESAL
    exit 4
fi

# Si existe el directorio auxiliar lo borramos
[ -d $TMPDIR ] && rm -rf $TMPDIR

# Descomprimimos backup
tar -xvzf $BACKUPFILE --directory /tmp &>/dev/null

# Comprobamos si es la misma versión
if ! diff $OPENGNSYS/doc/VERSION.txt $TMPDIR/VERSION.txt > /dev/null ; then
    echo "La versión del servidor no coincide con la del backup."
    cat $OPENGNSYS/doc/VERSION.txt $TMPDIR/VERSION.txt
    read -p "¿Quiere continuar? (y/n): " ANSWER
    if [ "${ANSWER^^}" != "Y" ]; then
        echo "Operación cancelada."
        exit 0
    fi
fi

# Copiamos los archivo a su sitio correcto
# DHCP
echo "   * Componemos la configuración del dhcp."
for DHCPCFGDIR in /etc/dhcp /etc/dhcp3; do
    if [ -r $DHCPCFGDIR/dhcpd.conf ]; then
        # Tomamos las variables globales de la configuración actual y las declaraciones host del backup
        # Inicio declaraciones host
        OLDHOSTINI=$(grep -n -m1 -e "^[[:blank:]]*host" -e "^#[[:blank:]]*host" $TMPDIR/dhcpd.conf|cut -d: -f1)
        let BEFOREHOST=$(grep -n -m1 -e "^[[:blank:]]*host" -e "^#[[:blank:]]*host" $DHCPCFGDIR/dhcpd.conf| cut -d: -f1)-1
        # Copia de seguridad de la configuración anterior
        cp $DHCPCFGDIR/dhcpd.conf $DHCPCFGDIR/dhcpd.conf-LAST
        mv $DHCPCFGDIR/dhcpd.conf $DHCPCFGDIR/dhcpd.conf-$DATE
        # Nuevo fichero
        sed ${BEFOREHOST}q $DHCPCFGDIR/dhcpd.conf-LAST > $DHCPCFGDIR/dhcpd.conf
        sed -n -e "$OLDHOSTINI,\$p" $TMPDIR/dhcpd.conf >> $DHCPCFGDIR/dhcpd.conf
        break
    fi
done

# TFTP
echo "   * Guardamos los ficheros PXE de los clientes."
mv $OPENGNSYS/tftpboot/menu.lst $OPENGNSYS/tftpboot/menu.lst-$DATE
cp -r $TMPDIR/menu.lst  $OPENGNSYS/tftpboot
chown -R www-data:www-data $OPENGNSYS/tftpboot/menu.lst

# Configuración de los clientes
echo "   * Guardamos la configuración de los clientes."
mv $OPENGNSYS/client/etc/engine.cfg $OPENGNSYS/client/etc/engine.cfg-$DATE
cp $TMPDIR/engine.cfg $OPENGNSYS/client/etc/engine.cfg

# Páginas de inicio
echo "   * Guardamos las páginas de inicio."
mv $OPENGNSYS/www/menus $OPENGNSYS/www/menus-$DATE
cp -r $TMPDIR/menus $OPENGNSYS/www

# MYSQL
echo "   * Importamos informacion mysql."
source $OPENGNSYS/etc/ogAdmServer.cfg
# Crear fichero temporal de acceso a la BD
MYCNF=$(mktemp /tmp/.my.cnf.XXXXX)
chmod 600 $MYCNF
trap "rm -f $MYCNF" 1 2 3 6 9 15
cat << EOT > $MYCNF
[client]
user=$USUARIO
password=$PASSWORD
EOT

# Si la BD tiene no definido el trigger necesitamos permisos de root
mysql --defaults-extra-file=$MYCNF -e "SHOW TRIGGERS FROM ogAdmBD;" |grep "Trigger" &>/dev/null
if [ $? -eq 0 ]; then
    # Existe el trigger: eliminamos líneas del trigger en ogAdmBD.sql
    read INI END <<< $(grep -n -e TRIGGER -e "END.*;;" $MYSQLFILE |cut -d: -f1)
    sed -i "$INI,${END}d" $MYSQLFILE
else
    # No existe: necesitamos privilegios de root
    # Clave root de MySQL
    while : ; do
        echo -n -e "\\nEnter root password for MySQL (${DEFAULT_MYSQL_ROOT_PASSWORD}): ";
        read -r MYSQL_ROOT_PASSWORD
        if [ -n "${MYSQL_ROOT_PASSWORD//[a-zA-Z0-9]/}" ]; then # Comprobamos que sea un valor alfanumerico
                echo -e "\\aERROR: Must be alphanumeric, try again..."
        else
                # Si esta vacio ponemos el valor por defecto
                MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-$DEFAULT_MYSQL_ROOT_PASSWORD}"
                break
        fi
    done
    cat << EOT > $MYCNF
[client]
user=root
password=$MYSQL_ROOT_PASSWORD
EOT

fi

# Copia de seguridad del estado de la base de datos
mysqldump --defaults-extra-file=$MYCNF --opt $CATALOG > $MYSQLBCK
# Importamos los datos nuevos
mysql --defaults-extra-file=$MYCNF -D "$CATALOG" < $MYSQLFILE &>/dev/null
[ $? -ne 0 ] && echo "ERROR: Error al importar la información de la base de datos."
# Importamos datos tabla usuario, ignoramos los repetidos
sed -i -e s/IGNORE//g -e s/INSERT/"\nALTER TABLE usuarios  ADD UNIQUE (usuario);\n\nINSERT IGNORE"/g $MYSQLFILE2
mysql --defaults-extra-file=$MYCNF -D "$CATALOG" < $MYSQLFILE2 &>/dev/null
[ $? -ne 0 ] && echo "ERROR: Error al importar la información de los usuarios de la consola"
# Borrar fichero temporal
rm -f $MYCNF

echo -e "Se ha terminado de importar los datos del backup. \n\nSe han realizado copias de seguridad de los archivos antiguos:" 
echo    "  - $DHCPCFGDIR/dhcpd.conf-$DATE"
echo    "  - $OPENGNSYS/tftpboot/menu.lst-$DATE"
echo    "  - $OPENGNSYS/client/etc/engine.cfg-$DATE"
echo    "  - $OPENGNSYS/www/menus-$DATE"
echo -e "  - $MYSQLBCK \n"

echo "Hay que revisar la configuración del dhcp. En la consola es necesario configurar los valores de las ips de repositorios, servidores ntp, etc y lanzar el \"netBoot Avanzado\" a todas las aulas"
