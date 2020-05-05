module.exports = {
  extends: "stylelint-config-standard",
  rules: {
    "font-family-no-missing-generic-family-keyword": null,
    "selector-pseudo-element-colon-notation": null,
    "at-rule-empty-line-before": null,
    "color-hex-length": null,
    "rule-empty-line-before": null,
    "no-descending-specificity": null,
    "no-empty-source": null,
    "string-quotes": "double",
    "at-rule-no-unknown": [
      true,
      {
        ignoreAtRules: [
          "extend",
          "at-root",
          "debug",
          "warn",
          "error",
          "if",
          "else",
          "for",
          "each",
          "while",
          "mixin",
          "include",
          "content",
          "return",
          "function",
          "tailwind",
          "apply",
          "responsive",
          "variants",
          "screen"
        ]
      }
    ]
  }
};
