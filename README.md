# AMP for Typecho
 A typecho plugin for Google AMP/ Baidu MIP

这是款一键生成符合Google AMP/Baidu MIP标准相关页面的插件，开启后可以进一步优化Google、Baidu的搜索结果。

最初本插件的功能是[直接通过暴力修改模板][1]实现的，结果发现有不少TX需要这个功能，所以就整理了一下做成插件，方便有需要的TX使用。

---
## 功能

- 生成符合Google AMP/Baidu MIP标准的AMP/MIP页面，并与标准页面建立关联。

- 生成AMP/MIP的SiteMap，方便爬虫。

- 生成AMP版的首页。
 
- 后台批量提交URL到Baidu。

---
## 安装

将文件夹重命名为`AMP`，然后拷贝至`usr/plugins/`下，最后在后台->插件处安装。

---
## 升级方法

**请先禁用插件后再升级**

PS:0.3版本在路由注册时有一个的错误，可能会导致AMP页面出错。如果AMP页面出现了“Delete”空白页，请先升级到最新版本，**禁用再启用一次插件**进行修复。

---
## 使用说明

在插件后台设置默认LOGO和图片，以及选择是否开启SiteMap、AMP首页等功能即可（默认全开启）。

到[百度站长][2]获取接口调用地址，填写到设置指定位置（使用批量提交URL功能时需要）。

注：
- 服务器未启用php-curl扩展时，后台批量提交URL到Baidu的功能不可用。
- ***非HTTPS站点***受 [amp-list 控件] [3]的src参数限制，AMP首页无法换页，建议关闭生成AMP首页功能。

---

启用Rewrite之后：

AMP首页为 http(s)://xxx/ampindex/

AMP页面为 http(s)://xxx/amp/slug/

MIP页面为 http(s)://xxx/mip/slug/




  [1]: https://holmesian.org/typecho-upgrade-AMP
  [2]: http://ziyuan.baidu.com/mip/index
  [3]: https://www.ampproject.org/docs/reference/components/amp-list
