<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Support\Facades\DB;

class CashController extends Controller
{
    public function calculateCash($module, $id, $type, $changes, $user){
        /**$exists = DB::table('cash')
            ->select('*')
            ->where('action_id', $id)
            ->where('module', $module)
            ->orderBy('created_at', 'desc')
            ->skip(0)
            ->take(1)
            ->get();**/
        $exists = Cash::where('action_id', $id)->where('module', $module)->orderBy('id', 'desc')->first();
        $latest_amount = Cash::orderBy('id', 'desc')->first()->amount;
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        if($exists) {
            $old_amount = $exists->changes;
            switch($module) {
                case ("order"):
                case ("stock"):
                case("expense"):
                    $amount = $latest_amount+$old_amount-$changes;
                    break;
                case("sale"):
                    $amount = $latest_amount-$old_amount+$changes;
                    break;
                default:
                    $amount = $latest_amount+0;
            }
        } else {
            switch($module) {
                case ("order"):
                case ("stock"):
                case("expense"):
                    $amount = $latest_amount-$changes;
                    break;
                case("sale"):
                    $amount = $latest_amount+$changes;
                    break;
                default:
                    $amount = $latest_amount+0;
            }
        }

        $cash = DB::table('cash')->insert(
            ['changes' => $changes, 'amount' => $amount, 'module' => $module, 'action_id' => $id, 'action_type' => $type, 'user_id' => $user, 'created_at' => $created_at, 'updated_at' => $updated_at]
        );

        return $cash;
    }
}
