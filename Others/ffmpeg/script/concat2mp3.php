#!/usr/bin/env php
<?php
/**
 * concat to mp3
 */
set_time_limit(0);

// 只显示文件名
$shell = "dir /b";
$output = [];
exec($shell, $output);

$filename_array = [];
foreach ($output as $filename) {
  $needle = '.';
  if (!stristr($filename, $needle)) {
    continue;
  }

  $needle = '<DIR>';
  if (stristr($filename, $needle)) {
    continue;
  }

  $filename_without_ext = pathinfo($filename, PATHINFO_FILENAME);
  $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
  $split_array = explode('_', $filename_without_ext);
  $last = array_pop($split_array);
  if ($last < 1) {
    continue;
  }

  $filename_array[implode('_', $split_array)][] = $last;
}

foreach ($filename_array as $filename => $value) {
  $output_mp3 = "{$filename}.mp3";
  if (count($value) == 1 || file_exists($output_mp3)) {
    continue;
  }

  $concat_param_string = [];
  foreach ($value as $value2) {
    $concat_param_string[] = "{$filename}_{$value2}.mp3";
  }
  $concat_param_string = 'concat:' . implode('|', $concat_param_string);

  $shell = 'ffmpeg' . " -i \"{$concat_param_string}\" -acodec copy {$output_mp3}";
  $out = [];
  exec($shell, $out);
}

/**
 * @param string $filename
 * @return bool
 */
function isMatchFileExt($filename)
{
  $keep_needle = ['.mp3'];
  foreach ($keep_needle as $ext) {
    if (stristr($filename, $ext)) {
      return true;
    }
  }

  return false;
}
