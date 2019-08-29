<?php
namespace app\admin\controller;

use app\admin\model\User;
use think\Db;

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
        //$users = new User();
        //$userList = $users->getUserList();
        $top_menus = Db::name("perm")->where(['is_delete'=>0, 'pid'=>0])->select();
        return $this->fetch('index/index', ['top_menus'=>$top_menus]);
    }
}