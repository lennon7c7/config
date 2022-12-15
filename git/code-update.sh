#!/bin/bash

# 遇到错误时终止脚本的执行
set -e

echo "---------- 更新代码 ----------"
# 无参数的调用例子，默认分支为master
# sh code-update.sh

# 第1个参数是分支的调用例子
# sh code-update.sh lennon-fix

# 第2个参数是强拉的调用例子（当有force push时，普通git pull会失败，所以得用此方法）
# sh code-update.sh lennon-fix 1


echo ""
echo "step1. env init"
# 版本号
SiteVersion=$(date "+%Y%m.%d%H.%M%S")
# 新目录
GitPath="/home/maoshishop"
# 仓库地址（如果未设置，默认取GitPath里面的）
GitClone=""
# 旧分支（如果未设置，默认取GitPath里面的）
GitBranchOld=""
# 目标目录
SourcePath="/home/wwwroot"
# 新分支（如果已设置第一个参数，则取第一个参数）
GitBranchNew="master"
if [[ "$1" != '' ]]; then
  GitBranchNew="$1"
fi


echo ""
echo "step2. git"
if [ ! -d "$GitPath" ]; then
  if [[ "$GitClone" == '' ]]; then
    GitClone=$(git config --get remote.origin.url)
  fi

  cd "${GitPath}/.."
  git clone $GitClone
  git config --global user.email "you@example.com"
  git config --global user.name "Your Name"
  git config --global --add safe.directory $SourcePath
  git config pull.ff only
fi

cd "$GitPath"
git checkout .

if [[ "$2" != '' ]]; then
  # 强拉（当有force push时，普通git pull会失败，所以得用此方法）
  GitBranchOld=$(cd "$SourcePath" && git symbolic-ref --short -q HEAD)
  git checkout master
  git branch -D $GitBranchNew
fi

LocalBranch=$(git symbolic-ref --short HEAD)
if [[ "$LocalBranch" != "$GitBranchNew" ]]; then
  OLD_REMOTE_BRANCH=$(git ls-remote --heads origin $LocalBranch)
  if [[ "$OLD_REMOTE_BRANCH" == "" ]]; then
    git fetch
  else
    git pull
  fi

  git checkout "$GitBranchNew"
fi
  git pull

# 设置不覆盖的文件
rm -f $GitPath"/config/config.php" $GitPath"/config/database.php" $GitPath"/config/extra/site.php" $GitPath"/config/extra/addons.php"
rm -f $GitPath"/addons/cos/config.php"

cp -rf . $SourcePath
chmod -R 777 $SourcePath


echo ""
echo "step3. enable debug"
cd "$SourcePath"
echo $'[app]' > .env
echo $'debug = true' >> .env
echo $'' >> .env
echo $'[database]' >> .env
echo $'debug = true' >> .env
echo $'' >> .env
echo $'[payment]' >> .env
echo $'environment = sandbox' >> .env
echo $'' >> .env
echo $'[error_report]' >> .env
echo $'push_url_exception = IGNORE' >> .env


echo ""
echo "step4. update database"
cd "$SourcePath/database/migrations"
find . -name '*_update_config_version.php' -exec rm {} \;
cd "$SourcePath"
php think migrate:run


echo ""
echo "step5. update version"
sed -i -e "s/'version' => '[0-9]*.[0-9]*.[0-9]*'/'version' => '${SiteVersion}'/g" "./config/extra/site.php"


echo ""
echo "---------- no shit $(date "+%Y-%m-%d %H:%M:%S") ----------"
