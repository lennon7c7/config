#!/usr/bin/env php
<?php
/**
 * video cutter: remove first 3s, last 4s, split into 5s clips
 */
set_time_limit(0);

$files = [];
if (!empty($argv[1])) {
  $files[] = $argv[1];
} else {
  $shell = "dir /b";
  $output = [];
  exec($shell, $output);
  foreach ($output as $filename) {
    if (!stristr($filename, '.')) continue;
    if (stristr($filename, '<DIR>')) continue;
    $files[] = $filename;
  }
}

foreach ($files as $filename) {
  if (!isMatchFileExt($filename)) continue;

  $filename_without_ext = pathinfo($filename, PATHINFO_FILENAME);
  $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);

  // 获取原始视频总时长
  $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 \"{$filename}\"";
  $duration = floatval(shell_exec($cmd));

  if ($duration <= 7) {
    echo "跳过 {$filename}，总时长不足7秒\n";
    continue;
  }

  $start = 30;
  $cut_duration = $duration - 30 - (7 * 60);
  $temp_cut_video = "cut_{$filename}";

  // 切掉前3秒和后4秒
  $cmd = "ffmpeg -y -ss {$start} -t {$cut_duration} -i \"{$filename}\" -c copy \"{$temp_cut_video}\"";
  shell_exec($cmd);

  // 再获取处理后的视频时长
  $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 \"{$temp_cut_video}\"";
  $new_duration = floatval(shell_exec($cmd));
  $segment_length = 7 * 60;
  $segments = ceil($new_duration / $segment_length);

  echo "处理文件：{$filename}，切割为 {$segments} 段...\n";

  for ($i = 0; $i < $segments; $i++) {
    $segment_start = $i * $segment_length;
    $output_file = "{$filename_without_ext}_part{$i}.mp4";
    if (file_exists($output_file)) continue;
    $cmd = "ffmpeg -y -ss {$segment_start} -t {$segment_length} -i \"{$temp_cut_video}\" -c copy \"{$output_file}\"";
    shell_exec($cmd);
  }

  unlink($temp_cut_video);
}

/**
 * 判断是否是视频文件
 * @param string $filename
 * @return bool
 */
function isMatchFileExt($filename)
{
  $keep_needle = ['mp4', 'rmvb', 'avi', 'mkv', 'ogv'];
  $filename_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  return in_array($filename_ext, $keep_needle);
}
