var scollBox = $('.shoutbox');
var height = 0;
$("ul li").each(function() {
  height += $(this).outerHeight(true); // to include margins
});
$('.chat-messages .list-group').animate({
  scrollTop: height
});
load_data = {
  'fetch': 1
};
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('input[name=_token]').val()
  }
});

window.setInterval(function() {
  $('.chat-messages .list-group');
  $.ajax({
    url: "shoutbox/messages",
    type: 'post',
    data: load_data,
    dataType: 'json',
    success: function(data) {
      $('.chat-messages .list-group').html(data.data);
    }
  });
}, 3000);

$("#chat-message").keypress(function(evt) {
  if (evt.which == 13) {
    var message = $('#chat-message').val();
    post_data = {
      'message': message
    };
    $.ajax({
      url: "shoutbox/send",
      type: 'post',
      data: post_data,
      dataType: 'json',
      success: function(data) {
        $(data.data).hide().appendTo('.chat-messages .list-group').fadeIn();
        $('#chat-error').addClass('hidden');
        $('#chat-message').removeClass('invalid');
        $('#chat-message').val('');
        $('.chat-messages .list-group').animate({
          scrollTop: height
        });
      },
      error: function(data) {
        $('#chat-message').addClass('invalid');
        $('#chat-error').removeClass('hidden');
        $('#chat-error').text(data.responseText);
      }
    });
  }
});
