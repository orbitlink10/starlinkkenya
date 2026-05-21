@extends('layouts.app')

@section('content')
    <div class="grid min-h-screen place-items-center px-6 py-8">
        <form
            class="w-full max-w-[440px] rounded-[30px] border border-[var(--border)] bg-white p-8 shadow-[var(--shadow)] md:p-[34px]"
            method="POST"
            action="{{ route('login.attempt') }}"
        >
            @csrf
            <h1 class="m-0 text-[38px] font-extrabold tracking-[-0.03em] text-[var(--ink)]">Starlink Kenya</h1>
            <p class="mb-7 mt-2.5 text-[17px] text-[var(--muted)]">Admin login</p>

            @if ($errors->any())
                <div class="mb-3.5 rounded-[10px] border border-[#ffd3db] bg-[#ffeef1] px-3 py-2.5 text-[14px] font-semibold text-[#9c1e2f]">
                    {{ $errors->first() }}
                </div>
            @endif

            <label class="mb-2 block text-[15px] font-semibold text-[#4d586d]" for="email">Email</label>
            <input
                class="mb-[18px] w-full rounded-[18px] border border-[var(--border)] bg-white/90 px-4 py-[15px] text-[16px] text-[var(--ink)] outline-none transition focus:border-[rgba(255,145,28,0.58)] focus:shadow-[0_0_0_4px_rgba(255,145,28,0.12)]"
                id="email"
                type="email"
                name="email"
                value="{{ old('email', 'admin@demo.com') }}"
                required
                autofocus
            >

            <label class="mb-2 block text-[15px] font-semibold text-[#4d586d]" for="password">Password</label>
            <input
                class="mb-[18px] w-full rounded-[18px] border border-[var(--border)] bg-white/90 px-4 py-[15px] text-[16px] text-[var(--ink)] outline-none transition focus:border-[rgba(255,145,28,0.58)] focus:shadow-[0_0_0_4px_rgba(255,145,28,0.12)]"
                id="password"
                type="password"
                name="password"
                value="admin123"
                required
            >

            <button
                class="w-full cursor-pointer rounded-full bg-[linear-gradient(135deg,var(--brand)_0%,#ffae42_100%)] px-[18px] py-[15px] text-[17px] font-bold text-white shadow-[0_12px_24px_rgba(245,141,25,0.22)] transition hover:-translate-y-0.5"
                type="submit"
            >
                Sign in to dashboard
            </button>

            <div class="mt-5 rounded-[18px] border border-dashed border-[var(--border)] bg-[var(--panel)] px-4 py-3.5 text-[14px] text-[var(--muted)]">
                Default access: <strong>admin@demo.com</strong> / <strong>admin123</strong>
            </div>
        </form>
    </div>
@endsection
