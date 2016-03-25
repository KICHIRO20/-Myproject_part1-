#!/bin/bash

if [ ! $1 ]
then
    echo "Avactis Restore Tool 1.1"
    echo "usage: restore.sh BACKUPFILE [DESTINATIONDIR [DATABASENAME]]"
    echo "BACKUPFILE     - backup file (.tar.gz) to restore"
    echo "DESTINATIONDIR - directory to restore to"
    echo "DATABASENAME   - MySQL database to restore to"
    exit 0
fi


# Detect the script path
#
# It defines variables: $script_path and $current_dir
detect_script_path()
{
    current_dir=`pwd`
    script_run_path=$0

    DN=`dirname $0`
    cd "$DN"
    script_path=`pwd`/backup.sh
}

# Init $script_path and $current_dir
detect_script_path

lib_path=`dirname $script_path`/lib.sh
. $lib_path

# Update PATH
PATH='/bin:/usr/bin:/usr/local/bin'

# Run environment test
echo -n "Testing environment ... "
test_env
if [ $? -ne "0" ]
then
    exit 1
fi
echo "OK"
    
echo -n "Detecting Avactis installation ... "
# Init $asc_root_dir, $asc_backup_dir, $backup_file and $asc_conf_file
detect_avactis_paths
if [ $? -ne "0" ]
then
    exit 1
fi
echo "OK"
	
# Init $db_server, $db_user, $db_pswd, $db_prefix and $db_name
detect_database_info
	
# Init $asc_version
detect_avactis_info

if [ $2 ]
then
    target_dir=$2
else
    target_dir=$asc_root_dir
fi

if [ $3 ]
then
    target_db=$3
fi

echo "Restoring $1 to $target_dir ... "
restore $1
