<?php

Route::group(['prefix' => 'tapi', 'middleware' => 'Skygdi\DeviceToken\ApiTokenCheck'], function () {
	    Route::get('get', function () {
		    //var_dump( Auth::user() );//->id;
		    echo "Current user Email:".Auth::user()->email."\n";
		});
	}
);

Route::post('/tapi/login', 'Skygdi\DeviceToken\DeviceTokenController@attempt');
