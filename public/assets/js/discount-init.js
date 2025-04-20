const hideDiscountAmount = () => {
  $('#discount_amount').parent().parent().addClass('d-none');
  $('#discount_amount').removeAttr('required');
  $('#discount_amount').val('');
};

const hideDiscountPercentage = () => {
  $('#discount_percentage').parent().parent().addClass('d-none');
  $('#discount_percentage').removeAttr('required');
  $('#discount_percentage').val('');
};

const showDiscountAmount = () => {
  $('#discount_amount').parent().parent().removeClass('d-none');
  $('#discount_amount').attr('required', true);
  $('#discount_date_range').attr('required', true);
};

const showDiscountPercentage = () => {
  $('#discount_percentage').parent().parent().removeClass('d-none');
  $('#discount_percentage').attr('required', true);
  $('#discount_date_range').attr('required', true);
};

// if discount type is selected
$('#discount_type').on('change', function () {
  let selectedText = $(this).find('option:selected').text();
  if (selectedText === 'Fixed') {
    hideDiscountPercentage();
    showDiscountAmount();
  } else if (selectedText === 'Percentage') {
    hideDiscountAmount();
    showDiscountPercentage();
  } else {
    hideDiscountAmount();
    hideDiscountPercentage();
    // $('#discount_date_range').attr('required', false);
  }
});
