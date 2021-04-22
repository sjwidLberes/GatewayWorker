<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   控制器基类(前后台使用)
 */
defined('LMXCMS') or exit();
class Action{
    protected $smarty;
    protected $config;
    protected function __construct(){
        //lcfling   此处编写全局控制器
    }
    public function run(){
        global $Socketdata;
        $a=isset($Socketdata['a']) ? $Socketdata['a'] : 'index';
        if(method_exists($this,$a)){
            eval('$this->'.$a.'();');
        }else{
            //如果方法不存在则执行index方法
            $this->index();
        }
    }

	
}
?>
