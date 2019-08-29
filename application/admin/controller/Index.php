<?php
namespace app\admin\controller;

use think\Db;
use think\Session;
class Index extends AdminBaseController
{

    public function main()
    {
        echo 'admin_index';
        exit(0);
    }

    /**
     * @return array
     */
    public function index()
    {
        $this->initUser();
        $top_menus = [];
        foreach ($this->permList as $key=> $val){
            if($val['pid'] == 0){
                $top_menus[] = $val;
            }
        }
        return $this->fetch('index/index', ['top_menus'=>$top_menus]);
    }

    public function logout(){

    }
}