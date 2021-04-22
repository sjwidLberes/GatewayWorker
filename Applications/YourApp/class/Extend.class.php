<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   系统扩展控制器基类
 */
class Extend extends Action{
    public function __construct() {
        parent::__construct();
        //smarty配置
        $this->smarty->compile_dir = ROOT_PATH.RUN_TYPE.'/'.EXTEND_DIR.'/compile/'; //编译文件路径
        $this->smarty->cache_dir = ROOT_PATH.RUN_TYPE.'/'.EXTEND_DIR.'/cache/'; //缓存目录
    }
}
?>