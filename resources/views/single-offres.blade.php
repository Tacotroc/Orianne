@extends('layouts.app')

@section('content')
<?php

  $model = get_field("modele", $post->ID);

  if(isset($model[0]->ID)){
    $brand = get_field("marque", $model[0]->ID);
  }

  $picture_1 = get_field("picture_1", $post->ID);
  $picture_2 = get_field("picture_2", $post->ID);
  $picture_3 = get_field("picture_3", $post->ID);

  if(get_post_status($post->ID) === "pending" || get_post_status($post->ID) === "draft"){
    $status = true;
  } else {
    $status = offerHasOpen($post->ID);
  }

  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){

    $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  }
   else{

    $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   }
?>

<section id="announcementContent">
    <article id="announcementLeft">
        <div id="announcePicture" data-slick='{"slidesToShow": 1, "slidesToScroll": 1}'>
            <?php if(!empty($picture_1)): ?><div class="announcePictureSingle" style='background:url(<?= $picture_1["sizes"]["large"] ?>) no-repeat'></div><?php endif; ?>
            <?php if(!empty($picture_2)): ?><div class="announcePictureSingle" style='background:url(<?= $picture_2["sizes"]["large"] ?>) no-repeat'></div><?php endif; ?>
            <?php if(!empty($picture_3)): ?><div class="announcePictureSingle" style='background:url(<?= $picture_3["sizes"]["large"] ?>) no-repeat'></div><?php endif; ?>
        </div>

      <div id="announcementInfo">
        <div class="deroulant">
          <div class="button">
            <button class="shareButton">
              <li class="sous">
              <a class="a" href="#spot">
              <img src="@asset('images/img_announcement/share.png')"
                alt="bouton partager"/>
                <span>Partager</span></a>

                <ul id="saveAndShare">
                  <li><a target="_blank" href="mailto:?Subject=Tacotroc&body=<?php echo $url; ?>" rel="nofollow">
                      <img id="mail" src="@asset('images/img_share/Mail.png')" title="envoyer par mail" alt="mail-logo"/></a></li>

                  <li><a target="_blank" href="https://twitter.com/share?url=" rel="nofollow">
                      <img id="twitter" src="@asset('images/img_share/Twitter.png')" title="partager sur Twitter" alt="twitter-logo"/></a></li>

                  <li><a target="_blank" href="https://www.facebook.com/sharer.php?url=" rel="nofollow">
                      <img id="facebook" src="@asset('images/img_share/Facebook.png')" title="partager sur Facebook" alt="facebook-logo"/></a></li>

                  <li><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>" rel="nofollow" onclick="javascript:window.open(this.href, '','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=450,width=650')">
                      <img id="linkedin" src="@asset('images/img_share/Linkedin.png')" title="partager sur Linkedin" alt="linkedin-logo"/></a></li>

                  <li><a target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo $url; ?>" data-pin-do="buttonBookmark" rel="nofollow">
                      <img id="pinterest" src="@asset('images/img_share/Pinterest.png')" title="épingler sur Pinterest" alt="pinterest-logo"/></a></li>
                </ul>
              </li>
            </button>
            <!--<button class=favori>
              <img id=star onclick="colorChange()" src="@asset('images/star.png')" alt="favori"/>
                <script>
                  function colorChange()
                  {
                    document.getElementById("star").src="@asset('images/star-color.png')";
                  }
                </script>
            </button>-->
          </div>
        </div>

        <div class="offerContent">
          <h3 class="brand">
            <?= isset($brand[0]->post_title) ? $brand[0]->post_title : '' ?>
            <span class="price">
              <?php if($status) : ?>
                <?= get_field("prix", $post->ID); ?><?= !empty(get_field("prix", $post->ID)) ? "€ " : "" ?>
              <?php else: ?>
                <span class="sold">Vendu</span>
              <?php endif; ?>
            </span>
          </h3>
          <h4><?= get_the_title() ?></h4>
          <!-- <p class="description"></p> -->
          <p class="location">Publiée le <?=  date('d/m/y', strtotime($post->post_date))?></p>
        </div>
      </div>

        <div id="criteres">
          <h2>Critères</h2>
          <div>
              <p>Marque</p>
              <?php if(isset($brand[0]->post_title)): ?>
                <p><a href="<?= get_permalink($brand[0]->ID) ?>"><?= $brand[0]->post_title ?></a></p>
              <?php else: ?>
                <p>Inconnue</p>
              <?php endif; ?>
          </div>
          <div>
              <p>Modèle</p>
              <?php if(isset($model[0]->post_title)): ?>
                <p><a href="<?= get_permalink($model[0]->ID) ?>"><?= $model[0]->post_title ?></a></p>
              <?php else: ?>
                <p>Inconnu</p>
              <?php endif; ?>
          </div>
          <div>
              <p>Année modèle</p>
              <p><?= get_field("year_model", $post->ID); ?></p>
          </div>
          <div>
              <p>État</p>
              <p><?= get_field("etat", $post->ID); ?></p>
          </div>
        </div>

        <div id="description">
          <h2>Description</h2>
          <p><?= get_field("description", $post->ID); ?></p>
        </div>

        <div id="localisation">
          <div id="LocalisationContent">
              <h2>Localisation</h2>
              <div id="loc">
                <p><?= get_field("ville", $post->ID); ?></p>
                <p><?= get_field("pays", $post->ID); ?></p>
              </div>
          </div>
        </div>
    </article>
    <article id="announcementRight">
      <div id="contact">
        <?php if($status && get_field("type", $post->ID) === "offre" ) : ?>
          <h2>Contacter le vendeur</h2>
          <div>
            <div id="popup">
              <p><button type=button onclick="popupFunction()"><a href='mailto:contact@tacotroc.com?Subject=<?php echo $url; ?>'/>Envoyer un message</a></button></p>
              <script>
                function popupFunction() {
                (alert("Afin de préserver l'anonymat et votre tranquilité, Tacotroc transmettra votre message au destinataire"))
              }
              </script>
            </div>
          </div>
        <?php else: ?>
          <h2>Proposer au demandeur</h2>
          <div>
            <div id="popup">
              <p><button><a href="/proposition">Envoyer un message</a>
              </button></p>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <?php if($status && get_field("type", $post->ID) === "offre" ) : ?>
        <div id="transaction">
            <h2 id="secured"><span>Transaction sécurisée</span><img src="@asset('images/img_announcement/ruban.png')" alt="ruban" id="ruban" /></h2>
            <p id="transactionContent"></p>
            <p id="buy">
              <a href="/choose_paiement/?annoucement=<?= get_the_ID() ?>">
              <button type="button">Acheter en ligne</button>
              </a>
            </p>
        </div>
      <?php endif; ?>
    </article>
</section>
@endsection
