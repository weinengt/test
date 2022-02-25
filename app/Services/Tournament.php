<?php

namespace App\Services;

use Illuminate\Http\Request;
use \App\Respositories\TournamentRespository;
use \App\Services\FirestoreService;


class Tournament
{
	private $milliSecondsTimeStamp;
	private $filterTournamentState = false;
	private $filterMatches =false;
	private $database;
	private $startAfter;

	public function __construct()
    {

    $this->milliSecondsTimeStamp = round(microtime(true) * 1000);
    $this->database = FirestoreService::connectCollection()->collection('tournamentsV1');
    }

    public function filterTournamentState(String $tournamentStateId, Array $tournamentData):array|null
    {
    
        if($tournamentStateId==1)
            return  $tournamentData;
        else if($tournamentStateId==2)
            return  $tournamentData['schedule']['start']<= $this->milliSecondsTimeStamp ? $tournamentData :null;
        else if($tournamentStateId==3)
            return  $tournamentData;
        
    }
    public function filterMatches(String $groupId, String $tournamentId, String $query):array|null
    {

		$query= $query.'->document("'.$groupId.'")->collection("matches")->where("id", "==","'.TournamentRespository::$matchId.'")';				
	 	eval("$query;");
		$documents=$collection->documents();
		foreach ($documents as $document)
		{
			if ($document->exists())
			{
				return $document->data();
			}
		}
	return null;
    }

    public function queryGenerator(String $tournamentStateId=null,String $tournamentId=null, Array|Object $startAfter=null,String $userId=null,String $role=null, String $gameId=null, String $limit, bool $tournamentIntro=false,bool $tournamentRule=false,bool $tournamentPaticipant=false, bool $tournamentGroup=false, bool $tournamentLikes=false,$tournamentComments=false,$groupId=null,$matchId=null,$getMatchbyId=false): String
    {
    		$public=true;
    		if($tournamentIntro==true)
    		{
    			$public=false;
    			return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("intro")';
    		}
    		else if($tournamentRule==true)
    		{
    			$public=false;
    			return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("rule")';
    		}
    		else if($tournamentPaticipant==true)
    		{
    			$public=false;
    			return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("participants")';
    		}
    		else if($tournamentGroup==true)
    		{
    			$public=false;
    			return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("groups")';
    		}
    		else if($tournamentLikes==true)
    		{
    			$public=false;
    			return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("likes")';
    		}
    		else if($tournamentComments==true)
    		{
    			$public=false;
    			return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("comments")';
    		}
    		else if(!empty($tournamentId)&&!empty($groupId))
    		{
    			$public=false;
    			return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("groups")->where("id", "==","'.$groupId.'")';	
    		}
    		else if($getMatchbyId==true)
    		{
    			if(empty($tournamentId))
    			{
    				TournamentRespository::$message=6;
    				return false;	
    			}
    			else if(empty($matchId))
    			{
    				TournamentRespository::$message=7;
    				return false;	
    			}
    			else
    			{
    				$public=false;
    				$this->filterMatches =true;
    				TournamentRespository::$matchId=$matchId;
    				return $query='$collection = $this->database->document("'.$tournamentId.'")->collection("groups")';	
    			}
    		}
    		else
				!empty($tournamentStateId)||!empty($startAfter)||!empty($limit)?$query='$collection = $this->database':$query="";

    		if (!empty($tournamentStateId))
    		{
    			if(is_int((int)$tournamentStateId)&&(int)$tournamentStateId>=1&&(int)$tournamentStateId<=3)
    			{
    			$this->filterTournamentState=true;
    			$tournamentStateId==1? $query=$query."->where('schedule.start', '>',".$this->milliSecondsTimeStamp.")->orderBy('schedule.start','asc')": null;
    			$tournamentStateId==2? $query=$query."->where('schedule.end', '>=',".$this->milliSecondsTimeStamp.")->orderBy('schedule.end','asc')": null;
    			$tournamentStateId==3? $query=$query."->where('schedule.end', '<',".$this->milliSecondsTimeStamp.")->orderBy('schedule.end','desc')": null;
    			}
    			else
    			{
    			TournamentRespository::$message=3;
    			return false;
    			}
    		}


    		if(!empty($tournamentId))
    		{
    			$query=$query."->where('id', '==','".$tournamentId."')";
    		}

    		if(!empty($gameId))
    		{
    			if(ctype_digit($gameId))
    				$query=$query."->where('game.id', '==',".$gameId.")";
    			else
    			{
    				TournamentRespository::$message=4;
    				return false;	
    			}
    		}

    		if(!empty($userId))
    		{
    			if(empty($role))
    			{
    				TournamentRespository::$message=1;
    				return false;		
    			}
    			else if($role=="organizer")
    			{
    				$public=false;
    				$query=$query."->where('manager.uid', '==','".$userId."')";
    			}
    			else
    			{
    				TournamentRespository::$message=2;
    				return false;		
    			}
    		}

    		if($public==true)
    		{
    			$query=$query.'->where("public", "==", true)';
    		}
    		
    		if(!empty($startAfter))
    		{
    			$this->startAfter=$startAfter;
    			$query=$query.'->startAfter($this->startAfter)';
    		}

    		if(!empty($limit))
    		{
    			if(ctype_digit($limit))
    			$query=$query."->limit(".$limit.")";
    			else
    			{
    				TournamentRespository::$message=5;
    				return false;	
    			}
    		}
    	return $query;
    }

    public function errorHandle(int $messageCode):String
    {
    		switch ($messageCode)
    		{
    				case 1:
    				return "Error, undefinded variable: user role";
    				case 2:
    				return "Error, invalid user role";
    				case 3:
    				return "Error, invalid tournamentStateId";
    				case 4:
    				return "Error, invalid gameId";
    				case 5:
    				return "Error, limit only accept numberic value";
    				case 6:
    				return "Error, undefinded variable: tournamentId";
    				case 7:
    				return "Error, undefinded variable: matchId";
    		}



    }

    public function queryExecute(String $query):array
    {
    	$unsortResult=[];
		eval("$query;");
		$documents=$collection->documents();
		foreach ($documents as $document)
		{
			if ($document->exists())
			{
				TournamentRespository::$startAfter=$document;

				$this->filterTournamentState?$this->filterTournamentState(TournamentRespository::$tournamentStateId,$document->data())!=null?$unsortResult[$document->id()]=$this->filterTournamentState(TournamentRespository::$tournamentStateId,$document->data()):null:null;

				if($this->filterMatches)
				{
					$tournamentId=$document->data();
					$result=$this->filterMatches($document->id(),$tournamentId['tournamentId'],$query);
				    !empty($result)?array_push($unsortResult, $result):null;
					
				}
				!$this->filterTournamentState&&!$this->filterMatches?$unsortResult[$document->id()]=$document->data():null;
				!empty($unsortResult[$document->id()])?ksort($unsortResult[$document->id()]):null;
               
			}
		}
	return $unsortResult;
    }


}