<?php
namespace X\Service\XAction\Component\WebView;
/**
 * 网页元信息管理器
 * @author Michael Luthor <michaelluthor@163.com>
 */
class MetaManager {
    /**
     * 当前视图的所有元信息
     * @var array
     */
    protected $metas = array(
    /* 'identifier'=> array(
     *      'name'=>'keywords',
     *      'content'=>'KeyWordList',
     *      'charset'=>'utf-8',
     *      'http-equiv'=>'refresh',
     * ), */
    );
    
    /**
     * 设置当前页面的编码格式
     * @param string $charset 字符集名称
     * @return void
     */
    public function setCharset( $charset='UTF-8' ) {
        $content = sprintf('text/html; charset=%s', $charset);
        $this->addMetaData('page.charset', null, $content, null, 'content-type');
    }
    
    /**
     * 对当前页面增加关键字
     * @example $manager->addKeyword("关键字1","关键字2", "关键字3");
     * @param string $keyword 关键字
     * @param string $_ ... 更多关键字
     * @return void
     */
    public function addKeyword( $keyword ) {
        $this->addKeywords(func_get_args());
    }
    
    /**
     * 使用数组的方式对当前页面增加关键字
     * @param array $keywords 关键字列表
     * @return void
     */
    public function addKeywords( $newKeywords ) {
        if ( empty($newKeywords) || !is_array($newKeywords)) {
            return;
        }
        
        $keywords = $this->getAttribute('page.keyword', 'content');
        if ( null === $keywords ) {
            $keywords = array();
        } else {
            $keywords = explode(',', $keywords);
        }
        $keywords = array_merge($keywords, $newKeywords);
        $keywords = array_unique($keywords);
        $keywords = implode(',', $keywords);
        $this->addMetaData('page.keyword', 'keywords', $keywords );
    }
    
    /**
     * 设置在指定秒数后跳转到其他链接.
     * @param string $url 跳转的链接
     * @param integer $seconds 跳转等待时间
     * @return void
     */
    public function refreshTo( $url, $seconds=0 ) {
        $content = sprintf('%d; URL=%s', $seconds, $url);
        $this->addMetaData('page.refresh', null, $content, null, 'refresh');
    }
    
    /**
     * 为当前页面增加作者信息
     * @param string $author 作者信息
     * @return void
     */
    public function addAuthor( $author ) {
        $this->addMetaData('page.author', 'author' , $author);
    }
    
    /**
     * 为当前页面增加描述信息
     * @param string $description 描述内容
     * @return void
     */
    public function addDescription ( $description ) {
        if ( empty($description) ) {
            return;
        }
        $description = Html::HTMLAttributeEncode($description);
        $this->addMetaData('page.description', 'description' , $description);
    }
    
    /**
     * 设置当前页面的过期时间
     * @param int $seconds 过期的秒数
     * @return void
     */
    public function setExpireTime( $seconds) {
        $expires = gmdate("D, d M Y H:i:s", time()+$seconds )." GMT";
        
        header ("Last-Modified: " .gmdate("D, d M Y H:i:s", time() )." GMT");
        header ("Expires: {$expires}");
        header ("Cache-Control: max-age={$seconds}");
        $this->addMetaData('page.expires', null, $expires, null, 'expires');
    }
    
    /**
     * 设置搜索引擎回访频率
     * @param int $time 回访天数
     * @return void
     */
    public function setRevisitAfter( $time = 3 ) {
        $time = ( 1 == $time ) ? '1 Day' : $time.' Days';
        $this->addMetaData('page.revisit.after', 'revisit-after', $time);
    }
    
    /**
     * 设置版权信息
     * @param string $copyright 版权信息
     * @return void
     */
    public function setCopyright( $copyright ) {
        $this->addMetaData('page.copyright', 'Copyright', $copyright);
    }
    
    /**
     * 设置当前页面蜘蛛的处理方式
     * @param string|array $indexMethod 处理方式
     * @param string $robot 蜘蛛类型
     * @return void
     */
    public function setRobots( $indexMethod, $robot='robots') {
        if ( is_array($indexMethod) ) {
            $indexMethod = implode(', ', $indexMethod);
        }
        $this->addMetaData('page.robots',$robot, $indexMethod);
    }
    
    /**
     * 禁止客户端缓存当前页面
     * @return void
     */
    public function disableTheClientCache() {
        $this->addMetaData('page.pragma.no.cach', null, 'No-cach', null, 'Pragma');
    }
    
