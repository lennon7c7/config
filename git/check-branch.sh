#!/bin/bash

# 遇到错误时终止脚本的执行
set -e

echo "---------- 查询分支的情况 ----------"


outputMergedBranch () {
  echo '---------- 列出远端分支提交超过14天 and 已合并进主分支（非必要则可以删除该分支，避免分支过多） ----------'
  for branch in $(git branch -r --merged ${main_branch})
  do
    if [[ " -> " = " ${branch} " ]]; then
      continue
    fi

    if [[ " ${filter_branch[@]} " =~ " ${branch} " ]]; then
      continue
    fi

    last_commit_timestamp=$(git log -n 1 ${branch} --pretty=format:"%at")
    if [[ ${last_commit_timestamp} > ${filter_day} ]]; then
      continue
    fi

    echo -e $(git log -n 1 ${branch} --pretty=format:"%cs \t ${branch} \t %an \t %s")
  done
  echo ''
}

outputUnmergedBranch () {
  echo '---------- 列出远端分支提交超过14天 and 未合并进主分支（提醒开发者是否有遗忘合并，或者其它原因。如果使用其它方式合并则可以删除该分支，避免分支过多） ----------'
  for branch in $(git branch -r --no-merged ${main_branch})
  do
    if [[ " ${filter_branch[@]} " =~ " ${branch} " ]]; then
      continue
    fi

    last_commit_timestamp=$(git log -n 1 ${branch} --pretty=format:"%at")
    if [[ ${last_commit_timestamp} > ${filter_day} ]]; then
      continue
    fi

    echo -e $(git log -n 1 ${branch} --pretty=format:"%cs \t ${branch} \t %an \t %s")
  done
  echo ''
}


# 主分支
main_branch='origin/master'
# 待过滤的分支
filter_branch=(${main_branch}, 'origin/HEAD', 'origin/preview' 'origin/lennon-feat' 'origin/lennon-fix' 'origin/lennon-feat-payment-2checkout')
# 待过滤的天数
filter_day=$(($(date +%s) - 7 * 24 * 60 * 60))
outputMergedBranch


# 主分支
main_branch='origin/master'
# 待过滤的分支
filter_branch=('origin/preview' 'origin/lennon-feat' 'origin/lennon-fix' 'origin/lennon-feat-payment-2checkout')
# 待过滤的天数
filter_day=$(($(date +%s) - 7 * 24 * 60 * 60))
outputUnmergedBranch
