<!DOCTYPE html>
<html>
<head>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>Повтори эти гребанные глаголы ленивая скотина!</title>
  <meta charset='UTF-8'>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<!--не нужный коментарий -->
<?php

require 'includes/dbconf.php';

$sent_verb_id = $_POST['verb_id'];
$sent_verb_word = $_POST['verb_word'];

/* если страница была загруженна и юзер отослал данные методом пост, находим этот глагол в таблице*/
if ( isset($sent_verb_id) ) {
  $result_for_check_verb = mysql_query("SELECT * FROM verbs WHERE verb_id='$sent_verb_id';"); 
  $arr_for_check_verb = mysql_fetch_array($result_for_check_verb);
  $check_verb_word = $arr_for_check_verb['verb_word'];
  /* $updated_check_verb_use = $arr_for_check_verb['verb_use'] + 1; */ /* +1 чтобы изменить значение счетчика verb_use*/

  /* если глагол из таблицы равен глаголу от юзера + запускаем лотерею и показываем еще один глагол*/
  if ($check_verb_word == $sent_verb_word) { 
    $result_for_counter = mysql_query("UPDATE verbs SET verb_use='$updated_check_verb_use' WHERE verb_id=$sent_verb_id;");
    show_new_word("<span class='verb-succes alert-message'>все четка!</span><br><br>");
  }
  /* если глагол из таблицы НЕ равен глаголу от юзера, то открываем еге заново!*/
  else {
    $old_verb_translation = $arr_for_check_verb['verb_translation'];
    $old_verb_face = $arr_for_check_verb['verb_face'];

    echo "<div class='verb'>
      <h1>$old_verb_translation</h1>
      <form action='index.php' method='post'>
        <input name='verb_id' value='$sent_verb_id' type='text' hidden>
        $old_verb_face <input name='verb_word' autocomplete='off' placeholder='сюда глагол' required autofocus><br><br>
        <span class='verb-danger alert-message'>НЕПРАВИЛЬНО!</span><br><br>
        <input type='submit' class='verb-button'>
      </form>
    </div>";
  }
}
/* если страница загружается впервые */
else {
  show_new_word("<span class='verb-info alert-message'>переведи глагол блиять!</span><br><br>");
}

/* ФУНКЦИИ */

function show_new_word($comment) {
  /* найти минимальное число из счетчика повторений глагола в базе 'verb_use'*/
  $verbs_min_use = mysql_query("SELECT MIN(verb_use) FROM verbs;"); /* SELECT * FROM verbs where verb_use=(SELECT MIN(verb_use) FROM verbs); */
  $verbs_min_use_arr = mysql_fetch_array($verbs_min_use);
  $verbs_use_min_number = $verbs_min_use_arr['MIN(verb_use)'];

  /* находим минимально повторенные глаголы и и добавляеем их id-шки в массив кторый будет использоваться в "лотерее"*/
  $verbs_lottery_arr = array();
  $verbs_result = mysql_query("SELECT * FROM verbs;");

  while ( $verbs_arr = mysql_fetch_array($verbs_result) ) {
    if ( $verbs_arr['verb_use'] == $verbs_use_min_number ) {
      $verb_id = $verbs_arr['verb_id'];
      array_push($verbs_lottery_arr, $verb_id);
    }
  }

  /* выбираем рендомно один id глагола и находим его в таблице*/
  $verbs_lottery_arr_length = count($verbs_lottery_arr);
  $verb_rand_number = mt_rand(0, $verbs_lottery_arr_length - 1);

  $your_verb_id = $verbs_lottery_arr[$verb_rand_number];
  $result_for_your_verb = mysql_query("SELECT * FROM verbs WHERE verb_id=$your_verb_id;");
  $arr_for_your_verb = mysql_fetch_array($result_for_your_verb);

  /* выводим эти глаголы на страницу */
  $verb_id = $arr_for_your_verb['verb_id'];
  $verb_face = $arr_for_your_verb['verb_face'];
  $verb_word = $arr_for_your_verb['verb_word'];
  
  $verb_translation = $arr_for_your_verb['verb_translation'];
  

  /*echo "<div class='verb'>
    <h1>$verb_translation</h1>
    <form action='index.php' method='post'>
      <input name='verb_id' value='$verb_id' type='text' hidden>
      $verb_face <input name='verb_word' autocomplete='off' placeholder='сюда глагол' required autofocus><br><br> 
      $comment
      <input type='submit' class='verb-button'> 
    </form>
    </div>";*/
  
  /* БЛОК pretext */
  $pretext_min_use = mysql_query("SELECT MIN(pretext_use) FROM pretext;");
  $pretext_min_use_arr = mysql_fetch_array($pretext_min_use);
  $pretext_use_min_number = $pretext_min_use_arr['MIN(pretext_use)'];
  
  $pretext_lottery_arr = array();
  $pretext_result = mysql_query("SELECT * FROM pretext;");

  while ( $arr_pretext = mysql_fetch_array($pretext_result) ) {
    if ( $arr_pretext['pretext_use'] == $pretext_use_min_number ) {
      $pretext_id = $arr_pretext['pretext_id'];
      array_push($pretext_lottery_arr, $pretext_id);
    }
  }

  $pretext_lottery_arr_length = count($pretext_lottery_arr);
  $pretext_rand_number = mt_rand(0, $pretext_lottery_arr_length - 1);
  
  $your_pretext_id = $pretext_lottery_arr[$pretext_rand_number];
  $result_for_your_pretext =  mysql_query("SELECT * FROM pretext WHERE pretext_id=$your_pretext_id;");
  $arr_for_your_pretext = mysql_fetch_array($result_for_your_pretext);

  $pretext_id = $arr_for_your_pretext['pretext_id'];
  $pretext_translation = $arr_for_your_pretext['pretext_translation'];
  $pretext_word = $arr_for_your_pretext['pretext_word'];

  /* Вывод на страницу */ 
  echo "<div class='verb'>

    <h1>$verb_translation $pretext_translation</h1>
    <form action='index.php' method='post'>
      <input name='verb_id' value='$verb_id' type='text' hidden>
      <input name='pretext_id' value='$pretext_id' type='text' hidden>
      $verb_face <input name='verb_word' pattern='^[$verb_word]+$' autocomplete='off' placeholder='сюда глагол' required autofocus>
      <input name='pretext_word' pattern='^[$pretext_word]+$' autocomplete='off' placeholder='сюда предлог' required autofocus><br><br> 
      <input type='submit' class='verb-button'> 
    </form>
    </div>";
}

?>



</body>
</html>