    /**
     * 增加元数据到当前页面
     * @param string $identifier 名称
     * @param string $name 元数据名称
     * @param string $content 元数据内容
     * @param string $charset 元数据字符集
     * @param string $httpEquiv 属性名称
     * @return void
     */
    public function addMetaData($identifier, $name=null, $content=null, $charset=null, $httpEquiv=null ) {
        $this->metas[$identifier] = array(
            'name'          => $name,
            'content'       => $content,
            'charset'       => $charset,
            'http-equiv'    => $httpEquiv,
        );
    }
    
    /**
     * 使用数组的方式增加元数据
     * @param string $identifier 名称
     * @param array $attributes 属性数组
     * @return void
     */
    public function addMetaByArray( $identifier, $attributes ) {
        $this->metas[$identifier] = $attributes;
    }
    
    /**
     * 通过名称获取元数据信息
     * @param string $identifier 名称
     * @return array|NULL
     */
    public function getMeta( $identifier ) {
        if ( isset($this->metas[$identifier]) ) {
            return $this->metas[$identifier];
        } else {
            return null;
        }
    }
    
    /**
     * 获取指定元数据属性
     * @param string $identifier 名称
     * @param string $attribute 属性名
     * @return NULL|mixed
     */
    public function getAttribute($identifier, $attribute) {
        $meta = $this->getMeta($identifier);
        if ( null===$meta || !isset($meta[$attribute])) {
            return null;
        }
        return $meta[$attribute];
    }
    
    /**
     * 增加 Open Graph 元数据
     * @param unknown $identifier 名称
     * @param unknown $property 属性名
     * @param unknown $content 属性值
     * @return void
     */
    private function addOpenGraphData( $identifier, $property, $content ) {
        $this->metas[$identifier] = array(
            'property'  => $property,
            'content'   => Html::HTMLAttributeEncode($content)
        );
    }
    
    /**
     * 设置 OG 标题
     * @param string $title
     * @return void
     */
    public function setOGTitle( $title ) {
        if ( empty($title) ) {
            return;
        }
        $this->addOpenGraphData('OpenGraph:Title', 'og:title', $title);
    }
    
    /**
     * 设置 OG 类型
     * @param string $type 类型，例如 article,book,movie
     * @return void
     */
    public function setOGType( $type ) {
        if ( empty($type) ) {
            return;
        }
        $this->addOpenGraphData('OpenGraph:Type', 'og:type', $type);
    }
    
    /**
     * 设置 OG 链接
     * @param string $url 链接地址
     * @return void
     */
    public function setOGURL( $url ) {
        if ( empty($url) ) {
            return;
        }
        $this->addOpenGraphData('OpenGraph:URL', 'og:url', $url);
    }
    
    /**
     * 设置 OG 图片
     * @param string $image 图片URL
     * @return void
     */
    public function setOGImage( $image ) {
        if ( empty($image) ) {
            return;
        }
        $this->addOpenGraphData('OpenGraph:Image', 'og:image', $image);
    }
    
    /**
     * 设置 OG 名称
     * @param string $name 名称
     * @return void
     */
    public function setOGSiteName( $name ) {
        if ( empty($name) ) {
            return;
        }
        $this->addOpenGraphData('OpenGraph:SiteName', 'og:site_name', $name);
    }
    
    /**
     * 设置 OG admins 管理帐号
     * @param string $admins 管理帐号
     * @return void
     */
    public function setOGAdmins( $admins, $mark='og:admins' ) {
        $this->addOpenGraphData('OpenGraph:Admins', $mark, $admins);
    }
    
    /**
     * 设置 OG 描述
     * @param string $description 描述
     * @return void
     */
    public function setOGDescription( $description ) {
        if ( empty($description) ) {
            return;
        }
        $this->addOpenGraphData('OpenGraph:Description', 'og:description', $description);
    }
    
    /**
     * 转换元数据信息到适合HTML的字符串
     * @return string
     */
    public function toString() {
        $metaList = array();
        foreach ( $this->metas as $meta ) {
            $meta = array_filter($meta);
            foreach ( $meta as $attribute => $value ) {
                $meta[$attribute] = $attribute.'="'.$value.'"';
            }
            $metaList[] = '<meta '.implode(' ', $meta).' />';
        }
    
        if ( 0 === count($metaList) ) {
            return null;
        }
    
        $metaList = implode("\n", $metaList);
        return $metaList;
    }
}