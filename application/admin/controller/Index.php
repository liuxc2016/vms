<?php
namespace app\admin\controller;

use think\Db;
use think\Session;
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
        $this->initUser();
        $top_menus = [];
        foreach ($this->permList as $key=> $val){
            if($val['pid'] == 0){
                $top_menus[] = $val;
            }
        }
        return $this->fetch('index/index', ['top_menus'=>$top_menus]);
    }

    public function login()
    {
        if(request()->isPost()){
            $params = request()->param();
            $uname = empty($params['uname'])?'':$params['uname'];
            $upass = empty($params['upass'])?'':$params['upass'];
            $ucode = empty($params['ucode'])?'':$params['ucode'];
            if(empty($uname) or empty($upass)){
                return json(['status'=>0, 'info'=>'登陆失败1！']);
            }

            $user = new User();
            $userInfo = $user->login($uname, $upass);
            if(!$userInfo){
                return json(['status'=>0, 'info'=>'登陆失败2！'.$uname.$upass]);
            }else{
                Session::set("userInfo", $userInfo);
                $this->userInfo = $userInfo;
                $this->permList = $user->getRolePermList($this->userInfo['role_id']);
                Session::set("permList", $this->permList );
                return json(['status'=>1, 'url'=>'/admin/index/index', 'info'=>'login ok!'],200);
            }


        }else{
            return $this->fetch('index/login');
        }
    }

    public function logout(){
        Session::set("userInfo", null);
        Session::set("userInfo", null);
        $this->redirect("/admin/index/login");

    }
}