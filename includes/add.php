<?php
require_once   "config.php";
require_once   "functions.php";

if (isset($_POST['link']) && !empty($_POST['link']) && isset($_POST['user_id']) && !empty($_POST['user_id'])) {
   if (addLink($_POST['user_id'], $_POST['link'])) {
      $_SESSION['success'] = "Ссылка успешно добавлена!";
   } else {
      $_SESSION['error'] = "Что-то пошло не так!";
   }
}


header("Location: /profile.php");
die;
