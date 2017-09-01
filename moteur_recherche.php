
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>formulaire de requete</title>
  </head>
  <body>
      <!-- formulaire du moteur de recherche dans action il faudra mettre le vrai
           chemin pour votre site -->
    <form class="" action" http://localhost/wp-v1/wordpress/index.php/page-d-exemple" method="post">
      <label for="ville">Ville</label>
      <input type="text" name="ville" id='ville'>
      <label for="departement">departement</label>
      <input type="text" name="departement" id='departement'>
      <label for="code postal">code postal</label>
      <input type="text" name="code postal" id='code postal'>
      <label for="activite">secteur activité</label>
      <input type="text" name="activite" id='activite'>
      <label for="hebergement">hebergement</label>
      <input type="text" name="hebergement" id='hebergement'>
      <label for="covoit">covoiturage</label>
      <input type="text" name="covoit" id='covoit'>
      <button type="submit">send</button>
    </form>

<?php
// connection a la base de donnée
  try {
    $bdd = new PDO("mysql:host=localhost; dbname=locations; charset=utf8", "root", "j9hn2x2");
  }
   catch (Exception $e) {
    die('Erreur:'.$e->getMessage());
  }
   $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   //condition pour verifier si un champ du formulaire est rempli
  // si oui on recupere les info dans un tableau et on recupere
  // les entrepreneurs par le critère de recherche
if(!empty($_POST)){

  $postArray = [
    "nom" => "nom",
    "prenom" => "prenom",
    "ville" => "ville",
    "departement" => "departement",
    "code postal" => "code postal",
    "activite" => "activite",
    "covoit" => "covoit",
    "hebergement" => "hebergement"
  ];
  $usedPost = false;
  foreach ($postArray as $key => $value) {
    if(!empty($_POST[$value])){
      $usedPost = $value;
    };
  }

  $sql = 'SELECT * FROM `TABLE 2` WHERE '.$usedPost.' = "'.$_POST[$usedPost].'"';
  $response = $bdd->query($sql);

  // si aucun champ n'est rempli renvoie un message d'erreur
}else{

  echo 'ko';
}

?>
 <table>
   <thead>
     <tr>
       <th>Nom</th>
       <th>Prenom</th>
       <th>Num tel</th>
       <th>Ville</th>
       <th>DEPARTEMENT</th>
       <th>Code Postal</th>
       <th>activité</th>
       <th>HEBERGEMENT</th>
       <th>COVOITURAGE</th>
     </tr>
   </thead>
   <tbody>
       <?php
        // affichage des données récupéré depuis la base de donnée
        while ($donnees = $response->fetch()) {
       ?>
       <tr>
         <td><?= $donnees['nom']?></td>
         <td><?= $donnees['prenom']?></td>
         <td><?= $donnees['mobile']?></td>
         <td><?= $donnees['ville'];?></td>
         <td><?= $donnees['departement']; ?></td>
         <td><?= $donnees['code postal'] ?></td>
         <td><?= $donnees['activite'] ?></td>
         <td><?= $donnees['hebergement'] ?></td>
         <td><?= $donnees['covoit'] ?></td>
       </tr>
   <?php }?>


   </tbody>
 </table>
</body>
</html>

