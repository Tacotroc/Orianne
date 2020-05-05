{{--
  Template Name: Paiement-cb
--}}

@extends('layouts.pay')
@section('content')
<?php

require_once(TRIPART_PLUGIN_PATH . "entity/TripartieAPI.php");
require_once(TRIPART_PLUGIN_PATH . "entity/verif_info.php");

$tripartieAPI = new TripartieAPI;
$annoucement = isset($_GET["annoucement"]) ? $_GET["annoucement"] : 0;
$address = isset($_POST["address"]) ? $_POST["address"] : "";
$country = isset($_POST["country"]) ? $_POST["country"] : "";
$city = isset($_POST["city"]) ? $_POST["city"] : "";
$firstname = isset($_POST["firstname"]) ? $_POST["firstname"] : "";
$lastname = isset($_POST["lastname"]) ? $_POST["lastname"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";
$phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
$postal = isset($_POST["postal"]) ? $_POST["postal"] : "";
$birthday = isset($_POST["birthday"]) ? $_POST["birthday"] : "";
$residency = isset($_POST["residency"]) ? $_POST["residency"] : "";
$nationality = isset($_POST["nationality"]) ? $_POST["nationality"] : "";

$mangoTransactionId = isset($_GET['mangoTransactionId']) ? $_GET["mangoTransactionId"] : "";
$transactionId = isset($_GET['transactionId']) ? $_GET['transactionId'] : "";
$buyerId = isset($_GET['buyerId']) ? $_GET['buyerId'] : "";
$buyerInformations = isset($_GET['buyerInformations']) ? $_GET['buyerInformations'] : "";

if (($address !== "" && $country !== "" && $city !== "" && $firstname !== "" && $lastname !== "" && $email !== "" && $phone !== "" && $postal !== "") || ($buyerId !== "" && $transactionId !== "" && $buyerInformations !== "")) {
  $step = "facturation";
} else {
  $step = "addr_livraison";
}

if ($step === "facturation" && $buyerId === "" && $transactionId === "" && $buyerInformations === "") {
  $utilisateurs = Verif::getAll($_POST['email'], $_POST['phone']);
  if (count($utilisateurs) > 0) {
    foreach ($utilisateurs as $utilisateur) {
      if (
        strtolower($_POST['firstname']) == strtolower($utilisateur->Prenom) &&
        strtolower($_POST['lastname']) == strtolower($utilisateur->Nom) &&
        $_POST['birthday'] == $utilisateur->Birthday
      ) {
        if ($_POST['phone'] == $utilisateur->Tel && $_POST['email'] != $utilisateur->Email) {
          echo "<br>Un compte avec le même numéro de téléphone a déjà été trouvé, mais l'adresse email ne correspond pas.<br>";
        }
        if ($_POST['email'] == $utilisateur->Email && $_POST['phone'] != $utilisateur->Tel) {
          echo "<br>Un compte avec la même adresse email a déjà été trouvé, mais le numéro de téléphone ne correspond pas.<br>";
        }
        $tripartieAPI->fetch_user($utilisateur->id);
        $actualUser = $tripartieAPI->getActualUser();
      } else {
        echo '<br>Information différente, renvoyer "Un utilisateur different a déjâ utilisé tacotroc.com"<br>';
      }
    }
  } else {
    $tripartieAPI->create_new_user();
    $actualUser = $tripartieAPI->getActualUser();
  }
} else if ($buyerId !== "" && $transactionId !== "" && $buyerInformations !== "") {
  $tripartieAPI->fetch_user($buyerId);
  $actualUser = $tripartieAPI->getActualUser();
}

if ($step === "facturation") {
  $order = array(
    "offer" => array(
      "id" => $annoucement,
      "title" => get_the_title($annoucement),
      "price" => get_field("prix", $annoucement)
    ),
    "packing" => array(
      "weight" => get_field("poids", $annoucement),
      "packing_larger" => get_field("packing_larger", $annoucement),
      "packing_height" => get_field("packing_height", $annoucement),
      "packing_depth" => get_field("packing_depth", $annoucement)
    ),
    "seller" => array(
      "state" => "FRANCE",
      "address" => get_field("address", $annoucement),
      "city" => get_field("ville", $annoucement),
      "postal" => get_field("postal", $annoucement),
      "firstname" => get_field("prenom", $annoucement),
      "lastname" => get_field("nom", $annoucement),
      "email" => get_field("email", $annoucement),
      "phone" => get_field("phone", $annoucement)
    ),
    "buyer" => null
  );

  if ($buyerId === "" && $transactionId === "" && $buyerInformations === "") {
    $order['buyer'] = array(
      "state" => $_POST["country"],
      "address" => $_POST["address"],
      "city" => $_POST["city"],
      "postal" => $_POST["postal"],
      "firstname" => $_POST["firstname"],
      "lastname" => $_POST["lastname"],
      "email" => $_POST["email"],
      "phone" => $_POST["phone"],
      "birthday" => $_POST["birthday"],
      "residency" => $_POST["residency"],
      "nationality" => $_POST["nationality"],
    );
  } else {
    /*echo "<pre>";
    print_r(base64_decode($buyerInformations));
    echo "</pre>";
    echo "<pre>";
    print_r(json_decode(base64_decode($buyerInformations), true));
    echo "</pre>";*/
    $order['buyer'] = json_decode(base64_decode($buyerInformations), true);
  }

  $order_buyer = $order['buyer'];
  $order = newOrder($order);
}

if ($annoucement === 0 || get_field("type", $annoucement) !== "offre" || !offerHasOpen($annoucement)) {
  global $wp_query;

  $wp_query->set_404();
  status_header(404);
?>
  <div class="p404_content">
    <h1>La page que vous recherchez </br> semble introuvable.</h1>
    <img src="@asset('images/404.png')" />
    <a href="/">Retour à la Page d’accueil</a>
  </div>
<?php } else {
  $price = number_format(get_field("prix", $annoucement), 2, '.', '');
  $total = number_format($price, 2, '.', '');
?>
  <section class="pay-left">
    <div class="header-pay">
      <a href="/">
        <img src="@asset('images/logo.png')" alt="logo" />
      </a>
    </div>

    <div class="pay-container">
      <div class="pay-steps">
        <ul>
          <li <?= $step === "addr_livraison" ? 'class="active-li "' : "" ?>id="li1"><span>01</span> Adresse de livraison</li>
          <li <?= $step === "facturation" ? 'class="active-li"' : "" ?> id="li2"><span>02</span> Informations de paiement</li>
          <li <?= $step === "confirmation" ? 'class="active-li"' : "" ?> id="li3"><span>03</span> Confirmation</li>
        </ul>
      </div>

      <?php if ($step === "addr_livraison") { ?>
        <div class="pay-form informations" id="form-pay">
          <h1>Saisir une adresse de livraison</h1>
          <form action="/tripart_cb/?annoucement=<?= $annoucement ?>" enctype="multipart/form-data" method="POST">
            <div class="flexinput-pay">
              <div class="flex-left">
                <label for="lastname">Nom*</label>
                <br />
                <input type="text" id="lastname" name="lastname" placeholder="Saisissez votre nom de famille" require />
              </div>

              <div class="flex-right">
                <label for="firstname">Prénom*</label>
                <br />
                <input type="text" id="firstname" name="firstname" placeholder="Saisissez votre prénom" require />
              </div>
            </div>

            <div class="flexinput-three">
              <div class="flex-left">
                <label for="birthday">Date de naissance*</label>
                <input type="date" placeholder="Renseignez votre date de naissance" name="birthday" id="birthday" required />
              </div>

              <div class="flex-middle">
                <label for="résidency">Pays de résidence*</label>
                <select placeholder="Renseignez votre date de naissance" name="residency" id="residency">
                  <option value="country_france" selected>France</option>
                </select>
              </div>

              <div class="flex-right">
                <label for="nationality">Nationalité*</label>
                <select placeholder="Renseignez votre date de naissance" name="nationality" id="nationality">
                  <option value="country_france" selected>France</option>
                </select>
              </div>
            </div>

            <label for="address">Adresse*</label>
            <input type="text" placeholder="Saisissez l’adresse de livraison" id="address" name="address" />

            <div class="flexinput-pay">
              <div class="flex-left">
                <label for="town">Ville*</label>
                <br />
                <input type="text" placeholder="Saisissez votre ville" id="city" name="city" />
              </div>

              <div class="flex-right">
                <label for="postal">Code postal*</label>
                <br />
                <input type="text" placeholder="Saisissez votre code postal" id="postal" name="postal" />
              </div>
            </div>

            <div class="flexinput-pay">
              <div class="flex-left">
                <label for="country">Pays*</label>
                <br />
                <select id="country" name="country">
                  <option value="france" selected>France</option>
                </select>
              </div>

              <div class="flex-right">
                <label for="phone">Numéro de téléphone*</label>
                <br />
                <input type="text" placeholder="Saisissez votre numéro de téléphone" id="phone" name="phone" required />
              </div>
            </div>

            <label for="mail">Email*</label>
            <input type="mail" placeholder="Saisissez votre adresse email" name="email" id="email" required />

            <p class="pay-bottom">
              <span id="back-pay" onclick="window.location.href='<?= get_permalink($annoucement) ?>'">
                <img src="@asset('images/img_pay/arrow.png')" alt="flèche" id="arrow-pay" />
                <span id="content-pay">Abandonner ma commande</span>
              </span>
              <span id="button-pay">
                <button type="submit" id="tunnelNext">Envoyer à cette adresse</button>
              </span>
            </p>
          </form>
        </div>
      <?php } else if ($step === "facturation") { ?>
        <div class="pay-form" id="pay-form">
          <h1>Reglez votre commande avec Tripartie</h1>
          <p class="subtitle">le tier de confiance pour les interets des deux partie</p>
          <p class="work">Remplissez les champs suivant :</p>

          <?php
          $actualUserId = $actualUser['id'];
          $currency = "currency_eur";
          $paymentMethodType = "payment_method_type_cb_visa_mastercard";
          $tripartieAPI->initiate_card_authorization($actualUserId, $currency, $paymentMethodType);
          $tokenizationData = $tripartieAPI->getTokenizationData();
          ?>

          <form id="tokenizer">
            <input type="hidden" id="buyer_informations" value="<?php echo base64_encode(json_encode($order_buyer)); ?>" />
            <input type="hidden" id="annoucement_id" value="<?php echo $annoucement; ?>" />
            <input type="hidden" id="buyer_id" value="<?php echo $actualUser['id']; ?>" />
            <input type="hidden" id="seller_id" value="<?php echo 6/*$seller_id*/; ?>" />
            <input type="hidden" id="id" value="<?php echo $tokenizationData['id']; ?>" />
            <input type="hidden" id="cardRegistrationURL" value="<?php echo $tokenizationData['cardRegistrationURL'] ?>" />
            <input type="hidden" name="accessKeyRef" id="accessKeyRef" value="<?php echo $tokenizationData['accessKey'] ?>" />
            <input type="hidden" name="data" id="data" value="<?php echo $tokenizationData['preregistrationData'] ?>" />
            <label for="cardNumber">Numéro de carte*</label>
            <input type="text" placeholder="XXXXXXXXXXXXXXXX" name="cardNumber" id="cardNumber" pattern="^([0-9]{16})$" minlength="16" maxlength="16" required />
            <div class="flexinput-pay">
              <div class="flex-left">
                <label for="cardExpirationDate">Date d'expiration*</label>
                <div class="flexinput-pay">
                  <div class="flex-left">
                    <select id='expirationDateMM' onchange="changeExpirationDateParceQueCaFaisPlaisirMemebeaucoupplaisirenvrai(this)">
                      <option value=''>Mois</option>
                      <option value='01'>01</option>
                      <option value='02'>02</option>
                      <option value='03'>03</option>
                      <option value='04'>04</option>
                      <option value='05'>05</option>
                      <option value='06'>06</option>
                      <option value='07'>07</option>
                      <option value='08'>08</option>
                      <option value='09'>09</option>
                      <option value='10'>10</option>
                      <option value='11'>11</option>
                      <option value='12'>12</option>
                    </select>
                  </div>
                  <div class="flex-right">
                    <select id='expirationDateYY' onchange="changeExpirationDateParceQueCaFaisPlaisirMemebeaucoupplaisirenvrai(this)">
                      <option value=''>Année</option>
                      <?php
                      $year = idate('Y');
                      $yearDigits = idate('y');
                      ?>
                      <option value='<?php echo $yearDigits; ?>'><?php echo $yearDigits++; ?></option>
                      <option value='<?php echo $yearDigits; ?>'><?php echo $yearDigits++; ?></option>
                      <option value='<?php echo $yearDigits; ?>'><?php echo $yearDigits++; ?></option>
                      <option value='<?php echo $yearDigits; ?>'><?php echo $yearDigits++; ?></option>
                      <option value='<?php echo $yearDigits; ?>'><?php echo $yearDigits++; ?></option>
                      <option value='<?php echo $yearDigits; ?>'><?php echo $yearDigits++; ?></option>
                    </select>
                  </div>
                </div>
                <input type="hidden" name="cardExpirationDate" id="cardExpirationDate" pattern="^(0[1-9]|1[0-2])([0-9]{2})$" minlength="4" maxlength="4" required />
                <script>
                  function changeExpirationDateParceQueCaFaisPlaisirMemebeaucoupplaisirenvrai(element) {
                    var YY = jQuery('#expirationDateYY').val();
                    var MM = jQuery('#expirationDateMM').val();
                    jQuery('#cardExpirationDate').val(MM + '' + YY);
                  }
                </script>
              </div>
              <div class="flex-right">
                <label for="cardCvx">CVV*</label>
                <input type="text" id="cardCvx" name="cardCvx" placeholder="123" required />
              </div>
            </div>

            <p class="pay-bottom">
              <span id="back-pay" onclick="window.location.href='<?= get_permalink($annoucement) ?>'">
                <img src="@asset('images/img_pay/arrow.png')" alt="flèche" id="arrow-pay" />
                <span id="content-pay">Abandonner ma commande</span>
              </span>
              <span id="button-pay">
                <button type="submit" id="tunnelNext">Envoyer à cette adresse</button>
              </span>
            </p>
          </form>

        <?php } else if ($step === "confirmation") { ?>
          <div class="pay-form confirmation" id="form-pay">
            <h1>Merci de votre commande</h1>

            <p class="subtitle">Nous vous remercions de votre commande. Nous vous tiendrons informés par e-mail lorsque
              l’article de votre commande aura été expédié.</p>

            <a href="/">
              Retour en page d’accueil
            </a>
          </div>
        <?php } ?>

        </div>
  </section>

  <aside class="pay-right">
    <div class="header-pay" id="mobiltitle">
      <img src="@asset('images/img_pay/logo.png')" alt="logo" />
    </div>
    <h2>Votre commande</h2>

    <div class="product-pay">
      <div class="picture-pay">
        <?php $picture_1 = get_field("picture_1", $annoucement); ?>
        <img src="<?= $picture_1["sizes"]["large"] ?>" alt="logo" />
      </div>

      <div class="product-content-pay">
        <p id="annoucement_title"><?= get_the_title($annoucement) ?></p>
        <p><?= $price ?>€</p>
      </div>
    </div>

    <?php if ($step === "facturation") { ?>
      <div class="sub-total">
        <p><span>Frais de livraison</span><span><?= $order["shipping_price"] ?>€</span></p>
        <p><span>Frais de traitement de transport</span><span><?= $order["feet_shipping"] ?>€</span></p>
        <p><span>Sous total</span><span><?= $order["shipping_price"] + $order["feet_shipping"] ?>€</span></p>
      </div>
    <?php } ?>
    <div class="sub-total">
      <p><span>Sous total</span><span><?= $total ?>€</span></p>
    </div>
  <?php } ?>

  <?php if ($step === "facturation") { ?>
    <p class="total"><span>Total</span><span> <span id="annoucement_price"><?= $order["ttc_price"] ?></span>€ TTC</span></p>
    <div id="paypal-button-container"></div>

    <script>
    </script>
  <?php } ?>
  </aside>
  @endsection