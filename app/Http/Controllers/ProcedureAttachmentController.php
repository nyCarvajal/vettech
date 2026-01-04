<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcedureAttachmentRequest;
use App\Models\Procedure;
use App\Models\ProcedureAttachment;
use App\Models\ProcedureEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProcedureAttachmentController extends Controller
{
    public function store(StoreProcedureAttachmentRequest $request, Procedure $procedure): RedirectResponse
    {
        $data = $request->validated();
        $file = $request->file('file');
        $path = $file->store('public/procedure-attachments');

        $attachment = $procedure->attachments()->create([
            'title' => $data['title'],
            'file_path' => $path,
            'mime' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        ProcedureEvent::create([
            'procedure_id' => $procedure->id,
            'event_type' => 'attachment_added',
            'payload' => ['attachment_id' => $attachment->id],
            'created_by' => Auth::id(),
        ]);

        return back()->with('status', 'Adjunto cargado');
    }

    public function destroy(Procedure $procedure, ProcedureAttachment $attachment): RedirectResponse
    {
        Storage::delete($attachment->file_path);
        $attachment->delete();

        ProcedureEvent::create([
            'procedure_id' => $procedure->id,
            'event_type' => 'attachment_deleted',
            'payload' => ['attachment_id' => $attachment->id],
            'created_by' => Auth::id(),
        ]);

        return back()->with('status', 'Adjunto eliminado');
    }
}
