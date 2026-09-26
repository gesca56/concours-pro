<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-institutionnel border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-institutionnel-hover focus:bg-institutionnel-hover active:bg-institutionnel-hover focus:outline-none focus:ring-2 focus:ring-institutionnel focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
