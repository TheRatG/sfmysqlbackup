# sfmysqlbackup
Console helper for [mydumper](https://github.com/maxbube/mydumper).

## Installation and requirements

. install [mydumper](https://github.com/maxbube/mydumper)
. install [mysql_config_editor](https://dev.mysql.com/doc/refman/5.7/en/mysql-config-editor.html)
. create unix user
```
useradd -m sfmysqlbackup
```
. login as `sfmysqlbackup`
```
su sfmysqlbackup
```
. download [sfmysqlbackup.phar](https://github.com/TheRatG/sfmysqlbackup) 
. run init, enter *--backup--dir*, *--database-url* (for example: mysql://db_user:db_password@127.0.0.1:3306/db_name), and *--remote-dir* 
```
php sfmysqlbackup.phar init --database-url=<url>
```

## Commands

### Init
Configure ~/.sfmysqlbackup/config.yaml file.

### Create
Create a dump using mydumper

### Show
Show list of local and remote backups

### Restore
Create a dump using myloader
