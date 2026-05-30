<?php

namespace App;

use Exception;

class Auth
{
    private $fileUsers;

    public function __construct($fileUsers)
    {
        $this->fileUsers = $fileUsers;
    }

    public function getAllUsers()
    {
        if (!file_exists($this->fileUsers)) return [];
        return json_decode(file_get_contents($this->fileUsers), true) ?? [];
    }

    // SKPL-F01.1: Registrasi
    public function register($nama, $email, $password, $role = 'pelanggan')
    {
        $users = $this->getAllUsers();

        // Cek duplikasi email
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                throw new Exception("Email sudah terdaftar!");
            }
        }

        // NF01: Keamanan Password (Hash)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $users[] = [
            'nama' => htmlspecialchars($nama),
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ];

        file_put_contents($this->fileUsers, json_encode($users, JSON_PRETTY_PRINT));
        return true;
    }

    // SKPL-F01.2: Login
    public function login($email, $password)
    {
        $users = $this->getAllUsers();

        foreach ($users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                // Return data user tanpa menyertakan password
                unset($user['password']);
                return $user;
            }
        }
        throw new Exception("Email atau password salah.");
    }
}
