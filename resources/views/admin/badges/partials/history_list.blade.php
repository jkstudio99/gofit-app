@foreach($badgeHistory as $index => $item)
<tr>
    <td class="text-center d-none d-md-table-cell">{{ $badgeHistory->firstItem() + $index }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-2 d-none d-sm-block">
                @if(!empty($item->profile_image) && file_exists(public_path('profile_images/' . $item->profile_image)))
                    <img src="{{ asset('profile_images/' . $item->profile_image) }}" class="rounded-circle" width="40" height="40" alt="Profile" style="object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
            </div>
            <div>
                <span class="fw-bold user-link">{{ $item->username }}</span>
                <div class="small text-muted d-none d-sm-block">{{ $item->firstname ?? '' }} {{ $item->lastname ?? '' }}</div>
            </div>
        </div>
    </td>
    <td class="text-center">
        <a href="{{ route('admin.badges.show', $item->badge_id) }}" class="d-inline-block">
        <img src="{{ asset('storage/' . $item->badge_image) }}" alt="{{ $item->badge_name }}" class="badge-image" width="60">
        </a>
    </td>
    <td class="d-none d-md-table-cell">
        <div class="fw-bold">{{ $item->badge_name }}</div>
        <div class="small text-muted">{{ Str::limit($item->badge_desc, 60) }}</div>
    </td>
    <td class="d-none d-lg-table-cell">
        @php
            $typeIcons = [
                'distance' => 'fa-route',
                'calories' => 'fa-fire',
                'streak' => 'fa-calendar-check',
                'speed' => 'fa-tachometer-alt',
                'event' => 'fa-trophy'
            ];
            $typeColors = [
                'distance' => 'success',
                'calories' => 'danger',
                'streak' => 'success',
                'speed' => 'info',
                'event' => 'warning'
            ];

            $color = isset($typeColors[$item->type]) ? $typeColors[$item->type] : 'secondary';
            $icon = isset($typeIcons[$item->type]) ? $typeIcons[$item->type] : 'fa-medal';
        @endphp

        <span class="badge bg-{{ $color }} d-inline-block px-2 py-1 rounded-pill">
            <i class="fas {{ $icon }} me-1"></i>
        @if($item->type == 'distance')
                ระยะทาง {{ $item->criteria }} กม.
        @elseif($item->type == 'calories')
                แคลอรี่ {{ $item->criteria }} kcal
        @elseif($item->type == 'streak')
                วิ่งต่อเนื่อง {{ $item->criteria }} วัน
        @elseif($item->type == 'speed')
                ความเร็ว {{ $item->criteria }} กม./ชม.
        @elseif($item->type == 'event')
                กิจกรรม {{ $item->criteria }} ครั้ง
        @else
                {{ $item->type }} {{ $item->criteria }}
        @endif
        </span>
    </td>
    <td class="text-center">
        @php
            $key = $item->user_id . '_' . $item->badge_id;
            $points = isset($pointsHistory[$key]) ? $pointsHistory[$key][0]->points : '--';
        @endphp
        @if(is_numeric($points))
            <span class="badge bg-success fs-6 px-3 py-2">+{{ $points }}</span>
        @else
            <span class="badge bg-secondary fs-6 px-3 py-2">{{ $points }}</span>
        @endif
    </td>
    <td>
        @php
            $earnedDate = \Carbon\Carbon::parse($item->earned_at);
            $thaiYear = $earnedDate->year + 543;
            $formattedDate = $earnedDate->locale('th')->translatedFormat('j M').' '.substr($thaiYear, 2);
        @endphp
        {{ $formattedDate }}
        <div class="small text-muted d-none d-md-block">{{ $earnedDate->format('H:i') }} น.</div>
    </td>
    <td class="text-center">
        <a href="{{ route('admin.badges.show', $item->badge_id) }}" class="btn btn-sm btn-info btn-action mb-1" title="ดูรายละเอียดเหรียญตรา">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.users.show', $item->user_id) }}" class="btn btn-sm btn-primary btn-action mb-1" title="ดูข้อมูลผู้ใช้">
            <i class="fas fa-user"></i>
        </a>
    </td>
</tr>
@endforeach

@if($badgeHistory->isEmpty())
<tr>
    <td colspan="8" class="text-center py-5">
        <div class="text-muted mb-3">
            <i class="fas fa-medal fa-4x"></i>
        </div>
        <h5>ไม่พบข้อมูลประวัติการได้รับเหรียญตรา</h5>
        <p class="text-muted">ยังไม่มีผู้ใช้ได้รับเหรียญตราหรือไม่พบข้อมูลตามเงื่อนไขที่กรอง</p>
    </td>
</tr>
@endif
