<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold">Penerimaan Servis</h2>
        <p class="mt-1 text-sm text-muted">
            Catat pelanggan dan perangkat yang diterima.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="mb-5 rounded-lg bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="saveIntake">
        @include('livewire.partials.service-intake-form')
    </form>
</div>
