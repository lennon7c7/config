#!/usr/bin/env php
<?php
/**
 * media to video
 */
set_time_limit(0);

$files = [];
if (!empty($argv[1])) {
  // 以参数的方式转
  $files[] = $argv[1];
} else {
  // 以当前目录的方式转
  $shell = "dir /b";
  $output = [];
  exec($shell, $output);

  foreach ($output as $filename) {
    $needle = '.';
    if (!stristr($filename, $needle)) {
      continue;
    }

    $needle = /** @lang text */
      '<DIR>';
    if (stristr($filename, $needle)) {
      continue;
    }

    $files[] = $filename;
  }
}


$temp_file = [];
foreach ($files as $filename) {
  if (!isMatchFileExt($filename)) {
    continue;
  }

  $temp_file[] = $filename;
}

if (empty($temp_file)) {
  return;
}


$file_list = [];
foreach ($temp_file as $key => $filename) {
  $index = $key + 1;
  $filename_without_ext = pathinfo($filename, PATHINFO_FILENAME);
  $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
  $temp_filename = "temp-$index.$filename_ext";
  $type = getimagesize($filename)[3];

  $file_list[] = [
    'old' => $filename,
    'new' => $temp_filename,
    'type' => $type,
  ];
}


foreach ($file_list as $filename) {
  rename($filename['old'], $filename['new']);
}


$output = 'output.mp4';
$shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.png" -c:v libx264 -r 30 ' . $output . ' -y';
$out = [];
exec($shell, $out);


foreach ($file_list as $filename) {
  rename($filename['new'], $filename['old']);
}


/**
 * @param string $filename
 * @return bool
 */
function isMatchFileExt($filename)
{
//  $keep_needle = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp', 'tiff', 'heic'];
  $keep_needle = ['png'];
  foreach ($keep_needle as $ext) {
    $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
    if ($filename_ext === $ext) {
      return true;
    }
  }

  return false;
}
