'use strict';
function Ajax() {
  this.handle = function(q) {
    return q.error(function(xhr, textStatus, errorMessage) {
      var message = null;

      if (xhr.status === 404) {
        message = '请求的资源没有找到';
      } else if (xhr.status === 401) {
        message = '请登录后再操作';
      } else {
        if (typeof errorMessage !== 'undefined') {
          message = errorMessage;
        }

        if ((typeof xhr.responseJSON !== 'undefined')) {
          if (xhr.status === 422) {
            for (var i in xhr.responseJSON) {
              message = xhr.responseJSON[i].message;
              break;
            }
          } else {
            if (typeof xhr.responseJSON.message !== 'undefined') {
              message = xhr.responseJSON.message;
            }
          }
          Message.error(xhr.responseJSON.message);
        }
      }

      if (!message) {
        message = '服务器发生了错误';
      }

      Message.error(message);
    });
  };

  this.get = function(url) {
    var q = $.ajax({
      type: 'get',
      url: url,
      dataType: 'json'
    });
    return this.handle(q);
  };

  this.delete = function(url) {
    var q = $.ajax({
      type: 'delete',
      url: url,
      dataType: 'json'
    });
    return this.handle(q);
  };

  this.post = function(url, data) {
    if (typeof data === 'undefined') {
      data = {};
    }
    var q = $.ajax({
      type: 'post',
      url: url,
      data: data,
      dataType: 'json'
    });
    return this.handle(q);
  };

  this.put = function(url, data) {
    if (typeof data === 'undefined') {
      data = {};
    }
    var q = $.ajax({
      type: 'put',
      url: url,
      data: data,
      dataType: 'json'
    });
    return this.handle(q);
  };
}

window.Ajax = new Ajax();
