# AMP for Typecho
 A typecho plugin for Google AMP

开启后对Google的搜索结果更加友好。

之前[直接通过暴力修改模板][1]实现的，结果发现有不少TX需要这个功能，所以整理了一下做成插件，方便使用。

支持MarkDown书写、已设置别名的日志。

## 功能

 自动生成符合Google AMP标准的AMP页面，并与标准页面建立关联。

 自动生成AMP的SiteMap，方便统一提交。


## 安装

将文件夹重命名为`AMP`，然后拷贝至`usr/plugins/`下，最后在后台->插件处安装。

## 升级方法

**请先禁用插件后再升级**

## 使用

到插件后台设置默认LOGO和默认图片，以及选择是否开启SiteMap功能（默认开启）。

 未开启Rewrite功能的，AMP页面为http(s)://xxx/index.php/amp/<slug>/ ， AMPSiteMap页面 http(s)://xxx/index.php/amp_sitemap.xml 

 开启Rewrite功能的，AMP页面 http(s)://xxx/amp/<slug>/ , AMPSiteMap页面 http(s)://xxx/amp_sitemap.xml 




  [1]: https://holmesian.org/typecho-upgrade-AMP