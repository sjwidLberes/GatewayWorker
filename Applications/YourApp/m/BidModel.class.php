<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   内容模块
 */
defined('LMXCMS') or exit();
class BidModel extends Model{

    public function __construct($field=array('*')) {
        parent::__construct();
        $this->field = $field;
        $this->tab = array('bid');
    }
    
    //增加信息
    public function add($data){
        $id = parent::addModel($data);
        return $id;
    }
    
    //修改信息
    public function update($data,$id){
        $param['where'] = 'id='.$id;
        parent::updateModel($data,$param);
    }

    //根据id获取信息数据
    public function updateData($id,$tab=false,$isGet=true){
        if($tab) $this->tab = $tab;
        $param['where'] = 'id='.$id;
        $data1 = parent::oneModel($param); //主表数据

    }
    
    //根据信息id判断信息是否存在
    public function is_info($id){
        $param['where'] = 'id='.$id;
        return parent::countModel($param);
    }
    public function count($tid,$uid,$where=array()){
        $param['where'][] = 'tid='.$tid;
        $param['where'][] = 'uid="'.$uid.'"';
        return parent::countModel($param);
    }
    public function countBy($where=array()){
        if($where){
            foreach($where as $v){
                $param['where'][]=$v;
            }
        }
        return parent::countModel($param);
    }
    
    //根据tid获取信息列表
    public function getInfolist($limit,$tid,$where=''){
        $param['order'] = 'id desc';
        $param['limit'] = $limit;
        $param['where'][]='tid='.$tid;
        if($where) $param['where'][] ='bider="'.$where.'"';
        return  parent::selectModel($param);
    }
    public function getInfolistby($where=array()){
        $param['order'] = 'id desc';
        if(!empty($where)) {
            foreach(@$where as $v){
                $param['where'][] = $v;
            }
        }
        return  parent::selectModel($param);
    }
    public function ChoiceBid($bid){
        $param['where'] = "id = '$bid'";
        $data = array(
            'state'=>'2'
        );
        return parent::updateModel($data,$param);
    }
}
?>