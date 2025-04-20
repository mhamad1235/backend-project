const defaultColor = $('#code').val() || '#0ab39c';

const picker = Pickr.create({
  el: '.color-picker',
  theme: 'classic',
  default: defaultColor,
  defaultRepresentation: 'HEXA',

  components: {
    preview: true,
    hue: true,

    // Input / output Options
    interaction: {
      hex: true,
      input: true,
      clear: true,
      save: true,
    },
  },
});

picker.on('change', (color, instance) => {
  $('#code').val(color.toHEXA().toString());
});

picker.on('clear', (color, instance) => {
  $('#code').val('#000000');
  picker.setColor('#000000');
});

picker.on('save', (color, instance) => {
  if (color) $('#code').val(color.toHEXA().toString());
  instance.hide();
});

// when click on color-lable or picker container show the picker
$(document).on('click', '.color-label,.picker-container', () => picker.show());

// disable typing in code input
$('#code').on('input', function () {
  $(this).val('');
  picker.show();
});
