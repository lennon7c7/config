rem 加大音频的声音的例子
rem D:\ffmpeg\bin\ffmpeg.exe -i input.mp3 -filter:a "volume = 20dB" output.mp3

call :extract %old%
goto :eof
:extract
rem 获取到文件路径 %~dp1
rem 获取到文件盘符 %~d1
rem 获取到文件名称 %~n1
rem 获取到文件后缀 %~x1

set old=%1
set temp=output%~x1
set new=%~n1%~x1

D:\ffmpeg\bin\ffmpeg.exe -i %old% -filter:a "volume = 20dB" %temp%

del %old%
ren %temp% "%new%"
