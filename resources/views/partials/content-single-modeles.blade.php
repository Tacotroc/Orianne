<?php
    global $wpdb;

    // Pagination
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = (20 * $page) - 20;
    $offset = $offset < 0 ? 0 : $offset;
    $terms = isset($_GET['terms']) ? implode(",", explode("-",$_GET['terms'])) : null;


    $q = str_replace(" ", "%", get_the_title());
    $type = isset($_GET['type']) ? $_GET['type'] : 'offre';

    $searching = search($q, $terms, $type, $offset);

    $offers = $searching["offers"];
    $nbResults = $searching["nbResults"];

    $q = str_replace('%', ' ', $q);

    $nGalery = 0;
    for($image=1;$image<11;$image++):
      if(!empty(get_field("galerie_{$image}", get_the_ID()))):
        $nGalery++;
      endif;
    endfor;
  ?>

<article @php post_class() @endphp>
<main>

<p class="path" style="cursor:pointer">
  <span onclick="window.location.href='/'">Accueil </span>
  <span class="pathImg"><img src="@asset('images/Base.png')" id="triangle" /></span>
  <span onclick="window.location.href='/marques'">Marque</span>
  <span class="pathImg"><img src="@asset('images/Base.png')" id="triangle" /></span>
  <span class="pathelt"><?php the_title() ?></span>
</p>

<section id="presentationModel">
    <div id="picture201">
      <?php if(!empty(get_field("cover"))): ?>
        <img src="<?php the_field("cover") ?>" alt="<?= the_title() ?>" />
      <?php endif; ?>
    </div>
    <div id="historyDiv">
        <h1><?php the_title() ?></h1>
        <p id="date201"><?php the_field("date") ?></p>
        <p id="history"><?php the_field("texte") ?></p>
    </div>
    <div id="pictureContent">

        <?php if(!empty(get_field("galerie_1"))): ?>
          <div class="picture" id="picture1">
              <p id="photoNumber">+<?= $nGalery ?></p>
              <img src="<?php the_field("galerie_1"); ?>" class="mainPicture" alt="miniature" id="car1" />
              <p class="title">Photos</p>
          </div>
        <?php endif; ?>

        <?php if(the_field("video")){
            ?>
        <div class="picture" id="picture2">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mini2.png" class="mainPicture" alt="miniature" id="car2" />

            <p class="title">Vidéo</p>
        </div>
            <?php
        }
        ?>
        <?php if(the_field("modele_3d")){
            ?>
            <div class="picture" id="picture3">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mini3.png" class="mainPicture" alt="miniature" id="car3" />
            <p class="title">360°</p>
        </div>
            <?php
        }
        ?>

    </div>

</section>

<div id="carouselPic">
    <p id="croix"><a class="cross" href="#spot"></a></p>

  <div class="slideshow">
      <div class="btn-bar">
          <p id="prev" onclick="plusSlide(-1)"><a class="arrow" href="#spot" style="transform:rotate(180deg)"></a></p>
          <p id="next" onclick="plusSlide(+1)"><a class="arrow" href="#spot"></a></p>
      </div>

      <div class=" slideshow-container" id="slide">
          <?php for($image=1;$image<11;$image++): ?>
            <?php if(!empty(get_field("galerie_{$image}", get_the_ID()))): ?>
              <div class="mySlides<?= $image === 1 ? ' active' : ''; ?>" id="indice-<?= $image ?>"> <img src="<?= get_field("galerie_{$image}", get_the_ID()) ?>" id="event1" /></div>
            <?php endif; ?>
          <?php endfor; ?>
      </div>
  </div>
</div>

