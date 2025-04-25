@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Run Tracker</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="running-stats">
                                <div class="stat-box">
                                    <h5>Distance</h5>
                                    <p id="distance">0.00 km</p>
                                </div>
                                <div class="stat-box">
                                    <h5>Duration</h5>
                                    <p id="duration">00:00:00</p>
                                </div>
                                <div class="stat-box">
                                    <h5>Calories</h5>
                                    <p id="calories">0 kcal</p>
                                </div>
                                <div class="stat-box">
                                    <h5>Avg. Speed</h5>
                                    <p id="avg-speed">0.0 km/h</p>
                                </div>

                                <div class="mt-4">
                                    <button id="startBtn" class="btn btn-success btn-lg btn-block">Start Run</button>
                                    <button id="pauseBtn" class="btn btn-warning btn-lg btn-block mt-2" style="display: none;">Pause</button>
                                    <button id="resumeBtn" class="btn btn-info btn-lg btn-block mt-2" style="display: none;">Resume</button>
                                    <button id="finishBtn" class="btn btn-danger btn-lg btn-block mt-2" style="display: none;">Finish Run</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Recent Activities</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Duration</th>
                                        <th>Distance</th>
                                        <th>Calories</th>
                                        <th>Avg. Speed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivities as $activity)
                                    <tr>
                                        <td>{{ $activity->start_time->format('d M Y, H:i') }}</td>
                                        <td>
                                            @if($activity->end_time)
                                                {{ gmdate('H:i:s', $activity->end_time->diffInSeconds($activity->start_time)) }}
                                            @else
                                                In Progress
                                            @endif
                                        </td>
                                        <td>{{ number_format($activity->distance, 2) }} km</td>
                                        <td>{{ number_format($activity->calories_burned, 0) }} kcal</td>
                                        <td>{{ number_format($activity->average_speed, 1) }} km/h</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No recent activities</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry"></script>
