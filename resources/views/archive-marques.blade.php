@extends('layouts.app')
<?php
$brands = getModelsAlpha();
?>
@section('content')
<p class="path" style="cursor:pointer">
  <span onclick="window.location.href='/'">Accueil</span>
  <span class="pathImg"><img src="@asset('images/img_research/Base.png')" alt="bouton partager" id="share" /></span>
  <span class="pathelt">Toutes les marques</span>
</p>

<div id="flex_position">


  <section id="allBrands">
    <article id="BrandsByLetters">
      <h1>Toutes les marques</h1>
      <p id="letters">
        <?php foreach ($brands as $key => $brand) : ?>
          <?php if (count($brand) > 0) : ?>
            <a href="#letter<?= ucfirst($key) ?>"><span class="letterSpan"><?= ucfirst($key) ?></span></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </p>

      <?php foreach ($brands as $key => $brand) : ?>
        <?php if (count($brand) > 0) : ?>
          <?php $nk = 0; ?>
          <h2 id="letter<?= ucfirst($key) ?>"><?= ucfirst($key) ?></h2>
          <div class="flexUl">
            <?php for ($i = 0; $i < ceil(count($brand) / 1); $i++) : ?>
              <ul>
                <?php for ($n = 0; $n < 1; $n++) : ?>
                  <li>
                    <a href="<?= get_permalink($brand[$nk]->id) ?>">
                      <span style="font-weight: bold"><?= $brand[$nk]->title ?></span>
                    </a>
                  </li>
                  <?php $nk++; ?>
                <?php endfor; ?>
              </ul>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </article>
  </section>
  <section id="imageAjoutee">
    <div id="doubleImages">
      <div>
        <img id="img1marque" src="@asset('images/SIGLES_Emblemes_Marques_500.png')" alt="">
      </div>
      <div>
        <img id="img1bismarque" src="@asset('images/Logos-marques-anciennes.jpg')" alt="">
      </div>
      <div>
        <img id="img2marque" src="@asset('images/Panneaux_marques_expo_salon_500.png')" alt="">
      </div>
    </div>
  </section>
</div>
@endsection
