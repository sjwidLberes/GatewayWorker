<?php /** *  【梦想cms】 http://www.lmxcms.com *  *   前台首页控制器 */
defined('LMXCMS') or exit();
class IndexAction extends HomeAction{
    public function __construct() {
        parent::__construct();
    }
    public function index(){
        echo "首页控制器";
    }

}
?>