<?php
/**
 * 下载文件
 *
 * @param string $fileUrl 文件URL
 * @param string $fileName 文件名
 */
function downloadFile($fileUrl, $fileName)
{
  // 设置文件保存路径
  $savePath = './' . $fileName;

  // 打开远程文件
  $file = fopen($fileUrl, 'rb');

  if ($file) {
    // 创建本地文件
    $saveFile = fopen($savePath, 'wb');

    if ($saveFile) {
      // 从远程文件读取数据并写入本地文件
      while (!feof($file)) {
        fwrite($saveFile, fread($file, 1024 * 8), 1024 * 8);
      }

      // 关闭文件句柄
      fclose($saveFile);
    } else {
      echo '无法创建本地文件！';
    }

    // 关闭文件句柄
    fclose($file);

    echo '文件下载完成！';
  } else {
    echo '无法打开远程文件！';
  }
}

$min = 11110;
$max = 19115;


for ($i = $min; $i <= $max; $i++) {
  // 获取文件URL
  $fileUrl = "http://www.feifeishijie.com/e/enews/?enews=DownSoft&classid=63&id=$i&pathid=0&pass=25e9384f38ad5f8a67011a8fce80a563&p=:::";

// 设置文件名
  $fileName = "$i.rar";
  echo $fileName . PHP_EOL;
// 执行下载
  downloadFile($fileUrl, $fileName);
}

