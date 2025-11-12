
<x-admin-layout title="Tools- Decrypt ENC">
 {{-- Tampilkan error validasi / decrypt --}}
  @if ($errors->any())
    <div class="mb-4 rounded border border-red-300 bg-red-50 text-red-700 px-3 py-2 text-sm">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="bg-white rounded-lg shadow p-6 max-w-xl justify-center mx-auto">
    <h2 class="text-lg font-semibold mb-4">Decrypt ENC to CSV</h2>

    <form method="POST" action="{{ route('admin.decrypt.process') }}" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm mb-1 font-medium">File terenkripsi (.enc)</label>
        <input type="file" name="file" accept=".enc" required
               class="block w-full border rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm mb-1 font-medium">Password enkripsi</label>
        <input type="password" name="password" minlength="8" required
               class="border rounded px-3 py-2 w-full">
        <p class="text-xs text-gray-500 mt-1">Masukkan password yang dipakai saat Export (Encrypted).</p>
      </div>

      <div class="pt-2 flex gap-2">
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
          Decrypt
        </button>
        <a href="/" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">
          Kembali
        </a>
      </div>
    </form>
  </div>
</x-admin-layout>
