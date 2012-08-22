<?php

    class PlayerEdit extends Controller {
        protected function run() {
            $recordId = isset($this->params["recordId"]) ? $this->params["recordId"] : -1;
            $res = $this->query("SELECT * FROM futbol_players WHERE id = '".$recordId."'");
            if($res->result) {
                
                $record = $res->result[0];
                $game = $this->getGame($record->gameId);

                if(!$this->areOptionsAvailable($record)) {
                    header("Location: ".$this->siteURL."/games/".$game->id);
                }
                
                $booked = $this->getBookedPlayers($game);
                $free = $game->maxPlayers - $booked;
                
                $numOfPlayers = isset($_POST["numOfPlayers"]) ? $_POST["numOfPlayers"] : $record->numOfPlayers;
                $comment = isset($_POST["comment"]) ? $_POST["comment"] : $record->comment;
                $recordId = isset($_POST["recordId"]) ? $_POST["recordId"] : "";
                $error = "";
                
                if(isset($_POST["action"]) && $_POST["action"] == "add-player") {
                    if($numOfPlayers > $free + $record->numOfPlayers) {
                        $error = "Няма достатъчно свободни места. Оставащите места са ".$free.".";
                    } else {
                        $this->query("UPDATE futbol_players SET
                            numOfPlayers = '".$numOfPlayers."', 
                            comment = '".$comment."'
                            WHERE id = '".$recordId."'
                        ");                        
                        header("Location: ".$this->siteURL."/games/".$game->id);
                    }
                }
                
                $this->renderLayout(view("playeradd.html", array(
                    "error" => $error,
                    "action" => $this->siteURL."/games/edit-player/".$record->id,
                    "numOfPlayers" => $numOfPlayers,
                    "comment" => $comment,
                    "recordId" => $record->id,
                    "formTitle" => "Добави нов играч/играчи",
                    "numOfPlayersCurrent" => '<option value="'.$record->numOfPlayers.'">'.$record->numOfPlayers.' играчи</option>'
                )));
                
                // header("Location: ".$this->siteURL."/games/".$gameId);
            } else {
                header("Location: ".$this->siteURL);
            }
        }
        private function getGame($id) {
            $res = $this->query("SELECT * FROM futbol_games WHERE id = '".$id."'");
            $game = $res->result[0];
            return $game;
        }
    }

?>