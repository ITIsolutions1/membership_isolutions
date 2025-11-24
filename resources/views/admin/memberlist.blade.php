@extends('adminlte::page')

@section('title', 'memberlist')

@section('content_header')
    <h1 class="mb-4">Event List</h1>
@endsection

@section('content')
@php
    $currentSort = request()->get('sort', 'created_at');
    $currentDirection = request()->get('direction', 'desc');
@endphp
<div>
     @if (session('success'))
          <div id="popup-success" class="alert alert-success alert-dismissible fade show my-4 shadow-sm" role="alert" style="font-size: 1rem; animation: slideIn 0.5s ease-out;">
              <i class="fas fa-check-circle mr-2"></i>
              <strong>Sukses!</strong> {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      @endif

       @if (session('success_create'))
          <div id="popup-success" class="alert alert-success alert-dismissible fade show my-4 shadow-sm" role="alert" style="font-size: 1rem; animation: slideIn 0.5s ease-out;">
              <i class="fas fa-check-circle mr-2"></i>
              <strong>Sukses!</strong> {{ session('success_create') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      @endif

      @if (session('error'))
          <div id="popup-error" class="alert alert-danger alert-dismissible fade show my-4 shadow-sm" role="alert" style="font-size: 1rem; animation: slideIn 0.5s ease-out;">
              <i class="fas fa-check-circle mr-2"></i>
              <strong>Error!</strong> {{ session('error') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      @endif

      @if (session('error_create'))
          <div id="popup-error" class="alert alert-danger alert-dismissible fade show my-4 shadow-sm" role="alert" style="font-size: 1rem; animation: slideIn 0.5s ease-out;">
              <i class="fas fa-check-circle mr-2"></i>
              <strong>Error!</strong> {{ session('error_create') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      @endif

      <div class="m-3">
        <form method="GET" class="form-inline mb-3">
            <div class="">
                <select name="peminatan_id" class="form-control mr-2">
                    <option value="">-- Semua Peminatan --</option>
                    <option value="nonton" {{ request('peminatan_id') == 'nonton' ? 'selected' : '' }}>Nonton</option>
                    <option value="seminar" {{ request('peminatan_id') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                    <option value="training development" {{ request('peminatan_id') == 'training development' ? 'selected' : '' }}>Training Development</option>
                </select>
            </div>

            <input type="text" name="search" class="form-control mr-2" placeholder="Cari nama, domisili, email" value="{{ request('search') }}">
            
            <select name="per_page" class="form-control mr-2" onchange="this.form.submit()">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
            </select>

            <button type="submit" class="btn btn-primary">Terapkan</button>
        </form>
        <a class="btn btn-primary" href="{{ route('member.create') }}">Crete new Member</a>
     </div>

    <div>

     

        <table class="table table-hover table-striped mb-0 ">
            <thead class="thead-dark">
                <tr>
                <th scope="col">No</th>
                {{-- <th scope="col">Nama</th> --}}
                {{-- <th> --}}
               <th scope="col">
                    <span>Nomor Member</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'user_id', 'direction' => 'asc', 'page' => 1]) }}"
                        style="text-decoration: none; {{ $currentSort == 'user_id' && $currentDirection == 'asc' ? 'font-weight: bold;' : '' }}">
                        &#9650;
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'user_id', 'direction' => 'desc', 'page' => 1]) }}"
                        style="text-decoration: none; {{ $currentSort == 'user_id' && $currentDirection == 'desc' ? 'font-weight: bold;' : '' }}">
                        &#9660;
                    </a>
                </th>


                <th>
                    <span>Nama</span>
                    <span>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama', 'direction' => 'asc', 'page' => 1]) }}"
                            style="text-decoration: none; {{ $currentSort == 'nama' && $currentDirection == 'asc' ? 'font-weight: bold;' : '' }}">
                                &#9650;
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama', 'direction' => 'desc', 'page' => 1]) }}"
                            style="text-decoration: none; {{ $currentSort == 'nama' && $currentDirection == 'desc' ? 'font-weight: bold;' : '' }}">
                                &#9660;
                        </a>
                    </span>
                </th>
                <!-- Domisili -->
                <th>
                    <span>Domisili</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'domisili', 'direction' => 'asc', 'page' => 1]) }}">
                        &#9650;
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'domisili', 'direction' => 'desc', 'page' => 1]) }}">
                        &#9660;
                    </a>
                </th>

                <!-- Email -->
                <th>
                    <span>Email</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => 'asc', 'page' => 1]) }}">
                        &#9650;
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => 'desc', 'page' => 1]) }}">
                        &#9660;
                    </a>
                </th>

                <!-- Level -->
                <th>
                    <span>Level</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'level', 'direction' => 'asc', 'page' => 1]) }}">
                        &#9650;
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'level', 'direction' => 'desc', 'page' => 1]) }}">
                        &#9660;
                    </a>
                </th>

                 <th>
                    <span>Sponsor</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'sponsor', 'direction' => 'asc', 'page' => 1]) }}">
                        &#9650;
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'sponsor', 'direction' => 'desc', 'page' => 1]) }}">
                        &#9660;
                    </a>
                </th>

                <th scope="col">Peminatan</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($member as $index => $item)
                    <tr>                
                        <td style="vertical-align: middle">{{ ($member instanceof \Illuminate\Pagination\LengthAwarePaginator ? $member->firstItem() + $index : $loop->iteration) }}</td>
                        <td style="vertical-align: middle">
                            {{ str_pad($item->user->id, 6, '0', STR_PAD_LEFT) }}
                        </td>

                        <td style="vertical-align: middle">{{ $item->nama }}</td>
                        <td style="vertical-align: middle">{{ $item->domisili }}</td>
                        <td style="vertical-align: middle">{{ $item->user->email }}</td>
                        <td style="vertical-align: middle">{{ \Illuminate\Support\Str::title($item->level) }}</td>
                        <td style="vertical-align: middle">{{ $item->sponsor }}</td>
                        
                       <td class="d-flex flex-column">
                            @foreach ($item->peminatan as $p)
                                <span class="badge badge-info mb-1">{{ $p->peminatan }}</span>
                            @endforeach
                        </td>
                        <td>
                          <div class="dropdown">
                              <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                                Option
                              </a>

                              <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('member.delete', $item->user->id) }}" onclick=" return confirm('Apakah Anda Yakin Mau Menghapus User Ini ? ')">Delete</a>
                                <a class="dropdown-item" href="{{ route('member.edit', $item->user->id) }}">Edit</a>                                                                  
                              </div>
                            </div>              
                        </td>                        
                    </tr>    
                @endforeach                               
            </tbody>
        </table>
    </div>
    <div class="px-3 pb-3 d-flex justify-content-end">
        @if(method_exists($member, 'links'))
            {{ $member->links() }}
        @endif
    </div>

</div>

<!-- Modal -->
{{-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> --}}

@endsection