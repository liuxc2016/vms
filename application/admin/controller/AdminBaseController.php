<?php
namespace app\admin\controller;

use think\Controller;
use think\Config;
use think\Db;
use think\Session;
use app\admin\model\User as UserModel;

class AdminBaseController extends Controller
{
    protected $beforeActionList = [
        //'initUser' =>  ['except'=>'login'],
    ];

    protected $permList;
    public $userInfo;

    protected function initUser(){
//        if(Session::get("userInfo")){
//            $this->userInfo = Session::get("userInfo");
//            $this->permList = Session::get("permList");
//        }
//
//        if(empty($this->userInfo) or empty($this->userInfo['id']) or empty($this->userInfo['role_id'])){
//            $this->redirect("/admin/index/login");
//        }
        $user = new UserModel();
        $this->userInfo = $user->getUserInfo(2);
        $this->permList = $user->getRolePermList($this->userInfo['role_id']);
    }

    public function _initialize()
    {
        $this->assign('WEB_ROOT', Config::get("web_url"));
        $this->assign('WEB_TITLE', 'tos');
    }

    //获取一级菜单
    protected function getTopMenu(){
        $top_menus = [];
        foreach ($this->permList as $key=>$val){
            if($val['pid'] == 0){
                $top_menus[] = $val;
            }
        }
        return $top_menus;
    }

    protected function getMenuTree(){
        $top_menus = $this->getTopMenu();
        foreach ($top_menus as $top_menu){
            $ret[$top_menu['name']] = $this->getChild($top_menu['id']);
        }
        return $ret;
    }

    private function getChild($pid){
        $d = Db::name("perm")->where(['is_delete'=>0, 'pid'=>$pid])->select();
        $ret = [];
        foreach ($d as $v){
            $m['title'] = $v['title'];
            $m['icon'] = $v['icon'];
            $m['href'] = $v['link'];
            $m['spread'] = false;
            $m['target'] = "";
            if($v['level'] < 3 ){
                $m['children'] = $this->getChild($v['id']);
            }
            $ret[] = $m;
        }
        return $ret;
    }
}
