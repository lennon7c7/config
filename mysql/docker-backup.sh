#!/bin/bash

# 遇到错误时终止脚本的执行
set -e


echo "---------- 备份数据库，只保留最近7天的 ----------"

# 星期几的全称（Sunday - Saturday）
DATE=$(date +%A)

# 以当前目录 设置成 基础目录
BASE_PATH=$(cd "$(dirname "$0")"; pwd)

## 以项目目录名称 设置成 数据库名
DB_NAME=$(dirname $0)
DB_NAME=${DB_NAME##*/}

DB_USER='root'
DB_PASSWORD='root'
DB_OUTPUT="${BASE_PATH}/${DATE}.sql"

# 备份数据库
docker exec mysql sh -c "cd /data/log && /usr/bin/mysqldump -u${DB_USER} --password=${DB_PASSWORD} ${DB_NAME} > backup.sql"
mv /data/mysql/log/backup.sql ${DB_OUTPUT}

echo ""
echo "---------- no shit $(date "+%Y-%m-%d %H:%M:%S") ----------"
