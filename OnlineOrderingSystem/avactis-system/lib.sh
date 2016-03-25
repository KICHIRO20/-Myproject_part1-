# Error checker
#
# Params:
# $1 - return code, 0 - no error
# $2 - error message
#
# Return: 
# 0 if no error
# 1 if error
check_error()
{
if [ $1 -ne "0" ]
then
    echo $2
    return 1
fi
return 0
}


# Test environment
#
# Params: 
#
# Return:
# 0 if no error
# 1 if eror
test_env()
{
    result=0
    
    commands='dirname xargs date find tar sed rm gzip mysqldump md5sum mysql'
    for i in $commands
    do
	grep_result=`$i --version > /dev/null 2>&1`
	check_error $? "ERROR: $i command not found" || result=1
    done
    
    return $result
}


# Detect the main Avactis pathes: root dir, backup dir and etc
#
# Requires: $script_path variable
#
# Defines:
# $asc_root_dir - Avactis installation directory
# $asc_backup_dir - /install/dir/avactis-conf/backup/
# $backup_file - current backup file path like /install/dir/avactis-conf/backup/backup_YYYY-MM-DD_HH-MM-SS.tar
# $asc_conf_file - Avactis config.php path
detect_avactis_paths()
{
    error=0
    
    asc_root_dir=`dirname $script_path | xargs -i dirname {}`
    if [ ! -d $asc_root_dir ]
    then
        echo "ERROR: $asc_root_dir doesn't exist"	
        error=1
    fi
    
    asc_backup_dir=$asc_root_dir/avactis-conf/backup/
    if [ ! -d $asc_backup_dir ]
    then
        echo "ERROR: $asc_backup_dir doesn't exist"	
        error=1
    fi
    
    asc_conf_file=$asc_root_dir/avactis-conf/config.php
    if [ ! -r $asc_conf_file ]
    then
        echo "ERROR: $asc_conf_file doesn't exist"	
        error=1
    fi
    
    asc_version_file=$asc_root_dir/avactis-system/core/version.php
    if [ ! -r $asc_version_file ]
    then
        echo "ERROR: $asc_version_file doesn't exist"	
        error=1
    fi
    
    asc_cache_dir=$asc_root_dir/avactis-conf/cache/
    if [ ! -d $asc_cache_dir ]
    then
        echo "ERROR: $asc_cache_dir doesn't exist"	
        error=1
    fi
    
    bname="backup_`date +%Y-%m-%d_%H-%M-%S`"
    backup_file=$asc_backup_dir$bname.tar    
    
    return $error
}

detect_database_info()
{
    db_server=`grep DB_SERVER $asc_conf_file |  sed "s/DB_SERVER\s*=\s*\"*\(.*\)\"*/\1/" | sed "s/[\"\n\r\t ]*$//" `
    db_user=`grep DB_USER $asc_conf_file |  sed "s/DB_USER\s*=\s*\"*\(.*\)\"*/\1/" | sed "s/[\"\n\r\t ]*$//" `
    db_name=`grep DB_NAME $asc_conf_file |  sed "s/DB_NAME\s*=\s*\"*\(.*\)\"*/\1/" | sed "s/[\"\n\r\t ]*$//" `
    db_pswd=`grep DB_PASSWORD $asc_conf_file |  sed "s/DB_PASSWORD\s*=\s*\"*\(.*\)\"*/\1/" | sed "s/[\"\n\r\t ]*$//" `
    db_prefix=`grep DB_PREFIX $asc_conf_file |  sed "s/DB_PREFIX\s*=\s*\"*\(.*\)\"*/\1/" | sed "s/[\"\n\r\t ]*$//" `
}

read_abi_file()
{
    if [ ! -r $1 ]
    then
        echo "ERROR: Failed to read abi file $1"
    fi
    
    abi_asc_version=`grep asc_version $1 | sed "s/asc_version\s*=\s*\"\(.*\)\"/\1/"`
    abi_backup_file_name=`grep backup_file_name $1 | sed "s/backup_file_name\s*=\s*\"\(.*\)\"/\1/"`
    abi_backup_file_size=`grep backup_file_size $1 | sed "s/backup_file_size\s*=\s*\"\(.*\)\"/\1/"`
    abi_backup_file_hash=`grep backup_file_hash $1 | sed "s/backup_file_hash\s*=\s*\"\(.*\)\"/\1/"`
    abi_backup_date=`grep backup_date $1 | sed "s/backup_date\s*=\s*\"\(.*\)\"/\1/"`
}


detect_avactis_info()
{
    asc_version=`grep "define('PRODUCT_VERSION_NUMBER" $asc_version_file |  sed "s/define('PRODUCT_VERSION_NUMBER'\,\s*'\(.*\)');/\1/"`
}

make_file_backup()
{
    cd $asc_root_dir
    if [ $backup_mode = "quick" ]
    then
        tar rf $backup_file avactis-images
    else
        tar rf $backup_file --exclude="./avactis-conf/backup" --exclude="./avactis-conf/cache" --exclude="*/.svn" --exclude="*/_*" .
    fi
}

