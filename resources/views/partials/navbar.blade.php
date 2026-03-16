<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background: linear-gradient(90deg, #007bff, #0056b3) !important;">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button" style="transition: all 0.3s ease; padding: 12px 15px;">
        <i class="fas fa-bars text-white" style="font-size: 18px;"></i>
      </a>
    </li>
  </ul>

  <!-- SEARCH FORM -->

  <!-- <ul></ul> -->
  <form class="form-inline ml-3">
    <div class="input-group input-group-sm" style="min-width: 280px;">
      <input class="form-control form-control-navbar" type="search" placeholder="Cari menu, data, atau fitur..." aria-label="Search" 
              style="border-radius: 25px 0 0 25px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); height: 38px; font-size: 14px;">
      <div class="input-group-append">
        <button class="btn btn-navbar text-white" type="submit" 
                style="border-radius: 0 25px 25px 0; background: rgba(255,255,255,0.2)">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
  </form>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto mt-2">
    <!-- Settings Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link text-white" data-toggle="dropdown" href="#" style="transition: all 0.3s ease; padding: 12px 15px;">
        <i class="fas fa-cog text-white" style="font-size: 18px;"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); min-width: 280px;">
          <div class="dropdown-item-text text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin: -8px -16px 8px -16px; padding: 20px;">
            <div style="text-transform: uppercase;">
              <strong style="font-size: 16px;">{{ Auth::user()->name ?? 'User' }}</strong>
              <div style="font-size: 12px; opacity: 0.9;">{{ Auth::user()->email ?? '' }}</div>
              <div style="font-size: 11px; opacity: 0.8; margin-top: 4px;">
                <i class="fas fa-circle text-success mr-1" style="font-size: 8px;"></i>Online
              </div>
            </div>
          </div>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="padding: 12px 16px;">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset($assetPath . 'dist/img/cpi-logo.png') }}" alt="Paperless Future Logo" class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold" >Paperless Further</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset($assetPath . 'dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info" style="text-transform: uppercase;">
                <a href="#" class="d-block">
                    {{ Auth::user()->role ?? '-' }}<br>
                    @if(Auth::user()->role === 'superadmin')
                        <span class="badge badge-success">All Plan</span>
                    @else
                        <span class="badge badge-success">{{Auth::user()->plan->nama_plan ?? '-'}}</span>
                    @endif
                </a>
            </div>
        </div>
        <hr>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item has-treeview">
              <a href="{{ url('/super-admin/dashboard') }}" class="nav-link {{ request()->is('super-admin/dashboard*') || request()->is('/') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p><b>Dashboard</b></p>
              </a>
            </li>
          @if(in_array(auth()->user()->id_role, [5, 1, 4])) <!-- Superadmin=5, Admin=1 -->
            <!-- Menu Data Master SUPER ADMIN -->
            @php
              $isDataMasterMenu = request()->is('*roles*')
                || request()->is('*plan*')
                || request()->is('*shift*')
                || request()->is('*data-rm*')
                || request()->is('*data-seasoning*')
                || request()->is('*data-defect*')
                || request()->is('*input-area*')
                || request()->is('*data-barang*')
                || request()->routeIs('produk.*')
                || request()->is('*jenis-better*')
                || request()->is('*suhu-adonan*')
                || request()->routeIs('nomor-formula.*')
                || request()->routeIs('bahan-forming.*')
                || request()->routeIs('bahan-non-forming.*')
                || request()->routeIs('bahan-emulsi.*')
                || request()->is('*jenis-emulsi*')
                || request()->is('*total-pemakaian-emulsi*')
                || request()->is('*nomor-emulsi*')
                || request()->is('*input-mesin-peralatan*');
            @endphp
            <li class="nav-item has-treeview {{ $isDataMasterMenu ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ $isDataMasterMenu ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fa fa-briefcase"></i>
                <p>Data Master 
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if(auth()->user()->id_role == 5) <!-- Hanya Superadmin -->
                <li class="nav-item">
                  <a href="{{ route ('access-control.index') }}" class="nav-link {{ request()->is('*access-control*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Access Control</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('roles.index') }}" class="nav-link {{ request()->is('*roles*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Role</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('plan.index') }}" class="nav-link {{ request()->is('*plan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Plan</p>
                  </a>
                </li>
                @endif
                <li class="nav-item">
                  <a href="{{ route ('data-shift.index') }}" class="nav-link {{ request()->is('*shift*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Shift</p>
                  </a>
                </li>
                
                <li class="nav-item">
                  <a href="{{ route ('data-rm.index') }}" class="nav-link {{ request()->is('*data-rm*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Data RM</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-seasoning.index') }}" class="nav-link {{ request()->is('*data-seasoning*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Data Seasoning</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-defect.index') }}" class="nav-link {{ request()->is('*data-defect*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Data Defect</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('input-area.index') }}" class="nav-link {{ request()->is('*input-area*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Area</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-barang.index') }}" class="nav-link {{ request()->is('*data-barang*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Data Barang</p>
                  </a>
                </li>
                
                <li class="nav-item">
                  <a href="{{ route ('produk.index') }}" class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Jenis Produk</p>
                  </a>
                </li>
                
                <li class="nav-item has-treeview {{ request()->routeIs('nomor-formula.*') || request()->routeIs('bahan-forming.*') || request()->routeIs('bahan-non-forming.*')  ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ request()->routeIs('nomor-formula.*') || request()->routeIs('bahan-forming.*') || request()->routeIs('bahan-non-forming.*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-code nav-icon"></i>
                    <p>
                      Formula & Bahan
                      <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ route ('nomor-formula.index') }}" class="nav-link {{ request()->routeIs('nomor-formula.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Nomor F. Forming</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('bahan-forming.index')}}" class="nav-link {{ request()->routeIs('bahan-forming.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Bahan Forming</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('bahan-non-forming.index')}}" class="nav-link {{ request()->routeIs('bahan-non-forming.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Bahan Non Forming</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item has-treeview {{ request()->routeIs('jenis-emulsi.*') || request()->routeIs('total-pemakaian-emulsi.*') || request()->routeIs('nomor-emulsi.*') || request()->routeIs('bahan-emulsi.*') ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ request()->routeIs('jenis-emulsi.*') || request()->routeIs('total-pemakaian-emulsi.*') || request()->routeIs('nomor-emulsi.*') || request()->routeIs('bahan-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-flask nav-icon"></i>
                    <p>
                        Emulsi
                      <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ route ('jenis-emulsi.index')}}" class="nav-link {{ request()->routeIs('jenis-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Jenis Emulsi</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('total-pemakaian-emulsi.index')}}" class="nav-link {{ request()->routeIs('total-pemakaian-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Total Pemakaian Emulsi</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('nomor-emulsi.index')}}" class="nav-link {{ request()->routeIs('nomor-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Nomor Proses Emulsi</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('bahan-emulsi.index')}}" class="nav-link {{ request()->routeIs('bahan-emulsi.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Bahan Emulsi</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('suhu-adonan.index')}}" class="nav-link {{ request()->is('*suhu-adonan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Suhu Adonan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('jenis-better.index')}}" class="nav-link {{ request()->is('*jenis-better*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Formula Better</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('jenis-breader.index')}}" class="nav-link {{ request()->is('*jenis-breader*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Jenis Breader</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('jenis-predust.index')}}" class="nav-link {{ request()->is('*jenis-predust*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Jenis Predust</p>
                  </a>
                </li>
                <li class="nav-item has-treeview {{ request()->routeIs('suhu-frayer-1.*') || request()->routeIs('suhu-frayer-2.*') || request()->routeIs('waktu-penggorengan.*') || request()->routeIs('waktu-penggorengan-2.*') || request()->routeIs('std-suhu-pusat.*') ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ request()->routeIs('suhu-frayer-1.*') || request()->routeIs('suhu-frayer-2.*') || request()->routeIs('waktu-penggorengan.*') || request()->routeIs('waktu-penggorengan-2.*') || request()->routeIs('std-suhu-pusat.*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-fire nav-icon"></i>
                    <p>
                      Penggorengan
                      <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ route ('suhu-frayer-1.index')}}" class="nav-link {{ request()->routeIs('suhu-frayer-1.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Suhu Frayer (1, 3, 4, 5)</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route('suhu-frayer-2.index') }}" class="nav-link {{ request()->routeIs('suhu-frayer-2.*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Suhu Frayer (2)</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('std-suhu-pusat.index')}}" class="nav-link {{ request()->is('*std-suhu-pusat*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> STD Suhu Pusat</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('std-salinitas-viskositas.index')}}" class="nav-link {{ request()->is('*std-salinitas-viskositas*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Salinitas dan Viskositas</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-tumbling.index')}}" class="nav-link {{ request()->is('*data-tumbling*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Data Tumbling</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('input-mesin-peralatan.index')}}" class="nav-link {{ request()->is('*input-mesin-peralatan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Input Mesin Peralatan</p>
                  </a>
                </li>
                
                <li class="nav-item has-treeview {{ request()->is('*suhu-blok*') || request()->is('*std-fan*') || request()->is('*std-suhu-pusat-roasting*') ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ request()->is('*suhu-blok*') || request()->is('*std-fan*') || request()->is('*std-suhu-pusat-roasting*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-snowflake nav-icon"></i>
                    <p>
                      Roasting
                      <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ route ('suhu-blok.index')}}" class="nav-link {{ request()->is('*suhu-blok*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Suhu Pemasakan</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('std-fan.index')}}" class="nav-link {{ request()->is('*std-fan*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Standart Fan/Humidity</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('std-suhu-pusat-roasting.index')}}" class="nav-link {{ request()->is('*std-suhu-pusat-roasting*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> STD Suhu Pusat</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item has-treeview {{ request()->is('*nama-formula-fla*') || request()->is('*nomor-step-formula-fla*') || request()->is('*bahan-formula-fla*') ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ request()->is('*nama-formula-fla*') || request()->is('*nomor-step-formula-fla*') || request()->is('*bahan-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class=" fas fa-th nav-icon"></i>
                    <p>
                      Produk Fla
                      <i class="fas fa-arrow-circle-down left ml-2" style="font-size:15px;"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ route ('nama-formula-fla.index')}}" class="nav-link {{ request()->is('*nama-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Formula FLA</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('nomor-step-formula-fla.index')}}" class="nav-link {{ request()->is('*nomor-step-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Step Formula</p>
                      </a>  
                    </li>           
                    <li class="nav-item">
                      <a href="{{ route ('bahan-formula-fla.index')}}" class="nav-link {{ request()->is('*bahan-formula-fla*') ? 'active' : '' }}" style="font-size:12px;">
                        <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                        <p> Bahan Fla</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route ('std-berat-rheon.index')}}" class="nav-link {{ request()->is('*std-berat-rheon*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="far fa-dot-circle nav-icon" style="font-size:8px;"></i>
                    <p> Input Std Rheon</p>
                  </a>
                </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-bag.index')}}" class="nav-link {{ request()->is('*data-bag*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Standard BAG/PACK</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-box.index')}}" class="nav-link {{ request()->is('*data-box*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Standart BOX</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-timbangan.index')}}" class="nav-link {{ request()->is('*data-timbangan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Data Timbangan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route ('data-thermo.index')}}" class="nav-link {{ request()->is('*data-thermo*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Data Thermometer</p>
                  </a>
                </li>
              </ul>
            </li>
          @endif
          @if(in_array(auth()->user()->id_role, [1, 2, 3, 4, 5])) <!-- Semua role -->
            <!-- MENU QC SISTEM -->
            <li class="nav-item has-treeview {{ request()->is('qc-sistem/chillroom*') || request()->is('qc-sistem/seasoning*') || request()->is('qc-sistem/shoestring*') || request()->is('qc-sistem/rebox*') || request()->is('qc-sistem/pemeriksaan-bahan-kemas*') ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->is('qc-sistem/chillroom*') || request()->is('qc-sistem/seasoning*') 
              || request()->is('qc-sistem/shoestring*') || request()->is('qc-sistem/rebox*') || request()->is('qc-sistem/pemeriksaan-bahan-kemas*') ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-inbox"></i>
                <p>
                  Penerimaan Bahan
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/chillroom') }}" class="nav-link {{ request()->is('qc-sistem/chillroom*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Chillroom</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/seasoning') }}" class="nav-link {{ request()->is('qc-sistem/seasoning*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Seasoning</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/shoestring') }}" class="nav-link {{ request()->is('qc-sistem/shoestring*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Shoestring</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/rebox') }}" class="nav-link {{ request()->is('qc-sistem/rebox*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Rebox</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pemeriksaan-bahan-kemas') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-bahan-kemas*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Kemasan</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->is('qc-sistem/persiapan-bahan-forming*') || request()->is('qc-sistem/persiapan-bahan-emulsi*') || request()->is('qc-sistem/persiapan-bahan-better*') ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-forming*') || request()->is('qc-sistem/persiapan-bahan-emulsi*') || request()->is('qc-sistem/persiapan-bahan-better*') ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                  Persiapan Bahan
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/persiapan-bahan-forming') }}" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-forming*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Bahan Forming</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/persiapan-bahan-emulsi') }}" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-emulsi*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Bahan Emulsi</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/persiapan-bahan-better') }}" class="nav-link {{ request()->is('qc-sistem/persiapan-bahan-better*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Bahan Better</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'penggorengan' || request()->segment(2) == 'pembuatan-predust' || request()->segment(2) == 'proses-battering' || request()->segment(2) == 'proses-breader' 
            || request()->segment(2) == 'proses-frayer' || request()->segment(2) == 'hasil-penggorengan' || request()->segment(2) == 'pembekuan-iqf-penggorengan' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->is('qc-sistem/penggorengan*') || request()->is('qc-sistem/pemasakan*') ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-fire"></i>
                <p>
                  Penggorengan
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/penggorengan') }}" class="nav-link {{ request()->is('qc-sistem/penggorengan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Penggorengan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pembuatan-predust') }}" class="nav-link {{ request()->is('qc-sistem/pembuatan-predust*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Predusting</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/proses-battering') }}" class="nav-link {{ request()->is('qc-sistem/proses-battering*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Battering</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/proses-breader') }}" class="nav-link {{ request()->is('qc-sistem/proses-breader*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Breadering</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/proses-frayer') }}" class="nav-link {{ request()->is('qc-sistem/proses-frayer*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Frayer</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/hasil-penggorengan') }}" class="nav-link {{ request()->is('qc-sistem/hasil-penggorengan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Hasil Penggorengan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pembekuan-iqf-penggorengan') }}" class="nav-link {{ request()->is('qc-sistem/pembekuan-iqf-penggorengan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pembekuan IQF</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'input-roasting' || request()->segment(2) == 'proses-roasting-fan' || request()->segment(2) == 'hasil-proses-roasting' 
            || request()->segment(2) == 'pembekuan-iqf-roasting' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'input-roasting' || request()->segment(2) == 'proses-roasting-fan' 
              || request()->segment(2) == 'hasil-proses-roasting' || request()->segment(2) == 'pembekuan-iqf-roasting' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-temperature-high"></i>
                <p>
                  Roasting
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/input-roasting') }}" class="nav-link {{ request()->is('qc-sistem/input-roasting*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Roasting</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/proses-roasting-fan') }}" class="nav-link {{ request()->is('qc-sistem/proses-roasting-fan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Proses Roasting</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/hasil-proses-roasting') }}" class="nav-link {{ request()->is('qc-sistem/hasil-proses-roasting*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Hasil Proses Roasting</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pembekuan-iqf-roasting') }}" class="nav-link {{ request()->is('qc-sistem/pembekuan-iqf-roasting*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pembekuan IQF</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="{{ url('/qc-sistem/proses-twahing') }}" class="nav-link {{ request()->is('qc-sistem/proses-twahing*') ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-thermometer-half"></i>
                <p>Pemeriksaan Proses Thawing</p>
              </a>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'bahan-baku-tumbling' || request()->segment(2) == 'proses-aging' || request()->segment(2) == 'proses-tumbling' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'bahan-baku-tumbling' || request()->segment(2) == 'proses-aging' || request()->segment(2) == 'proses-tumbling' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-sync-alt"></i>
                <p>
                  Proses Tumbling
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/bahan-baku-tumbling') }}" class="nav-link {{ request()->is('qc-sistem/bahan-baku-tumbling*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Bahan Baku Tumbling</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/proses-tumbling') }}" class="nav-link {{ request()->is('qc-sistem/proses-tumbling*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Proses Tumbling</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/proses-aging') }}" class="nav-link {{ request()->is('qc-sistem/proses-aging*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Proses Aging</p>
                  </a>
                </li>

              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'pengemasan-produk' || request()->segment(2) == 'pengemasan-plastik' || request()->segment(2) == 'berat-produk' || request()->segment(2) == 'pengemasan-karton' || request()->segment(2) == 'dokumentasi' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'pengemasan-produk' || request()->segment(2) == 'pengemasan-plastik' || request()->segment(2) == 'berat-produk' || request()->segment(2) == 'pengemasan-karton' || request()->segment(2) == 'dokumentasi' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-box"></i>
                <p>
                  Pengemasan
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pengemasan-produk') }}" class="nav-link {{ request()->is('qc-sistem/pengemasan-produk*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-angle-down nav-icon" style="font-size:8px;"></i>
                    <p>Pengemasan Produk</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pengemasan-plastik') }}" class="nav-link {{ request()->is('qc-sistem/pengemasan-plastik*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-angle-down nav-icon" style="font-size:8px;"></i>
                    <p>Pengemasan Plastik</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/berat-produk')}}" class="nav-link {{ request()->is('qc-sistem/berat-produk*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-angle-down nav-icon" style="font-size:8px;"></i>
                    <p>Berat Produk (Pack-Box)</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pengemasan-karton') }}" class="nav-link {{ request()->is('qc-sistem/pengemasan-karton*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-angle-down nav-icon" style="font-size:8px;"></i>
                    <p>Pengemasan Karton</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/dokumentasi') }}" class="nav-link {{ request()->is('qc-sistem/dokumentasi*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-angle-down nav-icon" style="font-size:8px;"></i>
                    <p>Dokumentasi</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'input-metal-detector' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'input-metal-detector' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-balance-scale"></i>
                <p>
                  Metal Detector
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/input-metal-detector') }}" class="nav-link {{ request()->is('qc-sistem/input-metal-detector*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Input Metal Detector</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'pembuatan-sample' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'pembuatan-sample' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-cubes"></i>
                <p>
                  Pembuatan Sample Produk
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pembuatan-sample') }}" class="nav-link {{ request()->is('qc-sistem/pembuatan-sample*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pembuatan Sampel</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'produk-yum' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'produk-yum' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-calendar-check"></i>
                <p>
                  KPI Berat Produk YUM
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/produk-yum') }}" class="nav-link {{ request()->is('qc-sistem/produk-yum*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>KPI Produk YUM</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'verifikasi-berat-produk' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'verifikasi-berat-produk' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-weight-hanging"></i>
                <p>
                  Verifikasi Berat per Tahapan
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/verifikasi-berat-produk') }}" class="nav-link {{ request()->is('qc-sistem/verifikasi-berat-produk*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p> Berat Per Tahapan</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'pemeriksaan-produk-cooking-mixer-fla' || request()->segment(2) == 'pemeriksaan-rheon-machine' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'pemeriksaan-produk-cooking-mixer-fla' || request()->segment(2) == 'pemeriksaan-rheon-machine' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-th"></i>
                <p>
                  Produk Fla
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pemeriksaan-produk-cooking-mixer-fla') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-produk-cooking-mixer-fla*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Cooking Mixer</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pemeriksaan-rheon-machine') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-rheon-machine*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Rheon Machine</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'pemeriksaan-rice-bites' || request()->segment(2) == 'pemasakan-nasi' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'pemeriksaan-rice-bites' || request()->segment(2) == 'pemasakan-nasi' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-indent"></i>
                <p>
                  Produk Rice Bites
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pemeriksaan-rice-bites') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-rice-bites*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pemeriksaan Produk Rice Bites</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pemasakan-nasi') }}" class="nav-link {{ request()->is('qc-sistem/pemasakan-nasi*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pemasakan Nasi</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'produk-forming' || request()->segment(2) == 'produk-non-forming' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'produk-forming' || request()->segment(2) == 'produk-non-forming' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-retweet"></i>
                <p>
                  Pergantian Proses Produksi
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/produk-forming') }}" class="nav-link {{ request()->is('qc-sistem/produk-forming*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Produk Forming</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/produk-non-forming') }}" class="nav-link {{ request()->is('qc-sistem/produk-non-forming*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Produk Non Forming</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'ketidaksesuaian-plastik' || request()->segment(2) == 'ketidaksesuaian-benda-asing' || request()->segment(2) == 'pemeriksaan-benda-asing' || request()->segment(2) == 'pemeriksaan-proses-produksi' ?  'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'ketidaksesuaian-plastik' || request()->segment(2) == 'ketidaksesuaian-benda-asing' || request()->segment(2) == 'pemeriksaan-benda-asing' || request()->segment(2) == 'pemeriksaan-proses-produksi' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-cloud"></i>
                <p>
                  Laporan Temuan
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/ketidaksesuaian-plastik') }}" class="nav-link {{ request()->is('qc-sistem/ketidaksesuaian-plastik*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Ketidaksesuaian Plastik</p>
                  </a>
                </li>
                <!-- <li class="nav-item">
                  <a href="{{ url('/qc-sistem/ketidaksesuaian-benda-asing') }}" class="nav-link {{ request()->is('qc-sistem/ketidaksesuaian-benda-asing*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Kontaminasi Benda Asing</p>
                  </a>
                </li> -->
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pemeriksaan-benda-asing') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-benda-asing*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pemeriksaan Benda Asing</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/pemeriksaan-proses-produksi') }}" class="nav-link {{ request()->is('qc-sistem/pemeriksaan-proses-produksi*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pemeriksaan Produksi</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'area-proses' || request()->segment(2) == 'gmp-karyawan' || request()->segment(2) == 'kontrol-sanitasi' || request()->segment(2) == 'barang-mudah-pecah' || request()->segment(2) == 'verif-peralatan' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'area-proses' || request()->segment(2) == 'gmp-karyawan' || request()->segment(2) == 'kontrol-sanitasi' || request()->segment(2) == 'barang-mudah-pecah' || request()->segment(2) == 'verif-peralatan' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-unlink"></i>
                <p>
                  Pemeriksaan Kondisi Area
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/area-proses') }}" class="nav-link {{ request()->is('qc-sistem/area-proses*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Area Proses</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/gmp-karyawan') }}" class="nav-link {{ request()->is('qc-sistem/gmp-karyawan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>GMP Karyawan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/kontrol-sanitasi') }}" class="nav-link {{ request()->is('qc-sistem/kontrol-sanitasi*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Kontrol Sanitasi</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/verif-peralatan') }}" class="nav-link {{ request()->is('qc-sistem/verif-peralatan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Verifikasi Peralatan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/verif-cip') }}" class="nav-link {{ request()->is('qc-sistem/verif-peralatan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Pemeriksaan CIP</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/barang-mudah-pecah') }}" class="nav-link {{ request()->is('qc-sistem/barang-mudah-pecah*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Barang Mudah Pecah</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item has-treeview {{ request()->segment(2) == 'timbangan' || request()->segment(2) == 'thermometer' ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->segment(2) == 'timbangan' || request()->segment(2) == 'thermometer' ? 'active' : '' }}" style="font-size:13px;">
                <i class="nav-icon fas fa-sticky-note"></i>
                <p>
                  Pemeriksaan Peralatan QC
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/timbangan') }}" class="nav-link {{ request()->is('qc-sistem/timbangan*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Timbangan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('/qc-sistem/thermometer') }}" class="nav-link {{ request()->is('qc-sistem/thermometer*') ? 'active' : '' }}" style="font-size:12px;">
                    <i class="fas fa-circle nav-icon" style="font-size:8px;"></i>
                    <p>Thermometer</p>
                  </a>
                </li>
              </ul>
            </li>
          @endif
          @if(auth()->user()->id_role == 5)
            <li class="nav-header">PENGATURAN</li>
            <li class="nav-item">
              <a href="{{ url('/super-admin/profile') }}" class="nav-link" style="font-size:13px;">
                <i class="nav-icon fa fa-user-alt"></i>
                <p>
                  Profile
                  <span class="badge badge-info right">2</span>
                </p>
              </a>
            </li>
          @endif
        </ul>
      </nav>
    </div>
</aside>