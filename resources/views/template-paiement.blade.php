{{--
  Template Name: Paiement
--}}

@extends('layouts.pay')
<script
  src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=EUR">
</script>

@section('content')
<?php
  $annoucement = isset($_GET["annoucement"]) ? $_GET["annoucement"] : 0;

  $adress = isset($_POST["adress"]) ? $_POST["adress"] : "";
  $country = isset($_POST["country"]) ? $_POST["country"] : "";
  $city = isset($_POST["city"]) ? $_POST["city"] : "";
  $firstname = isset($_POST["firstname"]) ? $_POST["firstname"] : "";
  $lastname = isset($_POST["lastname"]) ? $_POST["lastname"] : "";
  $email = isset($_POST["email"]) ? $_POST["email"] : "";
  $phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
  $postal = isset($_POST["postal"]) ? $_POST["postal"] : "";

  $step = "informations";
  if($adress !== "" && $country  !== "" && $city !== "" && $firstname !== "" && $lastname !== "" && $email !== "" && $phone !== "" && $postal !== ""){
    $step = "paiement";
  }

  if($step === "paiement"){
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
        "adress" => get_field("adress", $annoucement),
        "city" => get_field("ville", $annoucement),
        "postal" => get_field("postal", $annoucement),
        "firstname" => get_field("prenom", $annoucement),
        "lastname" => get_field("nom", $annoucement),
        "email" => get_field("email", $annoucement),
        "phone" =>  get_field("phone", $annoucement)
      ),
      "buyer" => array(
        "state" => $_POST["country"],
        "adress" => $_POST["adress"],
        "city" => $_POST["city"],
        "postal" => $_POST["postal"],
        "firstname" => $_POST["firstname"],
        "lastname" => $_POST["lastname"],
        "email" => $_POST["email"],
        "phone" =>  $_POST["phone"],
      )
    );

    $order = newOrder($order);
  }

  if($annoucement === 0 || get_field("type", $annoucement) !== "offre" || !offerHasOpen($annoucement)) :
    global $wp_query;

    $wp_query->set_404();
    status_header(404);
  ?>
  <div class="p404_content">
    <h1>La page que vous recherchez </br> semble introuvable.</h1>
    <img src="@asset('images/404.png')" />
    <a href="/">Retour à la Page d’accueil</a>
  </div>
  <?php else : ?>
    <?php
      $price = number_format(get_field("prix", $annoucement ), 2, '.', '');
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
                  <li <?= $step === "informations" ? 'class="active-li "' : "" ?>id="li1"><span>01</span> Adresse de livraison</li>
                  <li <?= $step === "paiement" ? 'class="active-li"' : "" ?> id="li2"><span>02</span> Paiement</li>
                  <li id="li3"><span>03</span> Confirmation</li>
              </ul>
          </div>

          <?php if($step === "paiement"): ?>
            <div class="pay-form" id="form-pay">
              <h1>Réglez votre commande avec Paypal</h1>
              <p class="subtitle">La solution simple et sécurisée pour régler votre commande.</p>

              <p class="work">Comment ça marche ?</p>

              <div class="explications">
                <div class="step">
                  <img src="@asset('images/checkout/step_1.png')" />
                  <p>Réglez votre achat avec PayPal, sans avoir à envoyer vos coordonnées bancaires au vendeur.</p>
                </div>

                <div class="step">
                  <img src="@asset('images/checkout/step_2.png')" />
                  <p>Vous pouvez si vous le désirez rattacher différents moyens de paiement à votre compte PayPal : cartes de paiement, comptes bancaires, cartes privatives... À vous de choisir.</p>
                </div>

                <div class="step">
                  <img src="@asset('images/checkout/step_3.png')" />
                  <p>Vous avez terminé. Votre colis est sûrement déjà en route.</p>
                </div>
              </div>
            </div>
          <?php else : ?>
            <div class="pay-form informations" id="form-pay">
                <h1>Saisir une adresse de livraison</h1>
                <form action="/paiement/?annoucement=<?= $annoucement ?>" enctype="multipart/form-data" method="POST" >
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

                    <label for="adress">Adresse*</label>
                    <input type="text" placeholder="Saisissez l’adresse de livraison" id="adress" name="adress" />

                    <div class="flexinput-pay">
                        <div class="flex-left">
                            <label for="town">Ville*</label>
                            <br />
                            <input type="text" placeholder="Saisissez votre ville" id="city" name="city" />
                        </div>

                        <div class="flex-right">
                            <label for="zipcode">Code postal*</label>
                            <br />
                            <input type="text" placeholder="Saisissez votre code postal" id="postal" name="postal" />
                        </div>
                    </div>

                    <div class="flexinput-pay">
                        <div class="flex-left">
                        <label for="country">Pays*</label>
                          <br />
                          <select id="country" name="country" required>
                            <?=
                              $query = $GLOBALS['wpdb']->get_results("SELECT * FROM twp_country");

                              foreach ($query as $country)
                              {
                                echo '<option value=>country</option>';
                              }
                            ?>
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
          <?php endif; ?>

          <div class="pay-form confirmation" id="form-pay">
              <h1>Merci de votre commande</h1>

              <p class="subtitle">Nous vous remercions de votre commande. Nous vous tiendrons informés par e-mail lorsque l’article de votre commande aura été expédié.</p>

              <a href="/">
                Retour en page d’accueil
              </a>
            </div>
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
            <p><?= get_the_title($annoucement) ?></p>
            <p><?= $price ?>€</p>
          </div>
      </div>

      <?php if($step === "paiement"): ?>
        <div class="sub-total">
            <p><span>Frais de livraison</span><span><?= $order["shipping_price"] ?>€</span></p>
            <p><span>Frais de traitement de transport</span><span><?= $order["feet_shipping"] ?>€</span></p>
            <p><span>Sous total</span><span><?= $order["shipping_price"] +  $order["feet_shipping"] ?>€</span></p>
        </div>
      <?php else: ?>
        <div class="sub-total">
            <p><span>Sous total</span><span><?= $total ?>€</span></p>
        </div>
      <?php endif; ?>

      <?php if($step === "paiement"): ?>
        <p class="total"><span>Total</span><span> <?= $order["ttc_price"] ?>€ TTC</span></p>
        <div id="paypal-button-container"></div>

        <script>
          paypal.Buttons({
            style: {
                layout: 'horizontal',
                color:  'blue',
                shape:  'pill',
                label:  'pay',
                height: 40
            },
            createOrder: function(data, actions) {
              return actions.order.create({
                purchase_units: [{
                  custom_id: '<?= $order["custom_id"] ?>',
                  amount: {
                    value: '<?= $order["ttc_price"] ?>'
                  }
                }],
                application_context: {
                  shipping_preference: "NO_SHIPPING"
                }
              });
            },
            onApprove: function(data, actions) {
              return actions.order.capture().then(function(details) {
                jQuery.post(
                  "/wp-json/transactions/in_progress", {
                    "id_offer": <?= $order["id_offer"] ?>,
                    "token_transaction": '<?= $order["custom_id"] ?>'
                  }
                );

                jQuery(".pay-form").hide();
                jQuery(".pay-form.confirmation").show();
                jQuery("#paypal-button-container").remove();
                jQuery(".pay-steps li").removeClass("active-li");
                jQuery(".pay-steps li#li3").addClass("active-li");
              });
            }
          }).render('#paypal-button-container');
        </script>
      <?php endif; ?>
    </aside>
  <?php endif; ?>
@endsection
