{{--
  Template Name: Articles Listes
--}}

<?php
  global $wpdb;

  $sticky_posts = get_option('sticky_posts');
  $sticky = implode(",", $sticky_posts);

  $page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
  $offset = (20 * $page) - 20;
  $offset = $offset < 0 ? 0 : $offset;

  $query = 'SELECT ID FROM wp_posts WHERE post_status = "publish" AND post_type = "post" AND ID not in ('.$sticky.') ORDER BY post_date DESC LIMIT 20 OFFSET '.$offset;
  $singles = $wpdb->get_results($query);

  $query = 'SELECT ID FROM wp_posts WHERE post_status = "publish" AND post_type = "post" AND ID not in ('.$sticky.')';
  $nbResults = $wpdb->get_results($query);
  $nbResults = count($nbResults);
?>

@extends('layouts.app')
@section('content')
<p class="path" style="cursor:pointer">
  <span onclick="window.location.href='/'">Accueil</span>
  <span class="pathImg"><img src="@asset('images/img_research/Base.png')" alt="triangle" id="triangle" /></span>
  <span class="pathelt"> Nos articles</span>
</p>

<?php if(count($sticky_posts) > 0 && $page === 1): ?>
<section class="onOne">
    <h1>À la une </h1>
    <article>
        <div id="left">
          <a href="<?= get_permalink($sticky_posts[0]) ?>">
            <div class="pictureLeft" style="background-image: url(<?= get_the_post_thumbnail_url($sticky_posts[0]) ?>)"></div>
              <div id="leftContent">
                  <h2 class="truncationTitle"><?= get_the_title($sticky_posts[0]) ?></h2>
                  <p class="truncation"><?= get_the_excerpt($sticky_posts[0]) ?></p>
              </div>
            </a>
        </div>
        <div id="right">
          <?php foreach($sticky_posts as $key => $sticky): if($key !== 0 && $key < 3): ?>
            <a href="<?= get_permalink($sticky_posts[$key]) ?>">
              <div class="rightContent">
                  <div class="pictureRight" style="background-image: url(<?= get_the_post_thumbnail_url($sticky_posts[$key]) ?>)"></div>
                  <div class="presentation">
                      <h2 class="truncationTitle"><?= get_the_title($sticky) ?></h2>
                      <p class="truncation"><?= get_the_excerpt($sticky) ?></p>
                  </div>
              </div>
            </a>
          <?php endif; endforeach; ?>
        </div>
    </article>
</section>
<?php endif; ?>

<section class="bottom">
    <article id="last">
        <p id="hrLast">
            Derniers <img src="@asset('images/img_articleList/hr.png')" alt="ligne horizontale" id="hr" />
        </p>
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

            foreach ( $categories as $category ) {
              printf( '<li><a href="%1$s">%2$s (%3$s)</a></li>',
                  esc_url( get_category_link( $category->term_id ) ),
                  esc_html( $category->name ),
                  esc_html( $category->count )
              );
            }
          ?>
        </ul>
      <p id="articleHrLow"><img src="@asset('images/img_article/hr.png')" alt="barre horizontale" id="hr" />
      <p id="imgPublish"><img src="@asset('images/img_articleList/illustration_car.png')" alt="illustration" id="illustration" /></p>
      <h2>Partagez vos connaissances avec notre communauté de membres.</h2>
      <p>Partagez vos connaissances sur les automobiles anciennes qui comptent pour vous. Votre article peut être court ou long, sérieux ou amusant, rapporté ou jugé - c’est la qualité de votre perspective qui compte.</p>
      <h3>DIRECTIVES EDITORIALES</h3>
      <p><span>1. </span> les écrits et images doivent être les vôtres, ou doivent être utilisés avec permission ou citation.</p>
      <p><span>2.</span> Tacotroc n'accepte pas de publicité. Ne commercialisez pas vous-même ou d’autres produits, ne présentez pas de publicités ou n’incluez aucune demande de dons ou d’inscriptions par courriel.</p>
      <p> <span>3.</span> Nos lecteurs moyens cherchent des informations relatives aux voitures anciennes. Nous vous recommandons donc de publier vos meilleures analyses, essais, tutos et idées.</p>
      <p id="buttonArticle"><button type="button" onclick="window.location.href='mailto:contact@tacotroc.com'">Publier un article</button></p>
    </article>
</section>

<?php
  if(count($singles) > 0):
    // $total, $currentPage, $nbPerPage, $path
    paginationBlog($nbResults, $page, 20, "/blog-automobile-ancienne/page/");
  endif;
?>
@endsection
