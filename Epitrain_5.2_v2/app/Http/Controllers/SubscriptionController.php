<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function addSubscription(Request $request) {
    	$user_id = $request->input('uid');
    	$subscribe_period = $request->input('period');

    	$current = Carbon::now()
    	->addHours(8)
    	-> toDateTimeString();

    	$end_date = Carbon::now();
    	$end_date -> toDateTimeString();  
    	$end_date -> addHours(8);

    	// if($subscribe_period === '1') {
    	// 	$end_date->addDays(1);	
    	// } elseif ($subscribe_period === '6') {
    	// 	$end_date->addDays(6);
    	// } else {
    	// 	$end_date->addDays(12);
    	// }

        $subscribe_periodInDays = (int)$subscribe_period;
        $end_date->addDays($subscribe_periodInDays);
    	
    	DB::table('subscription') ->insert(
                ['user_id' => $user_id, 'period' => $subscribe_period, 'end_date' => $end_date, 'created_at' => $current]
        );

        DB::table('users')
                ->where('id', '=',$user_id )
                ->update(['subscribe' => 1]);

    	return redirect('home');
    }


    public function addSubscriptionPlan(Request $request) {
        $monthperiod = $request->input('monthperiod');
        $price = $request->input('price');

        DB::table('subscriptionplan')->insert(
            ['monthperiod' => (int)$monthperiod, 'price' => (double)$price]
        );
        return redirect(url('subscriptionplan'));

    }

    public function deleteSubscriptionPlan(Request $request) {
        $id = $request->input('id');

        DB::table('subscriptionplan')
        ->where('id', '=', (int)$id)
        ->delete();
        
        return redirect(url('subscriptionplan'));
        
    }
}
