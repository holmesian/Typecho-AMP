<?php

class AMP_Action extends Typecho_Widget implements Widget_Interface_Do
{
    public function action()
    {

    }


    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->LOGO = Helper::options()->plugin('AMP')->LOGO;
        $this->defaultPIC = Helper::options()->plugin('AMP')->defaultPIC;
        $this->publisher = Helper::options()->title;
        $this->db = Typecho_Db::get();
        $this->baseurl=Helper::options()->index;
        $this->baseurl=str_replace("https://","//",$this->baseurl);
        $this->baseurl=str_replace("http://","//",$this->baseurl);
        
    }


    public static function headlink()
    {
        $widget = Typecho_Widget::widget('Widget_Archive');
        $ampurl = '';
        $mipurl = '';
        $router=explode('/',Helper::options()->routingTable['post']['url']);
        $slug=$router[count($router)-1];
        if(empty($slug)){
            $slug=$router[count($router)-2];
        }
        
        if ($widget->is('index')){
            if (Helper::options()->plugin('AMP')->ampIndex == 1) {
                $fullURL = Typecho_Common::url("ampindex", Helper::options()->index);
                $ampurl = "\n<link rel=\"amphtml\" href=\"{$fullURL}\">\n";
            }
        }
        
        if ($widget->is('post')) {
            $slug = str_replace('[slug]',$widget->request->slug,$slug);
            $slug = str_replace('[cid:digital]',$widget->request->cid,$slug);
            $fullURL = Typecho_Common::url("amp/{$slug}", Helper::options()->index);
            $ampurl = "\n<link rel=\"amphtml\" href=\"{$fullURL}\">\n";
            $fullURL = Typecho_Common::url("mip/{$slug}", Helper::options()->index);
            $mipurl = "<link rel=\"miphtml\" href=\"{$fullURL}\">\n";
        }
        $headurl=$ampurl.$mipurl;

        echo $headurl;
    }


    public function ampsitemap()
    {

        if (Helper::options()->plugin('AMP')->ampSiteMap == 0) {
            die('未开启ampSiteMap功能！');
        }
    
        $this->MakeSiteMap('amp');

    }
    
    public function mipsitemap()
    {
        
        if (Helper::options()->plugin('AMP')->mipSiteMap == 0) {
            die('未开启mipSiteMap功能！');
        }
        
        $this->MakeSiteMap('mip');
        
    }
    
    

    public function MIPpage()
    {
        $this->article = $this->getArticle($this->request->slug);

        if ($this->article['isMarkdown']) {
            ?>
            <!DOCTYPE html>
            <html mip>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
                <link rel="stylesheet" type="text/css" href="https://mipcache.bdstatic.com/static/v1/mip.css">
                <link rel="canonical" href="<?php print($this->article['permalink']); ?>">
                <title><?php print($this->article['title']); ?></title>
                <script src="https://mipcache.bdstatic.com/extensions/platform/v1/mip-cambrian/mip-cambrian.js"></script>
                <style mip-custom>body{margin:10px}.middle-text{text-align:center}.expire-tips{background-color:#f5d09a;border:1px solid #e2e2e2;border-left:5px solid #fff000;color:#333;font-size:15px;padding:5px 10px;margin:20px 0}.entry-content{color:#444;font-size:16px;font-family:Arial,'Hiragino Sans GB',冬青黑,'Microsoft YaHei',微软雅黑,SimSun,宋体,Helvetica,Tahoma,'Arial sans-serif';-webkit-font-smoothing:antialiased;line-height:1.8;word-wrap:break-word}.entry-content p{text-indent:2em;margin-top:12px}</style>
                <script type="application/ld+json">
                    {
                        "@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
                        "@id": "<?php print($this->article['mipurl']);?>",
                        "appid": "<?php print(Helper::options()->plugin('AMP')->baiduAPPID);?>",
                        "title": "<?php print($this->article['title']); ?>",
                        "images": [
                            "<?php print($this->Get_post_img()); ?>"
                            ],
                        "description": "<?php print(mb_substr(str_replace("\r\n", "", strip_tags($this->article['text'])), 0, 150) . "..."); ?>",
                        "pubDate": "<?php print($this->article['date']->format('Y-m-d\TH:i:s')); ?>",
                        "upDate": "<?php print($this->article['date']->format('Y-m-d\TH:i:s')); ?>",
                        "lrDate": "<?php print($this->article['date']->format('Y-m-d\TH:i:s')); ?>",
                        "isOrignal":1
                    }
               </script>
            </head>
            <body>
            <mip-cambrian site-id="<?php print(Helper::options()->plugin('AMP')->baiduAPPID);?>"></mip-cambrian>
            <header class="header">
                <div class="header-title"><h1><a href="/"><?php print($this->publisher);?></a></h1></div>
            </header>

            <div class="post"><h1 class="middle-text"><?php print($this->article['title']); ?></h1>
                <hr>
                <div class="entry-content">
                    <?php print($this->MIPInit($this->article['text'])); ?>
                </div>
                <p class="expire-tips">当前页面是本站的「<a href="https://www.mipengine.org/">Baidu MIP</a>」版。查看和发表评论请点击：<a
                        href="<?php print($this->article['permalink']); ?>#comments">完整版 »</a></p>
            </div>
            <hr>
            <!--mip 运行环境-->
            <script src="https://mipcache.bdstatic.com/static/v1/mip.js"></script>
            </body>
            </html>
            <?php
        } else {
            die('Delete');
        }


    }

    public function AMPlist(){
        if (Helper::options()->plugin('AMP')->ampIndex == 0) {
            die('未开启AMP版首页！');
        }
        $currentPage=$this->request->list_id;
        $articles=$this->MakeArticleList('amp',$currentPage,5);
//        var_dump();
        $article_data=array(
            'pageCount'=>ceil($this->_total/5),
            'currentPage'=>$currentPage,
        );
        $article_data['article']=array();
        foreach($articles as $article){
            if (isset($article['text'])) {
                $article['isMarkdown'] = (0 === strpos($article['text'], '<!--markdown-->'));
                if ($article['isMarkdown']) {
                    $article['text'] = substr($article['text'], 15);
                }
            }
            if($article['isMarkdown']){
                $article['text']=$html = Markdown::convert($article['text']);
            }
            $article_data['article'][]=array(
                'title'=>$article['title'],
                'url'=>$article['permalink'],
                'content'=>$this->substr_format(strip_tags($article['text']),200),
            );
        }
        $arr = array ('items'=>$article_data);
        echo json_encode($arr);

    }

    public function AMPindex(){
        if (Helper::options()->plugin('AMP')->ampIndex == 0) {
            die('未开启AMP版首页！');
        }
        ?>
        <!doctype html>
        <html amp lang="zh">
        <head>
            <meta charset="utf-8">
            <script async src="https://cdn.ampproject.org/v0.js"></script>
            <script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
            <script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.1.js"></script>
            <script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
            <title><?php print($this->publisher." -- AMP Version"); ?></title>
            <link rel="canonical" href="<?php Helper::options()->siteUrl(); ?>"/>
            <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
            <style amp-custom>*{margin:0;padding:0}html,body{height:100%}body{background:#fff;color:#666;font-size:14px;font-family:"-apple-system","Open Sans","HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,sans-serif}::selection,::-moz-selection,::-webkit-selection{background-color:#2479cc;color:#eee}h1{font-size:1.5em}h3{font-size:1.3em}h4{font-size:1.1em}a{color:#2479cc;text-decoration:none}header{background-color:#fff;box-shadow:0 0 40px 0 rgba(0,0,0,0.1);box-sizing:border-box;font-size:14px;height:60px;padding:0 15px;position:absolute;width:100%}header a{color:#333}header h1{font-size:30px;font-weight:400;line-height:30px;margin:15px 0}footer{font-size:.9em;text-align:center;width:auto}.content{padding-top:60px}article{position:relative;padding:30px;border-top:1px solid #fff;border-bottom:1px solid #ddd}.pageinfo{font-size:15px;padding:5px;margin:5px;text-align:center}.info{background-color:#f5d09a;border:1px solid #e2e2e2;border-left:5px solid #fff000;color:#333;font-size:15px;padding:5px 10px;margin:10px 0}.nav{text-align:center;margin-bottom:-25px}.nav button{width:150px;height:25px;margin:auto;margin-bottom:20px;border-width:0;border-radius:3px;background:#1e90ff;cursor:pointer;outline:0;color:white;font-size:16px}button:hover{background:#59f}article a{font-size:2em}article p{position:relative;line-height:2em;font-size:16px;text-indent:2em;padding-top:15px}</style>
            <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
            <noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
        </head>
        <body>
        <header>
            <div class="header-title"><h1><a href="<?php Helper::options()->siteUrl(); ?>"><?php print($this->publisher);?></a></h1></div>
        </header>
        <div></div>
        <div class="content">
            <amp-list width="auto"
                      height="650"
                      layout="fixed-height"
                      src="<?php echo Typecho_Common::url("amp/list/1", $this->baseurl);?>"
                      [src]="'<?php echo Typecho_Common::url("amp/list/", $this->baseurl);?>' + pageNumber"
                      single-item>
                
                <template type="amp-mustache">
                    {{#article}}
                    <article>
                        <a href="{{url}}">{{title}}</a>
                        <div class="article_content"><p>{{content}}</p></div>
                    </article>
                    {{/article}}
                    <p class="pageinfo">Page {{currentPage}} of {{pageCount}} </p>
                </template>
            </amp-list>
        </div>
        <footer>
        <div class="nav">
            <button class="prev"
                    hidden
                    [hidden]="pageNumber < 2"
                    on="tap:
    AMP.setState({
      pageNumber: pageNumber - 1
    })">Previous</button>
            <button class="next"
                    [hidden]="page ? pageNumber >= page.items.pageCount : false"
                    on="tap:
    AMP.setState({
      pageNumber: pageNumber ? pageNumber + 1 : 2
    })">Next</button>
        </div>

        <amp-state id="page"
                   src="<?php echo Typecho_Common::url("amp/list/1", $this->baseurl);?>"
                   [src]="'<?php echo Typecho_Common::url("amp/list/", $this->baseurl);?>' + pageNumber"></amp-state>
            <div><p class="info">当前页面是本站的「<a href="//www.ampproject.org/zh_cn/">Google AMP</a>」版。查看和发表评论请点击：<a
                    href="<?php print($this->baseurl); ?>">完整版 »</a></p></div>
        </footer>
        </body>
        </html>
        <?php

    }

    public function AMPpage()
    {
        $this->article = $this->getArticle($this->request->slug);

        if ($this->article['isMarkdown']) {
            ?>
            <!doctype html>
            <html amp lang="zh">
            <head>
                <meta charset="utf-8">
                <script async src="https://cdn.ampproject.org/v0.js"></script>
                <title><?php print($this->article['title']); ?></title>
                <link rel="canonical" href="<?php print($this->article['permalink']); ?>"/>
                <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
                <script type="application/ld+json">
      {
        "@context": "http://schema.org",
        "@type": "BlogPosting",
        "headline": "<?php print($this->article['title']); ?>",
        "mainEntityOfPage": "<?php print($this->article['permalink']); ?>",
        "author": {
          "@type": "Person",
          "name": "<?php print($this->article['author']); ?>"
        },
        "datePublished": "<?php print($this->article['date']->format('F j, Y')); ?>",
        "dateModified": "<?php print($this->article['date']->format('F j, Y')); ?>",
        "image": {
          "@type": "ImageObject",
          "url": "<?php print($this->Get_post_img()); ?>",
          "width": 700,
          "height": 400
        },
         "publisher": {
          "@type": "Organization",
          "name": "<?php print($this->publisher); ?>",
          "logo": {
            "@type": "ImageObject",
            "url": "<?php print($this->LOGO); ?>",
            "width": 60,
            "height": 60
          }
        },
        "description": "<?php print(mb_substr(str_replace("\r\n", "", strip_tags($this->article['text'])), 0, 150) . "..."); ?>"
      }
                </script>
                <style amp-custom>*{margin:0;padding:0}html,body{height:100%}body{background:#fff;color:#666;font-size:14px;font-family:"-apple-system","Open Sans","HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,sans-serif}::selection,::-moz-selection,::-webkit-selection{background-color:#2479CC;color:#eee}h1{font-size:1.5em}h3{font-size:1.3em}h4{font-size:1.1em}a{color:#2479CC;text-decoration:none}article{padding:85px 15px 0}article .entry-content{color:#444;font-size:16px;font-family:Arial,'Hiragino Sans GB',冬青黑,'Microsoft YaHei',微软雅黑,SimSun,宋体,Helvetica,Tahoma,'Arial sans-serif';-webkit-font-smoothing:antialiased;line-height:1.8;word-wrap:break-word}article h1.title{color:#333;font-size:2em;font-weight:300;line-height:35px;margin-bottom:25px}article .entry-content p{margin-top:15px;text-indent: 2em;}article h1.title a{color:#333;transition:color .3s}article h1.title a:hover{color:#2479CC}article blockquote{background-color:#f8f8f8;border-left:5px solid #2479CC;margin-top:10px;overflow:hidden;padding:15px 20px}article code{background-color:#eee;border-radius:5px;font-family:Consolas,Monaco,'Andale Mono',monospace;font-size:80%;margin:0 2px;padding:4px 5px;vertical-align:middle}article pre{background-color:#f8f8f8;border-left:5px solid #ccc;color:#5d6a6a;font-size:14px;line-height:1.6;overflow:hidden;padding:0.6em;position:relative;white-space:pre-wrap;word-break:break-word;word-wrap:break-word}article table{border:0;border-collapse:collapse;border-spacing:0}article pre code{background-color:transparent;border-radius:0 0 0 0;border:0;display:block;font-size:100%;margin:0;padding:0;position:relative}article table th,article table td{border:0}article table th{border-bottom:2px solid #848484;padding:6px 20px;text-align:left}article table td{border-bottom:1px solid #d0d0d0;padding:6px 20px}article .copyright-info,article .amp-info{font-size:14px}article .expire-tips{background-color:#f5d09a;border:1px solid #e2e2e2;border-left:5px solid #fff000;color:#333;font-size:15px;padding:5px 10px;margin:20px 0px}article .post-info,article .entry-content .date{font-size:14px}article .entry-content blockquote,article .entry-content ul,article .entry-content ol,article .entry-content dl,article .entry-content table,article .entry-content h1,article .entry-content h2,article .entry-content h3,article .entry-content h4,article .entry-content h5,article .entry-content h6,article .entry-content pre{margin-top:15px}article pre b.name{color:#eee;font-family:"Consolas","Liberation Mono",Courier,monospace;font-size:60px;line-height:1;pointer-events:none;position:absolute;right:10px;top:10px}article .entry-content .date{color:#999}article .entry-content ul ul,article .entry-content ul ol,article .entry-content ul dl,article .entry-content ol ul,article .entry-content ol ol,article .entry-content ol dl,article .entry-content dl ul,article .entry-content dl ol,article .entry-content dl dl,article .entry-content blockquote > p:first-of-type{margin-top:0}article .entry-content ul,article .entry-content ol,article .entry-content dl{margin-left:25px}.header{background-color:#fff;box-shadow:0 0 40px 0 rgba(0,0,0,0.1);box-sizing:border-box;font-size:14px;height:60px;padding:0 15px;position:absolute;width:100%}.footer{font-size:.9em;padding:15px 0 25px;text-align:center;width:auto}.header h1{font-size:30px;font-weight:400;line-height:30px;margin:15px 0px}.menu-list li a,.menu-list li span{border-bottom:solid 1px #ededed;color:#000;display:block;font-size:18px;height:60px;line-height:60px;text-align:center;width:86px}.header h1 a{color:#333}.tex .hljs-formula{background:#eee8d5}</style>
                <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
                <noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
            </head>
            <body>
            <header class="header">
                <div class="header-title"><h1><a href="<?php print(Typecho_Common::url("ampindex/", $this->baseurl)); ?>"><?php print($this->publisher);?></a></h1></div>
            </header>

            <article class="post"><h1 class="title"><?php print($this->article['title']); ?></h1>
                <div class="entry-content">
                    <?php print($this->AMPInit($this->article['text'])); ?>
                </div>
                <p class="expire-tips">当前页面是本站的「<a href="//www.ampproject.org/zh_cn/">Google AMP</a>」版。查看和发表评论请点击：<a
                        href="<?php print($this->article['permalink']); ?>#comments">完整版 »</a></p>
            </article>

            </body>
            </html>
            <?php
        } else {
            die('Delete');
        }
    }

    public function sendRealtime($contents, $class){
        //如果文章属性为隐藏或滞后发布
        if ('publish' != $contents['visibility'] || $contents['created'] > time()) {
            return;
        }

        //如果没有开启自动提交功能
        if (Helper::options()->plugin('AMP')->mipAutoSubmit == 0) {
            return;
        }

        //获取系统配置
        $options = Helper::options();

        //判断是否配置相关信息
        if (is_null($options->plugin('AMP')->baiduAPPID) or is_null($options->plugin('AMP')->baiduTOKEN) ) {
            throw new Typecho_Plugin_Exception(_t('参数未正确配置'));
        }
        $appid=$options->plugin('AMP')->baiduAPPID;
        $token=$options->plugin('AMP')->baiduTOKEN;
        $api= "http://data.zz.baidu.com/urls?appid={$appid}&token={$token}&type=batch";

        $article=Typecho_Widget::widget('AMP_Action')->getArticleByCid($class->cid);



        $urls=array($article['mipurl'],);

        try {
            //为了保证成功调用，先做判断
            if (false == Typecho_Http_Client::get()) {
                throw new Typecho_Plugin_Exception(_t('对不起, 您的主机不支持 php-curl 扩展而且没有打开 allow_url_fopen 功能, 无法正常使用此功能'));
            }

            //发送请求
            $http = Typecho_Http_Client::get();
            $http->setData(implode("\n", $urls));
            $http->setHeader('Content-Type', 'text/plain');
            $json = $http->send($api);
            $return = json_decode($json, 1);


        } catch (Typecho_Exception $e) {
            throw new Typecho_Plugin_Exception(_t('出现错误:'.$e->getMessage()));
        }


    }

    public function getArticle($slug)
    {
        $tempslug=explode('.',$slug)[0];
        if(preg_match("/^\d*$/",$tempslug)) {
            $cid=$tempslug;
            $article=$this->getArticleByCid($cid);
        }else{
            $slug=$tempslug;
            $article=$this->getArticleBySlug($slug);
        }
        return $article;

    }

    private function getArticleBySlug($slug){
        $select = $this->db->select()->from('table.contents')
            ->where('slug = ?', $slug);
        $article = $this->ArticleBase($select);
        return $article;
    }

    private function getArticleByCid($cid){
        $select = $this->db->select()->from('table.contents')
            ->where('cid = ?', $cid);
        $article = $this->ArticleBase($select);
        return $article;
    }

    private function ArticleBase($select){
        $article_src = $this->db->fetchRow($select);

        if (count($article_src) > 0) {
            $article = Typecho_Widget::widget("Widget_Abstract_Contents")->push($article_src);
            $select = $this->db->select('table.users.screenName')
                ->from('table.users')
                ->where('uid = ?', $article['authorId']);
            $author = $this->db->fetchRow($select);
            $article['author'] = $author['screenName'];
            $article['text'] = Markdown::convert($article['text']);

            $router=explode('/',Helper::options()->routingTable['post']['url']);
            $slugtemp=$router[count($router)-1];
            if(empty($slugtemp)){
                $slugtemp=$router[count($router)-2];
            }
            $slug = str_replace('[slug]',$article['slug'],$slugtemp);
            $slug = str_replace('[cid:digital]',$article['cid'],$slug);
            $article['mipurl'] = Typecho_Common::url("mip/{$slug}", Helper::options()->index);;
        } else {
            $article = array('isMarkdown' => false);
        }
        return $article;
    }


    public function MakeArticleList($linkType='amp',$page=0,$pageSize=0){
        $db = Typecho_Db::get();
        $sql=$db->select()->from('table.contents')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', 'post')
            ->order('table.contents.created', Typecho_Db::SORT_DESC);
        if($page>0 and $pageSize>0){
            $countSql = clone $sql;
            $this->_total = Typecho_Widget::widget('Widget_Abstract_Contents')->size($countSql);
            $sql=$sql->page($page,$pageSize);
        }
        $articles = $db->fetchAll($sql);

        $router=explode('/',Helper::options()->routingTable['post']['url']);
        $slugtemp=$router[count($router)-1];
        if(empty($slugtemp)){
            $slugtemp=$router[count($router)-2];
        }
        $articleList=array();
        foreach ($articles AS $article) {
            $article['categories'] = $db->fetchAll($db->select()->from('table.metas')
                ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
                ->where('table.relationships.cid = ?', $article['cid'])
                ->where('table.metas.type = ?', 'category')
                ->order('table.metas.order', Typecho_Db::SORT_ASC));
            $article['category'] = urlencode(current(Typecho_Common::arrayFlatten($article['categories'], 'slug')));
            $article['slug'] = urlencode($article['slug']);
            $article['date'] = new Typecho_Date($article['created']);
            $article['year'] = $article['date']->year;
            $article['month'] = $article['date']->month;
            $article['day'] = $article['date']->day;

            $slug = str_replace('[slug]',$article['slug'],$slugtemp);
            $slug = str_replace('[cid:digital]',$article['cid'],$slug);
            if($linkType=='mip'){
                $article['permalink'] = Typecho_Common::url("mip/{$slug}", Helper::options()->index);
            }else{
                $article['permalink'] = Typecho_Common::url("amp/{$slug}", Helper::options()->index);
            }
            $articleList[]=$article;
        }
        return $articleList;
    }


    private function Get_post_img()
    {
        $text = $this->article['text'];

        $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
        $patternMD = '/\!\[.*?\]\((http(s)?:\/\/.*?(jpg|png))/i';
        $patternMDfoot = '/\[.*?\]:\s*(http(s)?:\/\/.*?(jpg|png))/i';
        if (preg_match($patternMDfoot, $text, $img)) {
            $img_url = $img[1];
        } else if (preg_match($patternMD, $text, $img)) {
            $img_url = $img[1];
        } else if (preg_match($pattern, $text, $img)) {
            preg_match("/(?:\()(.*)(?:\))/i", $img[0], $result);
            $img_url = $img[1];
        } else {
            $img_url = $this->defaultPIC;
        }
        return $img_url;

    }

    private function MIPInit($text)
    {
    	$text=$this->IMGsize($text);
        $text = str_replace('<img', '<mip-img  layout="responsive" ', $text);
        $text = str_replace('img>', 'mip-img>', $text);
        $text = str_replace('<!- toc end ->', '', $text);
        $text = str_replace('javascript:content_index_toggleToc()', '#', $text);
        return $text;
    }

    private function AMPInit($text)
    {
		$text=$this->IMGsize($text);
        $text = str_replace('<img', '<amp-img  layout="responsive" ', $text);
        $text = str_replace('img>', 'amp-img>', $text);
        $text = str_replace('<!- toc end ->', '', $text);
        $text = str_replace('javascript:content_index_toggleToc()', '#', $text);
        return $text;
    }

    private function IMGsize($html){
		$html = preg_replace_callback(
			'(<img src="(.*?)")',
			function ($m) {
                if(isset(parse_url($m[1])['host'])){//Fix 相对路径与绝对路径附件的问题
                    if( parse_url($m[1])['host'] == parse_url(Helper::options()->siteUrl)['host'] ){
                        $url=$_SERVER['DOCUMENT_ROOT'].parse_url($m[1])['path'];
                    }else{
                        $url = $m[1];
                    }
                }else{
                    $url =$_SERVER['DOCUMENT_ROOT'].$m[1];
                }
                list($width, $height, $type, $attr) =getimagesize($url);
				if(!isset($width)){$width='500';}
				if(!isset($height)){$height='700';}
				return "<img width=\"{$width}\" height=\"{$height}\" src=\"{$m[1]}\"";
			},
			$html
		);
		return $html;
	}

    private function MakeSiteMap($maptype='amp'){
        //changefreq -> always、hourly、daily、weekly、monthly、yearly、never
        //priority -> 0.0优先级最低、1.0最高
        $root_url=Helper::options()->rootUrl;
        header("Content-Type: application/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";
        echo "\t<url>\n";
        echo "\t\t<loc>{$root_url}</loc>\n";
        echo "\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n";
        echo "\t\t<changefreq>daily</changefreq>\n";
        echo "\t\t<priority>1</priority>\n";
        echo "\t</url>\n";
        $articles=$this->MakeArticleList($maptype);
        foreach ($articles AS $article) {
            echo "\t<url>\n";
            echo "\t\t<loc>" . $article['permalink'] . "</loc>\n";
            echo "\t\t<lastmod>" . date('Y-m-d', $article['modified']) . "</lastmod>\n";
            echo "\t\t<changefreq>monthly</changefreq>\n";
            echo "\t\t<priority>0.5</priority>\n";
            echo "\t</url>\n";
        }
        echo "</urlset>";
        
    }
    
    
    private function substr_format($text, $length, $replace='...', $encoding='UTF-8')
    {
        if ($text && mb_strlen($text, $encoding)>$length)
        {
            return mb_substr($text, 0, $length, $encoding).$replace;
        }
        return $text;
    }
    
}

?>