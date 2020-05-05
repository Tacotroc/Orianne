<article @php post_class() @endphp>
  <main>
    <p class="path" style="cursor:pointer">
      <span onclick="window.location.href='/'">Accueil</span>
      <span class="pathImg"><img src="@asset('images/Base.png')" alt="triangle" id="triangle" /></span>
      <span onclick="window.location.href='/marques/'">Toutes les marques</span>
      <span class="pathImg"><img src="@asset('images/Base.png')" alt="triangle" id="triangle" /></span>
      <span class="pathelt"><?php the_title() ?></span>
    </p>
    <section id="peugeotSection">
        <div id="presentation">
            <div id="peugeotContent">
                <h2><?php the_title() ?></h2>
            <p><?php the_field("description") ?></p>
              </div>
            <div id="peugeotPicture">
                <img src="<?php the_field("image"); ?>" id="picture" />
            </div>
        </div>
        <div id="models">
            <h2>Modèles</h2>
            <div id="modelsContainer">
            <?php

            $brandId = get_the_ID();
            $query = new WP_Query(array(
              'post_type' => 'modeles',
              'post_status' => 'publish',
              'meta_query' => array(
								array(
									'key' => 'marque',
									'value' => '"' . $brandId  . '"',
                  'compare' => 'LIKE'
								)
							)
          ));

          while ($query->have_posts()) {
              $query->the_post();
              $post_id = get_the_ID();
              if(get_field("marque")[0]->ID === $brandId){
                  ?>
                  <a href="<?php echo get_permalink() ?>" class="model">

              <div class="modelpicture">
                  <img src="<?php echo the_post_thumbnail_url() ?>" />
              </div>
              <div class="modelnumber">
                  <p><?php the_title() ?></p>
              </div>
              </a>
          <?php
              }
          }

          wp_reset_query();
              ?>

            </div>
        </div>
    </section>
    <?php
        global $wpdb;

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = (20 * $page) - 20;
        $offset = $offset < 0 ? 0 : $offset;
        $terms = isset($_GET['terms']) ? implode(",", explode("-",$_GET['terms'])) : null;
        $type = isset($_GET['type']) ? $_GET['type'] : 'offre';

        $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele, (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type FROM wp_posts as p WHERE p.post_status='publish' AND p.post_type='offres' HAVING modele IN (SELECT ID FROM wp_posts WHERE wp_posts.`post_type`='modeles' AND post_name like '". get_the_title() ."%' AND post_status='publish') AND type='{$type}' LIMIT 20 OFFSET ".$offset." ";

        $offers = $wpdb->get_results($query);
    ?>
    <section id="Offers">
        <div id="recentOffers">
            <p id="menuOffer">
              <a href="?type=offre"><span <?= $type === "offre" ? 'id="activeOffer"' : ""; ?>>Offres</span></a>
              <a href="?type=demande"><span <?= $type === "demande" ? 'id="activeOffer"' : ""; ?>>Demandes</span></a>
              <a href="?type=echange"><span <?= $type === "echange" ? 'id="activeOffer"' : ""; ?>>Échanges</span></a>
            </p>
            <h2>Offres récentes <?php the_title() ?></h2>
            <article class="offers">
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
        <?php
          $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='marque') as marque FROM wp_posts as p WHERE p.post_type='clubs' AND post_status='publish' HAVING marque='" . get_the_ID() . "'";

          $clubs = $wpdb->get_results($query);
        ?>

        <?php if(count($clubs) > 0): ?>
        <div id="clubs">
            <p id="clubTitle"><img src="@asset('images/hr.png')" alt="ligne horizontale" id="hr" />Clubs</p>
            <?php foreach($clubs as $club): ?>
              <a href="<?= get_field("lien", $club->ID) ?>" target="_blank">
                <div id="club">
                    <div id="contentClub">
                        <h2><?= $club->post_title ?></h2>
                        <p><?= get_field("localisation", $club->ID) ?></p>
                    </div>

                    <div id="logoClub">
                        <img src="<?= get_field("image_du_club", $club->ID) ?>" alt="logo du club" />
                    </div>
                </div>
            </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
</main>

</article>
