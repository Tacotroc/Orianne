@extends('layouts.app')

@section('content')
  <?php
    global $wpdb;

    // Pagination
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = (20 * $page) - 20;
    $offset = $offset < 0 ? 0 : $offset;

    $q = isset($_GET['q']) ? $_GET['q'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : 'offre';

    // Terms
    $term = get_queried_object();
    $terms = get_term_children($term->term_id, 'category_piece');
    $terms[] = $term->term_id;
    $terms = implode(',', $terms);

    $terms_finder = isset($_GET['terms']) ? implode(",", explode("-",$_GET['terms'])) : null;
    if($terms_finder === null){
      $terms_finder = $terms;
    } else {
      $terms_finder = $terms_finder;
    }

    $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele, (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type FROM wp_posts as p WHERE p.post_status='publish' AND p.ID IN (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id IN (".$terms_finder.")) HAVING modele IN (SELECT ID FROM wp_posts WHERE wp_posts.`post_type`='modeles' AND post_name like '".$q."%' AND post_status='publish') AND type= '".$type."' LIMIT 20 OFFSET ".$offset." ";

    $offers = $wpdb->get_results($query);

    // Results
    $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele, (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type FROM wp_posts as p WHERE p.post_status='publish' AND p.post_type='".$type."' AND p.ID IN (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id IN (".$terms_finder.")) HAVING modele IN (SELECT ID FROM wp_posts WHERE wp_posts.`post_type`='modeles' AND post_name like '".$q."%' AND post_status='publish') AND type= '".$type."' ";

    $nbResults = $wpdb->get_results($query);
    $nbResults = count($nbResults);
  ?>
  <section id="results">
    <article class="offers">
        <h2>Résultats de recherches</h2>
        <p class="resultsNum"><?= $nbResults ?> résultats <?php if($q !== "") echo "pour " . $q ?></p>

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


    <article class="filterResults">
        <p class="filterhr"><img src="@asset('images/img_research/hr.png')" alt="ligne" class="hr" /><img src="@asset('images/img_research/filter.png')" alt="picto filrer" class="filter" />Filtrer</p>
        <p class="filterp">Pour filtrer nos annonces par type de pièces détachées, merci d'utiliser les filtres ci-dessous :</p>

        <?php
            $sub = array(159, 117, 60, 18);
            if(in_array($term->parent, $sub)){
              $termsSidebar = get_terms(array(
                'taxonomy' => 'category_piece',
                'hide_empty' => false,
                'parent' => $term->term_id
              ));

                echo '<ul id="researchUl" class="researchUl" style="display: block">';
                foreach($termsSidebar as $sub){
                  ?>
                    <li><input type="checkbox" id="term_<?= $sub->term_id ?>" /><label for="term_<?= $sub->term_id ?>"><?= $sub->name ?></label></li>
                  <?php
                }
                echo '</ul>';
            } else {
              $termsSidebar = get_terms(array(
                'taxonomy' => 'category_piece',
                'hide_empty' => false,
                'parent' => $term->term_id
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
            }
        ?>
    </article>
  </section>
@endsection
