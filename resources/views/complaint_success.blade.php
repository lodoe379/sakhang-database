@extends('layouts.app')

@section('content')
<div class="landing-wrapper bg-animated-mesh" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="glass-panel" style="padding: 50px; border-radius: 30px; text-align: center; max-width: 500px; animation: modalPop 0.5s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="background: #22c55e; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; box-shadow: 0 0 20px rgba(34, 197, 94, 0.4); margin: 0 auto 20px;">✓</div>
        <h2 style="color: #22c55e; margin-bottom: 10px; font-size: 32px;">Success!</h2>
        <p style="font-size: 18px; color: #334155; margin-bottom: 20px; font-weight: 500;">Your electrical complaint has been submitted successfully. Our maintenance team will process it shortly.</p>
        

        <a href="{{ route('landing') }}" class="btn" style="background: #1e3a5f; color: white; padding: 18px 40px; border-radius: 50px; font-weight: 800; text-decoration: none; display: inline-block; transition: all 0.3s; letter-spacing: 1px;">BACK TO HOME</a>
    </div>
</div>
<style>
    @keyframes modalPop {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>
@endsection
