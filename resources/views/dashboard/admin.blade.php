<div class="min-h-screen bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
    <aside class="fixed inset-y-0 w-64 bg-zinc-900 text-zinc-100">
        <div class="p-4 font-bold tracking-wide">LAWYER ADMIN</div>

        <nav class="mt-6 space-y-1">
            <a class="block px-4 py-2 hover:bg-zinc-800">Dashboard</a>
            <a class="block px-4 py-2 hover:bg-zinc-800">Cases</a>
            <a class="block px-4 py-2 hover:bg-zinc-800">Lawyers</a>
        </nav>
    </aside>

    <main class="ml-64 p-6">
        {{ $slot }}
    </main>
</div>
