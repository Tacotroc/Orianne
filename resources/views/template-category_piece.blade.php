{{--
  Template Name: Index annonces
--}}

<?php
    // Pagination

    //$page = (isset($_GET['page']) ? $_GET['page'] : 1);

    if(isset($_GET['page']) && $_GET['page']>0 && $_GET['page']<=$nb_pages){
      $page = $_GET['page'];
    }
    else {
      $page = 1;
    }

    $offset = (20 * $page) - 20;
    $offset = $offset < 0 ? 0 : $offset;
    $terms = isset($_GET['terms']) ? implode(",", explode("-",$_GET['terms'])) : null;

    //$q = isset($_GET['q']) ? $_GET['q'] : '';
    //$q = str_replace(' ', '%', $q);
    $type = isset($_GET['type']) ? $_GET['type'] : 'offre';

    $searching = search($q, $terms, $type, $offset);

    $offers = $searching["offers"];
    $nbResults = $searching["nbResults"];

    $q = str_replace('%', ' ', $q);

  ?>

@extends('layouts.app')

@section('content')
  <section id="results">
    <article class="offers">
        <h2>Résultats de recherches</h2>
        <p class="resultsNum"><?= $nbResults ?> résultats <?php if($q !== "") echo "pour " . $q ?></p>

        <div class="offersContainer">
          <?php foreach($offers as $offer):
            $model = get_field("modele", $offer->ID);
            $post = get_post($offer->ID);
            $image = get_field("picture_1", $offer->ID);
          ?>
          <a href="<?= get_permalink($offer->ID) ?>" class="clean">
            <div class="offer">
                <div class="offerPicture" style="background-image: url(<?= $image["url"]; ?>)"></div>
                <div class="offerContent">
                    <h3 class="brand"><?= isset($model[0]->post_title) ? $model[0]->post_title : "Marque inconnue" ?><span class="price"><?= get_field("prix", $offer->ID); ?><?= !empty(get_field("prix", $offer->ID)) ? "€ " : "" ?></span></h3>
                    <h4><?= get_the_title($offer->ID); ?></h4>
                    <p class="description"><?= helper_description(get_field("description", $offer->ID)); ?></p>
                    <p class="location"><?= get_field("city", $offer->ID); ?><span class="date">Publiée le <?=  date('d/m/y', strtotime($post->post_date))?></span></p>
                </div>
            </div>
          </a>
          <hr>
          <?php endforeach; ?>
        </div>

        <?php
          // $total, $currentPage, $nbPerPage, $path
          $path = $terms !== null ? $path . "&terms=" . $terms : $path;
          $path = $q !== null ? $path . "&q=" . $q : $path;
          $path = $path . "&page=";
          $path = "/annonces/?type=" . $type;
          paginationBlog($nbResults, $page, 20, $path);
        ?>

    </article>

    <article class="filterResults">
        <p class="filterhr"><img src="@asset('images/img_research/hr.png')" alt="ligne" class="hr" /><img src="@asset('images/img_research/filter.png')" alt="picto filrer" class="filter" />Filtrer</p>
        <p class="filterp">Pour filtrer nos annonces par type de pièces détachées, merci d'utiliser les filtres ci-dessous :</p>

        <?php
          $termsSidebar = get_terms(array(
            'taxonomy' => 'category_piece',
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
@endsection
