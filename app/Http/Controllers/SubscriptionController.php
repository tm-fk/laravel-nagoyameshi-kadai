<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SubscriptionController extends Controller
{
    public function create() {

        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    public function store(Request $request) {

        $user = $request->user();

            $request->user()->newSubscription(
                 'premium_plan','price_1Pd5NsGXYBip193mW5Sox4GG'
            )->create($request->paymentMethodId);
        

            return redirect()->route('home')->with('flash_message','有料プランの登録が完了しました。');
      }

    public function edit(User $user) {
        $user = Auth::user();

        $intent = $user->createSetupIntent();


        return view('subscription.edit', compact('user','intent'));
    }

    public function update(Request $request) {

        $user = $request->user();

        $user->updateDefaultPaymentMethod($request->paymentMethodId);


        return redirect()->route('home')->with('flash_message','お支払方法を変更しました。');

    }

    public function cancel() {

        return view('subscription.cancel');
    }

    public function destroy(Request $request) {

        $user = $request->user();

        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        $user->subscription('premium_plan')->cancelNow();

        $user->delete();

        return redirect()->route('home')->with('flash_message','有料プランを解約しました。');


    }
}