<script>
    let map;
    let marker;
    let polyline;
    let watchId;
    let startTime;
    let elapsedTime = 0;
    let timerInterval;
    let routeCoordinates = [];
    let isPaused = false;
    let activityId = null;
    let lastUpdateTime = 0;

    // Initialize the map
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDefaultUI: true,
            zoomControl: true
        });

        // Try to get the user's location to center the map
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    map.setCenter(pos);

                    // Create the marker at the current position
                    marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: 'Your Location'
                    });

                    // Create polyline for the route
                    polyline = new google.maps.Polyline({
                        map: map,
                        path: [],
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 3
                    });
                },
                () => {
                    // Handle location error
                    console.log("Error: The Geolocation service failed.");
                }
            );
        } else {
            // Browser doesn't support Geolocation
            console.log("Error: Your browser doesn't support geolocation.");
        }
    }

    // Start tracking
    function startTracking() {
        if (navigator.geolocation) {
            startTime = new Date().getTime();
            routeCoordinates = [];

            // Start the timer
            timerInterval = setInterval(updateTimer, 1000);

            // Watch position
            watchId = navigator.geolocation.watchPosition(
                updatePosition,
                handleLocationError,
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );

            // Start the activity in the backend
            fetch('/run/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    activityId = data.activity_id;
                } else {
                    alert(data.message);
                    stopTracking();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to start activity. Please try again.');
                stopTracking();
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    // Update position
    function updatePosition(position) {
        if (isPaused) return;

        const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        // Update marker
        marker.setPosition(pos);

        // Add to route
        routeCoordinates.push(pos);

        // Update polyline
        polyline.setPath(routeCoordinates);

        // Pan map to current position
        map.panTo(pos);

        // Calculate distance, speed, etc.
        updateStats();

        // Send route updates to server every 10 seconds
        const currentTime = new Date().getTime();
        if (activityId && (currentTime - lastUpdateTime > 10000)) {
            lastUpdateTime = currentTime;
            updateRouteOnServer();
        }
    }

    // Update the stats
    function updateStats() {
        // Calculate distance
        let distance = 0;
        if (routeCoordinates.length > 1) {
            for (let i = 0; i < routeCoordinates.length - 1; i++) {
                const p1 = new google.maps.LatLng(
                    routeCoordinates[i].lat,
                    routeCoordinates[i].lng
                );
                const p2 = new google.maps.LatLng(
                    routeCoordinates[i + 1].lat,
                    routeCoordinates[i + 1].lng
                );

                // Add distance in meters
                distance += google.maps.geometry.spherical.computeDistanceBetween(p1, p2);
            }
        }

        // Convert to kilometers
        distance = distance / 1000;

        // Duration in seconds
        const duration = elapsedTime;

        // Calculate average speed (km/h)
        let avgSpeed = 0;
        if (duration > 0) {
            avgSpeed = (distance / duration) * 3600;
        }

        // Calculate calories (rough estimate: 1 km ≈ 65 calories)
        const calories = distance * 65;

        // Update the UI
        document.getElementById('distance').textContent = distance.toFixed(2) + ' km';
        document.getElementById('avg-speed').textContent = avgSpeed.toFixed(1) + ' km/h';
        document.getElementById('calories').textContent = Math.round(calories) + ' kcal';
    }

    // Update the timer
    function updateTimer() {
        if (!isPaused) {
            elapsedTime = Math.floor((new Date().getTime() - startTime) / 1000);
        }

        const hours = Math.floor(elapsedTime / 3600);
        const minutes = Math.floor((elapsedTime - (hours * 3600)) / 60);
        const seconds = elapsedTime - (hours * 3600) - (minutes * 60);

        document.getElementById('duration').textContent =
            (hours < 10 ? '0' + hours : hours) + ':' +
            (minutes < 10 ? '0' + minutes : minutes) + ':' +
            (seconds < 10 ? '0' + seconds : seconds);
    }

    // Handle location errors
    function handleLocationError(error) {
        console.log('Error getting location:', error.message);
    }

    // Pause tracking
    function pauseTracking() {
        isPaused = true;
    }

    // Resume tracking
    function resumeTracking() {
        isPaused = false;
        startTime = new Date().getTime() - (elapsedTime * 1000);
    }

    // Stop tracking
    function stopTracking() {
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }

        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }

        // Reset the UI
        document.getElementById('startBtn').style.display = 'block';
        document.getElementById('pauseBtn').style.display = 'none';
        document.getElementById('resumeBtn').style.display = 'none';
        document.getElementById('finishBtn').style.display = 'none';

        // If we have an activity ID, finish it on the server
        if (activityId) {
            finishActivityOnServer();
        }
    }

    // Update route on server
    function updateRouteOnServer() {
        if (!activityId) return;

        fetch('/run/updateRoute', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                activity_id: activityId,
                route_data: JSON.stringify(routeCoordinates),
                current_distance: parseFloat(document.getElementById('distance').textContent)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Error updating route:', data.message);
            }
        })
        .catch(error => {
            console.error('Error updating route:', error);
        });
    }

    // Finish activity on server
    function finishActivityOnServer() {
        if (!activityId) return;

        const distance = parseFloat(document.getElementById('distance').textContent);
        const calories = parseInt(document.getElementById('calories').textContent);
        const avgSpeed = parseFloat(document.getElementById('avg-speed').textContent);

        fetch('/run/finish', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                activity_id: activityId,
                route_data: JSON.stringify(routeCoordinates),
                distance: distance,
                duration: elapsedTime,
                calories: calories,
                average_speed: avgSpeed
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Activity saved successfully!');
                // Refresh the page to update the activity list
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save activity. Please try again.');
        });
    }

    // Initialize when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        // Set up button event listeners
        document.getElementById('startBtn').addEventListener('click', function() {
            startTracking();
            this.style.display = 'none';
            document.getElementById('pauseBtn').style.display = 'block';
            document.getElementById('finishBtn').style.display = 'block';
        });

        document.getElementById('pauseBtn').addEventListener('click', function() {
            pauseTracking();
            this.style.display = 'none';
            document.getElementById('resumeBtn').style.display = 'block';
        });

        document.getElementById('resumeBtn').addEventListener('click', function() {
            resumeTracking();
            this.style.display = 'none';
            document.getElementById('pauseBtn').style.display = 'block';
        });

        document.getElementById('finishBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to finish this run?')) {
                stopTracking();
            }
        });
    });
</script>

<style>
    .stat-box {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        text-align: center;
    }

    .stat-box h5 {
        margin-bottom: 5px;
        color: #495057;
    }

    .stat-box p {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0;
    }

    .running-stats {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .running-stats .mt-4 {
        margin-top: auto !important;
    }
</style>
@endsection
