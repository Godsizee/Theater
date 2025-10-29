<?php
namespace App\Repositories;

use PDO;
use PDOException;
use Exception;

class UserRepository
{
    private PDO $pdo;
    public const ROLES = ['user', 'kundendienst', 'co-admin', 'admin'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getValidRoles(?string $promoterRole = null): array
    {
        if ($promoterRole === 'co-admin') {
            return ['user', 'kundendienst', 'co-admin'];
        }
        return self::ROLES;
    }

    public function isRolePromotionAllowed(string $promoterRole, string $targetRole): bool
    {
        $allowedRoles = $this->getValidRoles($promoterRole);
        return in_array($targetRole, $allowedRoles, true);
    }
    
    public function searchCustomers(string $searchTerm = ''): array
    {
        $sql = "SELECT u.UserId, u.Username, u.EMail, k.KundeId, k.Vorname, k.Nachname, k.Ort
                FROM User u
                JOIN Kunde k ON u.UserId = k.UserId";

        $params = [];
        if (!empty($searchTerm)) {
            $sql .= " WHERE u.Username LIKE :username_search
                        OR u.EMail LIKE :email_search
                        OR k.Vorname LIKE :vorname_search
                        OR k.Nachname LIKE :nachname_search
                        OR k.KundeId = :kunde_id_search";
            
            $likeTerm = '%' . $searchTerm . '%';
            $params[':username_search'] = $likeTerm;
            $params[':email_search'] = $likeTerm;
            $params[':vorname_search'] = $likeTerm;
            $params[':nachname_search'] = $likeTerm;
            $params[':kunde_id_search'] = $searchTerm;
        }
        $sql .= " ORDER BY k.Nachname, k.Vorname";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function register(array $data, bool $isAdminCreation = false): int
    {
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception("Benutzername, E-Mail und Passwort sind Pflichtfelder.");
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Bitte eine gültige E-Mail-Adresse eingeben.");
        }
        if (strlen($data['password']) < 8) {
            throw new Exception("Das Passwort muss mindestens 8 Zeichen lang sein.");
        }
        if ($this->exists($data['username'], $data['email'])) {
            throw new Exception("Benutzername oder E-Mail ist bereits vergeben.");
        }
        
        $this->pdo->beginTransaction();
        try {
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $role = 'user';
            if ($isAdminCreation && isset($data['role']) && in_array($data['role'], self::ROLES)) {
                $role = $data['role'];
            }

            $stmt_user = $this->pdo->prepare(
                "INSERT INTO User (Username, EMail, Password, Birthday, Rolle) VALUES (:username, :email, :password, :birthday, :role)"
            );
            $stmt_user->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $hashed_password,
                ':birthday' => $data['birthday'] ?? null,
                ':role' => $role
            ]);
            
            $new_user_id = (int)$this->pdo->lastInsertId();
            
            $stmt_kunde = $this->pdo->prepare("INSERT INTO Kunde (UserId, Vorname, Nachname, Strasse, PLZ, Ort, Land) VALUES (:user_id, :vorname, :nachname, :strasse, :plz, :ort, :land)");
            $stmt_kunde->execute([
                ':user_id' => $new_user_id,
                ':vorname' => $data['vorname'] ?? null,
                ':nachname' => $data['nachname'] ?? null,
                ':strasse' => $data['strasse'] ?? null,
                ':plz' => $data['plz'] ?? null,
                ':ort' => $data['ort'] ?? null,
                ':land' => $data['land'] ?? null
            ]);
            
            $this->pdo->commit();
            return $new_user_id;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Fehler bei der Registrierung: " . $e->getMessage());
            throw new Exception("Bei der Registrierung ist ein Datenbankfehler aufgetreten.");
        }
    }

    public function find(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT u.UserId, u.Username, u.EMail, u.Rolle, u.Birthday, k.KundeId, k.Vorname, k.Nachname, k.Strasse, k.Hausnummer, k.PLZ, k.Ort, k.Telefon, k.VornameChanged, k.BirthdayChanged FROM User u LEFT JOIN Kunde k ON u.UserId = k.UserId WHERE u.UserId = :id");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByUsernameOrEmail(string $identifier): ?array
    {
        $stmt = $this->pdo->prepare("SELECT UserId, Username, Password, Rolle FROM User WHERE Username = :username OR EMail = :email");
        $stmt->execute([':username' => $identifier, ':email' => $identifier]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function verifyPassword(int $userId, string $password): bool
    {
        $stmt = $this->pdo->prepare("SELECT Password FROM User WHERE UserId = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $hashedPassword = $stmt->fetchColumn();
        return $hashedPassword && password_verify($password, $hashedPassword);
    }

    public function getAll(string $sortColumn = 'Username', string $sortOrder = 'ASC'): array
    {
        $allowed_columns = ['UserId', 'Username', 'EMail', 'Rolle', 'Birthday'];
        if (!in_array($sortColumn, $allowed_columns)) {
            $sortColumn = 'Username';
        }
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT UserId, Username, EMail, Rolle, Birthday FROM User ORDER BY $sortColumn $sortOrder";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserProfile(int $userId, array $data): bool
    {
        $allowedUserFields = ['EMail', 'Birthday', 'Password'];
        $allowedKundeFields = ['Vorname', 'Nachname', 'Strasse', 'Hausnummer', 'PLZ', 'Ort', 'Telefon', 'VornameChanged', 'BirthdayChanged'];
        $userUpdates = [];
        $userParams = [':user_id' => $userId];
        $kundeUpdates = [];
        $kundeParams = [':user_id' => $userId];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedUserFields)) {
                $userUpdates[] = "$key = :$key";
                if ($key === 'Password') {
                    $userParams[":$key"] = password_hash($value, PASSWORD_DEFAULT);
                } else {
                    $userParams[":$key"] = $value;
                }
            }
            if (in_array($key, $allowedKundeFields)) {
                $dbField = ucfirst($key);
                $kundeUpdates[] = "$dbField = :$key";
                $kundeParams[":$key"] = $value;
            }
        }
        try {
            $this->pdo->beginTransaction();
            if (!empty($userUpdates)) {
                $sqlUser = "UPDATE User SET " . implode(', ', $userUpdates) . " WHERE UserId = :user_id";
                $this->pdo->prepare($sqlUser)->execute($userParams);
            }
            if (!empty($kundeUpdates)) {
                $sqlKunde = "UPDATE Kunde SET " . implode(', ', $kundeUpdates) . " WHERE UserId = :user_id";
                $this->pdo->prepare($sqlKunde)->execute($kundeParams);
            }
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Profil-Update-Fehler für User {$userId}: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $userId, array $data, ?string $newPassword = null): bool
    {
        $sql = "UPDATE User SET Username = :username, EMail = :email, Rolle = :role, Birthday = :birthday";
        $params = [
            ':username' => $data['username'], 
            ':email' => $data['email'], 
            ':role' => $data['role'], 
            ':birthday' => $data['birthday'], 
            ':id' => $userId
        ];
        if (!empty($newPassword)) {
            $sql .= ", Password = :password";
            $params[':password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        $sql .= " WHERE UserId = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateCustomerDetails(int $userId, array $customerData): bool
    {
        $allowedFields = ['Vorname', 'Nachname', 'Strasse', 'Hausnummer', 'PLZ', 'Ort', 'Telefon'];
        
        $updateParts = [];
        $params = [':user_id' => $userId];

        foreach ($customerData as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateParts[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        if (empty($updateParts)) {
            return true;
        }

        $sql = "UPDATE Kunde SET " . implode(', ', $updateParts) . " WHERE UserId = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM User WHERE UserId = :id");
        return $stmt->execute([':id' => $userId]);
    }

    public function exists(string $username, string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT UserId FROM User WHERE (Username = :username OR EMail = :email)";
        $params = [':username' => $username, ':email' => $email];
        if ($excludeId !== null) {
            $sql .= " AND UserId != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    public function isUsernameAvailable(string $username): bool
    {
        $stmt = $this->pdo->prepare("SELECT UserId FROM User WHERE Username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch() === false;
    }

    public function isEmailAvailable(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT UserId FROM User WHERE EMail = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() === false;
    }

    public function getSupportAndAdminUsers(): array
    {
        $stmt = $this->pdo->query("SELECT UserId, Username FROM User WHERE Rolle IN ('admin', 'co-admin', 'kundendienst') ORDER BY Username");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}