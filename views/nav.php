<hr/>
<nav>
    <a href='index.php'>Home</a>
    <a href='index.php?action=mostrarListaLibros'>Libros</a>
    <a href='index.php?action=mostrarListaAutores'>Autores</a>
    <?php
        if (isset($_SESSION['username'])) {
            echo "<a href='index.php?action=logout'>Logout</a>";
            echo "<span style='color:grey; font-size: 80%'>
                    <i>Está usted logueado como ".$_SESSION['username']."</i>
                  </span>";
        }
        else {
            echo "<a href='index.php?action=mostrarFormLogin'>Login</a>";
        }
    
    ?>
</nav>
<hr/>