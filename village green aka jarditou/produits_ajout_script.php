<?php session_start();
if  (isset ($_SESSION["Log"]))

$nomcat = $_POST['cat_nom'];

$bool = 1; // Pour une bonne redirection 
$_SESSION["champ"] = "ok";
$_SESSION["numerique"] = "ok";
$_SESSION["ref"] = "ok";
$_SESSION["fich"] = "ok";


$champexisteok = $libellemok = $prixok = $stockok  = false;

/*Vérification des champs qui doivent pas être null*/

if ( empty($_POST['pro_ref']) || empty($_POST['pro_libelle']) || empty($_POST['pro_prix']) || empty($_POST['pro_stock']) ) {
    $_SESSION["messchamp"] = "Vôtre modification contient des champs vides obligatoires";
    $bool = 0;
    }
else{
    $_SESSION["messchamp"] = "";
}

/*Vérification des champs qui doivent être numérique*/

if   (!($_POST['pro_prix'] >= 0) || !($_POST['pro_stock'] >= 0) ){
    $_SESSION["messnumeric"] = "Le prix et le stock doivent être des valeurs Numériques positives ou nulle";
    $bool = 0;
    }
else{
    $_SESSION["messnumeric"] = "";
    }


require "connexion_bdd.php"; // Inclusion de notre bibliothèque de fonctions
$db = connexionBase();// Appel de la fonction de connexion

//exclusion de référence

$pro_ref=$_POST['pro_ref'];
$requete1 = "SELECT * FROM produits where pro_ref=\"$pro_ref\""; //concatenantion d'une chaine de caractère
$result1 = $db->prepare($requete1);
$result1->execute();
$nb_ref=$result1->rowCount();
$result1->closeCursor();

if ($nb_ref == 1) { //test pour existante de la référence diffente de la référence du produit 
    $_SESSION["messref"] = "La Référence existe déjà";
    $bool = 0;
    }
else{
    $_SESSION["messref"] = "";
    }




    

//dans ce fichier, nous récupérons les informations nécessaires,
//pour réaliser la requête pour un nouvel enregistrement : INSERT
//récupération des informations passées en POST, nécessaires à la modification

$pro_cat_id=$_POST['cat_nom'];
$pro_ref=$_POST['pro_ref'];
$pro_libelle=$_POST['pro_libelle'];
$pro_description=$_POST['pro_description'];
$pro_prix=$_POST['pro_prix'];
$pro_stock=$_POST['pro_stock'];
$pro_couleur=$_POST['pro_couleur'];
$pro_photo=$_POST['pro_photo'];
$pro_date = date("y-m-d");
$erreur_file=$_FILES["fichier"]["error"];




$aMimeTypes = array("image/gif", "image/jpeg", "image/png");


if ($_FILES["fichier"]["tmp_name"] != ""){
    if ($erreur_file == 0){

        // On extrait le type du fichier via l'extension FILE_INFO 
    
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $_FILES["fichier"]["tmp_name"]);
        finfo_close($finfo);
    

        if (!in_array($mimetype, $aMimeTypes)){ //test pour existante de la référence diffente de la référence du produit que l'on veut modifier
        $_SESSION["messfich"] = "Le fichier n'est pas autorisé 1";
        $bool = 0;
        }
    else{
        $_SESSION["messfich"] = "";
        }
    }
}
else {$_SESSION["messfich"] = "Aucun fichier joint";
        $bool = 0;  }






if( ($bool == 1) ){
 if (isset($_POST["pro_bloque"])){

    $pro_bloque=$_POST['pro_bloque']; 
    $requete = $db->prepare("INSERT INTO produits (pro_cat_id,pro_ref,pro_libelle,pro_description,pro_prix,pro_stock,pro_couleur,pro_photo,pro_d_ajout,pro_bloque) 
                        values(:pro_cat_id,:pro_ref,:pro_libelle,:pro_description,:pro_prix,:pro_stock,:pro_couleur,:pro_photo,:pro_d_ajout,:pro_bloque)");
    $requete->bindValue(':pro_cat_id', $pro_cat_id);
    $requete->bindValue(':pro_ref', $pro_ref);
    $requete->bindValue(':pro_libelle', $pro_libelle);
    $requete->bindValue(':pro_description', $pro_description);
    $requete->bindValue(':pro_prix', $pro_prix);
    $requete->bindValue(':pro_stock', $pro_stock);
    $requete->bindValue(':pro_couleur', $pro_couleur);
    $requete->bindValue(':pro_photo', $pro_photo);
    $requete->bindValue(':pro_d_ajout', $pro_date);
    $requete->bindValue(':pro_bloque', $pro_bloque);
    }
else{
    $requete = $db->prepare("INSERT INTO produits (pro_cat_id,pro_ref,pro_libelle,pro_description,pro_prix,pro_stock,pro_couleur,pro_photo,pro_d_ajout) 
                        values(:pro_cat_id,:pro_ref,:pro_libelle,:pro_description,:pro_prix,:pro_stock,:pro_couleur,:pro_photo,:pro_d_ajout)");
    $requete->bindValue(':pro_cat_id', $pro_cat_id);
    $requete->bindValue(':pro_ref', $pro_ref);
    $requete->bindValue(':pro_libelle', $pro_libelle);
    $requete->bindValue(':pro_description', $pro_description);
    $requete->bindValue(':pro_prix', $pro_prix);
    $requete->bindValue(':pro_stock', $pro_stock);
    $requete->bindValue(':pro_couleur', $pro_couleur);
    $requete->bindValue(':pro_photo', $pro_photo);
    $requete->bindValue(':pro_d_ajout', $pro_date);

    }

$requete->execute();


    if (in_array($mimetype, $aMimeTypes)){
       
        $requete2="SELECT pro_id from produits where pro_id=LAST_INSERT_ID() ";
        $result2 = $db->query($requete2);
        if (!$result2) 
        {
            $tableauErreurs = $db->errorInfo();
            echo $tableauErreur[2]; 
            die("Erreur dans la requête");
        }
        if ($result2->rowCount() == 0) 
        {
           // Pas d'enregistrement
           die("La table est vide");
        }    
       
        $row = $result2->fetch(PDO::FETCH_OBJ);
        
        move_uploaded_file($_FILES["fichier"]["tmp_name"], "public/images/".$row->pro_id.".".$pro_photo);
    }

    //libère la connection au serveur de BDD
    $requete->closeCursor();


     //redirection vers la page index.php
     $_SESSION["champ"] = "";
     $_SESSION["messchamp"]="";
     $_SESSION["numerique"]="";
     $_SESSION["messnumeric"] = "";
     $_SESSION["ref"] = "";
     $_SESSION["messref"] = "";
     $_SESSION["fich"] = "";
     $_SESSION["messfich"] = "";
 
 
     unset($_SESSION["champ"]);
     unset($_SESSION["messchamp"]);
     unset($_SESSION["numerique"]);
     unset($_SESSION["messnumeric"]);
     unset($_SESSION["ref"]);
     unset($_SESSION["messref"]);
     unset($_SESSION["fich"]);
     unset($_SESSION["messfich"]);

    //redirection vers la page index.php
    header("Location: tableauadmin.php");
    exit;
}
else {
    header("Location: ajout.php");
    exit;
}