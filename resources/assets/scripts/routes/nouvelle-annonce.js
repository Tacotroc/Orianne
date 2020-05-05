export default {
  init() {
    // Load categories
    actions.fetchTaxonomyByParent(0, categories => {
      $("#category option").remove();
      $("#category").append(
        '<option value="" disabled selected>Sélectionner une catégorie de pièce</option>'
      );
      categories.map(category => {
        $("#category").append(
          `<option value="${category.term_id}">${category.name}</option>`
        );
      });
    });

    // Counter of chars
    $("#counter").keyup(function() {
      var nombreCaractereTextarea = $(this).val().length;
      var nombreCaractere = 1000 - nombreCaractereTextarea;
      var msg = nombreCaractere;
      $("#compteur").text(msg);
    });

    // Actions
    actions.changeCategory();
    actions.changeSubCategory();
    actions.changePicture();
    actions.announcementValidation();
    actions.onChangeType();
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
    $(".js-models-ajax").select2({
      placeholder: "Sélectionner un modèle",
      ajax: {
        url: "/wp-admin/admin-ajax.php?action=fetch_models",
        dataType: "json"
      }
    });

    $(".js-brands-ajax").select2({
      placeholder: "Sélectionner une marque",
      ajax: {
        url: "/wp-admin/admin-ajax.php?action=fetch_brands",
        dataType: "json"
      }
    });
  }
};

const actions = {
  resetInputHidden: () => {
    $("input[id=price]").prop("disabled", false);
    $(".priceShow").show();

    $(".colisShow input").prop("disabled", false);
    $(".colisShow").show();
  },
  onChangeType: () => {
    $("input:radio[name=announcementType]").on("change", function() {
      let type = $("input[name='announcementType']:checked").val();

      actions.resetInputHidden();

      if (type === "demande") {
        // Disable price, and colis informations
        $("input[id=price]").prop("disabled", true);
        $(".priceShow").hide();

        // Disable colis informations
        $(".colisShow input").prop("disabled", true);
        $(".colisShow").hide();
      }

      if (type === "echange") {
        $("input[id=price]").prop("disabled", true);
        $(".priceShow").hide();
      }
    });
  },
  changePicture: () => {
    $(".fileChosen").css(
      "background-image",
      `url(${$(".fileChosen").attr("data-default")})`
    );

    $(".fileDiv input").on("change", function() {
      $(this)
        .next("img")
        .css("display", "block");
      $(this)
        .parent()
        .next(".fileChosen")
        .css(
          "background-image",
          `url(${window.URL.createObjectURL($(this)[0].files[0])})`
        );
    });

    $(".fileDiv .cross").on("click", function() {
      $(this).hide();
      let chosen = $(this)
        .parent()
        .next(".fileChosen");

      $(this)
        .prev("input")
        .val("");

      $(chosen).css(
        "background-image",
        `url(${$(chosen).attr("data-default")})`
      );
    });
  },
  announcementValidation: () => {
    $("#announcement__validation .button").on("click", () => {
      $("#announcement__validation").removeClass("active");
    });
  },
  fetchTaxonomyByParent: (parent, callback) => {
    $.getJSON(
      "/wp-admin/admin-ajax.php",
      {
        action: "fetch_taxonomy",
        p: parent
      },
      function(response) {
        callback(response);
      }
    );
  },
  changeCategory: () => {
    $("#category").on("change", function() {
      actions.fetchTaxonomyByParent($(this).val(), categories => {
        $("#subcategory option").remove();
        $("#subcategory").append(
          '<option value="" disabled selected>Sélectionner une sous catégorie de pièce</option>'
        );
        categories.map(category => {
          $("#subcategory").append(
            `<option value="${category.term_id}">${category.name}</option>`
          );
        });

        $(".subcategory").show();
        $(".subsubcategory").hide();
      });
    });
  },
  changeSubCategory: () => {
    $("#subcategory").on("change", function() {
      actions.fetchTaxonomyByParent($(this).val(), categories => {
        $("#subsubcategory option").remove();
        $("#subsubcategory").append(
          '<option value="" disabled selected>Sélectionner une catégorie finale</option>'
        );
        categories.map(category => {
          $("#subsubcategory").append(
            `<option value="${category.term_id}">${category.name}</option>`
          );
        });

        $(".subsubcategory").show();
      });
    });
  }
};
