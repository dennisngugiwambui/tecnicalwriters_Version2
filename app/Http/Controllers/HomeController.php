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
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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

    /**
     * Display current orders - split between active (CONFIRMED/UNCONFIRMED) and completed (DONE/DELIVERED)
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function currentOrders()
    {
        // Get active orders (CONFIRMED, UNCONFIRMED)
        $activeOrders = Order::where('writer_id', Auth::id())
            ->whereIn('status', [
                Order::STATUS_CONFIRMED, 
                Order::STATUS_UNCONFIRMED
            ])
            ->with('client')
            ->latest()
            ->get();
            
        // Get completed orders (DONE, DELIVERED)
        $completedOrders = Order::where('writer_id', Auth::id())
            ->whereIn('status', [
                Order::STATUS_DONE,
                Order::STATUS_DELIVERED
            ])
            ->with('client')
            ->latest()
            ->get();
        
        // Calculate remaining time for active orders
        $activeOrders->map(function($order) {
            $deadline = Carbon::parse($order->deadline);
            $now = Carbon::now();
            
            if ($now->gt($deadline)) {
                // Deadline has passed
                $order->time_remaining = 'Overdue';
                $order->time_status = 'overdue';
            } else {
                $diff = $now->diff($deadline);
                
                if ($diff->days > 0) {
                    $order->time_remaining = $diff->days . 'd ' . $diff->h . 'h';
                } else if ($diff->h > 0) {
                    $order->time_remaining = $diff->h . 'h ' . $diff->i . 'm';
                } else {
                    $order->time_remaining = $diff->i . 'm';
                }
                
                // Add time status for color coding
                if ($diff->days > 2) {
                    $order->time_status = 'safe';
                } else if ($diff->days > 0 || $diff->h > 5) {
                    $order->time_status = 'warning';
                } else {
                    $order->time_status = 'urgent';
                }
            }
            
            return $order;
        });
        
        // Mark completed orders as completed
        $completedOrders->map(function($order) {
            $order->time_remaining = 'Completed';
            $order->time_status = 'completed';
            return $order;
        });
        
        return view('writers.current', compact('activeOrders', 'completedOrders'));
    }

    /**
     * Display orders that the writer has bid on
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function currentBidOrders()
    {
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

    /**
     * Display orders that are on revision
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function currentOrdersOnRevision()
    {
        $revisedOrders = Order::where('writer_id', Auth::id())
            ->where('status', Order::STATUS_REVISION)
            ->latest()
            ->get();
            
        return view('writers.revision', compact('revisedOrders'));
    }

    /**
     * Display completed orders
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function completedOrders()
    {
        $completedOrders = Order::where('writer_id', Auth::id())
            ->whereIn('status', [
                Order::STATUS_COMPLETED, 
                Order::STATUS_PAID
            ])
            ->latest()
            ->get();
            
        return view('writers.finished', compact('completedOrders'));
    }

    /**
     * Display orders that are on dispute
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function orderOnDispute()
    {
        $disputeOrders = Order::where('writer_id', Auth::id())
            ->where('status', Order::STATUS_DISPUTE)
            ->latest()
            ->get();
            
        return view('writers.dispute', compact('disputeOrders'));
    }
    
    /**
     * Display the messages page with threads
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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
            return redirect()->route('login')->with('error', 'Please login to view messages');
        }
    }
    /**
     * View a specific message thread
     *
     * @param  string  $threadId
     * @return \Illuminate\Contracts\Support\Renderable
     */
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
                
            $otherUser = $messages->count() > 0 ? 
                ($messages->first()->user_id == $user->id 
                    ? User::find($messages->first()->receiver_id)
                    : User::find($messages->first()->user_id)) : null;
            
            if (!$otherUser && $order) {
                // If no messages yet, find the other user based on order information
                if ($order->writer_id == $user->id) {
                    // If current user is the writer, other user is the client
                    $otherUser = User::find($order->client_id);
                } else {
                    // Otherwise, get a support user
                    $otherUser = User::where('usertype', 'admin')
                        ->orWhere('usertype', 'support')
                        ->first();
                }
            }
            
            // Get message threads for the main messages view
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
                
            // Get users for new message dropdown
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
            
            return view('writers.messages', compact('messageThreads', 'userOrders', 'users', 'messages', 'order', 'otherUser', 'type'));
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
            
            // Get message threads for the main messages view
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
                
            // Get users for new message dropdown
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
                
            return view('writers.messages', compact('messageThreads', 'userOrders', 'users', 'messages', 'otherUser', 'title', 'type'));
        }
    }

    /**
     * Send a new message
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Reply to a message
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your message contains prohibited keywords: ' . implode(', ', $foundKeywords) . '. Please revise your message to avoid payment discussions.'
                ], 400);
            }
            return back()->with('error', 'Your message contains prohibited keywords: ' . implode(', ', $foundKeywords) . '. Please revise your message to avoid payment discussions.');
        }
        
        if ($type === 'order') {
            $orderId = $parts[1];
            $order = Order::findOrFail($orderId);
            
            // Check if user can reply (should not be assigned to another writer)
            if ($order->writer_id && $order->writer_id != $user->id && $user->usertype !== 'admin' && $user->usertype !== 'support') {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This order is assigned to another writer.'
                    ], 403);
                }
                return back()->with('error', 'This order is assigned to another writer.');
            }
            
            // Get the previous message to determine the receiver
            $previousMessage = Message::where('order_id', $orderId)
                ->orderBy('created_at', 'desc')
                ->first();
                
            $receiverId = $previousMessage ? 
                ($previousMessage->user_id == $user->id 
                    ? $previousMessage->receiver_id 
                    : $previousMessage->user_id) :
                ($order->client_id); // Default to client if no previous messages
                
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
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully!'
            ]);
        }
        
        return back()->with('success', 'Reply sent successfully!');
    }

    /**
     * Check for new messages
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function checkNewMessages(Request $request, $orderId)
    {
        $messageType = $request->query('message_type', 'client');
        $lastMessageId = $request->query('last_id', 0);
        
        // Only get messages newer than the last one the client has
        $messages = Message::where('order_id', $orderId)
            ->where('message_type', $messageType)
            ->where('id', '>', $lastMessageId)
            ->with(['user', 'files'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'hasNewMessages' => $messages->count() > 0,
            'messages' => $messages
        ]);
    }
    /**
     * Display user finance page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userFinance()
    {
        return view('writers.finance');
    }

    /**
     * Display user profile page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        return view('writers.profile');
    }

    /**
     * Display user statistics page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function statistics()
    {
        return view('writers.statistics');
    }
    
    /**
     * Display assigned order details
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function AssignedOrder($id = null)
    {
        if (!$id) {
            // Use default ID if none provided
            $id = 201394828;
        }
        
        $order = Order::with(['customer', 'files'])
            ->where('id', $id)
            ->where('writer_id', Auth::id())
            ->firstOrFail();
        
        // Count unread messages
        $unreadMessages = Message::where('order_id', $order->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
        
        // Get messages grouped by date
        $messages = Message::where('order_id', $order->id)
            ->orderBy('created_at')
            ->get()
            ->groupBy(function($message) {
                return Carbon::parse($message->created_at)->format('F d, Y');
            });
        
        return view('writers.assigned-order-details', [
            'order' => $order,
            'unreadMessages' => $unreadMessages,
            'messages' => $messages
        ]);
    }

    /**
     * Display available order details
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function availableOrderDetails($id)
    {
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

    /**
     * Send a message for an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
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
            
            // Set title field 
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
    
    /**
     * Submit a bid for an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */

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

    /**
     * Download a file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Download multiple files
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Mark messages as read for an order
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function markMessagesRead(Request $request, $id)
    {
        try {
            Message::where('order_id', $id)
                ->where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking messages as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read'
            ], 500);
        }
    }

    /**
     * Upload files for an order and mark it as DONE if completed
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadFiles(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'files' => 'required|array',
            'files.*' => 'required|file|max:99000', // 99MB limit
            'descriptions' => 'required|array',
            'descriptions.*' => 'nullable|string'
        ]);
        
        $orderId = $request->order_id;
        $order = Order::where('id', $orderId)
            ->where('writer_id', Auth::id())
            ->firstOrFail();
        
        try {
            $uploadedFiles = [];
            $hasCompletedFile = false;
            
            // Process each file
            foreach ($request->file('files') as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $size = $file->getSize();
                $mimeType = $file->getMimeType();
                
                // Generate unique filename
                $filename = $order->order_number . '_' . uniqid() . '.' . $extension;
                
                // Store file in storage
                $path = $file->storeAs('order_files', $filename, 'public');
                
                // Get description
                $description = isset($request->descriptions[$index]) ? $request->descriptions[$index] : null;
                
                // Check if this file is marked as "completed"
                if ($description === 'completed') {
                    $hasCompletedFile = true;
                }
                
                // Create file record
                $fileRecord = File::create([
                    'order_id' => $order->id,
                    'path' => $path,
                    'original_name' => $originalName,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'description' => $description,
                    'uploaded_by' => Auth::id()
                ]);
                
                $uploadedFiles[] = $fileRecord;
            }
            
            // If a completed file was uploaded and order is CONFIRMED or ON_REVISION, mark order as DONE
            $statusChanged = false;
            if ($hasCompletedFile && in_array($order->status, [Order::STATUS_CONFIRMED, Order::STATUS_REVISION])) {
                $order->status = Order::STATUS_DONE;
                $order->save();
                $statusChanged = true;
                
                // Create system message
                Message::create([
                    'order_id' => $order->id,
                    'message' => "Writer has marked this order as completed.",
                    'user_id' => Auth::id(),
                    'receiver_id' => $order->client_id,
                    'is_general' => false,
                    'message_type' => 'client',
                    'read_at' => null
                ]);
            }
            
            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'status_changed' => $statusChanged,
                'message' => 'Files uploaded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading files: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload files: ' . $e->getMessage()
            ], 500);
        }
    }
}