<?php
namespace app\admin\controller;

use think\Controller;
class Index extends Controller
{
    public function _initialize()
    {
        $this->assign('WEB_ROOT', 'http://www.tp50.com/');
    }

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
        return $this->fetch('index/index',['name'=>'thinkphp']);
    }
}
