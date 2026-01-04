<?php

class UserController {

    function Register($name, $email, $password) {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';

        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if email already exists
            $check = $pdo->prepare('SELECT id_user FROM users WHERE email = :email');
            $check->bindParam(':email', $email);
            $check->execute();

            if ($check->rowCount() > 0) {
                return "Email already exists";
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, "user")');
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                return true;
            } else {
                return "Registration failed";
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    function Login($email, $password) {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';

        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['user_id'] = $row['id_user'];
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['user_role'] = $row['role'];
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    function Logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }
}
