# AMP for Typecho
 A typecho plugin for Google AMP/ Baidu MIP

开启后对Google、Baidu的搜索结果更加友好。

之前[直接通过暴力修改模板][1]实现的，结果发现有不少TX需要这个功能，所以整理了一下做成插件，方便使用。


## 功能

- 生成符合Google AMP/Baidu MIP标准的AMP/MIP页面，并与标准页面建立关联。

- 生成AMP/MIP的SiteMap，方便提交。
 
- 后台批量提交URL到Baidu。


## 安装

将文件夹重命名为`AMP`，然后拷贝至`usr/plugins/`下，最后在后台->插件处安装。


## 升级方法

**请先禁用插件后再升级**

PS:0.3版本在路由注册时有一个的错误，可能会导致AMP页面出错。如果AMP页面出现了“Delete”空白页，请升级到0.3.5，**禁用再启用一次插件**进行修复。

## 使用

到插件后台设置默认LOGO和默认图片，以及选择是否开启SiteMap功能（默认开启）。

到[百度站长][2]获取接口调用地址，填写到设置指定位置（使用批量提交URL功能时需要）。

PS：没有开启php-curl扩展的环境可能部分功能不可用。

---

启用Rewrite之后

AMP页面为 http(s)://xxx/amp/slug/

MIP页面为 http(s)://xxx/mip/slug/




  [1]: https://holmesian.org/typecho-upgrade-AMP
  [2]: http://ziyuan.baidu.com/mip/index
