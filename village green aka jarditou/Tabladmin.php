<!DOCTYPE html>
<html lang="fr">
<!--indique la langue dans laquelle la page web est rédigéé aux robots de référencement ou aux logiciels de synthése vocale-->
<!--les balises de la partie head ne sont pas affichées à l'exeption de title-->

<head>
    <!--meta permet de fourni des indications différentes du contenu de la page web -->
    <meta charset="UTF-8">
    <!--permet de spécifier aux navigateurs l'encodage de la page web, il s'agit là de la valeur standard qui évite les pbs d'affichages des caractères spéciaux-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" ,shrink-to-fit=no>
    <title>Tableau administrateur</title>
    <!--on importe Bootstrap via une URL pointant sur un CDN (un serveur externe hébergeant des fichiers) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">


    <?php
    require "connexion_bdd.php"; // Inclusion de notre bibliothèque de fonctions
    $db = connexionBase(); // Appel de la fonction de connexion 
    ?>


</head>

<body>
    <div class="container">
        <!--container global de la page-->

        <?php include 'Header/header_tableau.php';


        // On détermine sur quelle page on se trouve
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            $currentPage = (int) strip_tags($_GET['page']);
        } else {
            $currentPage = 1;
        }




        //PAGINATION

        // On détermine le nombre total d'articles
        $sql = "SELECT COUNT(*) AS nb_articles FROM produits";
        // On prépare la requête
        $query = $db->prepare($sql);
        // On exécute
        $query->execute();
        // On récupère le nombre d'articles
        $result = $query->fetch();
        // On determine le nombre d'articles totale dans nôtre base produits
        $nbArticles = (int) $result['nb_articles'];
        // On détermine le nombre d'articles par page
        $parPage = 8;
        // On calcule le nombre de pages total
        $pages = ceil($nbArticles / $parPage);
        // Calcul du 1er article de la page
        $premier = ($currentPage * $parPage) - $parPage;

        //Récupération de 10  articles
        $requete = 'SELECT * FROM produits  LIMIT :premier ,:parpage';

        // On prépare la requête
        $result = $db->prepare($requete);


        $result->bindValue(':premier', $premier, PDO::PARAM_INT);
        $result->bindValue(':parpage', $parPage, PDO::PARAM_INT);

        // On exécute
        $result->execute();






        if (!$result) {
            $tableauErreurs = $db->errorInfo();
            echo $tableauErreur[2];
            die("Erreur dans la requête");
        }

        if ($result->rowCount() == 0) {
            // Pas d'enregistrement
            die("La table est vide");
        }

        ?>

        <br>
        <a href="produits_ajout.php"><button>Créer un nouvel enregistrement</button></a>

        <p id="tableau"></p>
        <div class="table-responsive">
            <!--tableau responsive-->
            <table class="table table-hover table-bordered w-100 w-sm-50">
                <!--tableau avec separation des ligne et contour-->
                <thead>
                    <tr class="table-active">
                        <th>Photo</th>
                        <th>ID</th>
                        <th>Référence</th>
                        <th>Libellé</th>
                        <th>Prix</th>
                        <th>stock</th>
                        <th>Couleur</th>
                        <th>Ajout</th>
                        <th>Modif</th>
                        <th>bloqué</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    /*<td class="d-flex justify-content-center table-warning"><img src="jarditou_photos/<?=$row->pro_id.".".$row->pro_photo;?>" alt="<?=$row->pro_id.".".$row->pro_photo;?>" width="100"></td>*/
                    while ($row = $result->fetch(PDO::FETCH_OBJ)) {

                        echo '<tr>'; ?>
                        <td class="table-warning"><img src="Src/Img/img_outil<?= $row->pro_id . "." . $row->pro_photo; ?>" alt="<?= $row->pro_id . "." . $row->pro_photo; ?>" width="100">.</td>

                        <?php
                        echo "<th>" . $row->pro_id . "</th>";
                        echo "<th class='table-warning'>" . $row->pro_ref . "</th>";
                        echo '<th><a href="detailadmin.php?pro_id=' . $row->pro_id . '" title=' . $row->pro_libelle . '>' . $row->pro_libelle . '</a></th>';
                        echo "<th class='table-warning'>" . $row->pro_prix . "</th>";

                        if ($row->pro_stock == 0) {
                            echo "<th>" . "Rupture de stock" . "</th>";
                        } else {
                            echo "<th>" . $row->pro_stock . "</th>";
                        }

                        echo "<th class='table-warning'>" . $row->pro_couleur . "</th>";
                        echo "<th>" . $row->pro_d_ajout . "</th>";
                        echo "<th class='table-warning'>" . $row->pro_d_modif . "</th>";

                        if ($row->pro_bloque == 1) {   ?>
                            <th>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalScrollable">Bloqué</button>
                                <div class="modal" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalCenteredLabel">Produit Bloqué</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">Nous vous tiendront informé sur les futurs disponibilités du produit</div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                    <?php  }
                        echo "</tr>";
                    }

                    ?>

                </tbody>
            </table>
        </div>


        <!-- nav de pagination -->
        <nav>
            <ul class="pagination d-flex justify-content-center">
                <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
                <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                    <!--disabled pour desactivé le lien en page 1-->
                    <a href="tableauadmin.php?page=<?= $currentPage - 1 ?>" class="page-link">Précédente</a>
                </li>
                <?php for ($page = 1; $page <= $pages; $page++) : ?>
                    <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                    <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                        <a href="tableauadmin.php?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                    </li>
                <?php endfor ?>
                <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
                <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                    <!--disabled pour desactivé le lien en page maximum-->
                    <a href="tableauadmin.php?page=<?= $currentPage + 1 ?>" class="page-link">Suivante</a>
                </li>
            </ul>
        </nav>

        <?php include 'Footer/footer.php'; ?>
    </div>

    <!--fichiers Javascript nécessaires à Bootstrap-->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>