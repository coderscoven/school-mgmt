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
			} elseif (empty($type)) {
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
					exit;
				}

				//
				$dt = $this->genDateTime();

				//
				$accesslevel = "";
				if ($type == 1) : $accesslevel = LV_1;
				elseif ($type == 2) : $accesslevel = LV_2;
				else : $accesslevel = LV_3;
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
						"access_level" => $accesslevel,
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
					//
					return $this->json_response(200, "User information updated!", true);
				}
			}
		} catch (Exception $e) {
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


	/**
	 * save course
	 *
	 * @return mixed
	 */
	function save_course()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'fid', 'type', 'amount')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM courses where course ='$course' and level ='$level' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO courses set $data");
			if ($save) {
				$id = $this->db->insert_id;
				foreach ($fid as $k => $v) {
					$data = " course_id = '$id' ";
					$data .= ", description = '{$type[$k]}' ";
					$data .= ", amount = '{$amount[$k]}' ";
					$save2[] = $this->db->query("INSERT INTO fees set $data");
				}
				if (isset($save2))
					return 1;
			}
		} else {
			$save = $this->db->query("UPDATE courses set $data where id = $id");
			if ($save) {
				$this->db->query("DELETE FROM fees where course_id = $id and id not in (" . implode(',', $fid) . ") ");
				foreach ($fid as $k => $v) {
					$data = " course_id = '$id' ";
					$data .= ", description = '{$type[$k]}' ";
					$data .= ", amount = '{$amount[$k]}' ";
					if (empty($v)) {
						$save2[] = $this->db->query("INSERT INTO fees set $data");
					} else {
						$save2[] = $this->db->query("UPDATE fees set $data where id = $v");
					}
				}
				if (isset($save2))
					return 1;
			}
		}
	}


	/**
	 * delete course
	 *
	 * @return mixed
	 */
	function delete_course()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM courses where id = " . $id);
		$delete2 = $this->db->query("DELETE FROM fees where course_id = " . $id);
		if ($delete && $delete2) {
			return 1;
		}
	}


	/**
	 * save student
	 *
	 * @return mixed
	 */
	function save_student()
	{
		$response = array();

		try {
			extract($_POST);

			// validation
			if (empty($id_no)) {
				$response = array("bool" => false, "msg" => "Please enter student id number!");
			} elseif (empty($name)) {
				$response = array("bool" => false, "msg" => "Please enter student names!");
			} elseif (empty($id) && empty($_FILES['student_photo']['name'])) {
				$response = array("bool" => false, "msg" => "Please select student photo!");
			} elseif (empty($email)) {
				$response = array("bool" => false, "msg" => "Please enter student email!");
			} elseif (empty($genders)) {
				$response = array("bool" => false, "msg" => "Please select student gender!");
			} elseif (empty($dob)) {
				$response = array("bool" => false, "msg" => "Please select student date of birth!");
			} elseif (empty($stud_house)) {
				$response = array("bool" => false, "msg" => "Please select student house!");
			} elseif (empty($stud_class)) {
				$response = array("bool" => false, "msg" => "Please select student class!");
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

				// check if student id number exists
				$stmt = $this->db->query("SELECT * FROM student where id_no ='$id_no' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
				if ($stmt > 0) {
					$response = array("bool" => false, "msg" => "Student ID number entered already exists!");
					exit;
				}

				//
				$dt = $this->genDateTime();

				//
				if (empty($id)) {

					// upload
					$target_file = STUD_PHOTO_DIR . basename($_FILES["student_photo"]["name"]);
					$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
					$check = getimagesize($_FILES["student_photo"]["tmp_name"]);
					if ($check !== false) {

						if ($_FILES["student_photo"]["size"] < 1024000) {


							if (!in_array($imageFileType, array("jpg", "png", "jpeg"))) {
								$response = array("bool" => false, "msg" => "Only JPG, JPEG, PNG files are allowed.");
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
								$save = $this->pdo->prepare("INSERT INTO student (photo, id_no, name, gender, email, dob, house_id, class_id,  created_at, updated_at) values (:photo, :id_no, :name, :gender, :email, :dob, :house_id, :class_id, :created_at, :updated_at)");
								$save->execute([
									'photo' => $newName,
									'id_no' => $id_no,
									'name' => $name,
									'gender' => $genders,
									'email' => $email,
									'dob' => $dob,
									'house_id' => $stud_house,
									'class_id' => $stud_class,
									'created_at' => $dt,
									'updated_at' => $dt
								]);

								//
								$response = array("bool" => true, "msg" => "New student added!");
							}
						} else {
							$response = array("bool" => false, "msg" => "Photo must not exceed 1mb!");
						}
					} else {
						$response = array("bool" => false, "msg" => "Photo selected is not an image!");
					}
				} else {

					//
					$this->pdo->prepare("UPDATE student set id_no = ?, name = ?, gender = ?, email = ?, dob = ?, house_id = ?, class_id = ?, updated_at = ? where id = ?")->execute([$id_no, $name, $genders, $email, $dob, $stud_house, $stud_class, $dt, $id]);
					//
					$response = array("bool" => true, "msg" => "Student information updated!");
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
	 * save fees
	 *
	 * @return mixed
	 */
	function save_fees()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if ($k == 'total_fee') {
					$v = str_replace(',', '', $v);
				}
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM student_ef_list where ef_no ='$ef_no' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO student_ef_list set $data");
		} else {
			$save = $this->db->query("UPDATE student_ef_list set $data where id = $id");
		}
		if ($save)
			return 1;
	}


	/**
	 * delete fees
	 *
	 * @return mixed
	 */
	function delete_fees()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM student_ef_list where id = " . $id);
		if ($delete) {
			return 1;
		}
	}



	/**
	 * save payment
	 *
	 * @return mixed
	 */
	function save_payment()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if ($k == 'amount') {
					$v = str_replace(',', '', $v);
				}
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO payments set $data");
			if ($save)
				$id = $this->db->insert_id;
		} else {
			$save = $this->db->query("UPDATE payments set $data where id = $id");
		}
		if ($save)
			return json_encode(array('ef_id' => $ef_id, 'pid' => $id, 'status' => 1));
	}


	/**
	 * delete payment
	 *
	 * @return mixed
	 */
	function delete_payment()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM payments where id = " . $id);
		if ($delete) {
			return 1;
		}
	}


	/**
	 * fees
	 *
	 * @return mixed
	 */
	function fees()
	{
		$i = 1;
		$fees = $this->db->query("SELECT ef.*,s.name as sname,s.id_no FROM student_ef_list ef inner join student s on s.id = ef.student_id order by s.name asc ");
		while ($row = $fees->fetch_assoc()) :
			$paid = $this->db->query("SELECT sum(amount) as paid FROM payments where ef_id=" . $row['id']);
			$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';
			$balance = $row['total_fee'] - $paid;
?>
			<tr>
				<td class="text-center"><?php echo $i++ ?></td>
				<td>
					<p> <b><?php echo $row['id_no'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo $row['ef_no'] ?></b></p>
				</td>
				<td>
					<p> <b><?php echo ucwords($row['sname']) ?></b></p>
				</td>
				<td class="text-right">
					<p> <b><?php echo number_format($row['total_fee'], 2) ?></b></p>
				</td>
				<td class="text-right">
					<p> <b><?php echo number_format($paid, 2) ?></b></p>

				<td class="text-right">
					<p> <b><?php echo number_format($balance, 2) ?></b></p>
				</td>
				<td class="text-center">
					<button class="btn btn-sm btn-outline-primary view_payment" type="button" data-id="<?php echo $row['id'] ?>">View</button>
					<button class="btn btn-sm btn-outline-primary edit_fees" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
					<button class="btn btn-sm btn-outline-danger delete_fees" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
				</td>
			</tr>
			<?php
		endwhile;
	} // end



	/**
	 * payments
	 * 
	 * @return void
	 */
	function payments()
	{
		$i = 1;
		$payments = $this->db->query("SELECT p.*,s.name as sname, ef.ef_no,s.id_no FROM payments p inner join student_ef_list ef on ef.id = p.ef_id inner join student s on s.id = ef.student_id order by unix_timestamp(p.date_created) desc ");

		if ($payments->num_rows > 0) :

			while ($row = $payments->fetch_assoc()) :

				$paid = $this->db->query("SELECT sum(amount) as paid FROM payments where ef_id=" . $row['id']);
				$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';
			?>
				<tr>
					<td class="text-center"><?php echo $i++ ?></td>
					<td>
						<p> <b><?php echo date("M d,Y H:i A", strtotime($row['date_created'])) ?></b></p>
					</td>
					<td>
						<p> <b><?php echo $row['id_no'] ?></b></p>
					</td>
					<td>
						<p> <b><?php echo $row['ef_no'] ?></b></p>
					</td>
					<td>
						<p> <b><?php echo ucwords($row['sname']) ?></b></p>
					</td>
					<td class="text-right">
						<p> <b><?php echo number_format($row['amount'], 2) ?></b></p>
					</td>
					<td class="text-center">
						<button class="btn btn-sm btn-outline-primary view_payment" type="button" data-id="<?php echo $row['id'] ?>" data-ef_id="<?php echo $row['ef_id'] ?>">View</button>
						<button class="btn btn-sm btn-outline-primary edit_payment" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
						<button class="btn btn-sm btn-outline-danger delete_payment" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
					</td>
				</tr>
			<?php
			endwhile;
		endif;
	} // end



	/**
	 * courses
	 */
	function courses()
	{

		$i = 1;
		$course = $this->db->query("SELECT * FROM courses  order by course asc ");
		while ($row = $course->fetch_assoc()) :
			?>
			<tr>
				<td class="text-center"><?php echo $i++ ?></td>
				<td>
					<p> <b><?php echo $row['course'] . " - " . $row['level'] ?></b></p>
				</td>
				<td class="">
					<p><small><i><b><?php echo $row['description'] ?></i></small></p>
				</td>
				<td class="text-right">
					<p> <b><?php echo number_format($row['total_amount'], 2) ?></b></p>
				</td>
				<td class="text-center">
					<button class="btn btn-sm btn-outline-primary edit_course" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
					<button class="btn btn-sm btn-outline-danger delete_course" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
				</td>
			</tr>
		<?php endwhile;
	} // end


	/**
	 * fetch students list and display in table
	 * 
	 * @return	void
	 */
	function studentsList()
	{

		$student = $this->db->query("SELECT s.id, s.photo, s.name, s.id_no, s.dob, s.gender, s.email, h.house_name, c.class_name FROM student s join houses h on s.house_id = h.id join class_streams c on s.class_id = c.id order by s.name asc ");
		while ($row = $student->fetch_assoc()) :
		?>
			<tr>
				<td><?php echo $row['class_name'] ?></td>
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
					<button class="btn btn-sm btn-outline-primary edit_student" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
					<button class="btn btn-sm btn-outline-danger delete_student" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
				</td>
			</tr>
		<?php endwhile;
	} // end


	/**
	 * payment reports
	 * 
	 * @return	void
	 */
	function paymentReport($month)
	{
		$i = 1;
		$total = 0;
		$stmt = $this->db->prepare("SELECT p.*,s.name as sname, ef.ef_no,s.id_no FROM payments p inner join student_ef_list ef on ef.id = p.ef_id inner join student s on s.id = ef.student_id where date_format(p.date_created,'%Y-%m') = ? order by unix_timestamp(p.date_created) asc ");
		$stmt->bind_param('s', $month);
		$stmt->execute();
		$payments = $stmt->get_result();

		if ($payments) {
		?>
			<tbody>
				<?php
				while ($row = $payments->fetch_assoc()) :
					$total += $row['amount'];
				?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td>
							<p> <b><?php echo date("M d,Y H:i A", strtotime($row['id_no'])) ?></b></p>
						</td>
						<td>
							<p> <b><?php echo $row['id_no'] ?></b></p>
						</td>
						<td>
							<p> <b><?php echo $row['ef_no'] ?></b></p>
						</td>
						<td>
							<p> <b><?php echo ucwords($row['sname']) ?></b></p>
						</td>
						<td class="text-right">
							<p> <b><?php echo number_format($row['amount'], 2) ?></b></p>
						</td>

						<td class="text-right">
							<p> <b><?php echo $row['remarks'] ?></b></p>
						</td>
					</tr>
				<?php
				endwhile;
			} else {
				?>
				<tr>
					<th class="text-center" colspan="7">No Data.</th>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5" class="text-right">Total</th>
					<th class="text-right"><?php echo number_format($total, 2) ?></th>
					<th></th>
				</tr>
			</tfoot>
			<?php
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

			//
			return array("numStudent" => $numStudent->numStudents, "numTeacher" => $numTeacher->numTeachers);
		}


		/**
		 * fetch teachers list
		 * 
		 * @return void
		 */
		function teachersList()
		{
			$stmt = $this->db->query("SELECT * FROM teachers order by teacher_names asc");
			while ($row = $stmt->fetch_assoc()) :
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
						<button class="btn btn-sm btn-outline-primary edit_teacher" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
						<button class="btn btn-sm btn-outline-danger delete_teacher" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
					</td>
				</tr>
			<?php endwhile;
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
				$stmt = $this->pdo->prepare("DELETE FROM sch_terms WHERE id = ?");
				$stmt->execute([$id]);
				return $this->json_response(200, "School term deleted!", true);
			} catch (Exception $e) {
				$this->logToFile($e->getMessage());
				return $this->json_response(500, "Failed to delete information.");
			}
		}


		/**
		 * toggle school term status
		 * 
		 * @return	mixed
		 */
		function toggle_school_term_status()
		{
			try {

				extract($_POST);
				$dt = $this->genDateTime();
				$newsts = $term == yes ? no : yes;
				//
				$stmt = $this->pdo->prepare("SELECT * FROM sch_terms WHERE sch_sts = ?");
				$stmt->bind_param("s", yes);
				$stmt->execute();
				$arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
				if ($arr) {
					$this->pdo->prepare("UPDATE sch_terms SET sch_sts = ?, updated_at = ? WHERE sch_sts = ?")->execute([no, $dt, yes]);
				}

				//
				$this->pdo->prepare("UPDATE sch_terms SET sch_sts = ?, updated_at = ? WHERE id = ?")->execute([$newsts, $dt, $id]);
				return $this->json_response(200, "School term status updated!", true);
			} catch (Exception $e) {
				$this->logToFile($e->getMessage());
				return $this->json_response(500, "Failed to update school term status");
			}
		}


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

		// -------------------------- -------------------------------------------------------------------



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
						<button class="btn btn-sm btn-outline-primary edit_parent" type="button" data-id="<?php echo $row['parentid'] ?>">Edit</button>
						<button class="btn btn-sm btn-outline-danger delete_parent" type="button" data-id="<?php echo $row['parentid'] ?>">Delete</button>
					</td>
				</tr>
	<?php endwhile;
		} // end



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





		// -------------------------- -------------------------------------------------------------------

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


	}

	// init class object to be used gloabally
	$crud = new MainClass();
