<?php

namespace Skygdi\DeviceToken;

//use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Request;
use Validator;
use Auth;

/*
##resource get test
curl --header 'cookies: 1_1fd3356a236ae966de31ea1db8536df8' http://lw.skygdi.com/tapi/get

##login
curl --request POST 'http://lw.skygdi.com/tapi/login' \
--data "email=skygdi83@gmail.com" \
--data "password=123456"
*/

class DeviceTokenController extends Controller
{
    
    public static $base_path_name = "/framework/sessions_skygdi";
    public static $user_table = "users_custom";
    public static $session_file_expired_time = 60*60*24*3;

	public function __construct()
    {
    }

    public static function tokenCleaner(){
        $base_path = storage_path().self::$base_path_name;
        if( !file_exists($base_path) ) mkdir($base_path);

        $dp = dir($base_path);
        while($file=$dp->read()){
            if( $file=='.' || $file=='..' ) continue;
            $ext = pathinfo($base_path.'/'.$file, PATHINFO_EXTENSION);
            if( $ext!="txt" ) continue;

            if( time() - filemtime($base_path.'/'.$file) > self::$session_file_expired_time  ){
                unlink($base_path.'/'.$file);
            }
        }
    }



    public static function tokenSave($token,$token_parameter){
        $base_path = storage_path().self::$base_path_name;
        if( !file_exists($base_path) ) mkdir($base_path);
        $file = $base_path."/".$token.".txt";


        $parameter_base = self::tokenRead($token);
        if( !$parameter_base ) $parameter_base = array();   //for very first time

        //overriding
        foreach( $token_parameter as $key => $value ){
            $parameter_base[$key] = $value;
        }

        $fp = fopen($file, 'w');
        fwrite($fp, json_encode($parameter_base));
        fclose($fp);
    }

    public static function tokenRead($token)
    {
        $base_path = storage_path().self::$base_path_name;
        if( !file_exists($base_path) ) mkdir($base_path);

        $file = $base_path."/".$token.".txt";

        $parameter_base = array();
        if( file_exists($file) ){
            $fp = fopen($file, 'r');
            try{
                $parameter_base = (Array)json_decode( fread($fp, 100000) );
            }
            catch (Exception $e) { return false; }
        }
        return $parameter_base;
    }

    public static function tokenCheck($token)
    {
        $parameter_base = self::tokenRead($token);
        if( $parameter_base ){
            $par = array();
            $par["datetime_active"] = date("Y-m-d H:i:s",time());
            self::tokenSave($token,$par);
            Auth::loginUsingId($parameter_base["id"]);
            return true;
        }
        else return false;

    }

    public static function attempt()
    {
        $email = Request::input('email');
        $password = Request::input('password');

        $validator = Validator::make(Request::input(), [
            'email'     =>  'required|email|max:128',
            'password'  =>  'required|max:128',
        ]);

        if( $validator->fails() ){
            return self::response(["result"=>"fail","reason"=>"validation"]);
        }


    	$user = DB::table(self::$user_table)->where(['email' => $email])->first();
    	if ($user && Hash::check($password, $user->password)) {

            //regenerate token string
    		$token = $user->id."_".md5(time()."skygdi");
    		//$user->api_token = $token;

            $token_parameter = array();
            $token_parameter["datetime_login"] = date("Y-m-d H:i:s",time());
            $token_parameter["id"] = $user->id;
            $token_parameter["datetime_active"] = date("Y-m-d H:i:s",time());
            self::tokenSave($token,$token_parameter);

            self::tokenCleaner();


            return self::response(["result"=>"success","token"=>$token]);
		}
        else return self::response(["result"=>"fail","reason"=>"authentication"]);
    }

    public static function response($result)
    {
        return response()->json($result);
    }
    
}
