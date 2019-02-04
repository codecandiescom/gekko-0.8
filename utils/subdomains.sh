#!/bin/bash

function usage {
	echo "$0 - Creates a subdomain-based Gekko installation. (Tested";
	echo "under Apache only).";
	echo "--enable                   Sets Gekko in subdomain mode.";
	echo "--disable                  Sets Gekko in normal mode.";
	echo "--create [subdomain]       Creates environment for the giv";
	echo "en subdomain (it must be in your vhost.conf too!).";
	echo "--delete [subdomain]       Removes files for the given subd";
	echo "omain.";
	exit;
}

if [ ! $1 ];
then
	usage;
fi;

case $1 in
	--remove )
		if [ $2 ];
		then
			rm -rf ../src/virtual/$2
		fi;
	;;
	--create )
		if ([ -e ../src/virtual ] && [ $2 ]);
		then
			mkdir -p ../src/virtual/$2
			cp ../src/cgdb.php ../src/virtual/$2/cgdb.php
			mkdir -p ../src/virtual/$2/temp ../src/virtual/$2/data
			chmod 777 ../src/virtual/$2/temp ../src/virtual/$2/data
			> ../src/virtual/$2/dbconf.php
		fi;
	;;
	--enable )
		sed s/define\(\"GEKKO_SUBDOMAIN_MODE\".*/define\(\"GEKKO_SUBDOMAIN_MODE\",\ true\)\\\;/g ../src/conf.php > ../src/conf.php.tmp
		mv ../src/conf.php.tmp ../src/conf.php
		
		sed s/^\#Rewrite/Rewrite/g ../src/data/.htaccess > ../src/data/.htaccess.tmp
		mv ../src/data/.htaccess.tmp ../src/data/.htaccess
		
		mkdir -p ../src/virtual
		echo "You must manually edit ../src/data/.htaccess for being"
		echo "compatible with your server.";
		echo "Now you can add subdomains via $0 --create mysubdomain.";
	;;
	--disable )
		sed s/define\(\"GEKKO_SUBDOMAIN_MODE\".*/define\(\"GEKKO_SUBDOMAIN_MODE\",\ false\)\\\;/g ../src/conf.php > ../src/conf.php.tmp
		mv ../src/conf.php.tmp ../src/conf.php
		
		sed s/^Rewrite/\#Rewrite/g ../src/data/.htaccess > ../src/data/.htaccess.tmp
		mv ../src/data/.htaccess.tmp ../src/data/.htaccess
		
		echo "You must manually remove ../src/virtual directory."
	;;
esac

