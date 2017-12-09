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
//    $options = Helper::options();
    $api = Helper::options()->plugin('AMP')->baiduAPI;

    if (isset($_GET['page'])) {//URL分页
        $page = (int)($_GET['page']);
    } else {
        $page = 1;
    }
    //URL类型
    if ((isset($_GET['type']) and $_GET['type'] == 'amp') OR (isset($_POST['type']) and $_POST['type'] == 'amp')) {
        $sendtype = 'amp';
    } else {
        $sendtype = 'mip';
    }
    $api = preg_replace("/&type=[a-z]+/", "&type={$sendtype}", $api);//替换接口中的类型


    $articles = $db->fetchAll($db->select()->from('table.contents')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.created < ?', $options->gmtTime)
        ->where('table.contents.type = ?', 'post')
        ->page($page, 20)
        ->order('table.contents.created', Typecho_Db::SORT_DESC));

    $urls = array();
    foreach ($articles AS $article) {
        $type = $article['type'];
        $article['categories'] = $db->fetchAll($db->select()->from('table.metas')
            ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
            ->where('table.relationships.cid = ?', $article['cid'])
            ->where('table.metas.type = ?', 'category')
            ->order('table.metas.order', Typecho_Db::SORT_ASC));

        $article['permalink'] = Typecho_Common::url("{$sendtype}/{$article['slug']}", $options->index);
        echo '正在提交:' . $article['permalink'] . '  <br>';
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
//        'https://holmesian.org/mip/come-to-typecho',
//        'https://holmesian.org/mip/nginx-certificate-transparency',
//        'https://holmesian.org/mip/linode-vps-centos-anyconnect',
//        'https://holmesian.org/mip/weixin-browser-cache-disable',
//        'https://holmesian.org/mip/build-nethunter-android-kernel-for-gsm-sniffing',
//        'https://holmesian.org/mip/happy-new-year-2015',);

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

        $obj = json_decode($result, true);
        $name = "success_{$sendtype}";

        if (isset($obj[$name])) {

            echo '<hr>';
            echo "第{$page}页提交成功,";
            $count = $obj["remain_{$sendtype}"];
            echo "还可提交{$count}条URL,准备提交下一页";
            $page += 1;

            ?>
            <script language="JavaScript">
                window.setTimeout("location='<?php $options->adminUrl('extending.php?panel=AMP/Links.php' . "&send=1&type={$sendtype}&page={$page}");
                    ?>'", 2000);
            </script>
            未自动跳转请点击<a
                href="<?php $options->adminUrl('extending.php?panel=AMP/Links.php' . "&send=1&type={$sendtype}&page={$page}"); ?>">这里</a>
            <?php

        } else {
            echo "提交失败";
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
                    <div class="operate">
                        <select name="type">
                            <option value="amp">AMP</option>
                            <option value="mip">MIP</option>
                        </select>
                        <button type="submit" class="btn btn-s"><?php _e('提交到百度'); ?></button>
                    </div>
                </form>

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
