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
      <label for="city">Ville</label>
      <input type="text" name="city" id='city'>
      <label for="departement">departement</label>
      <input type="text" name="departement" id='departement'>
      <label for="zip_code">code postal</label>
      <input type="text" name="zip_code" id='zip_code'>
      <label for="activity">secteur activité</label>
      <input type="text" name="activity" id='activity'>
      <label for="accommodation">hebergement</label>
      <input type="text" name="accomodation" id='accomodation'>
      <label for="covoit">covoiturage</label>
      <input type="text" name="covoit" id='covoit'>
      <button type="submit">send</button>
    </form>

<?php
// connection a la base de donnée
  try {
    $bdd = new pdo ('mysql:host=localhost;dbname=test', 'nolan','adminannu');
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
    "city" => "city",
    "departement" => "departement",
    "zip_code" => "zip_code",
    "activity" => "activity",
    "covoit" => "covoit",
    "accommodation" => "accommodation"
  ];
  $usedPost = false;
  foreach ($postArray as $key => $value) {
    if(!empty($_POST[$value])){
      $usedPost = $value;
    };
  }

  $sql = 'SELECT * FROM users_location WHERE '.$usedPost.' = "'.$_POST[$usedPost].'"';
  $response = $bdd->query($sql);
  // si aucun champ n'est rempli renvoie un message d'erreur
}else{

  echo 'ko';
}

?>
 <table>
   <thead>
     <tr>
       <th>ID</th>
       <th>CITY</th>
       <th>DEPARTEMENT</th>
       <th>ZIP_CODE</th>
       <th>activity</th>
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
         <td><?= $donnees['id']; ?></td>
         <td><?= $donnees['city'];?></td>
         <td><?= $donnees['departement']; ?></td>
         <td><?= $donnees['zip_code'] ?></td>
         <td><?= $donnees['activity'] ?></td>
         <td><?= $donnees['accommodation'] ?></td>
         <td><?= $donnees['covoit'] ?></td>
       </tr>
   <?php }?>


   </tbody>
 </table>
</body>
</html>
