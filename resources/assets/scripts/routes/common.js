var _ = require("lodash");
var slick = require("slick-carousel");

export default {
  init() {
    // Big menu
    $("#selectInput").on("click", () => actions.showBigMenu());
    actions.search();
    actions.showFilters();
    actions.onFilter();
    actions.showFiltersByTerms();
    actions.slideShowModel();

    actions.responsive();

    // Annoucement carrousel
    if ($("#announcePicture").length > 0) {
      $("#announcePicture").slick({
        adaptiveHeight: false
      });
    }
  },
  finalize() {}
};
const helper = {
  getHomeUrl: () => {
    return $("#home_url").val();
  }
};

const actions = {
  responsive: () => {
    $("#query").focus(function() {
      $(".searching").addClass("active");
    });
  },
  slideShowModel: () => {
    $(".mainPicture, #photoNumber").on("click", () => {
      $("#carouselPic").css("display", "flex");
    });
    $("#croix").on("click", () => {
      $("#carouselPic").css("display", "none");
    });

    $(".mySlides").css("height", $(window).height());
  },
  showBigMenu: () => {
    let bigMenu = document.getElementById("bigMenu");
    if (bigMenu.style.maxHeight == "2000px") {
      bigMenu.style.maxHeight = "0";
      bigMenu.style.transition = "0.2s all ease-in-out";
      bigMenu.style.visibility = "hidden";
    } else {
      bigMenu.style.maxHeight = "2000px";
      bigMenu.style.visibility = "visible";
    }
  },
  search: () => {
    $(document).on(
      "click",
      "#bigMenu li.category, #bigMenu span.category_main",
      function() {
        // make search redirection
        $("#changing").html($(this).html());
        let bigMenu = document.getElementById("bigMenu");
        bigMenu.style.maxHeight = "0";
        bigMenu.style.transition = "0.5s all ease-in-out";
        bigMenu.style.visibility = "hidden";
        $("#search").attr("data-path", $(this).attr("data-path"));
      }
    );

    $(document).on("keyup", "#query", function(e) {
      if (e.keyCode === 13) {
        $("#search").click();
      }
    });

    $(document).on("click", "#search", function() {
      // make search redirection
      let query = $("#query").val();
      let path =
        $(this).attr("data-path") !== "/annonces"
          ? "/annonces/" + $(this).attr("data-path")
          : "/annonces";
      let type = $('input[name="search_type"]:checked').val();

      window.location = `${helper.getHomeUrl()}${path}/?q=${query}&type=${type}`;
    });
  },
  showFilters: () => {
    $(document).on("click", ".main_filters", function() {
      let selector = $(this).next(".researchUl");
      if ($(selector).hasClass("show")) {
        $(this)
          .find("img")
          .css("transform", "rotate(0deg)");
        $(selector).removeClass("show");
      } else {
        $(selector).addClass("show");
        $(this)
          .find("img")
          .css("transform", "rotate(180deg)");
      }
    });
  },
  showFiltersByTerms: () => {
    if ($(".filterResults").length > 0) {
      var url = new URL(window.location.href);
      var terms = url.searchParams.get("terms");

      if (terms !== null) {
        terms = terms.split("-");
        terms.forEach(term => {
          $(`#term_${term}`).prop("checked", true);
          $(`#term_${term}`)
            .parents("ul")
            .css("display", "block");
        });
      }
    }
  },
  onFilter: () => {
    $(document).on("click", ".researchUl label", function() {
      let term_id = $(this)
        .attr("for")
        .replace("term_", "");

      var active = $(`#term_${term_id}`).attr("checked");

      var url = new URL(window.location.href);
      var terms = url.searchParams.get("terms");
      var q = url.searchParams.get("q");
      var type =
        url.searchParams.get("type") !== null
          ? url.searchParams.get("type")
          : "demandes";

      if (terms === null && active === undefined) {
        window.location = `?terms=${term_id}&q=${q}&type=${type}`;
      }

      if (terms !== null && active === undefined) {
        if (terms !== "") {
          terms = terms.split("-");
          terms.push(term_id);
          terms = _.uniq(terms);
          terms = terms.join("-");
        } else {
          terms = term_id;
        }

        window.location = `?terms=${terms}&q=${q}&type=${type}`;
      }

      if (terms === null && active === "checked") {
        window.location = `?q=${q}&type=${type}`;
      }

      if (terms !== null && active === "checked") {
        terms = terms.split("-");
        _.remove(terms, function(el) {
          return el === term_id;
        });

        terms = _.uniq(terms);
        terms = terms.join("-");

        if (terms === "") {
          window.location = `?q=${q}&type=${type}`;
        } else {
          window.location = `?terms=${terms}&q=${q}&type=${type}`;
        }
      }
    });
  }
};
