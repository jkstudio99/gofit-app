<div class="col">
    <div class="card event-card h-100 shadow-sm">
        <!-- Event Image -->
        <div class="event-img-container">
            <img src="{{ asset('storage/' . ($event->event_image ?? 'events/default-event.png')) }}"
                 class="card-img-top"
                 alt="{{ $event->event_name }}"
                 onerror="this.src='{{ asset('images/event-placeholder.jpg') }}';">

            <!-- Event Status -->
            <div class="event-status">
                @if ($event->hasEnded())
                    <span class="badge bg-secondary">สิ้นสุดแล้ว</span>
                @elseif ($event->isActive())
                    <span class="badge bg-success">กำลังดำเนินการ</span>
                @else
                    <span class="badge bg-info">กำลังจะมาถึง</span>
                @endif
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <h5 class="card-title mb-2">{{ Str::limit($event->event_name, 40) }}</h5>
            <p class="card-text text-muted small">{{ Str::limit($event->description, 80) }}</p>

            <!-- Event Details -->
            <div class="mt-3">
                <!-- Date -->
                <div class="event-stat">
                    <i class="far fa-calendar-alt text-primary"></i>
                    <span>{{ \Carbon\Carbon::parse($event->start_datetime)->thaiDate() }}</span>
                </div>

                <!-- Time -->
                <div class="event-stat">
                    <i class="far fa-clock text-info"></i>
                    <span>{{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }}</span>
                </div>

                <!-- Location -->
                <div class="event-stat">
                    <i class="fas fa-map-marker-alt text-danger"></i>
                    <span>{{ Str::limit($event->location, 30) }}</span>
                </div>

                <!-- Participants -->
                <div class="event-stat">
                    <i class="fas fa-users text-success"></i>
                    <span>ผู้เข้าร่วม: {{ $event->activeParticipants()->count() }} / {{ $event->max_participants > 0 ? $event->max_participants : 'ไม่จำกัด' }}</span>
                </div>
                <div class="participants-progress">
                    @php
                        $percentage = $event->max_participants > 0
                            ? min(100, round(($event->activeParticipants()->count() / $event->max_participants) * 100))
                            : 0;
                    @endphp
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"
                             aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2 mt-3">
                <a href="{{ route('events.show', $event->event_id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-info-circle me-1"></i> รายละเอียด
                </a>
            </div>
        </div>
    </div>
</div>
