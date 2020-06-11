{{--
  Template Name: New offer
--}}

<?php
  include_once( ABSPATH . 'wp-admin/includes/image.php' );
  if(!empty($_POST)) {
    // Type annonce (announcementType)
    $type = $_POST["announcementType"];
    // Catégorie (category)
    $category = $_POST["category"];
    // Sous catégorie (subcategory)
    $subcategory = $_POST["subcategory"];
    // Sous sous catégorie (subsubcategory)
    $subsubcategory = $_POST["subsubcategory"];
    // Marque (brands)
    //$brand = $_POST["brand"];
    // Modéle (models)
    $model = isset($_POST["model"]) ? $_POST["model"] : 0;;
    // Année modèle (yearmodel)
    $yearmodel = isset($_POST["yearmodel"]) ? $_POST["yearmodel"] : 0;
    // Etat (state)
    $state = $_POST["state"];
    // Titre de l'annonce (title)
    $title = $_POST["title"];
    // Description de l'annonce (description)
    $description = $_POST["description"];
    // Prix (price)
    $price = isset($_POST["price"]) ? $_POST["price"] : 0;
    // Nom (name)
    $lastname = $_POST["lastname"];
    // Prénom (name)
    $firstname = $_POST["firstname"];
    // Société (society)
    $society = $_POST["society"];
    // Ville (city)
    $city = $_POST["city"];
    // Email (email)
    $email = $_POST["email"];
    // Téléphone (phone)
    $phone = $_POST["phone"];
    // Adresse
    $adress = $_POST["adress"];
    // Code postal
    $postal = $_POST["postal"];
    // Poids de l'objet (weight)
    $weight = $_POST["weight"];
    // Largeur du colis
    $packing_larger = $_POST["packing_larger"];
    // Hauteur du colis
    $packing_height = $_POST["packing_height"];
    // Profondeur du colis
    $packing_depth = $_POST["packing_depth"];

    $uploaddir = wp_upload_dir();

    foreach($_FILES as $key => $file) {
      if(!empty($_FILES['picture_1']['name'])){
        $uploadfile[$key] = $uploaddir["basedir"] . "/tmp/" . time() . "-" .  basename($_FILES[$key]['name']);
        move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile[$key]);
      }
    }

    $my_post = array(
      'post_title'    => wp_strip_all_tags( $title ),
      'post_status'   => 'pending',
      'post_author'   => 1,
      "post_type" => "offres"
    );

    $new = wp_insert_post( $my_post );

    wp_set_object_terms( $new, intval($subsubcategory), 'category_piece' );

    update_field("modele", intval($model), $new);
    update_field("etat", $state, $new);
    update_field("type", $type, $new);
    update_field("prix", $price, $new);
    update_field("email", $email, $new);
    update_field("poids", $weight, $new);
    update_field("packing_larger", $packing_larger, $new);
    update_field("packing_height", $packing_height, $new);
    update_field("packing_depth", $packing_depth, $new);
    update_field("year_model", $yearmodel, $new);
    update_field("description", $description, $new);
    update_field("nom", $lastname, $new);
    update_field("prenom", $firstname, $new);
    update_field("postal", $postal, $new);
    update_field("adress", $adress, $new);
    update_field("ville", $city, $new);
    update_field("society", $society, $new);
    update_field("phone", $phone, $new);

    foreach($_FILES as $key => $file) {
      if(!empty($_FILES[$key]['name'])){
        $wp_filetype = wp_check_filetype(basename($uploadfile[$key]), null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => "Image 1",
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $uploadfile[$key] );
        $imagenew = get_post( $attach_id );
        $fullsizepath = get_attached_file( $imagenew->ID );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        update_field($key, $attach_id, $new);
      }
    }

    sendEmailConfirmationAnnonce([
      "email" => $email,
      "lastname" => $lastname,
      "firstname" => $firstname,
      "title" => $title
    ]);

    $redirect = get_site_url(null, "/nouvelle-annonce/?v=1");
    header("Location: {$redirect}");

    exit();
  }
?>

