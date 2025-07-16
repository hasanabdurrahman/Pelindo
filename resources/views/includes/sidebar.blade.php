<div id="app">
    <div id="sidebar" class="active">
        <div class="sidebar-wrapper active">
            <div class="sidebar-header position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a class="navbar-brand m-0" href="#" target="_blank">
                            <img src="{{ asset('assets/images/logo/logo-edi.png') }}" class="navbar-brand-img" alt="main_logo" style="height:60px;border-radius:10px" >
                        </a>
                    </div>
                    <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                        <div class="form-check form-switch fs-6">
                            <span class="nav-link-text xs-1">Dark Mode</span>
                        </div>
                        <div class="form-check form-switch fs-6">
                            <input class="form-check-input  me-0" type="checkbox" id="toggle-dark">
                            <label class="form-check-label"></label>
                        </div>
                    </div>
                    <div class="sidebar-toggler  x">
                        <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                    </div>
                </div>
            </div>
            <div class="sidebar-menu">
                <ul class="menu">
                    {!!getmenu()!!}
                </ul>
            </div>
        </div>
    </div>