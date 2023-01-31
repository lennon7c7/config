#!/usr/bin/env php
<?php
/**
 * media to video
 */
set_time_limit(0);

$ffmpeg = 'D:\ffmpeg\bin\ffmpeg.exe';
$music_input = '..\mp3\5s.mp3';
$files = [];
if (!empty($argv[1])) {
  // 以参数的方式转
  $files[] = $argv[1];
} else {
  $files = getCurrentFileList();
}


$temp_file = [];
foreach ($files as $filename) {
  if (!isMatchFileExt($filename)) {
    continue;
  }

  $new_filename = covertImage($filename);
  if (empty($new_filename)) {
    continue;
  }

  $temp_file[] = $new_filename;
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

  list($width, $height, $type, $attr) = getimagesize($filename);

  $width_not_divisible_by_2 = false;
  if ($width % 2 != 0) {
    $width_not_divisible_by_2 = true;
  }

  $height_not_divisible_by_2 = false;
  if ($height % 2 != 0) {
    $height_not_divisible_by_2 = true;
  }

  if ($width_not_divisible_by_2 || $height_not_divisible_by_2) {
    if ($width_not_divisible_by_2) {
      $width--;
    }

    if ($height_not_divisible_by_2) {
      $height--;
    }

    resizeImage($filename, $width, $height);
  }

  $file_list[] = [
    'old' => $filename,
    'new' => $temp_filename,
    'width' => $width,
    'height' => $height,
  ];
}


foreach ($file_list as $filename) {
  rename($filename['old'], $filename['new']);
}


list($max_width, $max_height) = getPCRectangle($file_list);
$video_output = "output-{$max_width}x$max_height.mp4";
// scale把原图修改下分辨率，缺少的地方不剪切不拉伸而是加黑边，再把所有处理后的图片二次处理成视频
$shell = "$ffmpeg -framerate 1/1 -start_number 1 -i temp-%1d.jpg -c:v libx264 -r 30 -vf \"scale=" . $max_width . ':' . $max_height . ':force_original_aspect_ratio=decrease,pad=' . $max_width . ':' . $max_height . ':(ow-iw)/2:(oh-ih)/2" -qscale 1 ' . $video_output . ' -y';
$out = [];
exec($shell, $out);

$music_output = "music-$video_output";
$shell = "$ffmpeg -i $video_output -stream_loop -1 -i $music_input -shortest -map 0:v:0 -map 1:a:0 -c:v copy $music_output -y";
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
  $keep_needle = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'];
  foreach ($keep_needle as $ext) {
    $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
    if ($filename_ext === $ext) {
      return true;
    }
  }

  return false;
}

/**
 * 重置图片文件大小
 * @param string $filename 文件名
 * @param string $dst_width 修改后最大宽度
 * @param string $dst_height 修改后最大高度
 * @return void
 */
function resizeImage($filename, $dst_width, $dst_height)
{
  $ext = explode('.', $filename);
  $ext = $ext[count($ext) - 1];

  if ($ext == 'jpg' || $ext == 'jpeg') {
    $src_image = imagecreatefromjpeg($filename);
  } elseif ($ext == 'gif') {
    $src_image = imagecreatefromgif($filename);
  } elseif ($ext == 'png') {
    $src_image = imagecreatefrompng($filename);
  } elseif ($ext == 'bmp') {
    $src_image = imagecreatefrombmp($filename);
  } elseif ($ext == 'webp') {
    $src_image = imagecreatefromwebp($filename);
  }

  if (empty($src_image)) {
    return;
  }

  $src_width = imagesx($src_image);
  $src_height = imagesy($src_image);

  $dst_image = imagecreatetruecolor($dst_width, $dst_height);
  imagecopyresized($dst_image, $src_image, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);

  imagejpeg($dst_image, $filename, 100);
}

/**
 * Convert image to jpeg image
 * @param string $old_filename 文件名
 * @return string
 */
function covertImage($old_filename)
{
  $filename_without_ext = pathinfo($old_filename, PATHINFO_FILENAME);
  $new_filename = "$filename_without_ext.jpg";

  $ext = explode('.', $old_filename);
  $ext = $ext[count($ext) - 1];

  if ($ext == 'jpg') {
    return $old_filename;
  } elseif ($ext == 'jpeg') {
    rename($old_filename, $new_filename);
    return $new_filename;
  } elseif ($ext == 'gif') {
    $src_image = imagecreatefromgif($old_filename);
  } elseif ($ext == 'png') {
    $src_image = imagecreatefrompng($old_filename);
  } elseif ($ext == 'bmp') {
    $src_image = imagecreatefrombmp($old_filename);
  } elseif ($ext == 'webp') {
    $src_image = imagecreatefromwebp($old_filename);
  }

  if (empty($src_image)) {
    return '';
  }

  imagejpeg($src_image, $new_filename, 100);
  unlink($old_filename);

  return $new_filename;
}

/**
 * 获取当前文件列表
 * @return array
 */
function getCurrentFileList()
{
  $files = [];

  // 以当前目录的方式转
  $shell = "dir /b";
  $output = [];
  exec($shell, $output);

  foreach ($output as $filename) {
    $needle = '.';
    if (!stristr($filename, $needle)) {
      continue;
    }

    $needle =
      /** @lang text */
      '<DIR>';
    if (stristr($filename, $needle)) {
      continue;
    }

    $files[] = $filename;
  }

  return $files;
}

/**
 * 获取适用PC的长方形尺寸
 * @param array $file_list
 * @return array
 */
function getPCRectangle($file_list)
{
  $max_width = 2560;
  $max_height = 1440;

  $mix_width = 16;
  $mix_height = 9;

  $width = 160;
  $height = 90;
  foreach ($file_list as $filename) {
    if (empty($filename['width'])) {
      continue;
    }

    if ($filename['width'] > $width) {
      $width = $filename['width'];
      $height = $filename['height'];
    }
  }

  if ($width > $max_width || $height > $max_height) {
    return [$max_width, $max_height];
  } elseif ($width < $mix_width || $height < $mix_height) {
    return [$mix_width, $mix_height];
  } else {
    $height = bcmul(bcdiv($width, $mix_width), $mix_height);
    return [$width, $height];
  }
}

/**
 * 获取适用phone的长方形尺寸
 * @param array $file_list
 * @return array
 */
function getPhoneRectangle($file_list)
{
  $max_width = 1440;
  $max_height = 2560;

  $mix_width = 9;
  $mix_height = 16;

  $width = 90;
  $height = 160;
  foreach ($file_list as $filename) {
    if (empty($filename['height'])) {
      continue;
    }

    if ($filename['height'] > $height) {
      $height = $filename['height'];
      $width = $filename['width'];
    }
  }

  if ($width > $max_width || $height > $max_height) {
    return [$max_width, $max_height];
  } elseif ($width < $mix_width || $height < $mix_height) {
    return [$mix_width, $mix_height];
  } else {
    $width = bcmul(bcdiv($height, $mix_height), $mix_width);
    return [$width, $height];
  }
}
