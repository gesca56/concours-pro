@props(['dark' => false, 'href' => null])

{{-- Repère de marque temporaire — à remplacer par le vrai logo (fichier image) dès qu'il sera prêt. --}}
<a href="{{ $href ?? url('/') }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 group']) }}>
    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-institutionnel to-marine-light shadow-sm shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="w-5 h-5">
            <path d="M12 3L21 7.5L12 12L3 7.5L12 3Z" fill="white" fill-opacity="0.95"/>
            <path d="M6 10.5V15C6 15 8 17.5 12 17.5C16 17.5 18 15 18 15V10.5" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 7.5V13" stroke="white" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
    </span>
    <span class="font-bold tracking-tight text-lg {{ $dark ? 'text-white' : 'text-marine' }}">
        Concours<span class="text-institutionnel">Pro</span>
    </span>
</a>
