<?php

    class Money extends Controller {
        protected function run() {
        
            global $money;
            
            $total = 0;
            $str = '<table class="table table-bordered">';
            $str .= '
                <thead>
                <tr>
                    <th>Дата:</th>
                    <th>Сума</th>
                </tr>
                </thead>
                <tbody>
            ';
            foreach($money as $data) {
                $total += $data->money;
                $str .= '
                    <tr>
                        <td>'.$data->date.'</td>
                        <td>'.$data->money.'лв.</td>
                    </tr>
                ';
            }
            $str .= '
                    <tr>
                        <td></td>
                        <td>Общо: '.$total.'лв.</td>
                    </tr>
                ';
            $str .= '</tbody></table>';
            
            $this->renderLayout(view("money.html", array(
                "data" => $str 
            )));
            
        }
    }

?>