<script>
    var slideIndex = 1;
    function plusSlides(n) {
      showSlides((slideIndex += n));
    }

    function showSlides(n) {
      let i;
      let slides = document.getElementsByClassName("mySlides");
      let paragraph = document.getElementsByClassName("changep");

      if (n > slides.length && n > paragraph.length) {
        slideIndex = 1;
      }
      if (n < 1) {
        slideIndex = slides.length;
        slideIndex = paragraph.length;
      }
      for (i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
      }

      for (i = 0; i < paragraph.length; i++) {
        paragraph[i].style.display = "none";
      }

      slides[slideIndex - 1].classList.toggle("active");

      paragraph[slideIndex - 1].style.display = "block";
    }

    function plusSlide(n) {
      showSlide((slideIndex += n));
    }

    function showSlide(n) {
      let i;
      let slide = document.getElementsByClassName("mySlides");

      if (n > slide.length) {
        slideIndex = 1;
      }
      if (n < 1) {
        slideIndex = slide.length;
      }
      for (i = 0; i < slide.length; i++) {
        slide[i].classList.remove("active");
      }

      slide[slideIndex - 1].classList.toggle("active");
    }
  </script>

<section id="OffersModel">
    <div id="recentOffers">
        <p id="menuOffer">
          <a href="?type=offre"><span <?= $type === "offre" ? 'class="activeOffer"' : ""; ?>>Offres</span></a>
          <a href="?type=demande"><span <?= $type === "demande" ? 'class="activeOffer"' : ""; ?>>Demandes</span></a>
          <a href="?type=echange"><span <?= $type === "echange" ? 'class="activeOffer"' : ""; ?>>Échanges</span></a>
        </p>

        <article class="offers" id="lastOffers">
          <h2>Dernières annonces <?php the_title() ?></h2>
          <div class="offersContainer">
            <?php foreach($offers as $offer): ?>
            <?php
              $model = get_field("modele", $offer->ID);
              $post = get_post($offer->ID);
              $image = get_field("picture_1", $offer->ID);
            ?>
            <a href="<?= get_permalink($offer->ID) ?>" class="clean">
              <div class="offer">
                    <div class="offerPicture" style="background-image: url(<?= $image["url"]; ?>)"></div>
                    <div class="offerContent">
                        <h3 class="brand"><?= isset($model[0]->post_title) ? $model[0]->post_title : "Marque inconnue" ?><span class="price"><?= get_field("prix", $offer->ID); ?><?= !empty(get_field("prix", $offer->ID)) ? "€" : "" ?></span></h3>
                        <h4><?= get_the_title($offer->ID); ?></h4>
                        <p class="description"><?= helper_description(get_field("description", $offer->ID)); ?></p>
                        <p class="location"><?= get_field("city", $offer->ID); ?><span class="date">Publiée le <?=  date('d/m/y', strtotime($post->post_date))?></span></p>
                    </div>
                </div>
              </a>
              <hr>
            <?php endforeach; ?>
          </div>

        </article>
    </div>

    <article class="filterResults">
      <p class="filterhr"><img src="@asset('images/img_research/hr.png')" alt="ligne" class="hr" />Filtrer</p>
      <p class="filterp">Pour filtrer nos annonces par type de pièces détachées, merci d'utiliser les filtres ci-dessous :</p>

      <?php
        $termsSidebar = get_terms(array(
          'taxonomy' => 'category_piece',
          'hide_empty' => false,
          'parent' => 0
        ));

        foreach($termsSidebar as $sub){ ?>
          <p class="ultitle main_filters" style="cursor:pointer"><?= $sub->name ?><span class="arrow"><img src="@asset('images/img_research/arrow-down.png')" alt="flèche vers le bas" id="arrowResearch" /></span></p>
          <?php
          $termsSubSub = get_terms(array(
            'taxonomy' => 'category_piece',
            'hide_empty' => false,
            'parent' => $sub->term_id
          ));

          echo '<ul id="researchUl" class="researchUl">';
          foreach($termsSubSub as $subsub){
            ?>
              <li><input type="checkbox" id="term_<?= $subsub->term_id ?>" /><label for="term_<?= $subsub->term_id ?>"><?= $subsub->name ?></label></li>
            <?php
          }
          echo '</ul>';
        }
      ?>
    </article>
</section>
</main>
</article>
