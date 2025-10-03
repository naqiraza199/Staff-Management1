<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class ClientDocumentController extends Controller
{
     public function show($token)
    {
        $document = Document::where('signature_token', $token)->firstOrFail();
        return view('documents.sign', compact('document'));
    }

    public function store(Request $request, $token)
    {
        $document = Document::where('signature_token', $token)->firstOrFail();

        $request->validate([
            'signature' => 'required|string',
        ]);

        $document->update([
            'signature'   => $request->signature, 
            'signed_at'   => now(),
            'is_verified' => true,
        ]);

        return redirect()->route('documents.sign', $token)
            ->with('success', 'Thank you! Document signed successfully.');
    }

    public function demo()
    {
        return view('documents.demo');
    }
}
