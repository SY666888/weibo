<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    //构造函数，使用 Auth 中间件来验证用户的身份
    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>['show','create','store','index','confirmEmail']  //except黑名单过滤机制，only白名单方法
        ]);
        // 只允许未登录用户访问guest
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //用户创建
   public function  create()
   {
       return view('users.create');
   }
    public function show(User $user)
    {
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(2);
        return view('users.show', compact('user', 'statuses'));

    }
    //用户创建提交处理
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),  #使用的bcrypt方法加密的密码
        ]);
        //Auth::login($user);//用户注册后自动登录
        $this->sendEmailConfirmationTo($user);//用户注册后发邮件激活
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

    }
    //用户编辑 edit
    public  function  edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    //用户编辑提交处理
    public  function  update(User $user,Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6',
        ]);
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','个人资料更新成功！');
        return redirect()->route('users.show',$user->id);

    }
    //用户列表
    public function index()
    {
      $users=User::paginate(2);
      return view('users.index',compact('users'));
    }
    //用户删除操作
    public function destroy(User $user)
    {
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
    //发邮件激活
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(1);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(1);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }


}
