<?php

namespace App\Services;

use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;
use Google\Cloud\Firestore\Transaction;

use App\Interfaces\UserSessionScopeInterface;
use App\Traits\VerifyUsernameEmailTrait;
use App\Helpers\ManageSessionHelper;
use App\Http\Requests\LogInStoreRequest;
use App\Http\Requests\RegisteredUserStoreRequest;

use Exception;

class UserService implements UserSessionScopeInterface
{
    use VerifyUsernameEmailTrait;

    protected $firestore;
    protected $auth;

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->firestore = $firestore;
        $this->auth = $auth;
    }

    public function register(RegisteredUserStoreRequest $request): ?array
    {
        $authErrors = NULL;
        $validatedData = $request->validated();

        $db = $this->firestore->database();
        $usersColRef = $db->collection('users');

        // check if username and email has already been in use or not
        $authErrors = $this->checkUsernameEmailExist($usersColRef, $validatedData);

        //return error directly if there is any error
        if (isset($authErrors)) {
            return $authErrors;
        }

        $createdUser = $this->auth->createUser($validatedData);

        // create Firestore doc if user creation is successful
        if (isset($createdUser)) {
            $usersDocRef = $usersColRef->document($createdUser->uid);

            //Compile user data
            $data = $this->compileUserData($createdUser, $validatedData['username']);

            $usersDocRef->set($data);

            // update _PlaybookXID
            $counterDocRef = $db->collection('counters')->document('PlaybookXID');

            try {
                $db->runTransaction(function (Transaction $transaction) use ($counterDocRef, $usersDocRef) {
                    $this->updatePlaybookXIDCounter($transaction, $counterDocRef, $usersDocRef);
                });

                $signInResult = $this->auth->signInWithEmailAndPassword($validatedData['email'], $validatedData['password']);

                $this->manageUserSession(
                    self::CREATE_SESSION,
                    $request,
                    false,
                    [
                        "signInResult" => $signInResult,
                        "username" => $validatedData['username'],
                        "roles" => array("user")
                    ]
                );
            } catch (Exception $e) {
                // return error
                $authErrors['toast'] =  $e->getMessage();
            }
        }

        return NULL;
    }

    public function signIn(LogInStoreRequest $request): array
    {
        $signInResult = NULL;
        $authErrors = NULL;
        $validatedData = $request->validated();

        try {
            $signInResult = $this->auth->signInWithEmailAndPassword($validatedData['email'], $validatedData['password']);

            // sign in successful
            $db = $this->firestore->database();
            $usersColRef = $db->collection('users');
            $userDocs = $usersColRef->where('_id', '=', $signInResult->data()['localId'])->limit(1)->documents();
            $matchedUserDoc = $userDocs?->rows()[0]?->data() ?? NULL;

            $this->manageUserSession(
                self::CREATE_SESSION,
                $request,
                true,
                [
                    "signInResult" => $signInResult,
                    "username" => $matchedUserDoc['username'],
                    "roles" => $matchedUserDoc['roles']
                ]
            );
        } catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
            switch ($e->getMessage()) {
                case 'INVALID_PASSWORD':
                    $authErrors['password'] = 'Invalid password';
                    break;

                case 'EMAIL_NOT_FOUND':
                    $authErrors['email'] = 'Invalid email';
                    break;
            }
        }
        return ['signInResult' => $signInResult, 'authErrors' => $authErrors];
    }

    public function manageUserSession(int $operation, object $request, bool $regenerateSession = false, array $data = null): void
    {
        switch ($operation) {
            case self::CREATE_SESSION:
                $requiredData = [
                    'idToken' => $data['signInResult']->idToken(),
                    'uid' => $data['signInResult']->data()['localId'],
                    'username' => $data['username'],
                    'role' => $data['roles']
                ];
                ManageSessionHelper::createSession($request, $requiredData);
                break;

            case self::DESTROY_SESSION:
                $requiredData = ['idToken', 'uid', 'username', 'role'];
                ManageSessionHelper::destroySession($request, $requiredData);
                break;
        }
        if ($regenerateSession) {
            // prevent session fixation attacks
            $request->session()->regenerate();
        }
    }

    private function updatePlaybookXIDCounter(object $transaction, object $counterDocRef, object $usersDocRef): void
    {
        $snapshot = $transaction->snapshot($counterDocRef);

        if ($snapshot->exists()) {
            $transaction->update($counterDocRef, [
                ['path' => 'counterValue', 'value' => $snapshot['counterValue'] + 1]
            ]);
        } else {
            $transaction->set($counterDocRef, [
                'counterValue' => 1
            ]);
        }
        $transaction->update($usersDocRef, [
            ['path' => '_PlaybookXID', 'value' => $snapshot['counterValue'] + 1]
        ]);
    }

    private function compileUserData(object $createdUser, ?string $username): array
    {
        return [
            '_PlaybookXID' => NULL,
            '_id' => $createdUser->uid,
            'uid' => $createdUser->uid,
            'roles' => array('user'),
            'username' => $username,
            'shortenUsername' => strtolower($username),
            'displayName' => $createdUser->displayName,
            'email' => $createdUser->email,
            'phoneNumber' => $createdUser->phoneNumber,
            'dateOfBirth' => NULL,
            'bio' => NULL,
            'gender' => NULL,
            'fullName' => NULL,
            'icNumber' => NULL,
            'studentId' => NULL,
            'address' => NULL,
            'poscode' => NULL,
            'city' => NULL,
            'state' => NULL,
            'country' => NULL,
            'profileImage' => NULL,
            'tokens' => NULL,
            'coverImage' => 'https://firebasestorage.googleapis.com/v0/b/playbookx-project-ce143.appspot.com/o/assets%2Fimages%2Fdefault%2Fcovers%2Fcover2021_1.jpg?alt=media&token=00a19365-4dd6-480d-8adb-5119f7aa5734',
            'createdAt' => $createdUser->metadata->createdAt?->getTimestamp() * 1000 ?? NULL,
            'signedInAt' => $createdUser->metadata->lastLoginAt?->getTimestamp() * 1000 ?? NULL,
            'rank' => NULL,
            'providerId' => $createdUser->providerData[0]->providerId,
            'achievements' => (object) [
                'level' => 1,
                'point' => 0,
                'gems' => 0,
            ],
            'tournament' => (object) [
                'matches' => 0,
                'wins' => 0,
                'loses' => 0,
                'ties' => 0,
                'forfeits' => 0,
                'recentMatchResult' => NULL,
                'mostPlayedGame' => NULL,
                'upcomingTournament' => NULL,
            ],
            'communication' => (object) [
                'chatAvailable' => 0,
                'chatUnread' => 0,
                'lobbyChatAvailable' => 0,
                'lobbyChatUnread' => 0,
                'alertAvailable' => 0,
                'alertUnread' => 0,
            ],
            'count' => (object) [
                'posts' => 0,
                'photos' => 0,
                'followers' => 0,
                'following' => 0,
                'teams' => 0,
            ],
            'verified' => False,
        ];
    }
}
