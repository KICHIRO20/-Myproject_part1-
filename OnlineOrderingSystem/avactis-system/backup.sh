#!/bin/bash

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

# The backups are removed if created more than backup_expiration_time seconds ago
backup_expiration_time=$((60*60*24*7))

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

if [ $1 ]
then
    backup_mode="quick"
else
    backup_mode="full"
fi

echo -n "Backing up Avactis directory ($backup_mode mode) ..."
make_file_backup
echo "Done"

echo -n "Backing up Avactis database ($backup_mode mode) ..."
make_db_backup
echo "Done"

echo -n "Creating backup archive ..."
gzip_backup
echo "Done"

echo -n "Creating backup info-file ..."
make_abi
echo "Done"

echo -n "Removing old backups ..."
remove_old_backups
echo "Done"
