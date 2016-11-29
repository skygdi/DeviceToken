<?php


//Route::post('/tapi/login', 'SkygdiAuth@attempt');
//curl --header 'cookies: 1_34c425b128503ecbf882c73a9da420aa' http://lw.skygdi.com/tapi/get

/*curl --request POST 'http://lw.skygdi.com/tapi/login' --data "email=skygdi83@gmail.com" --data "password=123456"
*/



Route::group(['prefix' => 'tapi', 'middleware' => 'Skygdi\DeviceToken\ApiTokenCheck'], function () {
    //http://lw.skygdi.com/tapi/get
    //Route::get('get', 'test@api_get');
    Route::get('get', function () {
	    echo Auth::user()->id;
    	//var_dump($user);
	});
	//Route::post('login', 'SkygdiAuth@attempt');
});

Route::post('/tapi/login', 'Skygdi\DeviceToken\DeviceTokenController@attempt');
