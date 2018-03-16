<!DOCTYPE html>
<html mip>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <link rel="stylesheet" type="text/css" href="https://mipcache.bdstatic.com/static/v1/mip.css">
    <link rel="canonical" href="<?php print($MIPpage['permalink']); ?>">
    <title><?php print($MIPpage['title']); ?></title>
    <style mip-custom>body{margin:10px}.middle-text{text-align:center}.notice{background-color:#f5d09a;border:1px solid #e2e2e2;border-left:5px solid #fff000;color:#333;font-size:15px;padding:5px 10px;margin:20px 0}.entry-content{color:#444;font-size:16px;font-family:Arial,'Hiragino Sans GB',冬青黑,'Microsoft YaHei',微软雅黑,SimSun,宋体,Helvetica,Tahoma,'Arial sans-serif';-webkit-font-smoothing:antialiased;line-height:1.8;word-wrap:break-word}.entry-content p{text-indent:2em;margin-top:12px}</style>
    <script type="application/ld+json">
                    {
                        "@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
                        "@id": "<?php print($MIPpage['mipurl']);?>",
                        "appid": "<?php print($MIPpage['APPID']);?>",
                        "title": "<?php print($MIPpage['title']); ?>",
                        "images": [
                            "<?php print($MIPpage['imgData']['url']); ?>"
                            ],
                        "description": "<?php print($MIPpage['desc']);?>",
                        "pubDate": "<?php print($MIPpage['date']->format('Y-m-d\TH:i:s')); ?>",
                        "upDate": "<?php print($MIPpage['modified']); ?>",
                        "lrDate": "<?php print($MIPpage['modified']); ?>",
                        "isOrignal":1
                    }
               </script>
</head>
<body>
<mip-cambrian site-id="<?php print($MIPpage['APPID']);?>"></mip-cambrian>
<header class="header">
    <div class="header-title"><h1><a href="/"><?php print($MIPpage['publisher']);?></a></h1></div>
</header>

<div class="post"><h1 class="middle-text"><?php print($MIPpage['title']); ?></h1>
    <hr>
    <div class="entry-content">
        <?php print($MIPpage['MIPtext']); ?>
    </div>
    <p class="notice">当前页面是本站的「<a href="https://www.mipengine.org/">Baidu MIP</a>」版。查看和发表评论请点击：<a
            href="<?php print($MIPpage['permalink']); ?>">完整版 »</a></p>
    <?php if(!$MIPpage['isMarkdown']){print('<p class="notice">因本文不是用Markdown格式的编辑器书写的，转换的页面可能不符合MIP标准。</p>');} ?>
</div>
<hr>
<!--mip 运行环境-->
<script src="https://mipcache.bdstatic.com/static/v1/mip.js"></script>
<script src="https://mipcache.bdstatic.com/extensions/platform/v1/mip-cambrian/mip-cambrian.js"></script>
</body>
</html>