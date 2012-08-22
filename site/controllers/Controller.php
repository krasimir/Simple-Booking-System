<?php

    class Controller {
    
        protected $params;
        protected $root;
        protected $siteURL;
        protected $user;

        private $registerMessage = "";
        
        public function __construct($params) {
            View::$root = dirname(__FILE__)."/../tpl/";
            $this->params = $params;
            $this->root = dirname(__FILE__)."/../";
            $this->siteURL = "http://".$_SERVER["HTTP_HOST"];
            $this->initMysql();
            if($this->isLogin()) {
                $this->run();
            } else {
                $this->renderLayout(view("login.html", array(
                    "registerMessage" => $this->registerMessage
                )));
            }
        }
        protected function run() {
            
        }
        protected function renderLayout($content) {
            die(view("layout.html", array(
                "content" => $content,
                "siteURL" => $this->siteURL 
            )));
        }
        protected function isLogin() {         
            $action = isset($_POST["action"]) ? $_POST["action"] : "";
            switch($action) {
                case "login":
                    $password = isset($_POST["password"]) ? $_POST["password"] : "";
                    $email = isset($_POST["email"]) ? $_POST["email"] : "";
                    if($this->getCurrentUser($password, $email)) {
                        $_SESSION["futbolsessionpassword"] = $password;
                        $_SESSION["futbolsessionemail"] = $email;
                        return true;
                    };
                break;
                case "register":
                    $name = isset($_POST["name"]) ? $_POST["name"] : "";
                    $password = isset($_POST["password"]) ? $_POST["password"] : "";
                    $phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
                    $email = isset($_POST["email"]) ? $_POST["email"] : "";
                    if($name == "" || $password == "" || $phone == "" || $email == "") {
                        $this->registerMessage = '<div class="alert alert-error">Моля попълнете всички полета.</div>';
                        return false;
                    }
                    $q = "INSERT INTO futbol_users (name, password, email, phone) VALUES ('".$name."', '".$password."', '".$email."', '".$phone."')";
                    $this->query($q);
                    $_SESSION["futbolsessionpassword"] = $password;
                    $_SESSION["futbolsessionemail"] = $email;
                    return true;
                break;
                default:
                    if($this->getCurrentUser()) {
                        return true;
                    };
                break;
            }
            return false;
        }
        protected function logout() {
            $_SESSION["futbolsessionpassword"] = "";
            $_SESSION["futbolsessionemail"] = "";
            header("Location: ".$this->siteURL);
        }
        protected function isAdmin() {
            if($this->user && ($this->user->email == "krasimir@outset.ws" || $this->user->email == "info@krasimirtsonev.com")) {
                return true;
            } else {
                return false;
            }
        }
        protected function getUser($id) {
            $q = "SELECT * FROM futbol_users WHERE id = '".$id."'";
            $res = $this->query($q);
            return isset($res->result[0]) ? $res->result[0] : false;
        }
        protected function areOptionsAvailable($record) {
            if($record->userId == $this->user->id || $this->isAdmin()) {
                return true;
            } else {
                return false;
            }
        }
        // mysql
        private function initMysql() {
            mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
            mysql_select_db(MYSQL_DB);
            mysql_query("SET NAMES 'utf8'");
        }
        protected function query($q) {
            $res = mysql_query($q);
            $result = array();
            if(!is_bool($res)) {
                $numOfRes = mysql_num_rows($res);
                while($r = mysql_fetch_object($res)) {
                    array_push($result, $r);
                }
            }
            return (object) array(
                "result" => $result,
                "raw" => $res
            );
        }
        // utils
        protected function getFormatedDate($date) {
            $date = explode("-", $date);
            $gameTime = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
            $day = date("l", $gameTime);
            $month = date("F", $gameTime);
            return $date[0]." ".$month." (".$day.")";
        }
        protected function getBookedPlayers($game) {
            $res = $this->query("SELECT * FROM futbol_players WHERE gameId = '".$game->id."'");
            $totalPlayers = 0;
            if(isset($res->result)) {
                $numOfRecords = count($res->result);
                for($i=0; $i<$numOfRecords; $i++) {
                    $totalPlayers += $res->result[$i]->numOfPlayers;
                }
            }
            return $totalPlayers;
        }
        protected function getCurrentUser($password = null, $email = null) {
            $password = $password != null ? $password : (isset($_SESSION["futbolsessionpassword"]) ? $_SESSION["futbolsessionpassword"] : "");
            $email = $email != null ? $email : (isset($_SESSION["futbolsessionemail"]) ? $_SESSION["futbolsessionemail"] : "");
            $q = "SELECT * FROM futbol_users WHERE password = '".$password."' AND email = '".$email."'";
            $res = $this->query($q);
            return $this->user = isset($res->result[0]) ? $res->result[0] : false;
        }
    }

?>