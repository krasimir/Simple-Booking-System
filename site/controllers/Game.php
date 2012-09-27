<?php

    class Game extends Controller {
        protected function run() {
        
            $id = isset($this->params["idOfTheGame"]) ? $this->params["idOfTheGame"] : 0;
            $res = $this->query("SELECT * FROM futbol_games WHERE id = '".$id."'");
            $game = $res->result[0];
            $booked = $this->getBookedPlayers($game);
            $free = $game->maxPlayers - $booked;
            
            $name = $this->user->name;
            $phone = $this->user->phone;
            $numOfPlayers = isset($_POST["numOfPlayers"]) ? $_POST["numOfPlayers"] : "";
            $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
            $error = "";
            
            if(isset($_POST["action"]) && $_POST["action"] == "add-player") {
                if($numOfPlayers > $game->maxPlayers - $booked) {
                    $error = "Няма достатъчно свободни места. Оставащите места са ".$free.".";
                } else {
                    $this->query("INSERT INTO futbol_players (gameId, userId, numOfPlayers, name, phone, comment) VALUES (
                        '".$game->id."', 
                        '".$this->user->id."', 
                        '".$numOfPlayers."', 
                        '".$name."',
                        '".$phone."',
                        '".$comment."'
                    )");
                    $maxId = $this->query("SELECT max(id) as id FROM futbol_players");
                    $newlyAddedRecord = $maxId->result[0]->id;
                    setcookie("futbolPlayers", isset($_COOKIE["futbolPlayers"]) ? $_COOKIE["futbolPlayers"].",".$newlyAddedRecord : $newlyAddedRecord, time()+604800); // 1 week
                    $this->sendMessage($game->subscribers, "нов запис (".$game->date.")", $name."<br />".$phone."<br />".$numOfPlayers." играчи<br />".$comment);
                    header("Location: ".$this->siteURL."/games/".$game->id);
                }
            }

            if(isset($_POST["action"]) && $_POST["action"] == "subscribing") {
                $currentSubscribers = explode(",", $game->subscribers);
                $removing = false;
                $game->subscribers = '';
                foreach($currentSubscribers as $subscriber) {
                    if($subscriber == $this->user->email) {
                        $removing = true;
                    } else {
                        if($subscriber != "") {
                            $game->subscribers .= $subscriber.",";
                        }
                    }
                }
                if(!$removing) {
                    $game->subscribers .= $this->user->email.",";
                }
                $this->query("UPDATE futbol_games SET subscribers = '".$game->subscribers."' WHERE id = '".$game->id."'");
            }
            
            $this->renderLayout(view("game.html", array(
                "title" => $this->getFormatedDate($game->date),
                "subtitle" => $game->time." / ".$game->place." / ".$game->playground,
                "error" => $error,
                "players" => $this->getPlayers($game),
                "ticket" => $game->ticket,
                "booked" => $booked == $game->maxPlayers ? '<span class="label label-success">заети места '.$booked.' от '.$game->maxPlayers.'</span>' : '<span class="label label-important">заети места '.$booked.' от '.$game->maxPlayers.'</span>',
                "playerAddForm" => view("playeradd.html", array(
                    "error" => $error,
                    "action" => $this->siteURL."/games/".$game->id,
                    "name" => $name,
                    "phone" => $phone,
                    "numOfPlayers" => $numOfPlayers,
                    "comment" => $comment,
                    "formTitle" => "Добави нов играч/играчи",
                    "name" => $this->user->name,
                    "phone" => $this->user->phone,
                    "email" => $this->user->email,
                    "numOfPlayersCurrent" => ""
                )),
                "subscribing" => view("subscribing.html", array(
                    "action" => $this->siteURL."/games/".$game->id,
                    "buttonText" => $this->areYouSubscriber($game) ? "Вие сте абониран (премахни моя абонамент)" : "Абонирай ме",
                    "currentSubscribers" => $this->getCurrentSubscribers($game)
                ))
            )));
            
        }
        private function getPlayers($game) {
            $str = '<table class="table table-bordered">';
            $str .= '
                <thead>
                <tr>
                  <th>Име (телефон)</th>
                  <th>Коментар:</th>
                  <th>Запазени места:</th>
                  <th>Операции:</th>
                </tr>
              </thead>
              <tbody>
            ';
            $res = $this->query("SELECT * FROM futbol_players WHERE gameId = '".$game->id."' ORDER BY id DESC");
            $totalPlayers = 0;
            if(isset($res->result)) {
                $numOfRecords = count($res->result);
                for($i=0; $i<$numOfRecords; $i++) {
                    $record = $res->result[$i];
                    $numOfPlayers = $record->numOfPlayers;
                    $options = '';
                    $username = $record->name;
                    $phone = $record->phone;
                    if($user = $this->getUser($record->userId)) {
                        $username = $user->name;
                        $phone = $user->phone;
                    }
                    if($this->areOptionsAvailable($record)) {
                        $options = '
                            <a href="'.$this->siteURL.'/games/delete-player/'.$record->id.'">изтрии</a> | 
                            <a href="'.$this->siteURL.'/games/edit-player/'.$record->id.'">редактирай</a>
                        ';
                    }
                    $str .= '
                        <tr>
                            <td>'.$username.' ('.$phone.')</td>
                            <td>'.$record->comment.'</td>
                            <td><span class="label label-info">'.$numOfPlayers.'</span></td>
                            <td>'.$options.'</td>
                        </tr>
                    ';
                    $totalPlayers += $numOfPlayers;
                }
            }
            $str .= '
                <tr>
                    <td colspan="2"></td>
                    <td>Запазени '.$totalPlayers.' от '.$game->maxPlayers.'</td>
                </tr>
            ';
            $str .= '</tbody><table>';
            return $str;
        }
        private function areYouSubscriber($game) {
            $currentSubscribers = explode(",", $game->subscribers);
            foreach($currentSubscribers as $subscriber) {
                if($subscriber == $this->user->email) {
                    return true;
                }
            }
            return false;
        }
        private function getCurrentSubscribers($game) {
            $currentSubscribers = explode(",", $game->subscribers);
            $str = '';
            foreach($currentSubscribers as $subscriber) {
                if($subscriber != "") {
                    $str .= $subscriber."<br />";
                }
            }
            return $str;
        }
    }

?>