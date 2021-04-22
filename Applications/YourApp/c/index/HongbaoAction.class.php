<?php
/**
 *  【梦想cms】 http://www.lmxcms.com
 *
 *   前台内容页面控制器
 */
defined('LMXCMS') or exit();

class HongbaoAction extends HomeAction{

    private $reciverModel=null;
    private $hongbaoModel=null;
    private $user=null;
    private $userdata;

    public function __construct(){
        parent::__construct();
        $this->reciverModel=new ReciverModel();
        $this->hongbaoModel=new HongbaoModel();
        $this->user=new UserModel();
        if($_GET['openid']){
            $this->userdata=$this->user->getwxuser($_GET['openid']);
        }

    }
    public function index(){
        set_time_limit(60);
        if(empty($this->userdata)){
            $resutl['msg']='请先登录！';
            $resutl['error']='faild';
            die($resutl);
        }
        $trade_no=$_GET['trade_no'];
        $orderModel=new OrderModel();
        $orderData=$orderModel->getorder($trade_no);
        if(empty($orderData)){
            $resutl['msg']='订单不存在';
            $resutl['error']='faild';
            die($resutl);
        }
        $formid=$orderData['formid'];
        $kouling=$_GET['kouling'];
        $totle=(int)($_GET['money']*100);
        $fei=(int)($_GET['money']*100)*0.02;
        $realpay=$totle+$fei;
        $nums=$_GET['nums'];//红包数量
        if($totle<$nums){
            die;
        }
        if($nums>1){
            $nums_arr=array();

            while (count($nums_arr)<$nums-1){
                $point=rand(1,$totle-1);
                while(in_array($point,$nums_arr)){
                    $point=rand(1,$totle-1);
                }
                $nums_arr[]=$point;
            }
            arsort($nums_arr);
        }else{
            $nums_arr[]=0;
        }
        $maxkey=$totle;
        $money_arr=array();
        foreach($nums_arr as $k=>$value){
            $money_arr[]=$maxkey-$value;
            $maxkey=$value;
        }
        if($nums>1){
            $money_arr[]=$maxkey;
        }

        //金额进行入库
        $data=array();
        $hongbao_data=array();
        $hongbao_data['sender']       =$this->userdata['id'];
        $hongbao_data['kouling']      =$kouling;
        $hongbao_data['nums']         =$nums;
        $hongbao_data['totle_money'] =$totle;
        $hongbao_data['realpay'] =$realpay;
        $hongbao_data['done_money']  =0;
        $hongbao_data['formid']  =$formid;
        $hongbao_data['time']         =time();
        $hongbao_id=$this->hongbaoModel->add($hongbao_data);
        if(!$hongbao_id){
            die('获取红包id失败！');
        }

        $this->user->reduceCash($this->userdata['id'],$orderData['reduce']);
        foreach($money_arr as $k=>$value){
            $data["hb_id"]=$hongbao_id;
            $data["sender"]=$this->userdata['id'];
            $data["reciver_id"]="";
            $data["status"]=0;
            $data["money"]=$value;
            $data["time"]="";
            $this->reciverModel->add($data);
        }
        $temsg=new wxtchat();
        $page="/pages/share/share?id=".$hongbao_id;
        $mgData=$temsg->klSuccess($this->userdata['openid'],$page,$formid,$kouling,time());
        $error=$temsg->PostTemMsg($mgData);
        $resutl['hongbao_id']=$hongbao_id;
        $resutl['error']='success';
        $resutl['msg']=$error;
        die(json_encode($resutl));
    }

