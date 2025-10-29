<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\LoginAttemptRepository;
use Exception;

class AuthenticationService
{
    private UserRepository $userRepository;
    private LoginAttemptRepository $loginAttemptRepository;

    public function __construct(UserRepository $userRepository, LoginAttemptRepository $loginAttemptRepository)
    {
        $this->userRepository = $userRepository;
        $this->loginAttemptRepository = $loginAttemptRepository;
    }

    /**
     * Führt den kompletten Login-Prozess für einen Benutzer durch.
     * @param string $identifier Benutzername oder E-Mail
     * @param string $password
     * @param string $ipAddress
     * @return array|null Benutzerdaten bei Erfolg, sonst null.
     * @throws Exception Wenn der Login blockiert ist.
     */
    public function login(string $identifier, string $password, string $ipAddress): ?array
    {
        if (!$this->loginAttemptRepository->isAllowed($ipAddress, $identifier)) {
            throw new Exception("Zu viele fehlgeschlagene Login-Versuche. Bitte warten Sie 15 Minuten.");
        }

        $user = $this->userRepository->findByUsernameOrEmail($identifier);

        if ($user && password_verify($password, $user['Password'])) {
            $this->loginAttemptRepository->clearAttempts($ipAddress, $identifier);
            return [
                'UserId' => $user['UserId'],
                'Username' => $user['Username'],
                'Rolle' => $user['Rolle']
            ];
        } else {
            $this->loginAttemptRepository->recordFailure($ipAddress, $identifier);
            return null;
        }
    }
}