  <div class="shrink-0">
      <img class="size-9" src="\img\jemberlogo.png" alt="Your Company">
  </div>
  <div class="hidden md:block">
      <div class="ml-10 flex items-baseline space-x-4">
          <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
          <x-nav-link href="" :active="request()->is('admin/decrypt')">Dashboard Admin</x-nav-link>
          <div class="ml-auto flex items-baseline space-x-4">
              {{-- <form action="/searchKasus" method="GET" class="flex items-center space-x-2">
                  <input type="text" name="q" placeholder="Cari kecamatan..."
                      class="w-140 rounded-md px-2 py-1 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700">
                      Cari
                  </button>
              </form> --}}

              {{-- <a href="/blog"
                            class="{{ request()->is('blog') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium hover:text-white">Blog</a>
                        <a href="/about"
                            class="{{ request()->is('about') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium hover:text-white">About</a>
                        <a href="/contact"
                            class="{{ request()->is('contact') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium hover:text-white">Contact</a> --}}
          </div>
      </div>
  </div>
