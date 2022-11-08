<!DOCTYPE html>
<html lang="en">

<?php include 'classes/MainClass.php'; ?>

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : '' ?></title>


  <?php
  if (!isset($_SESSION['login_id']))
    header('location:login.php');
  include('./header.php');
  // include('./auth.php'); 
  ?>

</head>
<style>
  body {
    background: #80808045;
  }

  .modal-dialog.large {
    width: 80% !important;
    max-width: unset;
  }

  .modal-dialog.mid-large {
    width: 50% !important;
    max-width: unset;
  }

  #viewer_modal .btn-close {
    position: absolute;
    z-index: 999999;
    /*right: -4.5em;*/
    background: unset;
    color: white;
    border: unset;
    font-size: 27px;
    top: 0;
  }

  #viewer_modal .modal-dialog {
    width: 80%;
    max-width: unset;
    height: calc(90%);
    max-height: unset;
  }

  #viewer_modal .modal-content {
    background: black;
    border: unset;
    height: calc(100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #viewer_modal img,
  #viewer_modal video {
    max-height: calc(100%);
    max-width: calc(100%);
  }
</style>

<body>
  <?php include 'topbar.php' ?>
  <?php include 'navbar.php' ?>
  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white">
    </div>
  </div>

  <main id="view-panel">
    <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-3">
          <div class="col-sm-6">
            <?php
            $pagetitle = "";
            $pageText = "";
            switch ($page) {
              case "home":
                $pagetitle = '<h1><i class="fas fa-dashboard" role="img" aria-label="Dashboard"></i> DASHBOARD</h1>';
                $pageText = "Home";
                break;
              case "fees":
                $pagetitle = '<h1><i class="fas fa-money-check" role="img" aria-label="Fees"></i> FEES</h1>';
                $pageText = "Fees";
                break;
              case "payments":
                $pagetitle = '<h1><i class="fas fa-receipt" role="img" aria-label="Payments"></i> PAYMENTS</h1>';
                $pageText = "Payments";
                break;
              case "classes":
                $pagetitle = '<h1><i class="fas fa-scroll" role="img" aria-label="Classes"></i> Classes</h1>';
                $pageText = "Classes";
                break;
              case "teachers":
                $pagetitle = '<h1><i class="fas fa-chalkboard-teacher" role="img" aria-label="Teachers"></i> TEACHERS</h1>';
                $pageText = "Teachers";
                break;
              case "students":
                $pagetitle = '<h1><i class="fas fa-graduation-cap" role="img" aria-label="Students"></i> STUDENTS</h1>';
                $pageText = "Students";
                break;
              case "requirements":
                $pagetitle = '<h1><i class="fas fas fa-tools" role="img" aria-label="Requirements"></i> STUDENT REQUIREMENTS</h1>';
                $pageText = "Requirements";
                break;
              case "parents":
                $pagetitle = '<h1><i class="fas fa-user-gear" role="img" aria-label="Parents"></i> PARENTS</h1>';
                $pageText = "Parents";
                break;
              case "payments_report":
                $pagetitle = '<h1><i class="fas fa-th-list" role="img" aria-label="Reports"></i> REPORTS</h1>';
                $pageText = "Reports";
                break;
              case "users":
                $pagetitle = '<h1><i class="fas fa-users" role="img" aria-label="Users"></i> USERS</h1>';
                $pageText = "Users";
                break;
            }
            ?>
            <?php echo $pagetitle; ?>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="javascript:;"><?php echo "Term 1" ?></a></li>
              <li class="breadcrumb-item active"><?php echo 2022; ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <?php include $page . '.php' ?>
  </main>


  <?php require_once 'footer.php'; ?>
</body>

</html>