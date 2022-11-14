<!DOCTYPE html>
<html lang="en">

<?php
include 'classes/MainClass.php';

// breadcrumb
$breadcrumb = $crud->school_term_breadcrumb();
?>

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
  <div class="toast d-print-none" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white">
    </div>
  </div>

  <main id="view-panel">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $trackStudent = isset($_GET['student']) ? $_GET['student'] : n_a;
    ?>
    <section class="content-header d-print-none">
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
                $pagetitle = '<h1><i class="fas fa-money-check" role="img" aria-label="Fees"></i> STUDENT FEES</h1>';
                $pageText = "Student Fees";
                break;
              case "payments":
                $pagetitle = '<h1><i class="fas fa-receipt" role="img" aria-label="Payments"></i> MAKE PAYMENTS</h1>';
                $pageText = "Make Payments";
                break;
              case "classes":
                $pagetitle = '<h1><i class="fas fa-scroll" role="img" aria-label="Classes"></i> Classes</h1>';
                $pageText = "Classes";
                break;
              case "teachers":
                $pagetitle = '<h1><i class="fas fa-chalkboard-teacher" role="img" aria-label="Teachers"></i> TEACHERS</h1>';
                $pageText = "Teachers";
                break;
              case "roll-call":
                $pagetitle = '<h1><i class="fas fa-bullhorn" role="img" aria-label="Roll call"></i> STUDENT ROLL CALL</h1>';
                $pageText = "Roll call";
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
                $pagetitle = '<h1><i class="fas fa-th-list" role="img" aria-label="Reports"></i> PAYMENT REPORT</h1>';
                $pageText = "Payment Reports";
                break;
              case "requirements_report":
                $pagetitle = '<h1><i class="fas fa-th-list" role="img" aria-label="Reports"></i> REQUIREMENT REPORT</h1>';
                $pageText = "Requirement Reports";
                break;
              case "students_report":
                $pagetitle = '<h1><i class="fas fa-th-list" role="img" aria-label="Reports"></i> STUDENTS REPORT</h1>';
                $pageText = "Students Reports";
                break;
              case "teacher_details_report":
                $pagetitle = '<h1 class="text-uppercase"><i class="fas fa-th-list" role="img" aria-label="Reports"></i> Teacher details REPORT</h1>';
                $pageText = "Teacher Details Reports";
                break;
              case "users":
                $pagetitle = '<h1><i class="fas fa-users" role="img" aria-label="Users"></i> USERS</h1>';
                $pageText = "Users";
                break;
              case "payment-history":
                $pagetitle = '<h1><i class="fas fa-info-circle" role="img" aria-label="Payments"></i> PAYMENTS</h1>';
                $pageText = "Payments";
                break;
            }
            ?>
            <?php echo $pagetitle; ?>
          </div>
          <div class="col-sm-6 d-print-none">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="javascript:;" id="set_school_term"><?php echo "Term " . $breadcrumb['term']; ?></a></li>
              <li class="breadcrumb-item active" id="school_term_year"><?php echo $breadcrumb['year'] ?></li>
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