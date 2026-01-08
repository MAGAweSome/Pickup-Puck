@extends('layouts.app')

@section('content')
	<div class="max-w-lg mx-auto">
		<div class="bg-slate-gray/40 border border-white/10 rounded-2xl p-6">
			<h1 class="text-2xl font-extrabold text-white">Elevate Me To Admin</h1>
			<p class="text-sm text-white/70 mt-2">
				Enter the <span class="font-semibold text-white">ADMIN_SETUP_KEY</span> from your <span class="font-mono text-white">.env</span> file.
				If it matches, your account will be given the <span class="font-semibold text-white">admin</span> role.
			</p>

			@if (session('success'))
				<div class="mt-4 rounded-xl border border-green-400/30 bg-green-500/10 px-4 py-3 text-green-200">
					{{ session('success') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="mt-4 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-red-200">
					<div class="font-semibold">Please fix the following:</div>
					<ul class="list-disc list-inside text-sm mt-2">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<form method="POST" action="{{ route('setup.elevate-me-puck-admin.submit') }}" class="mt-6 space-y-4">
				@csrf

				<div>
					<label for="setup_key" class="block text-sm font-semibold text-white">Setup Key</label>
					<input
						id="setup_key"
						name="setup_key"
						type="password"
						autocomplete="off"
						spellcheck="false"
						value="{{ old('setup_key') }}"
						class="mt-2 w-full rounded-xl bg-deep-navy/70 border border-white/10 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ice-blue/60"
						placeholder="Enter ADMIN_SETUP_KEY"
						required
					/>
					<p class="text-xs text-white/60 mt-2">This page is rate-limited. Don’t share this key.</p>
				</div>

				<div class="flex items-center gap-3">
					<button
						type="submit"
						class="inline-flex items-center justify-center rounded-xl bg-ice-blue text-deep-navy font-bold px-5 py-3 hover:opacity-90 transition"
					>
						Make Me Admin
					</button>

					<a href="{{ route('home') }}" class="text-sm text-white/70 hover:text-white underline">
						Back to Home
					</a>
				</div>
			</form>

			<div class="mt-6 pt-4 border-t border-white/10 text-xs text-white/60">
				Logged in as <span class="text-white/80">{{ auth()->user()->email }}</span>
				@if (auth()->user()->hasRole('admin'))
					<span class="ml-2 inline-flex items-center rounded-full bg-green-500/10 border border-green-400/20 px-2 py-0.5 text-green-200">Already admin</span>
				@endif
			</div>
		</div>
	</div>
@endsection
