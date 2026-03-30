<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\FileCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $membership = Auth::user()?->activeMembership();
        abort_if(!$membership, 403);

        $photoCategory = FileCategory::where('name', 'photo')->first();
        if (!$photoCategory) {
            return view('gallery.index', ['events' => collect(), 'eventOptions' => []]);
        }

        $eventOptions = Event::whereHas('eventFiles', fn($q) =>
                $q->where('file_category_id', $photoCategory->file_category_id)
            )
            ->whereHas('clubs', fn($q) =>
                $q->where('clubs.club_id', $membership->club_id)
            )
            ->orderBy('start_date', 'desc')
            ->pluck('title', 'event_id')
            ->toArray();

        $events = Event::whereHas('eventFiles', fn($q) =>
                $q->where('file_category_id', $photoCategory->file_category_id)
            )
            ->whereHas('clubs', fn($q) =>
                $q->where('clubs.club_id', $membership->club_id)
            )
            ->when($request->filled('event_id'), fn($q) =>
                $q->where('event_id', $request->input('event_id'))
            )
            ->with(['eventFiles' => fn($q) =>
                $q->where('file_category_id', $photoCategory->file_category_id)
            ])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('gallery.index', compact('events', 'eventOptions'));
    }
}