<?php

namespace App\Traits;

trait VerifyUsernameEmailTrait
{
    private function verifyExistingRecord($colRef, $field, $input, $errorMessage)
    {
        $matchedDocs = $colRef->where($field, '=', strtolower($input))->limit(1)->documents();
        foreach ($matchedDocs as $doc) {
            if ($doc->exists()) return $errorMessage;
        }
    }

    private function checkUsernameEmailExist($usersColRef, $validatedData)
    {
        $authErrors = NULL;

        $usernameError = $this->verifyExistingRecord($usersColRef, 'shortenUsername', $validatedData['username'], 'This username is unavailable');
        $emailError = $this->verifyExistingRecord($usersColRef, 'email', $validatedData['email'], 'The email address is already in use by another account');

        if ($usernameError) $authErrors['username'] = $usernameError;
        if ($emailError) $authErrors['email'] = $emailError;

        return $authErrors;
    }
}
