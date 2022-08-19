#!/bin/bash

# 遇到错误时终止脚本的执行
set -e


echo "---------- 备份数据库，只保留最近7天的 ----------"


# install mysqldump if not found
if [[command -v apk &> /dev/null && ! command -v mysqldump &> /dev/null]]; then
    apk add mysql-client
fi


# 星期几的全称（Sunday - Saturday）
DATE=$(date +%A)

# 以当前目录 设置成 基础目录
BASE_PATH=$(cd "$(dirname "$0")"; pwd)

# 以项目目录名称 设置成 数据库名
DB_NAME=''
DB_HOST=''
DB_USER=''
DB_PASSWORD=''
DB_PORT='3306'
DB_OUTPUT="${BASE_PATH}/${DATE}.sql"

# 备份数据库
/usr/bin/mysqldump --default-character-set=utf8mb4 --skip-lock-tables "${DB_NAME}" --result-file="${DB_OUTPUT}" --host="${DB_HOST}" -u"${DB_USER}" -p"${DB_PASSWORD}" --port="${DB_PORT}"

echo ""
echo "---------- no shit ----------"
