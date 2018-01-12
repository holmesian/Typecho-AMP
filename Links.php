<?php
error_reporting(E_ALL);
include 'header.php';
include 'menu.php';
date_default_timezone_set('PRC');


if (isset($_GET['send'])) {
    if (false == Typecho_Http_Client::get()) {
        throw new Typecho_Plugin_Exception(_t('对不起, 您的主机不支持 php-curl 扩展而且没有打开 allow_url_fopen 功能, 无法正常使用此功能'));
    }
    $db = Typecho_Db::get();

    //URL分页
    if (isset($_GET['page'])) {
        $page = (int)($_GET['page']);
    } else {
        $page = 1;
    }
    //URL类型
    if ((isset($_GET['type']) and $_GET['type'] == 'amp') OR (isset($_POST['type']) and $_POST['type'] == 'amp')) {
        $sendtype = 'amp';
        $type='amp';
    } elseif((isset($_GET['type']) and $_GET['type'] == 'mip') OR (isset($_POST['type']) and $_POST['type'] == 'mip')) {
        $sendtype = 'mip';
        $type='mip';
    }
    elseif((isset($_GET['type']) and $_GET['type'] == 'batch') OR (isset($_POST['type']) and $_POST['type'] == 'batch')) {
        $sendtype = 'mip';
        $type = 'batch';
        $appid=Helper::options()->plugin('AMP')->baiduAPPID;
        $token=Helper::options()->plugin('AMP')->baiduTOKEN;
        $api= "http://data.zz.baidu.com/urls?appid={$appid}&token={$token}&type=batch";
    }else{
        $sendtype = 'mip';
        $type = 'mip';
    }

    $articles=Typecho_Widget::widget('AMP_Action')->MakeArticleList($sendtype,$page,20);

    //接口类型
    if(empty($api)){
        $api = Helper::options()->plugin('AMP')->baiduAPI;
        $api = preg_replace("/&type=[a-z]+/", "&type={$sendtype}", $api);//替换接口中的类型
    }

    $urls = array();
    foreach ($articles AS $article) {
        echo '正在提交:' . $article['permalink'] . " <br>";
        $urls[] = $article['permalink'];
    }

//    $urls=array('https://holmesian.org/mip/AMP-for-Typecho',
//        'https://holmesian.org/mip/typecho-upgrade-AMP',
//        'https://holmesian.org/mip/PHP7-PHP5-on-CentOS-at-the-same-time',
//        'https://holmesian.org/mip/OCSP-Stapling',
//        'https://holmesian.org/mip/centos-7-config',
//        'https://holmesian.org/mip/remove-letv-boot-ads',
//        'https://holmesian.org/mip/Engineer-continuing-education-answer',
//        'https://holmesian.org/mip/wechat-redenvelop-without-jailbreak',
//        'https://holmesian.org/mip/centos-bbr-congestion-control-algorithm',
//        'https://holmesian.org/mip/Raspberry-Pi-ARM-install-ocserv',
//        'https://holmesian.org/mip/Incorrect-string-value',
//        'https://holmesian.org/mip/KCP-accelerate-SS',
//        'https://holmesian.org/mip/father-io',
//        'https://holmesian.org/mip/Thinkpad-X250-Disassembly-SSD',
//        'https://holmesian.org/mip/come-to-typecho',);

    if (count($urls) > 0) {

        $ch = curl_init();
        $curl_options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);
//    var_dump($result);
//    string '{"remain":4999960,"success":0,"not_valid":[""]}'
//    string '{"success_mip":20,"remain_mip":9980}' (length=36)
//    $result='{"success_amp":20,"remain_amp":9980}';
//string(43) "{"success_batch":20,"remain_batch":4999960}"

        $obj = json_decode($result, true);
        $name = "success_{$type}";

        if (isset($obj[$name])) {

            echo '<hr>';
            echo "第{$page}页提交成功,";
            $count = $obj["remain_{$type}"];
            echo "还可提交{$count}条URL,准备提交下一页>>>";
            $page += 1;

            ?>
            <script language="JavaScript">
                window.setTimeout("location='<?php $options->adminUrl('extending.php?panel=AMP/Links.php' . "&send=1&type={$type}&page={$page}");
                    ?>'", 2000);
            </script>
            未自动跳转请点击<a
                href="<?php $options->adminUrl('extending.php?panel=AMP/Links.php' . "&send=1&type={$type}&page={$page}"); ?>">这里</a>
            <?php

        } else {
            echo "提交失败 -<";
            echo "还可提交{$obj['remain']}条URL";
        }
    } else {
        echo "已全部提交完成";
        ?>
        <script language="JavaScript">
            window.setTimeout("location='<?php $options->adminUrl('extending.php?panel=AMP/Links.php');?>'", 2000);
        </script>
        未自动跳转请点击<a href="<?php $options->adminUrl('extending.php?panel=AMP/Links.php'); ?>">这里</a>
        <?php
    }


} else {
    ?>
    <div class="main">
        <div class="body container">
            <?php include 'page-title.php'; ?>
            <div class="row typecho-page-main" role="main">
                <form action="<?php $options->adminUrl('extending.php?panel=AMP/Links.php&send=1'); ?>" method="POST">
                    <div class="operate" style="text-align: center;">
                        <select name="type" style="width:200px;text-align-last: center;">
                            <option value="amp">AMP</option>
                            <option value="mip">MIP</option>
                            <option value="batch">熊掌号</option>
                        </select>
                        <button type="submit" class="btn btn-s"><?php _e('开始提交'); ?></button>
                    </div>
                </form>
                <div>
                    <p>1.AMP（Accelerated Mobile Pages），是谷歌的一项开放源代码计划，可在移动设备上快速加载的轻便型网页，旨在使网页在移动设备上快速加载并且看起来非常美观。选择该项为自动向百度提交AMP页面地址。</p>
                    <p>2.MIP(Mobile Instant Page - 移动网页加速器)，是一套应用于移动网页的开放性技术标准。通过提供MIP-HTML规范、MIP-JS运行环境以及MIP-Cache页面缓存系统，实现移动网页加速。选择该项为自动向百度提交页面地址。</p>
                    <p>3.熊掌号，是百度熊掌号是内容和服务提供者入驻百度生态的实名账号。通过历史内容接口，每天可提交最多500万条有价值的内容，所提交内容会进入百度搜索统一处理流程。请先设置好APPID和TOKEN后再进行提交。</p>
                </div>

            </div><!-- end .typecho-page-main -->
        </div>
    </div>
    <?php
}
include 'copyright.php';
include 'common-js.php';
include 'table-js.php';
?>
<?php
include 'footer.php';
?>