make_db_backup()
{
    if [ $backup_mode = "quick" ]
    then
        mysqldump -q -h$db_server -u$db_user -p$db_pswd $db_name --ignore-table=$db_name.${db_prefix}reports_product_info --ignore-table=$db_name.${db_prefix}reports_product_stat --ignore-table=$db_name.${db_prefix}reports_visitor_info --ignore-table=$db_name.${db_prefix}reports_visitor_seances --ignore-table=$db_name.${db_prefix}reports_visitor_seance_info --ignore-table=$db_name.${db_prefix}report_periods --ignore-table=$db_name.${db_prefix}sm_fedex_countries --ignore-table=$db_name.${db_prefix}sm_fedex_das_zips --ignore-table=$db_name.${db_prefix}sm_fedex_if_zips --ignore-table=$db_name.${db_prefix}sm_fedex_methods --ignore-table=$db_name.${db_prefix}sm_fedex_rates --ignore-table=$db_name.${db_prefix}sm_fedex_zips --ignore-table=$db_name.${db_prefix}reports_orders_stat  --ignore-table=$db_name.${db_prefix}reports_orders_products_stat --ignore-table=$db_name.${db_prefix}reports_crawlers_visits --ignore-table=$db_name.${db_prefix}reports_crawlers_info --ignore-table=$db_name.${db_prefix}reports_carts_stat --ignore-table=$db_name.${db_prefix}pm_tranzila_responses --ignore-table=$db_name.${db_prefix}pm_epdq_currency --ignore-table=$db_name.${db_prefix}pm_dibs_currency --ignore-table=$db_name.${db_prefix}pm_cyberbit_currency --ignore-table=$db_name.${db_prefix}currencies --ignore-table=$db_name.${db_prefix}countries > $asc_root_dir/avactis-conf/cache/__database.dump.sql
    else
        mysqldump -q -h$db_server -u$db_user -p$db_pswd $db_name > $asc_root_dir/avactis-conf/cache/__database.dump.sql
    fi
    check_error $? "ERROR: Failed to create DB dump" || exit 1

    cd $asc_root_dir/avactis-conf/cache/
    tar rf $backup_file __database.dump.sql
    check_error $? "ERROR: Failed to pack DB dump to $backup_file" || exit 1

    rm $asc_root_dir/avactis-conf/cache/__database.dump.sql
}

gzip_backup()
{
    cd $asc_backup_dir
    
    gzip $backup_file
    check_error $? "ERROR: Failed to gzip $backup_file" || exit 1
    
    backup_file=$backup_file.gz
}

detect_backup_info()
{
    backup_size=$(stat -c%s "$1")
    backup_hash_string=`md5sum $1`
    backup_hash=${backup_hash_string:0:32}
    backup_date=$(stat -c%Y "$1")
}

make_abi()
{
    abi_file=$asc_backup_dir$bname.abi

    detect_backup_info $backup_file

    echo "; Avactis Backup Information File" > $abi_file
    echo "asc_version = \"$asc_version\"" >> $abi_file
    echo "backup_file_name = \"$backup_file\"" >> $abi_file
    echo "backup_file_size = \"$backup_size\"" >> $abi_file
    echo "backup_file_hash = \"$backup_hash\"" >> $abi_file
    echo "backup_date = \"$backup_date\"" >> $abi_file
}

# $1 - backup file name (should be *.tar.gz file)
restore()
{
    backup_file_name=$1

    if [ ! -r $backup_file_name ]
    then
        echo "ERROR: Failed to retore backup $1, file doesn't exist"	
        exit 1
    fi
    
    detect_backup_info $backup_file_name
    
    abi_file_name=${backup_file_name/.tar.gz/.abi}
    read_abi_file $abi_file_name
    
    if [ $backup_size != $abi_backup_file_size ]
    then
        echo "ERROR: Backup file size ($backup_size) doesn't match with abi ($abi_backup_file_size)"
        exit 1
    fi
    
    if [ $backup_hash != $abi_backup_file_hash ]
    then
        echo "ERROR: Backup file md5 hash ($backup_hash) doesn't match with abi ($abi_backup_file_hash)"
        exit 1
    fi
    
    echo -n "Restoring files to $target_dir ... "
    tar -C $target_dir -xzf $1
    check_error $? "ERROR: Failed to restore $1" || exit 1
    echo "Done"

    if [ $target_db ]
    then
        db_name=$target_db
    fi

    echo -n "Restoring database to $db_name at MySQL server $db_server ... "
    cd $target_dir
    mysql -h$db_server -u$db_user -p$db_pswd $db_name < __database.dump.sql
    check_error $? "ERROR: Failed to restore DB from $1" || exit 1
    rm __database.dump.sql
    echo "Done"

    echo -n "Clearing cache ... "
    rm -rf $target_dir/avactis-conf/cache/*
    check_error $? "ERROR: Failed to clear cache" || exit 1
    echo "Done"
}

remove_old_backups() 
{
    if [ $backup_expiration_time -eq 0 ]
    then
        exit
    fi
    
    curtime=`date +"%s"`
    oldtime=$(($curtime-$backup_expiration_time))

    cd $asc_backup_dir
    for f in $( find -name "*.abi" ); do
        read_abi_file $f
        if [ $abi_backup_date -lt $oldtime ]; then
            rm $f
            rm $abi_backup_file_name
        fi
    done
}

backup_mode="quick"
