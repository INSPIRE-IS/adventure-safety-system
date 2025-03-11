<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Jobs\SendMessageJob;
use App\Models\BulkMessage;
use Illuminate\Http\Request;

class BulkMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipients' => 'required|array', // Expecting an array of emails
            'recipients.*' => 'email',
        ]);

        $message = BulkMessage::create([
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        // Dispatch the job
        dispatch(new SendMessageJob($message, $request->recipients));

        return response()->json(['message' => 'Message queued for sending.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(BulkMessage $bulkMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BulkMessage $bulkMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BulkMessage $bulkMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BulkMessage $bulkMessage)
    {
        //
    }
}
