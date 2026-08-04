@extends('layouts.main')

@section('title', 'Security Access Recovery | EggMarket')

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center p-6 bg-[#FBFBFE] relative overflow-hidden font-sans">
    <!-- Subtle Background Accents -->
    <div class="absolute top-0 right-0 w-[50%] h-full bg-slate-50 rounded-l-[120px] -z-10 animate-in slide-in-from-right duration-1000"></div>
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-green-500/5 blur-[120px] rounded-full -z-10"></div>

    <div class="max-w-5xl w-full grid lg:grid-cols-2 gap-16 items-center animate-in zoom-in-95 duration-700">

        <!-- Left Side: Professional Context -->
        <div class="hidden lg:block space-y-12">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-[0.2em]">
                    <i class="fas fa-user-shield"></i> Security Protocol
                </div>
                <h1 class="text-6xl font-black text-slate-900 tracking-tighter leading-tight">
                    Recover your <br/> <span class="text-blue-600">Access Key.</span>
                </h1>
                <p class="text-lg text-slate-500 font-medium leading-relaxed max-w-md">
                    Follow the encrypted verification steps to restore your administrative or supplier node connectivity.
                </p>
            </div>

            <!-- Interactive Step Indicator -->
            <div class="space-y-6">
                <div class="flex items-center gap-5">
                    <div class="w-10 h-10 rounded-full {{ !session('recovery_email') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-green-500 text-white' }} flex items-center justify-center font-black text-sm transition-all duration-500">
                        {!! !session('recovery_email') ? '1' : '<i class="fas fa-check"></i>' !!}
                    </div>
                    <p class="text-sm font-bold {{ !session('recovery_email') ? 'text-slate-900' : 'text-slate-400' }}">Identity Verification</p>
                </div>
                <div class="flex items-center gap-5">
                    <div class="w-10 h-10 rounded-full {{ session('recovery_email') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center font-black text-sm transition-all duration-500">2</div>
                    <p class="text-sm font-bold {{ session('recovery_email') ? 'text-slate-900' : 'text-slate-400' }}">Key Synchronization</p>
                </div>
            </div>

            <!-- JavaScript Animated Code Card (View in Card) -->
            @if(session('recovery_email'))
                <div class="p-10 bg-white rounded-[40px] border border-slate-100 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.05)] animate-in slide-in-from-bottom duration-700">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-satellite-dish animate-pulse"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Incoming Transmission</p>
                            <p class="text-sm font-bold text-slate-900">Security Node #{{ rand(100, 999) }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-950 rounded-3xl p-8 text-center relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                        <p class="text-[9px] font-black text-blue-400 uppercase tracking-[0.3em] mb-4 relative z-10">Verification Token</p>
                        <h4 id="token-display" class="text-5xl font-black text-white tracking-[0.4em] font-mono leading-none relative z-10">------</h4>
                    </div>
                    <p class="text-[10px] text-center text-slate-400 font-bold uppercase tracking-widest mt-6 italic">Valid for 10 minutes</p>
                </div>
            @endif
        </div>

        <!-- Right Side: Clean Form Terminal -->
        <div class="bg-white rounded-[50px] p-10 lg:p-16 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.08)] border border-slate-50 relative overflow-hidden">
            <div class="mb-12">
                <img src="https://tse1.mm.bing.net/th/id/OIP.kzenbVuAfiBwLzLLnNiKQwAAAA?w=400&h=400&rs=1&pid=ImgDetMain&o=7&rm=3"
                     class="w-14 h-14 rounded-2xl shadow-xl shadow-green-100 mb-8" alt="Logo">
                <h3 class="text-3xl font-black text-slate-900 tracking-tight">Access Restoration</h3>
                <p class="text-slate-400 font-semibold mt-2">Initialize your security key reset sequence.</p>
            </div>

            @if(!session('recovery_email'))
                <!-- Step 1 Form -->
                <form action="{{ route('password.email') }}" method="POST" class="space-y-8 animate-in slide-in-from-right duration-500">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block ml-1">Identity Endpoint (Email)</label>
                        <input type="email" name="email" required
                               class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
                               placeholder="node@exchange.com">
                    </div>
                    <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-[24px] font-black text-lg uppercase tracking-widest hover:bg-black shadow-2xl transition-all active:scale-95 flex items-center justify-center gap-3">
                        Initiate Recovery <i class="fas fa-arrow-right text-xs text-blue-400"></i>
                    </button>
                </form>
            @else
                <!-- Step 2 Form -->
                <form action="{{ route('password.update') }}" method="POST" class="space-y-6 animate-in slide-in-from-right duration-500">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('recovery_email') }}">

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block ml-1 text-center">Enter Token Found in the Data Card</label>
                        <input type="text" name="code" id="token-input" required maxlength="6"
                               class="w-full text-center tracking-[1em] py-6 bg-blue-50 border-none rounded-2xl font-black text-3xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-blue-600 shadow-inner"
                               placeholder="XXXXXX">
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">New Access Key</label>
                            <input type="password" name="password" required
                                   class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
                                   placeholder="••••••••">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Confirm Key</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-6 rounded-[24px] font-black text-lg uppercase tracking-widest hover:bg-blue-700 shadow-2xl shadow-blue-100 transition-all active:scale-95">
                        Synchronize Access Key
                    </button>
                </form>
            @endif

            <div class="mt-12 text-center pt-8 border-t border-slate-50">
                <a href="{{ route('login') }}" class="text-xs font-black text-slate-400 hover:text-slate-900 transition-colors uppercase tracking-widest">
                    Return to Login Terminal
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tokenEl = document.getElementById('token-display');
        const tokenInput = document.getElementById('token-input');

        if (tokenEl) {
            const secureCode = "{{ session('display_code') }}";

            let frames = 0;
            const scramble = setInterval(() => {
                // High-speed scramble for professional feel
                tokenEl.innerText = Math.floor(100000 + Math.random() * 900000);
                frames++;
                if (frames > 12) {
                    clearInterval(scramble);
                    tokenEl.innerText = secureCode;
                    tokenEl.classList.add('text-emerald-400');
                    tokenEl.classList.remove('text-white');

                    // Automatic injection into the input field
                    if (tokenInput) {
                        tokenInput.value = secureCode;
                        tokenInput.classList.add('ring-4', 'ring-emerald-500/20', 'border-emerald-500');
                        // Optional: Trigger a success animation or sound here
                    }
                }
            }, 40);
        }
    });
</script>
@endsection
