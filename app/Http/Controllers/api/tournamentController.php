<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Respositories\TournamentRespository;

class tournamentController extends Controller
{
    private $database;
    private $TournamentRespository; 

	public function __construct()
	{
	$this->TournamentRespository = new TournamentRespository(); 
	isset(request()->tournamentStatedId)?$this->TournamentRespository->setTournamentStateId(request()->tournamentStatedId):null;
	isset(request()->tournamentId)?$this->TournamentRespository->setTournamentId(request()->tournamentId):null;
	isset(request()->gameId)?$this->TournamentRespository->setGameID(request()->gameId):null;
	isset(request()->userId)?$this->TournamentRespository->setUserId(request()->userId):null;
	isset(request()->role)?$this->TournamentRespository->setRole(request()->role):null;
	isset(request()->limit)?$this->TournamentRespository->setLimit(request()->limit):$this->TournamentRespository->setLimit();

	}

	public function getTournaments(Request $request) 
	{
		
   		return json_encode($this->TournamentRespository->getTournaments(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	public function getIntro(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)->getTournamentIntro(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	public function getRule(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)->getTournamentRule(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	public function getParticipants(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)->getParticipants(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	public function getGroups(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)-> getGroups(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	public function getLikes(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)-> getLikes(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	public function getComments(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)-> getComments(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	public function getGroupById(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)->setGroupId($request->groupId)->getGroupById(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	public function getMatchById(Request $request) 
	{

		return json_encode($this->TournamentRespository->setTournamentId($request->tournamentId)->setMatchId($request->matchId)->getMatchById(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
}
