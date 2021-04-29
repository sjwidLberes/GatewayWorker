<?php /**
 *  【梦想cms】 http://www.lmxcms.com *
 *   荷官操作控制器
 *   不需要验证连接token的  直接继承Action   不在继承HomeAction
 */
defined('LMXCMS') or exit();
class IndexAction extends HomeAction{
    public function __construct() {
        parent::__construct();
    }

    //房间初始化方法 接收荷官的ID  添加游戏开始计时器
    public function index(){
        echo "首页控制器";
    }

    //荷官发送打点数
    public function sendnum(){

    }

    //手动结束游戏
    public function gameend(){

    }
}
?>