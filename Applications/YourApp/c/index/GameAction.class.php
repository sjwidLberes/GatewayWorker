<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   前台栏目页面控制器
 */
defined('LMXCMS') or exit();
class GameAction extends HomeAction{
    public $SocketData=null;
    public function __construct(){
        parent::__construct();
        global $SocketData;
        if($this->SocketData==null){
            $this->SocketData=$SocketData;
        }
    }

    //用户从列表页面进入游戏房间  判断游戏状态  返回初始化数据给客户端
    public function index(){
        $GameModle=new Game();
        $nowStatus=$GameModle->getNowStatus();
    }

    //用户的投币方法 接收用户的投币数据  返回用户当前所属状态和
    public function play(){

    }

    //游戏中用户领奖方法  领取奖励后  用户处于 禁止投币状态  等待游戏大局重新开始
    public function award(){

    }
    //用户推出房间的方法
    public function outgame(){

    }
}
?>