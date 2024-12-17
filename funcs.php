<?php
function debug($data)
{
    echo "<pre>" . print_r($data, 1) . "</pre>";
}

function trim_data($data)
{
    $res = !empty($data) ? trim($data) : '';
    return $res;
}

function error_view($login, $pass): bool
{
    if (empty($login) || empty($pass)) {
        $_SESSION['errors'] = "Поля логин/пароль обязательны!";
        return false;
    }
    return true;
}



function registration(): bool
{
    global $pdo;

    $res = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
    $login = trim_data($_POST['login']);
    $pass = trim_data($_POST['pass']);
    $res_error = error_view($login, $pass);
    if ($res_error == true) {
        $res->execute([$login]);

        if ($res->fetchColumn()) {
            $_SESSION['errors'] = 'Данное имя уже используется';
            return false;
        }

        $pass = password_hash($pass, PASSWORD_DEFAULT);

        $res = $pdo->prepare("INSERT INTO users (login, pass) VALUES (?,?)");

        if ($res->execute([$login, $pass])) {
            $_SESSION['success'] = 'Успешная регистрация';
            return true;
        } else {
            $_SESSION['errors'] = 'Ошибка регистрации';
            return false;
        }
    }
    return true;
}

function login(): bool
{
    global $pdo;
    $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
    $pass = !empty($_POST['pass']) ? trim($_POST['pass']) : '';

    if (empty($login) || empty($pass)) {
        $_SESSION['errors'] = 'Поля логин/пароль обязательны';
        return false;
    }

    $res = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $res->execute([$login]);
    if (!$user = $res->fetch()) {
        $_SESSION['errors'] = 'Логин/пароль введены неверно';
        return false;
    }

    if (!password_verify($pass, $user['pass'])) {
        $_SESSION['errors'] = 'Логин/пароль введены неверно';
        return false;
    } else {
        $_SESSION['success'] = 'Вы успешно вошли';
        $_SESSION['user']['name'] = $user['login'];
        $_SESSION['user']['id'] = $user['id'];
        return true;
    }
}

function save_message(): bool
{
    global $pdo;

    $message = !empty($_POST['message']) ? trim($_POST['message']) : '';

    $id = !empty($_POST['id']) ? trim($_POST['id']) : '';


    if (!isset($_SESSION['user']['name'])) {
        $_SESSION['errors'] = 'Необходимо авторизоваться!';
        return false;
    }

    if (empty($message)) {
        $_SESSION['errors'] = 'Введите текст сообщения!';
        return false;
    }

    $res = $pdo->prepare("INSERT INTO messages (name,message) VALUES (?, ?)");
    if ($res->execute([$_SESSION['user']['name'], $message])) {
        $_SESSION['success'] = 'Сообщение добавлено';
        return true;
    } else {
        $_SESSION['errors'] = 'Введите текст сообщения!';
        return false;
    }
}

function get_messages(): array
{
    global $pdo;
    $pdo->query("SET lc_time_names = 'ru_RU'");
    $res = $pdo->query("SELECT id, name, message, DATE_FORMAT(created_at, '%d %M %Y %H:%i:%s') AS date FROM messages");
    return $res->fetchAll();

}

function delete_message($id): bool
{
    global $pdo;
    $res = $pdo->query("DELETE FROM messages WHERE id = '$id'");
    if ($res != 0) {
        $_SESSION['success'] = 'Сообщение удалено успешно';
        return true;
    } else {
        $_SESSION['errors'] = 'Ошибка удаления!';
        return false;
    }
}