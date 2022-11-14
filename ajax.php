<?php
ob_start();
$action = $_GET['action'];
include 'classes/MainClass.php';


//
if ($action == 'login') {
	$login = $crud->login();
	if ($login)
		echo $login;
}
if ($action == 'login2') {
	$login = $crud->login2();
	if ($login)
		echo $login;
}
if ($action == 'logout') {
	$logout = $crud->logout();
	if ($logout)
		echo $logout;
}
if ($action == 'logout2') {
	$logout = $crud->logout2();
	if ($logout)
		echo $logout;
}
if ($action == 'save_user') {
	echo $crud->save_user();
}
if ($action == 'delete_user') {
	$save = $crud->delete_user();
	if ($save)
		echo $save;
}
if ($action == 'signup') {
	$save = $crud->signup();
	if ($save)
		echo $save;
}
if ($action == 'update_account') {
	$save = $crud->update_account();
	if ($save)
		echo $save;
}
if ($action == "save_settings") {
	$save = $crud->save_settings();
	if ($save)
		echo $save;
}

// ------------------------------------
//	classes
// ------------------------------------

if ($action == "save_class") {
	$save = $crud->save_class();
	if ($save)
		echo $save;
}
if ($action == "delete_class") {
	$delete = $crud->delete_class();
	if ($delete)
		echo $delete;
}



// ------------------------------------
//	students
// ------------------------------------
if ($action == "save_student") {
	echo $crud->save_student();
}

if ($action == "delete_student") {
	$delete = $crud->delete_student();
	if ($delete)
		echo $delete;
}



// ------------------------------------
//	school term
// ------------------------------------
if ($action == "check_if_school_is_init") {
	echo $crud->check_if_school_is_init();
}

//
if ($action == "school_term_dt") {
	$crud->school_term_datatable();
}

// save school term
if ($action == "set_school_term") {
	echo $crud->set_school_term();
}

// toggle school term status
if ($action == "toggle_school_term_status") {
	echo $crud->toggle_school_term_status();
}

// delete school term
if ($action == "delete_school_term") {
	echo $crud->delete_school_term();
}





// ------------------------------------
//	fees
// ------------------------------------

// student details before making payments
if ($action == "get_student_details_on_fee") {
	echo $crud->get_student_details_on_fee();
}


// save fees struture
if ($action == "save_school_fees") {
	$save = $crud->save_school_fees();
	echo $save;
}

// fetch fees struture
if ($action == "fetch_all_fees_structure") {
	echo $crud->fetch_all_fees_structure();
}

// delete fee structure
if ($action == "delete_fees_structure") {
	$delete = $crud->delete_fees_structure();
	if ($delete)
		echo $delete;
}

// ----

// save fees and enroll student
if ($action == "enroll_and_set_fees") {
	echo $crud->enroll_and_set_fees();
}

// save payment
if ($action == "save_payment") {
	echo $crud->save_payment();
}

// delete fees enrollment
if ($action == "delete_enrollment_and_fees") {
	$delete = $crud->delete_enrollment_and_fees();
	if ($delete)
		echo $delete;
}

// delete payment
if ($action == "delete_payment") {
	echo $crud->delete_payment();
}

// ------------------------------------
//	requirments
// ------------------------------------

// add requirements
if ($action == 'save_requirements') {
	$save = $crud->save_requirements();
	if ($save)
		echo $save;
}

// fetch all school requirements
if ($action == "fetch_all_requirments") {
	echo $crud->fetch_all_requirments();
}

// delete requirement
if ($action == "delete_requirement") {
	$delete = $crud->delete_requirement();
	if ($delete)
		echo $delete;
}

// get students based on selected class
if ($action == "load_students_based_on_sel_class") {
	echo $crud->load_students_based_on_sel_class();
}

// get students based on selected requirements
if ($action == "load_requirements_based_on_sel_class") {
	echo $crud->load_requirements_based_on_sel_class();
}

// get students requirements brought in so far
if ($action == "load_student_requirements") {
	echo $crud->load_student_requirements();
}


// save student requirements
if ($action == "save_student_requirements") {
	echo $crud->save_student_requirements();
}


// load classes
if ($action == "load_class_student") {
	echo $crud->load_class_student();
}



// ------------------------------------
//	teachers
// ------------------------------------

// add new / edit teacher information
if ($action == 'save_teacher') {
	$save = $crud->save_teacher();
	if ($save)
		echo $save;
}
// delete teacher
if ($action == "delete_teacher") {
	$delete = $crud->delete_teacher();
	if ($delete)
		echo $delete;
}



// ------------------------------------
//	parents
// ------------------------------------

// add new / edit parents information
if ($action == 'save_parent') {
	$save = $crud->save_parent();
	if ($save)
		echo $save;
}
// delete parent
if ($action == "delete_parent") {
	$delete = $crud->delete_parent();
	if ($delete)
		echo $delete;
}

// ------------------------------------
//	reports
// ------------------------------------

// payments report
if ($action == "payments_report") {
	echo $crud->payments_report();
}

// requirements
if ($action == "requirements_reports") {
	echo $crud->requirements_reports();
}

// students report
if ($action == "students_reports") {
	echo $crud->students_reports();
}

// teachers info report
if ($action == "teacher_info_report") {
	echo $crud->teacher_info_report();
}


// ------------------------------------
//	charts
// ------------------------------------

// number of students per class
if ($action == "num_of_students_per_class_chart") {
	echo $crud->num_of_students_per_class_chart();
}

// daily payments
if ($action == "daily_fee_payment_chart") {
	echo $crud->daily_fee_payment_chart();
}



// ------------------------------------
//	misc
// ------------------------------------

// student roll call
if ($action == "load_student_roll_call") {
	echo $crud->load_student_roll_call();
}

// save roll call
if ($action == "save_roll_call_information") {
	echo $crud->save_roll_call_information();
}





// -----------------------
ob_end_flush();
