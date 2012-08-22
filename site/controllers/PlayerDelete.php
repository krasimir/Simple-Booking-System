<?php

    class PlayerDelete extends Controller {
        protected function run() {
            $recordId = isset($this->params["recordId"]) ? $this->params["recordId"] : -1;
            $res = $this->query("SELECT * FROM futbol_players WHERE id = '".$recordId."'");
            if($res->result) {
                $record = $res->result[0];
                $gameId = $record->gameId;
                $this->query("DELETE FROM futbol_players WHERE id = '".$recordId."'");
                header("Location: ".$this->siteURL."/games/".$gameId);
            } else {
                header("Location: ".$this->siteURL);
            }
        }
    }

?>