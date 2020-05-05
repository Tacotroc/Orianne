<?php
  $author_id = $post->post_author;
?>

<p class="path" style="cursor:pointer">
<span onclick="window.location.href='/'">Accueil</span>
<span class="pathImg"><img src="@asset('images/img_research/Base.png')" alt="triangle" id="triangle" /></span>
  <span class="pathelt"><?= get_the_title() ?></span></p>
  <?php if(get_the_post_thumbnail_url() != ""): ?>
    <p id="articlePicture">
      <img src="<?= get_the_post_thumbnail_url() ?>" />
    </p>
  <?php endif; ?>
</p>

<h1 id="articleH1"><?= get_the_title() ?></h1>

<section id="articleFirst">
    <article id="ArticleContent">
        <div id="infoWriter">
            <div id="avatar">
                <div id="avatarPic">
                    <img src="<?= get_avatar_url($author_id) ?>" alt="avatar" id="pic" />
                </div>
                <div id="avatarContent">
                    <p><?= get_the_author(); ?></p>
                </div>
            </div>
            <img src="@asset('images/img_article/barre.png')" alt="barre verticale" class="barre" />

            <div id="comm">
                <p><span id="numberCom"><?= get_comments_number() ?></span><br> commentaires</p>
            </div>
        </div>

        <div id="articleText">
            <?php the_content() ?>
        </div>
    </article>

    <article id="articlePop">
        <p id="articleHr"><img src="@asset('images/img_article/hr.png')" alt="barre horizontale" id="hr" /> <span>Derniers articles</span></p>

        <?php
          global $wpdb;
          $query = 'SELECT * FROM wp_posts WHERE post_status = "publish" AND post_type = "post" AND ID not in ('.$post->ID.') ORDER BY post_date LIMIT 10';
          $lastSingle = $wpdb->get_results($query);
        ?>

        <?php foreach($lastSingle as $last): ?>
        <a href="<?= get_permalink($last->ID) ?>">
          <div id="articleResume">
              <div id="resumecontent">
                  <h3><?= get_the_title($last->ID) ?></h3>
                  <p> <?=  date('d/m/y', strtotime($last->post_date))?></p>
              </div>
              <div id="resumePicture" style="background-image: url('<?= get_the_post_thumbnail_url($last->ID, 'medium') ?>');"></div>
          </div>
        </a>
        <?php endforeach; ?>

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
    </article>
</section>

<section id="articleCom">
    @php comments_template('/partials/comments.blade.php') @endphp
     <!--<div id="nextPrev">
        <a href="#">
            <div id="prev">
                <p onmouseover="hoverArrowLeft()" onmouseout="OutArrowLeft()"><img src="@asset('images/img_article/Left_Arrow_Icon.png')" alt="flèche gauche" id="leftArrow" />Article précédent</p>
            </div>
        </a>
        <img src="@asset('images/img_article/barre.png')" alt="barre" class="barre" />
        <a href="#">
            <div id="next">
                <p onmouseover="hoverArrowRight()" onmouseout="OutArrowright()">Article suivant<img src="@asset('images/img_article/Left_Arrow_Icon.png')" alt="flèche droite" id="rightArrow" /></p>
            </div>
        </a>
    </div>-->
</section>
