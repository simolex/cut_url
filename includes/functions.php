<?php

require_once "config.php";

function get_url($page = "")
{
   return HOST . "/$page";
}

function db()
{
   try {
      return new PDO(
         "mysql:host=" . DB_HOST . "; dbname=" . DB_NAME . "; charset=utf8",
         DB_USER,
         DB_PASS,
         [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
         ]
      );
   } catch (PDOException $e) {
      die($e->getMessage());
   }
}

function db_query($sql = "", $exec = false)
{
   if (empty($sql)) return false;

   if ($exec) {
      return db()->exec($sql);
   }

   return db()->query($sql);
}

function getUserCount()
{
   return db_query("SELECT count(*) FROM `user`;")->fetchColumn();
}

function getViewsCount()
{
   return db_query("SELECT sum(views) FROM `links`;")->fetchColumn();
}

function getLinksCount()
{
   return db_query("SELECT count(*) FROM `links`;")->fetchColumn();
}

function getLinkInfo_ByShortLink($url)
{
   if (empty($url)) return [];

   return db_query("SELECT * FROM `links` WHERE `short_link`= '$url';")->fetch();
}

function getUserInfo($login)
{
   if (empty($login)) return [];

   return db_query("SELECT * FROM `user` WHERE `login`= '$login';")->fetch();
}

function incrementViewLink($url)
{
   db_query("UPDATE `links` SET `views` = `views` + 1 WHERE `short_link` = '$url';", true);
}

function addUser($login, $pass)
{
   $password = password_hash($pass, PASSWORD_DEFAULT);
   return db_query("INSERT INTO `user` (`login`, `pass`) VALUES ('$login', '$password');", true);
}


function registerUser($form)
{
   if (
      empty($form) ||
      !isset($form['login']) ||
      empty($form['login']) ||
      !isset($form['pass']) ||
      !isset($form['pass2'])
   ) {
      return false;
   }
   $user = getUserInfo($form['login']);

   if (!empty($user)) {
      $_SESSION['error'] = "'" . $form['login'] . "' - пользователь уже существует";
      header("Location: register.php");
      die;
   }
   if ($form['pass'] !== $form['pass2']) {
      $_SESSION['error'] = "Пароли не совпадают";
      header("Location: register.php");
      die;
   }
   if (addUser($form['login'], $form['pass'])) {
      $_SESSION['success'] = "'" . $form['login'] . "' - успешно зарегистрирован.";
      header("Location: login.php");
      die;
   }
}

function loginUser($form)
{
   if (
      empty($form) ||
      !isset($form['login']) ||
      empty($form['login']) ||
      !isset($form['pass']) ||
      empty($form['pass'])
   ) {
      $_SESSION['error'] = "Логин и пароль не должны быть пустыми";
      header("Location: login.php");
      die;
   }

   $user = getUserInfo($form['login']);
   if (empty($user)) {
      $_SESSION['error'] = "Логин или пароль не верен.";
      header("Location: login.php");
      die;
   }

   if (password_verify($form['pass'], $user['pass'])) {

      $_SESSION['user'] = $user;
      header("Location: profile.php");
      die;
   } else {
      $_SESSION['error'] = "Логин или пароль не верен.";
      header("Location: login.php");
      die;
   }
}

function getUserLinks($user_id)
{
   if (empty($user_id)) return [];

   return db_query("SELECT * FROM `links` WHERE `user_id`= '$user_id';")->fetchAll();
}

function deleteLink($id)
{
   if (empty($id)) return false;

   return db_query("DELETE FROM `links` WHERE `id`= '$id';", true);
}

function createShortLink($size = 6)
{
   $new_string = str_shuffle(URL_CHARS);
   return substr($new_string, 0, $size);
}

function addLink($user_id, $link)
{
   $short_link = createShortLink();
   return db_query("INSERT INTO `links` (`user_id`, `orginal_link`, `short_link`) VALUES ('$user_id', '$link', '$short_link');", true);
}
