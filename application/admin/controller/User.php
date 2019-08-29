<?php
namespace app\admin\controller;
use think\Request;
use app\admin\model\User as UserModel;
class User extends AdminBaseController
{

    public function main()
    {
        echo '';
        exit(0);
    }

    public function test()
    {
        dump($this->userInfo);
        dump($this->permList);
        die;
    }
    /**
     * @return array
     */
    public function index()
    {
        $top_menus = $this->getTopMenu();
        return $this->fetch('user/user', ['top_menus'=>$top_menus]);
    }

    public function role(){
        return $this->fetch("user/role");

    }

    public function perm(){
        return $this->fetch("user/perm");
    }

    public function getMenuJson(){
        $main_menu = $this->getMenuTree();
        return json(['main_menu'=>$main_menu]);
    }


    public function userAdd()
    {
        if(request()->isPost()){
            $parmas = request()->param();

            if(empty($parmas['uname'])){
                return json([
                    'status'=>1,
                    'msg'=>'fail:提交参数不全！',
                    'insert_id'=>''
                ]);
            }
            $data = [
                'id'=>$parmas['id'],
                'uname'=>$parmas['uname'],
                'upass'=>$parmas['upass'],
                'nickname'=>empty($parmas['nickname'])?$parmas['uname']:$parmas['nickname'],
                'is_delete'=>$parmas['is_delete'],
                'role_id'=>$parmas['role_id'],
                'head_img'=>$parmas['head_img'],
            ];
            $user = new UserModel();
            $ret = $user->addUser($data);
            if($ret){
                return json([
                    'status'=>0,
                    'msg'=>'ok',
                    'insert_id'=>$ret
                ]);
            }else{
                return json([
                    'status'=>1,
                    'msg'=>'fail:'.$user->getError(),
                    'insert_id'=>$ret
                ]);
            }

        }else{
            return $this->fetch('user/userAdd', ['is_edit'=>0]);
        }
    }

    public function userEdit()
    {
        return $this->fetch('user/userAdd', ['is_edit'=>1]);
    }


    public function del()
    {
        if(request()->isPost()){
            $parmas = request()->param();
            $id = $parmas['id'];

            $user = new UserModel();
            $ret = $user->delUser($id);
            if($ret){
                return json([
                    'status'=>0,
                    'msg'=>'ok',
                ]);
            }else{
                return json([
                    'status'=>1,
                    'msg'=>'fail:'.$user->getError(),
                ]);
            }

        }else{
            return '';
        }
    }


    public function roleperm(){
        $roleId = request()->get('role_id');
        return $this->fetch("user/roleperm",['role_id'=>$roleId]);

    }

    public function editPerm()
    {
        if(request()->isPost()){
            $params = request()->param();
            $permInfo = $params;
            $userModel = new UserModel();
            $ret = $userModel->savePerm($permInfo);
            return json(['msg'=>'ok', 'ret'=>$ret]);
        }else{
            $permId = request()->get("perm_id");
            $userModel = new UserModel();
            $permInfo = $userModel->getPerm($permId);
            return $this->fetch("user/editPerm", ['perm_info'=>$permInfo]);
        }
    }

    public function editRolePerm()
    {

        $params = request()->param();
        $roleId = $params['role_id'];
        $permIds = json_decode($params['perm_ids'], true);
        $userModel = new UserModel();
        $ret = $userModel->saveRolePerm($roleId, $permIds);

        return json(['msg'=>'ok', 'ret'=>$ret]);

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

    public function getRolePermList()
    {
        $roleId = request()->get('role_id');
        $userModel = new UserModel();
        //$permList = $userModel->getPermList();
        $rolePerms = $userModel->getRolePermList($roleId);
        $data = [
            'code'=>0,
            'msg'=>'',
            'count'=>count($rolePerms),
            "is"=> true,
            "tip"=>"操作成功！",
            'data'=>$rolePerms
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