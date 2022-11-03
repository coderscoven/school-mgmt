<style>
  .logo {
    margin: auto;
    font-size: 20px;
    background: white;
    padding: 7px 11px;
    border-radius: 50% 50%;
    color: #000000b3;
  }
</style>

<nav class="navbar navbar-light fixed-top bg-primary p-0">
  <div class="container-fluid mt-2 mb-2">
    <div class="col-lg-12">
      <div class="col-md-1 float-left" style="display: flex;">

      </div>
      <div class="col-md-4 float-left text-white d-flex align-items-center">
        <a href="" class="text-white text-decoration-none">
          <img src="assets/img/icon-48x48.png" alt="Logo" style="width: 20px; height: 20px" class="mr-2">
          <strong>
            <?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : '' ?>
          </strong>
        </a>
      </div>
      <div class="float-right">
        <div class=" dropdown mr-4">
          <a href="#" class="text-white dropdown-toggle font-weight-bold" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo "Welcome " . $_SESSION['login_name'] ?>
          </a>
          <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
            <a class="dropdown-item small" href="javascript:void(0)" id="manage_my_account" data-usersession="<?php echo $_SESSION['login_id']; ?>">
              <i class="fas fa-user-circle"></i> Account</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item small" href="ajax.php?action=logout"><i class="fas fa-sign-out"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>

</nav>