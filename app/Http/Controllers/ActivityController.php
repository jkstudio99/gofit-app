<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of all active activities.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activities = Activity::where('status', 'active')
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->paginate(12);

        return view('activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new activity.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('activities.create');
    }

    /**
     * Store a newly created activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'registration_deadline' => 'required|date|before:start_time',
            'cancellation_deadline' => 'required|date|before:start_time',
            'max_participants' => 'nullable|integer|min:1',
            'instructor_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,cancelled',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('activities', 'public');
            $data['image_path'] = $imagePath;
        }

        Activity::create($data);

        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified activity.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        // Check if user is already registered
        $isRegistered = false;
        if (Auth::check()) {
            $isRegistered = ActivityRegistration::where('user_id', Auth::id())
                ->where('activity_id', $activity->id)
                ->where('status', 'registered')
                ->exists();
        }

        $spotsLeft = $activity->spotsLeft();

        return view('activities.show', compact('activity', 'isRegistered', 'spotsLeft'));
    }

    /**
     * Show the form for editing the specified activity.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        return view('activities.edit', compact('activity'));
    }

    /**
     * Update the specified activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'registration_deadline' => 'required|date|before:start_time',
            'cancellation_deadline' => 'required|date|before:start_time',
            'max_participants' => 'nullable|integer|min:1',
            'instructor_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,cancelled',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($activity->image_path) {
                Storage::disk('public')->delete($activity->image_path);
            }

            $imagePath = $request->file('image')->store('activities', 'public');
            $data['image_path'] = $imagePath;
        }

        $activity->update($data);

        return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified activity from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        // Check if there are any registrations
        $registrationsCount = $activity->registrations()->count();

        if ($registrationsCount > 0) {
            return redirect()->route('activities.index')->with('error', 'Cannot delete activity with registrations. Consider changing its status to cancelled instead.');
        }

        // Delete image if exists
        if ($activity->image_path) {
            Storage::disk('public')->delete($activity->image_path);
        }

        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }

    /**
     * Register the authenticated user for the activity.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function register(Activity $activity)
    {
        // Check if the activity is active
        if ($activity->status !== 'active') {
            return redirect()->back()->with('error', 'This activity is not available for registration.');
        }

        // Check if registration deadline has passed
        if (now() > $activity->registration_deadline) {
            return redirect()->back()->with('error', 'Registration deadline has passed.');
        }

        // Check if user is already registered
        $existingRegistration = ActivityRegistration::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->first();

        if ($existingRegistration && $existingRegistration->status === 'registered') {
            return redirect()->back()->with('error', 'You are already registered for this activity.');
        }

        // Check if the activity is full
        if ($activity->isFull()) {
            return redirect()->back()->with('error', 'Sorry, this activity is full.');
        }

        // Create or update registration
        if ($existingRegistration) {
            $existingRegistration->update([
                'status' => 'registered',
                'registration_date' => now()
            ]);
        } else {
            ActivityRegistration::create([
                'user_id' => Auth::id(),
                'activity_id' => $activity->id,
                'status' => 'registered',
                'registration_date' => now()
            ]);
        }

        return redirect()->route('activities.my')->with('success', 'You have successfully registered for this activity.');
    }

    /**
     * Cancel the authenticated user's registration for the activity.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function cancelRegistration(Activity $activity)
    {
        // Find user's registration
        $registration = ActivityRegistration::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->where('status', 'registered')
            ->first();

        if (!$registration) {
            return redirect()->back()->with('error', 'You are not registered for this activity.');
        }

        // Check if cancellation deadline has passed
        if (now() > $activity->cancellation_deadline) {
            return redirect()->back()->with('error', 'Cancellation deadline has passed.');
        }

        // Update registration status
        $registration->update(['status' => 'cancelled']);

        return redirect()->route('activities.my')->with('success', 'Your registration has been cancelled.');
    }

    /**
     * Display the authenticated user's registered activities.
     *
     * @return \Illuminate\Http\Response
     */
    public function myActivities()
    {
        $registrations = ActivityRegistration::where('user_id', Auth::id())
            ->with(['activity' => function($query) {
                $query->orderBy('start_time');
            }])
            ->paginate(10);

        return view('activities.my', compact('registrations'));
    }
}
