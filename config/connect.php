<?php

/**
 * constants
 */
require_once "constants.php";

class DBConnect
{


    /**
     * generate the current date and time
     * 
     * @return  mixed
     */
    protected function genDateTime()
    {
        return date('Y-m-d h:i:s');
    }


    /**
     * generate the current date only
     * 
     * @return  mixed
     */
    protected function genDate()
    {
        return date('Y-m-d');
    }



    /**
     * generate a random string
     *
     * @param  int $length the length of the string; defaults to 16
     * @return mixed
     */
    protected function genRandomString($length = 16)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }



    /**
     * log information to file
     *
     * @param  mixed $data  information to write
     * @return void
     */
    protected function logToFile($data)
    {
        // date
        $currentdate = $this->genDate();
        // date and time
        $datetime = $this->genDateTime();

        // file name
        $fileName = $datetime . '-' . $this->genRandomString() . '.txt';
        $dir = LOGS_DIR . $currentdate;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // write
        file_put_contents($dir . '/' . $fileName, $data);
    }


    /**
     * PDO
     * @return  mixed
     */
    protected function dbPDOConnect()
    {
        try {

            /**
             * database connect
             * 
             * @var   PDO
             */
            $connPdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
            // set the PDO error mode to exception
            $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //
            return $connPdo;
        } catch (PDOException $e) {

            $this->logToFile($e);
        }
    }


    /**
     * database; MySQLi Object-Oriented
     * @return  mixed
     */
    protected function dbObjConnect()
    {
        /**
         * database connect
         * 
         * @var   MySQLi Object-Oriented
         */
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($conn->connect_error) {
            $this->logToFile($conn->connect_error);
        }
        return $conn;
    }



    /**
     * json response
     *
     * @param  int   $code
     * @param  mixed $message
     * @param  bool $bool
     * @return mixed
     */
    protected function json_response($code = 200, $message = null, $bool = false)
    {
        // clear the old headers
        header_remove();
        // set the actual code
        http_response_code($code);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        // treat this as json
        header('Content-Type: application/json');
        $status = array(
            200 => '200 OK',
            400 => '400 Bad Request',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error'
        );
        // ok, validation error, or failure
        header('Status: ' . $status[$code]);
        // return the encoded json
        return json_encode(array(
            'status' => $code < 300, // success or not?
            'msg' => $message,
            'bool' => $bool
        ));
    } // end




    /**
     * formats responses for easy view
     *
     * @param  mixed $msg
     * @param  bool  $bool
     * @param  bool  $isdimissable
     * @return mixed
     */
    protected function htmlResponses($msg, $bool, $isdimissable)
    {

        $view = "";

        if ($isdimissable) {
            switch ($bool) {
                case true:
                    $view = '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $msg . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>';
                    break;

                case false:
                    $view = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $msg . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>';
                    break;
            }
        } else {
            switch ($bool) {
                case true:
                    $view = '<div class="alert alert-success" role="alert">' . $msg . '</div>';
                    break;

                case false:
                    $view = '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
                    break;
            }
        }
        return $view;
    }
}
