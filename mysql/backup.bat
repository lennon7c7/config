@echo off

rem -------------------------------------------------------------
rem  MySQL backup script for Windows.
rem -------------------------------------------------------------


@REM step1: init env
set "_path=%~dp0"
for %%a in ("%_path%") do (set "p_dir=%%~dpa")
for %%a in (%p_dir:~0,-1%) do (set p2_dir=%%~dpa&&set p2_folder=%%~nxa)
for %%a in (%p2_dir:~0,-1%) do (set p3_dir=%%~dpa&&set p3_folder=%%~nxa)
for %%a in (%p3_dir:~0,-1%) do (set p4_dir=%%~dpa&&set p4_folder=%%~nxa)
for %%a in (%p4_dir:~0,-1%) do (set p5_dir=%%~dpa&&set p5_folder=%%~nxa)
for %%a in (%p5_dir:~0,-1%) do (set p6_dir=%%~dpa&&set p6_folder=%%~nxa)


@REM dump file in docker
docker exec -it mysql sh -c "/usr/bin/mysqldump --default-character-set=utf8mb4 -uroot -proot --result-file=/home/temp.sql %p3_folder%"


@REM copy out from docker
set "_file=127-%p3_folder%-%date:~0,4%_%date:~5,2%_%date:~8,2%_%time:~0,2%_%time:~3,2%_%time:~6,2%-dump.sql
docker cp mysql:/home/temp.sql ../backup/%_file%
