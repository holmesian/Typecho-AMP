<?php
/**
 * AMP/MIP 插件 for Typecho
 *
 * @package AMP-MIP
 * @author Holmesian
 * @version 0.3.5
 * @link https://holmesian.org
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class AMP_Plugin implements Typecho_Plugin_Interface
{

    public static function activate()
    {
	    Typecho_Plugin::factory('Widget_Archive')->header = array('AMP_Action','headlink');
//        Helper::addRoute('amp_index', '/amp/', 'AMP_Action', 'AMPpage');
	    Helper::addRoute('amp_map', '/amp/[slug]', 'AMP_Action', 'AMPpage');
	    Helper::addRoute('amp_list', '/amp/list/[list_id]', 'AMP_Action', 'AMPlist');
	    Helper::addRoute('mip_map', '/mip/[slug]', 'AMP_Action', 'MIPpage');
        Helper::addRoute('amp_sitemap', '/amp_sitemap.xml', 'AMP_Action', 'ampsitemap');
        Helper::addRoute('mip_sitemap', '/mip_sitemap.xml', 'AMP_Action', 'mipsitemap');
        Helper::addPanel(1, 'AMP/Links.php', 'AMP/MIP自动提交', '自动提交', 'administrator');
        return '请进入设置填写接口调用地址';
    }
	

    public static function deactivate()
    {
        $msg = self::uninstall();
        return $msg . '插件卸载成功';
    }

    public static function index(){
        echo 1;
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
	
	    $element = new Typecho_Widget_Helper_Form_Element_Text('defaultPIC', null,'https://holmesian.org/usr/themes/Holmesian/images/holmesian.png', _t('默认图片地址'), '默认图片地址');
	    $form->addInput($element);
	
	    $element = new Typecho_Widget_Helper_Form_Element_Text('LOGO', null, 'https://holmesian.org/usr/themes/Holmesian/images/holmesian.png' , _t('默认LOGO地址'), '根据AMP的限制，尺寸最大不超过60*60');
	    $form->addInput($element);

        $element = new Typecho_Widget_Helper_Form_Element_Text('baiduAPI', null, '' , _t('接口调用地址'), '请到http://ziyuan.baidu.com/mip/index获取接口调用地址。');
        $form->addInput($element);

        $element = new Typecho_Widget_Helper_Form_Element_Radio('ampSiteMap', array(0 => '不开启', 1 => '开启'), 1, _t('是否开启AMP的SiteMap'),'ampSiteMap地址：'.Helper::options()->index.'/amp_sitemap.xml');
        $form->addInput($element);

        $element = new Typecho_Widget_Helper_Form_Element_Radio('mipSiteMap', array(0 => '不开启', 1 => '开启'), 1, _t('是否开启MIP的SiteMap'),'mipSiteMap地址：'.Helper::options()->index.'/mip_sitemap.xml');
        $form->addInput($element);
   
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }



    public static function uninstall()
    {
        //删除路由、菜单
        Helper::removeRoute('amp_index');
        Helper::removeRoute('amp_map');
        Helper::removeRoute('amp_list');
        Helper::removeRoute('amp_sitemap');
        Helper::removeRoute('mip_map');
        Helper::removeRoute('mip_sitemap');
        Helper::removePanel(1, 'AMP/Links.php');



    }
	
	




}