    /*
     * $reciverDoneArr 已经领取过的人的id数组
     * $totlereciver
     *
     *
     *
     * */
    public function share(){
        $user=new UserModel();
        $hongbao_id=(int)$_GET['hongbao'];
        $hongbaoData=$this->hongbaoModel->getData($hongbao_id);
        $hongbaoData['totle_money']=$hongbaoData['totle_money']/100;
        $userid=$hongbaoData['sender'];
        $userData=$user->getIdUserData($userid);
        $result['error']='success';
        $result['data']['hongbao']=$hongbaoData;
        $result['data']['sender']=$userData;
        $reciver=new ReciverModel();
        $param['where']="hb_id=$hongbao_id";
        $reciverData=$reciver->getData($param);
        $totleReciver=0;
        $reciverDoneArr=array();
        $DoneMoney=0;
        foreach($reciverData as $k=>$value){
            if($value['status']=='1'){
                if($totleReciver==0){
                    $reciverDoneArr[]=$value['reciver_id'];
                    $DoneMoney+=$value['money'];
                    $totleReciver++;
                }else{
                    $totleReciver++;
                    $DoneMoney+=$value['money'];
                    $reciverDoneArr[]=$value['reciver_id'];
                }
            }
        }
        if(in_array($this->userdata['id'],$reciverDoneArr)){
            $result['recivedone']=1;
        }else{
            $result['recivedone']=0;
        }
        $DoneMoney=$DoneMoney/100;
        $userList=$this->user->getwxUserlist($reciverDoneArr);
        foreach($userList as $k=>$value){
            foreach($reciverData as $t=>$i){
                if($userList[$k]['id']==$reciverData[$t]['reciver_id']){
                    $userList[$k]['reciver_money']=$reciverData[$t]['money']/100;
                    $userList[$k]['recordurl']=$reciverData[$t]['filepath'];
                    $userList[$k]['recordtime']=$reciverData[$t]['recordtime'];
                    $userList[$k]['recordid']=$reciverData[$t]['id'];
                }
            }
        }
        $result['totlereciver']=$totleReciver;
        $result['nums']=$hongbaoData['nums'];
        $result['donemoney']=$DoneMoney;
        $result['else']=$hongbaoData['nums']-$totleReciver;
        $result['data']['reciver']=$userList;
        if($result['else']>0 && $result['recivedone']<1 && time()-$hongbaoData['time']<86400){
            $result['buttonstatus']=1;//可以领取
        }elseif($result['else']>0&&$result['recivedone']>0){
            $result['buttonstatus']=2;//领取过次红包
        }elseif($result['else']<1){
            $result['buttonstatus']=0;//领取完毕
        }elseif(time()-$hongbaoData['time']>86400){
            $result['buttonstatus']=3;//可以领取
        }else{
            $result['buttonstatus']=0;
        }
        echo json_encode($result);
    }
    public function getsharedata(){
        $user=new UserModel();
        $hongbao_id=(int)$_GET['hongbao'];
        $hongbaoData=$this->hongbaoModel->getData($hongbao_id);
        $userid=$hongbaoData['sender'];
        $userData=$user->getIdUserData($userid);
        $result['error']='success';
        $result['data']['hongbao']=$hongbaoData;
        $result['data']['sender']=$userData;
        echo json_encode($result);
    }
    public function reciver(){

        $openid=$_POST['openid'];//领取红包的用户
        $userData=$this->user->getwxuser($openid);//领取红包的用户
        $hongbao_id=(int)$_POST['hongbao'];
        $reciver_id=$userData['id'];//领取红包的用户
        $voicepath=$_POST['voice'];
        $voicetime=(int)$_POST['recordtime'];
        $hongbaoData=$this->hongbaoModel->getData($hongbao_id);
        $formid=$hongbaoData['formid'];
        $postUserId=$hongbaoData['sender'];//发红包的用户id
        $postUserData=$this->user->getIdUserData($postUserId);
        $postUserOpenId=$postUserData['openid'];
        $param['where']="hb_id=$hongbao_id";
        $reciverList=$this->reciverModel->getData($param);
        $reciverDoneList=array();//已经领取过红包的id列表
        foreach($reciverList as $k=>$value){
            if($value['status']==1){
                $reciverDoneList[]=$value['reciver_id'];
            }else{
                $sendData=$value;
            }
        }
        array_unique($reciverDoneList);
        $reciverDoneNums=count($reciverDoneList);//全部领取过的人数
        if($reciver_id<1){
            $this->jsonout("faild","登录状态有误！");
        }
        if(empty($hongbaoData)){
            $this->jsonout("faild","红包不存在");
        }
        if($reciverDoneNums>=$hongbaoData['nums']){
            $this->jsonout("faild","手慢了，被领取完了");
        }
        if(in_array($reciver_id,$reciverDoneList)){
            $this->jsonout("faild","您已经领取过次红包，无法重复领取");
        }

        if($this->reciverModel->updata($this->checkData($reciver_id,$voicepath,$voicetime),$sendData['id'])){
            //领取成功
            if($this->reciverModel->CountSurplus($hongbao_id)<1){
                $temsg=new wxtchat();//发送模板消息
                $page="/pages/share/share?id=".$hongbao_id;
                $mgData=$temsg->klSuccess($postUserOpenId,$page,$formid,mb_substr($hongbaoData['kouling'],0,4,'utf-8'),time());
                $error=$temsg->PostTemMsg($mgData);
            }
            $this->user->addCash($reciver_id,$sendData['money']);
            $this->hongbaoModel->numsadd($hongbao_id);
            $this->jsonout("success","恭喜您！");
        }else{
            $this->jsonout("faild","系统繁忙！");
        }


    }
    public function getdata(){
        //print_r($_GET);
        echo json_encode($this->userdata,true);
    }
    private function jsonout($status,$msg){
        $result['error']="$status";
        $result['msg']=$msg;
        die(json_encode($result));
    }
    private function checkData($reciver_id,$filepath,$vtime){
        $data['recordtime']=$vtime;
        $data['reciver_id']=$reciver_id;
        $data['status']=1;
        $data['filepath']=$filepath;
        $data['time']=time();
        return $data;
    }
    //内容点击次数显示及统计
    public function getewm(){
        $id=(int)$_GET[id];
        $appid="wx51f3cb820fffe980";
        $secret="7c2ed451cf1f5a76da76177aa6f1dd00";
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
        $data=file_get_contents($url);
        $dataArr=json_decode($data,true);
        $codeUrl="https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$dataArr['access_token'];
        $curl=new curl();
        $post['scene']=$id;
        $post['width']="180";
        $post['path']="/pages/share/share?id=".$id;

        $curlData=$curl->wxcurl($codeUrl,"",$post);
        echo $curlData;
    }
}

?>