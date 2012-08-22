<?php

    class Games extends Controller {

        protected function run() {
            $this->renderLayout($this->getVailableGames());
        }

        private function getVailableGames() {
            $games = $this->query("SELECT * FROM futbol_games ORDER BY id ASC");
            $games = $games->result;
            $numOfGames = count($games);
            $list = '';
            $currentTime = time();
            for($i=0; $i<$numOfGames; $i++) {
                $date = explode("-", $games[$i]->date);
                $gameTime = mktime(0, 0, 0, $date[1], $date[0]+1, $date[2]);
                $booked = $this->getBookedPlayers($games[$i]);
                if($currentTime <= $gameTime) {
                    $list .= view("gamerow.html", array(
                        "linkURL" => $this->siteURL."/games/".$games[$i]->id,
                        "date" => $this->getFormatedDate($games[$i]->date),
                        "time" => $games[$i]->time,
                        "place" => $games[$i]->place,
                        "playground" => $games[$i]->playground,
                        "booked" => $booked,
                        "free" => $games[$i]->maxPlayers - $booked
                    ));
                }
            }
            return view("games.html", array(
                "list" => $list
            ));
        }
    }

?>