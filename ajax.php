<?php
ob_start();
$action = $_GET['action'];
include 'classes/MainClass.php';

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
	$save = $crud->save_user();
	if ($save)
		echo $save;
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
	$save = $crud->save_student();
	if ($save)
		echo $save;
}
if ($action == "delete_student") {
	$delete = $crud->delete_student();
	if ($delete)
		echo $delete;
}



// ------------------------------------
//	fees
// ------------------------------------

if ($action == "save_school_fees") {
	$save = $crud->save_school_fees();
	if ($save)
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

if ($action == "save_fees") {
	$save = $crud->save_fees();
	if ($save)
		echo $save;
}
if ($action == "delete_fees") {
	$delete = $crud->delete_fees();
	if ($delete)
		echo $delete;
}
if ($action == "save_payment") {
	$save = $crud->save_payment();
	if ($save)
		echo $save;
}
if ($action == "delete_payment") {
	$delete = $crud->delete_payment();
	if ($delete)
		echo $delete;
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


// -----------------------
ob_end_flush();
