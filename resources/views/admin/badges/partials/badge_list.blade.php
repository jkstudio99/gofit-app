@php
    // จัดกลุ่มตามประเภท
    $badgesByType = $badges->groupBy('type');

    // กำหนดลำดับการแสดงผล และชื่อแสดงผลภาษาไทย
    $typeOrder = ['distance', 'calories', 'streak', 'speed', 'event'];
    $typeNames = [
        'distance' => 'ระยะทาง',
        'calories' => 'แคลอรี่',
        'streak' => 'ต่อเนื่อง',
        'speed' => 'ความเร็ว',
        'event' => 'กิจกรรม'
    ];
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
@endphp

@if($badges->isEmpty())
    <div class="text-center py-5">
        <div class="text-muted mb-3">
            <i class="fas fa-medal fa-4x"></i>
        </div>
        <h5>ไม่พบข้อมูลเหรียญตรา</h5>
        <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
    </div>
@else
    <!-- แสดงเหรียญตามประเภท -->
    <ul class="nav nav-tabs mb-4" id="badgeTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-badges" type="button" role="tab" aria-controls="all-badges" aria-selected="true">
                <i class="fas fa-medal me-1"></i> ทั้งหมด
            </button>
        </li>
        @foreach($typeOrder as $type)
            @if($badgesByType->has($type))
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="{{ $type }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $type }}-badges" type="button" role="tab" aria-controls="{{ $type }}-badges" aria-selected="false">
                        <i class="fas {{ $typeIcons[$type] }} me-1"></i> {{ $typeNames[$type] }}
                        <span class="badge bg-{{ $typeColors[$type] }} ms-1 rounded-pill">{{ $badgesByType[$type]->count() }}</span>
                    </button>
                </li>
            @endif
        @endforeach
    </ul>

    <div class="tab-content" id="badgeTypeContent">
        <!-- ทั้งหมด -->
        <div class="tab-pane fade show active" id="all-badges" role="tabpanel" aria-labelledby="all-tab">
            @foreach($typeOrder as $type)
                @if($badgesByType->has($type))
                    <div class="badge-category-section mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="badge-type-icon bg-{{ $typeColors[$type] }} bg-opacity-10 text-{{ $typeColors[$type] }} me-2">
                                <i class="fas {{ $typeIcons[$type] }}"></i>
                            </div>
                            <h5 class="mb-0">เหรียญ{{ $typeNames[$type] }}</h5>
                            <div class="ms-auto">
                                <span class="badge bg-{{ $typeColors[$type] }}">
                                    {{ $badgesByType[$type]->count() }} รายการ
                                </span>
                            </div>
                        </div>

                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
                            @foreach($badgesByType[$type] as $badge)
                                <div class="col">
                                    <div class="card h-100 badge-card"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ $badge->badge_desc }}">

                                        <!-- Badge Type Indicator -->
                                        <span class="badge badge-type bg-{{ $typeColors[$type] }}">
                                            <i class="fas {{ $typeIcons[$type] }}"></i>
                                        </span>

                                        <!-- Users Badge -->
                                        <div class="badge-stats">
                                            <a href="{{ route('admin.badges.users', $badge) }}" class="badge bg-light text-dark">
                                                <i class="fas fa-users me-1"></i> {{ $badge->users_count ?? 0 }} คน
                                            </a>
                                        </div>

                                        <div class="badge-img-container">
                                            @if($badge->badge_image)
                                                <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                        class="badge-img"
                                                        alt="{{ $badge->badge_name }}">
                                            @else
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-medal fa-3x"></i>
                                                    <p class="small mt-2">ไม่มีรูปภาพ</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-body">
                                            <h6 class="card-title">{{ $badge->badge_name }}</h6>
                                            <p class="card-text badge-requirement small text-muted">
                                                @if($badge->type == 'distance')
                                                    วิ่งระยะทางสะสม {{ $badge->criteria }} กม.
                                                @elseif($badge->type == 'calories')
                                                    เผาผลาญแคลอรี่สะสม {{ $badge->criteria }} แคลอรี่
                                                @elseif($badge->type == 'streak')
                                                    วิ่งติดต่อกัน {{ $badge->criteria }} วัน
                                                @elseif($badge->type == 'speed')
                                                    วิ่งด้วยความเร็วเฉลี่ย {{ $badge->criteria }} กม./ชม.
                                                @elseif($badge->type == 'event')
                                                    เข้าร่วมกิจกรรม {{ $badge->criteria }} ครั้ง
                                                @else
                                                    {{ $badge->criteria }}
                                                @endif
                                            </p>

                                            <!-- แสดงคะแนนที่จะได้รับ -->
                                            <div class="badge-points small fw-bold">
                                                <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                    <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                                </span>
                                            </div>
                                        </div>

                                        <div class="card-footer bg-white py-2">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('admin.badges.show', $badge) }}" class="btn btn-sm btn-info badge-action-btn me-2 text-white" title="ดูรายละเอียด">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-warning badge-action-btn me-2" title="แก้ไข">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger badge-action-btn delete-badge"
                                                        title="ลบ" data-badge-id="{{ $badge->badge_id }}"
                                                        data-badge-name="{{ $badge->badge_name }}"
                                                        data-users-count="{{ $badge->users_count ?? 0 }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- แท็บแยกตามประเภท -->
        @foreach($typeOrder as $type)
            @if($badgesByType->has($type))
                <div class="tab-pane fade" id="{{ $type }}-badges" role="tabpanel" aria-labelledby="{{ $type }}-tab">
                    <div class="badge-type-header mb-4">
                        <div class="d-flex align-items-center">
                            <div class="badge-type-icon bg-{{ $typeColors[$type] }} bg-opacity-10 text-{{ $typeColors[$type] }} me-3">
                                <i class="fas {{ $typeIcons[$type] }}"></i>
                            </div>
                            <div>
                                <h4 class="mb-1">เหรียญ{{ $typeNames[$type] }}</h4>
                                <p class="text-muted mb-0">
                                    @if($type == 'distance')
                                        เหรียญที่ผู้ใช้จะได้รับเมื่อวิ่งได้ระยะทางตามเป้าหมาย
                                    @elseif($type == 'calories')
                                        เหรียญที่ผู้ใช้จะได้รับเมื่อเผาผลาญแคลอรี่ตามเป้าหมาย
                                    @elseif($type == 'streak')
                                        เหรียญที่ผู้ใช้จะได้รับเมื่อวิ่งติดต่อกันตามจำนวนวัน
                                    @elseif($type == 'speed')
                                        เหรียญที่ผู้ใช้จะได้รับเมื่อวิ่งด้วยความเร็วเฉลี่ยตามเป้าหมาย
                                    @elseif($type == 'event')
                                        เหรียญที่ผู้ใช้จะได้รับเมื่อเข้าร่วมกิจกรรมตามจำนวนครั้ง
                                    @endif
                                </p>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-{{ $typeColors[$type] }} rounded-pill px-3 py-2">
                                    <i class="fas fa-medal me-1"></i>
                                    {{ $badgesByType[$type]->count() }} รายการ
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                        @foreach($badgesByType[$type] as $badge)
                            <div class="col">
                                <div class="card h-100 badge-card"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ $badge->badge_desc }}">

                                    <!-- Badge Type Indicator -->
                                    <span class="badge badge-type bg-{{ $typeColors[$type] }}">
                                        <i class="fas {{ $typeIcons[$type] }}"></i>
                                    </span>

                                    <!-- Users Badge -->
                                    <div class="badge-stats">
                                        <a href="{{ route('admin.badges.users', $badge) }}" class="badge bg-light text-dark">
                                            <i class="fas fa-users me-1"></i> {{ $badge->users_count ?? 0 }} คน
                                        </a>
                                    </div>

                                    <div class="badge-img-container">
                                        @if($badge->badge_image)
                                            <img src="{{ asset('storage/' . $badge->badge_image) }}"
                                                    class="badge-img"
                                                    alt="{{ $badge->badge_name }}">
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="fas fa-medal fa-3x"></i>
                                                <p class="small mt-2">ไม่มีรูปภาพ</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <h6 class="card-title">{{ $badge->badge_name }}</h6>
                                        <p class="card-text badge-requirement small text-muted">
                                            @if($badge->type == 'distance')
                                                วิ่งระยะทางสะสม {{ $badge->criteria }} กม.
                                            @elseif($badge->type == 'calories')
                                                เผาผลาญแคลอรี่สะสม {{ $badge->criteria }} แคลอรี่
                                            @elseif($badge->type == 'streak')
                                                วิ่งติดต่อกัน {{ $badge->criteria }} วัน
                                            @elseif($badge->type == 'speed')
                                                วิ่งด้วยความเร็วเฉลี่ย {{ $badge->criteria }} กม./ชม.
                                            @elseif($badge->type == 'event')
                                                เข้าร่วมกิจกรรม {{ $badge->criteria }} ครั้ง
                                            @else
                                                {{ $badge->criteria }}
                                            @endif
                                        </p>

                                        <!-- แสดงคะแนนที่จะได้รับ -->
                                        <div class="badge-points small fw-bold">
                                            <span class="d-inline-block bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 mt-1">
                                                <i class="fas fa-coins me-1"></i> {{ $badge->points ?? 100 }} คะแนน
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-white py-2">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.badges.show', $badge) }}" class="btn btn-sm btn-info badge-action-btn me-2 text-white" title="ดูรายละเอียด">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-warning badge-action-btn me-2" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger badge-action-btn delete-badge"
                                                    title="ลบ" data-badge-id="{{ $badge->badge_id }}"
                                                    data-badge-name="{{ $badge->badge_name }}"
                                                    data-users-count="{{ $badge->users_count ?? 0 }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
