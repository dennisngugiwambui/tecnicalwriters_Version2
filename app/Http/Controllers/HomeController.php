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
use Illuminate\Support\Facades\Validator;
use App\Models\WriterProfile; 
use Illuminate\Support\Facades\DB;

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
                  
                // Check user status for redirection
                if ($user->usertype === 'writer') {
                    if ($user->status === 'pending') {
                        return redirect()->route('assessment.grammar')
                            ->with('message', 'You need to complete the grammar assessment.');
                    }
                    
                    if (in_array($user->status, ['failed', 'suspended', 'banned', 'terminated', 'locked']) || 
                        $user->is_suspended === 'yes') {
                        return redirect()->route('failed')
                            ->with('message', 'Your account has been ' . $user->status);
                    }
                }
                    
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
        $user = Auth::user();
        
        // Get IDs of orders the user has already bid on
        $biddedOrderIds = Bid::where('user_id', Auth::id())->pluck('order_id');
        
        // Get available orders excluding those the user has already bid on
        $availableOrders = Order::where('status', Order::STATUS_AVAILABLE)
            ->whereNotIn('id', $biddedOrderIds)
            ->with(['files', 'client', 'bids'])
            ->latest()
            ->get();
        
        // Get unique disciplines from available orders
        $disciplines = $availableOrders->pluck('discipline')->unique()->filter()->values();
        
        // Get writer profile for subjects
        $writerProfile = WriterProfile::where('user_id', $user->id)->first();
        $userSubjects = $writerProfile ? $writerProfile->subjects : [];
        
        // Get all available subjects from the system
        $allSubjects = [
            'English Literature', 'History', 'Mathematics', 'Physics', 'Chemistry', 
            'Biology', 'Computer Science', 'Economics', 'Business Studies', 'Psychology', 
            'Sociology', 'Political Science', 'Philosophy', 'Law', 'Medicine', 
            'Engineering', 'Architecture', 'Art & Design', 'Music', 'Film Studies',
            'Media Studies', 'Communications', 'Journalism', 'Marketing', 'Management', 
            'Finance', 'Accounting', 'Nursing', 'Education', 'Social Work'
        ];
        
        return view('writers.index', compact('availableOrders', 'disciplines', 'userSubjects', 'allSubjects'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function completedOrders(Request $request)
    {
        // Start with base query for writer's completed orders
        $query = Order::where('writer_id', Auth::id());
        
        // Apply status filter - default to all finished statuses if not specified
        if ($request->has('status') && !empty($request->status)) {
            $query->whereIn('status', $request->status);
        } else {
            $query->whereIn('status', [
                Order::STATUS_COMPLETED, 
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ]);
        }
        
        // Apply date range filter if provided
        if ($request->filled('start_date')) {
            $query->whereDate('updated_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('updated_at', '<=', $request->end_date);
        }
        
        // Apply order ID filter if provided
        if ($request->filled('order_id')) {
            $query->where('id', 'like', '%' . $request->order_id . '%');
        }
        
        // Apply topic title filter if provided
        if ($request->filled('topic')) {
            $query->where('title', 'like', '%' . $request->topic . '%');
        }
        
        // Get completed orders with related data and pagination
        $completedOrders = $query->with(['client', 'payments'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        // Calculate total earnings for all completed orders (not just current page)
        $totalEarningsQuery = Order::where('writer_id', Auth::id())
            ->whereIn('status', [
                Order::STATUS_COMPLETED, 
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ])
            ->with('payments')
            ->get();
            
        $totalEarnings = 0;
        foreach ($totalEarningsQuery as $order) {
            $totalEarnings += $order->payments->where('type', 'writer')->sum('amount');
        }
        
        // Pass data to view
        return view('writers.finished', compact('completedOrders', 'totalEarnings'));
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
            
            // Get client messages for this order
            $clientMessages = Message::where('order_id', $orderId)
                ->where('message_type', 'client')
                ->with(['user', 'receiver', 'files'])
                ->orderBy('created_at')
                ->get();
                
            // Get support messages for this order
            $supportMessages = Message::where('order_id', $orderId)
                ->where('message_type', 'support')
                ->with(['user', 'receiver', 'files'])
                ->orderBy('created_at')
                ->get();
            
            // Count unread client messages
            $clientUnreadCount = $clientMessages
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();
                
            // Count unread support messages
            $supportUnreadCount = $supportMessages
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();
            
            // Mark unread messages as read
            Message::where('order_id', $orderId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
                
            // Determine other user (client or support)
            $clientUser = User::find($order->client_id);
            $supportUser = User::where('usertype', 'admin')
                ->orWhere('usertype', 'support')
                ->first();
            
            // Get message threads for the main messages view (sidebar)
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
            
            return view('writers.messages', compact(
                'messageThreads', 
                'userOrders', 
                'users', 
                'clientMessages', 
                'supportMessages', 
                'order', 
                'clientUser', 
                'supportUser',
                'clientUnreadCount',
                'supportUnreadCount', 
                'type'
            ));
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
     * Get messages list for AJAX updates without reloading the page
     *
     * @return \Illuminate\Http\Response
     */
    public function getMessagesList()
    {
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
        
        // Render only the message list part
        $html = view('writers.partials.message-list', compact('messageThreads'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Search messages by order ID or content
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchMessages(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->query('search');
        $messageType = $request->query('type', 'all');
        
        // Build the query based on search parameters
        $query = Message::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            });
        
        // Apply message type filter if not "all"
        if ($messageType !== 'all') {
            $query->where('message_type', $messageType);
        }
        
        // Apply search term if provided
        if ($searchTerm) {
            // If search term is numeric, it might be an order ID
            if (is_numeric($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('order_id', $searchTerm)
                        ->orWhere('message', 'LIKE', "%{$searchTerm}%");
                });
            } else {
                // Otherwise search in message content
                $query->where('message', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('title', 'LIKE', "%{$searchTerm}%");
            }
        }
        
        // Get messages with relationships
        $messages = $query->with(['user', 'receiver', 'order', 'files'])
            ->latest()
            ->get();
        
        // Group messages by thread
        $messageThreads = $messages->groupBy(function($message) {
                if ($message->is_general) {
                    return 'general_' . $message->title . '_' . 
                        ($message->user_id == Auth::id() ? $message->receiver_id : $message->user_id);
                } else {
                    return 'order_' . ($message->order_id ?? 0);
                }
            })
            ->map(function($messages) {
                $latest = $messages->first();
                $latest->thread_messages_count = $messages->count();
                $latest->unread_count = $messages
                    ->where('receiver_id', Auth::id())
                    ->whereNull('read_at')
                    ->count();
                    
                return $latest;
            });
        
        // Render the message list with search results
        $html = view('writers.partials.message-list', compact('messageThreads'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $messageThreads->count()
        ]);
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
                $filePath = $file->storeAs('message_attachments/' . $message->id, $fileName, 'public');
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                
                $fileModel = new File([
                    'name' => $file->getClientOriginalName(),
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                    'uploaded_by' => Auth::id(),
                    'uploader_type' => 'WRITER',
                    'description' => $request->input('attachment_descriptions.' . $index, null),
                    'fileable_id' => $message->id,
                    'fileable_type' => get_class($message)
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
            
            // Check if user can reply (should be assigned or have placed a bid)
            $isAssigned = $order->writer_id == $user->id;
            $hasBid = Bid::where('order_id', $orderId)->where('user_id', $user->id)->exists();
            
            if (!$isAssigned && !$hasBid && $user->usertype !== 'admin' && $user->usertype !== 'support') {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot send messages for this order.'
                    ], 403);
                }
                return back()->with('error', 'You cannot send messages for this order.');
            }
            
            // Determine receiver based on message type
            $receiverId = null;
            $messageType = $request->message_type ?? 'client'; // Default to client if not specified
            
            if ($messageType === 'client') {
                $receiverId = $order->client_id;
            } else {
                // Find a support or admin user
                $supportUser = User::where('usertype', 'admin')
                    ->orWhere('usertype', 'support')
                    ->first();
                    
                if ($supportUser) {
                    $receiverId = $supportUser->id;
                } else {
                    throw new \Exception("No support staff available to receive the message");
                }
            }
                
            // Create the reply
            $message = new Message();
            $message->user_id = $user->id;
            $message->receiver_id = $receiverId;
            $message->order_id = $orderId;
            $message->is_general = false;
            $message->message = $request->message;
            $message->message_type = $messageType;
            $message->title = "Order #" . $orderId . " Message";
            $message->save();
        } else {
            // This is a general message
            $title = $parts[1];
            $otherUserId = $parts[2];
            
            // Determine message type based on receiver
            $receiver = User::find($otherUserId);
            $messageType = $receiver->usertype === 'client' ? 'client' : 'support';
            
            // Create the reply
            $message = new Message();
            $message->user_id = $user->id;
            $message->receiver_id = $otherUserId;
            $message->title = $title;
            $message->is_general = true;
            $message->message = $request->message;
            $message->message_type = $messageType;
            $message->save();
        }
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('message_attachments/' . $message->id, $fileName, 'public');
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                
                $fileModel = new File([
                    'name' => $file->getClientOriginalName(),
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                    'uploaded_by' => Auth::id(),
                    'uploader_type' => 'WRITER',
                    'description' => $request->input('attachment_descriptions.' . $index, null),
                    'fileable_id' => $message->id,
                    'fileable_type' => get_class($message)
                ]);
                
                $message->files()->save($fileModel);
            }
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('files')
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
     * Display user finance page with earnings data
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userFinance()
    {
        $user = Auth::user();
        
        // Load writer profile for payment details
        $user->load('writerProfile');
        
        // Get completed orders with payment information
        $completedOrders = Order::where('writer_id', $user->id)
            ->whereIn('status', [
                Order::STATUS_COMPLETED, 
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ])
            ->with(['client'])
            ->latest()
            ->get();
        
        // Calculate total earnings and map order data for the view
        $totalEarnings = 0;
        $unRequestedEarnings = $completedOrders->map(function($order) use (&$totalEarnings) {
            // For this example, we're using 70% of order price as writer payment
            $writerAmount = $order->price * 0.70;
            $totalEarnings += $writerAmount;
            
            return [
                'id' => $order->id,
                'date' => $order->updated_at->format('d M Y, g:i A'),
                'title' => $order->title,
                'client_name' => $order->client->name ?? 'Unknown Client',
                'transaction_type' => 'order completed (' . ($order->task_size ?? '0') . ' pages)',
                'description' => $order->type_of_service ?? $order->title,
                'amount' => $writerAmount
            ];
        });
        
        // For this example, we'll simulate some fines (in a real app, these would come from a fines table)
        $fines = []; // Get actual fines from your database if applicable
        $totalFines = 0; // Calculate total fines
        
        // Get withdrawal history - in a real app, these would come from your Finance model
        $withdrawalHistory = []; // Get actual withdrawal history from your database
        $totalWithdrawn = 0; // Calculate total withdrawn amount
        
        // Calculate available balance
        $availableBalance = $totalEarnings - $totalFines - $totalWithdrawn;
        
        return view('writers.finance', compact(
            'user',
            'unRequestedEarnings',
            'totalEarnings',
            'fines',
            'totalFines',
            'withdrawalHistory',
            'totalWithdrawn',
            'availableBalance'
        ));
    }

    /**
     * Request withdrawal of earnings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|string|in:mpesa,bank,paypal',
        ]);
        
        $user = Auth::user();
        
        try {
            // Check if user has sufficient balance
            $availableBalance = \App\Models\Finance::getAvailableBalance($user->id);
            
            if ($availableBalance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance. Your available balance is $' . number_format($availableBalance, 2)
                ], 400);
            }
            
            // Get payment details from writer profile
            $writerProfile = $user->writerProfile;
            if (!$writerProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete your profile with payment information first'
                ], 400);
            }
            
            // Create withdrawal request
            $withdrawal = \App\Models\Finance::requestWithdrawal(
                $user->id,
                $request->amount,
                $request->payment_method,
                $writerProfile->payment_details,
                'Withdrawal request via ' . $request->payment_method
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'withdrawal' => $withdrawal
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error requesting withdrawal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction details for a specific order
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function getOrderTransactions($orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('writer_id', $user->id)
            ->with(['client', 'financialTransactions' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->firstOrFail();
        
        $transactions = $order->financialTransactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->created_at,
                'type' => $transaction->transaction_type,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'description' => $transaction->description
            ];
        });
        
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'title' => $order->title,
                'client_name' => $order->client->name ?? 'Unknown Client',
                'completed_at' => $order->updated_at,
                'total_amount' => $order->financialTransactions->sum('amount')
            ],
            'transactions' => $transactions
        ]);
    }

    /**
     * Process a payment request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|string|in:mpesa,bank,paypal',
        ]);
        
        $user = Auth::user();
        
        try {
            // In a real-world application, you would:
            // 1. Verify the user has sufficient balance
            // 2. Create a withdrawal record in your finance table
            // 3. Update any related fields
            
            // For this example, we'll just return a success response
            return response()->json([
                'success' => true,
                'message' => 'Payment request submitted successfully. Your request will be processed shortly.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error requesting payment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter finance transactions by date range
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterFinanceTransactions(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|string|in:all,order_payment,withdrawal_request'
        ]);
        
        $user = Auth::user();
        
        // Build the query
        $query = \App\Models\Finance::where('user_id', $user->id);
        
        // Apply date filters if provided
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Apply transaction type filter if provided
        if ($request->filled('transaction_type') && $request->transaction_type !== 'all') {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        // Get results
        $transactions = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->created_at,
                    'type' => $transaction->transaction_type,
                    'amount' => $transaction->amount,
                    'balance_after' => $transaction->balance_after,
                    'status' => $transaction->status,
                    'description' => $transaction->description
                ];
            });
        
        // Calculate totals for the filtered data
        $totalEarnings = $transactions->where('type', 'order_payment')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal_request')->sum('amount');
        
        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'totalEarnings' => $totalEarnings,
            'totalWithdrawals' => abs($totalWithdrawals), // Make positive for display
            'netAmount' => $totalEarnings + $totalWithdrawals // Will be correct since withdrawals are negative
        ]);
    }

    /**
     * Display user profile page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Eager load the writer profile
        $user->load('writerProfile');
        
        return view('writers.profile', [
            'user' => $user
        ]);
    }
    
    /**
     * Update user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ProfileUpdate(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:15',
            'national_id' => 'required|string|max:20',
            'native_language' => 'required|string',
            'experience_years' => 'required|integer|min:0|max:30',
            'subjects' => 'required|array|min:2|max:5',
            'subjects.*' => 'string',
            'bio' => 'required|string|min:100|max:1000',
            'country' => 'required|string|max:100',
            'county' => 'required|string|max:100',
            'night_calls' => 'required|boolean',
            'force_assign' => 'required|boolean',
            'linkedin' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'payment_method' => 'required|string|in:mpesa,bank,paypal',
            'payment_details' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Start a database transaction
            DB::beginTransaction();
            
            // Update user's bio
            $user->bio = $request->bio;
            $user->save();
            
            // Find or create writer profile
            $profile = WriterProfile::firstOrNew(['user_id' => $user->id]);
            
            // Generate writer_id if it doesn't exist
            if (!$profile->writer_id) {
                $profile->writer_id = WriterProfile::generateWriterId($user->id);
            }
            
            // Update profile data
            $profile->phone_number = $request->phone_number;
            $profile->national_id = $request->national_id;
            $profile->country = $request->country;
            $profile->county = $request->county;
            $profile->native_language = $request->native_language;
            $profile->experience_years = $request->experience_years;
            $profile->subjects = $request->subjects;
            $profile->bio = $request->bio;
            $profile->night_calls = $request->night_calls;
            $profile->force_assign = $request->force_assign;
            $profile->linkedin = $request->linkedin;
            $profile->facebook = $request->facebook;
            $profile->payment_method = $request->payment_method;
            $profile->payment_details = $request->payment_details;
            
            // Save the profile
            $profile->save();
            
            // Commit the transaction
            DB::commit();
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
            
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            
            // Log the error
            Log::error('Error updating profile: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the user's status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,vacation,inactive'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value'
            ], 422);
        }
        
        try {
            $user = Auth::user();
            $user->status = $request->status;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your status'
            ], 500);
        }
    }
    
    /**
     * Upload a profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|max:2048' // 2MB max
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image file'
            ], 422);
        }
        
        try {
            $user = Auth::user();
            
            // Find or create writer profile
            $profile = WriterProfile::firstOrNew(['user_id' => $user->id]);
            
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
                
                // Delete previous file if exists
                if ($profile->profile_picture && Storage::exists('public/profiles/' . $profile->profile_picture)) {
                    Storage::delete('public/profiles/' . $profile->profile_picture);
                }
                
                // Store new file
                $path = $file->storeAs('public/profiles', $fileName);
                
                if (!$path) {
                    throw new \Exception('Failed to upload profile picture');
                }
                
                $profile->profile_picture = $fileName;
                $profile->save();
                
                // Also update user's profile picture field if it exists
                if (in_array('profile_picture', $user->fillable)) {
                    $user->profile_picture = $fileName;
                    $user->save();
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture uploaded successfully',
                    'file_path' => Storage::url('public/profiles/' . $fileName)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No profile picture file found'
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Error uploading profile picture: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading your profile picture'
            ], 500);
        }
    }

    /**
     * Display user statistics dashboard
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        
        // Load writer profile
        $user->load('writerProfile');
        
        // Calculate writer tenure
        $registrationDate = $user->created_at;
        $now = Carbon::now();
        $diffInMonths = $registrationDate->diffInMonths($now);
        $years = floor($diffInMonths / 12);
        $months = $diffInMonths % 12;
        
        // Format registration date for display
        $formattedRegistrationDate = $registrationDate->format('M Y');
        
        // Get total completed orders lifetime
        $totalLifetimeOrders = Order::where('writer_id', $user->id)
            ->whereIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ])
            ->count();
        
        // Get default date range (last 30 days)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(30);
        
        // If request has date parameters, use them instead
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        // Get recent completed orders
        $completedOrders = Order::where('writer_id', $user->id)
            ->whereIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();
        
        // Get top disciplines from completed orders
        $disciplineCount = [];
        foreach ($completedOrders as $order) {
            if (!empty($order->discipline)) {
                if (!isset($disciplineCount[$order->discipline])) {
                    $disciplineCount[$order->discipline] = 0;
                }
                $disciplineCount[$order->discipline]++;
            }
        }
        
        // Sort disciplines by count and take top 10
        arsort($disciplineCount);
        $topDisciplines = array_slice($disciplineCount, 0, 10, true);
        
        $totalOrders = $completedOrders->count();
        
        // Calculate statistics for completed orders
        $totalCompletedOrders = $completedOrders->count();
        $totalPages = $completedOrders->sum('task_size');
        
        // Calculate late orders (where deadline is before completion date)
        $totalLateOrders = $completedOrders->filter(function($order) {
            return $order->deadline && $order->updated_at && $order->deadline < $order->updated_at;
        })->count();
        
        // Get dispute orders
        $totalDisputes = Order::where('writer_id', $user->id)
            ->where('status', Order::STATUS_DISPUTE)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        
        // Calculate percentages
        $latenessPercentage = $totalCompletedOrders > 0 ? round(($totalLateOrders / $totalCompletedOrders) * 100, 1) : 0;
        $disputesPercentage = $totalCompletedOrders > 0 ? round(($totalDisputes / $totalCompletedOrders) * 100, 1) : 0;
        
        // Get monthly statistics for chart
        $monthlyData = $this->getMonthlyOrdersData($user->id, $startDate, $endDate);
        
        // Calculate writer rating
        $writerRating = $this->calculateWriterRating($user);
        
        return view('writers.statistics', [
            'user' => $user,
            'years' => $years,
            'months' => $months,
            'registrationDate' => $formattedRegistrationDate,
            'totalLifetimeOrders' => $totalLifetimeOrders,
            'topDisciplines' => $topDisciplines,
            'totalOrders' => $totalOrders,
            'totalCompletedOrders' => $totalCompletedOrders,
            'totalPages' => $totalPages,
            'totalLateOrders' => $totalLateOrders,
            'totalDisputes' => $totalDisputes,
            'latenessPercentage' => $latenessPercentage,
            'disputesPercentage' => $disputesPercentage,
            'monthlyData' => $monthlyData,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'currentRating' => $writerRating['currentRating'],
            'possibleRating' => $writerRating['possibleRating'],
            'qualityLevel' => $writerRating['qualityLevel'],
            'possibleQualityLevel' => $writerRating['possibleQualityLevel'],
            'workDuration' => $writerRating['workDuration'],
            'disputeCount' => $writerRating['disputeCount'],
            'possibleDisputeCount' => $writerRating['possibleDisputeCount'],
            'latenessCount' => $writerRating['latenessCount'],
            'possibleLatenessCount' => $writerRating['possibleLatenessCount'],
            'requestCount' => $writerRating['requestCount'],
            'possibleRequestCount' => $writerRating['possibleRequestCount']
        ]);
    }

    /**
     * Get monthly orders statistics
     *
     * @param int $writerId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getMonthlyOrdersData($writerId, $startDate, $endDate)
    {
        // Create a period from start date to end date by months
        $period = new \DatePeriod(
            $startDate->copy()->startOfMonth(),
            new \DateInterval('P1M'),
            $endDate->copy()->endOfMonth()->modify('+1 day')
        );
        
        $monthlyData = [];
        
        foreach ($period as $date) {
            $monthStart = $date->format('Y-m-01');
            $monthEnd = $date->format('Y-m-t');
            
            // Get orders for this month
            $orders = Order::where('writer_id', $writerId)
                ->whereIn('status', [
                    Order::STATUS_COMPLETED,
                    Order::STATUS_PAID,
                    Order::STATUS_FINISHED
                ])
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->get();
            
            // Count late orders
            $lateOrders = $orders->filter(function($order) {
                return $order->deadline && $order->updated_at && $order->deadline < $order->updated_at;
            })->count();
            
            // Count disputes
            $disputes = Order::where('writer_id', $writerId)
                ->where('status', Order::STATUS_DISPUTE)
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'orders' => $orders->count(),
                'pages' => $orders->sum('task_size'),
                'late_orders' => $lateOrders,
                'disputes' => $disputes
            ];
        }
        
        return $monthlyData;
    }

    /**
     * Calculate writer's rating
     *
     * @param User $user
     * @return array
     */
    private function calculateWriterRating($user)
    {
        // Work duration points
        $monthsWorking = Carbon::parse($user->created_at)->diffInMonths(Carbon::now());
        $workDurationPoints = 0;
        
        if ($monthsWorking >= 18) {
            $workDurationPoints = 20;
        } elseif ($monthsWorking >= 13) {
            $workDurationPoints = 15;
        } elseif ($monthsWorking >= 9) {
            $workDurationPoints = 10;
        } elseif ($monthsWorking >= 5) {
            $workDurationPoints = 5;
        }
        
        // Last 3 months period
        $lastThreeMonths = Carbon::now()->subMonths(3);
        
        // Get completed orders in last 3 months
        $recentOrders = Order::where('writer_id', $user->id)
            ->whereIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ])
            ->where('updated_at', '>=', $lastThreeMonths)
            ->get();
        
        // Count late orders by comparing deadline with completion date
        $lateOrdersCount = $recentOrders->filter(function($order) {
            return $order->deadline && $order->updated_at && $order->deadline < $order->updated_at;
        })->count();
        
        $latenessPoints = 0;
        if ($lateOrdersCount === 0) {
            $latenessPoints = 20;
        } elseif ($lateOrdersCount === 1) {
            $latenessPoints = 10;
        }
        
        // Disputes count (last 3 months)
        $disputesCount = Order::where('writer_id', $user->id)
            ->where('status', Order::STATUS_DISPUTE)
            ->where('updated_at', '>=', $lastThreeMonths)
            ->count();
        
        $disputesPoints = 0;
        if ($disputesCount === 0) {
            $disputesPoints = 20;
        } elseif ($disputesCount === 1) {
            $disputesPoints = 10;
        }
        
        // Request count - check for repeat clients as a proxy for requests
        $clientIds = $recentOrders->pluck('client_id')->toArray();
        $repeatClientCounts = array_count_values(array_filter($clientIds)); // Filter out null values
        $requestCount = count(array_filter($repeatClientCounts, function($count) {
            return $count > 1; // Client has ordered more than once from this writer
        }));
        
        $requestPoints = 0;
        if ($requestCount >= 3) {
            $requestPoints = 20;
        } elseif ($requestCount === 2) {
            $requestPoints = 10;
        } elseif ($requestCount === 1) {
            $requestPoints = 5;
        }
        
        // Quality level from writer profile or default
        $qualityLevel = $user->writerProfile->quality_level ?? 'Undergrad. (yrs. 3-4) B';
        
        // Map quality level to points (will use a more detailed approach in production)
        $levelPoints = [
            'High School D' => 0,
            'High School C' => 10,
            'High School B' => 20,
            'High School A' => 30,
            'Undergrad. (yrs. 1-2) D' => 10,
            'Undergrad. (yrs. 1-2) C' => 20,
            'Undergrad. (yrs. 1-2) B' => 30,
            'Undergrad. (yrs. 1-2) A' => 40,
            'Undergrad. (yrs. 3-4) D' => 40,
            'Undergrad. (yrs. 3-4) C' => 50,
            'Undergrad. (yrs. 3-4) B' => 60,
            'Undergrad. (yrs. 3-4) A' => 70,
            'Graduate D' => 60,
            'Graduate C' => 70,
            'Graduate B' => 80,
            'Graduate A' => 90,
            'PhD D' => 70,
            'PhD C' => 80,
            'PhD B' => 90,
            'PhD A' => 100,
        ];
        
        $qualityPoints = $levelPoints[$qualityLevel] ?? 60; // Default to Undergrad 3-4 B if not found
        
        // Calculate total rating
        $totalPoints = $qualityPoints + $workDurationPoints + $disputesPoints + $requestPoints + $latenessPoints;
        $currentRating = $totalPoints; // Max is 100
        
        // Calculate possible rating (with optimal performance)
        $possibleQualityLevel = $qualityLevel; // Quality level stays the same
        $possibleDisputeCount = 0;
        $possibleLatenessCount = 0;
        $possibleRequestCount = 3; // Optimal is 3+
        
        $possibleDisputesPoints = 20; // 0 disputes
        $possibleLatenessPoints = 20; // 0 lateness
        $possibleRequestsPoints = 20; // 3+ requests
        
        $possibleTotalPoints = $qualityPoints + $workDurationPoints + $possibleDisputesPoints + $possibleRequestsPoints + $possibleLatenessPoints;
        $possibleRating = $possibleTotalPoints; // Max is 100
        
        return [
            'currentRating' => $currentRating,
            'possibleRating' => $possibleRating,
            'qualityLevel' => $qualityLevel,
            'possibleQualityLevel' => $possibleQualityLevel,
            'workDuration' => $monthsWorking,
            'disputeCount' => $disputesCount,
            'possibleDisputeCount' => $possibleDisputeCount,
            'latenessCount' => $lateOrdersCount,
            'possibleLatenessCount' => $possibleLatenessCount,
            'requestCount' => $requestCount,
            'possibleRequestCount' => $possibleRequestCount
        ];
    }

    /**
     * Get disciplines statistics as JSON for AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisciplinesStats(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        
        // Get completed orders for the date range
        $completedOrders = Order::where('writer_id', Auth::id())
            ->whereIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();
        
        // Group orders by discipline
        $disciplines = [];
        
        // Count disciplines
        $disciplineCount = [];
        foreach ($completedOrders as $order) {
            if (!empty($order->discipline)) {
                if (!isset($disciplineCount[$order->discipline])) {
                    $disciplineCount[$order->discipline] = 0;
                }
                $disciplineCount[$order->discipline]++;
            }
        }
        
        // Format data for response
        foreach ($disciplineCount as $discipline => $count) {
            $disciplines[] = [
                'discipline' => $discipline,
                'count' => $count
            ];
        }
        
        // Sort by count (highest first) and take top 10
        usort($disciplines, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        $disciplines = array_slice($disciplines, 0, 10);
        
        return response()->json([
            'success' => true,
            'data' => [
                'disciplines' => $disciplines,
                'total_orders' => $completedOrders->count()
            ]
        ]);
    }

    /**
     * Get orders statistics as JSON for AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrdersStats(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'chart_type' => 'nullable|in:orders,pages,lateness,disputes',
        ]);
        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $chartType = $request->chart_type ?? 'orders';
        
        // Get monthly data
        $monthlyData = $this->getMonthlyOrdersData(Auth::id(), $startDate, $endDate);
        
        return response()->json([
            'success' => true,
            'data' => [
                'months' => $monthlyData,
                'chart_type' => $chartType
            ]
        ]);
    }

    /**
     * Get subjects distribution from writer profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubjectsDistribution(Request $request)
    {
        $user = Auth::user();
        $writerProfile = $user->writerProfile;
        
        // Get subjects from writer profile
        $subjects = [];
        if ($writerProfile && $writerProfile->subjects) {
            $subjects = $writerProfile->subjects;
            
            // If subjects is stored as a JSON string, decode it
            if (is_string($subjects)) {
                $subjects = json_decode($subjects, true) ?: [];
            }
        }
        
        if (empty($subjects)) {
            return response()->json([
                'success' => false,
                'message' => 'No subjects found in writer profile'
            ]);
        }
        
        // Get all completed orders
        $completedOrders = Order::where('writer_id', $user->id)
            ->whereIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_PAID,
                Order::STATUS_FINISHED
            ])
            ->get();
        
        // Initialize subject counts
        $subjectCounts = array_fill_keys($subjects, 0);
        
        // Count orders by subject
        foreach ($completedOrders as $order) {
            if (empty($order->discipline)) continue;
            
            $orderDiscipline = strtolower($order->discipline);
            foreach ($subjects as $subject) {
                if (strpos($orderDiscipline, strtolower($subject)) !== false) {
                    $subjectCounts[$subject]++;
                    break; // Count each order only once
                }
            }
        }
        
        // Format data for response
        $formattedSubjects = [];
        foreach ($subjectCounts as $subject => $count) {
            $formattedSubjects[] = [
                'subject' => $subject,
                'count' => $count
            ];
        }
        
        // Sort by count (highest first)
        usort($formattedSubjects, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'subjects' => $formattedSubjects,
                'total_orders' => $completedOrders->count()
            ]
        ]);
    }
    
      /**
     * Helper function to format file size in human-readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function humanFilesize($bytes, $precision = 2) 
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB']; 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        $bytes /= pow(1024, $pow);
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }

    /**
     * Display assigned order details
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function assignedOrder($id = null)
    {
        // If no ID is provided, redirect to the current orders page
        if (!$id) {
            return redirect()->route('writer.current');
        }
        
        // Find order by ID without restricting to current writer
        // This allows viewing reassigned orders' messages
        $order = Order::with(['client', 'files'])
            ->where('id', $id)
            ->first();
        
        // If order not found, redirect with error
        if (!$order) {
            return redirect()->route('writer.current')
                ->with('error', 'Order not found.');
        }
        
        // Check if current user can view this order
        $isAssigned = $order->writer_id == Auth::id();
        $isAdmin = in_array(Auth::user()->usertype, ['admin', 'support']);
                  
        if (!$isAssigned && !$isAdmin) {
            return redirect()->route('writer.current')
                ->with('error', 'You do not have permission to view this order.');
        }
        
        // Get ALL messages for this order
        $allMessages = Message::where('order_id', $id)
            ->with(['user', 'receiver', 'files'])
            ->orderBy('created_at')
            ->get();
        
        // Group client messages by date
        $clientMessages = $allMessages
            ->filter(function($message) {
                return $message->message_type === 'client';
            })
            ->groupBy(function($message) {
                return Carbon::parse($message->created_at)->format('F d, Y');
            });
        
        // Group support and system messages by date
        $supportMessages = $allMessages
            ->filter(function($message) {
                return in_array($message->message_type, ['admin', 'support', 'system']);
            })
            ->groupBy(function($message) {
                return Carbon::parse($message->created_at)->format('F d, Y');
            });
        
        // Count client unread messages
        $clientUnreadCount = $allMessages
            ->where('message_type', 'client')
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
            
        // Count support and system unread messages
        $supportUnreadCount = $allMessages
            ->whereIn('message_type', ['admin', 'support', 'system'])
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
        
        // Total unread messages for tab display
        $unreadMessages = $clientUnreadCount + $supportUnreadCount;
        
        // Register human_filesize helper function in the view
        view()->share('human_filesize', function($bytes, $precision = 2) {
            return $this->humanFilesize($bytes, $precision);
        });
        
        return view('writers.AssignedOrder', [
            'order' => $order,
            'clientMessages' => $clientMessages,
            'supportMessages' => $supportMessages,
            'clientUnreadCount' => $clientUnreadCount,
            'supportUnreadCount' => $supportUnreadCount,
            'unreadMessages' => $unreadMessages
        ]);
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
            $messageType = $request->input('type', 'all');
            
            // Build query for marking messages as read
            $query = Message::where('order_id', $id)
                ->where('receiver_id', Auth::id())
                ->whereNull('read_at');
                
            // Apply message type filter if specified
            if ($messageType !== 'all') {
                if ($messageType === 'client') {
                    $query->where('message_type', 'client');
                } else if ($messageType === 'support') {
                    $query->whereIn('message_type', ['admin', 'support', 'system']);
                }
            }
            
            // Mark matching messages as read
            $query->update(['read_at' => now()]);
            
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
                $filename = $order->id . '_' . uniqid() . '.' . $extension;
                
                // Store file in public storage for accessibility
                $path = $file->storeAs('order_files', $filename, 'public');
                
                // Get description
                $description = isset($request->descriptions[$index]) ? $request->descriptions[$index] : null;
                
                // Check if this is a completed file
                if ($description === 'completed') {
                    $hasCompletedFile = true;
                }
                
                // Create file record with more complete information
                $fileRecord = File::create([
                    'name' => $originalName,
                    'original_name' => $originalName,
                    'path' => $path,
                    'size' => $size,
                    'mime_type' => $mimeType,
                    'fileable_id' => $order->id,
                    'fileable_type' => get_class($order),
                    'uploaded_by' => Auth::id(),
                    'uploader_type' => 'WRITER',
                    'description' => $description
                ]);
                
                $uploadedFiles[] = $fileRecord;
            }
            
            // Update order status if a completed file was uploaded
            $statusChanged = false;
            if ($hasCompletedFile && !in_array($order->status, [Order::STATUS_DONE, Order::STATUS_DELIVERED])) {
                $order->status = Order::STATUS_DONE;
                $order->save();
                $statusChanged = true;
                
                // Create system message indicating order is complete
                Message::create([
                    'order_id' => $orderId,
                    'user_id' => Auth::id(),
                    'message' => "Order has been marked as DONE. Files have been uploaded and are ready for review.",
                    'message_type' => 'system',
                    'title' => 'Order Completed',
                    'is_general' => false
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
        
        // Log file path for debugging
        Log::info('Attempting to download file: ' . $file->path);
        
        // Check if file exists in storage
        if (!Storage::disk('public')->exists($file->path)) {
            Log::error('File not found in storage: ' . $file->path);
            return back()->with('error', 'File not found in storage');
        }
    
        return Storage::disk('public')->download($file->path, $file->name ?? 'download');
    }

    /**
     * Download multiple files as a zip
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
        $zipPath = storage_path('app/public/temp/' . $zipName);
        
        // Create temp directory if it doesn't exist
        if (!Storage::disk('public')->exists('temp')) {
            Storage::disk('public')->makeDirectory('temp');
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return response()->json(['error' => 'Cannot create zip file'], 500);
        }

        $files = File::whereIn('id', $fileIds)->get();
        
        foreach ($files as $file) {
            // Verify the file exists
            if (Storage::disk('public')->exists($file->path)) {
                $fileContent = Storage::disk('public')->get($file->path);
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
     * Writer confirms order assignment
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function confirmAssignment($id)
    {
        $order = Order::findOrFail($id);
        
        // Check if the order belongs to the current writer
        if ($order->writer_id != Auth::id()) {
            return redirect()->route('writer.current')->with('error', 'You are not assigned to this order.');
        }
        
        // Check if the order is in UNCONFIRMED status
        if ($order->status != Order::STATUS_UNCONFIRMED) {
            return redirect()->route('writer.assigned', $id)->with('error', 'This order cannot be confirmed at this time.');
        }
        
        // Update order status
        $order->status = Order::STATUS_CONFIRMED;
        $order->save();
        
        // Create system message confirming the assignment
        Message::create([
            'order_id' => $id,
            'user_id' => Auth::id(),
            'message' => "Order assignment has been accepted. I will start working on this order immediately.",
            'message_type' => 'system',
            'title' => 'Assignment Confirmed',
            'is_general' => false
        ]);
        
        // Notify admin about the confirmation
        $adminUsers = User::whereIn('usertype', ['admin', 'super_admin', 'support'])->get();
        foreach ($adminUsers as $admin) {
            Message::create([
                'order_id' => $id,
                'user_id' => Auth::id(),
                'receiver_id' => $admin->id,
                'message' => "Writer has accepted the order assignment.",
                'message_type' => 'notification',
                'title' => 'Order Assignment Accepted',
                'is_general' => false
            ]);
        }
        
        return redirect()->route('writer.assigned', $id)
            ->with('success', 'You have successfully accepted this order. You can now start working on it.');
    }

    /**
     * Writer rejects order assignment
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function rejectAssignment($id)
    {
        $order = Order::findOrFail($id);
        
        // Check if the order belongs to the current writer
        if ($order->writer_id != Auth::id()) {
            return redirect()->route('writer.current')
                ->with('error', 'You are not assigned to this order.');
        }
        
        // Check if the order is in UNCONFIRMED status
        if ($order->status != Order::STATUS_UNCONFIRMED) {
            return redirect()->route('writer.assigned', $id)
                ->with('error', 'This order cannot be rejected at this time.');
        }
        
        // Reset order status and writer
        $order->status = Order::STATUS_AVAILABLE;
        $oldWriterId = $order->writer_id;
        $order->writer_id = null;
        $order->save();
        
        // Create system message recording the rejection
        Message::create([
            'order_id' => $id,
            'user_id' => Auth::id(),
            'message' => "Order assignment has been declined by the writer.",
            'message_type' => 'system',
            'title' => 'Assignment Rejected',
            'is_general' => false
        ]);
        
        // Notify admin about the rejection
        $adminUsers = User::whereIn('usertype', ['admin', 'super_admin', 'support'])->get();
        foreach ($adminUsers as $admin) {
            Message::create([
                'order_id' => $id,
                'user_id' => Auth::id(),
                'receiver_id' => $admin->id,
                'message' => "Writer has declined the order assignment. Order is now available again.",
                'message_type' => 'notification',
                'title' => 'Order Assignment Rejected',
                'is_general' => false
            ]);
        }
        
        return redirect()->route('writer.current')
            ->with('info', 'You have declined the order assignment.');
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
     * Send a message for an available order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    private function sendAvailableOrderMessage(Request $request, $orderId)
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
                $filePath = $file->storeAs('order_messages/' . $orderId, $fileName, 'public');
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                
                $fileModel = new File([
                    'name' => $file->getClientOriginalName(),
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                    'uploaded_by' => Auth::id(),
                    'uploader_type' => 'WRITER',
                    'fileable_id' => $message->id,
                    'fileable_type' => get_class($message)
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
                    'message' => $message->load('files', 'user', 'receiver')
                ]);
            }
            
            return back()->with('success', 'Message sent successfully');
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            
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
            'cover_letter' => 'nullable|string|max:1000'
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
            Log::error('Error submitting bid: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to place bid: ' . $e->getMessage()
            ], 500);
        }
    }

    
}