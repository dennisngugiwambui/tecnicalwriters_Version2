<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Download a file for admin users
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function adminDownload($id)
    {
        $file = File::findOrFail($id);
        
        // Check if file exists in storage
        if (!Storage::disk('public')->exists($file->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found in storage'
            ], 404);
        }
        
        // Return file download
        return Storage::disk('public')->download($file->path, $file->original_name);
    }
    
    /**
     * Download multiple files as a ZIP for admin users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminDownloadMultiple(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:files,id'
        ]);
        
        return $this->createAndDownloadZip($request->file_ids);
    }
    
    /**
     * Download a file for writer users
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function writerDownload($id)
    {
        $file = File::findOrFail($id);
        
        // Check user permissions - writer can only download files from orders assigned to them
        $order = $file->fileable_type === 'App\Models\Order' ? $file->fileable : null;
        
        if (!$order || ($order->writer_id != Auth::id() && Auth::user()->usertype !== 'admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to download this file'
            ], 403);
        }
        
        // Check if file exists in storage
        if (!Storage::disk('public')->exists($file->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found in storage'
            ], 404);
        }
        
        // Return file download
        return Storage::disk('public')->download($file->path, $file->original_name);
    }
    
    /**
     * Download multiple files as a ZIP for writer users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function writerDownloadMultiple(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:files,id'
        ]);
        
        // Verify that the writer has access to all the files
        $files = File::whereIn('id', $request->file_ids)->get();
        
        foreach ($files as $file) {
            $order = $file->fileable_type === 'App\Models\Order' ? $file->fileable : null;
            
            if (!$order || ($order->writer_id != Auth::id() && Auth::user()->usertype !== 'admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to download one or more of these files'
                ], 403);
            }
        }
        
        return $this->createAndDownloadZip($request->file_ids);
    }
    
    /**
     * Create and download a ZIP file of the specified files
     *
     * @param  array  $fileIds
     * @return \Illuminate\Http\Response
     */
    private function createAndDownloadZip($fileIds)
    {
        // Get the files
        $files = File::whereIn('id', $fileIds)->get();
        
        // Create a temporary zip file
        $zipFileName = 'order-files-' . time() . '.zip';
        $tempPath = storage_path('app/public/temp');
        
        // Create temp directory if it doesn't exist
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
        
        $zipPath = $tempPath . '/' . $zipFileName;
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create zip file'
            ], 500);
        }
        
        // Add files to the zip
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->path)) {
                $fileContent = Storage::disk('public')->get($file->path);
                $zip->addFromString($file->original_name, $fileContent);
            }
        }
        
        $zip->close();
        
        // Return the zip file
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}