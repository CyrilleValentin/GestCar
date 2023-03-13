<?php
$pdo = require_once 'f_connexion.php';

                    // MIS A JOUR //

if (isset($_GET["action"])) {
    if ($_POST['action'] = "maj") {
        $voiture = [
            'id' => @$_POST['id_car'],
            'nom' => @$_POST["nom"],
            'mod' => @$_POST["modele"],
            'type' => @$_POST["type"],
            'gam' => @$_POST["gamme"]
        ];
        $sql = 'UPDATE car
        SET nom_car = :nom,
        modele_car = :model,
        type_car = :typ,
        gamme_car = :gam WHERE id_car  = :id_car';

        // preparation du statement
        $statement = $pdo->prepare($sql);

        // attachement des parametres
        $statement->bindParam(':id_car', $voiture['id'], PDO::PARAM_INT);
        $statement->bindParam(':nom', $voiture['nom']);
        $statement->bindParam(':model', $voiture['mod']);
        $statement->bindParam(':typ', $voiture['type']);
        $statement->bindParam(':gam', $voiture['gam']);
        $statement->execute(); 
    }
}

else

                            // INSERTION //
if (isset($_POST["nom"]) && isset($_POST["modele"])) {
    $nom = $_POST["nom"];
    $mod = $_POST["modele"];
    $type = $_POST["type"];
    $gam = $_POST["gamme"];
    $sql = 'INSERT INTO car (nom_car,modele_car,type_car,gamme_car) VALUES(:nom,:model,:typ,:gam)';
    $statement = $pdo->prepare($sql);
    $statement->execute([
        ':nom' => $nom,
        ':model' => $mod,
        ':typ' => $type,
        ':gam' => $gam
    ]);
    $id_car = $pdo->lastInsertId();
}


$nom = "";
$id_car = "";
$mod = "";
$action = "";
$type = "";
$gam = "";
$button = "Ajouter";

if (isset($_GET['action'])) {
    $button = "Mis à jour";
    $id_car = $_GET['id_car'];
    $action = $_GET['action'];
    if ($action == "maj") {
        $sql = 'SELECT *
        FROM car 
        WHERE id_car=:id_car';
        $statement = $pdo->prepare($sql);
        //$statement->execute();
        $statement->execute([':id_car' => $id_car]);
        $row = $statement->fetch(PDO::FETCH_OBJ);
        $nom = $row->nom_car;
        $mod = $row->modele_car;
        $type = $row->type_car;
        $gam = $row->gamme_car;
        $id_car = $row->id_car;


    } else {
        $button = "Suppression";
        $id_car = $_GET['id_car'];
        // requete sql de suppression
        $sql = 'DELETE FROM car
    WHERE id_car=:id_car';
        // préparation du statement
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id_car', $id_car, PDO::PARAM_INT);
        if ($statement->execute()) {
            // echo "l'ecole est supprimée avec succès ";
            header('location:/projet/voiture/index.php');
        }
    }
}

// LECTURE //

$sql = 'SELECT id_car, nom_car,modele_car,type_car,gamme_car
FROM car';
$statement = $pdo->prepare($sql);
$statement->execute();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>GestCar</title>
</head>
<script>
    function modifier(id_car) {
        window.location.href = "?action=maj&id_car=" + id_car;

    }
    function supprimer(id_car) {
        window.location.href = "?action=sup&id_car=" + id_car;
    }
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href)
    }
</script>

<body>
    <h1>GestCar</h1>

    <div class="container">
        <form action="" method="post">
            <input type="text" style="display:none;" name="id_car" value="<?php echo $id_car ?>">
            <input type="text" style="display:none;" name="action" id="action" value="<?php echo $action; ?>">
            <div class="input">
                <br>
                <div style="display:flex;">
                    <label for="">Nom</label>
                    <input type="text" required style="margin-left: 1rem;" name="nom" value="<?php echo $nom ?>">
                    <div style="margin-left: 1rem;">
                        <label for="">Modèle</label>
                        <input type="text" name="modele" required value="<?php echo $mod ?>">
                    </div>
                </div>
                <br>

                <br>
                <div style="margin-left: 1rem;">
                    <label for="">Type</label>
                    <select name="type" id="">
                        <option value="Berline" <?php if ($type == "Berline") {
                            echo "selected='Berline'";
                        } ?>>Berline</option>
                        <option value="4x4" <?php if ($type == "4x4") {
                            echo "selected='4x4'";
                        } ?>>4x4</option>
                    </select>

                    <label style="margin-left: 7rem;" for="">Gamme</label>
                    <select name="gamme" id="">
                        <option value="neuf" <?php if ($gam == "neuf") {
                            echo "selected='neuf'";
                        } ?>>Neuf</option>
                        <option value="occasion" <?php if ($gam == "occasion") {
                            echo "selected='occasion'";
                        } ?>>Occasion</option>
                    </select>
                </div>
                <br>
                <button class="button" type="submit">
                    <?php echo $button ?>
                </button>
            </div>
        </form>
    </div>
    <br>
    <br>
    <h1 style="font-size: 3rem;">LISTE DES ENREGISTREMENTS DE VOITURES</h1>
    <br>
    <table style="font-size: 1.5rem;">
        <tr>
            <th style="width: 2rem;">Id</th>
            <th style="width: 8rem;">Nom</th>
            <th style="width: 8rem;">Modèle</th>
            <th style="width: 8rem;">Type</th>
            <th style="width: 8rem;">Gamme</th>
            <th style="width: 15rem;" colspan="2">Actions</th>
        </tr>
        <?php
        while (($row = $statement->fetch(PDO::FETCH_OBJ)) !== false) {
            echo "<tr>";
            echo "<style>
               
                .mod{
                    border:solid 2px #45818e;
                }
                .modif{
                    margin-top:0.5rem;
                    background-color:	#00007f;
                    width:10rem;
                    font-size:1.7rem;
                    margin-left:1rem;
                }
                .suppr{
                    margin-left:1rem;
                    margin-right:1rem;
                    background-color:		#7f0000;
                    width:10rem;
                    font-size:1.7rem;
                    border-radius: 0.5rem;
                }
                </style>";
            echo "<td class='mod'>" . $row->id_car . " </td>";
            echo "<td class='mod'>" . $row->nom_car . " </td>";
            echo "<td class='mod'>" . $row->modele_car . " </td>";
            echo "<td class='mod'>" . $row->type_car . " </td>";
            echo "<td class='mod'>" . $row->gamme_car . " </td>";
            echo "<td><input class='modif' type='button' value='Modifier' onclick='modifier(\"" . $row->id_car . "\")'></td>";
            echo "<td><input class='suppr' type='button' value='Supprimer'  onclick='if(confirm(\"Etes vous sûr de vouloir supprimer?\")){supprimer(\"" . $row->id_car . "\")}'></td>";

            echo "</tr>" . PHP_EOL;
        }
        ?>
    </table>

</body>

</html>