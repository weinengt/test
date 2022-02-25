<?php

namespace App\Respositories;

use Illuminate\Http\Request;
use \App\Services\Tournament;
use \App\Services\FirestoreService;

class TournamentRespository
{
    static  $tournamentStateId="";
    private $tournamentId="";
    private $gameId="";
    private $userId="";
    private $role="";
    private $limit;
    private $query="";
    private $database;
    private $groupId;
    static $matchId;
    private $activeStartAfter = false;
    private $sortedResult=[];
    private $Tournament;
    static $startAfter=[];
    static $message="";

    public function __construct()
    {
    $this->Tournament= new Tournament();
    $this->database = FirestoreService::connectCollection()->collection('tournamentsV1');
    empty($this->limit) ?$this->setlimit():null;

    }

    public function setTournamentStateId(String $tournamentStateId):TournamentRespository
    {
    self::$tournamentStateId = $tournamentStateId;

    return $this;
    }

    public function setTournamentId(String $tournamentId):TournamentRespository
    {
        $this->tournamentId=$tournamentId;
        return $this;
    } 

    public function setGameId(String $gameId):TournamentRespository
    {
     $this->gameId=$gameId;   
    return $this;
    } 

    public function setUserId(String $userId):TournamentRespository
    {
     $this->userId=$userId; 
    return $this;
    } 

     public function setRole(String $role):TournamentRespository
    {
      $this->role=$role; 
    return $this;  
    } 

     public function setLimit(String $limit=null):TournamentRespository
    {
        !empty($limit)?$this->limit=$limit:$this->limit=30;
    return $this;
    } 

     public function activeStartAfter():TournamentRespository
    {
        
        $this->activeStartAfter=true;
    return $this;
    } 
     public function setGroupId(String $groupId=null):TournamentRespository
    {
        $this->groupId=$groupId;
    return $this;
    }
    public function setMatchId(String $matchId=null):TournamentRespository
    {
        self::$matchId=$matchId;
    return $this;
    }
    public function getTournaments():array|String
    {
        $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult= $this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;
    }

     public function getTournamentIntro():array|String
     {
        $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,true );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult=$this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;
     }
     public function getTournamentRule():array|String
     {
         $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,false,true );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult=$this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;

     }

     public function getParticipants():array|String
     {
         $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,false,false,true );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult=$this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;

     }

     public function getGroups():array|String
     {
         $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,false,false,false,true );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult= $this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;

     }
     public function getGroupById():array|String
     {
        $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,false,false,false,false,false,false, $this->groupId );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult=$this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;


     }
     public function getLikes():array|String
     {
         $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,false,false,false,false,true );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult=$this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;

     }

     public function getComments():array|String
     {
         $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,false,false,false,false,false,true );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult=$this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;

     }
     public function getMatchById():array|String
     {
         $this->query = $this->Tournament->queryGenerator( self::$tournamentStateId, $this->tournamentId, $this->activeStartAfter?self::$startAfter:null,$this->userId,$this->role, $this->gameId, $this->limit,false,false,false,false,false,false,null,self::$matchId,true );
        $this->query!=false?$sortedResult= $this->Tournament->queryExecute($this->query):$sortedResult=$this->Tournament-> errorHandle(self::$message);

       return  $sortedResult;

     }
}