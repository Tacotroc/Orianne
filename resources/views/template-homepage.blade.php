{{--
  Template Name: HomePage Template
--}}


<?php
// header('Location: http://www./login');
// exit();
//sendEmail();
?>

@extends('layouts.app')
@section('content')

  <?php
  global $wpdb;

  $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele,
    (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type
    FROM wp_posts as p
    WHERE p.post_status='publish'
    HAVING type='offre'
    ORDER BY p.post_date DESC
    LIMIT 20";

  $offers = $wpdb->get_results($query);

  $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele,
    (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type
    FROM wp_posts as p
    WHERE p.post_status='publish'
    HAVING type='demande'
    ORDER BY p.post_date DESC
    LIMIT 20";

  $demandes = $wpdb->get_results($query);
  ?>

  <?php
  // Manage sticky posts
  $sticky_posts = get_option('sticky_posts');
  $sticky = implode(",", $sticky_posts);
  ?>

  <div id="logo_partenaire">
    <h2>NOS PARTENAIRES</h2>
    <a href="https://www.ffve.org/" target="blank">
      <img src="@asset('images/Logo-FFVE.png')" alt="logo ffve"
           title="Fédération Française des Véhicules d'époque"/>
    </a>

    <img src="@asset('images/logos-partenaires-INITIATIVE.png')" alt="logo INITIATIVE"
         title="Initiative Lille Métropole Nord"/>

    <img src="@asset('images/logos-partenaires-MEL.png')" alt="logo Metropole Européenne de Lille"
         title="Métropole Européenne de Lille"/>

    <img src="@asset('images/logos-partenaires-EURATECH.png')" alt="euratech logo" title="EuraTechnologie"/>


  </div>

  <section class="market">
    <article class="offers">
      <h2>Dernières offres<a href="/annonces/?type=offre"><span>Voir tout ></span></a></h2>
      <div class="offersContainer">
        <?php
        $compteur_offers = 0;
        foreach ($offers as $offer) : ?>
        <?php
        if($compteur_offers != 5){
        $model = get_field("modele", $offer->ID);
        $post = get_post($offer->ID);
        $image = get_field("picture_1", $offer->ID);
        $medium = wp_get_attachment_image_src($image['ID'], 'medium');
        ?>
        <a href="<?= get_permalink($offer->ID) ?>" class="clean">
          <div class="offer">
            <div class="offerPicture" style="background-image: url(<?= $medium[0]; ?>)"></div>
            <div class="offerContent">
              <h3 class="brand"><?= isset($model[0]->post_title) ? $model[0]->post_title : "Marque inconnue" ?><span
                  class="price"><?= get_field("prix", $offer->ID); ?><?= !empty(get_field("prix", $offer->ID)) ? "€" : "" ?></span>
              </h3>
              <h4><?= get_the_title($offer->ID); ?></h4>
              <p class="description"><?= helper_description(get_field("description", $offer->ID)); ?></p>
              <p class="location"><?= get_field("city", $offer->ID); ?><span
                  class="date">Publiée le <?= date('d/m/y', strtotime($post->post_date)) ?></span></p>
            </div>
            </br>
          </div>
        </a>
        <hr>
        <?php
        $compteur_offers++;
        }
        ?>
        <?php endforeach; ?>
      </div>
    </article>
    <article id="requests">
      <h2>Dernières demandes<a href="/annonces/?type=demande"><span>Voir tout ></span></a></h2>
      <div id="requestContainer">
        <?php  $compteur_demandes = 0; ?>
        <?php foreach ($demandes as $demande) : ?>
        <?php
        if($compteur_demandes != 5){
        $model = get_field("modele", $demande->ID);
        $post = get_post($demande->ID);
        ?>
        <a href="<?= get_permalink($demande->ID) ?>" class="clean">
          <div class="requestContent">
            <h3 class="brand" style="display: none;"><?= $model[0]->post_title ?></h3>
            <h4><?= get_the_title($demande->ID); ?></h4>
            <p class="descriptionRequest"><?= helper_description(get_field("description", $demande->ID)); ?></p>
            <p class="location"><?= get_field("city", $demande->ID); ?><span
                class="date">Publiée le <?= date('d/m/y', strtotime($post->post_date)) ?></span></p>
          </div>
        </a>
        <hr class="test">
        <?php
        $compteur_demandes++;
        }
        ?>
        <?php endforeach; ?>
      </div>
    </article>
  </section>


  <section id="popularBrands">
    <h2>Marques populaires<a href="/marques"><span>Voir tout ></span></a></h2>
    <div id="brandContainer">
      <?php
      $query = "SELECT distinct(p.ID), p.post_title, pm.meta_key, pm.meta_value FROM wp_posts as p
                LEFT JOIN wp_postmeta as pm ON pm.post_id = p.ID
                WHERE p.post_status='publish'
                AND p.post_type='marques'
                HAVING meta_key = 'sticky' AND meta_value = '1'
                ORDER BY post_title ASC
                LIMIT 6";

      $brands = $wpdb->get_results($query);
      ?>
      <?php foreach ($brands as $brand) : ?>
      <a href="<?php echo get_permalink($brand->ID) ?>">
        <div class="brands">
          <img src="<?= get_field("sticky_picture", $brand->ID) ?>" alt="<?= $brand->post_title ?>" width="177"
               height="239"/>
          <p><?php echo get_the_title($brand->ID) ?></p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </section>


  <section class="onOne">
    <h1>
      <a href="/blog" class="headTop"><span>À la une</span></a>
      <a href="/blog"><span id="articleList">Voir tout ></span></a>
    </h1>
    <article>
      <div id="left">
        <a href="<?= get_permalink($sticky_posts[0]) ?>">
          <div class="pictureLeft"
               style="background-image: url(<?= get_the_post_thumbnail_url($sticky_posts[0]) ?>)"></div>
          <div id="leftContent">
            <h2 class="truncationTitle"><?= get_the_title($sticky_posts[0]) ?></h2>
            <p class="truncation"><?= get_the_excerpt($sticky_posts[0]) ?></p>
          </div>
        </a>
      </div>
      <div id="right">
        <?php foreach ($sticky_posts as $key => $sticky) : if ($key !== 0 && $key < 3) : ?>
        <a href="<?= get_permalink($sticky_posts[$key]) ?>">
          <div class="rightContent">
            <div class="pictureRight"
                 style="background-image: url(<?= get_the_post_thumbnail_url($sticky_posts[$key]) ?>)"></div>
            <div class="presentation">
              <h2 class="truncationTitle"><?= get_the_title($sticky) ?></h2>
              <p class="truncation"><?= get_the_excerpt($sticky) ?></p>
            </div>
          </div>
        </a>
        <?php endif;
        endforeach; ?>
      </div>
    </article>
  </section>
  <section>
    <?php
    $query = "SELECT p.ID, p.post_title, pm.meta_key, pm.meta_value FROM wp_posts as p
       LEFT JOIN wp_postmeta as pm ON pm.post_id = p.ID
        WHERE p.post_status='publish'
        AND p.post_type='event'
        HAVING pm.meta_key= 'date_fin'
        AND meta_value >= '" . date("Ymd") . "'";

    $events = $wpdb->get_results($query);
    ?>

    <?php if (count($events) > 0) : ?>
    <div id="events">
      <h1>Événements</h1>
      <div id="conteneur_all_slide">
        <?php foreach ($events as $key =>$event) : ?>
        <img class="picture_slider" src="<?= get_field("photo", $event->ID) ?>" alt="<?= get_field("link", $event->ID) ?>">
        <div class="text_slider_event">
          <span
            class="red">Du <?= get_field("date_debut", $event->ID) ?> au <?= get_field("date_fin", $event->ID) ?></span><br>
          <span class="titleEvent"><?= get_the_title($event->ID) ?></span><br>
          <span class="location"><?= get_field("lieu", $event->ID) ?></span><br>
          <span class="lien"><a href="<?= get_field("link", $event->ID) ?>" target="_blank">Cliquez ici pour plus d'informations</a></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </section>
  <script>
    $(document).ready(function () {
      $('#conteneur_all_slide').slick({
        dots: true,
        infinite: true,
        slidesToShow: 2,
        slidesToScroll: 2,
        autoplay: true,
        variableWidth:false,
        responsive: [
          {
            breakpoint: 765,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              dots:true,
              prevArrow:false,
              nextArrow:false,
            }
          }
        ]
      })
      ;
      var slide_number = $('.slick-slide:not(.slick-cloned)').length;
      var total_slide = slide_number;
      if ($(document).width() < 768) {
        console.log("passage en mode téléphone");
        var suppresion = slide_number-1;
        while (suppresion>0){
          $('#conteneur_all_slide').slick('slickRemove', suppresion);
          suppresion-=2;
        }
        total_slide = slide_number/2;
        if (total_slide>11){
          $('.slick-dots').hide();
        }
      }

      $(".picture_slider").on('click',function(){
        console.log($(this).attr("alt"));
        window.open($(this).attr("alt"),'_blank');

      });

    })
    ;
  </script>
@endsection
