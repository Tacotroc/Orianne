<header>
  <div class="banner">
    <div class="headerContainer">
      <div class="slogan">L’automobile a sa source</div>
      <div class="social">
        <a href="https://www.youtube.com/channel/UCtXcpAMCa9da6JbGpYRHVtA">
          <img src="@asset('images/social/youtube-white.png')"/>
        </a>
        <a href="https://www.facebook.com/tacotroc/">
          <img src="@asset('images/social/facebook.png')"/>
        </a>
        <a href="https://www.instagram.com/tacotroc/">
          <img src="@asset('images/social/instagram.png')"/>
        </a>
        <a href="mailto:contact@tacotroc.com">
          <img src="@asset('images/social/mail.png')"/>
        </a>
      </div>
    </div>
  </div>
  <div class="headerContainer searching">
    <a href="<?= get_home_url() ?>" id="logo">
      <img src="@asset('images/logo.png')" alt="logo" id="logoPicture"/>
    </a>
    <form>
      <p id="searchInput">
        <input id="query" type="search" placeholder="Marque, modèle"
               value=""><img
          src="@asset('images/loupe.png')" alt="loupe" id="loupe"/><br/>

        <span><input type="radio" name="search_type" id="radio1"
                     value="offre"<?= !isset($_GET['type']) || !empty($_GET['type']) || $_GET['type'] == "offre" ? ' checked="checked"' : "" ?>/><label
            for="radio1">offres</label></span>

        <span><input type="radio" name="search_type" id="radio2"
                     value="demande"<?= isset($_GET['type']) && $_GET['type'] == "demande" || isset($post->ID) && get_field("type", $post->ID) === "demande" && get_post_type() === "offres" ? ' checked="checked"' : "" ?>/><label
            for="radio2">Demandes</label></span>

        <span><input type="radio" name="search_type" id="radio3"
                     value="echange"<?= isset($_GET['type']) && $_GET['type'] == "echange" || isset($post->ID) && get_field("type", $post->ID) === "demande" && get_post_type() === "echange" ? ' checked="checked"' : "" ?>/><label
            for="radio3">Echanges</label></span>
      </p>
      <p id="selectInput" onclick="showBigMenu()"><span id="changing">Toutes pièces</span><span id="searchImg"><img
            src="@asset('images/arrowDown.png')" alt="flèche vers le bas" id="bottomArrow"/></span></p>

      <p id="button">
        <button type="button" id="search" style="cursor:pointer" data-path="/annonces" data-type="offres">Rechercher
        </button>
      </p>
    </form>
    <div id="bigMenu">
      <p id="all" style="cursor:pointer"><span class="category_main" data-path="">Toutes pièces</span></p>
      <div id="menuContainer">
        <div id="col1">
          <p style="cursor:pointer"><span class="category_main" data-path="mecanique">Mécanique</span></p>
          <ul>
            <?php foreach(helper_category(18) as $category): ?>
            <li class="category" data-path="<?= $category->slug ?>"><?= $category->name ?></li>
            <?php endforeach; ?>
          </ul>
          <p style="cursor:pointer"><span class="category_main" data-path="chassis">Chassis</span></p>
          <ul>
            <?php foreach(helper_category(117) as $category): ?>
            <li class="category" data-path="<?= $category->slug ?>"><?= $category->name ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div id="col2">
          <p style="cursor:pointer"><span class="category_main" data-path="transmission">Transmission</span></p>
          <ul>
            <?php foreach(helper_category(90) as $category): ?>
            <li class="category" data-path="<?= $category->slug ?>"><?= $category->name ?></li>
            <?php endforeach; ?>
          </ul>
          <p style="cursor:pointer"><span class="category_main" data-path="habitacle">Habitacle</span></p>
          <ul>
            <?php foreach(helper_category(180) as $category): ?>
            <li class="category" data-path="<?= $category->slug ?>"><?= $category->name ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div id="col3">
          <p style="cursor:pointer"><span class="category_main" data-path="carrosserie">Carrosserie</span></p>
          <ul>
            <?php foreach(helper_category(159) as $category): ?>
            <li class="category" data-path="<?= $category->slug ?>"><?= $category->name ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div id="col4">
          <p style="cursor:pointer"><span class="category_main" data-path="electricite">Electricité</span></p>
          <ul>
            <?php foreach(helper_category(60) as $category): ?>
            <li class="category" data-path="<?= $category->slug ?>"><?= $category->name ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <a href="<?= get_permalink(83) ?>">
    <div id="new">
      <!--<img src="@asset('images/img_index/Button_annonce.png')" alt="bouton" id="buttonAnnonce" style="display:none"/>-->
      <p><span id="plusNew"><img src="@asset('images/plus.png')" alt="plus" id="plus"/></span><span id="textNew">Déposer une annonce</span>
      </p>
    </div>
  </a>
  <div id="AddAnnoncePhone"><a href="<?= get_permalink(83) ?>"><img id="hiddenImg" src="@asset('images/plus.png')"/></a></div>
</header>
