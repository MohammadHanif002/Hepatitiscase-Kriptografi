<x-admin-layout title="Dashboard Admin - Data Kasus Hepatitis A di Jember">

  {{-- Slot aksi di header: tombol Logout --}}
  <x-slot:actions>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        Logout
      </button>
    </form>
  </x-slot:actions>

  {{-- Konten utama --}}
  <div class="bg-white p-6 rounded-lg shadow-md">

    <table class="min-w-full table-auto border border-gray-300">
      <thead>
        <tr class="bg-gray-200 text-left">
          <th class="px-4 py-2 border">ID</th>
          <th class="px-4 py-2 border">Kecamatan</th>
          <th class="px-4 py-2 border">Jumlah Kasus</th>
          <th class="px-4 py-2 border">Tahun</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($data as $row)
          <tr class="border-t">
            <td class="px-4 py-2 border">{{ $row->gid }}</td>
            <td class="px-4 py-2 border">{{ $row->kecamatan }}</td>
            <td class="px-4 py-2 border">{{ $row->jumlah_kasus }}</td>
            <td class="px-4 py-2 border">{{ $row->tahun }}</td>
            <td class="px-4 py-2 border space-x-2">
              <a href="{{ route('admin.edit', $row->gid) }}"
                 class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>

              <form action="{{ route('admin.delete', $row->gid) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Yakin ingin menghapus data ini?')"
                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-6 flex text-right justify-end">
      <a href="{{ route('admin.create') }}"
         class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Tambah Data</a>
    </div>
  </div>
</x-admin-layout>
