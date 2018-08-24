'use strict';
function Tool() {
  this.cloneHtml = function() {
    $('.clone').each(function() {
      var count = parseInt($(this).attr('clone'));
      var item = $(this).html();
      var html = '';
      for (var i = 1; i <= count; i++) {
        html += item;
      }
      $(this).html(html);
    });
  };
}

window.Tool = new Tool();
