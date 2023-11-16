<?php
/**
 * 下载文件
 * @param string $fileUrl 文件URL
 * @param string $fileName 文件名
 */
function downloadFile($fileUrl, $fileName)
{
  // 设置文件保存路径
  $savePath = './maps/' . $fileName;

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
      echo "    $fileUrl 无法创建本地文件！", PHP_EOL;
    }

    // 关闭文件句柄
    fclose($file);

//    echo '文件下载完成！';
  } else {
    echo "    $fileUrl 无法打开远程文件！", PHP_EOL;
  }
}

$min = 1;
$max = 609;
for ($i = $min; $i <= $max; $i++) {
  if ($i == 1) {
    $page_list_url = 'http://www.feifeishijie.com/fangshou/';
  } else {
    $page_list_url = "http://www.feifeishijie.com/fangshou/index_$i.html";
  }

  echo $page_list_url, PHP_EOL;
  $html = @file_get_contents($page_list_url);
  $needle = '您访问的页面不在';
  if (strpos($html, $needle) !== false) {
    echo $needle, PHP_EOL;
    exit();
  }

  // 创建DOM对象
  $dom = new DOMDocument();
  @$dom->loadHTML($html);

  $elements = $dom->getElementsByTagName('a');

  // 遍历元素
  foreach ($elements as $element) {
    // 获取href属性值和title属性值
    $href = $element->getAttribute('href');
    $title = $element->getAttribute('title');
    // 删除地图下载
    $title = str_replace('地图下载', '', $title);
    $title = str_replace('下载', '', $title);

    echo "  $href $title", PHP_EOL;
    if ($element->getAttribute('class') != 'more' || $href == '' || $title == '') {
//      echo "    $href $title", PHP_EOL;
      continue;
    }

    $id = substr($href, strrpos($href, '/') + 1, strrpos($href, '.') - strrpos($href, '/') - 1);
    if (!is_numeric($id)) {
      echo "    id: $id", PHP_EOL;
      continue;
    }

    // 获取文件URL
    $fileurl = "http://www.feifeishijie.com/e/enews/?enews=DownSoft&classid=63&id=$id&pathid=0&pass=25e9384f38ad5f8a67011a8fce80a563&p=:::";

    // 设置文件名
    $filename = "$id-$title.rar";
    echo "    $filename", PHP_EOL;
    // 执行下载
    downloadFile($fileurl, $filename);
  }
}
