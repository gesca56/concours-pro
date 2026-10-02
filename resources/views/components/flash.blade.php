{{-- Message de confirmation après une action (session « status »). --}}
@if (session('status'))
    <div role="status" {{ $attributes->merge(['class' => 'bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-md']) }}>
        {{ session('status') }}
    </div>
@endif
