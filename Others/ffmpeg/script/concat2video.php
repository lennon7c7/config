#!/usr/bin/env php
<?php
/**
 * concat to video
 */
set_time_limit(0);

// 只显示文件名
$shell = "dir /b";
$output = [];
exec($shell, $output);

$filename_array = [];
foreach ($output as $key => $filename) {
  $needle = '.';
  if (!stristr($filename, $needle)) {
    continue;
  }

  $needle = '<DIR>';
  if (stristr($filename, $needle)) {
    continue;
  }

  if (!isMatchFileExt($filename)) {
    continue;
  }

  $filename_array[] = "file '{$filename}'";
}

$txt_list = 'videos.txt';
$data = implode("\n", $filename_array);
file_put_contents($txt_list, $data);

$output = 'output.mp4';
$shell = "ffmpeg -f concat -safe 0 -i {$txt_list} -c copy {$output}";
$out = [];
exec($shell, $out);

$txt_list = 'videos.txt';
@unlink($txt_list);


/**
 * @param string $filename
 * @return bool
 */
function isMatchFileExt($filename)
{
  $keep_needle = ['mp4', 'rmvb', 'avi', 'mkv'];
  foreach ($keep_needle as $ext) {
    $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
    if ($filename_ext === $ext) {
      return true;
    }
  }

  return false;
}
