<?php
session_start();
ini_set('display_errors', 1);
//
include 'config/connect.php';

class MainClass extends DBConnect
{


	/**
	 * mysqli database connect
	 * @var	mixed
	 */
	private $db;

	/**
	 * pdo database connect
	 * @var	mixed
	 */
	private $pdo;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		ob_start();

		//
		$dbConn = new DBConnect();

		//
		$this->db = $dbConn->dbObjConnect();
		//
		$this->pdo = $dbConn->dbPDOConnect();
	}


	/**
	 * __destruct
	 *
	 * @return void
	 */
	function __destruct()
	{
		// close connection
		$this->db->close();
		$this->pdo = null;
		//
		ob_end_flush();
	}


	/**
	 * check if school is init
	 *
	 * @return mixed
	 */
	public function check_if_school_is_init()
	{

		$i = 0;
		$sch_sts = yes;
		$response = array();

		//
		$check = $this->pdo->prepare("SELECT * from sch_terms");

		if ($check->execute() === false) {
			$response =  array("msg" => "Please set school term!", "bool" => false);
		} else {
			$isactive = $this->db->prepare("SELECT * from sch_terms where sch_sts = ?");
			$isactive->bind_param("s", $sch_sts);
			$isactive->execute();
			$isactive->store_result();

			if ($isactive->num_rows === 0) {
				$response =  array("msg" => "Please activate a school term!", "bool" => false);
			} else
				$response =  array("msg" => "School term set", "bool" => true);
		}
		return json_encode($response);
	}


	/**
	 * generate enrollement reference number
	 *
	 * @return mixed
	 */
	public function gen_enrollement_ref_number()
	{
		$stmt = $this->pdo->prepare("SELECT id FROM track_fee_payments ORDER BY id DESC LIMIT 1");
		$stmt->execute();
		$result = $stmt->fetchObject();
		$id = $result ? $result->id + 1 : 1;
		//
		return $id . '-' . date('Y-m') . '-' . strtoupper($this->genRandomString(6));
	}


	/**
	 * system settings
	 * 
	 * @return	mixed
	 */
	function systemSettings()
	{
		return $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
	}


	/**
	 * error_logs
	 *
	 * @param  mixed $msg
	 * @return void
	 */
	private function error_logs($msg)
	{
		$dt = $this->genDateTime();
		//
		$stmt = $this->pdo->prepare("INSERT into error_logs(error_details, created_at, updated_at) values (:error_details, :created_at, :updated_at)");
		$stmt->execute([
			'error_details' => $msg, 'created_at' => $dt, 'updated_at' => $dt
		]);
	} // end


	/**
	 * login
	 *
	 * @return mixed
	 */
	function login()
	{
		extract($_POST);

		$response = array();

		//
		if (empty($username)) {
			$response = array("msg" => "Please enter your username!", "bool" => false);
		} elseif (empty($password)) {
			$response = array("msg" => "Please enter your password!", "bool" => false);
		} elseif (strlen($password) < 8) {
			$response = array("msg" => "Password can not be less than 8 characters!", "bool" => false);
		} else {

			//
			$smtp = $this->pdo->prepare("SELECT * from users where username = :username");
			$smtp->execute([
				"username" => $username
			]);
			//
			$result = $smtp->fetchObject();

			if ($result) {

				// hashed password
				$passwordHashed = $result->password;

				// check
				if (password_verify($password, $passwordHashed)) {

					// access level
					if ($result->access_level == $access_level) {

						// set session
						$_SESSION['login_id'] 			= $result->id;
						$_SESSION['login_name'] 		= $result->name;
						$_SESSION['login_username'] 	= $result->username;
						$_SESSION['login_access_level'] = $result->access_level;
						$_SESSION['login_type'] 		= $result->type;

						// response
						$response = array("msg" => "Login successful", "bool" => true);
					} else {
						$response = array("msg" => "Invalid access level entered. Please try again!", "bool" => false);
					}
				} else {
					$response = array("msg" => "Invalid password entered. Please try again!", "bool" => false);
				}
			} else {
				$response = array("msg" => "Invalid username entered. Please try again!", "bool" => false);
			}
		}
		return json_encode($response);
	}


	/**
	 * login 2
	 *
	 * @return void
	 */
	function login2()
	{
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM complainants where email = '" . $email . "' and password = '" . md5($password) . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			return 1;
		} else {
			return 3;
		}
	}


	/**
	 * logout
	 *
	 * @return void
	 */
	function logout()
	{
		session_destroy();
		unset(
			$_SESSION['login_id'],
			$_SESSION['login_name'],
			$_SESSION['login_username'],
			$_SESSION['login_access_level'],
			$_SESSION['login_type']
		);

		// foreach ($_SESSION as $key => $value) {
		// 	unset($_SESSION[$key]);
		// }
		header("location:login.php");
	}


	/**
	 * logout 2
	 *
	 * @return void
	 */
	function logout2()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}


	/**
	 * add new user or update existing information
	 *
	 * @return mixed
	 */
	function save_user()
	{

		try {
			extract($_POST);

			// validation
			if (empty($name)) {
				return $this->json_response(422, "Please enter name(s)!");
			} elseif (empty($username)) {
				return $this->json_response(422, "Please enter username!");
			} elseif (empty($accesslevel)) {
				return $this->json_response(422, "Please select access type!");
			} elseif (strlen($username) > 10) {
				return $this->json_response(422, "Username can not exceed 10 characters!");
			} elseif (strlen($name) > 30) {
				return $this->json_response(422, "Names can not exceed 30 characters!");
			} elseif (strlen($name) < 3) {
				return $this->json_response(422, "Invalid name entered. Please try again!");
			} elseif (strlen($username) < 3) {
				return $this->json_response(422, "Invalid username entered. Please try again!");
			} elseif (!empty($password) && strlen($password) < 8) {
				return $this->json_response(422, "Password can not be less than 8 characters!");
			} else {

				//
				$stmt = $this->pdo->prepare("SELECT * from users where username = :username and id != :id");
				$stmt->execute(["username" => $username, "id" => $id]);
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($result) {
					return $this->json_response(422, "Username entered already exists!");
				} else {

					//
					$dt = $this->genDateTime();

					//
					$type = "";
					if ($accesslevel == LV_1) : $type = 1;
					elseif ($accesslevel == LV_2) : $type = 2;
					else : $type = 3;
					endif;

					//
					if (empty($id)) {

						// hash password
						$hashedPassword = password_hash($password, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));

						//
						$save = $this->pdo->prepare("INSERT INTO users (name, username, access_level, password, type, created_at, updated_at) 
					values (:name, :username, :access_level, :password, :type, :created_at, :updated_at)");
						$save->execute([
							"name" => $name,
							"username" => $username,
							"access_level" => $type,
							"password" => $hashedPassword,
							"type" => $type,
							"created_at" => $dt,
							"updated_at" => $dt,
						]);
						//
						return $this->json_response(200, "New user successully added!", true);
					} else {

						if (!empty($password)) {

							// hash password
							$hashedPassword = password_hash($password, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));
							//
							$stmt = $this->pdo->prepare("UPDATE users SET name = ?, username = ?, access_level = ?, password = ?, type = ?, updated_at = ? WHERE id = ?")->execute([$name, $username, $accesslevel, $hashedPassword, $type, $dt, $id]);
						} else {
							//
							$stmt = $this->pdo->prepare("UPDATE users SET name = ?, username = ?, access_level = ?, type = ?, updated_at = ? WHERE id = ?")->execute([$name, $username, $accesslevel, $type, $dt, $id]);
						}
						// check if user is editing their own information
						if ($_SESSION['login_id'] == $id) :
							$_SESSION['login_access_level'] = $accesslevel;
							$_SESSION['login_type'] 		= $type;
						endif;

						//
						return $this->json_response(200, "User information updated!", true);
					}
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			return $this->json_response(422, "An error occured. Please try again later");
		}
	}


	/**
	 * delete user
	 *
	 * @return void
	 */
	function delete_user()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = " . $id);
		if ($delete)
			return 1;
	}


	/**
	 * sign up
	 *
	 * @return mixed
	 */
	function signup()
	{
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", address = '$address' ";
		$data .= ", contact = '$contact' ";
		$data .= ", password = '" . md5($password) . "' ";
		$chk = $this->db->query("SELECT * from complainants where email ='$email' " . (!empty($id) ? " and id != '$id' " : ''))->num_rows;
		if ($chk > 0) {
			return 3;
			exit;
		}
		if (empty($id))
			$save = $this->db->query("INSERT INTO complainants set $data");
		else
			$save = $this->db->query("UPDATE complainants set $data where id=$id ");
		if ($save) {
			if (empty($id))
				$id = $this->db->insert_id;
			$qry = $this->db->query("SELECT * FROM complainants where id = $id ");
			if ($qry->num_rows > 0) {
				foreach ($qry->fetch_array() as $key => $value) {
					if ($key != 'password' && !is_numeric($key))
						$_SESSION['login_' . $key] = $value;
				}
				return 1;
			} else {
				return 3;
			}
		}
	}


	/**
	 * update account
	 *
	 * @return mixed
	 */
	function update_account()
	{
		extract($_POST);
		$data = " name = '" . $firstname . ' ' . $lastname . "' ";
		$data .= ", username = '$email' ";
		if (!empty($password))
			$data .= ", password = '" . md5($password) . "' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if ($chk > 0) {
			return 2;
			exit;
		}
		$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if ($save) {
			$data = '';
			foreach ($_POST as $k => $v) {
				if ($k == 'password')
					continue;
				if (empty($data) && !is_numeric($k))
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if ($_FILES['img']['tmp_name'] != '') {
				$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
				$data .= ", avatar = '$fname' ";
			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if ($data) {
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if ($login)
					return 1;
			}
		}
	}


	/**
	 * save settings
	 *
	 * @return mixed
	 */
	function save_settings()
	{
		extract($_POST);
		$data = " name = '" . str_replace("'", "&#x2019;", $name) . "' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '" . htmlentities(str_replace("'", "&#x2019;", $about)) . "' ";
		if ($_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", cover_img = '$fname' ";
		}

		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if ($chk->num_rows > 0) {
			$save = $this->db->query("UPDATE system_settings set " . $data);
		} else {
			$save = $this->db->query("INSERT INTO system_settings set " . $data);
		}
		if ($save) {
			$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
			foreach ($query as $key => $value) {
				if (!is_numeric($key))
					$_SESSION['system'][$key] = $value;
			}

			return 1;
		}
	}



	// -------------------------- -------------------------------------------------------------------
	//		students
	// -------------------------- -------------------------------------------------------------------



	/**
	 * fetch students list and display in table
	 * 
	 * @return	void
	 */
	function studentsList()
	{

		$student = $this->db->query("SELECT s.id, s.photo, s.school_section, s.name, s.id_no, s.dob, s.gender, s.email, h.house_name, c.class_name FROM student s join houses h on s.house_id = h.id join class_streams c on s.class_id = c.id order by s.name asc ");

		//
		$isbursar = $_SESSION['login_access_level'] == LV_2 ? 'disabled' : '';

		//
		while ($row = $student->fetch_assoc()) :
?>
			<tr>
				<td><?php echo $row['class_name'] ?></td>
				<td><?php echo $row['school_section'] ?></td>
				<td>
					<p><img src="<?php echo $row['photo'] ?>" alt="" class="img-fluid" style="height: 50px"></p>
				</td>
				<td>
					<p> <b><?php echo $row['id_no'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo ucwords($row['name']) ?></b></p>
				</td>
				<td class="">
					<p class="small">DOB: <i><b><?php echo $row['dob'] ?></i></p>
					<p class="small">Gender: <i><b><?php echo $row['gender'] ?></i></p>
					<p class="small">Email: <i><b><?php echo $row['email'] ?></i></p>
					<p class="small">House: <i><b><?php echo $row['house_name'] ?></i></p>
				</td>
				<td class="text-center">
					<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">
						<button class="btn btn-outline-primary edit_student" type="button" data-id="<?php echo $row['id'] ?>" <?php echo $isbursar; ?>>Edit</button>
						<button class="btn btn-outline-danger delete_student" type="button" data-id="<?php echo $row['id'] ?>" <?php echo $isbursar; ?>>Delete</button>
					</div>
				</td>
			</tr>
			<?php endwhile;
	} // end





	/**
	 * save student
	 *
	 * @return mixed
	 */
	function save_student()
	{
		$response = "";

		try {
			extract($_POST);

			// validation
			if (empty($id_no)) {
				$response = $this->json_response(422, "Please enter student id number!");
			} elseif (empty($name)) {
				$response = $this->json_response(422, "Please enter student names!");
			} elseif (empty($id) && empty($_FILES['student_photo']['name'])) {
				$response = $this->json_response(422, "Please select student photo!");
			} elseif (empty($email)) {
				$response = $this->json_response(422, "Please enter student email!");
			} elseif (empty($genders)) {
				$response = $this->json_response(422, "Please select student gender!");
			} elseif (empty($dob)) {
				$response = $this->json_response(422, "Please select student date of birth!");
			} elseif (empty($stud_house)) {
				$response = $this->json_response(422, "Please select student house!");
			} elseif (empty($stud_class)) {
				$response = $this->json_response(422, "Please select student class!");
			} else {

				// get school section
				$school_section_qry = $this->pdo->prepare("SELECT b.school_section FROM class_details a join fees b on b.id = a.fees_id WHERE a.class_id = :id");
				$school_section_qry->execute(["id" => $stud_class]);
				$school_section = $school_section_qry->fetchObject();


				$data = "";
				foreach ($_POST as $k => $v) {
					if (!in_array($k, array('id')) && !is_numeric($k)) {
						if (empty($data)) {
							$data .= " $k='$v' ";
						} else {
							$data .= ", $k='$v' ";
						}
					}
				}

				// check if student id number exists
				$stmt = $this->db->query("SELECT * FROM student where id_no ='$id_no' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
				if ($stmt > 0) {
					$response = $this->json_response(422, "Student ID number entered already exists!");
				} else {

					//
					$dt = $this->genDateTime();

					// school term
					$school_term = $this->school_term_breadcrumb()['id'];

					//
					if (empty($id)) {

						// upload
						$target_file = STUD_PHOTO_DIR . basename($_FILES["student_photo"]["name"]);
						$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
						$check = getimagesize($_FILES["student_photo"]["tmp_name"]);
						if ($check !== false) {

							if ($_FILES["student_photo"]["size"] < 1024000) {

								if (!in_array($imageFileType, array("jpg", "png", "jpeg"))) {
									$response = $this->json_response(422, "Only JPG, JPEG, PNG files are allowed.");
								} else {

									//
									$ext = strtolower(pathinfo($_FILES["student_photo"]["name"], PATHINFO_EXTENSION));

									// last id
									$qrylastid = $this->pdo->prepare("SELECT id FROM student ORDER BY id DESC LIMIT 1");
									$qrylastid->execute();
									$lastidobj = $qrylastid->fetchObject();
									$lastid = !$lastidobj ? 1 : $lastidobj->id + 1;
									//
									$uploaddir = STUD_PHOTO_DIR . $lastid;
									if (!is_dir($uploaddir)) {
										mkdir($uploaddir, 0777);
									}
									$newName = $uploaddir . '/' . $lastid . '.' . $ext;

									// upload file
									move_uploaded_file($_FILES["student_photo"]["tmp_name"], $newName);

									// new student
									$save = $this->pdo->prepare("INSERT INTO student (photo, id_no, name, gender, email, dob, house_id, class_id, school_section, created_at, updated_at) values (:photo, :id_no, :name, :gender, :email, :dob, :house_id, :class_id, :schsection, :created_at, :updated_at)");
									$save->execute([
										'photo' => $newName,
										'id_no' => $id_no,
										'name' => $name,
										'gender' => $genders,
										'email' => $email,
										'dob' => $dob,
										'house_id' => $stud_house,
										'class_id' => $stud_class,
										'schsection' => $school_section->school_section,
										'created_at' => $dt,
										'updated_at' => $dt
									]);
									$stud_id = $this->pdo->lastInsertId();

									//
									$amountotpayqry = $this->pdo->prepare("SELECT b.amount from class_details a join fees b on a.fees_id = b.id where a.class_id = :class_id");
									$amountotpayqry->execute(["class_id" => $stud_class]);
									$amountotpay = $amountotpayqry->fetchObject();
									$amout_to_pay = $amountotpay->amount;

									//
									$response = $this->json_response(200, "New student added!", true);
								}
							} else {
								$response = $this->json_response(422, "Photo must not exceed 1mb!");
							}
						} else {
							$response = $this->json_response(422, "Photo selected is not an image!");
						}
					} else {

						//
						$amountotpayqry = $this->pdo->prepare("SELECT b.amount from class_details a join fees b on a.fees_id = b.id where a.class_id = :class_id");
						$amountotpayqry->execute(["class_id" => $stud_class]);
						$amountotpay = $amountotpayqry->fetchObject();
						$amout_to_pay = $amountotpay->amount;

						//
						$this->pdo->prepare("UPDATE student set id_no = ?, name = ?, gender = ?, email = ?, dob = ?, house_id = ?, class_id = ?, school_section = ?, updated_at = ? where id = ?")->execute([$id_no, $name, $genders, $email, $dob, $stud_house, $stud_class, $school_section->school_section, $dt, $id]);
						//
						$response = $this->json_response(200, "Student information updated!", true);
					}
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later:");
		}
		//
		return $response;
	}


	/**
	 * delete student
	 *
	 * @return mixed
	 */
	function delete_student()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM student where id = " . $id);
		if ($delete) {
			return 1;
		}
	}


	/**
	 * select students;
	 *
	 * @return mixed
	 */
	function select_students($ispdo = false)
	{
		if ($ispdo) {
			$stmt = $this->pdo->prepare("SELECT * from student order by name asc");
			return $stmt;
		} else {
			$stmt = $this->db->query("SELECT * from student order by name asc");
			return $stmt;
		}
	}


	/**
	 * select students; if a student has already been enrolled; do not show
	 *
	 * @return mixed
	 */
	function select_students_for_enrollment()
	{


		// current school term
		$term_sql = $this->pdo->prepare("SELECT id FROM sch_terms WHERE sch_sts = :sts");
		$term_sql->execute(["sts" => yes]);
		$term = $term_sql->fetchObject();
		$termid = $term->id;

		//
		$students = array();

		//
		$stmt = $this->pdo->prepare("SELECT id, name, id_no from student order by name asc");
		$stmt->execute();
		$results = $stmt->fetchAll();

		$stmt_qry2 = $this->pdo->prepare("SELECT school_term FROM track_fee_payments WHERE stud_id = :id and school_term = :termid");

		foreach ($results as $result) {

			$stmt_qry2->execute(["id" => $result['id'], "termid" => $termid]);
			$stmt_qry = $stmt_qry2->fetchObject();

			if (!$stmt_qry) {
				$students[] = $result;
			}
		}

		return $students;
	} // end


	/**
	 * get selected student eflist
	 *
	 * @param  mixed $id
	 * @return mixed
	 */
	function get_sel_track_fee_payments($id)
	{
		$stmt = $this->db->query("SELECT * FROM track_fee_payments where id = {$id} ");
		return $stmt;
	} // end




	// -------------------------- -------------------------------------------------------------------
	//		fees and payments
	// -------------------------- -------------------------------------------------------------------



	/**
	 * delete fees structure
	 *
	 * @return mixed
	 */
	function delete_fees_structure()
	{
		extract($_POST);
		$smtpt = $this->db->query("SELECT * FROM class_details where fees_id = " . $id)->num_rows;
		if ($smtpt > 0) {
			return 2;
		} else {
			$delete = $this->db->query("DELETE FROM fees where id = " . $id);
			if ($delete)
				return 1;
		}
	} // end


	/**
	 * fetch fee school section
	 *
	 * @return mixed
	 */
	function fetch_fee_school_section()
	{
		$stmt = $this->pdo->prepare("SELECT id, school_section from fees");
		$stmt->execute();
		return $stmt->fetchAll();
	} // end


	/**
	 * fetch fees structure
	 *
	 * @return mixed
	 */
	function fetch_all_fees_structure()
	{

		try {
			$stmt = $this->pdo->prepare("SELECT * from fees");
			$stmt->execute();
			$results = $stmt->fetchAll();

			if ($results) {

				//
				$view = "<div class='table-responsive'>";
				$view .= '<table class="table table-hover table-stripped" id="fees_structure_dt">';
				$view .= '<thead class="bg-secondary text-light">';
				$view .= '<tr>';
				$view .= '<th scope="col">#</th>';
				$view .= '<th scope="col">Section</th>';
				$view .= '<th scope="col">Amount</th>';
				$view .= '<th scope="col">Action</th>';
				$view .= '</tr>';
				$view .= "</thead>";
				$view .= "</body>";
				$cnt = 1;
				foreach ($results as $result) {

					$view .= '<tr id="tr_fee_' . $result['id'] . '">';
					$view .= '<th scope="row">' . $cnt . '</th>';
					$view .= '<td id="sch_section' . $result['id'] . '">' . $result['school_section'] . '</td>';
					$view .= '<td id="sch_amount' . $result['id'] . '">' . number_format($result['amount'], 2) . '</td>';
					$view .= '<td><div class="btn-group btn-group-sm" role="group" aria-label="action buttons"><button type="button" class="btn btn-sm btn-info edit_fees_structure" data-id="' . $result['id'] . '"><i class="bi-pencil" role="img" aria-label="edit"></i> Edit</button><button type="button" class="btn btn-sm btn-danger delete_fees_structure" data-id="' . $result['id'] . '"><i class="bi-x-circle" role="img" aria-label="delete"></i> Delete</button></div></td>';
					$view .= '</tr>';
					$cnt++;
				}

				$view .= "</body>";
				$view .= "</table>";
				$view .= "</div>";
				//
				return $this->json_response(200, $view, true);
			} else {
				$view = '<div class="alert alert-info fade show d-flex align-items-center justify-content-center" role="alert"><i class="bi-info-circle-fill flex-shrink-0 mr-2" width="24" height="24" role="img" aria-label="Danger:"></i><div>No fee structure added yet!</div></div>';
				//
				return $this->json_response(200, $view, true);
			}
		} catch (Exception $e) {
			$view = '<div class="alert alert-dange fade show d-flex align-items-center justify-content-center" role="alert"><i class="bi-exclamation-triangle-fill flex-shrink-0 mr-2" width="24" height="24" role="img" aria-label="Danger:"></i><div>Internal server. Failed to load fee structure!</div></div>';
			//
			$this->logToFile($e->getMessage());
			//
			return $this->json_response(500, $view, false);
		}
	} // end



	/**
	 * save school fees
	 *
	 * @return mixed
	 */
	function save_school_fees()
	{
		$response = "";

		try {
			extract($_POST);

			$dt = $this->genDateTime();

			if (empty($section)) {
				$response = $this->json_response(422, "<div class='alert alert-danger alert-dismissible fade show'>Please select section!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
			} elseif (empty($amount)) {
				$response = $this->json_response(422, "<div class='alert alert-danger alert-dismissible fade show'>Please enter amount!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
			} elseif (!is_numeric($amount)) {
				$response = $this->json_response(422, "<div class='alert alert-danger alert-dismissible fade show'>Please enter a valid amount!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
			} else {

				if (empty($sch_fees_id)) {

					$save = $this->pdo->prepare("INSERT into fees (school_section, amount, created_at, updated_at) values (?, ?, ?, ?)")->execute([$section, $amount, $dt, $dt]);
					if ($save) {
						$response = $this->json_response(200, "<div class='alert alert-success alert-dismissible fade show'>New school fees structure added!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>", true);
					} else {
						$response = $this->json_response(422, "<div class='alert alert-danger alert-dismissible fade show'>Error adding new school fees structure!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
					}
					//
				} else {
					$update = $this->pdo->prepare("UPDATE fees set school_section = ?, amount = ?, updated_at = ? where id = ?")->execute([$section, $amount, $dt, $sch_fees_id]);
					if ($update) {
						$response = $this->json_response(200, "<div class='alert alert-success alert-dismissible fade show'>School fees structure updated!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>", true);
					} else {
						$response = $this->json_response(422, "<div class='alert alert-danger alert-dismissible fade show'>Error updating school fees structure!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
					}
					//
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "<div class='alert alert-danger alert-dismissible fade show'>Internal server error. Please try again later.<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
		}
		//
		return $response;
	} // end



	/**
	 * fetch fees
	 *
	 * @return mixed
	 */
	function fetch_fees()
	{
		$data = array();
		$stmt = $this->pdo->prepare("SELECT * FROM fees");
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$data[] = $row;
		}
		return $data;
	} // end



	/**
	 * get selected student ef list
	 *
	 * @param  mixed $studid
	 * @return mixed
	 */
	function get_sel_student_ef_list($studid)
	{
		return $this->db->query("SELECT a.*, b.sch_term as tterm, b.sch_year as tyear FROM track_fee_payments a join sch_terms b on a.school_term = b.id where a.id = {$studid} ");
	} // end




	/**
	 * delete fees enrollement
	 *
	 * @return mixed
	 */
	function delete_enrollment_and_fees()
	{
		extract($_POST);
		$delete = $this->pdo->prepare("DELETE FROM track_fee_payments where id = :id")->execute(['id' => $id]);
		if ($delete) {
			$ispayment = $this->db->prepare("SELECT * FROM payments where ef_id = ?");
			$ispayment->bind_param('i', $id);
			$ispayment->execute();
			$result = $ispayment->get_result();
			if ($result->num_rows > 0) {
				$this->pdo->prepare("DELETE FROM payments where ef_id = :ef_id")->execute(['ef_id' => $id]);
			}
			return $this->array_response('Enrollment information deleted!', true);
		} else
			return $this->array_response('Failed to delete information!', false);
	} // end


	/**
	 * enroll student and set fees payment
	 *
	 * @return mixed
	 */
	function enroll_and_set_fees()
	{
		$response = "";

		try {
			extract($_POST);

			if (empty($stud_id)) {
				$response = $this->json_response(422, "Please select student!");
			} else {

				$check = $this->db->query("SELECT * FROM track_fee_payments where ef_no ='$ef_no' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
				if ($check > 0) {
					$response = $this->json_response(422, "Unknown error occured. Please try again!");
				} else {

					//
					$dt = $this->genDateTime();

					//
					$studentqry = $this->pdo->prepare("SELECT id_no from student where id = ?");
					$studentqry->execute([$stud_id]);
					$studentinfo = $studentqry->fetchObject();

					//
					$stud_no = $studentinfo->id_no;

					//
					if (empty($id)) {

						//
						$save = $this->pdo->prepare("INSERT INTO track_fee_payments (stud_id, stud_no, ef_no, school_term, total_fee, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)")->execute([$stud_id, $stud_no, $ef_no, $schtermid, $amout_to_pay, $dt, $dt]);

						//
						if ($save) $response = $this->json_response(200, "Information successful saved!", true);
						else $response = $this->json_response(422, "Error saving Information. Please try again!");
					} else {
						//
						$update = $this->pdo->prepare("UPDATE track_fee_payments set stud_id = ?, updated_at = ? where id = ?")->execute([$stud_id, $dt, $id]);

						//
						if ($update) $response = $this->json_response(200, "Information successfully updated!", true);
						else $response = $this->json_response(422, "Error updating Information. Please try again!");
					}
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}
		//
		return $response;
	}


	/**
	 * save fees payment
	 *
	 * @return mixed
	 */
	function save_payment()
	{
		$response = array();

		try {
			extract($_POST);

			if (empty($payment_serial)) {
				$response = array("msg" => "Please enter payment serial number!", "bool" => false);
			} elseif (empty($payment_date)) {
				$response = array("msg" => "Please select payment date!", "bool" => false);
			} elseif (empty($ef_id)) {
				$response = array("msg" => "Please select student!", "bool" => false);
			} elseif (empty($amount)) {
				$response = array("msg" => "Please enter amount to pay!", "bool" => false);
			} else {

				// trackpaymentid
				$check = $this->db->query("SELECT * FROM payments where slip_serial ='$payment_serial' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
				if ($check > 0) {
					//
					$response = array("msg" => "Payment serial number entered already exists. Please try again!", "bool" => false);
				} else {

					//
					$dt = $this->genDateTime();

					$studentinfoqry = $this->pdo->prepare("SELECT class_id FROM student where id = :id");
					$studentinfoqry->execute(['id' => $stud_id]);
					$studentinfo = $studentinfoqry->fetchObject();

					if (empty($id)) {

						// payments
						$save = $this->pdo->prepare("INSERT INTO payments (stud_id, ef_id, slip_serial, amount, class_id, payment_date, created_at, updated_at) values (:stud_id, :ef_id, :slip_serial, :amount, :class_id, :payment_date, :created_at, :updated_at)");
						$check = $save->execute([
							'stud_id' => $stud_id,
							'ef_id' => $ef_id,
							'slip_serial' => $payment_serial,
							'amount' => $amount,
							'class_id' => $studentinfo->class_id,
							'payment_date' => $payment_date,
							'created_at' => $dt,
							'updated_at' => $dt
						]);

						if ($check) {
							$id = $this->pdo->lastInsertId();
							$response = array("msg" => "Fee payment saved!", "bool" => true, 'ef_id' => $ef_id, 'pid' => $id, 'status' => 1);
						} else
							$response = array("msg" => "Error saving fee payment. Please try again!", "bool" => false);
					} else {

						// payments
						$update = $this->pdo->prepare("UPDATE payments set stud_id = :stud_id, ef_id = :ef_id, slip_serial = :slip_serial, amount = :amount, class_id = :class_id, payment_date = :payment_date, updated_at = :updated_at where id = :id");
						$check = $update->execute([
							'stud_id' => $stud_id,
							'ef_id' => $ef_id,
							'slip_serial' => $payment_serial,
							'amount' => $amount,
							'class_id' => $studentinfo->class_id,
							'payment_date' => $payment_date,
							'updated_at' => $dt,
							'id' => $id
						]);

						if ($check) {
							$response = array("msg" => "Fee payment successfully updated!", "bool" => true, 'ef_id' => $ef_id, 'pid' => $id, 'status' => 1);
						} else
							$response = array("msg" => "Error updating fee payment. Please try again!", "bool" => false);
					}
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$response = array("msg" => "Internal server error. Please try again later!", "bool" => false);
		}
		//
		return json_encode($response);
	} // end


	/**
	 * delete payment
	 *
	 * @return mixed
	 */
	function delete_payment()
	{
		try {
			extract($_POST);
			$delete = $this->db->prepare("DELETE FROM payments where id = ?");
			$delete->bind_param('i', $id);
			$check = $delete->execute();
			if ($check) {
				return $this->json_response(200, "Payment deleted!", true);
			} else
				return $this->json_response(422, "Error deleting payment. Please try again!");
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			return $this->json_response(500, "Internal server error. Please try again later!");
		}
	} // end


	/**
	 * get student details on when paying fees
	 *
	 * @return mixed
	 */
	function edit_student_details_on_fee($id)
	{

		$stmt = $this->pdo->prepare("SELECT b.amount, b.school_section, a.class_id FROM student a join fees b on a.school_section = b.school_section where a.id = :id");
		$stmt->execute(["id" => $id]);
		$results = $stmt->fetchObject();

		$feesamount = $results->amount;
		$schsection = $results->school_section;
		$classid 	= $results->class_id;

		//
		$classqry = $this->pdo->prepare("SELECT class_name FROM class_streams where id = :id");
		$classqry->execute(["id" => $classid]);
		$class = $classqry->fetchObject();

		// check fee balance
		$view = '<fieldset class="border px-2 py-1 border-secondary bg-light mt-4">';
		$view .= '<h6 class="font-weight-bold text-center">DETAILS</h6>';
		$view .= '<p class="mb-1"><strong class="mr-1">Class:</strong>' . $class->class_name . '</p>';
		$view .= '<p class="mb-1"><strong class="mr-1">School section:</strong>' . $schsection . '</p>';
		$view .= '<p class="mb-1"><strong class="mr-1">Fees:</strong>' . number_format($feesamount, 2) . '</p>';
		$view .= '</fieldset>';

		return array("view" => $view, "amount" => $feesamount);
	} // end


	/**
	 * get student details on when paying fees
	 *
	 * @return mixed
	 */
	function get_student_details_on_fee()
	{
		extract($_POST);
		$stmt = $this->pdo->prepare("SELECT b.amount, b.school_section, a.class_id FROM student a join fees b on a.school_section = b.school_section where a.id = :id");
		$stmt->execute(["id" => $id]);
		$results = $stmt->fetchObject();

		$feesamount = $results->amount;
		$schsection = $results->school_section;
		$classid 	= $results->class_id;

		//
		$classqry = $this->pdo->prepare("SELECT class_name FROM class_streams where id = :id");
		$classqry->execute(["id" => $classid]);
		$class = $classqry->fetchObject();

		// check fee balance
		$view = '<fieldset class="border px-2 py-1 border-secondary bg-light mt-4">';
		$view .= '<h6 class="font-weight-bold text-center">DETAILS</h6>';
		$view .= '<p class="mb-1"><strong class="mr-1">Class:</strong>' . $class->class_name . '</p>';
		$view .= '<p class="mb-1"><strong class="mr-1">School section:</strong>' . $schsection . '</p>';
		$view .= '<p class="mb-1"><strong class="mr-1">Fees:</strong>' . number_format($feesamount, 2) . '</p>';
		$view .= '</fieldset>';

		return json_encode(array("view" => $view, "amount" => $feesamount));
	} // end


	/**
	 * get fees
	 *
	 * @return mixed
	 */
	function fees()
	{
		$i = 1;
		$fees = $this->pdo->prepare("SELECT cs.class_name, ef.school_term, ef.id as efid, ef.ef_no, ef.total_fee, s.id as studid, s.name as sname, s.id_no FROM 
		track_fee_payments ef inner join student s on s.id = ef.stud_id inner join class_streams cs on s.class_id = cs.id order by s.name asc ");
		$fees->execute();
		$results = $fees->fetchAll();

		if ($results) :


			$term_sql = $this->pdo->prepare("SELECT sch_term, sch_year FROM sch_terms WHERE id = :id");

			foreach ($results as $row) :

				$term_sql->execute(["id" => $row['school_term']]);
				$term = $term_sql->fetchObject();

				$efterm    = $term->sch_year . '|' . $term->sch_term;
				$efid      = $row['efid'];
				$efno      = $row['ef_no'];
				$total_fee = $row['total_fee'];
				$studid    = $row['studid'];
				$sname     = $row['sname'];
				$id_no     = $row['id_no'];
				$classnm   = $row['class_name'];

				$paid = $this->db->query("SELECT sum(amount) as paid FROM payments where ef_id=" . $efid);
				$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';
				$balance = $total_fee - $paid;

				//
				// <?php echo 'index.php?' . http_build_query(['page' => 'students_report']); 
				$paymentsurl = "index.php?" . http_build_query(['page' => 'payment-history', "studid" => $studid, "efno" => $efno, "efid" => $efid]);
			?>
				<tr>
					<th scope="row"><?php echo $i++ ?></th>
					<td>
						<p><?php echo $efterm ?></p>
					</td>
					<td>
						<p><?php echo $classnm ?></p>
					</td>
					<td>
						<p><?php echo $id_no ?></p>
					</td>
					<td>
						<p><?php echo ucwords($sname) ?></p>
					</td>
					<td class="text-right">
						<p><?php echo number_format($total_fee, 2) ?></p>
					</td>
					<td class="text-right">
						<p><?php echo number_format($paid, 2) ?></p>

					<td class="text-right">
						<p><?php echo number_format($balance, 2) ?></p>
					</td>
					<td class="text-center">
						<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">
							<button class="btn btn-sm btn-primary view_payment" type="button" data-act="f" data-id="<?php echo $efid ?>">View</button>
							<button class="btn btn-info edit_fees" type="button" data-id="<?php echo $efid ?>"><i class="fas fa-pencil" role="img" aria-label="edit"></i> Edit</button>
							<button type="button" class="btn btn-danger delete_enrollment_and_fees" data-id="<?php echo $efid ?>"><i class="fas fa-times-circle" role="img" aria-label="delete"></i> Delete</button>
						</div>
					</td>
				</tr>
			<?php
			endforeach;
		else :
		endif;
	} // end



	/**
	 * fetch selected payments info
	 *
	 * @param  mixed $id
	 * @return mixed
	 */
	function sel_payments_info($id)
	{
		$stmt = $this->db->prepare("SELECT * FROM payments where id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}


	/**
	 * fetch selected payments info using ef no
	 *
	 * @param  mixed $efno
	 * @return mixed
	 */
	function sel_payments_info_efno($efno)
	{
		return $this->db->query("SELECT * FROM payments where ef_id = {$efno}");
	} // end


	/**
	 * fetch added encrollement infos
	 *
	 * @return mixed
	 */
	function fetch_encrollement_infos()
	{

		$stmt = $this->pdo->prepare("SELECT ef.*, s.name as sname, s.id_no, s.id as studid FROM track_fee_payments ef inner join student s on s.id = ef.stud_id order by s.name asc ");
		$stmt->execute();
		return $stmt->fetchAll();
	} // end


	/**
	 * fetch added encrollement infos
	 *
	 * @return mixed
	 */
	function oustandingbalance($efno)
	{
		if (!is_null($efno)) {
			$stmt = $this->pdo->prepare("SELECT total_fee from track_fee_payments where id = :efno");
			$stmt->execute(["efno" => $efno]);
			$result =  $stmt->fetchObject();
			return $result->total_fee;
		} else {
			return "";
		}
	} // end



	/**
	 * fetch_sel_enrollment_payment
	 *
	 * @param  mixed $enrollmentid
	 * @param  mixed $payment
	 * @return mixed
	 */
	function fetch_sel_enrollment_payment($enrollmentid, $payment = null)
	{
		return $this->db->query("SELECT sum(amount) as paid FROM payments where ef_id=" . $enrollmentid . (!is_null($payment) ? " and id!=$payment " : ''));
	}


	/**
	 * payments made after enrollment datatable
	 * 
	 * @return mixed
	 */
	function payments()
	{
		$i = 1;
		$payments = $this->pdo->prepare("SELECT p.*, s.name as sname, ef.ef_no, s.id_no FROM payments p inner join track_fee_payments ef on ef.id = p.ef_id inner join student s on s.id = ef.stud_id order by p.payment_date desc ");

		$view = "";
		if ($payments->execute()) :

			foreach ($payments->fetchAll() as $row) :

				$paid = $this->db->query("SELECT sum(amount) as paid FROM payments where ef_id=" . $row['id']);
				$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';

				//
				$ctobj = new DateTime($row['payment_date']);
				$cto = $ctobj->format("M d, Y");

				$view .= '<tr>';
				$view .= '<th scope="row">' . $i++ . '</th>';
				$view .= '<td>' . $cto . '</td>';
				$view .= '<td>' . $row['slip_serial'] . '</td>';
				$view .= '<td>' . $row['id_no'] . '</td>';
				$view .= '<td>' . ucwords($row['sname']) . '</td>';
				$view .= '<td>' . number_format($row['amount'], 2) . '</td>';
				$view .= '<td class="text-center">';
				$view .= '<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">';
				$view .= '<button class="btn btn-outline-primary view_payment" type="button" data-act="p" data-id="' . $row['id'] . '" data-ef_id="' . $row['ef_id'] . '">View</button>';
				$view .= '<button class="btn btn-outline-primary edit_payment" type="button" data-id="' . $row['id'] . '">Edit</button>';
				$view .= '<button class="btn btn-outline-danger delete_payment" type="button" data-id="' . $row['id'] . '">Delete</button>';
				$view .= '</div>';
				$view .= '</td>';
				$view .= '</tr>';
			endforeach;
		else :
		endif;
		echo $view;
	} // end



	/**
	 * fetch fees amount
	 *
	 * @param  mixed $feesid
	 * @return mixed
	 */
	function sel_fetch_amount($feesid)
	{
		return $this->db->query("SELECT * FROM fees where id = $feesid");
	} // end


	/**
	 * payment receipt
	 *
	 * @param  mixed $ef_id
	 * @return mixed
	 */
	function payment_receipt($ef_id)
	{
		return $this->db->query("SELECT ef.*, s.name as sname, s.id_no, c.class_name, d.fees_id, t.sch_term, t.sch_year FROM track_fee_payments ef 
		inner join 
		sch_terms t on ef.school_term = t.id 
		inner join
		student s on s.id = ef.stud_id 
		inner join 
		class_streams c on c.id = s.class_id 
		inner join 
		class_details d on c.id = d.class_id where ef.id = {$ef_id}");
	} // end




	/**
	 * fetch users
	 * 
	 * @return	void
	 */
	function usersList()
	{
		try {
			$type = array("", "Admin", "Bursar", "Teacher");
			$users = $this->db->query("SELECT * FROM users order by name asc");
			$i = 1;
			while ($row = $users->fetch_assoc()) :
			?>
				<tr>
					<td class="text-center">
						<?php echo $i++ ?>
					</td>
					<td>
						<?php echo ucwords($row['name']) ?>
					</td>

					<td>
						<?php echo $row['username'] ?>
					</td>
					<td>
						<?php echo $type[$row['type']] ?>
					</td>
					<td>
						<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">
							<button type="button" class="btn btn-primary">Action</button>
							<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item edit_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Edit</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item delete_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
							</div>
						</div>
					</td>
				</tr>
			<?php endwhile;
		} catch (Exception $e) {
			$this->logToFile($e->getMessage());
		}
	} // end



	/**
	 * get selected user information
	 * 
	 * @param  int	$id	
	 * @return mixed
	 */
	function getUser($id)
	{
		return $this->db->query("SELECT * FROM users where id =" . $_GET['id']);
	}


	/**
	 * get selected student information
	 * 
	 * @param  int	$id	
	 * @return mixed
	 */
	function getStudent($id)
	{
		return $this->db->query("SELECT * FROM student where id =" . $id);
	}


	/**
	 * get houses
	 * 
	 * @return mixed
	 */
	function getHouses()
	{
		$houses = array();
		$stmt = $this->pdo->prepare("SELECT id, house_name, house_slug FROM houses");
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$houses[] = $row;
		}
		return $houses;
	}


	/**
	 * get classes
	 * 
	 * @return mixed
	 */
	function getClasses()
	{
		$classes = array();
		$stmt = $this->pdo->prepare("SELECT id, class_name FROM class_streams");
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$classes[] = $row;
		}
		return $classes;
	}


	/**
	 * generate student ids
	 * 
	 * @return	mixed
	 */
	function generateStudentIds()
	{
		// last id
		$qrylastid = $this->pdo->prepare("SELECT id FROM student ORDER BY id DESC LIMIT 1");
		$qrylastid->execute();
		$lastidobj = $qrylastid->fetchObject();
		$lastid = !$lastidobj ? 1 : $lastidobj->id + 1;
		return date("Y") . '-00' . $lastid;
	}


	/**
	 * stats boxes
	 * 
	 * @return	mixed
	 */
	function statsBoxes()
	{
		// number of students
		$numStudents = $this->pdo->prepare("SELECT COUNT(*) AS numStudents FROM student");
		$numStudents->execute();
		$numStudent = $numStudents->fetchObject();

		// number of teachers
		$numTeachers = $this->pdo->prepare("SELECT COUNT(*) AS numTeachers FROM teachers");
		$numTeachers->execute();
		$numTeacher = $numTeachers->fetchObject();

		// fees payment
		// SELECT SUM(t.total_fee) as expected_amount, s.sch_year, s.sch_term FROM `track_fee_payments` t join sch_terms s on t.school_term = s.id group by t.school_term; 
		$feess = $this->pdo->prepare("SELECT SUM(t.total_fee) as expected_amount, SUM(p.amount) as amount_paid, s.sch_year, s.sch_term FROM track_fee_payments t join sch_terms s on t.school_term = s.id join payments p ON p.ef_id = t.id where s.sch_sts = :stts group by t.school_term");
		$feess->execute(["stts" => yes]);
		$fee = $feess->fetchObject();
		$fees_paid_so_far = 0.0;
		if ($fee) :
			$fees_paid_so_far = (($fee->expected_amount - $fee->amount_paid) / ($fee->expected_amount)) * 100;
		endif;

		//
		return array("numStudent" => $numStudent->numStudents, "numTeacher" => $numTeacher->numTeachers, "fees_paid_so_far" => round($fees_paid_so_far, 1));
	} // end


	/**
	 * number of students per class chart
	 *
	 * @return mixed
	 */
	public function num_of_students_per_class_chart()
	{
		$stmt = $this->pdo->prepare("SELECT COUNT(*) as numstudent, c.class_name as classname FROM student s inner join class_streams c on c.id = s.class_id GROUP by c.class_name");
		$stmt->execute();
		$results = $stmt->fetchAll();
		$data = array();
		foreach ($results as $result) {
			$data[] = $result;
		}
		return json_encode($data);
	} // end



	/**
	 * daily fee payment chart
	 *
	 * @return mixed
	 */
	public function daily_fee_payment_chart()
	{
		$stmt = $this->pdo->prepare("SELECT sum(amount) as amt, payment_date as pdate FROM payments GROUP by payment_date order by payment_date asc");
		$stmt->execute();
		$results = $stmt->fetchAll();
		$data = array();
		foreach ($results as $result) {
			$data[] = array("amt" => $result['amt'], "pdate" => (new DateTime($result['pdate']))->format('d M'));
		}
		return json_encode($data);
	} // end




	// -------------------------- -------------------------------------------------------------------
	//		school terms
	// -------------------------- -------------------------------------------------------------------


	/**
	 * set school term
	 *
	 * @return mixed
	 */
	function set_school_term()
	{

		try {
			extract($_POST);

			if (empty($sch_term)) {
				$response = $this->json_response(422, "Please select school term!");
			} elseif (!is_numeric($sch_term)) {
				$response = $this->json_response(422, "Please enter valid school term!");
			} elseif ($sch_term < 0 || $sch_term === "0") {
				$response = $this->json_response(422, "School term cannot be less than 1!");
			} elseif (empty($sch_year)) {
				$response = $this->json_response(422, "Please select school year!");
			} else {

				// limit school term year to only 3; 1 year = 3 terms
				$check_year = $this->db->prepare("SELECT * from sch_terms where sch_year = ?");
				$check_year->bind_param("s", $sch_year);
				$check_year->execute();
				$check_year->store_result();

				if ($check_year->num_rows >= 3) {
					$response = $this->json_response(422, "A year can only have 3 terms!");
				} else {

					$dt = $this->genDateTime();

					$schts = !empty($sch_sts) ? $sch_sts : no;

					// school term is being made active
					if ($schts == yes) {

						$this->pdo->prepare("UPDATE sch_terms SET sch_sts = ?, updated_at = ? WHERE sch_sts = ?")->execute([
							no, $dt, yes
						]);
					}
					// check if database terms if empty
					$check = $this->db->query("SELECT * from sch_terms")->num_rows;

					// reset all school term requirements in student table
					$resetstudreq = $this->pdo->prepare("UPDATE student SET requirement_id = :reqid");
					$resetstudreq->execute(["reqid" => null]);

					// save
					$save = $this->pdo->prepare("INSERT into sch_terms(sch_term, sch_year, sch_sts, created_at, updated_at) values (:sch_term, :sch_year, :sch_sts, :created_at, :updated_at)")->execute([
						"sch_term" => $sch_term,
						"sch_year" => $sch_year,
						"sch_sts" => $check < 0 ? yes : $schts,
						"created_at" => $dt,
						"updated_at" => $dt
					]);

					if ($save) {
						$response = $this->json_response(200, "School term details saved!", true);
					} else
						$response = $this->json_response(422, "Error occured while saving school term. Please try again!", false);
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}
		//
		return $response;
	} // end


	/**
	 * fetch school term
	 *
	 * @return mixed
	 */
	function fetch_school_terms()
	{
		$stmt = $this->pdo->prepare("SELECT * FROM sch_terms order by sch_year asc, sch_term asc ");
		$stmt->execute();
		return $stmt->fetchAll();
	} // end


	/**
	 * school term edit
	 *
	 * @param  int $id
	 * @return mixed
	 */
	function school_term_edit($id)
	{
		return $this->db->query("SELECT * FROM sch_terms where id = {$id} ");
	} // end


	/**
	 * school term datatable
	 *
	 * @return mixed
	 */
	function school_term_datatable()
	{
		$columns = array(
			0 => 'id',
			1 => 'sch_term',
			2 => 'sch_year',
			3 => 'sch_sts',
		);

		$smtp = "SELECT *";
		$smtp .= " from sch_terms";
		$query = mysqli_query($this->db, $smtp) or die("Unknown Error Occured!");

		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;

		$smtp = "SELECT *";
		$smtp .= " from sch_terms WHERE 1=1";

		if (!empty($_REQUEST['search']['value'])) {
			$smtp .= " AND ( sch_term LIKE '" . $_REQUEST['search']['value'] . "%' ";
			$smtp .= " OR sch_year LIKE '" . $_REQUEST['search']['value'] . "%' ";
			$smtp .= " OR sch_sts LIKE '" . $_REQUEST['search']['value'] . "%' )";
		}

		$query = mysqli_query($this->db, $smtp) or die("Unknown Error Occured!");

		$totalFiltered = mysqli_num_rows($query);

		$smtp .= " ORDER BY " . $columns[$_REQUEST['order'][0]['column']] . "   " . $_REQUEST['order'][0]['dir'] . "  LIMIT " . $_REQUEST['start'] . " ," . $_REQUEST['length'] . "   ";

		$query = mysqli_query($this->db, $smtp) or die("Unknown Error Occured!");

		$data = array();

		while ($row = mysqli_fetch_array($query)) {  // preparing an array

			$nestedData	=	array();

			//
			$id    	 = $row["id"];
			$term  	 = $row["sch_term"];
			$year 	 = $row["sch_year"];
			$sts 	 = $row["sch_sts"];
			$created = $row["created_at"];

			// action buttons
			$btntext = $sts == yes ? '<i class="bi-toggle-off" role="img" aria-label="Deactivate"></i>' : '<i class="bi-toggle-on" role="img" aria-label="Activate"></i>';
			$togglestatus = '<button type="button" class="btn btn-info toggle_school_term_status" data-id="' . $id . '" data-sts="' . $sts . '">' . $btntext . '</button>';
			$delete = '<button type="button" class="btn btn-danger delete_school_term" data-id="' . $id . '" ><i class="fas fa-times-circle" role="img" aria-label="Delete"></i></button>';

			//
			// pass database data to array
			// 
			$nestedData[] = $id;
			$nestedData[] = $term;
			$nestedData[] = $year;
			$nestedData[] = ucfirst($sts);
			// $nestedData[] = '<div class="btn-group btn-group-sm" role="group">' . $delete . '</div>';

			$data[] = $nestedData;
		}

		$json1 = array(
			"draw"            => intval($_REQUEST['draw']),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $data
		);

		echo json_encode($json1);
	} // end


	/**
	 * school term breadcrumb
	 *
	 * @return mixed
	 */
	function school_term_breadcrumb()
	{
		$stmt = $this->pdo->prepare("SELECT * from sch_terms where sch_sts = ?");
		$stmt->execute([yes]);
		$result = $stmt->fetchObject();
		if ($result) {
			return array("id" => $result->id, "term" => $result->sch_term, "year" => $result->sch_year);
		} else
			return array("term" => na, "year" => date('Y'));
	} // end



	/**
	 * delete school term
	 * 
	 * @return mixed
	 */
	function delete_school_term()
	{

		try {

			extract($_POST);
			$requirements = $this->pdo->prepare("SELECT * FROM track_requirements where school_term = :term");
			$requirements->execute(["term" => $id]);
			$requirement = $requirements->fetchAll();
			//
			$fees = $this->pdo->prepare("SELECT * FROM track_fee_payments where term_paid_for = :term");
			$fees->execute(["term" => $id]);
			$fee = $fees->fetchAll();

			if ($requirement || $fee) {
				return $this->array_response("School term cannot be deleted since its already in use!", false);
			} else {

				$isactive = $this->db->query("SELECT sch_sts FROM sch_terms WHERE id = '" . $id . "' AND sch_sts = '" . yes . "'")->num_rows;
				if ($isactive > 0) {
					return $this->array_response("School term is currently active!", false);
				} else {

					$stmt = $this->pdo->prepare("DELETE FROM sch_terms WHERE id = ?");
					$stmt->execute([$id]);
					return $this->array_response("School term deleted!", true);
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			return $this->array_response("Failed to delete information.", false);
		}
	} // end



	/**
	 * toggle school term status
	 *
	 * @return mixed
	 */
	function toggle_school_term_status()
	{

		$response = "";

		try {

			extract($_POST);

			//
			$newsts = $sts == yes ? no : yes;
			$dt = $this->genDateTime();

			//
			$stmt = $this->pdo->prepare("SELECT * FROM sch_terms WHERE sch_sts = '" . yes . "'")->num_rows;
			if ($stmt > 0) {
				$this->pdo->prepare("UPDATE sch_terms SET sch_sts = ?, updated_at = ? WHERE sch_sts = ?")->execute([no, $dt, yes]);
			}

			//
			$update = $this->pdo->prepare("UPDATE sch_terms set sch_sts = ?, updated_at = ? where id = ?");
			$check = $update->execute([$newsts, $dt, $id]);

			if ($check) $response = $this->json_response(200, "School term status updated!", true);
			else $response = $this->json_response(422, "Failed to update school term status. Please try again!");
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}
		//
		return $response;
	} // end



	/**
	 * save school terms
	 * 
	 * @return mixed
	 */
	function save_school_term()
	{
		//
		$term = trim($_POST['term']);
		$year = trim($_POST['year']);
		$sts  = trim($_POST['sts']);
		$id  = trim($_POST['id']);

		//
		$bool = false;
		$msg = "";

		//
		if (empty($term)) {
			$msg = "Please select school term!";
			$bool = false;
		} elseif (empty($year)) {
			$msg = "Please select school year!";
			$bool = false;
		} else {

			$dt = $this->genDateTime();

			if (empty($id)) {

				$stmt = $this->pdo->prepare("INSERT INTO sch_terms (sch_term, sch_year, sch_sts, created_at, updated_at) values (:sch_term, :sch_year, :sch_sts, :created_at, :updated_at)");
				$stmt->execute([
					'sch_term' => $term,
					'sch_year' => $year,
					'sch_sts' => $sts,
					'created_at' => $dt,
					'updated_at' => $dt
				]);
				$msg = "School term added!";
			} else {

				$this->pdo->prepare("UPDATE sch_terms SET sch_term = ?, sch_year = ?, sch_sts = ?, updated_at = ? WHERE id = ?")->execute([
					$term, $year, $sts, $dt, $id
				]);

				$msg = "School term updated!";
			}
			$bool = true;
		}

		//
		$response = array("msg" => $msg, "bool" => $bool);
		return json_encode($response);
	} // end


	/**
	 * select school term
	 * @return mixed
	 */
	function sel_school_term()
	{
		$qry = $this->pdo->query("SELECT id, sch_term, sch_year FROM sch_terms");
		$qry->execute();
		return $qry->fetchAll();
	} // end



	// -------------------------- -------------------------------------------------------------------
	//		teachers
	// -------------------------- -------------------------------------------------------------------


	/**
	 * fetch teachers
	 *
	 * @return mixed
	 */
	function fetch_teachers()
	{
		$data = array();
		$stmt = $this->pdo->prepare("SELECT * FROM teachers order by teacher_names asc");
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$data[] = $row;
		}
		return $data;
	} // end


	/**
	 * fetch teachers list
	 * 
	 * @return void
	 */
	function teachersList()
	{
		$stmt = $this->db->query("SELECT * FROM teachers order by teacher_names asc");

		//
		$isbursar = $_SESSION['login_access_level'] == LV_2 ? 'disabled' : '';
		//
		foreach ($this->fetch_teachers() as $key => $row) {
			?>
			<tr>
				<td>
					<p><img src="<?php echo $row['photo'] ?>" alt="" class="img-fluid" style="height: 50px"></p>
				</td>
				<td>
					<p> <b><?php echo $row['teacher_names'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo ucwords($row['teacher_location_address']) ?></b></p>
				</td>
				<td>
					<p> <b><?php echo ucwords($row['teacher_salary']) ?></b></p>
				</td>
				<td class="">
					<p class="small">Mob: <i><b><?php echo $row['teacher_tel'] ?></i></p>
					<p class="small">DOB: <i><b><?php echo $row['teacher_dob'] ?></i></p>
					<p class="small">Gender: <i><b><?php echo $row['teacher_sex'] ?></i></p>
					<p class="small">Email: <i><b><?php echo $row['teacher_email'] ?></i></p>
					<p class="small">Education: <i><b><?php echo $row['teacher_education'] ?></i></p>
				</td>
				<td class="text-center">

					<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">
						<button class="btn btn-outline-primary edit_teacher" type="button" data-id="<?php echo $row['id'] ?>" <?php echo $isbursar; ?>>Edit</button>
						<button class="btn btn-outline-danger delete_teacher" type="button" data-id="<?php echo $row['id'] ?>" <?php echo $isbursar; ?>>Delete</button>
					</div>
				</td>
			</tr>
		<?php }
	} // end



	/**
	 * get selected teacher information
	 * 
	 * @param  int	$id	
	 * @return mixed
	 */
	function getTeacher($id)
	{
		return $this->db->query("SELECT * FROM teachers where id =" . $id);
	} // end



	/**
	 * add new teacher or update existing information
	 *
	 * @return mixed
	 */
	function save_teacher()
	{
		$response = array();

		try {
			extract($_POST);

			// validation
			if (empty($name)) {
				$response = array("bool" => false, "msg" => "Please enter teacher names!");
			} elseif (empty($id) && empty($_FILES['teacher_photo']['name'])) {
				$response = array("bool" => false, "msg" => "Please select teacher photo!");
			} elseif (empty($genders)) {
				$response = array("bool" => false, "msg" => "Please select teacher gender!");
			} elseif (empty($dob)) {
				$response = array("bool" => false, "msg" => "Please select teacher date of birth!");
			} elseif (empty($email)) {
				$response = array("bool" => false, "msg" => "Please enter teacher email!");
			} elseif (empty($telcont)) {
				$response = array("bool" => false, "msg" => "Please enter tel contact of teacher!");
			} elseif (empty($education)) {
				$response = array("bool" => false, "msg" => "Please enter minimum education level of teacher!");
			} elseif (empty($location)) {
				$response = array("bool" => false, "msg" => "Please enter location address / street!");
			} elseif (empty($salary)) {
				$response = array("bool" => false, "msg" => "Please enter teacher salary!");
			} else {


				$data = "";
				foreach ($_POST as $k => $v) {
					if (!in_array($k, array('id')) && !is_numeric($k)) {
						if (empty($data)) {
							$data .= " $k='$v' ";
						} else {
							$data .= ", $k='$v' ";
						}
					}
				}

				//
				$dt = $this->genDateTime();

				//
				if (empty($id)) {

					// upload
					$target_file = TEACHERS_PHOTO_DIR . basename($_FILES["teacher_photo"]["name"]);
					$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
					$check = getimagesize($_FILES["teacher_photo"]["tmp_name"]);
					if ($check !== false) {

						if ($_FILES["teacher_photo"]["size"] < 1024000) {


							if (!in_array($imageFileType, array("jpg", "png", "jpeg"))) {
								$response = array("bool" => false, "msg" => "Only JPG, JPEG, PNG files are allowed.");
							} else {

								//
								$ext = strtolower(pathinfo($_FILES["teacher_photo"]["name"], PATHINFO_EXTENSION));

								// last id
								$qrylastid = $this->pdo->prepare("SELECT id FROM teachers ORDER BY id DESC LIMIT 1");
								$qrylastid->execute();
								$lastidobj = $qrylastid->fetchObject();
								$lastid = !$lastidobj ? 1 : $lastidobj->id + 1;
								// STUD_PHOTO_DIR
								$uploaddir = TEACHERS_PHOTO_DIR . $lastid;
								if (!is_dir($uploaddir)) {
									mkdir($uploaddir, 0777);
								}
								$newName = $uploaddir . '/' . $lastid . '.' . $ext;

								// upload file
								move_uploaded_file($_FILES["teacher_photo"]["tmp_name"], $newName);

								// new teachers
								$save = $this->pdo->prepare("INSERT INTO teachers (photo, teacher_names, teacher_dob, teacher_sex, teacher_tel, teacher_email, teacher_education, teacher_location_address, teacher_salary, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
								$save->execute([$newName, $name, $dob, $genders, $telcont, $email, $education, $location, $salary, $dt, $dt]);

								//
								$response = array("bool" => true, "msg" => "New teacher added!");
							}
						} else {
							$response = array("bool" => false, "msg" => "Photo must not exceed 1mb!");
						}
					} else {
						$response = array("bool" => false, "msg" => "Photo selected is not an image!");
					}
				} else {

					//
					$this->pdo->prepare("UPDATE teachers set teacher_names = ?, teacher_dob = ?, teacher_sex = ?, teacher_tel = ?, teacher_email = ?, teacher_education = ?, teacher_location_address = ?, teacher_salary = ?, updated_at = ? where id = ?")->execute([$name, $dob, $genders, $telcont, $email, $education, $location, $salary, $dt, $id]);
					//
					$response = array("bool" => true, "msg" => "Teacher information updated!");
				}
			}
		} catch (Exception $e) {
			//
			$this->logToFile($e->getMessage());

			//
			$response = array("bool" => false, "msg" => "Internal server error. Please try again later:");
		}
		//
		return json_encode($response);
	} // end


	/**
	 * delete teacher
	 *
	 * @return void
	 */
	function delete_teacher()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM teachers where id = " . $id);
		if ($delete)
			return 1;
	} // end




	// -------------------------- -------------------------------------------------------------------
	//		parents
	// -------------------------- -------------------------------------------------------------------



	/**
	 * add new parent or update existing information
	 *
	 * @return mixed
	 */
	function save_parent()
	{
		$response = array();

		try {

			//
			$name 		= trim($_POST['name']);
			$genders 	= trim($_POST['genders']);
			$email 		= trim($_POST['email']);
			$telcont 	= trim($_POST['telcont']);
			$residence 	= trim($_POST['residence']);
			$id 		= trim($_POST['id']);

			$students 	= $_POST['students']; // array

			// validation
			if (empty($name)) {
				$response = array("bool" => false, "msg" => "Please enter parent names!");
			} elseif (empty($genders)) {
				$response = array("bool" => false, "msg" => "Please select parent gender!");
			} elseif (empty($email)) {
				$response = array("bool" => false, "msg" => "Please enter parent email!");
			} elseif (empty($telcont)) {
				$response = array("bool" => false, "msg" => "Please enter tel contact of parent!");
			} elseif (empty($email)) {
				$response = array("bool" => false, "msg" => "Please enter parent email!");
			} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$response = array("bool" => false, "msg" => "Please enter valid email address!");
			} elseif (empty($residence)) {
				$response = array("bool" => false, "msg" => "Please enter location address / street!");
			} else {

				//
				$dt = $this->genDateTime();
				//
				$strstudent = implode(", ", $students);

				//
				if (empty($id)) {

					// new teachers
					$save = $this->pdo->prepare("INSERT INTO parents (student_id, parent_names, contacts, email, residence, gender, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?)");
					$save->execute([$strstudent, $name, $telcont, $email, $residence, $genders, $dt, $dt]);

					//
					$response = array("bool" => true, "msg" => "New parent added!");
				} else {

					//
					$this->pdo->prepare("UPDATE parents set student_id = ?, parent_names = ?, contacts = ?, email = ?, residence = ?, gender = ?, updated_at = ? where id = ?")->execute([$strstudent, $name, $telcont, $email, $residence, $genders, $dt, $id]);
					//
					$response = array("bool" => true, "msg" => "Parent information updated!");
				}
			}
		} catch (Exception $e) {
			//
			$this->logToFile($e->getMessage());
			//
			$response = array("bool" => false, "msg" => "Internal server error. Please try again later:");
		}
		//
		return json_encode($response);
	} // end


	/**
	 * delete parent
	 *
	 * @return void
	 */
	function delete_parent()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM parents where id = " . $id);
		if ($delete)
			return 1;
	} // end



	/**
	 * get selected parent information
	 * 
	 * @param  int	$id	
	 * @return mixed
	 */
	function getParent($id)
	{
		return $this->db->query("SELECT * FROM parents where id =" . $id);
	} // end


	/**
	 * get students for parent being added / editing
	 */
	function fetchStudentsforParents()
	{
		return $this->db->query("SELECT id, name,id_no from student order by name asc");
	} // emd


	/**
	 * fetch parents list
	 * 
	 * @return void
	 */
	function parentsList()
	{
		$stmt = $this->db->query("SELECT 
			p.id as parentid, 
			p.parent_names, 
			p.contacts,
			p.email,
			p.residence,
			p.gender,
			p.student_id		
			 FROM parents p ORDER BY p.parent_names ASC");

		$getstudentsql = "SELECT id, name from student where id = ?";

		while ($row = $stmt->fetch_assoc()) :
			$studentids = explode(", ", $row['student_id']);
			$stud_names = "";
			for ($i = 0; $i < count($studentids); $i++) {
				$ids = $studentids[$i];
				$getstudent = $this->pdo->prepare($getstudentsql);
				$getstudent->execute([$ids]);
				$names = $getstudent->fetchObject();
				$url = "student-details?" . http_build_query(["id" => $ids]);
				$stud_names .= '<a href="' . $url . '">' . $names->name . '</a>' . ", ";
			}
		?>
			<tr>
				<td>
					<p> <b><?php echo $row['parent_names'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo $row['contacts'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo $row['email'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo $row['gender'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo $row['residence'] ?></b></p>
				</td>
				<td>
					<p class="small"><b><?php echo rtrim($stud_names, ", ") ?></b></p>
				</td>
				<td class="text-center">
					<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">
						<button class="btn btn-outline-primary edit_parent" type="button" data-id="<?php echo $row['parentid'] ?>">Edit</button>
						<button class="btn btn-outline-danger delete_parent" type="button" data-id="<?php echo $row['parentid'] ?>">Delete</button>
					</div>
				</td>
			</tr>
		<?php endwhile;
	} // end





	// -------------------------- -------------------------------------------------------------------
	//		classes, streams
	// -------------------------- -------------------------------------------------------------------



	/**
	 * save / edit new class, stream
	 *
	 * @return mixed
	 */
	function save_class()
	{

		try {
			extract($_POST);

			//
			$classnameid = $_POST['classnameid'];
			$classinfoid = $_POST['classinfoid'];
			$name 		 = $_POST['class_name'];
			$teacher 	 = $_POST['class_teacher'];
			$fees 		 = $_POST['class_fees'];
			$description = trim($_POST['class_description']);

			//
			$response = "";

			//
			if (empty($name)) {
				$response = $this->json_response(422, "Please enter class name!");
			} elseif (empty($teacher)) {
				$response = $this->json_response(422, "Please select teacher!");
			} elseif (empty($fees)) {
				$response = $this->json_response(422, "Please select fees!");
			} elseif (!empty($description) && strlen($description) > 120) {
				$response = $this->json_response(422, "Description cannot exceed 120 characters!");
			} else {

				$check = $this->db->query("SELECT * FROM class_streams where class_name ='" . $name . "' " . (!empty($classnameid) ? " and id != {$classnameid} " : ''))->num_rows;
				if ($check > 0) {
					$response = $this->json_response(422, "Class name entered already exists!");
				} else {

					//
					$dt = $this->genDateTime();
					//
					$description = !empty($description) ? $description : null;

					if (empty($classnameid) && empty($classinfoid)) {

						// save class name
						$save = $this->pdo->prepare("INSERT INTO class_streams (class_name, created_at, updated_at) values (?,?,?)");
						$clname = $save->execute([$name, $dt, $dt]);
						if ($clname) {
							$id = $this->pdo->lastInsertId();

							// save class details
							$this->pdo->prepare("INSERT INTO class_details (class_id, teacher_id, fees_id, class_details, created_at, updated_at) values(?,?,?,?,?,?)")->execute([$id, $teacher, $fees, $description, $dt, $dt]);
							//
							$response = $this->json_response(200, "New class added!", true);
						}
					} else {
						$uclassname = $this->pdo->prepare("UPDATE class_streams set class_name = ?, updated_at = ? where id = ?")->execute([$name, $dt, $classnameid]);

						$uclassinfo = $this->pdo->prepare("UPDATE class_details set teacher_id = ?, fees_id = ?, class_details = ?, updated_at = ? where class_id = ? and id = ?")->execute([$teacher, $fees, $description, $dt, $classnameid, $classinfoid]);

						if ($uclassname && $uclassinfo) {
							//
							$response = $this->json_response(200, "Class updated!", true);
						} else {
							$response = $this->json_response(422, "Error updating class details. Please try again!");
						}
					}
				} // unique class name

			} // validation

		} catch (Exception $e) {
			//
			$this->logToFile($e->getMessage());
			//
			$this->error_logs($e);
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}

		return $response;
	} // end


	/**
	 * delete class
	 *
	 * @return mixed
	 */
	function delete_class()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM courses where id = " . $id);
		$delete2 = $this->db->query("DELETE FROM fees where course_id = " . $id);
		if ($delete && $delete2) {
			return 1;
		}
	}


	/**
	 * fetch selected class details
	 *
	 * @param  int $classnameid
	 * @param  int $classinfoid
	 * @return mixed
	 */
	function fetch_sel_class_info($classnameid, $classinfoid)
	{

		$stmt = $this->pdo->prepare("SELECT a.id as classnameid, a.class_name, b.id as classinfoid, b.teacher_id, b.fees_id, b.class_details FROM class_streams a join class_details b on a.id = b.class_id where a.id = ? and b.id = ?");
		$stmt->execute([$classnameid, $classinfoid]);
		$results = $stmt->fetchObject();
		return $results;
	} // end


	/**
	 * classes
	 * 
	 * @return void
	 */
	function fetchClasses()
	{
		$i = 1;
		$stmt = $this->pdo->prepare("SELECT a.id as classnameid, a.class_name, b.id as classinfoid, b.teacher_id, b.fees_id, b.class_details FROM class_streams a join class_details b on a.id = b.class_id order by a.class_name asc");
		$stmt->execute();
		$results = $stmt->fetchAll();

		// get teachers
		$sql_t = $this->pdo->prepare("SELECT teacher_names from teachers where id = ?");
		// get fees
		$sql_f = $this->pdo->prepare("SELECT school_section, amount from fees where id = ?");

		//
		$isbursar = $_SESSION['login_access_level'] == LV_2 ? 'disabled' : '';

		//
		foreach ($results as $row) {

			$teacherid = $row['teacher_id'];
			$feesid = $row['fees_id'];

			$sql_t->execute([$teacherid]);
			$teachers = $sql_t->fetchObject();

			//
			$sql_f->execute([$feesid]);
			$fees = $sql_f->fetchObject();
		?>
			<tr>
				<td class="text-center"><?php echo $i++ ?></td>
				<td>
					<p> <b><?php echo $row['class_name'] ?></b></p>
				</td>
				<td class="">
					<p><?php echo $teachers->teacher_names ?></p>
				</td>
				<td class="">
					<p><small><i><b><?php echo is_null($row['class_details']) ? na : $row['class_details']; ?></i></small></p>
				</td>
				<td class="text-right">
					<p><b><?php echo $fees->school_section . ' | ' . number_format($fees->amount, 2) ?></b></p>
				</td>
				<td class="text-center">
					<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">
						<button class="btn btn-outline-primary edit_class" type="button" data-classinfo="<?php echo $row['classinfoid'] ?>" data-id="<?php echo $row['classnameid'] ?>" <?php echo $isbursar; ?>>Edit</button>
						<button class="btn btn-outline-danger delete_class" type="button" data-classinfo="<?php echo $row['classinfoid'] ?>" data-id="<?php echo $row['classnameid'] ?>" <?php echo $isbursar; ?>>Delete</button>
					</div>
				</td>
			</tr>
		<?php }
	} // end






	// -------------------------- -------------------------------------------------------------------
	//		requirements, track requirments
	// -------------------------- -------------------------------------------------------------------


	/**
	 * fetch all requirments
	 *
	 * @return mixed
	 */
	function fetch_all_requirments()
	{

		try {
			$stmt = $this->pdo->prepare("SELECT * from requirements");
			$stmt->execute();
			$results = $stmt->fetchAll();

			if ($results) {

				//
				$view = "<div class='table-responsive'>";
				$view .= '<table class="table table-hover table-stripped" id="req_dt">';
				$view .= '<thead class="bg-secondary text-light">';
				$view .= '<tr>';
				$view .= '<th scope="col">#</th>';
				$view .= '<th scope="col">Name</th>';
				$view .= '<th scope="col">Description</th>';
				$view .= '<th scope="col">Action</th>';
				$view .= '</tr>';
				$view .= "</thead>";
				$view .= "</body>";
				$cnt = 1;
				foreach ($results as $result) {

					$view .= '<tr>';
					$view .= '<th scope="row">' . $cnt . '</th>';
					$view .= '<td>' . $result['item_name'] . '</td>';
					$view .= '<td>' . $result['item_description'] . '</td>';
					$view .= '<td><button type="button" class="btn btn-sm btn-danger delete_requirement" data-id="' . $result['id'] . '"><i class="bi-x-circle" role="img" aria-label="delete"></i> Delete</button></td>';
					$view .= '</tr>';
					$cnt++;
				}

				$view .= "</body>";
				$view .= "</table>";
				$view .= "</div>";
				//
				return $this->json_response(200, $view, true);
			} else {
				$view = '<div class="alert alert-info fade show d-flex align-items-center justify-content-center" role="alert"><i class="bi-info-circle-fill flex-shrink-0 mr-2" width="24" height="24" role="img" aria-label="Danger:"></i><div>No requirements added yet!</div></div>';
				//
				return $this->json_response(200, $view, true);
			}
		} catch (Exception $e) {
			$view = '<div class="alert alert-dange fade show d-flex align-items-center justify-content-center" role="alert"><i class="bi-exclamation-triangle-fill flex-shrink-0 mr-2" width="24" height="24" role="img" aria-label="Danger:"></i><div>Internal server. Failed to load requirements!</div></div>';
			//
			$this->logToFile($e->getMessage());
			//
			return $this->json_response(500, $view, false);
		}
	} // end


	/**
	 * fetch selected requirement
	 *
	 * @param  int $id
	 * @return mixed
	 */
	function fetch_sel_requirement($id)
	{
		$stmt = $this->pdo->prepare("SELECT * from requirements where id = :id")->execute(["id" => $id]);
		return $stmt->fetchObject();
	} // end


	/**
	 * delete requirement
	 *
	 * @return mixed
	 */
	function delete_requirement()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM requirements where id = " . $id);
		if ($delete)
			return 1;
	} // end


	/**
	 * save / update requirements
	 *
	 * @return mixed
	 */
	function save_requirements()
	{
		$response = "";

		try {
			extract($_POST);

			if (empty($req_names)) {
				$response = $this->json_response(422, "<div class='d-flex align-items-center justify-content-center text-danger mb-3'><i class='bi-exclamation-triangle-fill mr-1' width='24' height='24' role='img' aria-label='Danger:'></i><div>Please enter requirement name!</div></div>");
			} elseif (empty($req_description)) {
				$response = $this->json_response(422, "<div class='d-flex align-items-center justify-content-center text-danger mb-3'><i class='bi-exclamation-triangle-fill mr-1' width='24' height='24' role='img' aria-label='Danger:'></i><div>Please enter requirement description!</div></div>");
			} else {

				//
				$dt = $this->genDateTime();

				if (empty($id)) {

					// check if exists
					$stmt = $this->pdo->prepare("SELECT * from requirements where item_name = :item_name");
					$stmt->execute(["item_name" => $req_names]);
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($result) {
						$response = $this->json_response(422, "<div class='d-flex align-items-center justify-content-center text-danger mb-3'><i class='bi-exclamation-triangle-fill mr-1' width='24' height='24' role='img' aria-label='Danger:'></i><div>Requirement name entered already exists!</div></div>");
					} else {

						//
						$stmt = $this->pdo->prepare("INSERT into requirements(item_name, item_description, created_at, updated_at) values(?, ?, ?, ?)")->execute([$req_names, $req_description, $dt, $dt]);
						//
						if ($stmt) {
							$response = $this->json_response(200, "<div class='d-flex align-items-center justify-content-center text-success mb-3'><i class='bi-check-circle mr-1' width='24' height='24' role='img' aria-label='Ok:'></i><div>New requirement added!</div></div>", true);
						} else {
							$response = $this->json_response(422, "<div class='d-flex align-items-center justify-content-center text-danger mb-3'><i class='bi-exclamation-triangle-fill mr-1' width='24' height='24' role='img' aria-label='Danger:'></i><div>Error adding new requirement. Please try again!</div></div>");
						}
					}
				} else {

					//
					$stmt = $this->pdo->prepare("UPDATE requirements set item_name = ?, item_description = ?, updated_at = ? where id = ?")->execute([$req_names, $req_description, $dt, $id]);
					//
					if ($stmt) $response = $this->json_response(200, "<div class='d-flex align-items-center justify-content-center text-success mb-3'><i class='bi-check-circle mr-1' width='24' height='24' role='img' aria-label='Ok:'></i><div>Requirement updated!</div></div>", true);
					else $response = $this->json_response(422, "<div class='d-flex align-items-center justify-content-center text-danger mb-3'><i class='bi-exclamation-triangle-fill mr-1' width='24' height='24' role='img' aria-label='Danger:'></i><div>Error updating requirement. Please try again!</div></div>");
				}
			}
		} catch (Exception $e) {
			//
			$this->logToFile($e->getMessage());
			//
			$this->error_logs($e->getMessage());
			//
			$response = $this->json_response(500, "<div class='d-flex align-items-center justify-content-center text-danger mb-3'><i class='bi-exclamation-triangle-fill mr-1' width='24' height='24' role='img' aria-label='Danger:'></i><div>Internal server. Please try again later!</div></div>");
		}
		//
		return $response;
	} // end


	// -------------------------- -------------------------------------------------------------------	
	// -------------------------- -------------------------------------------------------------------

	/**
	 * load student requirements
	 *
	 * @return mixed
	 */
	function load_student_requirements()
	{

		extract($_POST);

		$view = "";

		// get school term
		$schterm = $this->pdo->prepare("SELECT id from sch_terms where sch_sts = :sch_sts");
		$schterm->execute(["sch_sts" => yes]);
		$term = $schterm->fetchObject();

		//
		$studentqry = $this->pdo->prepare("SELECT class_id, requirement_id from student where id = :id");
		$studentqry->execute(["id" => $studentid]);
		$student = $studentqry->fetchObject();

		//
		$check_for_requiremens = $this->pdo->prepare("SELECT requirement_id FROM track_requirements WHERE student_id = ? and id = ?");
		$check_for_requiremens->execute([$studentid, $student->requirement_id]);
		$check_for_requiremen = $check_for_requiremens->fetchObject();

		if (!$check_for_requiremen) {
			$query = $this->pdo->prepare("SELECT id, item_name, item_description from requirements order by item_name");
			$query->execute();
			$requirements = $query->fetchAll();
			$view = '<div class="row">';
			foreach ($requirements as $requirement) {
				$view .= '<div class="col-md-6 mb-2">';
				$view .= '<div class="custom-control custom-checkbox">';
				$view .= '<input type="checkbox" class="custom-control-input" id="sel_req_' . $requirement['id'] . '" name="sel_stud_requirment[]" value="' . $requirement['id'] . '">';
				$view .= '<label class="custom-control-label" for="sel_req_' . $requirement['id'] . '">' . $requirement['item_name'] . '</label>';
				$view .= '</div>';
				$view .= '</div>';
			}
			$view .= '</div>';
		} else {
			$query = $this->pdo->prepare("SELECT id, item_name, item_description from requirements order by item_name");
			$query->execute();
			$requirements = $query->fetchAll();

			//
			$track_requirement = $this->pdo->prepare("SELECT requirement_id FROM track_requirements WHERE class_id = :classid and student_id = :studid and id = :id");

			$view = '<div class="row">';

			foreach ($requirements as $requirement) {

				//
				$reqid = $requirement['id'];
				//
				$track_requirement->execute(["classid" => $student->class_id, "studid" => $studentid, "id" => $student->requirement_id]);
				$savereqs = $track_requirement->fetchObject();
				$reqsofararray = explode(", ", $savereqs->requirement_id);
				//
				$isselected = in_array($reqid, $reqsofararray) ? 'checked' : '';

				//
				$view .= '<div class="col-md-6 mb-2">';
				$view .= '<div class="custom-control custom-checkbox">';
				$view .= '<input type="checkbox" class="custom-control-input" id="sel_req_' . $requirement['id'] . '" name="sel_stud_requirment[]" value="' . $requirement['id'] . '" ' . $isselected . '>';
				$view .= '<label class="custom-control-label" for="sel_req_' . $requirement['id'] . '">' . $requirement['item_name'] . '</label>';
				$view .= '</div>';
				$view .= '</div>';
			}
			$view .= '</div>';
		}
		return $view;
	} // end


	/**
	 * load requirements based on sel class
	 *
	 * @return void
	 */
	function load_requirements_based_on_sel_class()
	{
		$query = $this->pdo->prepare("SELECT id, item_name, item_description from requirements order by item_name");
		$query->execute();
		$requirements = $query->fetchAll();
		$view = '<div class="row">';
		foreach ($requirements as $requirement) {
			$view .= '<div class="col-md-6 mb-2">';
			$view .= '<div class="custom-control custom-checkbox">';
			$view .= '<input type="checkbox" class="custom-control-input" id="sel_req_' . $requirement['id'] . '" name="sel_stud_requirment[]" value="' . $requirement['id'] . '">';
			$view .= '<label class="custom-control-label" for="sel_req_' . $requirement['id'] . '">' . $requirement['item_name'] . '</label>';
			$view .= '</div>';
			$view .= '</div>';
		}
		$view .= '</div>';
		echo $view;
	} // end


	/**
	 * load students based on sel class
	 *
	 * @return void
	 */
	function load_students_based_on_sel_class()
	{
		extract($_POST);

		$stmt = $this->pdo->prepare("SELECT id, name from student where class_id = :class_id");
		$stmt->execute(["class_id" => $class_id]);
		$students = $stmt->fetchAll();
		if ($students) {
			$view = '<div class="form-group">';
			$view .= '<label for="stud_req_sel_student">Select student</label>';
			$view .= '<select name="stud_req_sel_student" id="stud_req_sel_student" class="form-control" style="width: 100%;" data-placeholder="--- select ---">';
			$view .= '<option></option>';
			foreach ($students as $student) {
				$view .= '<option value="' . $student["id"] . '">' . $student["name"] . '</option>';
			}
			$view .= '</select>';
			$view .= '</div>';

			$view .= '<div class="form-group">';
			$view .= '<button type="submit" class="btn btn-primary" id="stud_req_submit_btn">Submit</button>';
			$view .= '</div>';
		?>
			<script>
				// select2
				$('#stud_req_sel_student').select2({
					placeholder: "--- select ---",
					width: "100%"
				}).on("change", function(ev) {
					let studid = $("#stud_req_sel_student option:selected").val()
					document.querySelector("#edit_req_student_id").value = studid

					$.ajax({
						url: "ajax.php?action=load_student_requirements",
						method: "post",
						data: {
							'studentid': studid
						},
						success: function(response) {
							$("#load_student_requirements_cnt").html(response)
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#load_student_requirements_cnt").html(jqXHR)
						}
					});
				});
			</script>
<?php

			echo $view;
		} else {
			echo '<div class="alert alert-info" role="alert"><p class="my-1">No student found in selected class. Please try again</p></div>';
		}
	} // end


	/**
	 * load class student
	 *
	 * @return mixed
	 */
	function load_class_student()
	{
		extract($_POST);

		$studentid = $_POST['studentid'];

		$view = "";
		$classes = $this->getClasses();

		if ($studentid == n_a) {
			$view .= '<div class="form-group">';
			$view .= '<label for="stud_req_sel_class">Select class</label>';
			$view .= '<select name="stud_req_sel_class" id="stud_req_sel_class" class="form-control" required>';
			$view .= '<option selected disabled>--- select ---</option>';
			foreach ($classes as $key => $v) {
				$view .= '<option value="' . $v['id'] . '">' . $v['class_name'] . '</option>';
			}
			$view .= '</select>';
			$view .= '</div>';

			$view .= '<span id="load_students_list_for_sel"></span>';
		} else {

			$studentinfos = $this->pdo->prepare("SELECT a.class_id, b.class_name from student a join class_streams b on a.class_id = b.id where a.id = ?");
			$studentinfos->execute([$studentid]);
			$studentinfo = $studentinfos->fetchObject();

			$classid = $studentinfo->class_id;
			$classname = $studentinfo->class_name;


			$view .= '<div class="form-group">';
			$view .= '<label for="stud_req_sel_class">Select class</label>';
			$view .= '<select name="stud_req_sel_class" id="stud_req_sel_class" class="form-control" required>';
			// $view .= '<option selected disabled>--- select ---</option>';
			foreach ($classes as $key => $v) {

				$isclassselected = $classid == $v['id'] ? "selected" : "";

				$view .= '<option value="' . $v['id'] . '" ' . $isclassselected . '>' . $v['class_name'] . '</option>';
			}
			$view .= '</select>';
			$view .= '</div>';

			// -
			$stmt = $this->pdo->prepare("SELECT id, name from student where class_id = :class_id");
			$stmt->execute(["class_id" => $classid]);
			$students = $stmt->fetchAll();


			$view .= '<span id="load_students_list_for_sel">';
			$view .= '<div class="form-group">';
			$view .= '<label for="stud_req_sel_student">Select student</label>';
			$view .= '<select name="stud_req_sel_student" id="stud_req_sel_student" class="form-control" style="width: 100%;" data-placeholder="--- select ---">';
			$view .= '<option></option>';
			foreach ($students as $student) {

				$isstudentelected = $studentid == $student['id'] ? "selected" : "";

				$view .= '<option value="' . $student["id"] . '" ' . $isstudentelected . '>' . $student["name"] . '</option>';
			}
			$view .= '</select>';
			$view .= '</div>';

			$view .= '<div class="form-group">';
			$view .= '<button type="submit" class="btn btn-primary" id="stud_req_submit_btn">Submit</button>';
			$view .= '</div>';
			$view .= '</span>';
		}

		return $view;
	} // end


	/**
	 * select requirements
	 *
	 * @param  mixed $studentid
	 * @return mixed
	 */
	function select_requirements($studentid)
	{

		$view = "";

		// get school term
		$schterm = $this->pdo->prepare("SELECT id from sch_terms where sch_sts = :sch_sts");
		$schterm->execute(["sch_sts" => yes]);
		$term = $schterm->fetchObject();

		//
		$studentqry = $this->pdo->prepare("SELECT class_id from student where id = :id");
		$studentqry->execute(["id" => $studentid]);
		$student = $studentqry->fetchObject();

		if ($studentid == n_a) {
			$query = $this->pdo->prepare("SELECT id, item_name, item_description from requirements order by item_name");
			$query->execute();
			$requirements = $query->fetchAll();
			$view = '<div class="row">';
			foreach ($requirements as $requirement) {
				$view .= '<div class="col-md-6 mb-2">';
				$view .= '<div class="custom-control custom-checkbox">';
				$view .= '<input type="checkbox" class="custom-control-input" id="sel_req_' . $requirement['id'] . '" name="sel_stud_requirment[]" value="' . $requirement['id'] . '">';
				$view .= '<label class="custom-control-label" for="sel_req_' . $requirement['id'] . '">' . $requirement['item_name'] . '</label>';
				$view .= '</div>';
				$view .= '</div>';
			}
			$view .= '</div>';
		} else {
			$query = $this->pdo->prepare("SELECT id, item_name, item_description from requirements order by item_name");
			$query->execute();
			$requirements = $query->fetchAll();
			//
			$track_requirement = $this->pdo->prepare("SELECT requirement_id FROM track_requirements WHERE class_id = :classid and student_id = :studid");

			$view = '<div class="row">';
			foreach ($requirements as $requirement) {
				//
				$reqid = $requirement['id'];
				//
				$track_requirement->execute(["classid" => $student->class_id, "studid" => $studentid]);
				$savereqs = $track_requirement->fetchObject();
				$reqsofararray = explode(", ", $savereqs->requirement_id);
				//
				$isselected = in_array($reqid, $reqsofararray) ? 'checked' : '';

				//
				$view .= '<div class="col-md-6 mb-2">';
				$view .= '<div class="custom-control custom-checkbox">';
				$view .= '<input type="checkbox" class="custom-control-input" id="sel_req_' . $requirement['id'] . '" name="sel_stud_requirment[]" value="' . $requirement['id'] . '" ' . $isselected . '>';
				$view .= '<label class="custom-control-label" for="sel_req_' . $requirement['id'] . '">' . $requirement['item_name'] . '</label>';
				$view .= '</div>';
				$view .= '</div>';
			}
			$view .= '</div>';
		}

		return $view;
	} // end


	/**
	 * save / edit student requirements
	 *
	 * @return mixed
	 */
	function save_student_requirements()
	{
		$response = "";

		try {
			extract($_POST);

			//
			$editstudid 	= $_POST['edit_req_student_id'];
			$classid    	= $_POST['stud_req_sel_class'];
			$studentid  	= $_POST['stud_req_sel_student'];
			// $requirementid  = $_POST['sel_stud_requirment'];

			//
			if (empty($classid)) {
				$response = $this->json_response(422, "Please select student class!");
			} elseif (empty($studentid)) {
				$response = $this->json_response(422, "Please select student!");
			} elseif (empty($_POST['sel_stud_requirment'])) {
				$response = $this->json_response(422, "Please select atleast one requirement!");
			} else {

				//
				$dt = $this->genDateTime();

				// get school term
				$schterm = $this->pdo->prepare("SELECT id from sch_terms where sch_sts = :sch_sts");
				$schterm->execute(["sch_sts" => yes]);
				$term = $schterm->fetchObject();

				// student 
				$studentqry = $this->pdo->prepare("SELECT * from student where id = :id");
				$studentqry->execute(["id" => $studentid]);
				$student = $studentqry->fetchObject();

				// check for requirements
				$check_requirements = $this->db->prepare("SELECT * FROM track_requirements where student_id = ? and class_id = ? and school_term = ?");
				$check_requirements->bind_param("iii", $studentid, $classid, $term->id);
				$check_requirements->execute();
				$check_requirements->store_result();

				// check if all requirements have been brought
				$allrequirements = $this->pdo->prepare("SELECT count(*) as totalrequirments from requirements");
				$allrequirements->execute();
				$allrequirement = $allrequirements->fetchObject();

				//
				if ($check_requirements->num_rows === 0) {

					// save to track students requirements tbl
					$save = $this->pdo->prepare("INSERT INTO track_requirements(student_id, class_id, requirement_id, req_sts, school_term, created_at, updated_at) VALUES (:student_id, :class_id, :requirement_id, :req_sts, :school_term, :created_at, :updated_at)");

					$requirementstring = "";
					$requirementsbrought = 0;
					for ($i = 0; $i < count($_POST['sel_stud_requirment']); $i++) {
						$requirementsbrought += 1;
						$requirementstring .= $_POST['sel_stud_requirment'][$i] . ", ";
					}
					$sel_req = rtrim($requirementstring, ", ");

					// if all requirements have been brought, we update the `req_sts` tbl as completed
					$req_sts = $allrequirement->totalrequirments <= $requirementsbrought ? yes : no;

					//
					$save->execute([
						'student_id' => $studentid,
						'class_id' => $classid,
						'requirement_id' => $sel_req,
						'req_sts' => $req_sts,
						'school_term' => $term->id,
						'created_at' => $dt,
						'updated_at' => $dt
					]);

					// requirement id
					$requirementid = $this->pdo->lastInsertId();

					// backup all student requirements
					$this->pdo->prepare("INSERT INTO student_requirement_id(student_id, requirement_id, created_at) values (?, ?, ?)")->execute([$studentid, $requirementid, $dt]);

					// update
					$updates_stud_tbl = $this->pdo->prepare("UPDATE student set requirement_id = ?, updated_at = ? where id = ?");
					$updates_stud_tbl->execute([$requirementid, $dt, $studentid]);

					//
					$response = $this->json_response(200, "Student requirement saved!", true);
				} else {

					$requirementstring = "";
					$requirementsbrought = 0;
					for ($i = 0; $i < count($_POST['sel_stud_requirment']); $i++) {
						$requirementsbrought += 1;
						$requirementstring .= $_POST['sel_stud_requirment'][$i] . ", ";
					}
					$sel_req = rtrim($requirementstring, ", ");

					// if all requirements have been brought, we update the `req_sts` tbl as completed
					$req_sts = $allrequirement->totalrequirments <= $requirementsbrought ? yes : no;

					// update
					$update_requirement = $this->pdo->prepare("UPDATE track_requirements SET requirement_id = :reqid, req_sts = :req_sts, school_term = :schterm, updated_at = :updatedat where student_id = :studid and class_id = :classid and id = :id");
					$update_requirement->execute([
						"reqid" => $sel_req,
						"req_sts" => $req_sts,
						"schterm" => $term->id,
						"updatedat" => $dt,
						"studid" => $studentid,
						"classid" => $classid,
						"id" => $student->requirement_id
					]);

					//
					$response = $this->json_response(200, "Student requirement updated!", true);
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}
		//
		return $response;
	} // end


	// -------------------------- -------------------------------------------------------------------	
	// -------------------------- -------------------------------------------------------------------


	/**
	 * selected student payment hist
	 *
	 * @return void
	 */
	function sel_student_payment_hist($studid)
	{
		$fees = $this->pdo->prepare("SELECT b.payment_date, b.amount, b.slip_serial, b.id as paymentid, d.class_name, c.sch_term, c.sch_year from student a join track_fee_payments t.stud_id = a.id join payments on payments b on a.id = b.stud_id 			
			 join sch_terms c on b.school_term = c.id join class_streams d on b.class_id = d.id	where a.id = :studid");
		$fees->execute(["studid" => $studid]);
		$results = $fees->fetchAll();

		$view = "";
		$i = 1;
		foreach ($results as $row) :

			$class_name = $row['class_name'];
			$sch_term   = $row['sch_term'];
			$sch_year   = $row['sch_year'];

			$paymentid   = $row['paymentid'];
			$paymentdate = $row['payment_date'];
			$amount      = $row['amount'];
			$slipserial  = $row['slip_serial'];

			$view .= '<tr>';
			$view .= '<th scope="row">' . $i++ . '</th>';
			$view .= '<td><p>' . $paymentdate . '</p></td>';
			$view .= '<td><p>' . $sch_year . '</p></td>';
			$view .= '<td><p>' . $sch_term . '</p></td>';
			$view .= '<td><p>' . $class_name . '</p></td>';
			$view .= '<td><p>' . $slipserial . '</p></td>';
			$view .= '<td>' . number_format($amount, 1) . '</td>';

			$view .= '<td class="text-center">';
			$view .= '<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">';
			// $view .= '<button class="btn btn-info edit_fees" type="button" title="Pay fees" data-id="' . $studid . '" data-efno="' . $efno . '">Edit</button>';

			$view .= '<a href="" class="btn btn-primary" title="Print">Print</a>';
			$view .= '</div>';
			$view .= '</td>';
			$view .= '</tr>';
		endforeach;

		return $view;
	} // end


	/**
	 * all students payments report
	 *
	 * @param  mixed $student_sel
	 * @param  mixed $filter_data
	 * @return mixed
	 */
	private function all_students_payments_report($rpt_term, $rpt_date, $student_sel, $filter_data)
	{

		$view = '';
		$view .= '<div class="table-responsive">';
		$view .= '<table class="table table-sm table-hover w-100" id="report-list">';
		$view .= '<thead class="bg-secondary text-light">';
		$view .= '<tr>';
		$view .= '<th scope="col">#</th>';
		$view .= '<th scope="col">Student names</th>';
		$view .= '<th scope="col">ID No.</th>';
		$view .= '<th scope="col">Amount to pay</th>';
		$view .= '<th scope="col">Paid Amount</th>';
		$view .= '<th scope="col">Balance</th>';
		$view .= '</tr>';
		$view .= '</thead>';

		// filter_data
		$a = 0;
		$b = 0;
		$c = 0;
		$d = 0;
		$e = 0;


		$i = 1;
		$total = 0;
		$query = "SELECT SUM(p.amount) as amount_paid, p.payment_date, s.name as sname, ef.school_term, ef.ef_no, p.slip_serial, s.id_no, ef.total_fee, st.sch_term, st.sch_year FROM payments p 
		inner join track_fee_payments ef on ef.id = p.ef_id 
		inner join student s on s.id = ef.stud_id 
		inner join sch_terms st  on st.id = ef.school_term 
		where (1=1";

		// current school term
		$term_string = "";
		if (!empty($rpt_term)) {
			$sch_terms = $this->pdo->prepare("SELECT sch_term, sch_year FROM sch_terms WHERE id = :id");
			$sch_terms->execute(['id' => $rpt_term]);
			$sch_term = $sch_terms->fetchObject();

			$term_string = "TERM " . $sch_term->sch_term . "- YEAR " . $sch_term->sch_year;
		} else {
			$term_string = "ALL TERMS";
		}


		//
		foreach ($filter_data as $k => $v) :

			// all students

			// student selected
			if ($v['type'] == 'dt_student') :
				++$a;
				$value = $v['value'];
			endif;

			// school term selected
			if ($v['type'] == 'dt_school_term') :
				++$b;
				$value = $v['value'];
				if ($value != "") {
					if ($b > 1) {
						$query .= " OR ef.school_term ='" . $value . "'";
					} else {
						$query .= " ) AND (ef.school_term ='" . $value . "'";
					}
				}
			//  else {
			// 	if ($b > 1) {
			// 		$query .= " OR ef.school_term ='" . $sch_term->id . "'";
			// 	} else {
			// 		$query .= " ) AND (ef.school_term ='" . $sch_term->id . "'";
			// 	}
			// }
			endif;

			// date range

			// school term selected
			if ($v['type'] == 'dt_date_range') :
				++$c;
				$value = $v['value'];
				if ($value != "") {
					if ($c > 1) {
						$query .= " OR p.payment_date ='" . $value . "'";
					} else {
						$query .= " ) AND (p.payment_date ='" . $value . "'";
					}
				}
			endif;
		endforeach;

		//
		$query .= " )";
		$query .= " group by ef.stud_id order by unix_timestamp(p.payment_date) asc";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$payments = $stmt->get_result();

		$messagetop = "";

		if ($payments) {

			$view .= '<tbody>';

			while ($row = $payments->fetch_assoc()) :

				// $total += $row['amount'];


				$messagetop = $student_sel == 'all' ? "ALL STUDENTS" : "STUDENT: " . strtoupper($row['sname']);

				$view .= '<tr>';
				$view .= '<th scope="row">' . $i++ . '</th>';
				$view .= '<td>' . ucwords($row['sname']) . '</td>';
				$view .= '<td>' . $row['id_no'] . '</td>';
				$view .= '<td>' . number_format($row['total_fee'], 2) . '</td>';
				$view .= '<td>' . number_format($row['amount_paid'], 2) . '</td>';
				$view .= '<td>' . number_format(($row['total_fee'] - $row['amount_paid']), 2) . '</td>';
				// $view .= '<td>' . (new DateTime($row['payment_date']))->format("M d, Y") . '</td>';
				// $view .= '<td>' . $row['sch_term'] . '</td>';
				// $view .= '<td>' . $row['sch_year'] . '</td>';
				// $view .= '<td>' . $row['slip_serial'] . '</td>';
				$view .= '</tr>';

			endwhile;
		} else {

			$view .= '<tr>';
			$view .= '<th class="text-center" colspan="5">No Data.</th>';
			$view .= '</tr>';
		}

		$view .= '</tbody>';
		//

		$view .= '</table>';
		$view .= '</div>';

		return json_encode(array("view" => $view, "title" => "PAYMENT REPORT", "messagetop" => $messagetop . ', ' . $term_string));
	}



	/**
	 * selected students payments report
	 *
	 * @param  mixed $student_sel
	 * @param  mixed $filter_data
	 * @return mixed
	 */
	private function sel_students_payments_report($rpt_term, $rpt_date, $student_sel, $filter_data)
	{

		$view = '';
		$view .= '<div class="table-responsive">';
		$view .= '<table class="table table-sm table-hover w-100" id="report-list">';
		$view .= '<thead class="bg-secondary text-light">';
		$view .= '<tr>';
		$view .= '<th scope="col">#</th>';
		$view .= '<th scope="col">Payment Date</th>';
		// $view .= '<th scope="col">Term</th>';
		// $view .= '<th scope="col">Year</th>';
		$view .= '<th scope="col">Serial No.</th>';
		// $view .= '<th scope="col">ID No.</th>';
		$view .= '<th scope="col">Paid Amount</th>';
		// $view .= '<th scope="col">Balance</th>';
		$view .= '</tr>';
		$view .= '</thead>';

		// filter_data
		$a = 0;
		$b = 0;
		$c = 0;
		$d = 0;
		$e = 0;


		$i = 1;
		$total = 0;
		$query = "SELECT p.*, s.name as sname, ef.school_term, ef.ef_no, p.slip_serial, s.id_no, ef.total_fee, st.sch_term, st.sch_year FROM payments p inner join track_fee_payments ef on ef.id = p.ef_id inner join student s on s.id = ef.stud_id inner join sch_terms st on st.id = ef.school_term and s.id = '" . $student_sel . "' where (1=1";

		// current school term
		$term_string = "";
		if (!empty($rpt_term)) {
			$sch_terms = $this->pdo->prepare("SELECT sch_term, sch_year FROM sch_terms WHERE id = :id");
			$sch_terms->execute(['id' => $rpt_term]);
			$sch_term = $sch_terms->fetchObject();

			$term_string = "TERM " . $sch_term->sch_term . "- YEAR " . $sch_term->sch_year;
		} else {
			$term_string = "ALL TERMS";
		}

		//
		foreach ($filter_data as $k => $v) :

			// school term selected
			if ($v['type'] == 'dt_school_term') :
				++$b;
				$value = $v['value'];
				if ($value != "") {
					if ($b > 1) {
						$query .= " OR ef.school_term ='" . $value . "'";
					} else {
						$query .= " ) AND (ef.school_term ='" . $value . "'";
					}
				}
			endif;

			// date range

			// school term selected
			if ($v['type'] == 'dt_date_range') :
				++$c;
				$value = $v['value'];
				if ($value != "") {
					if ($c > 1) {
						$query .= " OR p.payment_date ='" . $value . "'";
					} else {
						$query .= " ) AND (p.payment_date ='" . $value . "'";
					}
				}
			endif;
		endforeach;

		//
		$query .= " )";
		$query .= " order by unix_timestamp(p.payment_date) asc";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$payments = $stmt->get_result();

		$messagetop = "";


		if ($payments) {

			$view .= '<tbody>';

			while ($row = $payments->fetch_assoc()) :

				$total += $row['amount'];

				$messagetop = "STUDENT: " . strtoupper($row['sname']);

				$view .= '<tr>';
				$view .= '<th scope="row">' . $i++ . '</th>';
				$view .= '<td>' . (new DateTime($row['payment_date']))->format("M d, Y") . '</td>';
				$view .= '<td>' . $row['slip_serial'] . '</td>';
				// $view .= '<td>' . $row['id_no'] . '</td>';
				$view .= '<td>' . number_format($row['amount'], 2) . '</td>';
				$view .= '</tr>';

			endwhile;
		} else {

			$view .= '<tr>';
			$view .= '<th class="text-center" colspan="3">No Data.</th>';
			$view .= '</tr>';
		}

		$view .= '</tbody>';
		$view .= '<tfoot>';
		$view .= '<tr>';
		$view .= '<th colspan="3" class="text-right">Total</th>';

		$view .= '<th scope="row">' . number_format($total, 2) . '</th>';
		$view .= '</tr>';
		$view .= '</tfoot>';

		//

		$view .= '</table>';
		$view .= '</div>';

		return json_encode(array("view" => $view, "title" => "PAYMENT REPORT", "messagetop" => $messagetop . ', ' . $term_string));
	}


	/**
	 * payments report
	 *
	 * @return void
	 */
	function payments_report()
	{
		extract($_POST);

		if ($student_sel == 'all') {
			return $this->all_students_payments_report($rpt_term, $rpt_date, $student_sel, $filter_data);
		} else {
			return $this->sel_students_payments_report($rpt_term, $rpt_date, $student_sel, $filter_data);
		}
	} //


	/**
	 * students reports
	 *
	 * @return mixed
	 */
	function students_reports()
	{
		extract($_POST);
		$view = "";

		if (empty($rprt_student)) {
			$view = '<div class="alert alert-danger" role="alert"><p class="my-1">Please select student</p></div>';
			return json_encode(array("bool" => false, "msg" => $view));
		} else {
			$stmt = $this->pdo->prepare("SELECT * FROM student where id = :id");
			$stmt->execute(["id" => $rprt_student]);
			$student = $stmt->fetchObject();

			if ($student) {

				$bool = false;

				// house
				$houses = $this->pdo->prepare("SELECT house_name FROM houses where id = :id");
				$houses->execute(["id" => $student->house_id]);
				$house = $houses->fetchObject();

				// class
				$classes = $this->pdo->prepare("SELECT a.class_name, c.amount FROM class_streams a inner join class_details b on a.id = b.class_id inner join fees c on c.id = b.fees_id where a.id = :id");
				$classes->execute(["id" => $student->class_id]);
				$classs = $classes->fetchObject();

				// fees 
				$feeses = $this->pdo->prepare("SELECT * FROM fees where id = :id");
				$feeses->execute(["id" => $student->class_id]);
				$fees = $feeses->fetchObject();

				// parents
				$parentss =  $this->pdo->prepare("SELECT * FROM parents");
				$parentss->execute();
				$parents = $parentss->fetchAll();
				$parentsview = "";
				$parentsview .= '<fieldset class="mb-2">';
				$parentsview .= '<legend class="h6 text-center font-weight-bold">PARENT INFORMATION</legend>';
				foreach ($parents as $parent) {

					$parentids = explode(', ', $parent['student_id']);

					if (in_array($student->id, $parentids)) {
						$bool = true;
						$parentsview .= '<div class="d-flex justify-content-around mb-3">';
						$parentsview .= '<div class="">';
						$parentsview .= '<p class=""><strong class="mr-2">Names:</strong>' . $parent['parent_names'] . '</p>';
						$parentsview .= '<p class=""><strong class="mr-2">Gender:</strong>' . $parent['gender'] . '</p>';
						$parentsview .= '</div>';
						$parentsview .= '<div class="">';
						$parentsview .= '<p class=""><strong class="mr-2">Contact:</strong>' . $parent['contacts'] . '</p>';
						$parentsview .= '<p class=""><strong class="mr-2">Email:</strong>' . $parent['email'] . '</p>';
						$parentsview .= '</div>';
						$parentsview .= '</div>';
						$parentsview .= '<p class="text-center"><strong class="mr-2">Residence:</strong>' . $parent['residence'] . '</p>';
					}
				}
				$parentsview .= '</fieldset>';

				$view .= '<h4 class="text-center font-weight-bold mb-3">STUDENT DETAILS REPORT</h4>';

				$view .= '<div class="text-center mb-4">';
				$view .= '<img src="' . $student->photo . '" alt="" class="img-thumbnail" style="height: 150px">';
				$view .= '</div>';

				$view .= '<fieldset class="mb-2">';
				$view .= '<legend class="h6 text-center font-weight-bold">PERSONAL INFORMATION</legend>';
				$view .= '<div class="d-flex justify-content-around mb-3">';
				$view .= '<div class="">';
				$view .= '<p class=""><strong class="mr-2">Names:</strong>' . $student->name . '</p>';
				$view .= '<p class=""><strong class="mr-2">Gender:</strong>' . $student->gender . '</p>';
				$view .= '</div>';
				$view .= '<div class="">';
				$view .= '<p class=""><strong class="mr-2">DOB:</strong>' . $student->dob . '</p>';
				$view .= '<p class=""><strong class="mr-2">Email:</strong>' . $student->email . '</p>';
				$view .= '</div>';
				$view .= '</div>';
				$view .= '</fieldset>';

				if ($bool) $view .= $parentsview;

				$view .= '<fieldset class="mb-2">';
				$view .= '<legend class="h6 text-center font-weight-bold">SCHOOL INFORMATION</legend>';
				$view .= '<div class="d-flex justify-content-around mb-3">';
				$view .= '<div class="">';
				$view .= '<p class=""><strong class="mr-2">House:</strong>' . $house->house_name . '</p>';
				$view .= '<p class=""><strong class="mr-2">Class:</strong>' . $classs->class_name . '</p>';
				$view .= '</div>';
				$view .= '<div class="">';
				$view .= '<p class=""><strong class="mr-2">School section:</strong>' . $student->school_section . '</p>';
				$view .= '<p class=""><strong class="mr-2">Fees:</strong>' . number_format($classs->amount, 2) . '</p>';
				$view .= '</div>';
				$view .= '</div>';
				$view .= '</fieldset>';


				//
				return json_encode(array("bool" => true, "msg" => $view));
			} else {
				$view = '<div class="alert alert-danger" role="alert"><p class="my-1">Failed to load student information. Please try again later.</p></div>';
				return json_encode(array("bool" => false, "msg" => $view));
			}
		}
	} // end



	/**
	 * requirements reports
	 *
	 * @return mixed
	 */
	function requirements_reports()
	{
		extract($_POST);

		$view = "";
		try {
			if (empty($rprt_req_student)) {
				$view = '<div class="alert alert-danger" role="alert"><p class="my-1">Please select student!</p></div>';
				return $this->json_response(422, $view);
			} elseif (empty($rprt_req_school_term)) {
				$view = '<div class="alert alert-danger" role="alert"><p class="my-1">Please select school term!</p></div>';
				return $this->json_response(422, $view);
			} else {

				$stmt = $this->pdo->prepare("SELECT s.*, t.requirement_id, sc.sch_term, sc.sch_year FROM student s inner join track_requirements t on s.requirement_id = t.id inner join sch_terms sc on t.school_term = sc.id where s.id = :id and t.school_term = :term");
				$stmt->execute(["id" => $rprt_req_student, "term" => $rprt_req_school_term]);
				$student = $stmt->fetchObject();

				if ($student) {

					// these're requirements the student has brought so far;
					$reqids = explode(", ", $student->requirement_id);
					$clause = implode(',', array_fill(0, count($reqids), '?')); //create 3 question marks
					$types = str_repeat('i', count($reqids)); //create 3 ints for bind_param

					// get requirements names and descriptions
					$reqinfos = $this->db->prepare("SELECT id, item_name, item_description from requirements where id in ($clause)");
					$reqinfos->bind_param($types, ...$reqids);
					$reqinfos->execute();
					$resArr = $reqinfos->get_result()->fetch_all(MYSQLI_ASSOC);

					if ($resArr) {

						$cnt = 1;


						//
						$dt = (new DateTime($this->genDateTime()))->format("d M, Y h:i");

						$view = '<div class="student_requirements_wrapper">';

						$view .= '<div class="req_rprt_header mb-5 text-center">';
						$view .= '<h4 class="font-weight-bold mb-4">STUDENT REQUIREMENTS REPORT</h4>';
						$view .= '<div class="d-flex justify-content-around">';
						$view .= '<p class="text-uppercase"><strong class="mr-2">STUDENT NAMES:</strong>' . $student->name . '</p>';
						$view .= '<p class="text-uppercase"><strong class="mr-2">YEAR-TERM:</strong>' . $student->sch_year . ' - ' . $student->sch_term . '</p>';
						// $view .= '<p class="text-uppercase"><strong class="mr-2">DATE:</strong>' . $dt . '</p>';
						$view .= '</div>';
						$view .= '</div>';

						$view .= '<div class="req_rprt_content mb-5 d-flex justify-content-center">';

						$view .= '<table class="table table-striped">';
						$view .= '<thead class="bg-secondary text-light">';
						$view .= '<tr>';
						$view .= '<th>#</th>';
						$view .= '<th>ITEM</th>';
						$view .= '<th>DESCRIPTION</th>';
						$view .= '</tr>';
						$view .= '</thead>';

						$view .= '<tbody>';
						foreach ($resArr as $resAr) {

							$view .= '<tr>';
							$view .= "<th scope='row'>" . $cnt++ . "</th>";
							$view .= "<th scope='row'>" . $resAr['item_name'] . "</th>";
							$view .= "<th scope='row'>" . $resAr['item_description'] . "</th>";
							$view .= '</tr>';
						}

						$view .= '</tbody>';
						$view .= '</table>';

						$view .= '</div>';

						$view .= '</div>';

						//
						return $this->json_response(200, $view, true);
					} else {
						//
						$view = '<div class="alert alert-info" role="alert"><p class="my-1">No results found for selected parameters. Please try again.</p></div>';
						return $this->json_response(422, $view);
					}
				} else {

					//
					$view = '<div class="alert alert-info" role="alert"><p class="my-1">No results found for selected parameters. Please try again.</p></div>';
					return $this->json_response(422, $view);
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$view = '<div class="alert alert-danger" role="alert"><p class="my-1">Internal server failed to process request. Please try again.</p></div>';
			return $this->json_response(500, $view);
		}
	} // end



	/**
	 * load student roll call form based on class selected
	 *
	 * @return mixed
	 */
	function load_student_roll_call()
	{

		$response = "";
		$view = "";

		try {
			extract($_POST);

			if (empty($roll_call_class)) {
				$view = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
				<p class="my-1">Please select class to roll call!</p>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>';

				$response = $this->json_response(422, $view);
			} else {

				//
				$stmt = $this->pdo->prepare("SELECT b.teacher_names, b.id as teacher_id from class_details a inner join teachers b on b.id = a.teacher_id where a.class_id = :cid");
				$stmt->execute(["cid" => $roll_call_class]);
				$result1 = $stmt->fetchObject();

				if (!$result1) {
					$view = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
					<p class="my-1">No information regarding selected class found!</p>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>';
					$response = $this->json_response(422, $view);
				} else {

					//
					$students =  $this->pdo->prepare("SELECT id, name from student where class_id = :cid order by name asc");
					$students->execute(["cid" => $roll_call_class]);
					$result2 = $students->fetchAll();

					$cnt = 1;

					//
					$view = '<div class="card shadow-sm mb-4">';
					$view .= '<div class="card-body">';
					$view .= '<div class="d-flex align-items-md-center justify-content-md-around">';
					$view .= '<p class="h6 my-1"><strong class="mr-2">Class Teacher:</strong>' . $result1->teacher_names . '</p>';
					$view .= '<p class="h6 my-1"><strong class="mr-2">No. of students:</strong>' . count($result2) . '</p>';
					$view .= '</div>';
					$view .= '</div>';
					$view .= '</div>';

					if (!$result2) {
						$view .= '<div class="alert alert-info" role="alert">
							<p class="my-1">Selected class has no students. Please select another class!</p>
						  </div>';
						$response = $this->json_response(422, $view);
					} else {
						//
						$rc_date = (new DateTime($roll_call_date))->format('Y-m-d');


						// check if information already exists;
						$checkexists = $this->db->prepare("SELECT * FROM roll_call where roll_call_date = ? and class_id = ?");
						$checkexists->bind_param('si', $rc_date, $roll_call_class);
						$checkexists->execute();
						$ifexists = $checkexists->get_result();

						//
						$view .= '<form id="submit_roll_call_info_dtfrm" role="form">';
						$view .= '<div class="table-responsive">';
						$view .= '<table class="table table-sm w-100">';
						$view .= '<thead class="bg-secondary text-light">';
						$view .= '<tr>';
						$view .= '<th scope="col" style="width: 10%">#</th>';
						$view .= '<th scope="col" style="width: 70%">Student</th>';
						$view .= '<th scope="col" style="width: 20%">Status</th>';
						$view .= '</tr>';
						$view .= '</thead>';
						$view .= '<tbody>';

						if ($ifexists->num_rows === 0) {

							foreach ($result2 as $row) {
								$view .= '<input type="hidden" value="" name="rc_id[]">';
								$view .= '<input type="hidden" value="' . $row['id'] . '" name="rc_stud_id[]">';
								$view .= '<input type="hidden" value="' . $row['name'] . '" name="rc_stud_name[]">';
								$view .= '<tr>';
								$view .= '<th scope="row">' . $cnt++ . '</th>';
								$view .= '<th scope="row">' . $row['name'] . '</th>';
								$view .= '<th scope="row">';
								$view .= '<select class="form-control" name="rc_stud_status[]"><option value="">---select---</option><option value="Absent">Absent</option><option value="Present">Present</option></select>';
								$view .= '</th>';
								$view .= '</tr>';
							}
						} else {

							while ($row = $ifexists->fetch_assoc()) {

								$isabsent = $row['stud_status'] == "Absent" ? "selected" : "";
								$ispresent = $row['stud_status'] == "Present" ? "selected" : "";

								$view .= '<input type="hidden" value="' . $row['id'] . '" name="rc_id[]">';
								$view .= '<input type="hidden" value="' . $row['student_id'] . '" name="rc_stud_id[]">';
								$view .= '<input type="hidden" value="' . $row['student_names'] . '" name="rc_stud_name[]">';
								$view .= '<tr>';
								$view .= '<th scope="row">' . $cnt++ . '</th>';
								$view .= '<th scope="row">' . $row['student_names'] . '</th>';
								$view .= '<th scope="row">';
								$view .= '<select class="form-control" name="rc_stud_status[]">';
								$view .= '<option value="">---select---</option>';
								$view .= '<option value="Absent" ' . $isabsent . '>Absent</option>';
								$view .= '<option value="Present" ' . $ispresent . '>Present</option>';
								$view .= '</select>';
								$view .= '</th>';
								$view .= '</tr>';
							}
						}
						$view .= '</tbody>';
						$view .= '</table>';
						$view .= '</div>';
						$view .= '<input type="hidden" name="rc_class_id" value="' . $roll_call_class . '">';
						$view .= '<input type="hidden" name="rc_class_date" value="' . $rc_date . '">';
						$view .= '<input type="hidden" name="rc_class_teacher" value="' . $result1->teacher_id . '">';
						$view .= '<div class="form-group"><button type="submit" id="submit_roll_call_btn" class="btn btn-info">Submit</button></div>';
						$view .= '</form>';

						//
						$response = $this->json_response(200, $view, true);
					}
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$view = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<p class="my-1">Internal server error. Please try again later!</p>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>';
			//
			$response = $this->json_response(500, $view);
		}
		//
		return $response;
	} // end



	/**
	 * save roll call information
	 *
	 * @return mixed
	 */
	function save_roll_call_information()
	{
		$response = "";

		try {
			extract($_POST);

			//
			$dt = $this->genDateTime();


			// array
			// $rc_stud_id;
			// rc_stud_name
			// $rc_stud_status;

			// //
			// $rc_class_id;
			// $rc_class_date;
			// $rc_class_teacher;

			if (empty($rc_stud_status)) {
				//
				$response = $this->json_response(422, "Please select student status!");
			} else {

				//
				$bool = true;
				$msg = "";
				$data = array();

				//
				foreach ($rc_stud_status as $key => $value) {
					$name   = $rc_stud_name[$key];
					$studid = $rc_stud_id[$key];
					if (empty($value)) {
						$bool = false;
						$msg = "Please select status for student: <strong>{$name}</strong>";
					} else {
						$bool = true;
						$data[] = array("name" => $name, "studid" => $studid, "studstatus" => $value);
					}
				}

				if ($bool === false) {
					//
					$response = $this->json_response(422, $msg, false);
				} else {

					// check if information already exists;
					$checkexists = $this->db->prepare("SELECT * FROM roll_call where roll_call_date = ? and class_id = ?");
					$checkexists->bind_param('si', $rc_class_date, $rc_class_id);
					$checkexists->execute();
					$result = $checkexists->get_result();

					if ($result->num_rows === 0) {

						//
						$save = $this->pdo->prepare("INSERT INTO roll_call (student_names, student_id, class_id, roll_call_date, stud_status, created_at, updated_at) values (:student_names, :student_id, :class_id, :roll_call_date, :stud_status, :created_at, :updated_at)");

						// loop through
						foreach ($data as $x => $x_value) {

							$save->execute([
								'student_names' => $x_value['name'],
								'student_id' => $x_value['studid'],
								'class_id' => $rc_class_id,
								'roll_call_date' => $rc_class_date,
								'stud_status' => $x_value['studstatus'],
								'created_at' => $dt,
								'updated_at' => $dt
							]);
						}

						//
						$response = $this->json_response(200, "Roll call information saved!", true);
					} else {

						//
						$update = $this->pdo->prepare("UPDATE roll_call set stud_status = :stud_status, updated_at = :updated_at where class_id = :class_id and roll_call_date = :roll_call_date and student_id = :student_id");
						// loop through
						foreach ($data as $x => $x_value) {

							$update->execute([
								'stud_status' => $x_value['studstatus'],
								'updated_at' => $dt,
								'class_id' => $rc_class_id,
								'roll_call_date' => $rc_class_date,
								'student_id' => $x_value['studid'],
							]);
						}

						//
						$response = $this->json_response(200, "Roll call information updated!", true);
					}
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}
		//
		return $response;
	} // end



	/**
	 * teachers
	 *
	 * @return mixed
	 */
	function teacher_info_report()
	{

		$response = "";

		try {
			extract($_POST);

			if (empty($rprt_teacher_info)) {
				//
				$response = $this->json_response(422, "Please select teacher!", false);
			} else {

				//
				$stmt = $this->pdo->prepare("SELECT * FROM teachers where id = :id");
				$stmt->execute(["id" => $rprt_teacher_info]);
				$result = $stmt->fetchObject();

				//
				$stmt2 = $this->pdo->prepare("SELECT a.class_name, b.* FROM class_streams a join class_details b on b.class_id = a.id where b.teacher_id = :id");
				$stmt2->execute(["id" => $rprt_teacher_info]);
				$result2 = $stmt2->fetchObject();

				if ($result) {

					$view = '<div id="teacher_info_rprt_cnt">';
					$view .= '<div id="tch_rprt_header" class="text-center mb-4">';
					$view .= '<h5 class="font-weight-bold">TEACHER DETAILS REPORT</h5>';
					$view .= '</div>';
					$view .= '<div id="tch_rprt_pp" class="text-center mb-5">';
					$view .= '<img src="' . $result->photo . '" alt="" class="img-thumbnail" style="height: 150px">';
					$view .= '</div>';

					$view .= '<div id="tch_rprt_cnt" class="mb-3 text-center">';

					$view .= '<h6 class="font-weight-bold mb-3">PERSONAL DETAILS</h6>';

					$view .= '<table class="w-100 table mb-4">';
					$view .= '<tr>';
					$view .= '<th>NAMES: </th><td>' . $result->teacher_names . '</td>';
					$view .= '</tr>';
					$view .= '<tr>';
					$view .= '<th>DOB: </th><td>' . $result->teacher_dob . '</td>';
					$view .= '</tr>';

					$view .= '<tr>';
					$view .= '<th>GENDER: </th><td>' . $result->teacher_sex . '</td>';
					$view .= '</tr>';

					$view .= '<tr>';
					$view .= '<th>TEL: </th><td>' . $result->teacher_tel . '</td>';
					$view .= '</tr>';

					$view .= '<tr>';
					$view .= '<th>EMAIL: </th><td>' . $result->teacher_email . '</td>';
					$view .= '</tr>';

					$view .= '<tr>';
					$view .= '<th>EDUCATION: </th><td>' . $result->teacher_education . '</td>';
					$view .= '</tr>';

					$view .= '<tr>';
					$view .= '<th>LOCATION ADDRESS: </th><td>' . $result->teacher_location_address . '</td>';
					$view .= '</tr>';
					//
					$view .= '</table>';


					$view .= '<h6 class="font-weight-bold mb-3">SCHOOL DETAILS</h6>';

					$view .= '<table class="w-100 table">';
					$view .= '<tr>';
					$view .= '<th>CLASS: </th><td>' . $result2->class_name . '</td>';
					$view .= '</tr>';
					$view .= '<tr>';
					$view .= '<th>SALARY: </th><td>' . $result->teacher_salary . '</td>';
					$view .= '</tr>';
					$view .= '</table>';

					$view .= '</div>';
					$view .= '</div>';

					//
					$response = $this->json_response(200, $view, true);
				} else {

					//
					$response = $this->json_response(422, "Failed to load teacher information. Data missing!", false);
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}
		//
		return $response;
	} // end



	/**
	 * fetch teachers and bursars
	 */
	function fetch_teachers_bursars($table = LV_3)
	{
		if ($table == LV_3) {
			$stmt = $this->pdo->prepare("SELECT id, teacher_names from teachers order by teacher_names asc");
			$stmt->execute();
			return $stmt->fetchAll();
		}
	} // end






	/**
	 * student roll call report
	 *
	 * @return mixed
	 */
	function student_roll_call_report()
	{

		$response = "";

		try {
			extract($_POST);

			if (empty($roll_call_class)) {
				//
				$response = $this->json_response(422, "<div class='alert alert-danger' role='alert'><p class='my-1'>Please select class!</p></div>", false);
			} elseif (empty($roll_call_date)) {
				//
				$response = $this->json_response(422, "<div class='alert alert-danger' role='alert'><p class='my-1'>Please select date!</p></div>", false);
			} else {

				//
				$stmt = $this->pdo->prepare("SELECT * FROM roll_call where class_id = :id and roll_call_date = :rc_date");
				$stmt->execute(["id" => $roll_call_class, "rc_date" => $roll_call_date]);
				$result = $stmt->fetchAll();

				if ($result) {

					$cnt = 1;

					//
					$stmt2 = $this->pdo->prepare("SELECT a.class_name, b.*, c.teacher_names FROM class_streams a 
					join class_details b on b.class_id = a.id join 
					teachers c on b.teacher_id = c.id 
					where a.id = :id");
					$stmt2->execute(["id" => $roll_call_class]);
					$result2 = $stmt2->fetchObject();

					$view = '<div id="teacher_info_rprt_cnt">';
					$view .= '<div id="tch_rprt_header" class="text-center mb-5">';
					$view .= '<h5 class="font-weight-bold">CLASS ATTENDANCE REPORT</h5>';
					$view .= '</div>';

					$view .= '<div id="tch_rprt_cnt" class="mb-3 text-center">';

					$view .= '<h6 class="font-weight-bold mb-3">CLASS DETAILS</h6>';
					$view .= '<table class="w-100 table mb-4 border-0">';
					$view .= '<tr>';
					$view .= '<th>DATE: </th><td>' . (new DateTime($roll_call_date))->format('d M, Y') . '</td>';
					$view .= '<th>CLASS: </th><td>' . $result2->class_name . '</td>';
					$view .= '<th>TEACHER: </th><td>' . $result2->teacher_names . '</td>';
					$view .= '</tr>';
					$view .= '</table>';

					$view .= '<h6 class="font-weight-bold mb-3">ATTENDANCE DETAILS</h6>';
					$view .= '<table class="w-100 table table-bordered mb-4">';

					$view .= '<thead class="bg-secondary text-light">';
					$view .= '<tr>';
					$view .= '<th scope="col">#</th>';
					$view .= '<th scope="col">STUDENT NAMES</th>';
					$view .= '<th scope="col">STUDENT STATUS</th>';
					$view .= '</tr>';
					$view .= '</thead>';


					$view .= '<tbody>';
					foreach ($result as $row) {
						$view .= '<tr>';
						$view .= '<th scope="row">' . $cnt++ . '</th>';
						$view .= '<td>' . $row["student_names"] . '</td>';
						$view .= '<td>' . $row["stud_status"] . '</td>';
						$view .= '</tr>';
					}
					$view .= '</tbody>';
					$view .= '</table>';



					$view .= '</div>';
					$view .= '</div>';

					//
					$response = $this->json_response(200, $view, true);
				} else {

					//
					$response = $this->json_response(422, "<div class='alert alert-info' role='alert'><p class='my-1'>No results found. Please try again!</p></div>", false);
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "<div class='alert alert-danger' role='alert'><p class='my-1'><p>Internal server error. Please try again later!</p></div>");
		}
		//
		return $response;
	} // end






	/**
	 * generate a random password
	 *
	 * @return mixed
	 */
	function genRandomPassword()
	{

		return $this->genRandomString(8);
	} // end



	/**
	 * assign an existing user login credentials; useful for teachers, bursars
	 *
	 * @return mixed
	 */
	function manage_user_credentials()
	{
		$response = "";

		try {

			extract($_POST);

			//
			if (empty($assign_user_id)) {
				$response = $this->json_response(422, "Please select user to assign credentials!");
			} elseif (empty($assign_user_name)) {
				$response = $this->json_response(422, "Please enter user name!");
			} elseif (empty($assign_user_password)) {
				$response = $this->json_response(422, "Please enter user password!");
			} elseif (strlen($assign_user_password) < 8) {
				$response = $this->json_response(422, "Password can not be less than 8 characters!");
			} else {

				//
				$stmt = $this->pdo->prepare("SELECT * from users where username = :username");
				$stmt->execute(["username" => $assign_user_name]);
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($result) {
					return $this->json_response(422, "Username entered already exists!");
				} else {
					//
					$dt = $this->genDateTime();

					$info_qry =  $this->pdo->prepare("SELECT teacher_names, teacher_email from teachers where id = :id");
					$info_qry->execute(["id" => $assign_user_id]);
					$info = $info_qry->fetchObject();
					//
					$name = $info->teacher_names;
					$email = $info->teacher_email;

					//
					// hash password
					$hashedPassword = password_hash($assign_user_password, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));

					//
					$save = $this->pdo->prepare("INSERT INTO users (name, username, email_address, access_level, password, type, created_at, updated_at) 
										values (:name, :username, :email_address, :access_level, :password, :type, :created_at, :updated_at)");
					$check = $save->execute([
						"name" => $name,
						"username" => $assign_user_name,
						"email_address" => $email,
						"access_level" => LV_3,
						"password" => $hashedPassword,
						"type" => 3,
						"created_at" => $dt,
						"updated_at" => $dt,
					]);

					//
					if ($check) :
						return $this->json_response(200, "{$name} has successfully been assigned login credentials!", true);
					else :
						return $this->json_response(422, "Error submitting information. Please try again!");
					endif;
				}
			}
		} catch (Exception $e) {
			//
			$this->error_logs($e->getMessage());
			//
			$this->logToFile($e->getMessage());
			//
			$response = $this->json_response(500, "Internal server error. Please try again later!");
		}
		//
		return $response;
	}
} // end of class

// init class object to be used gloabally
$crud = new MainClass();
