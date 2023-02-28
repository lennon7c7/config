@echo off

rem -------------------------------------------------------------
rem  MySQL init script for Windows.
rem -------------------------------------------------------------


@REM step1: init env
set "_path=%~dp0"
for %%a in ("%_path%") do (set "p_dir=%%~dpa")
for %%a in (%p_dir:~0,-1%) do (set p2_dir=%%~dpa&&set p2_folder=%%~nxa)
for %%a in (%p2_dir:~0,-1%) do (set p3_dir=%%~dpa&&set p3_folder=%%~nxa)
for %%a in (%p3_dir:~0,-1%) do (set p4_dir=%%~dpa&&set p4_folder=%%~nxa)
for %%a in (%p4_dir:~0,-1%) do (set p5_dir=%%~dpa&&set p5_folder=%%~nxa)
for %%a in (%p5_dir:~0,-1%) do (set p6_dir=%%~dpa&&set p6_folder=%%~nxa)


@REM step2: docker delete
docker rm -f "%p2_folder%"


@REM step3: docker start
@cd /d %p_dir%
docker run -d --restart=always -p 3306:3306 -v %p_dir%log:/data/log -v %p_dir%data:/var/lib/mysql -v %p_dir%config:/etc/mysql/conf.d --env MYSQL_ROOT_PASSWORD=root --name mysql mysql:8 --character-set-server=utf8mb4 --collation-server=utf8mb4_general_ci
