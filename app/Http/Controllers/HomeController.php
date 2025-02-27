<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bid;
use App\Models\Message;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $availableOrders = Order::where('status', Order::STATUS_AVAILABLE)
        ->latest()
        ->with(['files', 'client'])
        ->get();


        //dd($availableOrders);
        
        return view('writers.index', compact('availableOrders'));
    
    
    }

    public function currentOrders()
    {
        $currentOrders = Order::where('writer_id', Auth::id())
        ->whereIn('status', [
            Order::STATUS_CONFIRMED, 
            Order::STATUS_UNCONFIRMED,
            Order::STATUS_IN_PROGRESS,
            Order::STATUS_DONE,
            Order::STATUS_DELIVERED
        ])->latest()->get();
        
        return view('writers.current', compact('currentOrders'));
    }

    public function currentBidOrders()
    {
        return view('writers.bids');
    }

    public function currentOrdersOnRevision()
    {
        return view('writers.revision');
    }

    public function completedOrders()
    {
        return view('writers.finished');
    }

    public function orderOnDispute()
    {
        return view('writers.dispute');
    }

    public function Messages()
    {
        return view('writers.messages');
    }

    public function userFinance()
    {
        return view('writers.finance');
    }

    public function profile()
    {
        return view('writers.profile');
    }

    public function statistics()
    {
        return view('writers.statistics');
    }
    
    public function AssignedOrder()
    {
        return view('writers.AssignedOrder');
    }

    public function availableOrderDetails($id)
    {
        $order = Order::where('status', Order::STATUS_AVAILABLE)
            ->with(['files', 'client', 'bids'])
            ->findOrFail($id);
            
        $userHasBid = $order->bids()->where('user_id', Auth::id())->exists();
            
        return view('writers.availableOrderDetails', compact('order', 'userHasBid'));
    }

}


// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Auth;
// use App\Models\User;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\File;
// use Illuminate\Support\Facades\Hash;

// class HomeController extends Controller
// {
//     /**
//      * Create a new controller instance.
//      *
//      * @return void
//      */
//     public function __construct()
//     {
//         $this->middleware('auth');
//     }

//     /**
//      * Show the application dashboard.
//      *
//      * @return \Illuminate\Contracts\Support\Renderable
//      */
//     public function index()
//     {
//         $user = auth()->user();

//         if ($user->usertype == 'writer') {
//             switch ($user->status) {
//                 case 'pending':
//                     return view('writer.pending');
//                 case 'suspended':
//                     return view('writer.suspended');
//                 case 'terminated':
//                     return view('writer.terminated');
//                 case 'active':
//                 case 'verified':
//                     return redirect()->route('writer.admin');
//                 default:
//                     return view('home');
//             }
//         } elseif (in_array($user->usertype, ['admin', 'support'])) {
//             return redirect()->route('admin.dashboard');
//         }

//         // Default view for other user types
//         return view('home');
//     }
// }
