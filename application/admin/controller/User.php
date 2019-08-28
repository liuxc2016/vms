<?php
namespace app\admin\controller;

use app\admin\model\User as UserModel;
class User extends AdminBaseController
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
        $userModel = new UserModel();
        $userList = $userModel->getUserList();

        return $this->fetch('index/index',['name'=>'thinkphp']);
    }

    public function role(){
        return $this->fetch("user/role");

    }

    public function roleperm(){
        return $this->fetch("user/roleperm");

    }

    public function getRoleList()
    {
        $userModel = new UserModel();
        $roleList = $userModel->getRoleList();
        //$this->ajaxReturn(['code'=>0, 'msg'=>0, 'count'=>count($ret), 'data'=>$ret]);
        $data = [
          'code'=>0,
          'msg'=>0,
          'count'=>count($roleList),
          'data'=>$roleList
        ];
        return json($data, 200);

    }
}