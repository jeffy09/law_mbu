<nav class="navbar navbar-light navbar-expand-lg">
        <div class="container-fluid">
                <div class="navbar-nav" id="navbarNav">
                        <ul class="navbar-nav">
                                <!-- <li class="nav-item">
                                        <a class="nav-link hide-sidebar-toggle-button" href="#"><i class="material-icons">first_page</i></a>
                                </li> -->
                                <li class="nav-item">
                                        <a class="nav-link" href="#">
                                                <font size="3">กฎหมายลำดับรอง มหาวิทยาลัยมหามกุฏราชวิทยาลัย</font>
                                        </a>
                                </li>
                                <!-- <li class="nav-item dropdown hidden-on-mobile">
                                        <a class="nav-link dropdown-toggle" href="#" id="exploreDropdownLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="material-icons-outlined">explore</i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-lg large-items-menu" aria-labelledby="exploreDropdownLink">
                                                <li>
                                                        <h6 class="dropdown-header">Repositories</h6>
                                                </li>
                                                <li>
                                                        <a class="dropdown-item" href="#">
                                                                <h5 class="dropdown-item-title">
                                                                        Neptune iOS
                                                                        <span class="badge badge-warning">1.0.2</span>
                                                                        <span class="hidden-helper-text">switch<i class="material-icons">keyboard_arrow_right</i></span>
                                                                </h5>
                                                                <span class="dropdown-item-description">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                                                        </a>
                                                </li>
                                                <li>
                                                        <a class="dropdown-item" href="#">
                                                                <h5 class="dropdown-item-title">
                                                                        Neptune Android
                                                                        <span class="badge badge-info">dev</span>
                                                                        <span class="hidden-helper-text">switch<i class="material-icons">keyboard_arrow_right</i></span>
                                                                </h5>
                                                                <span class="dropdown-item-description">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                                                        </a>
                                                </li>
                                                <li class="dropdown-btn-item d-grid">
                                                        <button class="btn btn-primary">Create new repository</button>
                                                </li>
                                        </ul>
                                </li> -->
                        </ul>

                </div>
                
                        <div class="d-flex">
                                <ul class="navbar-nav">
                                        <li class="nav-item">
                                                <a class="nav-link nav-notifications-toggle" id="notificationsDropDown" href="#" data-bs-toggle="dropdown"><?= $_SESSION['first_name']; ?></a>
                                                <div class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropDown">
                                                        <h6 class="dropdown-header">Action</h6>
                                                        <div class="notifications-dropdown-list">
                                                                <a href="#">
                                                                        <div class="notifications-dropdown-item">
                                                                                <div class="notifications-dropdown-list">
                                                                                        <form action="index.php?page=logout" method="post">
                                                                                                <button type="submit" class="btn btn-danger m-b-xs">Logout</button>
                                                                                        </form>
                                                                                </div>
                                                                        </div>
                                                                </a>
                                                        </div>
                                                </div>
                                        </li>
                                </ul>
                        </div>
                
        </div>
</nav>