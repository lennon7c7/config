#!/usr/bin/env php
<?php
/**
 * video cutter: remove first $start seconds, last $end seconds, split into $segment_length seconds clips
 * Usage: php video_cutter.php [video_file] [start_seconds] [end_seconds] [segment_length_seconds]
 * If no video_file given, process all video files in current dir.
 */

set_time_limit(0);

$start = isset($argv[2]) ? intval($argv[2]) : 3;        // 默认去掉前3秒
$end = isset($argv[3]) ? intval($argv[3]) : 4;          // 默认去掉后4秒
$segment_length = isset($argv[4]) ? intval($argv[4]) : 0; // 默认每段5秒

if ($start < 0 || $end < 0) {
  echo "参数错误：start, end 必须 >= 0\n";
  exit(1);
}

echo "参数：去掉前{$start}秒\n";
echo "参数：去掉后{$end}秒\n";
echo "参数：每段{$segment_length}秒\n\n";

// 获取文件列表
$files = [];
if (!empty($argv[1]) && is_file($argv[1])) {
  $files[] = $argv[1];
} else {
  // 扫描当前目录所有支持的视频文件
  $dirFiles = scandir(getcwd());
  foreach ($dirFiles as $file) {
    if (is_file($file) && isVideoFile($file)) {
      $files[] = $file;
    }
  }
}

if (empty($files)) {
  echo "未找到待处理的视频文件\n";
  exit(0);
}

foreach ($files as $file) {
  echo "处理文件：{$file}\n";

  // 获取文件名
  $file_name_without_ext = pathinfo($file, PATHINFO_FILENAME);

  // 获取文件后缀名
  $file_ext = pathinfo($file, PATHINFO_EXTENSION);

  // 获取视频总时长
  $duration = getVideoDuration($file);
  if ($duration === false) {
    echo "无法获取视频时长，跳过\n";
    continue;
  }

  echo "首次视频时长 {$duration}s\n";
  if ($duration <= $start + $end + 1) { // 额外留1秒容错
    echo "视频时长 {$duration}s 不足以剪辑，跳过\n";
    continue;
  }

  $cut_duration = $duration - $start - $end;
  $temp_cut_video = "{$file_name_without_ext}_cut.$file_ext";

  // 剪掉前后指定时长，重新编码拷贝
  $cmd = "ffmpeg -y -ss {$start} -t {$cut_duration} -i " . escapeshellarg($file) . " -c copy " . escapeshellarg($temp_cut_video) . " 2>&1";
  exec($cmd, $output, $ret);
  if ($ret !== 0) {
    print_r($output);
    echo "剪辑失败，跳过\n";
    continue;
  }
  echo "处理文件：{$file}\n";

  // 获取剪辑后视频时长
  $new_duration = getVideoDuration($temp_cut_video);
  if ($new_duration === false) {
    echo "无法获取剪辑后视频时长，跳过\n";
    continue;
  }
  echo "剪辑后视频: $temp_cut_video  {$new_duration}s\n";

  if ($segment_length > 0) {
    $segments = ceil($new_duration / $segment_length);
    echo "切割为 {$segments} 段，每段 {$segment_length} 秒\n";

    $filename_without_ext = pathinfo($file, PATHINFO_FILENAME);

    for ($i = 0; $i < $segments; $i++) {
      $segment_start = $i * $segment_length;
      // 处理最后一段可能不满segment_length
      $segment_time = ($segment_start + $segment_length > $new_duration) ? ($new_duration - $segment_start) : $segment_length;
      if ($segment_time <= 0) break;

      $output_file = "{$filename_without_ext}_part{$i}.$file_ext";
      if (file_exists($output_file)) {
        echo "已存在，跳过 {$output_file}\n";
        continue;
      }

      $cmd = "ffmpeg -y -ss {$segment_start} -t {$segment_time} -i " . escapeshellarg($temp_cut_video) . " -c copy " . escapeshellarg($output_file) . " 2>&1";
      exec($cmd, $out, $ret2);
      if ($ret2 !== 0) {
        echo "生成片段失败：{$output_file}\n";
        continue;
      }

      $duration = getVideoDuration($output_file);
      echo "生成片段：{$output_file} {$duration}s\n";
    }
  }
}

echo "全部处理完成\n";


function getVideoDuration(string $filename)
{
  $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filename);
  $output = shell_exec($cmd);
  if ($output === null) return false;
  $duration = intval(trim($output));
  return $duration > 0 ? $duration : false;
}

function isVideoFile(string $filename): bool
{
  $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  $allow = ['mp4', 'rmvb', 'avi', 'mkv', 'ogv'];
  return in_array($ext, $allow);
}
