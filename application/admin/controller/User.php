<?php
namespace app\admin\controller;

use app\admin\model\User as UserModel;
use think\Request;
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
        return $this->fetch('user/index');
    }

    public function role(){
        return $this->fetch("user/role");

    }

    public function perm(){
        return $this->fetch("user/perm");
    }

    public function userAdd()
    {
        return $this->fetch('user/userAdd');
    }


    public function roleperm(){
        return $this->fetch("user/roleperm");

    }

    public function addPerm(Request $request)
    {
        $params = $request->param();
        $perm = $params['perm'];
        $userModel = new UserModel();
        $perm_id = $userModel->addPerm($perm);
        return json(['perm_id'=>$perm_id]);
    }
    public function delPerm()
    {
        $permId = request()->param("perm_id");
        $userModel = new UserModel();
        $userModel->delPerm($permId);
        return json('ok');
    }

    public function permList()
    {
        $userModel = new UserModel();
        $permList = $userModel->getPermList();

        $data = [
            'code'=>0,
            'msg'=>'',
            'count'=>count($permList),
            "is"=> true,
            "tip"=>"操作成功！",
            'data'=>$permList
        ];
        return json($data, 200);

    }

    public function getUserList()
    {
        $userModel = new UserModel();
        $roleList = $userModel->getUserList();
        //$this->ajaxReturn(['code'=>0, 'msg'=>0, 'count'=>count($ret), 'data'=>$ret]);
        $data = [
            'code'=>0,
            'msg'=>0,
            'count'=>count($roleList),
            'data'=>$roleList
        ];
        return json($data, 200);

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