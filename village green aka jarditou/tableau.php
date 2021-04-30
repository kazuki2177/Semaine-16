<!DOCTYPE html>
<html lang="fr">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boostrap  -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Tableau</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

</head>

<body>

    <div class="container">
        <!-- Début de page -->
        
        <?php include "Header/header_tableau.php" ; ?>
    <?php
        // On détermine sur quelle page on se trouve
    if(isset($_GET['page']) && !empty($_GET['page'])){
         $currentPage = (int) strip_tags($_GET['page']);
    }else{
        $currentPage = 1;
    }

    //PAGINATION
    
    // On détermine le nombre total d'articles
    $sql = "SELECT COUNT(*) AS nb_articles FROM produits where pro_stock != 0"; 
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
    $requete = 'SELECT * FROM produits where pro_stock != 0  LIMIT :premier ,:parpage';

     // On prépare la requête
    $result = $db->prepare($requete);


    $result->bindValue(':premier', $premier, PDO::PARAM_INT);
    $result->bindValue(':parpage', $parPage, PDO::PARAM_INT);

    // On exécute
    $result->execute();
   

  
   


    if (!$result) 
    {
    $tableauErreurs = $db->errorInfo();
    echo $tableauErreur[2]; 
    die("Erreur dans la requête");
    }

    if ($result->rowCount() == 0) 
    {
    // Pas d'enregistrement
    die("La table est vide");
    }
     
     ?>

        <div class="row ">

            <div class="col-12 col-sm-12">

                <!-- tableau -->
                <section>
                    <div class="table-responsive">
                        <table class="table-striped table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">photo</th>
                                    <th scope="col">ID </th>
                                    <th scope="col">catégorie</th>
                                    <th scope="col">Libellé</th>
                                    <th scope="col">Prix</th>
                                    <th scope="col">Couleur</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 

/*<td class="d-flex justify-content-center table-warning"><img src="jarditou_photos/<?=$row->pro_id.".".$row->pro_photo;?>" alt="<?=$row->pro_id.".".$row->pro_photo;?>" width="100"></td>*/
            while ($row = $result->fetch(PDO::FETCH_OBJ)){
                echo'<tr>';?>
                    <td class="table-warning"><img src="public/images/<?=$row->pro_id.".".$row->pro_photo;?>" alt="<?=$row->pro_id.".".$row->pro_photo;?>" width="100">.</td>
                    
            <?php
                    echo"<th>".$row->pro_id."</th>";
                    echo"<th class='table-warning'>".$row->pro_ref."</th>";
                    echo '<th><a href="detail.php?pro_id='.$row->pro_id.'" title='.$row->pro_libelle.'>'.$row->pro_libelle.'</a></th>';
                    echo"<th class='table-warning'>".$row->pro_prix."</th>";
                    
                    if ($row->pro_stock == 0)  {echo"<th>"."Rupture de stock"."</th>";} else {echo"<th>".$row->pro_stock."</th>";}
                    
                    echo"<th class='table-warning'>".$row->pro_couleur."</th>";?>
                    <th><a href="ajoutpanier.php?pro_id=<?=$row->pro_id?>">Ajout panier</a></th>

                <?php   
                echo"</tr>";
            }
            ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

        </div>


        <!-- Bas de page -->
        
        <?php include "Footer/footer.php"; ?>

    </div>
    <!-- script boostrap -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>