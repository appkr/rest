(function(window, FastClick, $) {

  var message = "",
    messageType = "danger",
    delay = 5000;

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  window.addEventListener('load', function() {
    FastClick.attach(document.body);
  }, false);

  window.utility = {
    reloadPage: function(interval) {
      if (interval == '') interval = 3000;

      setTimeout(function () {
        window.location.reload(true);
      }, interval);
    },

    redirectTo: function(url, interval) {
      if (interval == '') interval = 3000;

      setTimeout(function () {
        window.location.href = url;
      }, interval);
    },

    flashMessage: function(message, type, delay) {
      var flashObj = $("div.js-flash-message");

      if (flashObj) {
        flashObj.remove();
      }

      if (message.length > 100) {
        delay = delay * 1.5;
      }

      $("<div></div>", {
        "class": "alert alert-" + type + " alert-dismissible js-flash-message",
        "html": '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' + message
      }).appendTo($(".container").first());

      $("div.js-flash-message").fadeIn(300).delay(delay).fadeOut(300);
    }
  };

  if($(".flash-message")) {
    $(".flash-message").not(".flash-important").delay(delay).fadeOut();
  }

  // Back to Top Button
  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      $('#back-to-top').fadeIn();
    } else {
      $('#back-to-top').fadeOut();
    }
  });

  $('#back-to-top').click(function () {
    $('#back-to-top').tooltip('hide');
    $('body,html').animate({
      scrollTop: 0
    }, 800);
    return false;
  });

})(window, FastClick, jQuery);