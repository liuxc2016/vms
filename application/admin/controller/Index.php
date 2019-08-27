<?php
namespace app\admin\controller;

use app\admin\model\User;
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
        $users = User::all(['is_delete'=>0])->toArray();
        return $this->fetch('index/index',['name'=>'thinkphp']);
    }
}
