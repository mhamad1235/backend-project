// Handle New Line in Textarea
// Auto adjust textarea height based on content
$('.multiline-input').on('input', function () {
  this.style.height = 'auto';
  this.style.height = this.scrollHeight + 'px';
});

if ($('.multiline-input').val().split('\n').length > 1) {
  $('.multiline-input').attr('rows', $('.multiline-input').val().split('\n').length);
}
