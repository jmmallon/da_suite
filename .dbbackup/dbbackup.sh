#/bin/bash
date=$(date +"%Y-%m-%d %T")
echo Starting backup: $date
cd /home4/deliciou/public_html/.suite/.dbbackup
rm dadb.zip
/usr/bin/mysqldump -udeliciou_jm -p2cwhptqj9p7y deliciou_suite played songlist | /usr/bin/zip -9 dadb.zip -
/usr/local/cpanel/3rdparty/lib/path-bin/git add dadb.zip
/usr/local/cpanel/3rdparty/lib/path-bin/git commit -m "Database backup for $date"
/usr/local/cpanel/3rdparty/lib/path-bin/git push
echo
echo "==================================="
echo
