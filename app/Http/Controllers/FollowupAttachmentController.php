<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFollowupAttachmentRequest;
use App\Models\Followup;
use App\Models\FollowupAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FollowupAttachmentController extends Controller
{
    public function store(StoreFollowupAttachmentRequest $request, Followup $followup): RedirectResponse
    {
        $data = $request->validated();
        $file = $request->file('file');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $path = $file->storeAs('followups', $filename . '-' . time() . '.' . $file->getClientOriginalExtension(), 'public');

        $followup->attachments()->create([
            'title' => trim($data['title']),
            'file_path' => $path,
            'mime' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('status', 'Adjunto cargado');
    }

    public function destroy(Followup $followup, FollowupAttachment $attachment): RedirectResponse
    {
        $this->authorize('addAttachment', $followup);

        if ($attachment->followup_id !== $followup->id) {
            abort(404);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('status', 'Adjunto eliminado');
    }
}
