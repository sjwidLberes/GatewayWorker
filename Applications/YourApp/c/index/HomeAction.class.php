<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   前台控制器基类
 *   次控制器用来判断是否登录
 */
defined('LMXCMS') or exit();
class HomeAction extends Action{
    protected $username;
	protected $l; //语言文字
    protected $loginstate;//用户登录状态
    protected function __construct() {
        parent::__construct();
        global $l;
        $this->l = $l;
    }

}
?>