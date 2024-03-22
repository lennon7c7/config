#!/bin/bash

# 遇到错误时终止脚本的执行
set -e

echo "---------- MySQL init script for Linux ----------"


echo ""
echo "step1. env init"
parent_dir=$(dirname ${BASH_SOURCE[0]})
container=`basename ${parent_dir}`
echo $parent_dir
mkdir -p "$parent_dir/config"
mkdir -p "$parent_dir/log"
mkdir -p "$parent_dir/data"


echo ""
echo "step2. docker"
result=$( docker ps -a -q -f name="$container" )
if [[ -n "$result" ]]; then
  docker rm -f $container
fi


echo ""
echo "step2. run"
cd $parent_dir || exit
docker run -d --restart=always -p 3306:3306 -v "$parent_dir/log:/data/log" -v "$parent_dir/data:/var/lib/mysql" -v "$parent_dir/config:/etc/mysql/conf.d" --env MYSQL_ROOT_PASSWORD=root --name mysql mysql:8 --character-set-server=utf8mb4 --collation-server=utf8mb4_general_ci


echo ""
echo "---------- no shit $(date "+%Y-%m-%d %H:%M:%S") ----------"
