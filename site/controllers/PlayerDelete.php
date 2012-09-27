<?php

    class PlayerDelete extends Controller {
        protected function run() {
            $recordId = isset($this->params["recordId"]) ? $this->params["recordId"] : -1;
            $res = $this->query("SELECT * FROM futbol_players WHERE id = '".$recordId."'");
            if($res->result) {
                $game = $this->query("SELECT * FROM futbol_games WHERE id = '".$res->result[0]->gameId."'");
                $record = $res->result[0];
                $gameId = $record->gameId;
                $this->query("DELETE FROM futbol_players WHERE id = '".$recordId."'");
                $this->sendMessage($game->result[0]->subscribers, "изтриване на запис (".$game->result[0]->date.")", $this->user->name."<br />".$this->user->phone);
                header("Location: ".$this->siteURL."/games/".$gameId);
            } else {
                header("Location: ".$this->siteURL);
            }
        }
    }

?>