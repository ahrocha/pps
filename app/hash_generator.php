<?php

if (isset($argv[1])) {
    $password = $argv[1];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    echo "Senha fornecida: " . $password . "\n";
    echo "Senha Hashed: " . $hashedPassword . "\n";
} else {
    echo "Erro: falta a senha.\n";
    echo "php hash_generator.php sua_senha_super_secreta\n";
}
