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
    
    // Add this to your BaseController or a middleware
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Current orders count
                $currentCount = Order::where('writer_id', $user->id)
                    ->whereIn('status', [
                        Order::STATUS_CONFIRMED,
                        Order::STATUS_UNCONFIRMED,
                        Order::STATUS_IN_PROGRESS,
                        Order::STATUS_DONE,
                        Order::STATUS_DELIVERED
                    ])
                    ->count();

                // Revision orders count
                $revisionCount = Order::where('writer_id', $user->id)
                    ->where('status', Order::STATUS_REVISION)
                    ->count();

                // Dispute orders count 
                $disputeCount = Order::where('writer_id', $user->id)
                    ->where('status', Order::STATUS_DISPUTE)
                    ->count();

                // Bids count
                $bidsCount = Bid::where('user_id', $user->id)
                    ->whereHas('order', function($query) {
                        $query->where('status', Order::STATUS_AVAILABLE);
                    })
                    ->count();

                // Unread messages count
                $unreadMessagesCount = Message::whereIn('order_id', function($query) use ($user) {
                    $query->select('id')
                        ->from('orders')
                        ->where(function($orderQuery) use ($user) {
                            $orderQuery->where('writer_id', $user->id)
                                ->orWhereIn('id', function($bidQuery) use ($user) {
                                    $bidQuery->select('order_id')
                                        ->from('bids')
                                        ->where('user_id', $user->id);
                                });
                        });
                })
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();

                // Share variables with all views
                view()->share(compact(
                    'currentCount',
                    'revisionCount',
                    'disputeCount',
                    'bidsCount',
                    'unreadMessagesCount'
                ));
            }
            
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Check if user is active
        //$user = Auth::user();
        //if ($user->status !== 'active') {
         //   return redirect()->route('writer.pending')->with('error', 'Your account is not active yet.');
        //}
    
        // Get IDs of orders the user has already bid on
        $biddedOrderIds = Bid::where('user_id', Auth::id())->pluck('order_id');
        
        // Get available orders excluding those the user has already bid on
        $availableOrders = Order::where('status', Order::STATUS_AVAILABLE)
            ->whereNotIn('id', $biddedOrderIds)
            ->with(['files', 'client', 'bids'])
            ->latest()
            ->get();
        
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
        // Check if user is active
        //$user = Auth::user();
        //if ($user->status !== 'active') {
        //    return redirect()->route('writer.pending')->with('error', 'Your account is not active yet.');
        //}

        // Get orders the user has bid on
        $biddedOrderIds = Bid::where('user_id', Auth::id())->pluck('order_id');
        
        // Get those orders with user's bid information
        $bidOrders = Order::whereIn('id', $biddedOrderIds)
            ->with(['files', 'client', 'bids' => function($query) {
                $query->where('user_id', Auth::id());
            }])
            ->latest()
            ->get();
            
        return view('writers.bids', compact('bidOrders'));
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
    // Add this method to HomeController.php
    public function messages()
    {
        $user = Auth::user();
        
        // Get all messages related to orders the writer is involved with
        $messageThreads = Message::select('order_id')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id) // Messages sent by user
                    ->orWhereIn('order_id', function($subquery) use ($user) {
                        $subquery->select('id')
                            ->from('orders')
                            ->where(function($orderQuery) use ($user) {
                                $orderQuery->where('writer_id', $user->id) // Assigned orders
                                    ->orWhereIn('id', function($bidQuery) use ($user) {
                                        $bidQuery->select('order_id')
                                            ->from('bids')
                                            ->where('user_id', $user->id); // Orders bid on
                                    });
                            });
                    });
            })
            ->with(['order'])
            ->groupBy('order_id')
            ->orderByDesc(function($query) {
                $query->select('created_at')
                    ->from('messages')
                    ->whereColumn('order_id', 'messages.order_id')
                    ->latest()
                    ->limit(1);
            })
            ->get();
        
        // For each thread, get the latest message
        foreach ($messageThreads as $thread) {
            $thread->latestMessage = Message::where('order_id', $thread->order_id)
                ->with(['user', 'files'])
                ->latest()
                ->first();
        }
        
        // Get orders for new message dropdown
        $userOrders = Order::where('writer_id', $user->id)
            ->orWhereIn('id', function($query) use ($user) {
                $query->select('order_id')
                    ->from('bids')
                    ->where('user_id', $user->id);
            })
            ->select('id', 'title')
            ->get();
            
        return view('writers.messages', compact('messageThreads', 'userOrders'));
    }

    public function sendNewMessage(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string',
            'message_type' => 'required|in:client,support',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        // Check for forbidden words
        $forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
        $messageText = strtolower($request->message);
        $foundKeywords = [];
        
        foreach ($forbiddenKeywords as $keyword) {
            if (strpos($messageText, $keyword) !== false) {
                $foundKeywords[] = $keyword;
            }
        }
        
        if (!empty($foundKeywords)) {
            return back()->with('error', 'Your message contains prohibited keywords: ' . implode(', ', $foundKeywords) . '. Please revise your message to avoid payment discussions.');
        }
        
        // Create new message
        $message = new Message();
        $message->order_id = $request->order_id;
        $message->user_id = Auth::id();
        $message->message = $request->message;
        $message->message_type = $request->message_type;
        $message->save();
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('order_messages/' . $request->order_id, $fileName);
                $fileSize = $file->getSize();
                
                $fileModel = new File([
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $fileSize,
                    'uploaded_by' => Auth::id(),
                    'description' => $request->input('attachment_descriptions.' . $index, null)
                ]);
                
                $message->files()->save($fileModel);
            }
        }
        
        return redirect()->route('writer.messages')->with('success', 'Message sent successfully!');
    }

    public function viewMessageThread($orderId)
    {
        $user = Auth::user();
        
        // Get the order
        $order = Order::findOrFail($orderId);
        
        // Check if user has access to this order
        $hasAccess = $order->writer_id == $user->id || 
                    $order->bids()->where('user_id', $user->id)->exists();
                    
        if (!$hasAccess && $user->usertype !== 'admin' && $user->usertype !== 'support') {
            return redirect()->route('writer.messages')->with('error', 'You don\'t have access to view these messages.');
        }
        
        // Get all messages for this order
        $messages = Message::where('order_id', $orderId)
            ->with(['user', 'files'])
            ->orderBy('created_at')
            ->get();
        
        // Mark unread messages as read
        Message::where('order_id', $orderId)
            ->whereNotIn('user_id', [$user->id])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return view('writers.message-thread', compact('order', 'messages'));
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

        // In HomeController.php
    public function availableOrderDetails($id)
    {
        // Check if user is active
        //$user = Auth::user();
        //if ($user->status !== 'active') {
        //    return redirect()->route('writer.pending')->with('error', 'Your account is not active yet.');
        //}

        // Get order details with related data
        $order = Order::with(['files', 'client', 'bids', 'messages' => function($query) {
            $query->with('user')->latest();
        }])->findOrFail($id);

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
        
        // Get client messages (between writer and client)
        $clientMessages = $order->messages()
            ->with('user')
            ->where('message_type', 'client')
            ->latest()
            ->get();
        
        // Get support messages (between writer and support/admin)
        $supportMessages = $order->messages()
            ->with('user')
            ->where('message_type', 'support')
            ->latest()
            ->get();
        
        // Verify files exist in storage
        foreach ($order->files as $file) {
            $file->exists = Storage::exists($file->path);
        }

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

    public function sendMessage(Request $request, $orderId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'message_type' => 'required|in:client,support',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            // Get the order
            $order = Order::findOrFail($orderId);
            
            // Check if user can send a message for this order
            // Writers can message if they've placed a bid or are assigned
            $userHasBid = $order->bids()->where('user_id', Auth::id())->exists();
            $isAssigned = $order->writer_id == Auth::id();
            
            if (!$userHasBid && !$isAssigned && Auth::user()->usertype !== 'admin' && Auth::user()->usertype !== 'support') {
                return back()->with('error', 'You must place a bid first to message about this order');
            }
            
            // Check for forbidden keywords
            $forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
            $messageText = strtolower($request->message);
            $foundKeywords = [];
            
            foreach ($forbiddenKeywords as $keyword) {
                if (strpos($messageText, $keyword) !== false) {
                    $foundKeywords[] = $keyword;
                }
            }
            
            if (!empty($foundKeywords)) {
                return back()->with('warning', 'Your message contains prohibited keywords: ' . implode(', ', $foundKeywords) . '. Please revise your message to avoid payment discussions in the messaging system.');
            }
            
            // Create a new message
            $message = new Message();
            $message->order_id = $orderId;
            $message->user_id = Auth::id();
            $message->message = $request->message;
            $message->message_type = $request->message_type;
            $message->save();
            
            // Handle file attachment if present
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('order_messages/' . $orderId, $fileName);
                $fileSize = $file->getSize();
                
                $fileModel = new File([
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $fileSize,
                    'uploaded_by' => Auth::id(),
                ]);
                
                $message->files()->save($fileModel);
            }
            
            // Mark any unread messages from the other side as read
            if ($request->message_type === 'client') {
                // Mark client messages as read
                Message::where('order_id', $orderId)
                    ->where('message_type', 'client')
                    ->where('user_id', $order->client_id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } else {
                // Mark support messages as read
                Message::where('order_id', $orderId)
                    ->where('message_type', 'support')
                    ->whereNotIn('user_id', [Auth::id()])
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
            
            return back()->with('success', 'Message sent successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
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
