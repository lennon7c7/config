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

		$needle =
			/** @lang text */
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
$max_width = 800;
$max_height = 1050;
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

		resize_image($filename, $filename, $width, $height);
	}
	
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
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30  ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -s ' . $max_width . 'x' . $max_height . ' -vf scale=iw:ih ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -s ' . $max_width . 'x' . $max_height . ' -vf scale=iw:ih ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -vf scale=-1:-1 ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -vf scale=1050:1050,setsar=-1:-1 ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -vf scale=w=800:h=1050:force_original_aspect_ratio=decrease ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -vf "scale=800:1050:force_original_aspect_ratio=decrease,pad=800:1050:(ow-iw)/2:(oh-ih)/2" ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -vf "scale=800:1050,pad=800:1050:-1:-1" ' . $output . ' -y';
// $shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -vf scale=iw:ih ' . $output . ' -y';
// scale把原图修改下分辨率，缺少的地方不剪切不拉伸而是加黑边，再把所有处理后的图片二次处理成视频
$shell = 'D:\ffmpeg\bin\ffmpeg.exe -framerate 1/2 -start_number 1 -i "temp-%1d.jpg" -c:v libx264 -r 30 -vf "scale=800:532:force_original_aspect_ratio=decrease,pad=800:532:(ow-iw)/2:(oh-ih)/2" -qscale 1 ' . $output . ' -y';
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
	$keep_needle = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp', 'tiff', 'heic'];
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
 * @param string $filename：文件名。
 * @param string $tmpname：文件路径，如上传中的临时目录。
 * @param string $xmax：修改后最大宽度。
 * @param string $ymax：修改后最大高度。
 */
function resize_image($filename, $tmpname, $xmax, $ymax)
{
	$ext = explode(".", $filename);
	$ext = $ext[count($ext) - 1];

	if ($ext == "jpg" || $ext == "jpeg")
		$im = imagecreatefromjpeg($tmpname);
	elseif ($ext == "png")
		$im = imagecreatefrompng($tmpname);
	elseif ($ext == "gif")
		$im = imagecreatefromgif($tmpname);

	$x = imagesx($im);
	$y = imagesy($im);

	if ($x <= $xmax && $y <= $ymax)
		return $im;

	$im2 = imagecreatetruecolor($xmax, $ymax);
	imagecopyresized($im2, $im, 0, 0, 0, 0, $xmax, $ymax, $x, $y);

	imagejpeg($im2, $filename);
}
