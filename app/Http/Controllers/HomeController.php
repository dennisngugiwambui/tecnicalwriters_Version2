<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bid;
use App\Models\Message;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        if (Auth::check()) {
            $user = Auth::user();
        
        
            // Get all message threads related to this user (sent or received)
            $messageThreads = Message::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('receiver_id', $user->id);
                })
                ->with(['user', 'receiver', 'order', 'files'])
                ->latest()
                ->get()
                ->groupBy(function($message) {
                    // Group by conversation
                    if ($message->is_general) {
                        // If it's a general message, group by title and the other user
                        return 'general_' . $message->title . '_' . 
                            ($message->user_id == Auth::id() ? $message->receiver_id : $message->user_id);
                    } else {
                        // If it's order related, group by order
                        return 'order_' . ($message->order_id ?? 0);
                    }
                })
                ->map(function($messages) {
                    // For each thread, get the latest message
                    $latest = $messages->first();
                    $latest->thread_messages_count = $messages->count();
                    $latest->unread_count = $messages
                        ->where('receiver_id', Auth::id())
                        ->whereNull('read_at')
                        ->count();
                        
                    return $latest;
                });
            
            // Get users for new message dropdown (clients, support staff)
            $users = User::whereIn('usertype', ['client', 'admin', 'support'])
                ->select('id', 'name', 'usertype', 'email')
                ->orderBy('usertype')
                ->orderBy('name')
                ->get()
                ->map(function($user) {
                    $user->display_name = $user->name . ' (' . ucfirst($user->usertype) . ')';
                    return $user;
                });
                
            // Get orders for new message dropdown
            $userOrders = Order::where('writer_id', $user->id)
                ->orWhereIn('id', function($query) use ($user) {
                    $query->select('order_id')
                        ->from('bids')
                        ->where('user_id', $user->id);
                })
                ->select('id', 'title')
                ->get();
                
            return view('writers.messages', compact('messageThreads', 'userOrders', 'users'));

        } else {
            // Handle unauthenticated user - perhaps redirect to login
            $messageThreads = collect(); // Empty collection
            return redirect()->route('login')->with('error', 'Please login to view messages');
        }
    }

    public function sendNewMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'order_id' => 'nullable|exists:orders,id',
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
        $message->user_id = Auth::id();
        $message->receiver_id = $request->receiver_id;
        $message->title = $request->title;
        $message->message = $request->message;
        $message->order_id = $request->order_id;
        $message->is_general = empty($request->order_id);
        $message->message_type = User::find($request->receiver_id)->usertype === 'client' ? 'client' : 'support';
        $message->save();
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('message_attachments/' . $message->id, $fileName);
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

    public function viewMessageThread($threadId)
    {
        $user = Auth::user();
        
        // Parse the thread ID to determine if it's general or order-related
        $parts = explode('_', $threadId);
        $type = $parts[0];
        
        if ($type === 'order') {
            $orderId = $parts[1];
            $order = Order::findOrFail($orderId);
            
            // Get all messages for this order
            $messages = Message::where('order_id', $orderId)
                ->with(['user', 'receiver', 'files'])
                ->orderBy('created_at')
                ->get();
            
            // Mark unread messages as read
            Message::where('order_id', $orderId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
                
            $otherUser = $messages->first()->user_id == $user->id 
                ? User::find($messages->first()->receiver_id)
                : User::find($messages->first()->user_id);
                
            return view('writers.message-thread', compact('order', 'messages', 'otherUser', 'type'));
        } else {
            // This is a general message thread
            $title = $parts[1];
            $otherUserId = $parts[2];
            $otherUser = User::findOrFail($otherUserId);
            
            // Get all messages between these users with this title
            $messages = Message::where(function($query) use ($user, $otherUser) {
                    $query->where(function($q) use ($user, $otherUser) {
                        $q->where('user_id', $user->id)
                        ->where('receiver_id', $otherUser->id);
                    })->orWhere(function($q) use ($user, $otherUser) {
                        $q->where('user_id', $otherUser->id)
                        ->where('receiver_id', $user->id);
                    });
                })
                ->where('is_general', true)
                ->where('title', $title)
                ->with(['user', 'receiver', 'files'])
                ->orderBy('created_at')
                ->get();
            
            // Mark unread messages as read
            Message::where(function($query) use ($user, $otherUser) {
                    $query->where('user_id', $otherUser->id)
                        ->where('receiver_id', $user->id);
                })
                ->where('is_general', true)
                ->where('title', $title)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
                
            return view('writers.message-thread', compact('messages', 'otherUser', 'title', 'type'));
        }
    }

    public function replyToMessage(Request $request)
    {
        $request->validate([
            'thread_id' => 'required|string',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);
        
        $user = Auth::user();
        $threadId = $request->thread_id;
        $parts = explode('_', $threadId);
        $type = $parts[0];
        
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
        
        if ($type === 'order') {
            $orderId = $parts[1];
            $order = Order::findOrFail($orderId);
            
            // Get the previous message to determine the receiver
            $previousMessage = Message::where('order_id', $orderId)
                ->orderBy('created_at', 'desc')
                ->first();
                
            $receiverId = $previousMessage->user_id == $user->id 
                ? $previousMessage->receiver_id 
                : $previousMessage->user_id;
                
            // Create the reply
            $message = new Message();
            $message->user_id = $user->id;
            $message->receiver_id = $receiverId;
            $message->order_id = $orderId;
            $message->is_general = false;
            $message->message = $request->message;
            $message->message_type = User::find($receiverId)->usertype === 'client' ? 'client' : 'support';
            $message->save();
        } else {
            // This is a general message
            $title = $parts[1];
            $otherUserId = $parts[2];
            
            // Create the reply
            $message = new Message();
            $message->user_id = $user->id;
            $message->receiver_id = $otherUserId;
            $message->title = $title;
            $message->is_general = true;
            $message->message = $request->message;
            $message->message_type = User::find($otherUserId)->usertype === 'client' ? 'client' : 'support';
            $message->save();
        }
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('message_attachments/' . $message->id, $fileName);
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
        
        return back()->with('success', 'Reply sent successfully!');
    }

    public function checkNewMessages(Request $request, $orderId)
    {
        $messageType = $request->query('message_type', 'client');
        $lastMessageId = $request->query('last_id', 0);
        
        // Only get messages newer than the last one the client has
        $messages = Message::where('order_id', $orderId)
            ->where('message_type', $messageType)
            ->where('id', '>', $lastMessageId)  // This is important!
            ->with(['user', 'files'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'hasNewMessages' => $messages->count() > 0,
            'messages' => $messages
        ]);
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
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You must place a bid first to message about this order'
                    ], 403);
                }
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
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your message contains prohibited keywords: ' . implode(', ', $foundKeywords) . '. Please revise your message to avoid payment discussions in the messaging system.'
                    ], 400);
                }
                return back()->with('warning', 'Your message contains prohibited keywords: ' . implode(', ', $foundKeywords) . '. Please revise your message to avoid payment discussions in the messaging system.');
            }
            
            // Create a new message
            $message = new Message();
            $message->order_id = $orderId;
            $message->user_id = Auth::id();
            $message->message = $request->message;
            $message->message_type = $request->message_type;
            
            // Set title field - this is what was missing
            $message->title = $request->title ?? "Order #" . $orderId . " Message";
            
            // Set receiver_id based on message type
            if ($request->message_type === 'client') {
                // For client messages, set receiver to the client
                $message->receiver_id = $order->client_id;
            } else {
                // For support messages, find an admin or support user
                $supportUser = User::where('usertype', 'admin')
                    ->orWhere('usertype', 'support')
                    ->first();
                    
                if ($supportUser) {
                    $message->receiver_id = $supportUser->id;
                } else {
                    throw new \Exception("No support staff available to receive the message");
                }
            }
            
            // Set is_general to false since this is order-related
            $message->is_general = false;
            
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return back()->with('success', 'Message sent successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message: ' . $e->getMessage()
                ], 500);
            }
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
