<?php
/**
 * Created by PhpStorm.
 * User: VNM05YPG
 * Date: 3/27/2017
 * Time: 11:26 AM
 */

namespace App\Http\Controllers\Auth;


use App\Constants\AdminWeb\CommonConstant;
use App\Helpers\AdminWeb\LoginOtp;
use App\Helpers\VoucherHelp;
use App\Http\Controllers\Controller;
use App\Interfaces\AdminInterface;
use App\Models\Biz\AdminActivityLog;
use App\Services\AdminWeb\BruteForceAttempt;
use Carbon\Carbon;
use Illuminate\Encryption\Encrypter;
use App\Services\Configure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Helpers\SignatureHelper;

class AdminLoginController extends Controller
{
    private $admin;
    protected $configService;
    const VERIFY_CMS_OTP = 'verify_cms_otp';
    const LAST_SESSION = 'last_session';

    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;
    const MESSAGE_FAIL = 'Thất bại';
    const MESSAGE_SUCCESS = 'Thành công';

    public function __construct(AdminInterface $admin, Configure $configService)
    {
        $this->admin = $admin;
        $this->configService = $configService;
        $this->SIGNATURE_PRIVATE_KEY_ADMIN_LOGIN = $this->configService->getConfig('SIGNATURE_PRIVATE_KEY_ADMIN_LOGIN', '');
    }

    public function login(Request $request)
    {
        $userName = $request->get('username');
        $passWord = $request->get('password');
        $user = $this->admin->where(['username' => $userName])->active()->first();
        if(!empty($user)){
            if(Hash::check($passWord, $user->password)){
                $date_check = $user->updated_at;
                date_add($date_check,date_interval_create_from_date_string("1 days"));
                $update_token = (strtotime($date_check) < strtotime(date('YmdHis')));
                $new_token = $user->api_token;
                if($update_token ||  $new_token == '' || empty($new_token)){
                    $new_token = md5($userName . date('YmdHis').str_replace([' ','.'],'',microtime()));
                    $this->admin->where(['username' => $userName])->update(['api_token' => $new_token]);
                }
                $data = collect(["admin" => $user, "token" => $new_token]);
                $adminActivityLog = new AdminActivityLog();
                $adminActivityLog->admin_id = $user->id;
                $adminActivityLog->note = $user->username;
                $adminActivityLog->params = json_encode($request->all());
                $adminActivityLog->name = \Request::route()->getName();
                $adminActivityLog->method = $request->method();
                $adminActivityLog->save();

                // Check password expired
                $isCheckDayChangePass =  (int) $this->configService->getConfig(Configure::IS_CHECK_TIME_CHANGE_PASSWORD, 0);
                if (!$isCheckDayChangePass) return response()->json($data, 200);
                $timeDayChangePass =  (int) $this->configService->getConfig(Configure::TIME_DAY_CHANGE_PASSWORD, 90);
                if ($user->last_update_password < Carbon::now()->subDays($timeDayChangePass)) {
                    $redirectUrl =  $this->loginToCMS($user, VoucherHelp::getDomainCms(). '/cms/change-password-force-admin');

                    return response()->json($data->put('cpasurl', $redirectUrl), 200);
                }
                return response()->json($data, 200);
            }
        }
        return response()->json(null, 200);
    }

    public function logout(Request $request)
    {
        if(\Auth::guard('admin')->check()){
            $user = \Auth::guard('admin')->id();
            $this->admin->where(['id' => $user])->update(['api_token' => '']);
            return response()->json([
                'msg' => 'Logout successfully'
            ], 200);
        }
        return response()->json(null, 200);
    }


}
