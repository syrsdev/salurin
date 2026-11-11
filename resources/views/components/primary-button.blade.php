<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-lg border border-transparent bg-blue-900 px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-blue-800 focus:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 active:bg-blue-950 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