@extends('layouts.app')
@section('content')
<p class="path" style="cursor:pointer">
  <span onclick="window.location.href='/'">Accueil</span>
  <span class="pathImg"><img src="@asset('images/img_research/Base.png')" id="triangle" /></span>
  <span class="pathelt"> Déposer une annonce</span></p>
    <form action="<?php the_permalink();?>" enctype="multipart/form-data" method="post">
        <section id="announcement">
            <h1>Votre annonce</h1>
            <h2>Type d’annonce*</h2>
            <p><input type="radio" name="announcementType" id="type1" value="offre" /><label for="type1">Offre (vous vendez une pièce détachée)</label></p>
            <p><input type="radio" name="announcementType" id="type2" value="demande" /><label for="type2">Demande (vous recherchez une pièce détachée)</label></p>
            <p><input type="radio" name="announcementType" id="type3" value="echange" /><label for="type3">Échange ou don (vous échangez ou donnez une pièce détachée)</label></p>

            <p><label for="category" class="labelSelect">Catégorie*</label></p>
            <p>
                <select id="category" name="category" required></select>
            </p>
            <p>
              <label for="subcategory" class="labelSelect subcategory">Sous Catégorie*</label>
            </p>
            <p>
                <select id="subcategory" class="subcategory" name="subcategory" required></select>
            </p>
            <p>
              <label for="subsubcategory" class="labelSelect subsubcategory">Catégorie finale*</label>
            </p>
            <p>
              <select id="subsubcategory" class="subsubcategory" name="subsubcategory" required></select>
            </p>
            <!--<p><label for="brand" class="labelSelect">Marques*</label></p>
            <p>
            <select style="width: 50%" class="js-brands-ajax" name="brand" ></select>
            </p>-->
            <p>
              <label for="model" class="labelSelect">Modèle</label>
            </p>
            <p>
              <select style="width: 50%" class="js-models-ajax" name="model">
                <option value="">Autre</option>
              </select>
            </p>
            <p>
              <label for="yearmodel" class="labelSelect">Année Modèle</label>
            </p>
            <p>
              <input id="yearmodel" name="yearmodel" type="number" min="1895" max="1990"/>
            </p>
            <p><label for="state" class="labelSelect">Etat*</label></p>
            <p>
                <select value="" id="state" name="state">
                    <option disabled selected>Sélectionner l’état de la pièce</option>
                    <option value="Neuf sous emballage">Neuf sous emballage</option>
                    <option value="Neuf déballé">Neuf déballé</option>
                    <option value="Peu utilisé / Peu usé / Peu endommagé">Peu utilisé / Peu usé / Peu endommagé</option>
                    <option value="Utilisé / Usé / Endommagé, à réparer / Rouillé">Utilisé / Usé / Endommagé, à réparer / Rouillé</option>
                    <option value="Très Usé / En mauvais état / Fortement rouillé ">Très Usé / En mauvais état / Fortement rouillé </option>
                </select>
            </p>
            <p><label for="title" class="labelSelect">Titre de l’annonce*</label></p>
            <p id="titleP"><input type="text" id="title" name="title" placeholder="Saisissez le titre de votre annonce" required/><img src="<?= get_template_directory_uri(); ?>/assets/images/img_new_announcement/3-layers.png" alt="bulle" id="bubble" /></p>
            <p><label for="descriptif" class="labelSelect">Descriptif de l’annonce*</label></p>
            <p id="textareaP"><textarea placeholder="Saisissez votre description" name="description" id="counter" maxlength="1000" ></textarea><span id="compteur">1000</span></p>

            <p class="priceShow"><label for="price" class="labelSelect">Prix*</label></p>
            <p class="priceShow"><input type="number" id="price" name="price" min="1" pattern="[0-9]*" required /></p>
            <p><label for="photos" class="labelSelect" id="testFile">Photos * <span id="colorChange">Formats acceptées: jpg, png</span></label> </p>
            <div id="fileContainer">
                <div class="fileDiv" >
                    <p class="fileP">
                      <input type="file" name="picture_1" id="picture_1" accept="image/png, image/jpeg" multiple style="opacity:0" />
                      <img src="@asset('images/img_new_announcement/cross.png')" alt="croix" class="cross" id="cross1" />
                    </p>
                    <div class="fileChosen" data-default="@asset('images/img_new_announcement/selectImg.png')"></div>
                </div>
                <div class="fileDiv">
                    <p class="fileP">
                      <input type="file" name="picture_2" id="picture_2" accept="image/png, image/jpeg" multiple style="opacity:0" />
                      <img src="@asset('images/img_new_announcement/cross.png')" alt="croix" id="cross2" class="cross" />
                    </p>
                    <div class="fileChosen" data-default="@asset('images/img_new_announcement/selectImg.png')"></div>
                </div>
                <div class="fileDiv">
                    <p class="fileP">
                      <input type="file" name="picture_3" id="picture_3" accept="image/png, image/jpeg" multiple style="opacity:0" />
                      <img src="@asset('images/img_new_announcement/cross.png')" alt="croix" id="cross3" class="cross" />
                    </p>
                    <div class="fileChosen" data-default="@asset('images/img_new_announcement/selectImg.png')"></div>
                </div>
            </div>
        </section>

        <section class="colisShow" id="info">
            <h1>Informations sur le colis</h1>
            <p><label for="weight" class="labelSelect">Poids de la pièces et emballage (en kilogramme)*</label></p>
            <p><input type="number" id="weight" name="weight" required/></p>

            <p><label for="packing_larger" class="labelSelect">Largeur du colis (en centimètre)*</label></p>
            <p><input type="number" id="packing_larger" name="packing_larger" required/></p>

            <p><label for="packing_height" class="labelSelect">Hauteur du colis (en centimètre)*</label></p>
            <p><input type="number" id="packing_height" name="packing_height" required/></p>

            <p><label for="packing_depth" class="labelSelect">Profondeur du colis (en centimètre)*</label></p>
            <p><input type="number" id="packing_depth" name="packing_depth" required/></p>
        </section>

        <section id="info">
            <h1>vos informations</h1>
            <div id="companyAndName">
                <div id="name">
                    <p><label for="name">Nom* <span>N’apparaitra pas dans l’annonce</span></label></p>
                    <p><input type="text" id="lastname" name="lastname" required/></p>
                </div>
                <div id="name">
                    <p><label for="name">Prénom* <span>N’apparaitra pas dans l’annonce</span></label></p>
                    <p><input type="text" id="firstname" name="firstname" required/></p>
                </div>
            </div>

            <div id="company">
                    <p><label for="company">Société <span>(facultatif, n’apparaitra pas dans l’annonce)</span></label></p>
                    <p><input type="text" name="society" id="company" /></p>
                </div>

            <p><label for="mail">Adresse* <span>N’apparaitra pas dans l’annonce</span></label></p>
            <p><input type="text" name="adress" id="adress" required/></p>

            <div id="companyAndName">
                <div id="name">
                    <p><label for="name">Ville*</label></p>
                    <p><input type="text" id="city" name="city" required/></p>
                </div>
                <div id="company">
                    <p><label for="postal">Code postal*</label></p>
                    <p><input id="postal" name="postal" type="text" required/></p>
                </div>
            </div>

            <p><label for="mail">Email* <span>Votre adresse mail n’apparaitra pas dans l’annonce</span></label></p>
            <p><input type="mail" name="email" id="mail" required/></p>
            <p><label for="tel">Téléphone* <span>Votre numéro de téléphone n’apparaitra pas dans l’annonce</span></label></p>
            <p><input type="tel" id="tel" name="phone" required/></p>
        </section>
        <p id="buttonP"><span>* Champs obligatoires</span><button type="submit">Publier l’annonce</button></p>
    </form>
    <div id="announcement__validation" class="<?php if($_GET['v'] == "1"){ echo "active"; } ?>">
      <div class="announcement__validation--layer"></div>
      <div class="announcement__validation--container">
        <h3>Annonce en cours de vérification…</h3>
        <p>Votre annonce sera relue par notre équipe éditoriale pour validation. Une fois votre annonce confirmée, elle sera validée et mise en ligne dans un délai de 48h maximum. Vous recevrez un email de confirmation, vous indiquant que votre annonce est en ligne.</p>
        <p>En cas de refus de votre annonce par l’équipe éditoriale, vous recevrez un mail vous expliquant les motifs du refus.</p>

        <p>Si vous ne recevez pas de mail de confirmation, 2 options:</br>
        - l’adresse mail indiquée n’est pas valide</br>
        - l’email a été bloqué par votre filtre antispam. Dans ce dernier cas il vous suffit de retrouver l’email  dans votre boite de reception anti-spam.</p>

        <div class="announcement__validation--container-buttons">
          <div class="link">Une question ? <a href="mailto:tacotroc@tacotroc.com">Contactez-nous</a></div>
          <div class="button">J’ai compris</div>
        </div>
      </div>
    </div>
@endsection
