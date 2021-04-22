<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   前台控制器基类
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
    
    //效验自定义表单提交时间
    protected function formTime(){
        if(isset($_COOKIE['formtime'])){
            rewrite::error($this->config['form_time'].$this->l['form_time_error']);
        }
    }
    
    //设置自定义表单时间
    protected function setFormTime(){
        setcookie('formtime',1,time() + $this->config['form_time']);
    }
    
    //设置留言板提交时间
    protected function bookTime(){
        if(isset($_COOKIE['booktime'])){
            rewrite::error($GLOBALS['public']['repeatbook'].$this->l['form_time_error']);
        }
    }
    
    //设置自定义表单时间
    protected function setBookTime(){
        setcookie('booktime',1,time() + $GLOBALS['public']['repeatbook']);
    }
    
    //设置搜索间隔时间
    protected function setSearchTime(){
        if($GLOBALS['public']['searchtime'] > 0){
            setcookie('searchtime',1,time() + $GLOBALS['public']['searchtime']);
        }
    }
    
    //验证搜索时间
    protected function searchTime(){
        if($GLOBALS['public']['searchtime'] > 0 && isset($_COOKIE['searchtime'])){
            rewrite::error($this->l['search_time_error'],$GLOBALS['public']['weburl']);
        }
    }
    //登陆状态

	//权限验证
	protected function purview($classid){
	    //判断是否需要用户登录

        if($GLOBALS['allclass'][$classid]['islogin']==1) {
            $this->username = LoginAction::isloginAction();
            if($GLOBALS['allclass'][$classid]['isvip']==1){
                if(!$this->vipPower($this->username)){
                    rewrite::error('会员专属信息，请升级会员！','m=user&a=liveup');
                }
            }
        }


	}
    protected function vipPower($username){
        $usermodel=new UserModel();
        $userdata=$usermodel->getNameUserData($username);
        if($userdata['vip']==1){
            return true;
        }else{
            return false;
        }
    }
}
?>