<?php

namespace App\Http\Controllers;

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
        return view('writers.index');
    }

    public function currentOrders()
    {
        return view('writers.current');
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
