<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bid;
use App\Models\Message;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Str;
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
         // Get order details with related data
         $order = Order::where('status', Order::STATUS_AVAILABLE)
         ->with(['files', 'client', 'bids', 'messages'])
         ->findOrFail($id);
     
     // Calculate time remaining until deadline
     $deadline = Carbon::parse($order->deadline);
     $now = Carbon::now();
     $diff = $now->diff($deadline);
     
     $timeRemaining = '';
     if ($diff->days > 0) {
         $timeRemaining .= $diff->days . 'd ';
     }
     if ($diff->h > 0) {
         $timeRemaining .= $diff->h . 'h ';
     }
     $timeRemaining .= $diff->i . 'm';
     
     // Check if user has bid on this order
     $userHasBid = $order->bids()->where('user_id', Auth::id())->exists();
     $bidCount = $order->bids->count();
     
     // Get client/support messages
     $clientMessages = $order->messages()->whereIn('user_id', [$order->client_id, Auth::id()])->latest()->get();
     $supportMessages = $order->messages()->whereIn('user_id', function($query) {
         $query->select('id')->from('users')->whereIn('usertype', ['admin', 'support']);
     })->orWhere('user_id', Auth::id())->latest()->get();
     
     return view('writers.availableOrderDetails', compact(
         'order', 
         'userHasBid', 
         'timeRemaining', 
         'bidCount',
         'clientMessages',
         'supportMessages',
         'deadline'
     ));
    }

    public function submitBid(Request $request, $orderId)
{
    // Validate request
    $request->validate([
        'amount' => 'required|numeric|min:0',
    ]);

    try {
        // Get the order
        $order = Order::findOrFail($orderId);
        
        // Check if order is available
        if ($order->status !== Order::STATUS_AVAILABLE) {
            return response()->json([
                'success' => false,
                'message' => 'This order is no longer available for bidding'
            ], 400);
        }
        
        // Check if user already has a bid on this order
        $existingBid = Bid::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->exists();
            
        if ($existingBid) {
            return response()->json([
                'success' => false,
                'message' => 'You have already placed a bid on this order'
            ], 400);
        }
        
        // Create a new bid
        $bid = new Bid();
        $bid->order_id = $orderId;
        $bid->user_id = Auth::id();
        $bid->amount = $request->amount;
        $bid->delivery_time = $order->deadline; // Use order deadline as delivery time
        $bid->cover_letter = $request->cover_letter ?? 'I can complete this order as requested.';
        $bid->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Your bid has been placed successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to place bid: ' . $e->getMessage()
        ], 500);
    }
}

public function download(Request $request)
{
    $request->validate([
        'file_id' => 'required|exists:files,id',
    ]);

    $file = File::findOrFail($request->file_id);
    
    // Check if user has permission to download the file
    // For order files, check if the order is available or assigned to this writer
    if ($file->fileable_type === 'App\Models\Order') {
        $order = Order::find($file->fileable_id);
        
        if (!$order || 
            ($order->status !== Order::STATUS_AVAILABLE && 
             $order->writer_id !== Auth::id() && 
             Auth::user()->usertype !== 'admin')) {
            abort(403, 'You do not have permission to download this file');
        }
    }

    // Check if file exists in storage
    if (!Storage::exists($file->path)) {
        abort(404, 'File not found in storage');
    }

    return Storage::download($file->path, $file->name);
}

public function downloadMultiple(Request $request)
{
    $request->validate([
        'file_ids' => 'required|array',
        'file_ids.*' => 'exists:files,id',
    ]);

    $fileIds = $request->file_ids;
    
    // If only one file, download it directly
    if (count($fileIds) === 1) {
        return $this->download(new Request(['file_id' => $fileIds[0]]));
    }

    // For multiple files, create a zip
    $zip = new ZipArchive();
    $zipName = 'order_files_' . time() . '.zip';
    $zipPath = storage_path('app/temp/' . $zipName);
    
    // Create temp directory if it doesn't exist
    if (!Storage::exists('temp')) {
        Storage::makeDirectory('temp');
    }

    if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
        abort(500, 'Cannot create zip file');
    }

    $files = File::whereIn('id', $fileIds)->get();
    
    foreach ($files as $file) {
        // Verify permission for each file
        if ($file->fileable_type === 'App\Models\Order') {
            $order = Order::find($file->fileable_id);
            
            if (!$order || 
                ($order->status !== Order::STATUS_AVAILABLE && 
                 $order->writer_id !== Auth::id() && 
                 Auth::user()->usertype !== 'admin')) {
                continue; // Skip files without permission
            }
        }
        
        if (Storage::exists($file->path)) {
            $fileContent = Storage::get($file->path);
            // Generate a unique name to avoid conflicts
            $fileNameInZip = Str::slug(pathinfo($file->name, PATHINFO_FILENAME)) . '_' . 
                             substr(md5($file->id), 0, 6) . '.' . 
                             pathinfo($file->name, PATHINFO_EXTENSION);
            $zip->addFromString($fileNameInZip, $fileContent);
        }
    }

    $zip->close();

    return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
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
