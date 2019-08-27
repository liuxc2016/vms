<?php
namespace app\admin\controller;

use think\Controller;
use think\Config;
class AdminBaseController extends Controller
{
    public function _initialize()
    {
        $this->assign('WEB_ROOT', Config::get("web_url"));
    }
}
