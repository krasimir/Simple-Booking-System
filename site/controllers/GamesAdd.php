<?php
    
    class GamesAdd extends Controller {
        public function run() {
        
            $date = isset($_POST["date"]) ? $_POST["date"] : "";
            $place = isset($_POST["place"]) ? $_POST["place"] : "";
            $time = isset($_POST["time"]) ? $_POST["time"] : "";
            $playground = isset($_POST["playground"]) ? $_POST["playground"] : "";
            $maxPlayers = isset($_POST["maxPlayers"]) ? $_POST["maxPlayers"] : "";
            $ticket = isset($_POST["ticket"]) ? $_POST["ticket"] : "";
        
            if($date != "" && $place != "" && $time != "" && $playground != "") {
                $this->query("INSERT INTO futbol_games (date, time, place, playground, maxPlayers, ticket) VALUES ('".$date."', '".$time."', '".$place."', '".$playground."', '".$maxPlayers."', '".$ticket."')");
                $maxId = $this->query("SELECT max(id) as id FROM futbol_games");
                $newlyAddedRecord = $maxId->result[0]->id;
                $this->renderLayout(view("gamesaddsuccess.html", array(
                    "date" => $date,
                    "place" => $place,
                    "time" => $time,
                    "playground" => $playground,
                    "maxPlayers" => $maxPlayers,
                    "linkToTheGame" => $this->siteURL."/games/".$newlyAddedRecord,
                    "ticket" => $ticket
                )));
            } else {
                $this->renderLayout(view("gamesadd.html", array(
                    "date" => $date,
                    "place" => $place,
                    "time" => $time,
                    "playground" => $playground,
                    "maxPlayers" => $maxPlayers,
                    "ticket" => $ticket
                )));
            }
        
            
        }
    }

?>