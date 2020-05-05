<?php
  global $wpdb;

  $category = get_the_category();

  $page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
  $offset = (20 * $page) - 20;
  $offset = $offset < 0 ? 0 : $offset;

  $query = 'SELECT ID FROM wp_posts WHERE post_status = "publish" AND post_type = "post" AND ID in (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id="' . $category[0]->term_id . '") LIMIT 20 OFFSET '.$offset;
  $singles = $wpdb->get_results($query);

  $query = 'SELECT ID FROM wp_posts WHERE post_status = "publish" AND post_type = "post" AND ID in (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id="' . $category[0]->term_id . '")';
  $nbResults = $wpdb->get_results($query);
  $nbResults = count($nbResults);
?>

@extends('layouts.app')
@section('content')
<p class="path" style="cursor:pointer">
  <span onclick="window.location.href='/'">Accueil</span>
  <span class="pathImg"><img src="@asset('images/img_research/Base.png')" alt="triangle" id="triangle" /></span>
  <span class="pathelt">Catégorie <?= $category[0]->cat_name ?></span>
</p>

<section class="bottom">
    <article id="last">
        <h1>Articles de la catégorie <?= $category[0]->cat_name ?><h1>
        <?php foreach($singles as $single): ?>
          <a href="<?= get_permalink($single->ID) ?>">
            <div class="articles">
                <div class="articlePicture" style="background-image: url(<?= get_the_post_thumbnail_url($single->ID, 'medium') ?>)"></div>
                <div class="articleContent">
                    <h2 class="truncationTitle"><?= get_the_title($single->ID); ?></h2>
                    <p class="truncation"><?= get_the_excerpt($single->ID); ?></p>
                </div>
            </div>
          </a>
        <?php endforeach; ?>
    </article>
    <article class="publish">
      <p id="articleHr"><img src="@asset('images/img_article/hr.png')" alt="barre horizontale" id="hr" /> <span>Catégories</span></p>
        <ul class="singleCategories">
          <?php
            $categories = get_categories( array(
              'orderby' => 'name',
              'parent'  => 0
            ) );

            $i = 0;
            while($i < count($categories)){
              printf( '<li><a href="%1$s"> %2$s (%3$s)</a></li>',
                  esc_url( get_category_link( $categories[$i]->term_id ) ),
                  esc_html( $categories[$i]->name ),
                  esc_html( $categories[$i]->count )
              );
              $i++;
            }
          ?>
        </ul>
      <p id="articleHrLow"><img src="@asset('images/img_article/hr.png')" alt="barre horizontale" id="hr" />
      <p id="imgPublish"><img src="@asset('images/img_articleList/illustration_car.png')" alt="illustration" id="illustration" /></p>

      <h2>Partagez vos connaissances avec notre communauté de membres.</h2>
      <p>Partagez vos connaissances sur les automobiles anciennes qui comptent pour vous. Votre article peut être court ou long, sérieux ou amusant, rapporté ou jugé - c’est la qualité de votre perspective qui compte.</p>
      <h3>DIRECTIVES EDITORIALES</h3>
      <p><span>1.</span> les écrits et images doivent être les vôtres, ou doivent être utilisés avec permission ou citation.</p>
      <p><span>2.</span> Tacotroc n'accepte pas de publicité. Ne commercialisez pas vous-même ou d’autres produits, ne présentez pas de publicités ou n’incluez aucune demande de dons ou d’inscriptions par courriel.</p>
      <p> <span>3.</span> Nos lecteurs moyens cherchent des informations relatives aux voitures anciennes. Nous vous recommandons donc de publier vos meilleures analyses, essais, tutos et idées.</p>
      <p id="buttonArticle">
      <a href="https://www.facebook.com/tacotroc/"><button type="button">Publier un article</button></a>
      </p>
    </article>
</section>

<?php
  if(count($singles) > 0):
    // $total, $currentPage, $nbPerPage, $path
    paginationBlog($nbResults, $page, 20, "/category/{$category[0]->slug}/page/");
  endif;
?>
@endsection

