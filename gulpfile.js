var elixir = require('laravel-elixir');

var sassSource = "mystyle.scss";

var sassTarget = "resources/assets/css";

var cssSource = [
  "../../vendor/select2/select2.css",
  "../../vendor/select2/select2-bootstrap.css",
  "../../vendor/dropzone/downloads/css/dropzone.css",
  "mystyle.css",
];
var scriptSource = [
  "../../vendor/jquery/dist/jquery.js",
  "../../vendor/bootstrap-sass/assets/javascripts/bootstrap.js",
  "../../vendor/bootstrap-filestyle/src/bootstrap-filestyle.js",
  "../../vendor/dropzone/downloads/dropzone.js",
  "../../vendor/fastclick/lib/fastclick.js",
  "../../vendor/select2/select2.js",
  "myscript.js"
];

var versioningSource = [
  "css/all.css",
  "js/all.js"
];

elixir(function(mix) {
  mix
    .sass(sassSource, sassTarget)
    .styles(cssSource)
    .scripts(scriptSource)
    .version(versioningSource)
      // TODO: delete interim directories
    .copy("resources/vendor/font-awesome/fonts", "public/build/fonts")
    .copy("resources/vendor/select2/select2-spinner.gif", "public/build/css")
    .copy("resources/vendor/select2/select2.png", "public/build/css")
    .copy("resources/vendor/select2/select2x2.png", "public/build/css")
    .copy("resources/vendor/dropzone/downloads/images/spritemap.png", "public/build/images")
    .copy("resources/vendor/dropzone/downloads/images/spritemap@2x.png", "public/build/images");
});

