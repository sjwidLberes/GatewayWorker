<?php 
/**
 *  【梦想cms】 http://www.lmxcms.com
 * 
 *   前台栏目页面控制器
 */
defined('LMXCMS') or exit();
class ListAction extends HomeAction{
    private $classid;
    private $mid;
    public function __construct(){
        parent::__construct();
        $this->classid = (int)$_POST['classid'] ? (int)$_POST['classid'] : (int)$_GET['classid'];
        $this->mid = (int)$_POST['mid'] ? (int)$_POST['mid'] : (int)$_GET['mid'];
        if((!$this->classid || !isset($GLOBALS['allclass'][$this->classid])) && !$this->mid){
            _404();
        }
        if($GLOBALS['allclass'][$this->classid]['classtype'] == 2){
            //外部链接直接跳转
            rewrite::php_url($GLOBALS['allclass'][$this->classid]['classurl']);
        }
    }
    public function index(){
        $temModel = new parse($this->smarty);
		//parent::purview($this->classid);
        if($this->classid) {
            if ($GLOBALS['allclass'][$this->classid]['classtype'] == 0) {
                $model = new ContentModel($this->classid);
            } else if ($GLOBALS['allclass'][$this->classid]['classtype'] == 1) {
                $model = new ColumnModel();
            }
            echo $temModel->lists($this->classid, $model);
        }else if($this->mid){
            $model = new ContentModel('',$this->mid);
            echo $temModel->lists_m($this->mid, $model);
        }
    }
}
?>