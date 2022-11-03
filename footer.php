<div id="preloader"></div>
<a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
            </div>
            <div class="modal-body">
                <div id="delete_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-dismiss="modal"><span class="fas fa-times"></span></button>
            <img src="" alt="">
        </div>
    </div>
</div>


<!-- // footer scripts -->
<!-- .. jquery -->
<script src="assets/vendor/jquery/jquery.min.js"></script>
<!-- .. bootstrap -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- .. jquery ui -->
<script src="assets/vendor/jquery-ui/jquery-ui.min.js"></script>
<!-- .. datatables -->
<script src="assets/vendor/dataTables/datatables.min.js"></script>
<!-- .. jquery.easing -->
<script src="assets/vendor/jquery.easing/jquery.easing.min.js"></script>
<!-- .. validate -->
<script src="assets/vendor/php-email-form/validate.js"></script>
<!-- .. venobox -->
<script src="assets/vendor/venobox/venobox.min.js"></script>
<!-- .. jquery.waypoints -->
<script src="assets/vendor/waypoints/jquery.waypoints.min.js"></script>
<!-- .. counterup -->
<script src="assets/vendor/counterup/counterup.min.js"></script>
<!-- .. carousel -->
<script src="assets/vendor/owl.carousel/owl.carousel.min.js"></script>
<!-- .. toastr -->
<script src="assets/vendor/toastr/toastr.min.js"></script>
<!-- .. moment -->
<script src="assets/vendor/moment/moment.min.js"></script>
<!-- .. fullcalendar -->
<script src="assets/vendor/fullcalendar/main.min.js"></script>
<script src="assets/vendor/fullcalendar-daygrid/main.min.js"></script>
<script src="assets/vendor/fullcalendar-timegrid/main.min.js"></script>
<script src="assets/vendor/fullcalendar-interaction/main.min.js"></script>
<script src="assets/vendor/fullcalendar-bootstrap/main.min.js"></script>
<!-- .. bootstrap-datepicker -->
<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<!-- .. select2 -->
<script src="assets/vendor/select2/js/select2.full.min.js"></script>
<!-- overlayScrollbars -->
<script src="assets/vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- .. datetimepicker -->
<script src="assets/js/jquery.datetimepicker.full.min.js"></script>
<!-- .. Chartjs -->
<script src="assets/vendor/chart.js/Chart.min.js"></script>
<!-- .. jquery-te -->
<script src="assets/js/jquery-te-1.4.0.min.js" charset="utf-8"></script>
<?php if ($_GET['page'] == 'home') { ?>
    <script src="assets/js/dashboard3.js"></script>
<?php } ?>
<!-- .. site script -->
<script src="assets/js/main.min.js"></script>