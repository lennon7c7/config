#!/usr/bin/env php
<?php
/**
 * video to mp3
 */
set_time_limit(0);

// 只显示文件名
$shell = "dir /b";
$output = [];
exec($shell, $output);

foreach ($output as $filename) {
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

  $filename = filterFilenameKeyword($filename);
  $filename = filterFilenameSpecialWord($filename);

  $filename_without_ext = pathinfo($filename, PATHINFO_FILENAME);
  $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
  $mp3 = "{$filename_without_ext}.mp3";
  if (file_exists($mp3)) {
    continue;
  }

  $temp_filename = "temp.{$filename_ext}";
  rename($filename, $temp_filename);

  $shell = 'D:\ffmpeg\bin\ffprobe.exe' . " {$temp_filename} -print_format json -show_streams -select_streams a -hide_banner -v quiet";
  $out = [];
  exec($shell, $out);
  $out_json = implode('', $out);
  $out_array = json_decode($out_json, true);
  if (!empty($out_array['streams']) && count($out_array['streams']) > 1) {
    // output all audio if exist
    foreach ($out_array['streams'] as $value) {
      $temp_mp3 = "temp-audio-map{$value['index']}.mp3";
      $mp3 = $filename_without_ext . "-audio-{$value['index']}.mp3";
      if (!isset($value['index']) || empty($value['codec_type']) || $value['codec_type'] != 'audio' || file_exists($mp3)) {
        continue;
      }

      $shell = 'D:\ffmpeg\bin\ffmpeg.exe' . " -i {$temp_filename} -map 0:{$value['index']} -f mp3 -vn {$temp_mp3}";
      $out = [];
      exec($shell, $out);

      rename($temp_mp3, $mp3);
    }
  } else {
    // output default audio
    $temp_mp3 = "temp.mp3";
    $shell = 'D:\ffmpeg\bin\ffmpeg.exe' . " -i {$temp_filename} -vn {$temp_mp3}";
    $out = [];
    exec($shell, $out);

    rename($temp_mp3, $mp3);
  }

  rename($temp_filename, $filename);
}

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

/**
 * 文件名过滤关键字
 * @param string $filename_old
 * @param string $filename_new
 */
function filterFilenameKeyword($filename_old)
{
  $filename_new = $filename_old;

  $filename_new = preg_replace('/\[(.+)\]/', '', $filename_new);

  $filter_keyword = [
    'DVD',
    'BD国粤英语中英双字', 'BD国粤英语', 'BD中英双字幕', 'BD国英双语双字', 'BD中英双字', 'BD中字',
    '中英双字幕', '中英双字',
  ];
  foreach ($filter_keyword as $keyword) {
    $filename_new = str_replace($keyword, '', $filename_new);
  }

  rename($filename_old, $filename_new);

  return $filename_new;
}

/**
 * 文件名过滤前后的特殊符号
 * @param string $filename_old
 * @param string $filename_new
 */
function filterFilenameSpecialWord($filename_old)
{
  $filename_new = $filename_old;

  // 过滤前后的特殊符号
  $filename_without_ext = pathinfo($filename_new, PATHINFO_FILENAME);
  $filename_ext = pathinfo($filename_new, PATHINFO_EXTENSION);

  $filter_keyword = ['`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '+', '=',
    ',', '<', '.', '>', '/', '?', ' '
  ];
  foreach ($filter_keyword as $keyword) {
    $filename_without_ext = trim($filename_without_ext, $keyword);
  }

  $filename_new = "{$filename_without_ext}.{$filename_ext}";

  rename($filename_old, $filename_new);

  return $filename_new;
}
