/*eslint-disable */
import "jquery";
import "select2";
var _ = require("lodash");

(function() {
  if (jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd)
    var e = jQuery.fn.select2.amd;
  return (
    e.define("select2/i18n/fr", [], function() {
      return {
        errorLoading: function() {
          return "Les résultats ne peuvent pas être chargés.";
        },
        inputTooLong: function(e) {
          var t = e.input.length - e.maximum;
          return "Supprimez " + t + " caractère" + (t > 1 ? "s" : "");
        },
        inputTooShort: function(e) {
          var t = e.minimum - e.input.length;
          return "Saisissez au moins " + t + " caractère" + (t > 1 ? "s" : "");
        },
        loadingMore: function() {
          return "Chargement de résultats supplémentaires…";
        },
        maximumSelected: function(e) {
          return (
            "Vous pouvez seulement sélectionner " +
            e.maximum +
            " élément" +
            (e.maximum > 1 ? "s" : "")
          );
        },
        noResults: function() {
          return "Aucun résultat trouvé";
        },
        searching: function() {
          return "Recherche en cours…";
        },
        removeAllItems: function() {
          return "Supprimer tous les articles";
        }
      };
    }),
    { define: e.define, require: e.require }
  );
})();

// Import everything from autoload
import "./autoload/**/*";

// import local dependencies
import Router from "./util/Router";
import common from "./routes/common";
import home from "./routes/home";
import aboutUs from "./routes/about";
import nouvelleAnnonce from "./routes/nouvelle-annonce";
import proposition from "./routes/proposition";

/** Populate Router instance with DOM routes */
const routes = new Router({
  common,
  home,
  aboutUs,
  nouvelleAnnonce,
  proposition
});

// Load Events
jQuery(document).ready(() => routes.loadEvents());
