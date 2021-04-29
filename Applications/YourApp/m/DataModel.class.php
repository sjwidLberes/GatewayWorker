<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   内容模块
 */
defined('LMXCMS') or exit();
class DataModel{
    protected $mmModel=null;
    public function __construct() {
        $this->mmModel=new Memcached("127.0.0.1","11211");
    }
    
    //获取房间信息
    public function getRoomData($roomid){
        $result=$this->mmModel->get($roomid);
        $result=unserialize($result);
        return $result;
    }
    public function setRoomData($roomid,$value=array()){
        $value=serialize($value);
        $this->mmModel->set($roomid,$value);
    }

    //todo 进入房间时候 初始化客户数据
    public function addUserToRoom($uid,$roomid,$Userdata){
        $roomData=$this->getRoomData($roomid);
        if(empty($roomData["alluser"][$uid])){
            $roomData["alluser"][$uid]=$Userdata;
            $this->setRoomData($roomid,$roomData);
            return true;
        }else{
            return false;
        }
    }

    //todo 更新房间内客户数据
    public function updataUserRoomData($uid,$roomid,$Userdata){
        $roomData=$this->getRoomData($roomid);
        if(empty($roomData["alluser"][$uid])){
            return false;
        }else{
            $roomData["alluser"][$uid]=$Userdata;
            $this->setRoomData($roomid,$roomData);
            return true;
        }
    }
    //todo 客户投币后  吧他加入到游戏中
    public function addUserToGame($uid,$roomid){
        $roomData=$this->getRoomData($roomid);
        if(empty($roomData["OnUser"][$uid])){
            $roomData["OnUser"][$uid]["id"]=[$uid];
            $this->setRoomData($roomid,$roomData);
            return true;
        }else{
            return true;
        }
    }
    //todo 客户选择领奖之后 踢出游戏
    public function setUserOutGame($uid,$roomid){
        $roomData=$this->getRoomData($roomid);
        unset($roomData["OnUser"][$uid]);
        $this->setRoomData($roomid,$roomData);
        return true;
    }
    //todo 设置游戏状态
    public function setGameStatus($roomid,$status){
        $roomData=$this->getRoomData($roomid);
        $roomData['status']=$status;
        $this->setRoomData($roomid,$roomData);
        return true;
    }
    //todo 取得游戏状态
    public function getGameStatus($roomid){
        $roomData=$this->getRoomData($roomid);
        return $roomData['status'];
    }
}
?>