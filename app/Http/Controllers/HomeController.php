<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
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

class WriterOrderController extends Controller
{
    /**
     * Display the writer's active and completed orders
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function currentOrders()
    {
        $writer = Auth::guard('writer')->user();
        
        // Get active orders (CONFIRMED, UNCONFIRMED)
        $activeOrders = Order::where('writer_id', $writer->id)
            ->whereIn('status', ['CONFIRMED', 'UNCONFIRMED'])
            ->with('customer')
            ->latest()
            ->get();
            
        // Get completed orders (DONE, DELIVERED)
        $completedOrders = Order::where('writer_id', $writer->id)
            ->whereIn('status', ['DONE', 'DELIVERED'])
            ->with('customer')
            ->latest()
            ->get();
        
        // Calculate remaining time for each order
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
        
        return view('writers.current', compact('activeOrders', 'completedOrders'));
    }
    
    /**
     * Display the assigned order details
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAssignedOrder($id)
    {
        $writer = Auth::guard('writer')->user();
        
        $order = Order::with(['customer', 'files'])
            ->where('id', $id)
            ->where('writer_id', $writer->id)
            ->firstOrFail();
        
        // Count unread messages
        $unreadMessages = Message::where('order_id', $order->id)
            ->where('recipient_id', $writer->id)
            ->where('recipient_type', 'WRITER')
            ->where('is_read', false)
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
     * Mark messages as read
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markMessagesRead(Request $request, $id)
    {
        $writer = Auth::guard('writer')->user();
        
        try {
            Message::where('order_id', $id)
                ->where('recipient_id', $writer->id)
                ->where('recipient_type', 'WRITER')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
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
     * Send a message
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'content' => 'required_without:attachment|nullable|string',
            'attachment' => 'nullable|file|max:20000' // 20MB max
        ]);
        
        $writer = Auth::guard('writer')->user();
        
        $order = Order::where('id', $request->order_id)
            ->where('writer_id', $writer->id)
            ->firstOrFail();
        
        try {
            // Check for forbidden keywords
            $forbiddenKeywords = ['dollar', 'money', 'pay', 'shillings', 'cash', 'price', 'payment'];
            $messageText = strtolower($request->content);
            $foundKeywords = [];
            
            foreach ($forbiddenKeywords as $keyword) {
                if (strpos($messageText, $keyword) !== false) {
                    $foundKeywords[] = $keyword;
                }
            }
            
            if (!empty($foundKeywords)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your message contains prohibited keywords: ' . implode(', ', $foundKeywords) . '. Please avoid payment discussions.'
                ], 400);
            }
            
            // Create message
            $message = Message::create([
                'order_id' => $order->id,
                'content' => $request->content,
                'sender_id' => $writer->id,
                'sender_type' => 'WRITER',
                'recipient_id' => $order->customer_id,
                'recipient_type' => 'CUSTOMER',
                'is_read' => false
            ]);
            
            // Handle attachment if present
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $size = $file->getSize();
                $mimeType = $file->getMimeType();
                
                // Generate unique filename
                $filename = 'msg_' . $message->id . '_' . uniqid() . '.' . $extension;
                
                // Store file in storage
                $path = $file->storeAs('message_attachments', $filename, 'public');
                
                // Create file record
                $fileRecord = File::create([
                    'order_id' => $order->id,
                    'message_id' => $message->id,
                    'path' => $path,
                    'original_name' => $originalName,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'description' => 'Message Attachment',
                    'uploader_id' => $writer->id,
                    'uploader_type' => 'WRITER'
                ]);
                
                // Update message with attachment info
                $message->has_attachment = true;
                $message->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'formatted_time' => Carbon::parse($message->created_at)->format('h:i A')
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }
    
    /**
     * Extend order deadline
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function extendDeadline(Request $request, $id)
    {
        $request->validate([
            'extension_hours' => 'required|integer|min:1|max:24',
            'reason' => 'required|string|min:10'
        ]);
        
        $writer = Auth::guard('writer')->user();
        
        $order = Order::where('id', $id)
            ->where('writer_id', $writer->id)
            ->where('status', '!=', 'DONE')
            ->firstOrFail();
        
        try {
            // Update deadline
            $currentDeadline = Carbon::parse($order->deadline);
            $newDeadline = $currentDeadline->addHours($request->extension_hours);
            
            $order->deadline = $newDeadline;
            $order->save();
            
            // Create system message about extension
            Message::create([
                'order_id' => $order->id,
                'content' => "Deadline has been extended by {$request->extension_hours} hours. Reason: {$request->reason}",
                'sender_id' => $writer->id,
                'sender_type' => 'WRITER',
                'recipient_id' => $order->customer_id,
                'recipient_type' => 'CUSTOMER',
                'is_read' => false
            ]);
            
            // Create actual message from writer
            Message::create([
                'order_id' => $order->id,
                'content' => $request->reason,
                'sender_id' => $writer->id,
                'sender_type' => 'WRITER',
                'recipient_id' => $order->customer_id,
                'recipient_type' => 'CUSTOMER',
                'is_read' => false
            ]);
            
            return response()->json([
                'success' => true,
                'new_deadline' => $newDeadline,
                'message' => 'Deadline extended successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error extending deadline: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to extend deadline'
            ], 500);
        }
    }
    
    /**
     * Reassign order to another writer
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reassignOrder(Request $request, $id)
    {
        $writer = Auth::guard('writer')->user();
        
        $order = Order::where('id', $id)
            ->where('writer_id', $writer->id)
            ->where('status', '!=', 'DONE')
            ->firstOrFail();
        
        try {
            // Update order status to pending reassignment
            $order->status = 'PENDING_REASSIGNMENT';
            $order->save();
            
            // Create system message about reassignment
            Message::create([
                'order_id' => $order->id,
                'content' => "Writer has requested order reassignment.",
                'sender_id' => $writer->id,
                'sender_type' => 'WRITER',
                'recipient_id' => 0, // System/Support
                'recipient_type' => 'SUPPORT',
                'is_read' => false
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order reassignment requested successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error reassigning order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to request reassignment'
            ], 500);
        }
    }
    
    /**
     * Upload files for an order
     *
     * @param  \Illuminate\Http\Request  $request
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
        
        $writer = Auth::guard('writer')->user();
        
        $order = Order::where('id', $request->order_id)
            ->where('writer_id', $writer->id)
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
                    'uploader_id' => $writer->id,
                    'uploader_type' => 'WRITER'
                ]);
                
                $uploadedFiles[] = $fileRecord;
            }
            
            // If a completed file was uploaded, mark order as DONE
            $statusChanged = false;
            if ($hasCompletedFile && in_array($order->status, ['CONFIRMED', 'ON_REVISION'])) {
                $order->status = 'DONE';
                $order->save();
                $statusChanged = true;
                
                // Create system message
                Message::create([
                    'order_id' => $order->id,
                    'content' => "Writer has marked this order as completed.",
                    'sender_id' => $writer->id,
                    'sender_type' => 'WRITER',
                    'recipient_id' => $order->customer_id,
                    'recipient_type' => 'CUSTOMER',
                    'is_read' => false
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($id)
    {
        $writer = Auth::guard('writer')->user();
        
        $file = File::findOrFail($id);
        
        // Check if writer has access to this file
        $order = Order::where('id', $file->order_id)
            ->where(function($query) use ($writer) {
                $query->where('writer_id', $writer->id)
                    ->orWhereHas('bids', function($q) use ($writer) {
                        $q->where('user_id', $writer->id);
                    });
            })
            ->firstOrFail();
        
        try {
            if (!Storage::disk('public')->exists($file->path)) {
                throw new \Exception("File not found: {$file->path}");
            }
            
            return Storage::disk('public')->download($file->path, $file->original_name);
        } catch (\Exception $e) {
            Log::error('Error downloading file: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'File not found or cannot be downloaded'
            ], 404);
        }
    }
    
    /**
     * Download multiple files as a ZIP archive
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadMultipleFiles(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'required|exists:files,id'
        ]);
        
        $writer = Auth::guard('writer')->user();
        $fileIds = $request->file_ids;
        
        try {
            // Get all files and verify access
            $files = File::whereIn('id', $fileIds)->get();
            
            if ($files->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No files found'], 404);
            }
            
            // Check if writer has access to all files
            foreach ($files as $file) {
                $order = Order::where('id', $file->order_id)
                    ->where(function($query) use ($writer) {
                        $query->where('writer_id', $writer->id)
                            ->orWhereHas('bids', function($q) use ($writer) {
                                $q->where('user_id', $writer->id);
                            });
                    })
                    ->first();
                
                if (!$order) {
                    return response()->json(['success' => false, 'message' => 'Access denied to one or more files'], 403);
                }
            }
            
            // Create temporary zip file
            $zipFileName = 'order_files_' . time() . '.zip';
            $zipFilePath = storage_path('app/temp/' . $zipFileName);
            
            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $zip = new ZipArchive();
            
            if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {
                throw new \Exception("Could not create ZIP file");
            }
            
            // Add each file to the zip
            foreach ($files as $file) {
                if (!Storage::disk('public')->exists($file->path)) {
                    continue; // Skip files that don't exist
                }
                
                $filePath = Storage::disk('public')->path($file->path);
                $zip->addFile($filePath, $file->original_name);
            }
            
            $zip->close();
            
            // Return the zip file
            return response()->download($zipFilePath, 'order_files.zip')->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error creating download ZIP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create download package'
            ], 500);
        }
    }
}