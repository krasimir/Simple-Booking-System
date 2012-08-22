<?php

    class Users extends Controller {
        protected function run() {
        
            if(!$this->isAdmin()) {
                return false;
            }

            $q = "SELECT * FROM futbol_users";
            $res = $this->query($q);
            
            $total = 0;
            $str = '<table class="table table-bordered">';
            $str .= '
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Password</th>
                </tr>
                </thead>
                <tbody>
            ';
            foreach($res->result as $user) {
                $str .= '
                    <tr>
                        <td>'.$user->name.'</td>
                        <td>'.$user->email.'</td>
                        <td>'.$user->phone.'</td>
                        <td>'.$user->password.'</td>
                    </tr>
                ';
            }
            $str .= '</tbody></table>';
            
            $this->renderLayout(view("users.html", array(
                "data" => $str 
            )));
            
        }
    }

?>