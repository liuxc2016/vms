<?php

namespace app\admin\model;

use think\Model;
use think\Db;
class User extends Model
{
    protected $resultSetType = 'collection';    //自定义初始化
    private $role_table = "role";
    protected function initialize()
    {
        parent::initialize();
    }

    protected $insert = ["add_time", "last_login_ip"];
    protected function setLastLoginIpAttr(){
        return request()->ip();
    }

    protected  function setAddTimeAttr(){
        return date("Y-m-d H:i:s");
    }

    public function addUser($userData)
    {

        if($userData['id']){
            $userId = $userData['id'];
            unset($userData['id']);
            $ret = Db::name("user")->where("id", $userId)->update($userData);
            if($ret !== false){
                return $userId;
            }else{
                return false;
            }
        }

        $uname = $userData['uname'];
        if(empty($uname)){
            return false;
        }
        $find = Db::name("user")->where('uname', $uname)->find();
        if($find){
            return false;
        }
        $ret = Db::name("user")->insert($userData);
        if($ret){
            return Db::name("user")->getLastInsID();
        }else{
            return false;
        }
    }

    public function delUser($userId)
    {
        if(empty($userId)){
            return false;
        }
        return Db::name("user")->where('id', $userId)->delete();
    }
    public function getUserInfo($userId)
    {
        $list = Db::name("user")->where('id', $userId)->find();
        return $list;
    }

    public function getPerm($perm_id)
    {
        $list = Db::name("perm")->where('id', $perm_id)->find();
        return $list;
    }

    public function delPerm($perm_id)
    {
        $list = Db::name("perm")->where('id', $perm_id)->update(['is_delete'=>1]);
        return $list;
    }

    public function addPerm($permInfo)
    {
        $data = $permInfo;
        $newPermId = Db::name('perm')->insertGetId($data);
        return $newPermId;
    }

    public function savePerm($permInfo)
    {
        $id = $permInfo['id'];
        $data = [
            'name'=>$permInfo['name'],
            'link'=>$permInfo['link'],
            'icon'=>$permInfo['icon'],
        ];
        $ret = Db::name('perm')->where("id", $id)->update($data);
        return $ret;
    }

    public function login($uname, $upass)
    {
        $userInfo = Db::name("user")->where('uname',$uname)
            ->where( 'upass', $upass)
            ->find();
        if(empty($userInfo)){
            return false;
        }elseif ($userInfo['is_delete'] != 0){
            return false;
        }
        return $userInfo;
    }

    public function getUserList($keyWord='', $page=1, $limit=0, $getTotal=0)
    {
        if($keyWord){
            $where["u.uname|u.nickname"] = ["like", "%$keyWord%"];
        }else{
            $where = "1 = 1";
        }

        if($getTotal == 1){
            return Db::name("user")->alias("u")
                ->join('tp_role r','u.role_id = r.id', 'left')
                ->where($where)->count();
        }else{
            $list = Db::name("user")->alias("u")
                ->join('tp_role r','u.role_id = r.id', 'left')
                ->where($where)
                ->field("u.*, r.name as role_name")
                ->limit(($page - 1)*$limit, $limit)
                ->select();
            foreach ($list as $key=>$val){
                unset($list['upass']);
                unset($list['salt']);
            }
            return $list;
        }
    }

    public function getRoleList($keyWord='', $page=1, $limit=0, $getTotal=0){
        if($keyWord){
            $where["name"] = ["like", "%$keyWord%"];
        }else{
            $where = "1 = 1";
        }
        if($getTotal == 1){
            return Db::name("role")
                ->where($where)->count();
        }else{
            $list = Db::name("role")
                ->where($where)
                ->limit(($page - 1)*$limit, $limit)
                ->select();

            return $list;
        }

    }

    /*添加--编辑角色*/
    public function saveRole($userData)
    {

        if($userData['id']){
            $userId = $userData['id'];
            unset($userData['id']);
            $ret = Db::name("role")->where("id", $userId)->update($userData);
            if($ret !== false){
                return $userId;
            }else{
                return false;
            }
        }

        $name = $userData['name'];
        if(empty($name)){
            return false;
        }
        $find = Db::name("role")->where('name', $name)->find();
        if($find){
            return false;
        }
        $ret = Db::name("role")->insert($userData);
        if($ret){
            return Db::name("role")->getLastInsID();
        }else{
            return false;
        }
    }

    public function delRole($roleId)
    {
        if(empty($roleId)){
            return false;
        }

        return Db::name("role")->delete($roleId);
    }


    public function getPermList()
    {
        $list = Db::name("perm")->where(['is_delete'=>0])->select();
        return $list;
    }

    public function getRolePermList($roleId)
    {
        $list = Db::name("perm")->where(['is_delete'=>0])->select();

        $rolePerms = Db::name("role_perm")->where(['role_id'=>$roleId])->column('perm_id');
        foreach ($list as $key => $val){
            if(in_array($val['id'], $rolePerms)){
                $list[$key]['checked'] = true;
                $list[$key]['lay_is_checked'] = true;
            }else{
                $list[$key]['checked'] = false;
                $list[$key]['lay_is_checked'] = false;
            }
        }
        return $list;
    }
    public function saveRolePerm($roleId, $permIds)
    {
        $rolePerms = Db::name("role_perm")->where(['role_id'=>$roleId])->column('perm_id');
        $need_sub = array_diff($rolePerms, $permIds);
        $need_add = array_diff($permIds, $rolePerms);
        if(!empty($need_sub)){
            Db::name("role_perm")->where("role_id = $roleId")->where('perm_id','in',$need_sub)->delete();
        }
        foreach ($need_add as $perm_id){
            $data = [
              "role_id"=>$roleId,
              "perm_id"=>$perm_id,
            ];
            Db::name("role_perm")->insert($data);
        }
        return true;
    }

    public function saveLoginRecord($userInfo, $ip)
    {
        $data['last_login_ip']      = $ip;
        $data['last_login_time']    = date("Y-m-d H:i:s");
        $this->where("id" , $userInfo['id'])->update($data);
    }

}