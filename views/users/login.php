<?php
    if (isset($data['mensaje']))
        echo "<div style='color: red'>".$data['mensaje']."</div>";
    
?>
<form action='index.php' method='GET'>
    <input type='hidden' name='action' value='procesarFormLogin'>
    Usuario: <input type='text' name='username'><br>
    Contraseña: <input type='password' name='pass'><br>
    <input type='submit'>
</form>