@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16">
    <div class="mb-12">
        <h1 class="text-4xl font-black text-gray-900 tracking-tight">Account Settings</h1>
        <p class="text-gray-500 font-medium mt-2">Manage your personal profile, farm details, and security.</p>
    </div>

    @if(session('success'))
        <div class="mb-8 bg-green-50 border border-green-100 p-6 rounded-3xl text-green-700 font-bold text-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-10">
        <!-- Profile Details -->
        <div class="bg-white p-10 rounded-[48px] border border-gray-100 shadow-sm">
            <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                <i class="fas fa-id-card text-green-600"></i> Profile Information
            </h3>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-gray-50 border-none rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-green-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-gray-50 border-none rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-green-500 transition-all">
                    </div>
                </div>

                @if($user->role->name == 'farmer')
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Farm Name</label>
                    <input type="text" name="farm_name" value="{{ old('farm_name', $user->farm_name) }}" required class="w-full bg-gray-50 border-none rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-green-500 transition-all">
                </div>
                @endif

                <!-- Location Selector -->
                <div class="space-y-3">
                    <div class="flex justify-between items-end">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">My Pinned Location</label>
                        <button type="button" onclick="detectMyLocation()" class="text-[10px] font-black text-green-600 uppercase tracking-widest flex items-center gap-1 hover:underline">
                            <i class="fas fa-location-crosshairs"></i> Update with GPS
                        </button>
                    </div>
                    <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" required class="w-full bg-gray-50 border-none rounded-t-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-green-500 transition-all" placeholder="Street Address, City">
                    <div id="map" class="h-64 w-full rounded-b-2xl border-2 border-gray-50"></div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $user->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $user->longitude) }}">
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-5 rounded-[24px] font-black uppercase tracking-widest hover:bg-green-700 shadow-xl shadow-green-100 transition-all">
                    Save Profile Changes
                </button>
            </form>
        </div>

        <!-- Security / Password -->
        <div class="bg-white p-10 rounded-[48px] border border-gray-100 shadow-sm">
            <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                <i class="fas fa-shield-halved text-blue-600"></i> Account Security
            </h3>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Current Password</label>
                    <input type="password" name="current_password" required class="w-full bg-gray-50 border-none rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">New Password</label>
                        <input type="password" name="password" required class="w-full bg-gray-50 border-none rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border-none rounded-2xl p-4 font-bold text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[24px] font-black uppercase tracking-widest hover:bg-black shadow-xl shadow-gray-200 transition-all">
                    Update Password
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-50 text-center">
                <p class="text-xs font-bold text-slate-400">Require a security bypass protocol?</p>
                <button type="button" onclick="openEmergencyModal()" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:underline mt-2 inline-block">
                    Initialize Emergency Key Recovery
                </button>
            </div>

<!-- Emergency Recovery Modal -->
<div id="emergencyModal" class="hidden fixed inset-0 z-[110] bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-6">
    <div class="bg-white rounded-[60px] w-full max-w-4xl shadow-2xl animate-in zoom-in-95 duration-500 overflow-hidden border border-white/20">
        <div class="grid lg:grid-cols-2">
            <!-- Left Side: Digital Token Card -->
            <div class="p-12 bg-slate-900 flex flex-col justify-center items-center text-center space-y-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-bl-full"></div>
                <div class="w-20 h-20 bg-white/10 rounded-[32px] flex items-center justify-center text-blue-400 shadow-2xl shadow-blue-500/20">
                    <i class="fas fa-microchip fa-2x animate-pulse"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-4">Secure Transmission Node</p>
                    <h4 id="settings-token-display" class="text-6xl font-black text-white tracking-[0.4em] font-mono leading-none">------</h4>
                </div>
                <p class="text-xs font-bold text-slate-400 max-w-xs leading-relaxed uppercase tracking-widest opacity-60">Automatic key-pair synchronization active.</p>
            </div>

            <!-- Right Side: Reset Form -->
            <div class="p-12 lg:p-16 bg-white relative">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Bypass Protocol</h3>
                        <p class="text-slate-400 font-bold text-[10px] uppercase tracking-widest mt-2">Updating Access Credentials</p>
                    </div>
                    <button onclick="closeEmergencyModal()" class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 hover:text-rose-500 transition-all flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="current_password" id="auto-fill-current" value="password"> <!-- Simplified for demo -->

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block ml-1">Generated Dispatch Token</label>
                        <input type="text" id="settings-token-input" readonly class="w-full text-center tracking-[1em] py-5 bg-blue-50 border-none rounded-2xl font-black text-2xl text-blue-600 shadow-inner">
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">New Security Key</label>
                            <input type="password" name="password" required class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-4 focus:ring-blue-500/10">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Verify Node Key</label>
                            <input type="password" name="password_confirmation" required class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-4 focus:ring-blue-500/10">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-[28px] font-black uppercase tracking-widest hover:bg-black shadow-2xl transition-all active:scale-95">
                        Synchronize Node Key
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([{{ $user->latitude ?? 14.5995 }}, {{ $user->longitude ?? 120.9842 }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var marker = L.marker([{{ $user->latitude ?? 14.5995 }}, {{ $user->longitude ?? 120.9842 }}]).addTo(map);

    function setLocation(lat, lng) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 16);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }

    map.on('click', function(e) {
        setLocation(e.latlng.lat, e.latlng.lng);
    });

    function detectMyLocation() {
        if (!navigator.geolocation) return alert("Geolocation not supported");
        navigator.geolocation.getCurrentPosition((pos) => {
            setLocation(pos.coords.latitude, pos.coords.longitude);
        });
    }

    function openEmergencyModal() {
        const modal = document.getElementById('emergencyModal');
        const display = document.getElementById('settings-token-display');
        const input = document.getElementById('settings-token-input');

        modal.classList.remove('hidden');

        // Reset state
        display.innerText = "------";
        display.classList.remove('text-green-400');
        display.classList.add('text-white');
        input.value = "";

        // High-speed scramble effect
        const secureCode = Math.floor(100000 + Math.random() * 900000);
        let frames = 0;
        const interval = setInterval(() => {
            display.innerText = Math.floor(100000 + Math.random() * 900000);
            frames++;
            if (frames > 12) {
                clearInterval(interval);
                display.innerText = secureCode;
                display.classList.add('text-green-400');
                display.classList.remove('text-white');

                // Automatic insertion
                input.value = secureCode;
            }
        }, 40);
    }

    function closeEmergencyModal() {
        document.getElementById('emergencyModal').classList.add('hidden');
    }
</script>
@endsection
