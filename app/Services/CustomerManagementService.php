<?php
// app/Services/CustomerManagementService.php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\AuditLogRepository;
use PDO;
use Exception;

class CustomerManagementService
{
    private PDO $pdo;
    private UserRepository $userRepository;
    private AuditLogRepository $logRepository;

    public function __construct(PDO $pdo, UserRepository $userRepository, AuditLogRepository $logRepository)
    {
        $this->pdo = $pdo;
        $this->userRepository = $userRepository;
        $this->logRepository = $logRepository;
    }

    /**
     * Aktualisiert die Daten eines Kunden und loggt die Änderungen.
     * Kapselt die gesamte Geschäftslogik.
     *
     * @param integer $editorUserId ID des bearbeitenden Mitarbeiters.
     * @param string $editorRole Rolle des bearbeitenden Mitarbeiters.
     * @param integer $targetUserId ID des zu bearbeitenden Kunden.
     * @param array $formData Die rohen Formulardaten aus $_POST.
     * @return void
     * @throws Exception
     */
    public function updateCustomer(int $editorUserId, string $editorRole, int $targetUserId, array $formData): void
    {
        $this->pdo->beginTransaction();
        try {
            $oldData = $this->userRepository->find($targetUserId);
            if (!$oldData) {
                throw new Exception("Der zu bearbeitende Benutzer wurde nicht gefunden.");
            }

            $newPassword = $formData['password'] ?? null;
            $newUserData = [
                'username' => trim($formData['username']),
                'email' => trim($formData['email']),
                'role' => trim($formData['role']),
                'birthday' => !empty($formData['birthday']) ? trim($formData['birthday']) : null,
            ];
            $newCustomerData = [
                'Vorname' => trim($formData['vorname']), 'Nachname' => trim($formData['nachname']),
                'Strasse' => trim($formData['strasse']), 'Hausnummer' => trim($formData['hausnummer']),
                'PLZ' => trim($formData['plz']), 'Ort' => trim($formData['ort']),
                'Telefon' => trim($formData['telefon']),
            ];

            // --- Validierung ---
            if (!$this->userRepository->isRolePromotionAllowed($editorRole, $newUserData['role'])) {
                throw new Exception("Sie haben keine Berechtigung, die Rolle '" . htmlspecialchars($newUserData['role']) . "' zuzuweisen.");
            }
            if (!empty($newPassword) && strlen($newPassword) < 8) {
                throw new Exception("Das Passwort muss mindestens 8 Zeichen lang sein.");
            }
            if ($this->userRepository->exists($newUserData['username'], $newUserData['email'], $targetUserId)) {
                throw new Exception("Benutzername oder E-Mail-Adresse ist bereits an einen anderen Benutzer vergeben.");
            }

            // --- Datenbank-Operationen ---
            $this->userRepository->update($targetUserId, $newUserData, $newPassword);
            $this->userRepository->updateCustomerDetails($targetUserId, $newCustomerData);

            // --- Logging ---
            $allNewData = array_merge($newUserData, $newCustomerData);
            foreach ($allNewData as $field => $newValue) {
                $dbField = str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
                if (isset($oldData[$dbField]) && $oldData[$dbField] != $newValue) {
                    $this->logRepository->log($editorUserId, 'KUNDENDATEN_GEAENDERT', [
                        'BetroffenerUserId' => $targetUserId, 'Feld' => $dbField,
                        'AlterWert' => $oldData[$dbField] ?? 'N/A', 'NeuerWert' => $newValue ?? 'N/A'
                    ]);
                }
            }
            if (!empty($newPassword)) {
                $this->logRepository->log($editorUserId, 'PASSWORT_GEAENDERT', ['BetroffenerUserId' => $targetUserId]);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // Wirft die Exception weiter, damit der Controller sie fangen kann.
            throw $e;
        }
    }
}