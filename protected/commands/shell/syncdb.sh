echo '下载sql文件...'
mysqldump -uroot -h192.168.1.100 --database 10000km > ./10000km.sql
echo '下载完成.'
echo '开始导入...'
mysql -uroot < ./10000km.sql
echo '导入完成.'

