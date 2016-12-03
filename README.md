# DeviceToken
For some device like mobile or special needed , this is a simple solution:

`composer require skygdi/devicetoken "dev-master"` 

Add to config/app.php providers

`Skygdi\DeviceToken\DeviceTokenServiceProvider::class,`


### Usage:

`curl --request POST 'http://127.0.0.1:8000/tapi/login' --data "email=yourdomain@gmail.com"  --data "password=123456"`

Success login result like that(All Json return):

`{"result":"success","token":"1_0c4cdb8a5af4d2df6cb5925473f71f2b"}`

With the token for coming every requests (the default test route as follow)

`curl --header 'cookies: 1_0c4cdb8a5af4d2df6cb5925473f71f2b' http://127.0.0.1:8000/tapi/get`

###You could write your route like that:

```sh
Route::group(['prefix' => 'tapi', 'middleware' => 'Skygdi\DeviceToken\ApiTokenCheck'], function () {
	    Route::get('get', function () {
		    //echo "Current user Email:".Auth::user()->email."\n";
		});
	}
);
```







